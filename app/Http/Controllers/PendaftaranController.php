<?php

namespace App\Http\Controllers;

use App\Models\DokumenPendaftaran;
use App\Models\Pendaftar;
use App\Models\Setting;
use Illuminate\Http\Request; // <-- Penting untuk Database Transaction
use Illuminate\Support\Facades\DB; // <-- Penting untuk mencatat error
use Illuminate\Support\Facades\Log; // <-- Penting untuk generate kode acak
use Illuminate\Support\Str;
use PDF;

class PendaftaranController extends Controller
{
    /**
     * Menampilkan halaman formulir pendaftaran.
     */
    public function create()
    {
        // Ambil setting kuota dengan fallback aman
        $kuota_ring1 = (int) optional(Setting::where('key', 'kuota_ring1')->first())->value ?? 30;
        $kuota_umum = (int) optional(Setting::where('key', 'kuota_umum')->first())->value ?? 20;
        $max_slots = $kuota_ring1 + $kuota_umum; // Total kuota
        
        // Ambil porsi tertinggi
        $max_porsi = (int) optional(Setting::where('key', 'max_porsi')->first())->value ?? 999999;
        
        // Hitung pendaftar Ring 1 (yang punya kelurahan_id dan ring_status = 1)
        $peserta_ring1 = Pendaftar::whereHas('kelurahan', function($q) {
            $q->where('ring_status', 1);
        })->count();
        
        // Hitung pendaftar Umum (NULL kelurahan_id ATAU ring_status != 1)
        $peserta_umum = Pendaftar::where(function($q) {
            $q->whereNull('kelurahan_id')
              ->orWhereHas('kelurahan', function($sub) {
                  $sub->where('ring_status', '!=', 1);
              });
        })->count();
        
        // Hitung total dan sisa
        $peserta_terdaftar = $peserta_ring1 + $peserta_umum;
        $sisa_slot = $max_slots - $peserta_terdaftar;
        $sisa_ring1 = $kuota_ring1 - $peserta_ring1;
        $sisa_umum = $kuota_umum - $peserta_umum;

        return view('pendaftaran', compact(
            'sisa_slot', 'max_slots', 'max_porsi', 
            'peserta_terdaftar', 'peserta_ring1', 'peserta_umum',
            'kuota_ring1', 'kuota_umum', 'sisa_ring1', 'sisa_umum'
        ));
    }

