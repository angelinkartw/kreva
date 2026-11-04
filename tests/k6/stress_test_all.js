import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    // =====================================================
    // MANAJEMEN SKENARIO: MEMAKSA USER ANTRE SECARA AMAN
    // =====================================================
    scenarios: {
        evaluasi_sisehat: {
            executor: 'per-vu-iterations',
            vus: 5,              // Menggunakan tepat 5 Virtual Users sesuai kebutuhanmu
            iterations: 1,       // Setiap user HANYA boleh mengeksekusi alur tepat 1 kali
            maxDuration: '1m',   // Batas waktu toleransi maksimal seluruh pengujian
        },
    },

    // Nilai ambang batas kegagalan dan performa yang ketat
    thresholds: {
        http_req_duration: ['p(95)<10000'], // 95% request harus di bawah 10 detik
        http_req_failed: ['rate<0.05'],     // Tingkat kegagalan total wajib di bawah 5%
    },
};

// Kamu tinggal mengganti komentar (//) di bawah ini untuk berpindah target server
const BASE_URL = 'https://sisehat.infinityfree.me';
// const BASE_URL = 'https://sisehat-web-production.up.railway.app';

export default function () {
    const jar = http.cookieJar();

    // Menyuntikkan Header Browser Asli agar tidak dicurigai sebagai Bot jahat oleh firewall
    const params = {
        jar: jar,
        timeout: '30s',
        headers: {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language': 'id,en-US;q=0.7,en;q=0.3',
            'Connection': 'keep-alive', // Menjaga stabilitas handshake jaringan
        },
    };

    // =====================================================
    // 1. KUNJUNGAN KE HALAMAN LOGIN (GET)
    // =====================================================
    const loginPage = http.get(`${BASE_URL}/login`, params);

    check(loginPage, {
        '1. GET /login Sukses (200)': (r) => r.status === 200,
    });

    // Jeda 3 detik (Meniru manusia mengetik username dan password)
    sleep(3);

    // =====================================================
    // 2. PROSES OTENTIKASI AKUN (POST)
    // =====================================================
    const payload = {
        email: __ENV.LOGIN_USER || 'Kodlak',
        password: __ENV.LOGIN_PASS || 'Pass12345',
    };

    // Duplikasi params dan tambahkan Content-Type untuk form POST
    const postParams = Object.assign({}, params, {
        headers: Object.assign({}, params.headers, {
            'Content-Type': 'application/x-www-form-urlencoded',
        }),
        redirects: 0, // Mencegah k6 otomatis mengejar redirect agar cookie tidak rusak
    });

    const loginRes = http.post(`${BASE_URL}/login`, payload, postParams);

    console.log(`[VU #${__VU}] Status Login Terbaca: ${loginRes.status}`);

    check(loginRes, {
        '2. POST /login Berhasil Direspons': (r) => r.status === 302 || r.status === 200,
    });

    sleep(3);

    // =====================================================
    // 3. AKSES HALAMAN UTAMA (DASHBOARD)
    // =====================================================
    const dashboard = http.get(`${BASE_URL}/dashboard`, params);

    check(dashboard, {
        '3. GET /dashboard Lolos (200)': (r) => r.status === 200,
    });

    sleep(2);

    // =====================================================
    // 4. AKSES FITUR UTAMA (ENAM FAKTOR)
    // =====================================================
    const faktor = http.get(`${BASE_URL}/enam-faktor`, params);

    check(faktor, {
        '4. GET /enam-faktor Lolos (200)': (r) => r.status === 200,
    });

    sleep(2);

    // =====================================================
    // 5. AKSES LOGIKA SISTEM (REKOMENDASI)
    // =====================================================
    const rekomendasi = http.get(`${BASE_URL}/rekomendasi`, params);

    check(rekomendasi, {
        '5. GET /rekomendasi Lolos (200)': (r) => r.status === 200,
    });

    // Jeda akhir sebelum VU menutup koneksi secara berkala
    sleep(2);
}