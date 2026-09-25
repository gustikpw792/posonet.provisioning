# Panduan Konfigurasi FileZilla Server (XAMPP) & Custom Home Directory

Panduan cepat ini menjelaskan cara mengaktifkan FileZilla Server bawaan XAMPP dan mengubah direktori penyimpanan default (*Home Directory*) untuk menampung file backup OLT atau perangkat lainnya.

---

## Langkah 1: Menjalankan FileZilla Server di XAMPP

1. Buka **XAMPP Control Panel**.
2. Pada baris **FileZilla**, klik tombol **Start**.
3. Pastikan indikator FileZilla berubah menjadi warna hijau (menandakan service telah berjalan).
4. Klik tombol **Admin** pada baris FileZilla untuk membuka *FileZilla Server Interface*.
5. Klik **OK / Connect** pada pop-up koneksi server (secara default tidak menggunakan password).

---

## Langkah 2: Membuat User Baru

1. Setelah aplikasi FileZilla Server terbuka, klik menu **Edit** > **Users**.
2. Pada sisi kanan jendela (*Users*), klik tombol **Add**.
3. Masukkan nama user baru (contoh: `backup_olt`), lalu klik **OK**.
4. Di bagian tengah (*Account settings*), centang opsi **Password** dan masukkan password yang aman.

---

## Langkah 3: Mengatur Default Home Directory

1. Masih di jendela **Users**, beralihlah ke menu **Shared folders** di panel sebelah kiri.
2. Di bawah kolom *Shared folders*, klik tombol **Add**.
3. Cari dan pilih folder di komputer Anda yang ingin dijadikan tempat penyimpanan utama (contoh: `D:\Backup_OLT`).
4. **Penting:** Pastikan folder yang baru Anda tambahkan tersebut terpilih, lalu klik tombol **Set as home dir** di sebelah kanan. Tanda `H` akan muncul di samping direktori tersebut.
5. Di bagian **Files** dan **Directories** (sebelah kanan), centang hak akses berikut agar OLT bisa menulis file:
   * **Files:** Read, Write, Delete, Append
   * **Directories:** Create, Delete, List, +Subdirs

---

## Langkah 4: Menyimpan & Pengujian

1. Klik tombol **OK** di bagian bawah jendela *Users* untuk menyimpan konfigurasi.
2. Pastikan Windows Firewall Anda telah mengizinkan port FTP (**Port 21**) agar OLT dapat terhubung dari luar.
3. Lakukan pengujian koneksi dari komputer lain atau langsung dari OLT menggunakan parameter IP komputer XAMPP, username, dan password yang telah dibuat.
