@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 15px;">
        <h3 class="btn-lg text-center text-white mb-3" style=" background-color: #20c997;">Register</h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Nama</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required placeholder="Masukkan email">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required placeholder="Masukkan password">
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label fw-bold">Konfirmasi Password</label>
                <input id="password-confirm" type="password" class="form-control"
                    name="password_confirmation" required placeholder="Ulangi password">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label fw-bold">Role</label>
                <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                    <option value="" selected disabled>Pilih role</option>
                    <option value="admin">Dokter</option>
                    <option value="office">Karyawan</option>
                    <option value="guest">Pasien</option>
                </select>
                @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="w-100 btn text-white text-center" style=" background-color: #20c997;">Daftar</button>
        </form>

        <div class="text-center mt-3">
            <small>Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Login</a></small>
        </div>
    </div>
</div>
@endsection