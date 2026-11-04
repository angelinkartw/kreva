<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KREVA</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        overflow-x: hidden;
        background: #f9fafb; /* Backup background */
    }

    .container {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    /* ================= LEFT SIDE ================= */
    .left {
        flex: 1.2;
        position: relative;
        background: #000;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: clamp(40px, 8vw, 80px);
        overflow: hidden;
    }

    .left::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('/images/bg_login.jpg');
        background-size: cover;
        background-position: center;
        opacity: 0.6; /* Sesuaikan nilai ini (0.0 - 1.0) */
        z-index: 1;
    }

    .left > * {
        position: relative;
        z-index: 2;
    }

    .logo img {
        height: 45px; /* Sesuaikan tinggi logo agar pas */
        width: auto;
    }

    .logo {
        position: absolute;
        top: 40px;
        left: clamp(40px, 8vw, 80px);
        display: flex;
        align-items: center;
    }

    .left h1 {
        font-size: clamp(32px, 4vw, 48px);
        font-weight: 800; /* Dibuat Bold */
        max-width: 500px;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .left p {
        max-width: 450px;
        opacity: 0.9;
        font-size: clamp(15px, 1.2vw, 18px);
        line-height: 1.6;
    }

    /* ================= RIGHT SIDE ================= */
    .right {
        flex: 1; /* Mengambil sisa ruang */
        display: flex;
        justify-content: center; 
        align-items: center;
        background: #ffffff;
        padding: clamp(30px, 5vw, 60px); /* Padding dinamis, MENGGANTIKAN padding-left yang kaku */
    }

    .form-wrapper {
        width: 100%;
        max-width: 480px; /* Disamakan dengan Register */
        display: flex;
        flex-direction: column;
    }

    .form-wrapper h2 {
        font-size: clamp(28px, 3vw, 36px);
        font-weight: 700; /* Dibuat Bold */
        color: #111;
        margin-bottom: 8px;
    }

    .form-wrapper > p {
        font-size: 15px;
        color: #666;
        margin-bottom: 32px;
        line-height: 1.5;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 20px; /* Jarak antar input sedikit diperlebar agar tidak sesak */
    }

    /* Tambahan grup input untuk kerapian (opsional tapi disarankan) */
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-group label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    input {
        width: 100%;
        padding: 14px 16px; 
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 15px;
        transition: all 0.2s ease;
        background: #fcfcfc; /* Disamakan dengan Register */
    }

    select {
        width: 100%;
        padding: 14px 16px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 15px;
        background: #fcfcfc;
    }

    input:focus {
        border-color: #1e5eff;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(30, 94, 255, 0.1);
    }

    .error {
        color: #d93025;
        font-size: 14px;
        background: #fce8e6;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        border-left: 4px solid #d93025;
    }

    button {
        width: 100%;
        padding: 14px 16px; 
        background: #064ECC;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        margin-top: 10px; /* Disamakan dengan Register */
    }

    button:hover {
        background: #064ECC; /* Mengikuti warna baru yang Anda masukkan */
        opacity: 0.9;
    }

    button:active {
        transform: scale(0.98); /* Efek klik */
    }

    .small {
        text-align: center;
        margin-top: 24px; /* Disamakan dengan Register */
        font-size: 14px;
        color: #555;
    }

    .small a {
        color: #064ECC;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .small a:hover {
        color: #174ad6;
        text-decoration: underline;
    }

    /* Password Show/Hide Styles */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-wrapper input {
        padding-right: 45px;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        padding: 0;
        margin: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        transition: color 0.2s;
        width: 24px;
        height: 24px;
        z-index: 10;
    }

    .toggle-password:hover {
        color: #064ECC;
        background: transparent !important;
        opacity: 1 !important;
    }
    
    .toggle-password:hover svg {
        fill: none !important;
    }
    
    .toggle-password svg {
        width: 20px;
        height: 20px;
    }

    /* 🔥 RESPONSIVE */
    @media (max-width: 900px) {
        .container {
            flex-direction: column;
        }

        .left {
            flex: none; 
            padding: 100px 30px 60px; /* Padding disesuaikan untuk mobile */
        }

        .logo {
            top: 30px;
            left: 30px;
        }

        .right {
            flex: 1; 
            padding: 40px 30px;
        }

        .form-wrapper {
            max-width: 100%; /* Memaksimalkan lebar di layar HP */
        }
    }
    </style>
    <link href="{{ asset('css/kreva-theme.css') }}" rel="stylesheet">
</head>
<body class="kreva-auth">

<div class="container">

    <div class="left">
        <div class="logo">
            <img src="{{ asset('images/kreva_logo_white.svg') }}" alt="KREVA Logo">
        </div>

        <h1>Masuk ke Marketplace UMKM KREVA</h1>
        <p>
            Jelajahi produk sebagai pembeli atau kelola katalog produk sebagai penjual.
        </p>
    </div>

    <div class="right">
        <div class="form-wrapper">

            <h2>Masuk</h2>
            <p>Masuk dengan username Anda. Halaman awal akan disesuaikan dengan jenis akun.</p>

            @if (session('database_error'))
                <div class="error" role="alert">{{ session('database_error') }}</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username"
                        name="username" 
                        placeholder="Masukkan Username"
                        value="{{ old('username', old('email')) }}"
                        required
                    >
                    @error('username') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="input-group">
                    <label for="account_type">Masuk sebagai</label>
                    <select id="account_type" name="account_type" required>
                        <option value="buyer" {{ old('account_type', request('account_type', 'buyer')) === 'buyer' ? 'selected' : '' }}>Pembeli UMKM</option>
                        <option value="seller" {{ old('account_type', request('account_type')) === 'seller' ? 'selected' : '' }}>Penjual UMKM</option>
                        <option value="employee" {{ old('account_type', request('account_type')) === 'employee' ? 'selected' : '' }}>Karyawan UMKM</option>
                    </select>
                    @error('account_type') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="input-group" x-data="{ show: false }">
                    <label for="password">Kata Sandi</label>
                    <div class="password-wrapper">
                        <input 
                            type="password"
                            :type="show ? 'text' : 'password'" 
                            id="password"
                            name="password" 
                            placeholder="Minimal 8 karakter"
                            required
                        >
                        <button type="button" class="toggle-password" @click="show = !show">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.822 7.822L21 21m-2.278-2.278-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit">Masuk</button>
            </form>

            <div class="small">
                Belum memiliki akun?
                <a href="{{ route('register') }}">Daftar</a>
            </div>
            <div class="small" style="margin-top: 12px;">
                <a href="{{ route('login', ['account_type' => 'seller']) }}">Masuk sebagai Penjual</a>
                &nbsp;·&nbsp;
                <a href="{{ route('login', ['account_type' => 'buyer']) }}">Masuk sebagai Pembeli</a>
            </div>
            <div class="small" style="margin-top: 12px;">
                <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>