    /**
     * Menyimpan data dari formulir pendaftaran.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI DATA DINAMIS BERDASARKAN JENIS LOKASI
        // ==================================================
        $jenisLokasi = $request->input('jenis_lokasi', 'gresik');
        
        // Validasi dasar yang selalu ada
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'jenis_porsi' => 'required|in:berangkat,penggabungan,mutasi',
            'nomor_porsi' => 'required|string|max:50|unique:pendaftars,nomor_porsi_haji',
            'nomor_porsi_penggabungan' => 'nullable|string|max:50', // Hanya untuk penggabungan
            'file_ktp' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_kk' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_akta' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_nikah' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_ijazah' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_bpih' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_spph' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'file_paspor' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'file_booster1' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'file_booster2' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'persetujuan' => 'required|accepted',
        ];
        
        // Validasi tambahan untuk penggabungan
        if ($request->input('jenis_porsi') === 'penggabungan') {
            $rules['nomor_porsi_penggabungan'] = 'required|string|max:50';
        }
        
        // Tambah validasi sesuai jenis lokasi
        if ($jenisLokasi === 'gresik') {
            // Untuk Gresik, validasi dropdown
            $rules['kelurahan_id'] = 'required|exists:kelurahans,id';
        } else {
            // Untuk luar Gresik, validasi input manual
            $rules['kabupaten_kota'] = 'required|string|max:100';
            $rules['kecamatan_manual'] = 'required|string|max:100';
            $rules['kelurahan_manual'] = 'required|string|max:100';
        }
        
        $validatedData = $request->validate($rules);

        // 2. CEK PORSI TERTINGGI (HANYA UNTUK JENIS BERANGKAT)
        // =====================================================
        $jenis_porsi = $request->input('jenis_porsi');
        $max_porsi = (int) optional(Setting::where('key', 'max_porsi')->first())->value ?? 999999;
        $nomor_porsi_input = (int) preg_replace('/\D/', '', $request->nomor_porsi);
        
        // Hanya cek batas porsi untuk jenis "berangkat" dan "mutasi", skip untuk "penggabungan"
        if (($jenis_porsi === 'berangkat' || $jenis_porsi === 'mutasi') && $nomor_porsi_input > $max_porsi) {
            return back()->withInput()->withErrors([
                'nomor_porsi' => 'Nomor Porsi Haji Anda (' . number_format($nomor_porsi_input, 0, ',', '.') . ') melebihi Porsi Tertinggi Departemen Agama tahun ini (' . number_format($max_porsi, 0, ',', '.') . '). Pendaftaran tidak dapat dilanjutkan.'
            ]);
        }
        
        // 2B. CEK KUOTA BERDASARKAN JENIS LOKASI
        // ========================================
        $kuota_ring1 = (int) optional(Setting::where('key', 'kuota_ring1')->first())->value ?? 30;
        $kuota_umum = (int) optional(Setting::where('key', 'kuota_umum')->first())->value ?? 20;
        
        // Cek apakah pendaftar adalah Ring 1
        $isRing1 = false;
        if ($jenisLokasi === 'gresik' && isset($validatedData['kelurahan_id'])) {
            $kelurahan = \App\Models\Kelurahan::find($validatedData['kelurahan_id']);
            if ($kelurahan && $kelurahan->ring_status == 1) {
                $isRing1 = true;
            }
        }
        
        // Hitung peserta yang sudah terdaftar
        if ($isRing1) {
            // Cek kuota Ring 1
            $peserta_ring1 = Pendaftar::whereHas('kelurahan', function($q) {
                $q->where('ring_status', 1);
            })->count();
            
            if ($peserta_ring1 >= $kuota_ring1) {
                return back()->withInput()->with('error', 
                    'Maaf, kuota Ring 1 (Kelurahan Prioritas) sudah penuh (' . $kuota_ring1 . ' kursi). Silakan hubungi admin untuk informasi lebih lanjut.');
            }
        } else {
            // Cek kuota Umum
            $peserta_umum = Pendaftar::where(function($q) {
                $q->whereNull('kelurahan_id')
                  ->orWhereHas('kelurahan', function($sub) {
                      $sub->where('ring_status', '!=', 1);
                  });
            })->count();
            
            if ($peserta_umum >= $kuota_umum) {
                return back()->withInput()->with('error', 
                    'Maaf, kuota Umum (Luar Ring 1) sudah penuh (' . $kuota_umum . ' kursi). Silakan hubungi admin untuk informasi lebih lanjut.');
            }
        }

        // 3. DATABASE TRANSACTION
        // =========================
        try {
            DB::beginTransaction();

            // 3. GENERATE KODE PENDAFTARAN DENGAN NOMOR URUT
            // ================================================
            // Ambil nomor urut terakhir dan tambah 1
            $nomorUrut = Pendaftar::count() + 1;
            $kodeUrut = str_pad($nomorUrut, 5, '0', STR_PAD_LEFT); // Format: 00001, 00002, dst
            
            // Format: KBH-YYYYMMDD-XXXXX
            $kodePendaftaran = 'KBH-'.now()->format('Ymd').'-'.$kodeUrut;

            // 4. SIMPAN DATA DIRI KE TABEL 'PENDAFTARS'
            // ===========================================
            $dataPendaftar = [
                'kode_pendaftaran' => $kodePendaftaran,
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'tempat_lahir' => $validatedData['tempat_lahir'],
                'tanggal_lahir' => $validatedData['tanggal_lahir'],
                'alamat' => $validatedData['alamat'],
                'nomor_hp' => $validatedData['telepon'],
                'jenis_porsi' => $validatedData['jenis_porsi'],
                'nomor_porsi_haji' => $validatedData['nomor_porsi'],
                'nomor_porsi_penggabungan' => $validatedData['nomor_porsi_penggabungan'] ?? null,
                'status_pendaftaran' => 'menunggu', // Default: menunggu verifikasi admin
            ];
            
            // Simpan data lokasi sesuai jenis
            if ($jenisLokasi === 'gresik') {
                $dataPendaftar['kelurahan_id'] = $validatedData['kelurahan_id'];
                $dataPendaftar['kabupaten_kota'] = 'Gresik';
                $dataPendaftar['kecamatan_manual'] = null;
                $dataPendaftar['kelurahan_manual'] = null;
            } else {
                $dataPendaftar['kelurahan_id'] = null;
                $dataPendaftar['kabupaten_kota'] = $validatedData['kabupaten_kota'];
                $dataPendaftar['kecamatan_manual'] = $validatedData['kecamatan_manual'];
                $dataPendaftar['kelurahan_manual'] = $validatedData['kelurahan_manual'];
            }
            
            $pendaftar = Pendaftar::create($dataPendaftar);

            // 5. UPLOAD & SIMPAN SEMUA FILE DOKUMEN
            // =======================================
            $basePath = 'dokumen-pendaftar/'.$kodePendaftaran;

            $dokumenPaths = [
                'pendaftar_id' => $pendaftar->id,
                'file_ktp_path' => $request->file('file_ktp')->store($basePath, 'public'),
                'file_kk_path' => $request->file('file_kk')->store($basePath, 'public'),
                'file_akta_path' => $request->file('file_akta')->store($basePath, 'public'),
                'file_nikah_path' => $request->file('file_nikah')->store($basePath, 'public'),
                'file_ijazah_path' => $request->file('file_ijazah')->store($basePath, 'public'),
                'file_bpih_path' => $request->file('file_bpih')->store($basePath, 'public'),
                'file_spph_path' => $request->file('file_spph')->store($basePath, 'public'),
            ];

            // File opsional: paspor, vaksin booster
            if ($request->hasFile('file_paspor')) {
                $dokumenPaths['file_paspor_path'] = $request->file('file_paspor')->store($basePath, 'public');
            }
            
            if ($request->hasFile('file_booster1')) {
                $dokumenPaths['file_booster1_path'] = $request->file('file_booster1')->store($basePath, 'public');
            }
            
            if ($request->hasFile('file_booster2')) {
                $dokumenPaths['file_booster2_path'] = $request->file('file_booster2')->store($basePath, 'public');
            }

            // 6. SIMPAN PATH DOKUMEN KE TABEL 'DOKUMEN_PENDAFTARANS'
            // ======================================================
            DokumenPendaftaran::create($dokumenPaths);

            // 7. JIKA SEMUA BERHASIL, KONFIRMASI TRANSAKSI
            // ============================================
            DB::commit();

            // 8. KIRIM NOTIFIKASI WHATSAPP KE USER
            // =====================================
            $this->kirimNotifikasiWhatsApp(
                $validatedData['telepon'],
                $validatedData['nama_lengkap'],
                $kodePendaftaran
            );
            
            // 9. ARAHKAN KE HALAMAN SUKSES
            // ==============================
            return redirect()->route('pendaftaran.sukses')->with('kode_pendaftaran', $kodePendaftaran);

        } catch (\Exception $e) {
            // 10. JIKA ADA ERROR, BATALKAN SEMUA & CATAT LOG
            // ===============================================
            DB::rollBack();

            // Mencatat error ke file log Laravel untuk di-debug nanti.
            Log::error('Gagal saat pendaftaran haji: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Kembalikan ke form dengan pesan error yang lebih spesifik untuk debugging
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pendaftaran Anda. Error: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi WhatsApp ke pendaftar
     */
    private function kirimNotifikasiWhatsApp($nomorHP, $namaLengkap, $kodePendaftaran)
    {
        try {
            // Format nomor HP ke format internasional (62xxx)
            $nomorHP = preg_replace('/^0/', '62', $nomorHP);
            $nomorHP = preg_replace('/[^0-9]/', '', $nomorHP);
            
            // Pesan yang akan dikirim
            $pesan = "*🕌 KBIHU ASWAJA - Konfirmasi Pendaftaran 🕌*\n\n";
            $pesan .= "Assalamu'alaikum Wr. Wb.\n\n";
            $pesan .= "Yth. Bapak/Ibu *{$namaLengkap}*,\n\n";
            $pesan .= "Alhamdulillah, pendaftaran Anda telah kami terima dengan baik.\n\n";
            $pesan .= "📋 *KODE PENDAFTARAN ANDA:*\n";
            $pesan .= "*{$kodePendaftaran}*\n\n";
            $pesan .= "⚠️ *PENTING:*\n";
            $pesan .= "• Simpan kode ini dengan baik\n";
            $pesan .= "• Gunakan untuk cek status pendaftaran\n";
            $pesan .= "• Jangan bagikan ke orang lain\n\n";
            $pesan .= "📱 *Langkah Selanjutnya:*\n";
            $pesan .= "1. Tim kami akan melakukan verifikasi dokumen\n";
            $pesan .= "2. Anda akan dihubungi dalam 1-3 hari kerja\n";
            $pesan .= "3. Cek status: " . url('/cek-status') . "\n\n";
            $pesan .= "Untuk informasi lebih lanjut, hubungi:\n";
            $pesan .= "📞 +62 812-8166-6811\n\n";
            $pesan .= "Jazakumullahu khairan 🤲\n";
            $pesan .= "_KBIHU Aswaja - Melayani dengan Ikhlas_";
            
            // OPSI 1: Menggunakan Fonnte (Perlu API Key)
            // Uncomment jika sudah punya API key Fonnte
            /*
            $apiKey = env('FONNTE_API_KEY'); // Tambahkan di .env
            
            if ($apiKey) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'target' => $nomorHP,
                        'message' => $pesan,
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $apiKey
                    ),
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                
                Log::info('WhatsApp terkirim ke: ' . $nomorHP, ['response' => $response]);
            }
            */
            
            // OPSI 2: Log saja (untuk testing tanpa API)
            // Hapus ini kalau sudah pakai API real
            Log::info('WhatsApp Notification', [
                'nomor' => $nomorHP,
                'nama' => $namaLengkap,
                'kode' => $kodePendaftaran,
                'pesan' => $pesan
            ]);
            
        } catch (\Exception $e) {
            // Jangan failed total kalau WA gagal, cukup log saja
            Log::error('Gagal kirim WhatsApp: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan halaman sukses setelah pendaftaran.
     */
    public function success()
    {
        // Pastikan halaman ini hanya bisa diakses jika ada session 'kode_pendaftaran'
        if (! session('kode_pendaftaran')) {
            return redirect()->route('pendaftaran.form');
        }

        return view('pendaftaran-sukses');
    }

    public function downloadKartu($kode)
    {
        // Cari pendaftar berdasarkan kode yang ada di URL
        $pendaftar = Pendaftar::where('kode_pendaftaran', $kode)->firstOrFail();

        // Load view PDF dengan data pendaftar
        $pdf = PDF::loadView('pdf.kartu-pendaftaran', compact('pendaftar'));

        // Atur nama file yang akan di-download
        $fileName = 'Kartu-Pendaftaran-Haji-'.Str::slug($pendaftar->nama_lengkap).'.pdf';

        // Kembalikan sebagai download
        return $pdf->download($fileName);
    }
}
