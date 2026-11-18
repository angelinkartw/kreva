<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf9f4">
    <title>Daftar - KREVA</title>
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
        .page { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(460px, .92fr); }
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
        .form-card { width: min(100%, 460px); }
        .mobile-brand { display: none; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px; color: #647064; font-size: 13px; font-weight: 500; text-decoration: none; }
        .back-link:hover, .text-link:hover { color: #26402e; text-decoration: underline; }
        h2 { margin: 0; color: #1e3325; font-size: clamp(30px, 3vw, 38px); line-height: 1.2; letter-spacing: -.035em; }
        .intro { margin: 10px 0 22px; color: #77786f; font-size: 14px; line-height: 1.7; }
        .notice { margin: 0 0 16px; border: 1px solid #f1c7bb; border-radius: 12px; background: #fff4f0; padding: 12px 14px; color: #a53d2f; font-size: 13px; line-height: 1.6; }
        form { display: grid; gap: 14px; }
        .field { display: grid; gap: 7px; }
        label, .group-label { color: #41443d; font-size: 13px; font-weight: 600; }
        input, select { width: 100%; min-height: 48px; border: 1px solid #deded5; border-radius: 12px; outline: 0; background: #fffefa; padding: 0 14px; color: #292b25; font: inherit; font-size: 14px; transition: border-color .2s, box-shadow .2s; }
        input::placeholder { color: #a2a399; }
        input:focus, select:focus { border-color: #558257; box-shadow: 0 0 0 4px rgba(85,130,87,.12); }
        .field-error { color: #b44335; font-size: 12px; }
        .seller-fields[hidden] { display: none; }
        .seller-fields { display: grid; gap: 14px; }
        .radio-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .radio-choice { display: flex; align-items: center; gap: 9px; min-height: 46px; border: 1px solid #deded5; border-radius: 12px; background: #fffefa; padding: 0 12px; color: #51534c; font-size: 13px; font-weight: 500; cursor: pointer; }
        .radio-choice input { width: 16px; min-height: 16px; height: 16px; margin: 0; accent-color: #345236; }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 68px; }
        .show-password { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: 0; background: transparent; padding: 7px; color: #558257; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
        .submit { min-height: 52px; margin-top: 4px; border: 0; border-radius: 12px; background: #26402e; color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 18px rgba(38,64,46,.16); transition: background .2s, transform .2s; }
        .submit:hover { transform: translateY(-1px); background: #1e3325; }
        .divider { display: flex; align-items: center; gap: 14px; margin: 22px 0 16px; color: #999a91; font-size: 12px; }
        .divider::before, .divider::after { flex: 1; height: 1px; background: #e9e5db; content: ''; }
        .login-prompt { margin: 0; color: #77786f; text-align: center; font-size: 13px; }
        .login-prompt a { color: #345236; font-weight: 700; text-decoration: none; }
        .login-prompt a:hover { text-decoration: underline; }
        @media (max-width: 800px) {
            .page { grid-template-columns: 1fr; }
            .story { display: none; }
            .form-side { min-height: 100vh; padding: 30px 22px; }
            .mobile-brand { display: block; margin-bottom: 32px; }
            .mobile-brand img { width: 128px; height: auto; }
            .back-link { margin-bottom: 18px; }
        }
        @media (max-width: 420px) {
            .form-side { padding: 24px 18px; }
            .radio-row { grid-template-columns: 1fr; }
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
            <h1>Temukan karya.<br>Dukung sesama.</h1>
            <p>Bergabunglah dengan komunitas yang menghargai cerita dan karya di balik setiap produk lokal.</p>
        </div>
        <div class="story-foot">© {{ date('Y') }} KREVA · Dukung karya lokal Indonesia</div>
    </section>

    <section class="form-side">
        <div class="form-card">
            <a class="mobile-brand" href="{{ url('/') }}" aria-label="Kembali ke beranda">
                <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA">
            </a>
            <a class="back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Kembali ke beranda</a>
            <h2>Buat akun KREVA</h2>
            <p class="intro">Daftar sebagai pembeli atau penjual dan mulai jelajahi produk lokal.</p>

            @if (session('database_error'))
                <div class="notice" role="alert">{{ session('database_error') }}</div>
            @endif
            @if ($errors->any())
                <div class="notice" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="field">
                    <label for="nama_user">Username</label>
                    <input type="text" id="nama_user" name="nama_user" value="{{ old('nama_user') }}" placeholder="Buat username Anda" maxlength="100" autocomplete="username" required autofocus>
                    @error('nama_user') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="account_type">Saya ingin bergabung sebagai</label>
                    <select id="account_type" name="account_type" required>
                        <option value="buyer" {{ old('account_type', request('account_type', 'seller')) === 'buyer' ? 'selected' : '' }}>Pembeli — menjelajahi produk lokal</option>
                        <option value="seller" {{ old('account_type', request('account_type', 'seller')) === 'seller' ? 'selected' : '' }}>Penjual — menawarkan produk lokal</option>
                    </select>
                    @error('account_type') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="seller-fields" id="seller-fields" hidden>
                    <div class="field">
                        <label for="age">Umur</label>
                        <input type="number" id="age" name="age" min="13" max="120" value="{{ old('age') }}" placeholder="Umur (minimal 13 tahun)" inputmode="numeric">
                        @error('age') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <span class="group-label">Jenis kelamin</span>
                        <div class="radio-row">
                            <label class="radio-choice">
                                <input type="radio" name="gender" value="2" {{ old('gender') == '2' ? 'checked' : '' }}>
                                Laki-laki
                            </label>
                            <label class="radio-choice">
                                <input type="radio" name="gender" value="1" {{ old('gender') == '1' ? 'checked' : '' }}>
                                Perempuan
                            </label>
                        </div>
                        @error('gender') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="password">Kata sandi</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                        <button class="show-password" type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi">Lihat</button>
                    </div>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Ulangi kata sandi</label>
                    <div class="password-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Masukkan kata sandi yang sama" autocomplete="new-password" required>
                        <button class="show-password" type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">Lihat</button>
                    </div>
                </div>

                <button class="submit" type="submit">Buat akun</button>
            </form>

            <div class="divider">Sudah punya akun?</div>
            <p class="login-prompt">Silakan <a href="{{ route('login') }}">masuk di sini</a></p>
        </div>
    </section>
</main>
<script>
    const accountType = document.getElementById('account_type');
    const sellerFields = document.getElementById('seller-fields');
    const ageInput = document.getElementById('age');
    const genderInputs = sellerFields.querySelectorAll('input[name="gender"]');

    function updateSellerFields() {
        const isSeller = accountType.value === 'seller';
        sellerFields.hidden = !isSeller;
        ageInput.required = isSeller;
        genderInputs.forEach((input) => { input.required = isSeller; });
    }

    accountType.addEventListener('change', updateSellerFields);
    updateSellerFields();

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
