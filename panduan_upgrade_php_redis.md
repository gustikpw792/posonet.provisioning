# Panduan Upgrade ke PHP 7.4.0 (XAMPP) & Instalasi Redis di Windows

Panduan ini menjelaskan langkah demi langkah untuk melakukan upgrade versi PHP ke **7.4.0** pada XAMPP Windows, serta melakukan instalasi **Redis Server** beserta **Ekstensi PHP Redis**.

---

## 📋 Prasyarat
Sebelum memulai, pastikan Anda telah **mematikan (Stop)** semua layanan (Apache dan MySQL) melalui XAMPP Control Panel.

---

## 🛠️ Bagian 1: Upgrade PHP ke 7.4.0 di XAMPP

### Langkah 1: Unduh PHP 7.4.0
1. Buka halaman arsip resmi PHP Windows di [PHP-Windows Archives](https://windows.php.net/downloads/releases/archives/).
2. Cari versi **PHP 7.4.0**.
3. Unduh varian **Thread Safe (TS)** yang sesuai dengan arsitektur OS Anda:
   * **x64** (untuk OS 64-bit): `php-7.4.0-Win32-vc15-x64.zip`
   * **x86** (untuk OS 32-bit): `php-7.4.0-Win32-vc15-x86.zip`

### Langkah 2: Backup dan Ganti Folder PHP Lama
1. Buka direktori instalasi XAMPP Anda (biasanya di `C:\xampp\`).
2. Cari folder bernama `php`. Ubah nama (*rename*) folder tersebut menjadi `php_old` sebagai cadangan.
3. Buat folder baru dengan nama **`php`** di dalam direktori `C:\xampp\`.
4. Ekstrak seluruh isi file ZIP PHP 7.4.0 yang telah diunduh ke dalam folder `php` baru tersebut.

### Langkah 3: Konfigurasi File `php.ini`
1. Masuk ke folder `C:\xampp\php\`.
2. Cari file bernama `php.ini-development` atau `php.ini-production`. Duplikat/salin file tersebut dan ubah namanya menjadi **`php.ini`**.
3. Buka file `php.ini` menggunakan teks editor (seperti Notepad atau VS Code).
4. Cari baris berikut dan sesuaikan (hapus tanda titik koma `;` di depannya jika ada):
   ```ini
   extension_dir = "C:\xampp\php\ext"
   ```
5. Aktifkan ekstensi penting dengan menghapus tanda titik koma (`;`) di depan baris-baris berikut:
   ```ini
   extension=curl
   extension=gd2
   extension=mbstring
   extension=mysqli
   extension=openssl
   extension=pdo_mysql
   ```
6. Simpan perubahan file tersebut.

### Langkah 4: Sesuaikan Konfigurasi Apache (`httpd-xampp.conf`)
1. Buka file `C:\xampp\apache\conf\extra\httpd-xampp.conf`.
2. Pastikan pemuatan modul Apache mengarah ke file `.dll` yang benar dari versi PHP baru. Periksa atau sesuaikan baris berikut:
   ```apache
   LoadModule php7_module "C:/xampp/php/php7apache2_4.dll"
   ```
3. Simpan dan tutup file tersebut.

---

## ⚡ Bagian 2: Instalasi dan Konfigurasi Redis

### Langkah 1: Instal Redis Server di Windows
1. Buka halaman rilis [GitHub tporadowski/redis](https://github.com/tporadowski/redis/releases).
2. Unduh file berformat `.msi` versi terbaru yang stabil.
3. Untuk menjalankan server Redis, buka **Command Prompt (CMD)**, lalu ketik perintah berikut:
   ```cmd
   redis-cli
   ```

### Langkah 2: Unduh dan Pasang Ekstensi PHP Redis (`php_redis.dll`)
1. Buka situs [PECL php_redis](https://pecl.php.net/package/redis).
2. Cari versi ekstensi yang mendukung PHP 7.4, lalu klik tautan **DLL** di sebelahnya.
3. Pilih file unduhan dengan kriteria: **7.4**, **Thread Safe (TS)**, dan arsitektur yang sama dengan PHP Anda (**x64** atau **x86**).
4. Ekstrak file ZIP yang diunduh, lalu cari file bernama **`php_redis.dll`**.
5. Salin (*copy*) file `php_redis.dll` tersebut dan tempel (*paste*) ke dalam folder ekstensi XAMPP di **`C:\xampp\php\ext\`**.

### Langkah 3: Aktifkan Ekstensi di `php.ini`
1. Buka kembali file **`C:\xampp\php\php.ini`**.
2. Gulir ke bagian paling bawah dokumen atau ke area daftar ekstensi, lalu tambahkan baris berikut:
   ```ini
   extension=php_redis.dll
   ```
3. Simpan file `php.ini`.

---

## 🧪 Bagian 3: Uji Coba Verifikasi

1. Buka **XAMPP Control Panel** dan klik tombol **Start** pada modul **Apache**.
2. Buka browser Anda, lalu akses tautan: `http://localhost/dashboard/phpinfo.php` (atau buat file PHP baru berisi `<?php phpinfo(); ?>`).
3. Periksa informasi di halaman tersebut:
   * **PHP Version** di bagian atas harus menunjukkan **7.4.0**.
   * Gunakan fitur cari (`Ctrl + F`) lalu ketik **"redis"**. Jika berhasil, akan muncul tabel konfigurasi khusus untuk modul Redis.
4. Atau bisa coba di cmd
   ```cmd
   php -m
   ```