@extends('layouts.main')

@section('title', 'Berita & Artikel | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Berita & Artikel Haji</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Informasi terkini, tips bermanfaat, dan panduan mendalam seputar
                perjalanan ibadah haji.</p>
        </div>
    </div>

    <!-- 2. Konten Utama: Grid Artikel Elegan -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row g-4">

                {{-- LOOPING ARTIKEL DARI DATABASE --}}
                @forelse ($articles as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="card article-card h-100">
                            {{-- Link gambar sekarang mengarah ke detail --}}
                            <a href="{{ route('berita.show', $article->slug) }}">
                                <img src="{{ $article->gambar_utama_path ? asset('storage/' . $article->gambar_utama_path) : 'https://via.placeholder.com/400x250.png?text=KBIHU+Aswaja' }}"
                                    class="article-card-img" alt="{{ $article->judul }}">
                            </a>
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold mt-2">
                                    {{-- Link judul sekarang mengarah ke detail dan memiliki class stretched-link --}}
                                    <a href="{{ route('berita.show', $article->slug) }}"
                                        class="text-decoration-none text-dark stretched-link">{{ $article->judul }}</a>
                                </h5>
                                <p class="card-text text-muted small mt-2">{{ Str::limit($article->excerpt, 100) }}</p>
                                <div class="mt-auto text-muted small">
                                    Dipublikasikan pada {{ $article->created_at->format('d F Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h4 class="fw-bold">Belum Ada Artikel</h4>
                        <p class="text-muted">Saat ini belum ada berita atau artikel yang dipublikasikan.</p>
                    </div>
                @endforelse
                {{-- AKHIR LOOPING --}}

            </div>

            <!-- Paginasi Modern dari Laravel -->
            <div class="mt-5 pt-4 d-flex justify-content-center">
                {{ $articles->links() }}
            </div>

        </div>
    </section>

@endsection