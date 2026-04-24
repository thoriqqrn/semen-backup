@extends('admin.layouts.app')
@section('title', 'Pengaturan RING Wilayah')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-bold" style="color: #28a745;">Pengaturan RING Wilayah</h1>

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
        $('#wilayahTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 50 // Tampilkan 50 entri per halaman
        });
    });
</script>
@endpush