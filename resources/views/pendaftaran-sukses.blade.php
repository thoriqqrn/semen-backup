git commit -m "first clean commit"@extends('layouts.main')

@section('title', 'Pengajuan Pendaftaran | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold text-success">Pengajuan Pendaftaran!</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Terima kasih telah melakukan pendaftaran haji melalui KBIHU Aswaja.
                Silahkan tunggu admin memverifikasi pendaftaran Anda.</p>
        </div>
    </div>

    <!-- 2. Konten Utama: Kode Pendaftaran -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-custom text-center p-4 p-md-5">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                            <h3 class="fw-bold">Pendaftaran anda telah diajukan!</h3>
                            
                            {{-- Cek apakah session kode_pendaftaran ada --}}
                            @if(session('kode_pendaftaran'))
                                <p class="text-muted">Harap catat dan simpan <strong>Kode Pendaftaran</strong> di bawah ini. Kode ini akan digunakan untuk mengecek status pendaftaran Anda.</p>

                                <div class="bg-light p-4 rounded-3 mt-4 d-inline-block">
                                    <h2 class="fw-bolder display-5" style="letter-spacing: 2px;">
                                        {{ session('kode_pendaftaran') }}
                                    </h2>
                                </div>
                                
                                {{-- Tombol Download PDF sudah dihapus --}}

                            @else
                                <p class="text-muted">Terima kasih. Proses pendaftaran Anda sedang kami verifikasi.</p>
                            @endif
                            
                            <hr class="my-4">
                            
                            <h4 class="fw-bold">Langkah Selanjutnya</h4>
                            <p class="text-muted">
                                Tim kami akan segera melakukan verifikasi data dan dokumen Anda. 
                                <br>
                                <strong>Kartu Tanda Pendaftaran</strong> dapat diunduh melalui menu <strong>Cek Status</strong> setelah data Anda dinyatakan lolos verifikasi oleh Admin.
                            </p>
                            
                            <a href="{{ url('/') }}" class="btn btn-success mt-3">
                                <i class="fas fa-home me-2"></i> Kembali ke Halaman Utama
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection