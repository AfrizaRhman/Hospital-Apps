@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header background-color: #20c997; text-white">
            <h4 class="mb-0">Tambah Tagihan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('bills.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="patient_id">Nama Pasien</label>
                    <select name="patient_id" id="patient_id" class="form-control" required>
                        <option value="" selected disabled>Nama Pasien</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="transaction">Jenis Transaksi</label>
                    <select name="transaction" id="transaction" class="form-control" required>
                        <option value="" selected disabled>Jenis Transaksi</option>
                        <option value="tunai">Tunai</option>
                        <option value="non-tunai">Non-Tunai</option>
                        <option value="bpjs">BPJS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bill">Status Tagihan</label>
                    <select name="bill" id="bill" class="form-control" required>
                        <option value="" selected disabled>Status Tagihan</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum-lunas">Belum Lunas</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('index') }}" class="btn btn-outline-secondary rounded-3 px-4">Kembali</a>
                    <button type="submit" class="btn btn-success rounded-3 px-4">
                        <i class="fa-solid fa-paper-plane"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection