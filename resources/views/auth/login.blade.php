<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf9f4">
    <title>Masuk - KREVA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/kreva_logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: 'Poppins', sans-serif;
            color: #292b25;
            background: #fbf9f4;
            font-synthesis: none;
        }
        * { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; }
        a { color: inherit; }
        .page { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(420px, .92fr); }
        .story { position: relative; display: flex; min-height: 100vh; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 38px clamp(32px, 6vw, 88px); color: #fff; background: #26402e; }
        .story::before { position: absolute; inset: 0; content: ''; background: linear-gradient(180deg, rgba(20, 39, 26, .2), rgba(20, 39, 26, .88)), url('{{ asset('images/landing/hero-composition.png') }}') center / cover; }
        .story > * { position: relative; z-index: 1; }
        .brand img { display: block; width: 140px; height: auto; filter: brightness(0) invert(1); }
        .story-copy { max-width: 570px; padding: 70px 0; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,.35); border-radius: 999px; padding: 8px 14px; color: #f8ede1; font-size: 12px; font-weight: 600; letter-spacing: .04em; }
        .eyebrow::before { width: 7px; height: 7px; border-radius: 50%; background: #e9a46e; content: ''; }
        h1 { margin: 22px 0 14px; font-size: clamp(36px, 4.5vw, 64px); line-height: 1.12; letter-spacing: -.04em; }
        .story-copy p { max-width: 450px; margin: 0; color: #f4eee2; font-size: 16px; line-height: 1.8; }
        .story-foot { color: rgba(255,255,255,.75); font-size: 12px; }
        .form-side { display: grid; place-items: center; padding: 36px clamp(24px, 5vw, 76px); }
        .form-card { width: min(100%, 440px); }
        .mobile-brand { display: none; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 28px; color: #647064; font-size: 13px; font-weight: 500; text-decoration: none; }
        .back-link:hover, .text-link:hover { color: #26402e; text-decoration: underline; }
        h2 { margin: 0; color: #1e3325; font-size: clamp(30px, 3vw, 38px); line-height: 1.2; letter-spacing: -.035em; }
        .intro { margin: 10px 0 28px; color: #77786f; font-size: 14px; line-height: 1.7; }
        .notice { margin: 0 0 18px; border: 1px solid #f1c7bb; border-radius: 12px; background: #fff4f0; padding: 12px 14px; color: #a53d2f; font-size: 13px; line-height: 1.6; }
        form { display: grid; gap: 18px; }
        .field { display: grid; gap: 8px; }
        label { color: #41443d; font-size: 13px; font-weight: 600; }
        input, select { width: 100%; min-height: 50px; border: 1px solid #deded5; border-radius: 12px; outline: 0; background: #fffefa; padding: 0 15px; color: #292b25; font: inherit; font-size: 14px; transition: border-color .2s, box-shadow .2s; }
        input::placeholder { color: #a2a399; }
        input:focus, select:focus { border-color: #558257; box-shadow: 0 0 0 4px rgba(85,130,87,.12); }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 68px; }
        .show-password { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: 0; background: transparent; padding: 7px; color: #558257; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
        .field-error { color: #b44335; font-size: 12px; }
        .form-options { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: -3px; color: #72746c; font-size: 12px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .remember input { width: 16px; min-height: 16px; height: 16px; margin: 0; accent-color: #345236; }
        .text-link { color: #345236; font-weight: 600; text-decoration: none; }
        .submit { min-height: 52px; border: 0; border-radius: 12px; background: #26402e; color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 18px rgba(38,64,46,.16); transition: background .2s, transform .2s; }
        .submit:hover { transform: translateY(-1px); background: #1e3325; }
        .divider { display: flex; align-items: center; gap: 14px; margin: 24px 0 18px; color: #999a91; font-size: 12px; }
        .divider::before, .divider::after { flex: 1; height: 1px; background: #e9e5db; content: ''; }
        .register-prompt { margin: 0; color: #77786f; text-align: center; font-size: 13px; }
        .register-prompt a { color: #345236; font-weight: 700; text-decoration: none; }
        .register-prompt a:hover { text-decoration: underline; }
        @media (max-width: 800px) {
            .page { grid-template-columns: 1fr; }
            .story { display: none; }
            .form-side { min-height: 100vh; padding: 30px 22px; }
            .mobile-brand { display: block; margin-bottom: 36px; }
            .mobile-brand img { width: 128px; height: auto; }
            .back-link { margin-bottom: 22px; }
        }
        @media (max-width: 420px) {
            .form-side { padding: 24px 18px; }
            .form-options { align-items: flex-start; }
        }
    </style>
</head>
<body>
<main class="page">
    <section class="story" aria-label="Tentang KREVA">
        <a class="brand" href="{{ url('/') }}" aria-label="Kembali ke beranda">
            <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA">
        </a>
        <div class="story-copy">
            <span class="eyebrow">Marketplace UMKM Indonesia</span>
            <h1>Cerita lokal,<br>karya istimewa.</h1>
            <p>Temukan karya pilihan dari para pengrajin dan pelaku usaha lokal, atau kelola toko Anda bersama KREVA.</p>
        </div>
        <div class="story-foot">© {{ date('Y') }} KREVA · Dukung karya lokal Indonesia</div>
    </section>

    <section class="form-side">
        <div class="form-card">
            <a class="mobile-brand" href="{{ url('/') }}" aria-label="Kembali ke beranda">
                <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA">
            </a>
            <a class="back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Kembali ke beranda</a>
            <h2>Selamat datang</h2>
            <p class="intro">Masuk ke akun KREVA untuk melanjutkan.</p>

            @if (session('database_error'))
                <div class="notice" role="alert">{{ session('database_error') }}</div>
            @endif
            @if ($errors->any())
                <div class="notice" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="username">Username atau email</label>
                    <input type="text" id="username" name="username" value="{{ old('username', old('email')) }}" placeholder="Masukkan username atau email" autocomplete="username" required autofocus>
                    @error('username') <span class="field-error">{{ $message }}</span> @enderror
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="account_type">Masuk sebagai</label>
                    <select id="account_type" name="account_type" required>
                        <option value="buyer" {{ old('account_type', request('account_type', 'buyer')) === 'buyer' ? 'selected' : '' }}>Pembeli</option>
                        <option value="seller" {{ old('account_type', request('account_type')) === 'seller' ? 'selected' : '' }}>Penjual</option>
                        <option value="employee" {{ old('account_type', request('account_type')) === 'employee' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                    @error('account_type') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Kata sandi</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                        <button class="show-password" type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi">Lihat</button>
                    </div>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    <a class="text-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                </div>

                <button class="submit" type="submit">Masuk ke akun</button>
            </form>

            <div class="divider">Belum bergabung?</div>
            <p class="register-prompt">Buat akun baru <a href="{{ route('register') }}">Daftar di sini</a></p>
        </div>
    </section>
</main>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.textContent = visible ? 'Sembunyi' : 'Lihat';
            button.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });
</script>
</body>
</html>
