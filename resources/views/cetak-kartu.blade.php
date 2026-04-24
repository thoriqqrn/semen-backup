@extends('layouts.main')

@section('title', 'Cetak Ulang Kartu Pendaftaran | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Cetak Bukti Pendaftaran</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Masukkan kode pendaftaran Anda untuk menampilkan dan mengunduh kembali kartu bukti pendaftaran.</p>
        </div>
    </div>

    <!-- 2. Form & Hasil -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Form Pencarian -->
                    <div class="card border-0 shadow-sm rounded-custom mb-5">
                        <div class="card-body p-4 p-md-5">
                            <form id="formCariPendaftar">
                                <div class="row align-items-end">
                                    <div class="col-md-9">
                                        <label for="kode_pendaftaran" class="form-label fw-bold">Masukkan Kode Pendaftaran Anda</label>
                                        <input type="text" name="kode_pendaftaran" id="kode_pendaftaran" class="form-control form-control-lg" placeholder="Contoh: KBH-20251025-A8X3F" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-success btn-lg w-100 mt-3 mt-md-0">
                                            <i class="fas fa-search me-2"></i>Tampilkan Kartu
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div id="statusPesan" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Area Preview Kartu & Tombol Download -->
                    <div id="hasilPencarian" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold">Preview Kartu Pendaftaran</h3>
                            <button id="btnDownloadPdf" class="btn btn-primary btn-lg">
                                <i class="fas fa-download me-2"></i> Unduh sebagai PDF
                            </button>
                        </div>
                        
                        {{-- TEMPLATE KARTU - VERSI SIMPEL UNTUK PDF --}}
                        <div id="kartu-pendaftaran-template" style="width: 800px; background: white; padding: 40px; border: 3px solid #198754; margin: 0 auto;">
                            
                            <!-- HEADER -->
                            <div style="text-align: center; border-bottom: 3px solid #198754; padding-bottom: 20px; margin-bottom: 30px;">
                                <h1 style="margin: 0 0 10px 0; color: #198754; font-size: 36px; font-weight: bold;">KBIHU ASWAJA</h1>
                                <h2 style="margin: 0; font-size: 22px; color: #333;">BUKTI PENDAFTARAN ONLINE</h2>
                            </div>
                            
                            <!-- ISI DATA -->
                            <div style="margin: 30px 0;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: bold; font-size: 16px; width: 35%;">NAMA LENGKAP</td>
                                        <td style="padding: 12px 0; font-size: 16px; width: 5%;">:</td>
                                        <td style="padding: 12px 0; font-size: 16px;" id="kartu-nama">-</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: bold; font-size: 16px;">TEMPAT, TGL LAHIR</td>
                                        <td style="padding: 12px 0; font-size: 16px;">:</td>
                                        <td style="padding: 12px 0; font-size: 16px;" id="kartu-ttl">-</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: bold; font-size: 16px;">NOMOR PORSI HAJI</td>
                                        <td style="padding: 12px 0; font-size: 16px;">:</td>
                                        <td style="padding: 12px 0; font-size: 16px;" id="kartu-porsi">-</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: bold; font-size: 16px;">TANGGAL DAFTAR</td>
                                        <td style="padding: 12px 0; font-size: 16px;">:</td>
                                        <td style="padding: 12px 0; font-size: 16px;" id="kartu-tgl-daftar">-</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- BARCODE / KODE -->
                            <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 2px dashed #ccc;">
                                <p style="margin-bottom: 15px; font-weight: bold; font-size: 14px;">KODE PENDAFTARAN ANDA</p>
                                <div style="font-size: 32px; font-weight: bold; letter-spacing: 4px; border: 2px solid #198754; padding: 15px 30px; display: inline-block; background: #f8f9fa; color: #198754; border-radius: 5px;" id="kartu-kode">-</div>
                            </div>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    {{-- CDN untuk jsPDF dan html2canvas --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formCariPendaftar');
        const inputKode = document.getElementById('kode_pendaftaran');
        const statusPesan = document.getElementById('statusPesan');
        const hasilContainer = document.getElementById('hasilPencarian');
        const btnDownload = document.getElementById('btnDownloadPdf');
        const kartuTemplate = document.getElementById('kartu-pendaftaran-template');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const kode = inputKode.value.trim();
            if (!kode) {
                statusPesan.innerHTML = `<div class="alert alert-warning">Mohon masukkan kode pendaftaran.</div>`;
                return;
            }

            // Tampilkan loading
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...`;
            statusPesan.innerHTML = '';
            hasilContainer.style.display = 'none';

            // Panggil API backend
            fetch(`{{ route('api.pendaftar.data') }}?kode_pendaftaran=${encodeURIComponent(kode)}`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.error || 'Terjadi kesalahan') });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data diterima:', data); // Debug
                    
                    // Isi data ke template
                    document.getElementById('kartu-nama').textContent = data.nama_lengkap || '-';
                    document.getElementById('kartu-ttl').textContent = `${data.tempat_lahir || '-'}, ${data.tanggal_lahir || '-'}`;
                    document.getElementById('kartu-porsi').textContent = data.nomor_porsi_haji || '-';
                    document.getElementById('kartu-tgl-daftar').textContent = data.tanggal_daftar || '-';
                    document.getElementById('kartu-kode').textContent = data.kode_pendaftaran || '-';
                    
                    // Tampilkan hasil
                    hasilContainer.style.display = 'block';
                    
                    // Scroll ke hasil
                    setTimeout(() => {
                        hasilContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                })
                .catch(error => {
                    statusPesan.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${error.message}</div>`;
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-search me-2"></i>Tampilkan Kartu`;
                });
        });

        // Event listener untuk download PDF - METODE BARU
        btnDownload.addEventListener('click', function() {
            const kodeClean = document.getElementById('kartu-kode').textContent.trim();
            
            btnDownload.disabled = true;
            btnDownload.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Membuat PDF...`;
            
            // Gunakan html2canvas + jsPDF secara manual
            html2canvas(kartuTemplate, {
                scale: 2,
                useCORS: true,
                logging: false,
                width: 800,
                height: kartuTemplate.offsetHeight
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                
                // Inisialisasi jsPDF
                const { jsPDF } = window.jspdf;
                
                // Ukuran A5 landscape dalam mm: 210 x 148
                const pdf = new jsPDF('l', 'mm', 'a5');
                
                const pdfWidth = 210; // A5 landscape width
                const pdfHeight = 148; // A5 landscape height
                
                // Hitung proporsi gambar
                const imgWidth = pdfWidth - 20; // margin 10mm kiri-kanan
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // Posisi center
                const x = 10;
                const y = (pdfHeight - imgHeight) / 2;
                
                // Tambahkan gambar ke PDF
                pdf.addImage(imgData, 'JPEG', x, y, imgWidth, imgHeight);
                
                // Download
                pdf.save(`Kartu-Pendaftaran-${kodeClean}.pdf`);
                
                btnDownload.disabled = false;
                btnDownload.innerHTML = `<i class="fas fa-download me-2"></i> Unduh sebagai PDF`;
            }).catch(error => {
                console.error('Error membuat PDF:', error);
                alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
                btnDownload.disabled = false;
                btnDownload.innerHTML = `<i class="fas fa-download me-2"></i> Unduh sebagai PDF`;
            });
        });
    });
    </script>
@endpus