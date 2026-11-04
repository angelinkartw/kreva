# Panduan Stress Testing SiSehat Menggunakan k6 🚀

Folder ini berisi konfigurasi dan skrip stress testing untuk aplikasi **SiSehat** menggunakan **k6**, alat pengujian beban (*load testing*) modern yang sangat ringan dan efisien.

Ada dua skrip yang tersedia:
1. **`stress_test.js`**: Menguji rute umum halaman tamu (Login & Register) secara cepat.
2. **`stress_test_all.js`**: Menguji **rute & fitur paling penting/utama** aplikasi (*Halaman Login, Otentikasi Masuk, Dashboard Statistik, Formulir Asesmen, dan Logout*) dengan penanganan CSRF Token & Session Cookies otomatis.

---

## 🛠️ Persiapan & Instalasi k6

Sebelum menjalankan stress test, pastikan Anda telah menginstal k6 di sistem operasi Windows Anda.

### Cara 1: Menggunakan Winget (Rekomendasi - Cepat)
Buka Terminal/PowerShell Anda, jalankan perintah berikut:
```powershell
winget install k6
```

### Cara 2: Menggunakan Chocolatey
Jika Anda menggunakan Chocolatey:
```powershell
choco install k6
```

---

## 🏃 Cara Menjalankan Skrip Pengujian

### 1. Menjalankan Skrip Halaman Tamu (`stress_test.js`)
Menguji halaman `/login` dan `/register` tanpa membutuhkan kredensial akun:
* **Target Default (Production Railway)**:
  ```bash
  k6 run tests/k6/stress_test.js
  ```
* **Target Localhost**:
  ```bash
  k6 run -e BASE_URL=http://localhost:8000 tests/k6/stress_test.js
  ```

---

### 2. Menjalankan Skrip Fitur Inti Aplikasi (`stress_test_all.js`)
Skrip ini mensimulasikan alur terpenting dari aplikasi SiSehat: mengunjungi halaman login -> masuk ke sistem -> melihat dashboard ringkasan -> membuka formulir kuesioner asesmen -> logout secara aman.

#### A. Hanya Menguji Rute Publik (Halaman Login Tamu)
Jika Anda tidak menyertakan username & password, skrip ini hanya akan menguji performa halaman login saja:
```bash
k6 run tests/k6/stress_test_all.js
```

#### B. Menguji Fitur Inti (Termasuk Login, Dashboard, & Asesmen)
Untuk menguji rute utama terproteksi secara utuh, kirimkan kredensial akun menggunakan opsi `-e` (Environment Variables) dari command line:

* **Contoh Menjalankan di Production Railway**:
  ```bash
  k6 run -e LOGIN_USER=nama_user_anda -e LOGIN_PASS=password_anda tests/k6/stress_test_all.js
  ```
  
* **Contoh Menjalankan di Localhost**:
  ```bash
  k6 run -e BASE_URL=http://localhost:8000 -e LOGIN_USER=nama_user_anda -e LOGIN_PASS=password_anda tests/k6/stress_test_all.js
  ```

*Ganti `nama_user_anda` dan `password_anda` sesuai data akun valid yang ada di database Anda.*

---

## ⚙️ Spesifikasi & Metrik Pengujian

Sesuai permintaan Anda, skrip dikonfigurasi dengan:
* **`vus: 5`**: Mensimulasikan **5 User Virtual** aktif secara bersamaan.
* **`duration: '15s'`**: Durasi pengujian berlangsung selama **15 detik**.
* **Thresholds (Batas Toleransi Performa)**:
  * `http_req_failed < 0.01`: Toleransi kegagalan request (error rate) harus di bawah **1%**.
  * `http_req_duration: p(95)<4500`: 95% request harus merespons di bawah **4.5 detik (4500ms)** (disesuaikan dengan spesifikasi server cloud gratisan Railway).
