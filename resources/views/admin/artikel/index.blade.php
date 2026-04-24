@extends('admin.layouts.app')

@section('title', 'Manajemen Artikel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold" style="color: #28a745;">Manajemen Artikel</h1>
        <a href="{{ route('admin.artikel.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i> Tulis Artikel Baru
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
            <h6 class="m-0 font-weight-bold text-success">Daftar Artikel</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Penulis</th>
                            <th>Tgl Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($artikels as $artikel)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $artikel->judul }}</td>
                                <td>
                                    <span class="badge {{ $artikel->is_published ? 'bg-success' : 'bg-warning' }}">
                                        {{ $artikel->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ $artikel->admin->name ?? 'N/A' }}</td>
                                <td>{{ $artikel->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.artikel.edit', $artikel->id) }}" class="btn btn-info btn-sm text-white"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.artikel.destroy', $artikel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada artikel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection