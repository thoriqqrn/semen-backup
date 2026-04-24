@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="mb-4 fw-bold" style="color: #28a745;">Dashboard Admin</h1>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm rounded-custom border-0">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Total Pendaftar</h5>
                    <p class="card-text display-4 fw-bold">{{ App\Models\Pendaftar::count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm rounded-custom border-0">
                <div class="card-body text-center">
                    <i class="fas fa-images fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Item Galeri</h5>
                    <p class="card-text display-4 fw-bold">{{ App\Models\Galeri::count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm rounded-custom border-0">
                <div class="card-body text-center">
                    <i class="fas fa-newspaper fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Total Artikel</h5>
                    <p class="card-text display-4 fw-bold">{{ App\Models\Artikel::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-custom border-0 mt-4">
        <div class="card-header bg-white fw-bold" style="color: #28a745;">Pendaftar Terbaru</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>Jenis Porsi</th>
                        <th>Nomor Porsi</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (App\Models\Pendaftar::latest()->take(5)->get() as $pendaftar)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pendaftar->nama_lengkap }}</td>
                            <td>
                                @if($pendaftar->jenis_porsi === 'penggabungan')
                                    <span class="badge bg-warning text-dark">Penggabungan</span>
                                @elseif($pendaftar->jenis_porsi === 'berangkat')
                                    <span class="badge bg-success">Berangkat</span>
                                @elseif($pendaftar->jenis_porsi === 'mutasi')
                                    <span class="badge bg-info">Mutasi</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $pendaftar->nomor_porsi_haji }}</td>
                            <td>
                                @if($pendaftar->status_pendaftaran === 'diterima')
                                    <span class="badge bg-success">✅ Diterima</span>
                                @elseif($pendaftar->status_pendaftaran === 'ditolak')
                                    <span class="badge bg-danger">❌ Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Verifikasi</span>
                                @endif
                            </td>
                            <td>{{ $pendaftar->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-end">
                <a href="#" class="btn btn-outline-success">Lihat Semua Pendaftar <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
@endsection