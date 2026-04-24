@extends('admin.layouts.app'){{-- Menggunakan layout dashboard admin --}}

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold" style="color: #28a745;">Manajemen Galeri</h1>
            <a href="{{ route('admin.galeri.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Tambah Item Galeri
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Daftar Item Galeri</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gambar/Video</th>
                                <th>Judul</th>
                                <th>Tahun</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Ditambahkan Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($galeris as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($item->tipe == 'foto')
                                            <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->judul }}" width="100">
                                        @else
                                            <video width="100" controls>
                                                <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </td>
                                    <td>{{ $item->judul }}</td>
                                    <td>{{ $item->tahun_kegiatan }}</td>
                                    <td>{{ ucfirst($item->tipe) }}</td>
                                    <td>
                                        <span class="badge {{ $item->is_published ? 'bg-success' : 'bg-warning' }}">
                                            {{ $item->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>{{ $item->admin->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.galeri.edit', $item->id) }}" class="btn btn-info btn-sm text-white"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada item galeri.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection