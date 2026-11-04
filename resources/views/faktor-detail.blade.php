<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Faktor - {{ $faktor['title'] }}</title>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/responsive-layout.css') }}" rel="stylesheet">

    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; height: 100vh; background-color: #f4f7f6; overflow: hidden; }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 280px; background-color: #ffffff; border-right: 1px solid #f1f5f9;
            display: flex; flex-direction: column; padding: 25px 15px; flex-shrink: 0; z-index: 10;
        }
        .logo-box { display: flex; justify-content: center; align-items: center; margin-bottom: 25px; width: 100%; }
        .logo-main { width: 150px; height: auto; display: block; }

        .btn-primary-custom {
            background-color: #0a4ebd; color: white; border: none; padding: 12px; border-radius: 8px;
            cursor: pointer; font-weight: 700; font-size: 13px; margin-bottom: 20px; width: 100%; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary-custom:hover { background-color: #083ba1; transform: translateY(-1px); }
                .sidebar-divider { border: none; border-top: 1.5px solid #e5e7eb; margin-top: 10px; margin-bottom: 15px; width: 100%; }

        .nav-list { list-style: none; flex-grow: 1; margin-top: 10px; }
        .nav-item { 
            display: flex; align-items: center; gap: 12px; padding: 12px 15px; 
            margin-bottom: 8px; border-radius: 10px; cursor: pointer; 
            color: #718096; transition: all 0.3s ease; 
            font-weight: 500;
        }
        .nav-icon {
            width: 20px; height: 20px; object-fit: contain;
            filter: grayscale(100%); opacity: 0.6; transition: 0.3s;
        }
        .nav-item span { font-size: 14px; line-height: normal; }
        
        .nav-item.active { 
            background-color: #eef2ff; 
            color: #1d4ed8; 
            font-weight: 700;
        }
        .nav-item.active .nav-icon { 
            filter: none; 
            opacity: 1; 
            filter: brightness(0) saturate(100%) invert(26%) sepia(89%) saturate(1966%) hue-rotate(211deg) brightness(97%) contrast(101%);
        }
        
        .nav-item:hover:not(.active) { background-color: #f8fafc; color: #1e293b; }
        .nav-item:hover .nav-icon { opacity: 0.9; filter: grayscale(0%); }

        .profile-card { 
            margin-top: auto; 
            padding: 12px 15px; 
            background-color: #eef2ff; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            cursor: pointer;
            transition: 0.3s;
        }
        .profile-card:hover { background-color: #e0e7ff; }
        .avatar { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info { flex-grow: 1; text-align: left; }
        .profile-info h4 { font-size: 14px; color: #1e293b; font-weight: 800; line-height: 1.2; }
        .profile-info p { font-size: 11px; color: #64748b; font-weight: 600; }
        
        .logout-btn-small {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .logout-btn-small:hover {
            background-color: #fee2e2;
        }
        .logout-btn-small svg {
            width: 20px;
            height: 20px;
        }

        /* ================= MAIN CONTENT ================= */
        .content-area { flex-grow: 1; padding: 40px; overflow-y: auto; scroll-behavior: smooth; }
        
        .page-header { margin-bottom: 30px; display: flex; flex-direction: column; gap: 8px; }
        .page-header h1 { font-size: 28px; color: #1e293b; font-weight: 800; margin-bottom: 4px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; max-width: 800px; }

        .back-link { font-size: 13px; color: #1d4ed8; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }

        /* Summary Box */
        .summary-box {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: stretch; /* Make children stretch to equal height */
            gap: 30px;
            margin-bottom: 30px;
        }

        .summary-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
        }

        .summary-title-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .summary-title-wrapper h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        .factor-badge {
            background-color: #eef2ff;
            color: #3b82f6;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .summary-left p {
            color: #475569;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        .summary-right {
            background-color: #fffbeb;
            border-radius: 12px;
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 180px;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .score-label {
            font-size: 11px;
            font-weight: 700;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .score-value {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .score-value span {
            font-size: 16px;
            color: #64748b;
            font-weight: 600;
        }

        /* Chart Grid Layout */
        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .card { 
            background: #ffffff; 
            border-radius: 12px; 
            padding: 25px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
            border: none;
        }

        .card-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .donut-container {
            position: relative;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .legend-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #475569;
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .legend-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Table */
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .table-header {
            background-color: #f8fafc;
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 25px;
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 15px 25px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge.sedang { color: #d97706; background-color: #fef3c7; }
        .badge.tinggi { color: #059669; background-color: #d1fae5; }
        .badge.rendah { color: #dc2626; background-color: #fee2e2; }

    </style>
    <link href="{{ asset('css/kreva-theme.css') }}" rel="stylesheet">
</head>
<body class="kreva-analytics">

    <div class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA" class="logo-main" onerror="this.style.display='none'; this.insertAdjacentHTML('afterend', '<h2 style=\'color:#0d47a1; font-weight:900;\'>KREVA</h2>');">
        </div>
        
        <button class="btn-primary-custom" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">+ Tambahkan UMKM Anda</button>
        <div class="sidebar-divider"></div>
        
        <ul class="nav-list">
            <li class="nav-item" onclick="window.location='{{ route('dashboard') }}'">
                <img src="{{ asset('images/Beranda_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/25/25694.png'">
                <span>Beranda</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.asesmen') }}'">
                <img src="{{ asset('images/Asesmen_Organisasi_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1161/1161388.png'">
                <span>Asesmen Organisasi</span>
            </li>
            <li class="nav-item active" onclick="window.location='{{ route('dashboard.faktor') }}'">
                <img src="{{ asset('images/6_Faktor_Penilaian_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/138/138439.png'">
                <span>6 Faktor Penilaian</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.rekomendasi') }}'">
                <img src="{{ asset('images/Rekomendasi_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/151/151300.png'">
                <span>Rekomendasi</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">
                <img src="{{ asset('images/Tambah_Data_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2990/2990315.png'">
                <span>Tambah Data UMKM</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-karyawan') }}'">
                <img src="{{ asset('images/Tambah_Data_Karyawan_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'">
                <span>Tambah Data Karyawan</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.manajemen-umkm') }}'">
                <img src="{{ asset('images/Manajemen_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/104/104646.png'">
                <span>Manajemen UMKM</span>
            </li>
        </ul>

        <div class="profile-card">
            <div class="avatar">
                <img src="{{ asset('images/profil.svg') }}" class="nav-icon">
            </div>
            <div class="profile-info">
                <h4>{{ auth()->user()->name ?? 'Riski' }}</h4>
                <x-sidebar-role-subtitle />
            </div>
            <button class="logout-btn-small" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Keluar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="content-area">
        <a href="{{ route('dashboard.faktor') }}" class="back-link">← Kembali ke 6 Faktor Penilaian</a>
        
        <div class="page-header">
            <h1>{{ $faktor['title'] }}</h1>
            <p>Berdasarkan hasil analisis, berikut adalah penjelasan dari faktor {{ strtolower($faktor['title']) }} UMKM anda.</p>
        </div>

        <div class="summary-box">
            <div class="summary-left">
                <div class="summary-title-wrapper">
                    <h2>{{ $faktor['title'] }}</h2>
                    <span class="factor-badge">{{ $faktor['id'] }} / 6</span>
                </div>
                <p>{{ $faktor['description'] }}</p>
            </div>
            <div class="summary-right">
                <div class="score-label">Skor Rata-Rata</div>
                <div class="score-value">{{ $faktor['score'] }} <span>/ 5.0</span></div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="card">
                <h3 class="card-title">Distribusi Jawaban per Sub-Indikator</h3>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            
            <div class="card">
                <h3 class="card-title">Kategori Kesehatan Faktor</h3>
                <p style="font-size: 11px; color: #64748b; margin-top: -15px; margin-bottom: 20px;">Berdasarkan agregat nyawa responden</p>
                <div class="donut-container">
                    <canvas id="donutChart"></canvas>
                    <!-- Center text -->
                    <div style="position: absolute; text-align: center;">
                        <div style="font-size: 20px; font-weight: 800; color: #0f172a;">{{ $faktor['category_percentage'] }}%</div>
                        <div style="font-size: 11px; color: #64748b;">{{ $faktor['category_label'] }}</div>
                    </div>
                </div>
                
                <div class="legend-container">
                    <div class="legend-item">
                        <div class="legend-left"><div class="legend-dot" style="background-color: #059669;"></div> Tinggi (Baik)</div>
                        <div class="legend-value">{{ $faktor['category_percentage'] }}%</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-left"><div class="legend-dot" style="background-color: #d97706;"></div> Sedang</div>
                        <div class="legend-value">{{ max(0, 100 - $faktor['category_percentage'] - 10) }}%</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-left"><div class="legend-dot" style="background-color: #dc2626;"></div> Rendah</div>
                        <div class="legend-value">10%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 class="card-title" style="margin-bottom: 0;">Tabel Detail Sub-Indikator</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama sub-indikator</th>
                        <th>Skor rata-rata</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faktor['sub_indicators'] as $sub)
                    <tr>
                        <td>{{ $sub['name'] }}</td>
                        <td style="text-align: center;">{{ $sub['score'] }}</td>
                        <td>
                            <span class="badge {{ strtolower($sub['category']) }}">
                                {{ $sub['category'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="height: 40px;"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Data for Bar Chart
            const chartData = {!! json_encode($faktor['chart_data']) !!};
            const labels = chartData.map((_, i) => 'Sub-' + (i + 1));
            
            const ctxBar = document.getElementById('barChart').getContext('2d');
            
            // Map scores to colors and heights
            // Max height is 5
            const backgroundColors = chartData.map(score => {
                if(score >= 4.0) return '#34a853'; // Green
                if(score >= 3.0) return '#fbbc04'; // Yellow
                return '#ea4335'; // Red
            });

            // Calculate remaining values for the gray background bars
            const remainingData = chartData.map(score => 5 - score);

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Skor',
                            data: chartData,
                            backgroundColor: backgroundColors,
                            borderRadius: 0, // removed border radius to make it look like one continuous bar with the top part
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        },
                        {
                            label: 'Sisa',
                            data: remainingData,
                            backgroundColor: '#f1f5f9', // Light gray
                            borderRadius: 0,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            max: 5,
                            ticks: {
                                stepSize: 1,
                                color: '#94a3b8',
                                font: { size: 11 }
                            },
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            }
                        },
                        x: {
                            stacked: true,
                            ticks: {
                                color: '#64748b',
                                font: { size: 10 }
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Skor: ' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });

            // Donut Chart
            const ctxDonut = document.getElementById('donutChart').getContext('2d');
            const pct = {{ $faktor['category_percentage'] }};
            const rem1 = 100 - pct - 12;
            const rem2 = 12;

            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Tinggi', 'Sedang', 'Rendah'],
                    datasets: [{
                        data: [pct, rem1, rem2],
                        backgroundColor: ['#059669', '#d97706', '#dc2626'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
        });
    </script>
    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>
