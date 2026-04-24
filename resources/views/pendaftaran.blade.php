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
        }

        .dropzone-preview-container.active {
            display: block;
        }

        .dropzone-prompt.hidden {
            display: none;
        }

        /* Preview image dibuat lebih kecil */
        .dropzone-preview-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin: 0 auto 8px;
            display: block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    </style>
    
    {{-- SweetAlert2 CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

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
                        @if ($max_slots > 0)
                            @if ($sisa_slot > 0)
                                {{-- 3 Kotak Informasi: Ring 1, Kuota Umum, dan Nomor Porsi --}}
                                <div class="row g-4 mb-4" id="infoCardsPendaftaran" style="{{ $errors->any() || session('error') ? 'display: none;' : '' }}">
                                    {{-- KOTAK RING 1 --}}
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 20px;">
                                            <div class="card-body p-4 d-flex flex-column">
                                                <div class="text-center mb-4">
                                                    <div class="mb-3">
                                                        <i class="fas fa-star" style="font-size: 3.5rem; color: rgba(255,255,255,0.9);"></i>
                                                    </div>
                                                    <h3 class="fw-bold text-white mb-2">RING 1</h3>
                                                    <p class="text-white-50 mb-0">Warga Kelurahan Prioritas</p>
                                                </div>
                                                
                                                <div class="bg-white rounded-3 p-4 mt-auto" style="background: rgba(255,255,255,0.95) !important;">
                                                    <div class="text-center mb-3">
                                                        <h1 class="display-4 fw-bold mb-0" style="color: #11998e;">{{ $kuota_ring1 }}</h1>
                                                        <p class="text-muted mb-0">Total Kuota</p>
                                                    </div>
                                                    <hr class="my-3">
                                                    <div class="d-flex justify-content-around">
                                                        <div class="text-center">
                                                            <i class="fas fa-user-check text-success mb-2" style="font-size: 1.5rem;"></i>
                                                            <h4 class="fw-bold mb-0" style="color: #11998e;">{{ $peserta_ring1 }}</h4>
                                                            <small class="text-muted">Terdaftar</small>
                                                        </div>
                                                        <div class="text-center">
                                                            <i class="fas fa-chair text-success mb-2" style="font-size: 1.5rem;"></i>
                                                            <h4 class="fw-bold mb-0" style="color: #11998e;">{{ $sisa_ring1 }}</h4>
                                                            <small class="text-muted">Sisa Kuota</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- KOTAK KUOTA UMUM --}}
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                                            <div class="card-body p-4 d-flex flex-column">
                                                <div class="text-center mb-4">
                                                    <div class="mb-3">
                                                        <i class="fas fa-globe-asia" style="font-size: 3.5rem; color: rgba(255,255,255,0.9);"></i>
                                                    </div>
                                                    <h3 class="fw-bold text-white mb-2">UMUM</h3>
                                                    <p class="text-white-50 mb-0">Warga Luar Ring 1 & Luar Kota</p>
                                                </div>
                                                
                                                <div class="bg-white rounded-3 p-4 mt-auto" style="background: rgba(255,255,255,0.95) !important;">
                                                    <div class="text-center mb-3">
                                                        <h1 class="display-4 fw-bold mb-0" style="color: #667eea;">{{ $kuota_umum }}</h1>
                                                        <p class="text-muted mb-0">Total Kuota</p>
                                                    </div>
                                                    <hr class="my-3">
                                                    <div class="d-flex justify-content-around">
                                                        <div class="text-center">
                                                            <i class="fas fa-user-check text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                            <h4 class="fw-bold mb-0" style="color: #667eea;">{{ $peserta_umum }}</h4>
                                                            <small class="text-muted">Terdaftar</small>
                                                        </div>
                                                        <div class="text-center">
                                                            <i class="fas fa-chair text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                            <h4 class="fw-bold mb-0" style="color: #667eea;">{{ $sisa_umum }}</h4>
                                                            <small class="text-muted">Sisa Kuota</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- KOTAK NOMOR PORSI --}}
                                    <div class="col-lg-4 col-md-12">
                                        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #f5564e 0%, #d43f3a 100%); border-radius: 20px;">
                                            <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                                                <div class="mb-3">
                                                    <i class="fas fa-ticket-alt" style="font-size: 3.5rem; color: rgba(255,255,255,0.9);"></i>
                                                </div>
                                                <h3 class="fw-bold text-white mb-2">Porsi Tertinggi Saat Ini</h3>
                                                <p class="text-white-50 mb-4">Porsi tertinggi berubah sewaktu-waktu sesuai ketentuan BP Haji RI</p>
                                                
                                                <div class="bg-white rounded-3 p-4" style="background: rgba(255,255,255,0.95) !important;">
                                                    <h1 class="display-4 fw-bold mb-0" style="color: #d43f3a;">
                                                        {{ $max_porsi }}
                                                    </h1>
                                                    <p class="text-muted mb-0">Nomor Porsi Tertinggi</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger text-center" role="alert">
                                    <h4 class="alert-heading fw-bold">Pendaftaran Ditutup</h4>
                                    <p class="mb-0 fs-5">Mohon maaf, kuota pendaftaran haji untuk tahun ini telah terpenuhi. Silakan
                                        kembali lagi untuk informasi pendaftaran tahun berikutnya.</p>
                                </div>
                            @endif
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
                            <div class="alert alert-warning d-flex align-items-center p-4 rounded-custom mb-4">
                                <i class="fas fa-exclamation-triangle fa-2x me-4"></i>
                                <div>
                                    <h4 class="alert-heading fw-bold">Perhatian Sebelum Mengisi!</h4>
                                    <ul>
                                        <li>Pastikan nama yang Anda masukkan sama persis dengan yang tertera pada  KTP/KK/Akte Kelahiran/Buku Nikah/Ijazah (SD/SLTP/SLTA).</li>
                                        <li>Pastikan Porsi Haji Anda masuk dalam Keberangkatan Tahun Depan.</li>
                                        <li>Semua dokumen yang diunggah harus dapat dibaca dengan jelas (tidak buram).</li>
                                        <li>Ukuran maksimal untuk setiap file adalah 2MB. Format yang diterima: JPG, PNG, PDF.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-center mb-5">
                                <button type="button" class="btn btn-success btn-lg px-5 py-3 shadow fw-bold" id="btnMulaiDaftar">
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
                                                                <div class="dropzone-preview-container">
                                                                    <img src="" class="dropzone-preview-image" alt="Preview">
                                                                    <div class="dropzone-preview-overlay">
                                                                        <div class="dropzone-filename"></div>
                                                                        <div class="dropzone-change-text">Klik untuk ganti</div>
                                                                    </div>
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
            
            if(btnMulaiDaftar) {
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

            // Dropzone handling dengan preview yang lebih kompak
            const dropzones = document.querySelectorAll('.dropzone');
            dropzones.forEach(dropzone => {
                const input = dropzone.querySelector('input[type="file"]');
                const prompt = dropzone.querySelector('.dropzone-prompt');
                const previewContainer = dropzone.querySelector('.dropzone-preview-container');
                const previewImage = dropzone.querySelector('.dropzone-preview-image');
                const filenameDiv = dropzone.querySelector('.dropzone-filename');

                dropzone.addEventListener('click', () => {
                    input.click();
                });

                input.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                const updatePreview = (file) => {
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
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Icon PDF yang lebih kecil dan rapi
                        previewImage.src = 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PDF_file_icon.svg/1200px-PDF_file_icon.svg.png';
                    }
                };

                input.addEventListener('change', () => {
                    if (input.files.length > 0) {
                        updatePreview(input.files[0]);
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
                        input.files = e.dataTransfer.files;
                        updatePreview(input.files[0]);
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
        });
    </script>
@endpush