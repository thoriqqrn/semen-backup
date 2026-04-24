@extends('layouts.main')

{{-- Judul halaman akan mengambil judul artikel secara dinamis --}}
@section('title', $artikel->judul . ' | KBIHU Aswaja')

@section('content')

    <!-- 1. Header Artikel -->
    <div class="py-5" style="background-color: #f0f4f2;">
        <div class="container text-center">
            <h1 class="display-5 fw-bold">{{ $artikel->judul }}</h1>
            <p class="lead text-muted col-lg-8 mx-auto">{{ $artikel->excerpt }}</p>
            <div class="mt-3 text-muted">
                <span><i class="fas fa-user me-2"></i>Oleh: {{ $artikel->admin->name ?? 'Admin' }}</span>
                <span class="mx-3">|</span>
                <span><i class="fas fa-calendar-alt me-2"></i>Dipublikasikan pada {{ $artikel->created_at->format('d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Konten Artikel -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <!-- Gambar Utama Artikel -->
                    @if ($artikel->gambar_utama_path)
                        <img src="{{ asset('storage/' . $artikel->gambar_utama_path) }}" class="img-fluid rounded-custom shadow-sm mb-5" alt="{{ $artikel->judul }}">
                    @endif

                    <!-- Isi Konten Lengkap -->
                    <div class="article-content" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! nl2br(e($artikel->konten)) !!}
                    </div>

                    <hr class="my-5">

                    <!-- Tombol Kembali -->
                    <div class="text-center">
                        <a href="{{ route('berita') }}" class="btn btn-outline-success btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Artikel
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

{{-- Menambahkan style khusus untuk konten artikel agar rapi --}}
@push('styles')
<style>
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: bold;
    }
</style>
@endpush