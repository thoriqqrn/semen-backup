@extends('admin.layouts.app')
@section('title', 'Detail Pendaftar - ' . $pendaftar->nama_lengkap)
@push('styles')
<style>
    .comparison-table td, .comparison-table th { padding: 0.75rem; vertical-align: middle; }
    .match { color: green; font-weight: bold; }
    .mismatch { color: red; font-weight: bold; }
    .status-text { font-style: italic; color: #6c757d; margin-top: 10px; }
    .data-source { font-size: 0.8rem; color: #6c757d; display: block; font-weight: normal; }
    .placeholder-glow .placeholder { min-height: 1.2em; }
    .document-preview { cursor: pointer; transition: transform 0.2s; }
    .document-preview:hover { transform: scale(1.05); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold" style="color: #28a745;">Detail Calon Jamaah</h1>
        <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Kotak Verifikasi OCR (KTP) -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-id-card me-2"></i> Asisten Verifikasi KTP</h6>
        </div>
        <div class="card-body">
            <p>Klik tombol untuk mengekstrak data dari KTP dan membandingkannya dengan data form.</p>
            <button id="processOcrKtpBtn" class="btn btn-primary" data-process-url="{{ route('admin.pendaftar.ocr.ktp', $pendaftar->id) }}">
                <i class="fas fa-cogs me-2"></i> Baca Data KTP
            </button>
            <div id="ktpStatus" class="status-text"></div>
            <div id="ktpResultContainer" class="mt-4" style="display: none;">
                <hr>
                <h5 class="fw-bold">Hasil Perbandingan KTP vs Form</h5>
                <table class="table table-bordered comparison-table">
                    <thead class="table-light"><tr><th>Data</th><th>Hasil KTP (OCR)</th><th>Data Form</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr><th>Nama</th><td id="ktpOcrNama"></td><td id="ktpFormNama"></td><td id="ktpStatusNama"></td></tr>
                        <tr><th>Tgl Lahir</th><td id="ktpOcrTtl"></td><td id="ktpFormTtl"></td><td id="ktpStatusTtl"></td></tr>
                        <tr><th>NIK</th><td id="ktpOcrNik" colspan="3"></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kotak Verifikasi OCR (KK) -->
    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-users me-2"></i> Asisten Verifikasi KK</h6>
        </div>
        <div class="card-body">
            <p>Klik tombol untuk mengekstrak semua anggota keluarga dari KK dan menemukan data pendaftar.</p>
            <button id="processKkBtn" class="btn btn-info" data-process-url="{{ route('admin.pendaftar.ocr.kk', $pendaftar->id) }}">
                <i class="fas fa-cogs me-2"></i> Baca Data KK
            </button>
            <div id="kkStatus" class="status-text"></div>
            <div id="kkResultContainer" class="mt-4" style="display: none;">
                <hr>
                <h5 class="fw-bold">Ringkasan Hasil Analisis KK</h5>
                <div class="row">
                    <div class="col-md-4"><strong>Total Anggota Terdeteksi:</strong> <span id="kkTotalAnggota">0</span></div>
                    <div class="col-md-4"><strong>Skor Kecocokan:</strong> <span id="kkMatchScore">0</span>%</div>
                    <div class="col-md-4"><strong>Status Pendaftar:</strong> <span id="kkStatusBadge" class="badge bg-secondary">Belum diproses</span></div>
                </div>
                <h5 class="fw-bold mt-4">Data Pendaftar yang Cocok di KK</h5>
                <table class="table table-bordered comparison-table">
                     <thead class="table-light"><tr><th>Data</th><th>Ditemukan di KK (OCR)</th><th>Data dari Form</th></tr></thead>
                     <tbody>
                         <tr><th>NIK</th><td id="kkNik"></td><td class="text-muted"><em>-</em></td></tr>
                         <tr><th>Nama</th><td id="kkNama"></td><td id="kkFormNama"></td></tr>
                         <tr><th>Tgl Lahir</th><td id="kkTtl"></td><td id="kkFormTtl"></td></tr>
                         <tr><th>Status di KK</th><td id="kkStatusHubungan" colspan="2"></td></tr>
                     </tbody>
                </table>
                 <h5 class="fw-bold mt-4">Semua Anggota Keluarga Terdeteksi</h5>
                 <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light"><tr><th>#</th><th>NIK</th><th>Nama</th><th>Tanggal Lahir</th><th>Status</th></tr></thead>
                        <tbody id="kkAnggotaTableBody"></tbody>
                    </table>
                 </div>
            </div>
        </div>
    </div>
    
    <!-- Data Diri, Dokumen, Catatan (Tidak Berubah) -->
    <div class="row">
        <div class="col-12">
            <!-- Card Data Diri -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-user-circle me-2"></i>Data Diri - {{ $pendaftar->nama_lengkap }}</h6>
                    @if($pendaftar->kelurahan && $pendaftar->kelurahan->ring_status == 1)
                        <span class="badge bg-success p-2"><i class="fas fa-star me-1"></i> PRIORITAS RING 1</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th style="width: 30%;">Kode Pendaftaran</th><td><span class="fw-bold">{{ $pendaftar->kode_pendaftaran }}</span></td></tr>
                        <tr><th>Nama Lengkap</th><td>{{ $pendaftar->nama_lengkap }}</td></tr>
                        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d F Y') }}</td></tr>
                        <tr>
                            <th>Alamat Lengkap</th>
                            <td>
                                {{ $pendaftar->alamat }}<br>
                                @if($pendaftar->kelurahan_id)
                                    <small class="text-muted">
                                        Kel. {{ $pendaftar->kelurahan->nama_kelurahan ?? '-' }}, 
                                        Kec. {{ $pendaftar->kelurahan->kecamatan->nama_kecamatan ?? '-' }}, 
                                        Kab. Gresik
                                        @if($pendaftar->kelurahan->ring_status == 1)
                                            <span class="badge bg-success ms-2"><i class="fas fa-star"></i> Ring 1</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 2)
                                            <span class="badge bg-info ms-2"><i class="fas fa-star"></i> Ring 2</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 3)
                                            <span class="badge bg-warning ms-2"><i class="fas fa-star"></i> Ring 3</span>
                                        @elseif($pendaftar->kelurahan->ring_status)
                                            <span class="badge bg-primary ms-2">Ring {{ $pendaftar->kelurahan->ring_status }}</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">Belum Ada Ring</span>
                                        @endif
                                    </small>
                                @else
                                    <small class="text-muted">
                                        Kel. {{ $pendaftar->kelurahan_manual }}, 
                                        Kec. {{ $pendaftar->kecamatan_manual }}, 
                                        Kab. {{ $pendaftar->kabupaten_kota }}
                                        <span class="badge bg-dark ms-2"><i class="fas fa-map-marker-alt"></i> Luar Ring</span>
                                    </small>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Nomor HP (WhatsApp)</th><td>{{ $pendaftar->nomor_hp }}</td></tr>
                        <tr>
                            <th>Jenis Porsi</th>
                            <td>
                                @if($pendaftar->jenis_porsi === 'penggabungan')
                                    <span class="badge bg-warning text-dark">Penggabungan</span>
                                @elseif($pendaftar->jenis_porsi === 'berangkat')
                                    <span class="badge bg-success">Berangkat</span>
                                @elseif($pendaftar->jenis_porsi === 'mutasi')
                                    <span class="badge bg-info">Mutasi</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Ada Data</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Nomor Porsi Haji</th><td>{{ $pendaftar->nomor_porsi_haji }}</td></tr>
                        @if($pendaftar->jenis_porsi === 'penggabungan' && $pendaftar->nomor_porsi_penggabungan)
                        <tr>
                            <th>Nomor Porsi Penggabungan</th>
                            <td><span class="badge bg-warning text-dark">{{ $pendaftar->nomor_porsi_penggabungan }}</span></td>
                        </tr>
                        @endif
                        <tr><th>Status Pendaftaran</th><td><span class="badge bg-info">{{ $pendaftar->status_pendaftaran }}</span></td></tr>
                    </table>
                </div>
            </div>

            <!-- Card Dokumen Terlampir -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><i class="fas fa-folder-open me-2"></i>Dokumen Terlampir</h6></div>
                <div class="card-body">
                    @if ($pendaftar->dokumen)
                        <div class="row g-3">
                            @php
                                // Array $dokumens sekarang sudah diisi lengkap
                                $dokumens = [
                                    'KTP' => $pendaftar->dokumen->file_ktp_path,
                                    'Kartu Keluarga' => $pendaftar->dokumen->file_kk_path,
                                    'Akta Kelahiran' => $pendaftar->dokumen->file_akta_path,
                                    'Ijazah' => $pendaftar->dokumen->file_ijazah_path,
                                    'Buku Nikah (Opsional)' => $pendaftar->dokumen->file_nikah_path,
                                    'Bukti Setoran Awal (BPIH)' => $pendaftar->dokumen->file_bpih_path,
                                    'Surat Pendaftaran Pergi Haji (SPPH)' => $pendaftar->dokumen->file_spph_path,
                                    'Pas Foto' => $pendaftar->dokumen->file_foto_path,
                                    'Paspor' => $pendaftar->dokumen->file_paspor_path,
                                    'Vaksin Booster 1' => $pendaftar->dokumen->file_booster1_path,
                                    'Vaksin Booster 2' => $pendaftar->dokumen->file_booster2_path,
                                ];
                            @endphp
                            @foreach ($dokumens as $nama => $path)
                                @if ($path)
                                    <div class="col-md-6">
                                        <div class="border rounded p-2 h-100">
                                            <h6 class="fw-bold">{{ $nama }}</h6>
                                            @if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('storage/' . $path) }}" alt="Preview {{ $nama }}" class="img-thumbnail document-preview w-100" style="height: 150px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img-src="{{ asset('storage/' . $path) }}" data-img-title="{{ $nama }}">
                                            @else
                                                <div class="text-center p-4 bg-light rounded h-100 d-flex flex-column justify-content-center align-items-center">
                                                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                                    <p class="mb-2">File PDF</p>
                                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-2"></i>Lihat/Unduh</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Verifikasi Pendaftaran -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3 bg-gradient-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-check-circle me-2"></i>Verifikasi Pendaftaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Status Saat Ini: 
                                @if($pendaftar->status_pendaftaran === 'menunggu')
                                    <span class="badge bg-warning text-dark fs-6">⏳ Menunggu Verifikasi</span>
                                @elseif($pendaftar->status_pendaftaran === 'diterima')
                                    <span class="badge bg-success fs-6">✅ Diterima</span>
                                @elseif($pendaftar->status_pendaftaran === 'ditolak')
                                    <span class="badge bg-danger fs-6">❌ Ditolak</span>
                                @else
                                    <span class="badge bg-secondary fs-6">{{ $pendaftar->status_pendaftaran }}</span>
                                @endif
                            </h5>
                        </div>
                    </div>

                    @if($pendaftar->status_pendaftaran === 'ditolak' && $pendaftar->alasan_penolakan)
                        <div class="alert alert-danger">
                            <strong>Alasan Penolakan:</strong><br>
                            {{ $pendaftar->alasan_penolakan }}
                        </div>
                    @endif

                    @if($pendaftar->status_pendaftaran !== 'diterima')
                        <div class="row">
                            <!-- Tombol Terima -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#terimaModal">
                                    <i class="fas fa-check-circle me-2"></i>Terima Pendaftaran
                                </button>
                            </div>

                            <!-- Tombol Tolak -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                    <i class="fas fa-times-circle me-2"></i>Tolak Pendaftaran
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Pendaftar ini sudah <strong>DITERIMA</strong>. Kartu pendaftaran dapat dicetak.
                            @if($pendaftar->catatan_admin)
                                <hr>
                                <strong>Catatan Admin:</strong><br>
                                {{ $pendaftar->catatan_admin }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Catatan Internal Admin -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><i class="fas fa-edit me-2"></i>Catatan Internal Admin</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.pendaftar.update', $pendaftar->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="5" placeholder="Tulis catatan internal di sini...">{{ $pendaftar->catatan_admin }}</textarea>
                            <input type="hidden" name="status_pendaftaran" value="{{ $pendaftar->status_pendaftaran }}">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Catatan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="imagePreviewModalLabel">Preview Dokumen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center"><img src="" class="img-fluid w-100" alt="Preview"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SCRIPT GABUNGAN: MODAL, OCR KTP, OCR KK --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // LOGIKA UNTUK PROSES OCR KTP
    // ========================================
    const ktpBtn = document.getElementById('processOcrKtpBtn');
    if (ktpBtn) {
        ktpBtn.addEventListener('click', function() {
            const url = this.dataset.processUrl;
            const button = this;
            const originalText = button.innerHTML;
            const statusEl = document.getElementById('ktpStatus');
            const resultContainer = document.getElementById('ktpResultContainer');

            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menganalisis KTP...`;
            resultContainer.style.display = 'none';
            statusEl.textContent = 'Mengirim gambar KTP ke AI...';

            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }})
            .then(res => res.ok ? res.json() : res.json().then(err => { throw new Error(err.error) }))
            .then(data => {
                statusEl.textContent = '✓ Proses KTP Selesai! Metode: ' + data.method;
                statusEl.style.color = 'green';

                document.getElementById('ktpOcrNama').textContent = data.ocr.nama;
                document.getElementById('ktpFormNama').textContent = data.form.nama;
                document.getElementById('ktpStatusNama').innerHTML = data.comparison.nama ? `<span class="match"><i class="fas fa-check-circle"></i> Cocok</span>` : `<span class="mismatch"><i class="fas fa-times-circle"></i> Tidak Cocok</span>`;
                
                document.getElementById('ktpOcrTtl').textContent = data.ocr.ttl;
                document.getElementById('ktpFormTtl').textContent = data.form.ttl;
                document.getElementById('ktpStatusTtl').innerHTML = data.comparison.ttl ? `<span class="match"><i class="fas fa-check-circle"></i> Cocok</span>` : `<span class="mismatch"><i class="fas fa-times-circle"></i> Tidak Cocok</span>`;
                
                document.getElementById('ktpOcrNik').textContent = data.ocr.nik;
                resultContainer.style.display = 'block';
            })
            .catch(error => {
                statusEl.textContent = '✗ Error KTP: ' + error.message;
                statusEl.style.color = 'red';
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }

    // ========================================
    // LOGIKA UNTUK PROSES OCR KK
    // ========================================
    const kkBtn = document.getElementById('processKkBtn');
    if (kkBtn) {
        kkBtn.addEventListener('click', function() {
            const url = this.dataset.processUrl;
            const button = this;
            const originalText = button.innerHTML;
            const statusEl = document.getElementById('kkStatus');
            const resultContainer = document.getElementById('kkResultContainer');

            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menganalisis KK...`;
            resultContainer.style.display = 'none';
            statusEl.textContent = 'Mengirim gambar KK ke AI...';

            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }})
            .then(res => res.ok ? res.json() : res.json().then(err => { throw new Error(err.error) }))
            .then(data => {
                statusEl.textContent = '✓ Proses KK Selesai!';
                statusEl.style.color = 'green';

                document.getElementById('kkTotalAnggota').textContent = data.total_anggota || '0';
                document.getElementById('kkMatchScore').textContent = data.match_score || '0';
                
                const statusBadge = document.getElementById('kkStatusBadge');
                if (data.pendaftar_ditemukan) {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Ditemukan di KK';
                } else {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Tidak Ditemukan di KK';
                }
                
                if (data.matched_data) {
                    document.getElementById('kkNik').textContent = data.matched_data.nik || '-';
                    document.getElementById('kkNama').textContent = data.matched_data.nama || '-';
                    document.getElementById('kkTtl').textContent = data.matched_data.ttl || '-';
                    document.getElementById('kkStatusHubungan').textContent = data.matched_data.status || '-';
                }
                
                document.getElementById('kkFormNama').textContent = data.form_data.nama || '-';
                document.getElementById('kkFormTtl').textContent = data.form_data.ttl || '-';
                
                const anggotaTableBody = document.getElementById('kkAnggotaTableBody');
                anggotaTableBody.innerHTML = ''; 
                
                if (data.anggota_keluarga && data.anggota_keluarga.length > 0) {
                    data.anggota_keluarga.forEach((anggota, index) => {
                        anggotaTableBody.innerHTML += `<tr><td>${index + 1}</td><td>${anggota.nik || '-'}</td><td>${anggota.nama || '-'}</td><td>${anggota.ttl || '-'}</td><td>${anggota.status || '-'}</td></tr>`;
                    });
                } else {
                    anggotaTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data anggota terdeteksi.</td></tr>';
                }
                
                resultContainer.style.display = 'block';
            })
            .catch(error => {
                statusEl.textContent = '✗ Error KK: ' + error.message;
                statusEl.style.color = 'red';
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }

    // ========================================
    // MODAL PREVIEW GAMBAR
    // ========================================
    const imagePreviewModal = document.getElementById('imagePreviewModal');
    if (imagePreviewModal) {
        imagePreviewModal.addEventListener('show.bs.modal', function (event) {
            const triggerElement = event.relatedTarget;
            const imgSrc = triggerElement.getAttribute('data-img-src');
            const imgTitle = triggerElement.getAttribute('data-img-title');
            const modalTitle = imagePreviewModal.querySelector('.modal-title');
            const modalImage = imagePreviewModal.querySelector('.modal-body img');
            modalTitle.textContent = 'Preview: ' + imgTitle;
            modalImage.src = imgSrc;
            modalImage.alt = 'Preview: ' + imgTitle;
        });
    }

    // ========================================
    // TOMBOL SALIN HASIL KE CATATAN
    // ========================================
    const catatanAdminTextarea = document.getElementById('catatan_admin');
    if (copyBtn && catatanAdminTextarea) {
        copyBtn.addEventListener('click', function() {
            let newText = `=== HASIL VERIFIKASI OCR (${new Date().toLocaleString('id-ID')}) ===\n\n`;
            if(ocrResults.ktp) {
                newText += `[KTP]\nNIK: ${ocrResults.ktp.ocr.nik}\nNama: ${ocrResults.ktp.ocr.nama}\nTgl Lahir: ${ocrResults.ktp.ocr.ttl}\n\n`;
            }
            if(ocrResults.kk) {
                newText += `[KK]\nNIK: ${ocrResults.kk.ocr_kk.nik}\nNama Kepala Keluarga: ${ocrResults.kk.ocr_kk.nama}\n\n`;
            }
            newText += `[PERBANDINGAN]\nNama: ${document.getElementById('statusNama').innerText.replace('\n', ' | ')}\nNIK: ${document.getElementById('statusNik').innerText}\nTgl Lahir: ${document.getElementById('statusTtl').innerText}\n====================================`;
            const existingText = catatanAdminTextarea.value;
            catatanAdminTextarea.value = existingText ? existingText.trim() + '\n\n' + newText : newText;

            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Disalin!', text: 'Hasil verifikasi telah disalin ke kolom Catatan Admin.', timer: 1500, showConfirmButton: false });
            } else {
                alert('Hasil verifikasi telah disalin.');
            }
        });
    }
});
</script>

