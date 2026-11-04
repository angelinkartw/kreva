<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat RESTful API Documentation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-base: #0b0f19;
            --bg-surface: #131b2e;
            --bg-surface-hover: #1e2942;
            --border-color: #22304f;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.15);
            --cyan: #06b6d4;
            --emerald: #10b981;
            --rose: #f43f5e;
            --amber: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        /* Layout Grid */
        .container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        aside {
            background-color: #0d1323;
            border-right: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 2rem 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.25rem;
            color: white;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 800;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-group-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.8rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--bg-surface-hover);
        }

        .nav-link.active {
            color: white;
            background: linear-gradient(to right, var(--primary-glow), rgba(99, 102, 241, 0.03));
            border-left: 3px solid var(--primary);
        }

        .nav-link-method {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .method-get { background-color: rgba(6, 182, 212, 0.15); color: var(--cyan); }
        .method-post { background-color: rgba(16, 185, 129, 0.15); color: var(--emerald); }

        /* Main Content Area */
        main {
            padding: 3rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        header {
            margin-bottom: 3.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 2rem;
            position: relative;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 120px;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--cyan));
        }

        .app-title {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .app-desc {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 800px;
        }

        /* Section Styling */
        section {
            margin-bottom: 4rem;
            scroll-margin-top: 2rem;
        }

        .section-header {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Endpoint Card */
        .endpoint-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            transition: border-color 0.2s ease;
        }

        .endpoint-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }

        .endpoint-header {
            padding: 1.5rem 2rem;
            background-color: rgba(13, 19, 35, 0.5);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .endpoint-identity {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .method-badge {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-get { background: rgba(6, 182, 212, 0.12); border: 1px solid rgba(6, 182, 212, 0.3); color: #22d3ee; }
        .badge-post { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }

        .endpoint-path {
            font-family: 'Fira Code', monospace;
            font-size: 1.1rem;
            font-weight: 500;
            color: #ffffff;
        }

        .auth-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 99px;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .auth-required {
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #fbbf24;
        }

        .auth-public {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
        }

        .endpoint-body {
            padding: 2rem;
        }

        .endpoint-info {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 1024px) {
            .endpoint-info {
                grid-template-columns: 1fr;
            }
        }

        .endpoint-desc-area {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .endpoint-title {
            font-size: 1.35rem;
            color: white;
        }

        .endpoint-desc {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Params Table */
        .params-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .params-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .params-table th, .params-table td {
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .params-table th {
            color: var(--text-muted);
            font-weight: 600;
            background-color: rgba(13, 19, 35, 0.25);
        }

        .param-name {
            font-family: 'Fira Code', monospace;
            color: #e2e8f0;
            font-weight: 500;
        }

        .param-type {
            font-size: 0.8rem;
            color: var(--cyan);
            font-weight: 600;
        }

        .param-required {
            font-size: 0.75rem;
            color: var(--rose);
            font-weight: 600;
        }

        .param-optional {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Code Block Panel */
        .code-panel {
            background-color: #0a0e17;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .code-panel-header {
            padding: 0.75rem 1.25rem;
            background-color: #0d121f;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .code-panel-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-copy {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .btn-copy:hover {
            color: var(--text-primary);
        }

        pre {
            padding: 1.25rem;
            margin: 0;
            overflow-x: auto;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            color: #cbd5e1;
        }

        /* Glow Decorations */
        .glow-circle {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary-glow) 0%, rgba(99, 102, 241, 0) 70%);
            top: -150px;
            right: -100px;
            z-index: -1;
            pointer-events: none;
        }

        /* File Card Grid */
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3.5rem;
        }

        .file-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .file-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        }

        .file-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--cyan));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .file-card:hover::before {
            opacity: 1;
        }

        .file-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .file-title-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .file-icon {
            color: var(--primary);
            flex-shrink: 0;
        }

        .file-name {
            font-family: 'Fira Code', monospace;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            word-break: break-all;
        }

        .file-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-new {
            background-color: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-modify {
            background-color: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .file-desc {
            font-size: 0.88rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .file-mapping {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
        }

        .file-mapping-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.6rem;
        }

        .file-endpoint-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .file-endpoint-tag {
            font-family: 'Fira Code', monospace;
            font-size: 0.72rem;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            background-color: rgba(20, 27, 45, 0.5);
            border: 1px solid var(--border-color);
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }

        .file-endpoint-tag:hover {
            border-color: rgba(99, 102, 241, 0.3);
            background-color: rgba(99, 102, 241, 0.08);
        }

        .file-endpoint-tag .tag-method {
            font-weight: 800;
            font-size: 0.6rem;
        }
        .tag-get { color: #22d3ee; }
        .tag-post { color: #34d399; }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- SIDEBAR -->
        <aside>
            <div class="logo-area">
                <div class="logo-icon">S</div>
                <div class="logo-text">SiSehat API</div>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Sistem & Tes</div>
                <a href="#ping" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>Status Ping</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Struktur & Berkas</div>
                <a href="#file-list" class="nav-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.8; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Daftar Berkas API</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Autentikasi</div>
                <a href="#register" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Register Owner</span>
                </a>
                <a href="#login" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Login Akun</span>
                </a>
                <a href="#logout" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Logout</span>
                </a>
                <a href="#user" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>Profil User</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Manajemen UMKM</div>
                <a href="#umkm-get" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>Detail UMKM</span>
                </a>
                <a href="#umkm-post" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Daftar UMKM</span>
                </a>
                <a href="#karyawan-get" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>List Karyawan</span>
                </a>
                <a href="#karyawan-post" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Tambah Karyawan</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Asesmen & Radar</div>
                <a href="#asesmen-get" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>Kuesioner</span>
                </a>
                <a href="#asesmen-post" class="nav-link">
                    <span class="nav-link-method method-post">POST</span>
                    <span>Simpan Jawaban</span>
                </a>
                <a href="#dashboard" class="nav-link">
                    <span class="nav-link-method method-get">GET</span>
                    <span>Dashboard Radar</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main>
            <header>
                <div class="glow-circle"></div>
                <h1 class="app-title">RESTful API Dokumentasi</h1>
                <p class="app-desc">Selamat datang di portal dokumentasi resmi API Back-End SiSehat. API ini menggunakan format respons standar JSON dan dilindungi oleh otentikasi berbasis token personal (Laravel Sanctum).</p>
            </header>

            <!-- ======================================================= -->
            <!-- STRUKTUR BERKAS API -->
            <!-- ======================================================= -->
            <section id="file-list">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <h2>Daftar Berkas Implementasi API</h2>
                </div>
                <p class="app-desc" style="margin-bottom: 2rem;">
                    Lapisan API pada aplikasi <b>SiSehat</b> dirancang secara modular dan terpisah dari rute monolitik Blade yang sudah ada. Berikut adalah daftar berkas baru dan yang dimodifikasi sesuai dengan konfigurasi autentikasi <b>Laravel Sanctum</b> dan manajemen data <b>RESTful JSON</b>:
                </p>

                <div class="file-grid">
                    <!-- FILE 1: AuthController.php -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">AuthController.php</span>
                            </div>
                            <span class="file-badge badge-new">Baru</span>
                        </div>
                        <div class="file-desc">
                            Menangani proses registrasi akun Owner baru, login via token untuk semua role, logout (revokasi token), dan mengambil profil user aktif saat ini beserta data UMKM pendukung.
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Rute / Endpoint Terkait:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/register</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/login</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/logout</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-get">GET</span>/user</span>
                            </div>
                        </div>
                    </div>

                    <!-- FILE 2: UmkmController.php -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">UmkmController.php</span>
                            </div>
                            <span class="file-badge badge-new">Baru</span>
                        </div>
                        <div class="file-desc">
                            Menangani fitur manajemen UMKM melalui API, meliputi pendaftaran UMKM baru oleh Owner, penarikan detail UMKM, pengambilan daftar nama karyawan terdaftar, dan pendaftaran akun karyawan baru.
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Rute / Endpoint Terkait:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag"><span class="tag-method tag-get">GET</span>/umkm</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/umkm</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-get">GET</span>/umkm/karyawan</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/umkm/karyawan</span>
                            </div>
                        </div>
                    </div>

                    <!-- FILE 3: AssessmentController.php -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">AssessmentController.php</span>
                            </div>
                            <span class="file-badge badge-new">Baru</span>
                        </div>
                        <div class="file-desc">
                            Pusat alur kuesioner kesehatan. Menyediakan kuesioner dinamis (Owner & Karyawan), perhitungan scoring otomatis real-time, visualisasi radar 6 faktor, status kesehatan, dan rekomendasi solusi.
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Rute / Endpoint Terkait:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag"><span class="tag-method tag-get">GET</span>/asesmen</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-post">POST</span>/asesmen</span>
                                <span class="file-endpoint-tag"><span class="tag-method tag-get">GET</span>/asesmen/dashboard</span>
                            </div>
                        </div>
                    </div>

                    <!-- FILE 4: routes/api.php -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">routes/api.php</span>
                            </div>
                            <span class="file-badge badge-modify">Modifikasi</span>
                        </div>
                        <div class="file-desc">
                            Tempat konfigurasi rute API Back-End. Memisahkan secara ketat rute publik (register, login, ping) dan rute aman terproteksi token Sanctum (auth:sanctum).
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Fitur Utama:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag">Sanctum Auth Middleware</span>
                                <span class="file-endpoint-tag">API Docs Route /api</span>
                                <span class="file-endpoint-tag">Public Ping Response</span>
                            </div>
                        </div>
                    </div>

                    <!-- FILE 5: app/Models/User.php -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">app/Models/User.php</span>
                            </div>
                            <span class="file-badge badge-modify">Modifikasi</span>
                        </div>
                        <div class="file-desc">
                            Model Eloquent User ditambahkan trait <code>Laravel\Sanctum\HasApiTokens</code>. Memberikan dukungan token otentikasi sesi Bearer API untuk setiap pengguna.
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Fitur Utama:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag">HasApiTokens Trait</span>
                                <span class="file-endpoint-tag">Sanctum Auth</span>
                            </div>
                        </div>
                    </div>

                    <!-- FILE 6: composer.json -->
                    <div class="file-card">
                        <div class="file-header">
                            <div class="file-title-area">
                                <svg class="file-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span class="file-name">composer.json / .lock</span>
                            </div>
                            <span class="file-badge badge-modify">Modifikasi</span>
                        </div>
                        <div class="file-desc">
                            Menambahkan dependensi core <code>laravel/sanctum: ^4.3</code> guna mengaktifkan framework otentikasi token yang terenkripsi dan terenkapsulasi secara aman.
                        </div>
                        <div class="file-mapping">
                            <div class="file-mapping-title">Dependency Ditambahkan:</div>
                            <div class="file-endpoint-list">
                                <span class="file-endpoint-tag">laravel/sanctum ^4.3</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================= -->
            <!-- SISTEM & TES -->
            <!-- ======================================================= -->
            <section id="system-group">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--cyan)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                    <h2>Sistem & Pengujian</h2>
                </div>

                <!-- ENDPOINT: STATUS PING -->
                <div class="endpoint-card" id="ping">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/ping</span>
                        </div>
                        <span class="auth-badge auth-public">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Public Route
                        </span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Status Ping</h3>
                                <p class="endpoint-desc">Rute pengujian publik yang sangat ramah terhadap browser. Digunakan untuk memeriksa apakah server API Anda berjalan normal, terhubung ke internet, dan merespons dalam format JSON standar secara instan tanpa perlu melampirkan token.</p>
                                
                                <div>
                                    <div class="params-section-title">Query Parameters</div>
                                    <p class="endpoint-desc" style="font-style: italic;">Rute ini tidak memerlukan parameter apa pun.</p>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Koneksi API SiSehat berhasil!",
  "environment": "production",
  "timestamp": "2026-05-20T19:40:00+07:00"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================= -->
            <!-- AUTENTIKASI -->
            <!-- ======================================================= -->
            <section id="auth-group">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <h2>Autentikasi</h2>
                </div>

                <!-- ENDPOINT: REGISTER -->
                <div class="endpoint-card" id="register">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/register</span>
                        </div>
                        <span class="auth-badge auth-public">Public Route</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Registrasi Akun Owner</h3>
                                <p class="endpoint-desc">Mendaftarkan pengguna baru dengan peran utama sebagai <b>Owner</b> (Pemilik UMKM). Mengotomatiskan segmentasi umur ke kategori 1, 2, atau 3, membuat data login baru, dan langsung menerbitkan token sesi API.</p>
                                
                                <div>
                                    <div class="params-section-title">Request Body (JSON)</div>
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="param-name">nama_user</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Nama pengguna unik (maksimal 100 karakter).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">age</td>
                                                <td class="param-type">numeric <span class="param-required">Wajib</span></td>
                                                <td>Umur asli (akan dikelompokkan otomatis).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">gender</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Jenis Kelamin: <code>1</code> (Laki-laki) atau <code>2</code> (Perempuan).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">password</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Kata sandi keamanan (minimal 8 karakter).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">password_confirmation</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Konfirmasi ulang kata sandi (harus cocok).</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (201)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": {
      "nama_user": "Owner Baru",
      "age": 2,
      "gender": "1",
      "role": "owner",
      "id_user": 45
    },
    "access_token": "1|AbCdEfG...",
    "token_type": "Bearer"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: LOGIN -->
                <div class="endpoint-card" id="login">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/login</span>
                        </div>
                        <span class="auth-badge auth-public">Public Route</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Login Akun</h3>
                                <p class="endpoint-desc">Memvalidasi kredensial pengguna dan mengembalikan token otorisasi Bearer. Klien API wajib menyimpan token ini untuk disematkan pada setiap request terproteksi.</p>
                                
                                <div>
                                    <div class="params-section-title">Request Body (JSON)</div>
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="param-name">nama_user</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Username pengguna (bisa juga melampirkan key <code>email</code>).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">password</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Kata sandi akun terkait.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id_user": 2,
      "nama_user": "Responden 2",
      "role": "owner",
      "id_umkm": 2
    },
    "access_token": "2|XyZ123...",
    "token_type": "Bearer"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: LOGOUT -->
                <div class="endpoint-card" id="logout">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/logout</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Logout Sesi</h3>
                                <p class="endpoint-desc">Mencabut dan menghapus token autentikasi aktif yang sedang digunakan saat ini secara permanen dari database server.</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Logout berhasil"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: PROFIL USER -->
                <div class="endpoint-card" id="user">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/user</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Ambil Profil Pengguna</h3>
                                <p class="endpoint-desc">Mengambil detail data diri pengguna aktif saat ini berdasarkan Bearer token yang dilampirkan, lengkap dengan detail asosiasi data UMKM tempat dia bergabung.</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Data pengguna berhasil diambil",
  "data": {
    "user": {
      "id_user": 2,
      "nama_user": "Responden 2",
      "role": "owner",
      "id_umkm": 2
    },
    "umkm": {
      "id_umkm": 2,
      "nama_umkm": "Bakery Sejahtera",
      "industry": "Makanan",
      "usia_usaha": 3
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================= -->
            <!-- MANAJEMEN UMKM -->
            <!-- ======================================================= -->
            <section id="umkm-group">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--emerald)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <h2>Manajemen UMKM & Karyawan</h2>
                </div>

                <!-- ENDPOINT: DETAIL UMKM -->
                <div class="endpoint-card" id="umkm-get">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/umkm</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Detail UMKM Aktif</h3>
                                <p class="endpoint-desc">Mendapatkan data informasi terperinci mengenai UMKM tempat pengguna saat ini bernaung (Jika role <i>employee</i>) atau UMKM yang dimiliki (Jika role <i>owner</i>).</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Data UMKM berhasil diambil",
  "data": {
    "id_umkm": 2,
    "nama_umkm": "Bakery Sejahtera",
    "industry": "Makanan",
    "usia_usaha": 3,
    "id_user": 2
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: DAFTAR UMKM -->
                <div class="endpoint-card" id="umkm-post">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/umkm</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Registrasi UMKM Baru</h3>
                                <p class="endpoint-desc">Mendaftarkan profil usaha UMKM baru milik Owner. Proteksi ganda disematkan agar setiap akun Owner hanya diperbolehkan mendaftarkan maksimal **satu** unit UMKM saja.</p>
                                
                                <div>
                                    <div class="params-section-title">Request Body (JSON)</div>
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="param-name">nama_umkm</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Nama unit usaha UMKM.</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">industry</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Kategori industri/tipe bisnis.</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">usia_usaha</td>
                                                <td class="param-type">numeric <span class="param-required">Wajib</span></td>
                                                <td>Berapa tahun usaha telah berdiri.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (201)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "UMKM berhasil didaftarkan",
  "data": {
    "id_umkm": 6,
    "nama_umkm": "Laundry Kilat",
    "industry": "Jasa",
    "usia_usaha": 1,
    "id_user": 45
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: LIST KARYAWAN -->
                <div class="endpoint-card" id="karyawan-get">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/umkm/karyawan</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">List Karyawan Terdaftar</h3>
                                <p class="endpoint-desc">Mengambil daftar seluruh akun karyawan terdaftar yang tergabung pada satu unit UMKM berdasarkan id_umkm aktif pengguna saat ini.</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Daftar karyawan berhasil diambil",
  "data": [
    {
      "id_user": 1,
      "nama_user": "Responden 1",
      "gender": "1",
      "age": 1,
      "role": "employee",
      "id_umkm": 2
    }
  ]
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: TAMBAH KARYAWAN -->
                <div class="endpoint-card" id="karyawan-post">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/umkm/karyawan</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Daftarkan Karyawan Baru</h3>
                                <p class="endpoint-desc">Mengizinkan Owner untuk membuatkan akun baru bagi karyawannya. Mengotomatiskan kategori umur karyawan, menghubungkannya langsung ke UMKM, dan **secara otomatis membuka kembali status asesmen yang sempat selesai** agar karyawan baru dapat ikut mengisi.</p>
                                
                                <div>
                                    <div class="params-section-title">Request Body (JSON)</div>
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="param-name">nama_user</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Nama akun unik karyawan baru.</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">gender</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Jenis Kelamin: <code>1</code> atau <code>2</code>.</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">age</td>
                                                <td class="param-type">numeric <span class="param-required">Wajib</span></td>
                                                <td>Umur asli karyawan (angka).</td>
                                            </tr>
                                            <tr>
                                                <td class="param-name">password</td>
                                                <td class="param-type">string <span class="param-required">Wajib</span></td>
                                                <td>Password login karyawan (min 8 karakter).</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (201)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Karyawan berhasil didaftarkan",
  "data": {
    "nama_user": "Karyawan Sehat",
    "gender": "2",
    "age": 2,
    "role": "employee",
    "id_umkm": 2,
    "id_user": 48
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================= -->
            <!-- ASESMEN & RADAR -->
            <!-- ======================================================= -->
            <section id="assessment-group">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <h2>Asesmen & Radar Dashboard</h2>
                </div>

                <!-- ENDPOINT: KUESIONER -->
                <div class="endpoint-card" id="asesmen-get">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/asesmen</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Ambil Kuesioner (Pertanyaan)</h3>
                                <p class="endpoint-desc">Mengambil daftar pertanyaan kuesioner dinamis terjemahan Bahasa Indonesia yang sudah difilter sesuai dengan peran/role dari pengguna aktif yang meminta (baik khusus owner, khusus employee, atau keduanya), lengkap dengan teks opsi pilihan ganda dan status progres partisipasi karyawan saat ini.</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Daftar pertanyaan berhasil diambil",
  "data": {
    "already_submitted": false,
    "owner_finished": false,
    "employee_finished": false,
    "total_employees": 1,
    "employees_finished_count": 0,
    "sections": [
      {
        "factor_id": 1,
        "title": "Nilai Organisasi",
        "desc": "Budaya dan nilai organisasi anda.",
        "questions": [
          {
            "id": "OH1",
            "text": "Apakah usaha Anda memiliki Nomor Induk Berusaha (NIB)?",
            "options": ["Tidak", "Sedang Proses", "Ya"]
          }
        ]
      }
    ]
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: SUBMIT JAWABAN -->
                <div class="endpoint-card" id="asesmen-post">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-post">POST</span>
                            <span class="endpoint-path">/api/asesmen</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Simpan Jawaban Asesmen</h3>
                                <p class="endpoint-desc">Menyimpan sekumpulan jawaban dari kuesioner. Otomatis melakukan pencocokan skor (skala 3 opsi dan 5 opsi), mengkalkulasi ulang data rata-rata skor asesmen secara real-time, dan **mengubah status asesmen menjadi Selesai secara otomatis** jika seluruh anggota UMKM (Owner + Karyawan) terdeteksi telah rampung berpartisipasi.</p>
                                
                                <div>
                                    <div class="params-section-title">Request Body (JSON)</div>
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="param-name">answers</td>
                                                <td class="param-type">object <span class="param-required">Wajib</span></td>
                                                <td>Kamus berisi pasangan key (ID pertanyaan) dan value (opsi teks pilihan). Contoh: <code>{"OH1": "Ya", "OH10": "Setuju"}</code>.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Asesmen berhasil disimpan",
  "data": {
    "assessment_id": 14,
    "owner_finished": true,
    "employee_finished": true,
    "total_employees": 1,
    "employees_finished_count": 1,
    "assessment_status": "Selesai"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDPOINT: DASHBOARD RADAR -->
                <div class="endpoint-card" id="dashboard">
                    <div class="endpoint-header">
                        <div class="endpoint-identity">
                            <span class="method-badge badge-get">GET</span>
                            <span class="endpoint-path">/api/asesmen/dashboard</span>
                        </div>
                        <span class="auth-badge auth-required">Token Required</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-info">
                            <div class="endpoint-desc-area">
                                <h3 class="endpoint-title">Ringkasan Hasil & Radar Chart</h3>
                                <p class="endpoint-desc">Pusat data kecerdasan bisnis untuk aplikasi mobile/frontend. Mengembalikan skor kesehatan terhitung skala 100, kategori kesehatan (BAIK/CUKUP/KURANG), identifikasi faktor terlemah beserta **insight kritis & saran solusi terperinci**, array data mentah radar chart 6 faktor, ringkasan perbandingan ranking global, serta data detail sub-indikator (skor per pertanyaan).</p>
                            </div>

                            <div class="code-panel">
                                <div class="code-panel-header">
                                    <span class="code-panel-title">Response JSON (200)</span>
                                    <button class="btn-copy" onclick="copyToClipboard(this)">Salin</button>
                                </div>
                                <pre><code>{
  "success": true,
  "message": "Data ringkasan asesmen berhasil diambil",
  "data": {
    "has_assessment": true,
    "skor_kesehatan": 78,
    "status_kesehatan": "BAIK",
    "weakest_factor": {
      "id": 3,
      "name": "Sumber Daya Institusi",
      "score": 45.2
    },
    "insight_kritis": "Fokuslah pada perbaikan aset infrastruktur utama...",
    "total_responden": 45,
    "total_umkm": 15,
    "radar_chart": {
      "labels": ["Nilai Organisasi", "Keterlibatan Pemimpin", ...],
      "values": [80, 85, 45.2, 90, 75, 92]
    },
    "factors_details": [
      {
        "id": 1,
        "title": "Nilai Organisasi",
        "score_raw": 4,
        "score_percentage": 80,
        "category_label": "Baik",
        "sub_indicators": [
          {
            "question_id": "OH1",
            "question_text": "Apakah memiliki NIB?",
            "score_raw": 4,
            "category": "Tinggi"
          }
        ]
      }
    ]
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <script>
        function copyToClipboard(button) {
            const pre = button.parentElement.nextElementSibling;
            const code = pre.querySelector('code');
            const range = document.createRange();
            range.selectNode(code);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            
            try {
                document.execCommand('copy');
                button.textContent = 'Tersalin!';
                button.style.color = 'var(--emerald)';
                
                setTimeout(() => {
                    button.textContent = 'Salin';
                    button.style.color = 'var(--text-muted)';
                }, 2000);
            } catch (err) {
                console.error('Gagal menyalin: ', err);
            }
            window.getSelection().removeAllRanges();
        }

        // Active Sidebar Sync
        const sections = document.querySelectorAll('section, .endpoint-card');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </style>
</body>
</html>
