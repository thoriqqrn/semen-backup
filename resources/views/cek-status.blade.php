@extends('layouts.main')

@section('title', 'Cek Status Pendaftaran | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Cek Status Pendaftaran</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Masukkan kode pendaftaran Anda untuk melihat progres verifikasi.</p>
        </div>
    </div>

    <section class="py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- BAGIAN 1: FORM PENCARIAN -->
                    <div class="card border-0 shadow-sm rounded-custom mb-5">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="fw-bold mb-4 text-center">Cari Data Calon Jamaah</h4>
                            {{-- Form ini menggunakan JavaScript, tidak reload halaman --}}
                            <form id="formCariPendaftar">
                                <div class="input-group input-group-lg">
                                    <input type="text" name="kode_pendaftaran" id="kode_pendaftaran" class="form-control" placeholder="Masukkan Kode Pendaftaran (Contoh: KBH-2025...)" required>
                                    <button class="btn btn-success px-4" type="submit" id="btnCari">
                                        <i class="fas fa-search me-2"></i> Cek Status
                                    </button>
                                </div>
                                <div id="statusPesan"></div>
                            </form>
                        </div>
                    </div>

                    <!-- BAGIAN 2: HASIL PENCARIAN (Hidden by Default) -->
                    <div id="resultSection" style="display: none;">
                        
                        <!-- Timeline Status Lifecycle -->
                        <div class="mb-5 position-relative">
                            <div class="d-flex justify-content-between align-items-center position-relative z-1">
                                
                                <!-- Step 1: Pendaftaran Berhasil -->
                                <div class="step-item text-center">
                                    <div class="step-circle completed">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text mt-2 fw-bold text-success">Pendaftaran<br>Berhasil</div>
                                </div>

                                <!-- Line 1 -->
                                <div class="step-connector flex-grow-1 mx-2">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>

                                <!-- Step 2: Verifikasi -->
                                <div class="step-item text-center">
                                    <div class="step-circle" id="circleVerifikasi">
                                        <span id="iconVerifikasi">2</span>
                                    </div>
                                    <div class="step-text mt-2 fw-bold" id="textVerifikasi">Menunggu<br>Verifikasi</div>
                                </div>

                                <!-- Line 2 -->
                                <div class="step-connector flex-grow-1 mx-2">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" id="lineResult" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Step 3: Hasil (Diterima/Ditolak) -->
                                <div class="step-item text-center">
                                    <div class="step-circle" id="circleHasil">
                                        <span id="iconHasil">3</span>
                                    </div>
                                    <div class="step-text mt-2 fw-bold" id="textHasil">Hasil<br>Seleksi</div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Data Card -->
                        <div class="card border-0 shadow-sm rounded-custom animation-fade-in">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-bold text-center">Identitas Pendaftar</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block text-uppercase ls-1">Kode Pendaftaran</small>
                                        <span class="fs-5 fw-bold text-dark" id="resKode"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block text-uppercase ls-1">Nama Lengkap</small>
                                        <span class="fs-5 fw-bold text-dark" id="resNama"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block text-uppercase ls-1">Nomor Porsi</small>
                                        <span class="fs-5 fw-bold text-dark" id="resPorsi"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block text-uppercase ls-1">Tempat, Tgl Lahir</small>
                                        <span class="fs-5 fw-bold text-dark" id="resTtl"></span>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- AREA AKSI: DITERIMA / DITOLAK / MENUNGGU -->
                                <div id="actionArea" class="text-center">
                                    
                                    <!-- Jika DITERIMA -->
                                    <div id="statusAccepted" style="display: none;">
                                        <div class="alert alert-success d-inline-block px-5 py-3 mb-3">
                                            <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                            <h5 class="fw-bold mb-0">SELAMAT! PENDAFTARAN DITERIMA</h5>
                                        </div>
                                        <p class="text-muted mb-3">Kartu pendaftaran Anda sudah terbit. Silakan unduh di bawah ini.</p>
                                        {{-- Tombol ini mentrigger Script JS html2pdf --}}
                                        <button type="button" id="btnCetakKartu" class="btn btn-primary btn-lg shadow-sm">
                                            <i class="fas fa-file-pdf me-2"></i> Unduh Kartu Pendaftaran
                                        </button>
                                    </div>

                                    <!-- Jika DITOLAK -->
                                    <div id="statusRejected" style="display: none;">
                                        <div class="alert alert-danger d-inline-block px-5 py-3 mb-3">
                                            <i class="fas fa-times-circle fa-2x mb-2 d-block"></i>
                                            <h5 class="fw-bold mb-0">MOHON MAAF, PENDAFTARAN DITOLAK</h5>
                                        </div>
                                        <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 mt-2">
                                            <div class="card-body text-start">
                                                <h6 class="fw-bold text-danger"><i class="fas fa-info-circle me-1"></i> Alasan Penolakan:</h6>
                                                <p class="mb-0 text-dark" id="textAlasanPenolakan">-</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jika MENUNGGU -->
                                    <div id="statusPending" style="display: none;">
                                        <div class="alert alert-warning d-inline-block px-5 py-3">
                                            <i class="fas fa-hourglass-half fa-2x mb-2 d-block"></i>
                                            <h5 class="fw-bold mb-0">SEDANG DALAM PROSES VERIFIKASI</h5>
                                        </div>
                                        <p class="text-muted mt-2">Data Anda sedang diperiksa oleh admin. Mohon cek secara berkala.</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    .rounded-custom { border-radius: 15px; }
    .ls-1 { letter-spacing: 1px; }

    /* Timeline Styles */
    .step-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        transition: all 0.4s ease;
    }
    .step-circle.completed {
        background: #198754;
        color: white;
        box-shadow: 0 0 0 5px rgba(25, 135, 84, 0.2);
    }
    .step-circle.active {
        background: #198754;
        color: white;
        animation: pulse-green 2s infinite;
    }
    .step-circle.rejected {
        background: #dc3545;
        color: white;
        box-shadow: 0 0 0 5px rgba(220, 53, 69, 0.2);
    }
    .step-text { font-size: 14px; transition: color 0.3s; }
    .text-success { color: #198754 !important; }
    .text-danger { color: #dc3545 !important; }

    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    .animation-fade-in { animation: fadeIn 0.6s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
    {{-- Script jsPDF Manual (Tanpa html2canvas) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    let currentData = null;

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formCariPendaftar');
        const inputKode = document.getElementById('kode_pendaftaran');
        const statusPesan = document.getElementById('statusPesan');
        const btnCari = document.getElementById('btnCari');
        const resultSection = document.getElementById('resultSection');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const kode = inputKode.value.trim();
            if (!kode) return;

            // UI Loading State
            btnCari.disabled = true;
            btnCari.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Mencari...`;
            statusPesan.innerHTML = '';
            resultSection.style.display = 'none';

            // Panggil API Route
            fetch(`{{ route('api.pendaftar.data') }}?kode_pendaftaran=${kode}`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.error || 'Data tidak ditemukan') });
                    }
                    return response.json();
                })
                .then(data => {
                    currentData = data;
                    renderData(data);
                    resultSection.style.display = 'block';
                    resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(error => {
                    statusPesan.innerHTML = `<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-circle me-2"></i>${error.message}</div>`;
                })
                .finally(() => {
                    btnCari.disabled = false;
                    btnCari.innerHTML = `<i class="fas fa-search me-2"></i> Cek Status`;
                });
        });
    });

    function renderData(data) {
        // 1. Render Identitas
        document.getElementById('resKode').textContent = data.kode_pendaftaran;
        document.getElementById('resNama').textContent = data.nama_lengkap;
        document.getElementById('resPorsi').textContent = data.nomor_porsi_haji;
        document.getElementById('resTtl').textContent = `${data.tempat_lahir}, ${data.tanggal_lahir}`;

        // 2. Render Timeline & Action Area
        const rawStatus = data.status_pendaftaran || '';
        const status = rawStatus.toLowerCase(); 

        const circleVerif = document.getElementById('circleVerifikasi');
        const textVerif = document.getElementById('textVerifikasi');
        const iconVerif = document.getElementById('iconVerifikasi');
        
        const circleHasil = document.getElementById('circleHasil');
        const textHasil = document.getElementById('textHasil');
        const iconHasil = document.getElementById('iconHasil');
        const lineResult = document.getElementById('lineResult');

        const areaAccepted = document.getElementById('statusAccepted');
        const areaRejected = document.getElementById('statusRejected');
        const areaPending = document.getElementById('statusPending');

        // Reset Styles
        circleVerif.className = 'step-circle';
        circleHasil.className = 'step-circle';
        textVerif.classList.remove('text-success', 'text-danger');
        textHasil.classList.remove('text-success', 'text-danger');
        lineResult.className = 'progress-bar';
        lineResult.style.width = '0%';
        iconVerif.innerHTML = '2';
        iconHasil.innerHTML = '3';

        areaAccepted.style.display = 'none';
        areaRejected.style.display = 'none';
        areaPending.style.display = 'none';

        if (status.includes('diterima')) {
            // Status: DITERIMA
            circleVerif.classList.add('completed');
            textVerif.classList.add('text-success');
            iconVerif.innerHTML = '<i class="fas fa-check"></i>';
            
            lineResult.classList.add('bg-success');
            lineResult.style.width = '100%';

            circleHasil.classList.add('completed');
            textHasil.classList.add('text-success');
            textHasil.innerHTML = 'Diterima';
            iconHasil.innerHTML = '<i class="fas fa-check"></i>';

            areaAccepted.style.display = 'block';

        } else if (status.includes('ditolak')) {
            // Status: DITOLAK
            circleVerif.classList.add('completed');
            textVerif.classList.add('text-success');
            iconVerif.innerHTML = '<i class="fas fa-check"></i>';

            lineResult.classList.add('bg-danger');
            lineResult.style.width = '100%';

            circleHasil.classList.add('rejected');
            textHasil.classList.add('text-danger');
            textHasil.innerHTML = 'Ditolak';
            iconHasil.innerHTML = '<i class="fas fa-times"></i>';

            areaRejected.style.display = 'block';
            document.getElementById('textAlasanPenolakan').textContent = data.alasan_penolakan || 'Tidak ada alasan spesifik.';

        } else {
            // Status: MENUNGGU
            circleVerif.classList.add('active');
            textVerif.classList.add('text-success');
            areaPending.style.display = 'block';
        }
    }

    // === PDF GENERATOR MANUAL (TANPA HTML2CANVAS) - 100% PASTI MUNCUL ===
    document.getElementById('btnCetakKartu').addEventListener('click', function() {
        if (!currentData) {
            alert('Data tidak tersedia');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Membuat PDF...';

        try {
            const { jsPDF } = window.jspdf;
            
            // Landscape A4: 297mm x 210mm
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            const pageWidth = 297;
            const pageHeight = 210;
            const margin = 15;
            const contentWidth = pageWidth - (margin * 2);

            // BORDER HIJAU
            doc.setDrawColor(25, 135, 84);
            doc.setLineWidth(2);
            doc.rect(margin, margin, contentWidth, pageHeight - (margin * 2));

            // HEADER
            doc.setFontSize(28);
            doc.setTextColor(25, 135, 84);
            doc.setFont('helvetica', 'bold');
            doc.text('KBIHU ASWAJA', pageWidth / 2, 35, { align: 'center' });

            doc.setFontSize(11);
            doc.setTextColor(85, 85, 85);
            doc.setFont('helvetica', 'normal');
            doc.text('Kelompok Bimbingan Ibadah Haji dan Umrah Ahlussunnah wal Jama\'ah', pageWidth / 2, 42, { align: 'center' });

            doc.setFontSize(18);
            doc.setTextColor(51, 51, 51);
            doc.setFont('helvetica', 'bold');
            doc.text('BUKTI PENDAFTARAN ONLINE', pageWidth / 2, 52, { align: 'center' });

            // GARIS BAWAH HEADER
            doc.setDrawColor(25, 135, 84);
            doc.setLineWidth(1);
            doc.line(margin + 10, 58, pageWidth - margin - 10, 58);

            // DATA TABLE
            const startY = 75;
            const labelX = margin + 20;
            const valueX = margin + 90;
            const lineHeight = 12;
            let currentY = startY;

            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);

            // Helper function
            function addRow(label, value) {
                doc.setFont('helvetica', 'bold');
                doc.text(label, labelX, currentY);
                doc.setFont('helvetica', 'normal');
                doc.text(': ' + value, valueX, currentY);
                
                // Garis bawah
                doc.setDrawColor(238, 238, 238);
                doc.setLineWidth(0.3);
                doc.line(labelX, currentY + 2, pageWidth - margin - 20, currentY + 2);
                
                currentY += lineHeight;
            }

            addRow('Kode Pendaftaran', currentData.kode_pendaftaran);
            addRow('Nama Lengkap', currentData.nama_lengkap);
            addRow('Nomor Porsi Haji', currentData.nomor_porsi_haji);
            addRow('Tempat, Tgl Lahir', `${currentData.tempat_lahir}, ${currentData.tanggal_lahir}`);
            addRow('Tanggal Pendaftaran', currentData.tanggal_daftar);

            // STATUS BADGE
            doc.setFont('helvetica', 'bold');
            doc.text('Status Verifikasi', labelX, currentY);
            
            // Box hijau untuk status
            const badgeX = valueX + 3;
            const badgeY = currentY - 5;
            const badgeWidth = 90;
            const badgeHeight = 10;
            
            doc.setFillColor(212, 237, 218); // Light green background
            doc.setDrawColor(25, 135, 84); // Green border
            doc.setLineWidth(0.8);
            doc.roundedRect(badgeX, badgeY, badgeWidth, badgeHeight, 2, 2, 'FD');
            
            doc.setTextColor(25, 135, 84);
            doc.setFontSize(11);
            doc.text('DITERIMA', badgeX + badgeWidth / 2, currentY, { align: 'center' });

            // FOOTER
            doc.setFontSize(9);
            doc.setTextColor(119, 119, 119);
            doc.setFont('helvetica', 'normal');
            
            const footerY = pageHeight - 25;
            doc.setDrawColor(204, 204, 204);
            doc.setLineWidth(0.5);
            doc.line(margin + 20, footerY - 5, pageWidth - margin - 20, footerY - 5);
            
            doc.text('Dokumen ini diterbitkan secara elektronik oleh sistem KBIHU Aswaja.', pageWidth / 2, footerY, { align: 'center' });
            doc.text('Harap simpan dokumen ini sebagai bukti pendaftaran yang sah.', pageWidth / 2, footerY + 5, { align: 'center' });

            // SAVE PDF
            doc.save(`Kartu-${currentData.kode_pendaftaran}.pdf`);
            
            console.log('PDF berhasil dibuat!');
            
        } catch (error) {
            console.error('Error membuat PDF:', error);
            alert('Gagal membuat PDF: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    </script>
@endpush