@extends('layouts.app')

@section('content')
<div class="form-box">
    <h2>Verifikasi Akun</h2>
    <p>Silakan masukkan kode OTP yang kami kirim ke email Anda.</p>

    <form action="#" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="otp" required placeholder="Masukkan 6 Digit OTP">
            <i class="fas fa-key input-icon"></i>
        </div>
        <button type="submit" class="btn-primary">Verifikasi Sekarang</button>
    </form>
</div>
@endsection