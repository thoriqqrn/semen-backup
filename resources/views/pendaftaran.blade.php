@extends('layouts.main')

@section('title', 'Pendaftaran Online | KBIHU Aswaja')

@push('styles')
    {{-- CSS Kustom untuk Dropzone Interaktif dengan Preview --}}

    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease-in-out;
            position: relative;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            overflow: hidden;
        }

        .dropzone.is-dragover {
            border-color: #28a745;
            background-color: #e6f7ed;
        }

        .dropzone-prompt {
            text-align: center;
            display: block;
        }

        /* Preview akan menggantikan prompt, tapi tetap dalam ukuran kecil */
        .dropzone-preview-container {
            display: none;
            text-align: center;
            max-width: 100%;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .dropzone-preview-container.active {
            display: block;
        }

        .dropzone-prompt.hidden {
            display: none;
        }

        /* Preview image dibuat lebih kecil */
        .dropzone-preview-image {
            width: 100%;
            max-width: 220px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            margin: 0 auto 8px;
            display: block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dropzone-preview-frame {
            width: 100%;
            max-width: 220px;
            height: 90px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin: 0 auto 8px;
            background-color: #fff;
            display: none;
        }

        .dropzone-preview-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 8px;
            display: none;
            color: #dc3545;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .dropzone-preview-icon.active {
            display: flex;
        }

        .dropzone-filename {
            font-weight: 500;
            font-size: 0.75rem;
            color: #495057;
            word-break: break-all;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 4px;
        }

        .dropzone-change-text {
            font-size: 0.7rem;
            color: #28a745;
            text-decoration: underline;
            cursor: pointer;
        }

        .dropzone:hover .dropzone-change-text {
            color: #1e7e34;
        }

        .btn-remove-file {
            width: 28px;
            height: 28px;
            padding: 0;
            z-index: 10;
            top: 6px;
            right: 6px;
            transform: none;
        }

        /* Stepper styles tetap sama */
        .stepper .nav-link {
            background-color: #e9ecef;
            color: var(--dark-color);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
        }

        .stepper .nav-link.active,
        .stepper .nav-link.completed {
            background-color: #28a745;
            color: #fff;
        }

        .stepper .step-index {
            background-color: #adb5bd;
            color: #fff;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .stepper .nav-link.active .step-index,
        .stepper .nav-link.completed .step-index {
            background-color: #fff;
            color: #28a745;
        }

        .quota-headline {
            background: linear-gradient(135deg, #f7fff9 0%, #f1fdf6 100%);
            border: 1px solid #cfeedd;
            border-radius: 16px;
            padding: 0.95rem 1.05rem;
            box-shadow: 0 10px 24px rgba(22, 163, 74, 0.10);
        }

        .quota-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #14532d;
            margin: 0;
            text-transform: uppercase;
        }

        .quota-badge {
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.4rem 0.75rem;
        }

        .quota-card {
            border-radius: 16px;
            border: 1px solid #d9efe2;
            box-shadow: 0 12px 26px rgba(22, 163, 74, 0.14);
            background: #ffffff;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .quota-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 28px rgba(22, 163, 74, 0.18);
        }

        .quota-card .card-body {
            padding: 1rem;
        }

        .quota-chip {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 0.55rem;
        }

        .quota-number {
            font-size: 1.75rem;
            line-height: 1;
            font-weight: 800;
            margin-bottom: 0.2rem;
        }

        .quota-subtext {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.35;
            min-height: 2.2rem;
        }

        .quota-meta {
            border-top: 1px dashed #e2e8f0;
            margin-top: 0.8rem;
            padding-top: 0.72rem;
            display: flex;
            justify-content: space-between;
            gap: 0.6rem;
        }

        .quota-grid .col {
            display: flex;
        }

        .quota-grid .quota-card {
            width: 100%;
        }

        .quota-meta strong {
            display: block;
            font-size: 1rem;
            line-height: 1;
        }

        .quota-meta small {
            font-size: 0.73rem;
            color: #64748b;
        }

        .attention-box {
            background: linear-gradient(135deg, #fff4d6 0%, #ffeab3 100%);
            border: 1px solid #f4cd67;
            border-radius: 18px;
            padding: 1.35rem 1.25rem;
        }

        .attention-box h4 {
            font-size: 2rem;
            font-weight: 800;
            color: #6b4f00;
            margin-bottom: 0.7rem;
        }

        .attention-lead {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6b4f00;
            margin-bottom: 0.5rem;
        }

        .attention-box ul {
            margin-bottom: 0;
            padding-left: 1.35rem;
        }

        .attention-box li {
            font-size: 1.22rem;
            line-height: 1.55;
            color: #5b4200;
            margin-bottom: 0.35rem;
        }

        .quota-full-card {
            border: 1px solid #fecaca;
            border-radius: 16px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            box-shadow: 0 12px 24px rgba(220, 38, 38, 0.10);
        }

        .btn-daftar-disabled {
            background-color: #adb5bd !important;
            border-color: #adb5bd !important;
            color: #ffffff !important;
            cursor: not-allowed;
            opacity: 0.85;
            box-shadow: none !important;
        }

        @media (max-width: 768px) {
            .attention-box h4 {
                font-size: 1.65rem;
            }

            .attention-box li {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 576px) {
            .stepper {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.4rem;
                justify-content: initial !important;
            }

            .stepper .nav-item {
                width: 100%;
                margin: 0 !important;
            }

            .stepper .nav-link {
                flex-direction: column;
                justify-content: center;
                gap: 0.2rem;
                padding: 0.5rem 0.3rem;
                border-radius: 12px;
                min-height: 64px;
                font-size: 0.78rem;
                text-align: center;
                line-height: 1.2;
            }

            .stepper .step-index {
                margin-right: 0;
                width: 22px;
                height: 22px;
                font-size: 0.75rem;
            }
        }
    </style>
    
    {{-- SweetAlert2 CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

    @php
        $isKuotaPenuh = ($sisa_slot <= 0);
    @endphp

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Formulir Pendaftaran Haji</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Silakan lengkapi data diri dan unggah dokumen persyaratan Anda.
                Tim kami akan segera menghubungi Anda untuk proses selanjutnya.</p>
        </div>
    </div>

    <!-- 2. Formulir Pendaftaran Section -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    {{-- Letakkan ini di bawah <div class="col-lg-10"> dan di atas alert peringatan --}}
                        @if (!$isKuotaPenuh)
                                <div id="infoCardsPendaftaran" style="{{ $errors->any() || session('error') ? 'display: none;' : '' }}">
                                    <div class="quota-headline d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                        <p class="quota-title">KUOTA HARI INI PER {{ $tanggal_hari_ini }}</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge text-bg-success quota-badge">Kuota Ring 1 + Umum: {{ $max_slots }}</span>
                                            <span class="badge text-bg-primary quota-badge">Terisi Hari Ini: {{ $peserta_hari_ini }}</span>
                                            <span class="badge text-bg-warning quota-badge">Sisa Hari Ini: {{ $sisa_slot }}</span>
                                        </div>
                                    </div>

                                    {{-- 3 Kotak Informasi: Ring 1, Kuota Umum, dan Nomor Porsi --}}
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4 quota-grid">
                                        {{-- KOTAK RING 1 --}}
                                        <div class="col">
                                            <div class="card quota-card">
                                                <div class="card-body">
                                                    <div class="quota-chip" style="background:#dcfce7;color:#166534;">
                                                        <i class="fas fa-id-card"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-1">RING 1</h5>
                                                    <p class="quota-subtext mb-0">Berdasarkan KTP, bertempat tinggal dan berdomisili di sekitar Pabrik Semen Indonesia Gresik.</p>
                                                    <div class="mt-2 quota-number" style="color:#166534;">{{ $kuota_ring1 }}</div>
                                                    <small class="text-muted">Total Kuota Ring 1</small>

                                                    <div class="quota-meta">
                                                        <div>
                                                            <strong style="color:#166534;">{{ $peserta_ring1 }}</strong>
                                                            <small>Terdaftar</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <strong style="color:#166534;">{{ $sisa_ring1 }}</strong>
                                                            <small>Sisa Kuota</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- KOTAK KUOTA UMUM --}}
                                        <div class="col">
                                            <div class="card quota-card">
                                                <div class="card-body">
                                                    <div class="quota-chip" style="background:#dbeafe;color:#1d4ed8;">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-1">UMUM</h5>
                                                    <p class="quota-subtext mb-0">Pendaftar di luar kriteria Ring 1 atau dari luar Kabupaten Gresik.</p>
                                                    <div class="mt-2 quota-number" style="color:#1d4ed8;">{{ $kuota_umum }}</div>
                                                    <small class="text-muted">Total Kuota Umum</small>

                                                    <div class="quota-meta">
                                                        <div>
                                                            <strong style="color:#1d4ed8;">{{ $peserta_umum }}</strong>
                                                            <small>Terdaftar</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <strong style="color:#1d4ed8;">{{ $sisa_umum }}</strong>
                                                            <small>Sisa Kuota</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        {{-- KOTAK NOMOR PORSI --}}
                                        <div class="col">
                                            <div class="card quota-card">
                                                <div class="card-body">
                                                    <div class="quota-chip" style="background:#fee2e2;color:#dc2626;">
                                                        <i class="fas fa-ticket-alt"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-1">Porsi Tertinggi Saat Ini</h5>
                                                    <p class="quota-subtext mb-0">Diperbarui sesuai ketentuan BP Haji RI dan dapat berubah sewaktu-waktu.</p>
                                                    <div class="mt-2 quota-number" style="color:#dc2626;">{{ $max_porsi }}</div>
                                                    <small class="text-muted">Nomor Porsi Tertinggi</small>

                                                    <div class="quota-meta">
                                                        <div>
                                                            <strong style="color:#dc2626;">{{ $max_slots }}</strong>
                                                            <small>Kuota Total</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <strong style="color:#dc2626;">{{ $sisa_slot }}</strong>
                                                            <small>Sisa Total</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @else
                            <div class="card quota-full-card mb-4" role="alert">
                                <div class="card-body p-4 text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-times text-danger" style="font-size: 2rem;"></i>
                                    </div>
                                    <h4 class="alert-heading fw-bold text-danger">Kuota Hari Ini Habis / Sudah Terpenuhi</h4>
                                    <p class="mb-2 fs-5">Mohon maaf, pendaftaran untuk hari ini sudah ditutup karena kuota penuh.</p>
                                    <p class="mb-0 text-muted">Silakan daftar kembali besok atau menunggu jadwal pembukaan kuota berikutnya dari admin.</p>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h5 class="fw-bold">Terdapat Kesalahan!</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div id="introPendaftaran" style="{{ $errors->any() || session('error') ? 'display: none;' : '' }}">
                            <div class="attention-box d-flex align-items-start p-4 mb-4">
                                <i class="fas fa-exclamation-triangle fa-2x me-4"></i>
                                <div>
                                    <h4 class="alert-heading fw-bold">Perhatian Sebelum Mengisi!</h4>
                                    <p class="attention-lead">Mohon baca perlahan agar proses verifikasi lebih cepat dan tidak ditolak.</p>
                                    <ul>
                                        <li>Pastikan nama yang Anda masukkan sama persis dengan yang tertera pada  KTP/KK/Akte Kelahiran/Buku Nikah/Ijazah (SD/SLTP/SLTA).</li>
                                        <li>Pastikan Porsi Haji Anda masuk dalam Keberangkatan Tahun Depan.</li>
                                        <li>Semua dokumen yang diunggah harus dapat dibaca dengan jelas (tidak buram).</li>
                                        <li>Ukuran maksimal untuk setiap file adalah 2MB. Format yang diterima: JPG, PNG, PDF.
                                        </li>
                                        <li>Khusus Ring 1: berdasarkan KTP, bertempat tinggal dan berdomisili di sekitar Pabrik Semen Indonesia Gresik.</li>
                                        <li>Jika kuota harian sudah penuh, pendaftaran akan dibuka kembali pada hari berikutnya.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-center mb-5">
                                <button type="button"
                                    class="btn {{ !$isKuotaPenuh ? 'btn-success' : 'btn-daftar-disabled' }} btn-lg px-5 py-3 shadow fw-bold"
                                    id="btnMulaiDaftar"
                                    {{ !$isKuotaPenuh ? '' : 'disabled aria-disabled=true title=Kuota hari ini penuh' }}>
                                    <i class="fas fa-edit me-2"></i> Daftar Sekarang
                                </button>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-custom" id="formPendaftaranCard" style="{{ $errors->any() || session('error') ? '' : 'display: none;' }}">
                            <div class="card-body p-4 p-md-5">

                                <ul class="nav nav-pills justify-content-center mb-4 stepper" id="stepper" role="tablist">
                                    <li class="nav-item mx-2" role="presentation"><a class="nav-link active" id="tab-data"
                                            data-bs-toggle="tab" href="#step1" role="tab" aria-controls="step1"
                                            aria-selected="true"><span class="step-index">1</span> Data Diri</a></li>
                                    <li class="nav-item mx-2" role="presentation"><a class="nav-link" id="tab-doc"
                                            data-bs-toggle="tab" href="#step2" role="tab" aria-controls="step2"
                                            aria-selected="false"><span class="step-index">2</span> Dokumen</a></li>
                                    <li class="nav-item mx-2" role="presentation"><a class="nav-link" id="tab-review"
                                            data-bs-toggle="tab" href="#step3" role="tab" aria-controls="step3"
                                            aria-selected="false"><span class="step-index">3</span> Review & Kirim</a></li>
                                </ul>

                                <div class="progress mb-5" style="height: 10px; border-radius: 20px;">
                                    <div id="stepProgress" class="progress-bar bg-success" role="progressbar"
                                        style="width: 33.3%;" aria-valuenow="33.3" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>

                                <form id="formPendaftaran" action="{{ route('pendaftaran.store') }}" method="POST"
                                    enctype="multipart/form-data" class="needs-validation" novalidate>
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="step1" role="tabpanel"
                                            aria-labelledby="tab-data">
                                            <div class="p-4 rounded-custom" style="background-color: #f8f9fa;">
                                                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                                    <h3 class="fw-bold mb-0">Data Diri Calon Jamaah</h3>
                                                    <button type="button" class="btn btn-primary" id="btnIsiOtomatisKTP">
                                                        <i class="fas fa-id-card me-2"></i>Isi Otomatis dengan KTP
                                                    </button>
                                                </div>
                                                <div class="row g-4">
                                                    <div class="col-12"><label for="nama_lengkap"
                                                            class="form-label fw-bold">1.
                                                            Nama Lengkap (Sesuai KTP)</label><input type="text"
                                                            id="nama_lengkap" name="nama_lengkap" class="form-control"
                                                            value="{{ old('nama_lengkap') }}" required>
                                                        <div class="invalid-feedback">Nama lengkap wajib diisi.</div>
                                                    </div>
                                                    <div class="col-md-6"><label for="tempat_lahir"
                                                            class="form-label fw-bold">2. Tempat Lahir</label><input
                                                            type="text" id="tempat_lahir" name="tempat_lahir"
                                                            class="form-control" value="{{ old('tempat_lahir') }}" required>
                                                        <div class="invalid-feedback">Tempat lahir wajib diisi.</div>
                                                    </div>
                                                    <div class="col-md-6"><label for="tanggal_lahir"
                                                            class="form-label fw-bold">Tanggal Lahir</label><input
                                                            type="date" id="tanggal_lahir" name="tanggal_lahir"
                                                            class="form-control" value="{{ old('tanggal_lahir') }}"
                                                            required>
                                                        <div class="invalid-feedback">Tanggal lahir wajib diisi.</div>
                                                    </div>
                                                    
                                                    {{-- PILIHAN LOKASI: GRESIK ATAU LUAR GRESIK --}}
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">3. Alamat</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="jenis_lokasi" 
                                                                    id="lokasiGresik" value="gresik" checked>
                                                                <label class="form-check-label" for="lokasiGresik">
                                                                    Kabupaten Gresik
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="jenis_lokasi" 
                                                                    id="lokasiLuar" value="luar">
                                                                <label class="form-check-label" for="lokasiLuar">
                                                                    Luar Kabupaten Gresik
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- FORM UNTUK GRESIK (DROPDOWN) --}}
                                                    <div id="formGresik">
                                                        <div class="col-md-6">
                                                            <label for="kecamatan" class="form-label">Kecamatan</label>
                                                            <select name="kecamatan_id" id="kecamatan" class="form-select">
                                                                <option value="">Pilih Kecamatan...</option>
                                                            </select>
                                                            <div class="invalid-feedback">Kecamatan wajib dipilih.</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="kelurahan" class="form-label">Kelurahan / Desa</label>
                                                            <select name="kelurahan_id" id="kelurahan" class="form-select" disabled>
                                                                <option value="">Pilih Kecamatan Terlebih Dahulu</option>
                                                            </select>
                                                            <div class="invalid-feedback">Kelurahan wajib dipilih.</div>
                                                        </div>
                                                    </div>

                                                    {{-- FORM UNTUK LUAR GRESIK (TEXT INPUT) --}}
                                                    <div id="formLuarGresik" style="display: none;">
                                                        <div class="col-md-4">
                                                            <label for="kabupaten_kota" class="form-label">Kabupaten/Kota</label>
                                                            <input type="text" id="kabupaten_kota" name="kabupaten_kota" 
                                                                class="form-control" placeholder="Contoh: Surabaya">
                                                            <div class="invalid-feedback">Kabupaten/Kota wajib diisi.</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="kecamatan_manual" class="form-label">Kecamatan</label>
                                                            <input type="text" id="kecamatan_manual" name="kecamatan_manual" 
                                                                class="form-control" placeholder="Contoh: Gubeng">
                                                            <div class="invalid-feedback">Kecamatan wajib diisi.</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="kelurahan_manual" class="form-label">Kelurahan/Desa</label>
                                                            <input type="text" id="kelurahan_manual" name="kelurahan_manual" 
                                                                class="form-control" placeholder="Contoh: Airlangga">
                                                            <div class="invalid-feedback">Kelurahan/Desa wajib diisi.</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="alamat" class="form-label">Detail Alamat (Nama Jalan,
                                                            No. Rumah, RT/RW)</label>
                                                        <textarea id="alamat" name="alamat" rows="2" class="form-control"
                                                            placeholder="Contoh: Jl. Pahlawan No. 123, RT 01 / RW 02"
                                                            required>{{ old('alamat') }}</textarea>
                                                        <div class="invalid-feedback">Detail alamat wajib diisi.</div>
                                                    </div>
                                                    {{-- AKHIR FORM LOKASI --}}
                                                    <div class="col-md-6"><label for="telepon" class="form-label fw-bold">4.
                                                            Telepon / HP (Aktif WhatsApp)</label><input type="tel"
                                                            id="telepon" name="telepon" class="form-control"
                                                            value="{{ old('telepon') }}" required
                                                            placeholder="08xxxxxxxxxx">
                                                        <div class="invalid-feedback">Nomor HP wajib diisi.</div>
                                                    </div>
                                                    
                                                    {{-- JENIS PORSI --}}
                                                    <div class="col-md-6">
                                                        <label for="jenis_porsi" class="form-label fw-bold">5. Jenis Porsi</label>
                                                        <select id="jenis_porsi" name="jenis_porsi" class="form-select" required>
                                                            <option value="" {{ old('jenis_porsi') == '' ? 'selected' : '' }}>-- Pilih Jenis Porsi --</option>
                                                            <option value="berangkat" {{ old('jenis_porsi') == 'berangkat' ? 'selected' : '' }}>Berangkat</option>
                                                            <option value="penggabungan" {{ old('jenis_porsi') == 'penggabungan' ? 'selected' : '' }}>Penggabungan</option>
                                                            <option value="mutasi" {{ old('jenis_porsi') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                                                        </select>
                                                        <div class="invalid-feedback">Jenis porsi wajib dipilih.</div>
                                                    </div>
                                                    
                                                    {{-- NOMOR PORSI UTAMA (MUNCUL SETELAH PILIH JENIS) --}}
                                                    <div class="col-md-6" id="fieldNomorPorsi" style="display: none;">
                                                        <label for="nomor_porsi" class="form-label fw-bold">6. Nomor Porsi Haji</label>
                                                        <input type="text" id="nomor_porsi" name="nomor_porsi" class="form-control"
                                                            value="{{ old('nomor_porsi') }}" required>
                                                        <small class="text-muted" id="hintNomorPorsi">
                                                            <i class="fas fa-info-circle"></i> Nomor porsi maksimal: <strong>{{ $max_porsi ?? 999999 }}</strong>
                                                        </small>
                                                        <div class="invalid-feedback">Nomor porsi wajib diisi.</div>
                                                    </div>
                                                    
                                                    {{-- NOMOR PORSI PENGGABUNGAN (MUNCUL JIKA PILIH PENGGABUNGAN) --}}
                                                    <div class="col-md-6" id="fieldPorsiPenggabungan" style="display: none;">
                                                        <label for="nomor_porsi_penggabungan" class="form-label fw-bold">Nomor Porsi Penggabungan</label>
                                                        <input type="text" id="nomor_porsi_penggabungan" name="nomor_porsi_penggabungan" class="form-control"
                                                            value="{{ old('nomor_porsi_penggabungan') }}">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle"></i> Isi nomor porsi yang akan digabungkan
                                                        </small>
                                                        <div class="invalid-feedback">Nomor porsi penggabungan wajib diisi.</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="button" class="btn btn-success btn-lg px-4 next-step">Lanjut
                                                    ke
                                                    Dokumen <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="tab-doc">
                                            <div class="p-4 rounded-custom" style="background-color: #f8f9fa;">
                                                <h3 class="fw-bold border-bottom pb-3 mb-4">Lampiran Dokumen Persyaratan
                                                </h3>
                                                <div class="row g-4">
                                                    @php
                                                        $dokumenList = [
                                                            ['name' => 'file_ktp', 'label' => '1. KTP', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_kk', 'label' => '2. Kartu Keluarga (KK)', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_akta', 'label' => '3. Akta Kelahiran', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_nikah', 'label' => '4. Surat Nikah', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_ijazah', 'label' => '5. Ijazah', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_bpih', 'label' => '6. Bukti Setoran Awal (BPIH)', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_spph', 'label' => '7. Surat Pendaftaran Pergi Haji (SPPH)', 'required' => true, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_paspor', 'label' => '8. Paspor (Jika Ada)', 'required' => false, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_booster1', 'label' => '9. Vaksin Booster 1 (Jika Ada)', 'required' => false, 'accept' => 'image/*,application/pdf'],
                                                            ['name' => 'file_booster2', 'label' => '10. Vaksin Booster 2 (Jika Ada)', 'required' => false, 'accept' => 'image/*,application/pdf'],
                                                        ];
                                                    @endphp

                                                    @foreach ($dokumenList as $dok)
                                                        <div class="col-md-6">
                                                            <label for="{{ $dok['name'] }}"
                                                                class="form-label fw-bold">{{ $dok['label'] }}</label>
                                                            <div class="dropzone">
                                                                <input type="file" name="{{ $dok['name'] }}"
                                                                    id="{{ $dok['name'] }}" class="d-none"
                                                                    accept="{{ $dok['accept'] }}" {{ $dok['required'] ? 'required' : '' }}>
                                                                <div class="dropzone-prompt">
                                                                    <i class="fas fa-cloud-upload-alt d-block mb-2 fa-2x"></i>
                                                                    Klik atau seret file ke sini
                                                                </div>
                                                                <div class="dropzone-preview-container position-relative">
                                                                    <img src="" class="dropzone-preview-image" alt="Preview">
                                                                    <iframe class="dropzone-preview-frame" title="Preview PDF"></iframe>
                                                                    <div class="dropzone-preview-icon" aria-hidden="true">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                    </div>
                                                                    <div class="dropzone-preview-overlay">
                                                                        <div class="dropzone-filename"></div>
                                                                        <div class="dropzone-change-text">Klik untuk ganti</div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-danger position-absolute rounded-circle btn-remove-file" aria-label="Hapus file">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                {{ explode(' ', $dok['label'])[1] ?? $dok['label'] }} wajib
                                                                diunggah.
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-4">
                                                <button type="button" class="btn btn-outline-secondary btn-lg prev-step"><i
                                                        class="fas fa-arrow-left me-2"></i> Kembali</button>
                                                <button type="button" class="btn btn-success btn-lg px-4 next-step">Lanjut
                                                    ke
                                                    Review <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="tab-review">
                                            <div class="p-4 rounded-custom" style="background-color: #f8f9fa;">
                                                <h3 class="fw-bold border-bottom pb-3 mb-4">Review Data & Pendaftaran</h3>
                                                <p class="text-muted">Mohon periksa kembali semua data yang telah Anda
                                                    masukkan.
                                                    Pastikan tidak ada kesalahan sebelum mengirimkan formulir.</p>
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="persetujuan" name="persetujuan" required>
                                                    <label class="form-check-label" for="persetujuan">Saya menyatakan bahwa
                                                        data
                                                        yang diisikan adalah benar dan dokumen yang diunggah sesuai
                                                        aslinya.</label>
                                                    <div class="invalid-feedback">Anda harus menyetujui pernyataan ini.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-4">
                                                <button type="button" class="btn btn-outline-secondary btn-lg prev-step"><i
                                                        class="fas fa-arrow-left me-2"></i> Kembali</button>
                                                <button type="submit" class="btn btn-success btn-lg px-5"><i
                                                        class="fas fa-paper-plane me-2"></i> Kirim Pendaftaran</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                {{-- MODAL ISI OTOMATIS KTP --}}
                                <div class="modal fade" id="modalOCR_KTP" tabindex="-1" aria-labelledby="modalOCRLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="modalOCRLabel">
                                                    <i class="fas fa-id-card me-2"></i>Scan KTP Otomatis
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <p class="text-muted">Pilih metode untuk scan KTP Anda:</p>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-outline-primary w-100 py-3" id="btnBukaKamera">
                                                                <i class="fas fa-camera fa-3x mb-2"></i>
                                                                <h6>Buka Kamera</h6>
                                                            </button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-outline-success w-100 py-3" id="btnUploadKTP">
                                                                <i class="fas fa-upload fa-3x mb-2"></i>
                                                                <h6>Upload Foto</h6>
                                                            </button>
                                                            <input type="file" id="inputUploadKTP" accept="image/*" class="d-none">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Area Kamera --}}
                                                <div id="cameraArea" class="d-none">
                                                    <video id="videoKTP" width="100%" autoplay class="rounded mb-3"></video>
                                                    <canvas id="canvasKTP" class="d-none"></canvas>
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-success" id="btnCaptureKTP">
                                                            <i class="fas fa-camera me-2"></i>Ambil Foto
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" id="btnBatalCamera">
                                                            <i class="fas fa-times me-2"></i>Batal
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                {{-- Preview & Processing --}}
                                                <div id="previewArea" class="d-none">
                                                    <img id="previewKTP" src="" class="img-fluid rounded mb-3" alt="Preview KTP">
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-primary flex-grow-1" id="btnProsesOCR">
                                                            <i class="fas fa-magic me-2"></i>Proses Scan
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" id="btnUlangi">
                                                            <i class="fas fa-redo me-2"></i>Ulangi
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                {{-- Loading --}}
                                                <div id="loadingOCR" class="text-center d-none">
                                                    <div class="spinner-border text-primary mb-3" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="text-muted">Sedang membaca KTP Anda...<br>Mohon tunggu sebentar</p>
                                                </div>
                                            </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // == TOGGLE FULL FORM ==
            const btnMulaiDaftar = document.getElementById('btnMulaiDaftar');
            const introPendaftaran = document.getElementById('introPendaftaran');
            const formPendaftaranCard = document.getElementById('formPendaftaranCard');
            const infoCardsPendaftaran = document.getElementById('infoCardsPendaftaran');
            
            if(btnMulaiDaftar && !btnMulaiDaftar.disabled) {
                btnMulaiDaftar.addEventListener('click', function() {
                    introPendaftaran.style.display = 'none';
                    formPendaftaranCard.style.display = 'block';
                    if (infoCardsPendaftaran) {
                        infoCardsPendaftaran.style.display = 'none';
                    }
                    
                    formPendaftaranCard.scrollIntoView({ behavior: 'smooth' });
                });
            }

            // == TOGGLE FORM GRESIK / LUAR GRESIK ==
            const lokasiGresik = document.getElementById('lokasiGresik');
            const lokasiLuar = document.getElementById('lokasiLuar');
            const formGresik = document.getElementById('formGresik');
            const formLuarGresik = document.getElementById('formLuarGresik');
            
            // Dropdown elements
            const kecamatanSelect = document.getElementById('kecamatan');
            const kelurahanSelect = document.getElementById('kelurahan');
            
            // Manual input elements
            const kabupatenInput = document.getElementById('kabupaten_kota');
            const kecamatanInput = document.getElementById('kecamatan_manual');
            const kelurahanInput = document.getElementById('kelurahan_manual');

            function toggleFormLokasi() {
                if (lokasiGresik.checked) {
                    // Tampilkan form dropdown Gresik
                    formGresik.style.display = 'contents';
                    formLuarGresik.style.display = 'none';
                    
                    // Set required untuk dropdown
                    kecamatanSelect.required = true;
                    kelurahanSelect.required = true;
                    
                    // Remove required dari input manual
                    kabupatenInput.required = false;
                    kecamatanInput.required = false;
                    kelurahanInput.required = false;
                    
                    // Clear manual input values
                    kabupatenInput.value = '';
                    kecamatanInput.value = '';
                    kelurahanInput.value = '';
                } else {
                    // Tampilkan form input manual
                    formGresik.style.display = 'none';
                    formLuarGresik.style.display = 'contents';
                    
                    // Remove required dari dropdown
                    kecamatanSelect.required = false;
                    kelurahanSelect.required = false;
                    
                    // Set required untuk input manual
                    kabupatenInput.required = true;
                    kecamatanInput.required = true;
                    kelurahanInput.required = true;
                    
                    // Clear dropdown values
                    kecamatanSelect.value = '';
                    kelurahanSelect.value = '';
                    kelurahanSelect.disabled = true;
                }
            }

            lokasiGresik.addEventListener('change', toggleFormLokasi);
            lokasiLuar.addEventListener('change', toggleFormLokasi);

            // == TOGGLE JENIS PORSI (BERANGKAT / PENGGABUNGAN) ==
            const jenisPorsiSelect = document.getElementById('jenis_porsi');
            const fieldNomorPorsi = document.getElementById('fieldNomorPorsi');
            const fieldPorsiPenggabungan = document.getElementById('fieldPorsiPenggabungan');
            const inputNomorPorsi = document.getElementById('nomor_porsi');
            const inputPorsiPenggabungan = document.getElementById('nomor_porsi_penggabungan');
            
            function toggleJenisPorsi() {
                const jenisValue = jenisPorsiSelect.value;
                
                if (jenisValue === '') {
                    // Belum pilih - sembunyikan semua field porsi
                    fieldNomorPorsi.style.display = 'none';
                    fieldPorsiPenggabungan.style.display = 'none';
                    inputNomorPorsi.required = false;
                    inputPorsiPenggabungan.required = false;
                } else if (jenisValue === 'penggabungan') {
                    // Penggabungan - tampilkan kedua field
                    fieldNomorPorsi.style.display = 'block';
                    fieldPorsiPenggabungan.style.display = 'block';
                    inputNomorPorsi.required = true;
                    inputPorsiPenggabungan.required = true;
                } else {
                    // Berangkat atau Mutasi - hanya tampilkan field nomor porsi utama
                    fieldNomorPorsi.style.display = 'block';
                    fieldPorsiPenggabungan.style.display = 'none';
                    inputNomorPorsi.required = true;
                    inputPorsiPenggabungan.required = false;
                    inputPorsiPenggabungan.value = '';
                }
            }
            
            // Event listener untuk perubahan jenis porsi
            jenisPorsiSelect.addEventListener('change', toggleJenisPorsi);
            
            // Panggil saat halaman dimuat (untuk old() value)
            toggleJenisPorsi();

            // == LOGIKA DROPDOWN ALAMAT UNTUK GRESIK ==
            // Ambil data kecamatan saat halaman dimuat
            fetch('{{ route("api.kecamatan") }}')
                .then(response => response.json())
                .then(data => {
                    data.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.nama_kecamatan;
                        kecamatanSelect.appendChild(option);
                    });
                });

            // Event listener saat kecamatan dipilih
            kecamatanSelect.addEventListener('change', function () {
                const kecamatanId = this.value;
                kelurahanSelect.innerHTML = '<option value="">Memuat...</option>'; // Reset
                kelurahanSelect.disabled = true;

                if (kecamatanId) {
                    fetch(`{{ url('api/kelurahan') }}?kecamatan_id=${kecamatanId}`)
                        .then(response => response.json())
                        .then(data => {
                            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan / Desa...</option>';
                            data.forEach(kelurahan => {
                                const option = document.createElement('option');
                                option.value = kelurahan.id;
                                option.textContent = kelurahan.nama_kelurahan;
                                kelurahanSelect.appendChild(option);
                            });
                            kelurahanSelect.disabled = false;
                        });
                } else {
                    kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan Terlebih Dahulu</option>';
                }
            });
            
            const form = document.getElementById('formPendaftaran');
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            const progress = document.getElementById('stepProgress');
            const stepLinks = document.querySelectorAll('#stepper a');
            const tabInstances = Array.from(stepLinks).map(link => new bootstrap.Tab(link));

            function updateUI(activeIndex) {
                const pct = ((activeIndex) / (stepLinks.length - 1)) * 100;
                progress.style.width = pct + '%';
                stepLinks.forEach((link, index) => {
                    if (index < activeIndex) {
                        link.classList.add('completed');
                        link.classList.remove('active');
                    } else if (index === activeIndex) {
                        link.classList.add('active');
                        link.classList.add('completed');
                    } else {
                        link.classList.remove('active');
                        link.classList.remove('completed');
                    }
                });
            }

            function getActiveIndex() {
                return Array.from(document.querySelectorAll('.tab-pane')).findIndex(pane => pane.classList.contains('active'));
            }

            nextButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const activeIndex = getActiveIndex();
                    const activePane = document.querySelectorAll('.tab-pane')[activeIndex];
                    const controls = activePane.querySelectorAll('input[required], textarea[required], select[required]');
                    let allValid = true;

                    controls.forEach(input => {
                        if (!input.checkValidity()) {
                            allValid = false;
                        }
                    });

                    if (allValid) {
                        // CEK NOMOR PORSI SAAT DARI STEP 1 KE STEP 2 (DATA DIRI → DOKUMEN)
                        if (activeIndex === 0) {
                            const jenisPorsi = document.getElementById('jenis_porsi').value;
                            const inputPorsi = document.getElementById('nomor_porsi');
                            const maxPorsi = {{ $max_porsi ?? 999999 }};
                            const nilaiPorsi = parseInt(inputPorsi.value) || 0;
                            
                            // HANYA CEK UNTUK JENIS BERANGKAT DAN MUTASI, SKIP UNTUK PENGGABUNGAN
                            if ((jenisPorsi === 'berangkat' || jenisPorsi === 'mutasi') && nilaiPorsi > maxPorsi) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Nomor Porsi Melebihi Batas!',
                                    html: `<p>Nomor Porsi Anda <strong>${nilaiPorsi.toLocaleString('id-ID')}</strong> melebihi Porsi Tertinggi Departemen Agama tahun ini: <strong>${maxPorsi.toLocaleString('id-ID')}</strong></p><p class="text-danger fw-bold">Pendaftaran tidak dapat dilanjutkan.</p>`,
                                    confirmButtonText: 'Ubah Nomor Porsi',
                                    confirmButtonColor: '#dc3545'
                                });
                                return; // Stop navigation
                            }
                        }
                        
                        form.classList.remove('was-validated');
                        if (activeIndex < tabInstances.length - 1) {
                            tabInstances[activeIndex + 1].show();
                        }
                    } else {
                        form.classList.add('was-validated');
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const activeIndex = getActiveIndex();
                    if (activeIndex > 0) {
                        tabInstances[activeIndex - 1].show();
                    }
                });
            });

            stepLinks.forEach((link) => {
                link.addEventListener('shown.bs.tab', function () {
                    updateUI(getActiveIndex());
                });
            });

            // ========================================
            // INDEXED DB UNTUK FILE UPLOAD
            // ========================================
            const dbName = 'AswajaHajiDB';
            const storeName = 'files_draft';
            let db;

            const initDB = new Promise((resolve, reject) => {
                const request = indexedDB.open(dbName, 1);
                request.onupgradeneeded = (e) => {
                    e.target.result.createObjectStore(storeName);
                };
                request.onsuccess = (e) => {
                    db = e.target.result;
                    resolve(db);
                };
                request.onerror = (e) => reject(e);
            });

            const saveFileToDB = async (key, file) => {
                await initDB;
                return new Promise((resolve, reject) => {
                    const tx = db.transaction(storeName, 'readwrite');
                    const store = tx.objectStore(storeName);
                    store.put(file, key);
                    tx.oncomplete = () => resolve();
                    tx.onerror = (e) => reject(e);
                });
            };

            const getFileFromDB = async (key) => {
                await initDB;
                return new Promise((resolve, reject) => {
                    const tx = db.transaction(storeName, 'readonly');
                    const store = tx.objectStore(storeName);
                    const request = store.get(key);
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = (e) => reject(e);
                });
            };

            const removeFileFromDB = async (key) => {
                await initDB;
                return new Promise((resolve, reject) => {
                    const tx = db.transaction(storeName, 'readwrite');
                    const store = tx.objectStore(storeName);
                    store.delete(key);
                    tx.oncomplete = () => resolve();
                    tx.onerror = (e) => reject(e);
                });
            };

            const clearFilesDB = async () => {
                await initDB;
                return new Promise((resolve, reject) => {
                    const tx = db.transaction(storeName, 'readwrite');
                    const store = tx.objectStore(storeName);
                    store.clear();
                    tx.oncomplete = () => resolve();
                    tx.onerror = (e) => reject(e);
                });
            };

            // Dropzone handling dengan preview yang lebih kompak
            const dropzones = document.querySelectorAll('.dropzone');
            dropzones.forEach(dropzone => {
                const input = dropzone.querySelector('input[type="file"]');
                const prompt = dropzone.querySelector('.dropzone-prompt');
                const previewContainer = dropzone.querySelector('.dropzone-preview-container');
                const previewImage = dropzone.querySelector('.dropzone-preview-image');
                const previewFrame = dropzone.querySelector('.dropzone-preview-frame');
                const previewIcon = dropzone.querySelector('.dropzone-preview-icon');
                const filenameDiv = dropzone.querySelector('.dropzone-filename');
                const btnRemove = dropzone.querySelector('.btn-remove-file');
                let preventInitialRestore = false;
                let currentPreviewUrl = null;

                const clearPreviewMedia = () => {
                    if (currentPreviewUrl) {
                        URL.revokeObjectURL(currentPreviewUrl);
                        currentPreviewUrl = null;
                    }

                    previewImage.style.display = 'none';
                    previewImage.removeAttribute('src');

                    if (previewFrame) {
                        previewFrame.style.display = 'none';
                        previewFrame.removeAttribute('src');
                    }

                    if (previewIcon) {
                        previewIcon.classList.remove('active');
                    }
                };

                const resetDropzoneState = () => {
                    clearPreviewMedia();
                    prompt.classList.remove('hidden');
                    previewContainer.classList.remove('active');
                    filenameDiv.textContent = '';
                    filenameDiv.removeAttribute('title');
                };

                // Load file from IndexedDB on startup
                getFileFromDB(input.name).then(file => {
                    if (file && !preventInitialRestore && input.files.length === 0) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        updatePreview(file);
                    }
                }).catch(e => console.error("Error loading file:", e));

                dropzone.addEventListener('click', (e) => {
                    if(e.target.closest('.btn-remove-file')) return; // Abaikan jika klik tombol hapus
                    input.click();
                });

                input.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                if (btnRemove) {
                    btnRemove.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        preventInitialRestore = true;
                        input.value = ''; // Reset file input
                        resetDropzoneState();
                        await removeFileFromDB(input.name); // Hapus dari db
                    });
                }

                const updatePreview = (file) => {
                    clearPreviewMedia();

                    // Sembunyikan prompt dan tampilkan preview
                    prompt.classList.add('hidden');
                    previewContainer.classList.add('active');

                    // Set filename dengan ellipsis jika terlalu panjang
                    filenameDiv.textContent = file.name;
                    filenameDiv.title = file.name; // Tooltip untuk nama lengkap

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = () => {
                            previewImage.src = reader.result;
                            previewImage.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf' && previewFrame) {
                        currentPreviewUrl = URL.createObjectURL(file);
                        previewFrame.src = `${currentPreviewUrl}#toolbar=0&navpanes=0&scrollbar=0`;
                        previewFrame.style.display = 'block';
                    } else {
                        previewIcon?.classList.add('active');
                    }
                };

                input.addEventListener('change', () => {
                    if (input.files.length > 0) {
                        preventInitialRestore = true;
                        updatePreview(input.files[0]);
                        saveFileToDB(input.name, input.files[0]); // Simpan ke db
                    } else {
                        resetDropzoneState();
                    }
                });

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('is-dragover');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('is-dragover');
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('is-dragover');
                    if (e.dataTransfer.files.length > 0) {
                        preventInitialRestore = true;
                        const file = e.dataTransfer.files[0];
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        updatePreview(file);
                        saveFileToDB(input.name, file); // Simpan ke db
                    }
                });
            });

            updateUI(0);
            
            // ========================================
            // ISI OTOMATIS DENGAN KTP (OCR GEMINI)
            // ========================================
            const modalOCR = new bootstrap.Modal(document.getElementById('modalOCR_KTP'));
            const btnIsiOtomatis = document.getElementById('btnIsiOtomatisKTP');
            const btnBukaKamera = document.getElementById('btnBukaKamera');
            const btnUploadKTP = document.getElementById('btnUploadKTP');
            const inputUploadKTP = document.getElementById('inputUploadKTP');
            const cameraArea = document.getElementById('cameraArea');
            const previewArea = document.getElementById('previewArea');
            const loadingOCR = document.getElementById('loadingOCR');
            const videoKTP = document.getElementById('videoKTP');
            const canvasKTP = document.getElementById('canvasKTP');
            const previewKTP = document.getElementById('previewKTP');
            
            let stream = null;
            let capturedImage = null;
            
            // Buka modal OCR
            btnIsiOtomatis.addEventListener('click', () => {
                modalOCR.show();
            });
            
            // Buka kamera
            btnBukaKamera.addEventListener('click', async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'environment' } // Gunakan kamera belakang jika ada
                    });
                    videoKTP.srcObject = stream;
                    cameraArea.classList.remove('d-none');
                    btnBukaKamera.parentElement.parentElement.classList.add('d-none');
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Kamera Ditolak',
                        text: 'Mohon izinkan akses kamera untuk menggunakan fitur ini.',
                    });
                }
            });
            
            // Capture foto dari kamera
            document.getElementById('btnCaptureKTP').addEventListener('click', () => {
                const context = canvasKTP.getContext('2d');
                canvasKTP.width = videoKTP.videoWidth;
                canvasKTP.height = videoKTP.videoHeight;
                context.drawImage(videoKTP, 0, 0);
                
                canvasKTP.toBlob((blob) => {
                    capturedImage = blob;
                    previewKTP.src = URL.createObjectURL(blob);
                    
                    // Stop camera
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                    
                    // Show preview
                    cameraArea.classList.add('d-none');
                    previewArea.classList.remove('d-none');
                }, 'image/jpeg', 0.95);
            });
            
            // Batal kamera
            document.getElementById('btnBatalCamera').addEventListener('click', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                cameraArea.classList.add('d-none');
                btnBukaKamera.parentElement.parentElement.classList.remove('d-none');
            });
            
            // Upload file
            btnUploadKTP.addEventListener('click', () => {
                inputUploadKTP.click();
            });
            
            inputUploadKTP.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    capturedImage = e.target.files[0];
                    previewKTP.src = URL.createObjectURL(capturedImage);
                    
                    btnBukaKamera.parentElement.parentElement.classList.add('d-none');
                    previewArea.classList.remove('d-none');
                }
            });
            
            // Ulangi
            document.getElementById('btnUlangi').addEventListener('click', () => {
                previewArea.classList.add('d-none');
                btnBukaKamera.parentElement.parentElement.classList.remove('d-none');
                capturedImage = null;
            });
            
            // Proses OCR
            document.getElementById('btnProsesOCR').addEventListener('click', async () => {
                if (!capturedImage) return;
                
                previewArea.classList.add('d-none');
                loadingOCR.classList.remove('d-none');
                
                const formData = new FormData();
                formData.append('ktp_image', capturedImage);
                
                try {
                    const response = await fetch('{{ route("api.ocr-ktp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Auto-fill form
                        if (result.data.nama_lengkap) {
                            document.getElementById('nama_lengkap').value = result.data.nama_lengkap;
                        }
                        if (result.data.tempat_lahir) {
                            document.getElementById('tempat_lahir').value = result.data.tempat_lahir;
                        }
                        if (result.data.tanggal_lahir) {
                            document.getElementById('tanggal_lahir').value = result.data.tanggal_lahir;
                        }
                        if (result.data.alamat) {
                            document.getElementById('alamat').value = result.data.alamat;
                        }
                        
                        // Tutup modal
                        modalOCR.hide();
                        
                        // Reset modal state
                        setTimeout(() => {
                            loadingOCR.classList.add('d-none');
                            btnBukaKamera.parentElement.parentElement.classList.remove('d-none');
                        }, 300);
                        
                        // Alert konfirmasi
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Terisi!',
                            html: '<p class="mb-0"><strong>⚠️ PENTING:</strong> Periksa dan koreksi data hasil scan sebelum melanjutkan.</p>',
                            confirmButtonText: 'Baik, Saya Mengerti',
                            confirmButtonColor: '#28a745'
                        });
                    } else {
                        throw new Error(result.message || 'Gagal membaca KTP');
                    }
                } catch (error) {
                    loadingOCR.classList.add('d-none');
                    previewArea.classList.remove('d-none');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Scan KTP',
                        text: error.message || 'Terjadi kesalahan saat membaca KTP. Silakan coba lagi.',
                    });
                }
            });
            
            // Reset modal saat ditutup
            document.getElementById('modalOCR_KTP').addEventListener('hidden.bs.modal', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                cameraArea.classList.add('d-none');
                previewArea.classList.add('d-none');
                loadingOCR.classList.add('d-none');
                btnBukaKamera.parentElement.parentElement.classList.remove('d-none');
                capturedImage = null;
            });

            // ========================================
            // SIMPAN DATA FORM KE LOCAL STORAGE (DRAFT)
            // ========================================
            const formInputs = document.querySelectorAll('#formPendaftaran input:not([type="file"]):not([type="hidden"]), #formPendaftaran textarea, #formPendaftaran select');
            const LOCAL_STORAGE_KEY = 'draft_pendaftaran_haji';

            function loadDraft() {
                const draft = localStorage.getItem(LOCAL_STORAGE_KEY);
                // Hanya load draft jika form tidak memiliki pesan error dari server
                // karena jika ada error, laravel menggunakan fungsi old()
                const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
                
                if (draft && !hasErrors) {
                    try {
                        const data = JSON.parse(draft);
                        formInputs.forEach(input => {
                            if (input.name && data[input.name] !== undefined && data[input.name] !== '') {
                                if (input.type === 'radio' || input.type === 'checkbox') {
                                    if (input.value === data[input.name] || (input.type === 'checkbox' && data[input.name] === true)) {
                                        input.checked = true;
                                        // Panggil manual event change khusus untuk radio tombol untuk trigger fungsi toggle yang ada
                                        if(input.name === 'jenis_lokasi') { toggleFormLokasi(); }
                                    }
                                } else {
                                    input.value = data[input.name];
                                    if(input.name === 'jenis_porsi') { toggleJenisPorsi(); }
                                }
                            }
                        });
                        
                        // Menangani dropdown Kecamatan & Kelurahan Gresik
                        if (data['kecamatan_id']) {
                            // Tunggu opsi kecamatan dimuat oleh fetch
                            setTimeout(() => {
                                const kecSelect = document.getElementById('kecamatan');
                                kecSelect.value = data['kecamatan_id'];
                                kecSelect.dispatchEvent(new Event('change'));
                                
                                if (data['kelurahan_id']) {
                                    // Tunggu opsi kelurahan dimuat
                                    setTimeout(() => {
                                        document.getElementById('kelurahan').value = data['kelurahan_id'];
                                    }, 1500); // Waktu cukup untuk fetch api/kelurahan
                                }
                            }, 500); // Waktu cukup untuk fetch api/kecamatan
                        }
                    } catch (e) {
                        console.error('Gagal memuat draft pendaftaran:', e);
                    }
                }
            }

            function saveDraft() {
                const data = {};
                formInputs.forEach(input => {
                    if (input.name) {
                        if (input.type === 'radio') {
                            if (input.checked) data[input.name] = input.value;
                        } else if (input.type === 'checkbox') {
                            data[input.name] = input.checked;
                        } else {
                            data[input.name] = input.value;
                        }
                    }
                });
                localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data));
            }

            // Simpan setiap kali ada perubahan pada input
            formInputs.forEach(input => {
                input.addEventListener('input', saveDraft);
                input.addEventListener('change', saveDraft);
            });

            // Hapus data dari local storage dan IndexedDB saat berhasil disubmit
            document.getElementById('formPendaftaran').addEventListener('submit', function() {
                // Pastikan yang disubmit adalah data yang valid dan lolos required browser
                if (this.checkValidity()) {
                    localStorage.removeItem(LOCAL_STORAGE_KEY);
                    clearFilesDB().catch(console.error);
                }
            });

            // Jalankan saat halaman siap
            loadDraft();
        });
    </script>
@endpush