<!-- Modal Terima Pendaftaran -->
<div class="modal fade" id="terimaModal" tabindex="-1" aria-labelledby="terimaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.pendaftar.verifikasi-terima', $pendaftar->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="terimaModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Terima Pendaftaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda akan <strong>MENERIMA</strong> pendaftaran: <br>
                        <strong>{{ $pendaftar->nama_lengkap }}</strong> ({{ $pendaftar->kode_pendaftaran }})
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_admin_terima" class="form-label">
                            <strong>Catatan Admin (Opsional)</strong>
                        </label>
                        <textarea name="catatan_admin" id="catatan_admin_terima" class="form-control" rows="4" placeholder="Catatan tambahan untuk pendaftar yang diterima (opsional)..."></textarea>
                        <small class="text-muted">Maksimal 500 karakter. Catatan ini untuk internal admin.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Ya, Terima Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak Pendaftaran -->
<div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.pendaftar.verifikasi-tolak', $pendaftar->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="tolakModalLabel">
                        <i class="fas fa-times-circle me-2"></i>Tolak Pendaftaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Anda akan <strong>MENOLAK</strong> pendaftaran: <br>
                        <strong>{{ $pendaftar->nama_lengkap }}</strong> ({{ $pendaftar->kode_pendaftaran }})
                    </div>
                    
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">
                            <strong>Alasan Penolakan <span class="text-danger">*</span></strong>
                        </label>
                        <textarea name="alasan_penolakan" id="alasan_penolakan" class="form-control @error('alasan_penolakan') is-invalid @enderror" rows="5" placeholder="Contoh: Dokumen KTP tidak jelas, Nomor porsi haji tidak valid, dll..." required>{{ old('alasan_penolakan') }}</textarea>
                        @error('alasan_penolakan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 10 karakter. Alasan ini akan ditampilkan ke pendaftar.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check me-2"></i>Ya, Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endpush