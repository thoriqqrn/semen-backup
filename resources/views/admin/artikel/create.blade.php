@extends('admin.layouts.app')

@section('title', 'Tulis Artikel Baru')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-bold" style="color: #28a745;">Tulis Artikel Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.artikel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Artikel</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="excerpt" class="form-label">Ringkasan / Excerpt</label>
                    <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" required>{{ old('excerpt') }}</textarea>
                    @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="konten" class="form-label">Konten Lengkap</label>
                    <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="10" required>{{ old('konten') }}</textarea>
                    @error('konten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="gambar_utama" class="form-label">Gambar Utama (Opsional)</label>
                    <input type="file" class="form-control @error('gambar_utama') is-invalid @enderror" id="gambar_utama" name="gambar_utama">
                    @error('gambar_utama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" checked>
                    <label class="form-check-label" for="is_published">Publikasikan Langsung</label>
                </div>
                <button type="submit" class="btn btn-success">Simpan Artikel</button>
                <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection