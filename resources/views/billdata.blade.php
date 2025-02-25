@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 d-flex flex-column flex-shrink-0 p-3 text-white sidebar" style="height: 100vh; background-color: #20c997; position: fixed;">
            <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                <span class="fs-4 font-weight-bold">🏥 Hospital App</span>
            </a>
            <hr>

            <!-- Tombol Kembali -->
            <a href="{{ route('index') }}" class="btn btn-light mb-3">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>

            <!-- Navigasi Rekam Medis -->
            <div class="dropdown">
                @if(auth()->user()->role === 'office')
                <button class="btn btn-outline-light dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-file-invoice-dollar fa-lg"></i> Tagihan
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('bills.create') }}">➕ Tambah Tagihan</a></li>
                    <li><a class="dropdown-item" href="{{ route('bills.index') }}">📋 List Tagihan</a></li>
                </ul>
                @endif
            </div>

            <hr>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-10 offset-md-2">
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 font-weight-bold">📋 Data Tagihan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light py-2 px-4 mb-3 rounded">
                                <div class="card-body d-flex justify-content-between align-items-center p-2">
                                    <h5 class="m-0 text-dark">Total Tagihan</h5>
                                    <h5 class="text-success">{{ $totalBills }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center" id="billsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Transaksi</th>
                                    <th>Status Tagihan</th>
                                    <th>Tanggal</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#billsTable').DataTable({ // Perbaiki selector ID di sini
            processing: true,
            serverSide: true, // Aktifkan server-side processing
            responsive: true,
            lengthChange: false,
            ajax: "{{ route('bills.bills.datatable') }}", // Perbaiki route di sini
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, // Kolom index
                {
                    data: 'name',
                    name: 'name'
                }, // Kolom nama pasien
                {
                    data: 'transaction',
                    name: 'transaction'
                }, // Kolom jenis transaksi
                {
                    data: 'bill',
                    name: 'bill'
                }, // Kolom status tagihan
                {
                    data: 'created_at',
                    name: 'created_at'
                }, // Kolom tanggal dibuat
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                } // Kolom aksi
            ]
        });
    });
</script>
@endpush