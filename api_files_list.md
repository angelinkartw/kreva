# Daftar File Implementasi RESTful API - SiSehat

Berikut adalah daftar file yang dibuat baru dan diubah dalam rangka implementasi Back-End RESTful API berbasis Laravel Sanctum pada aplikasi SiSehat:

---

## 1. Berkas Baru (New Files)
Semua Controller API ditempatkan di folder khusus `app/Http/Controllers/Api/` agar terpisah dengan controller web Blade monolitik yang sudah ada:

### 📄 [AuthController.php](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AuthController.php)
* **Tipe**: Controller
* **Deskripsi**: Menangani proses registrasi akun Owner baru, login via token untuk semua role, logout (revokasi token), dan mengambil profil user aktif saat ini beserta data UMKM pendukung.

### 📄 [UmkmController.php](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/UmkmController.php)
* **Tipe**: Controller
* **Deskripsi**: Menangani fitur manajemen UMKM melalui API, meliputi pendaftaran UMKM baru oleh Owner, penarikan detail UMKM, pengambilan daftar nama karyawan terdaftar, dan pendaftaran akun karyawan baru.

### 📄 [AssessmentController.php](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php)
* **Tipe**: Controller
* **Deskripsi**: Pusat alur survei/kuesioner kesehatan. Menyediakan endpoint untuk daftar pertanyaan terjemahan Bahasa Indonesia berdasarkan peran user, penyimpanan respons jawaban individu dengan logika live-scoring, serta kalkulasi radar chart, insight kritis terlemah, dan status kesehatan.

---

## 2. Berkas yang Diubah (Modified Files)

### 📄 [User.php](file:///c:/laragon/www/sisehat/app/Models/User.php)
* **Tipe**: Model Eloquent
* **Deskripsi**: Ditambahkan trait `Laravel\Sanctum\HasApiTokens` untuk mengaktifkan fitur penerbitan dan otorisasi access token menggunakan Sanctum Bearer Auth.

### 📄 [api.php](file:///c:/laragon/www/sisehat/routes/api.php)
* **Tipe**: Routing
* **Deskripsi**: Rute khusus Back-End API didefinisikan di sini. Memisahkan endpoint publik (`/login`, `/register`, `/ping`) dan endpoint aman terproteksi token Sanctum.

### 📄 [composer.json](file:///c:/laragon/www/sisehat/composer.json) & [composer.lock](file:///c:/laragon/www/sisehat/composer.lock)
* **Tipe**: Konfigurasi Dependensi Composer
* **Deskripsi**: Ditambahkan dependensi pustaka `"laravel/sanctum": "^4.3"` ke dalam proyek untuk mendukung token-based authentication.
