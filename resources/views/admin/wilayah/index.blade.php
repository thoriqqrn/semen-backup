@extends('admin.layouts.app')
@section('title', 'Pengaturan RING Wilayah')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-bold" style="color: #28a745;">Pengaturan RING Wilayah</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-success">CRUD Kecamatan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.wilayah.kecamatan.store') }}" method="POST" class="row g-2 mb-3">
                @csrf
                <div class="col-md-8">
                    <input type="text" name="nama_kecamatan" class="form-control" placeholder="Nama Kecamatan" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">Tambah Kecamatan</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="kecamatanTable">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th>Nama Kecamatan</th>
                            <th style="width: 28%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kecamatans as $kecamatan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <form action="{{ route('admin.wilayah.kecamatan.update', $kecamatan->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="nama_kecamatan" class="form-control form-control-sm" value="{{ $kecamatan->nama_kecamatan }}" required>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('admin.wilayah.kecamatan.destroy', $kecamatan->id) }}" method="POST" onsubmit="return confirm('Hapus kecamatan ini? Kelurahan terkait juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-success">CRUD Kelurahan / Desa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.wilayah.kelurahan.store') }}" method="POST" class="row g-2 mb-3">
                @csrf
                <div class="col-md-4">
                    <select name="kecamatan_id" class="form-select" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="nama_kelurahan" class="form-control" placeholder="Nama Kelurahan/Desa" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="ring_status" class="form-control" min="1" placeholder="R">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Tambah</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="kelurahanCrudTable">
                    <thead>
                        <tr>
                            <th style="width: 6%;">No</th>
                            <th style="width: 26%;">Kecamatan</th>
                            <th>Kelurahan/Desa</th>
                            <th style="width: 10%;">Ring</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelurahans as $kelurahan)
                            @php $formId = 'form-update-kelurahan-' . $kelurahan->id; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                        <select name="kecamatan_id" form="{{ $formId }}" class="form-select form-select-sm" required>
                                            @foreach($kecamatans as $kecamatan)
                                                <option value="{{ $kecamatan->id }}" {{ $kelurahan->kecamatan_id == $kecamatan->id ? 'selected' : '' }}>
                                                    {{ $kecamatan->nama_kecamatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                </td>
                                <td>
                                        <input type="text" name="nama_kelurahan" form="{{ $formId }}" class="form-control form-control-sm" value="{{ $kelurahan->nama_kelurahan }}" required>
                                </td>
                                <td>
                                        <input type="number" name="ring_status" form="{{ $formId }}" class="form-control form-control-sm" value="{{ $kelurahan->ring_status }}" min="1" placeholder="-">
                                </td>
                                <td>
                                    <form id="{{ $formId }}" action="{{ route('admin.wilayah.kelurahan.update', $kelurahan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <button type="submit" form="{{ $formId }}" class="btn btn-sm btn-primary me-1">Simpan</button>
                                    <form action="{{ route('admin.wilayah.kelurahan.destroy', $kelurahan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelurahan/desa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-success">Atur Status RING untuk Kelurahan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.wilayah.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table table-bordered" id="wilayahTable">
                        <thead>
                            <tr>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th style="width: 15%;">Status RING (1, 2, 3, dst)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelurahans as $kelurahan)
                            <tr>
                                <td>{{ $kelurahan->kecamatan->nama_kecamatan }}</td>
                                <td>{{ $kelurahan->nama_kelurahan }}</td>
                                <td>
                                    <input type="number" name="rings[{{ $kelurahan->id }}]" class="form-control" value="{{ $kelurahan->ring_status }}" min="1">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan Semua Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk DataTables --}}
<script>
    $(document).ready(function() {
        $('#kecamatanTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 25
        });

        $('#kelurahanCrudTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 25
        });

        $('#wilayahTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 50 // Tampilkan 50 entri per halaman
        });
    });
</script>
@endpush