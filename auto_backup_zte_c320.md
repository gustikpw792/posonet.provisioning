# Panduan Konfigurasi Auto Backup OLT ZTE C320 V2.1

Dokumen ringkas ini digunakan sebagai referensi cepat untuk mengonfigurasi otomatisasi pencadangan (*auto backup*) file konfigurasi OLT ZTE C320 versi 2.1 ke server FTP/SFTP.

---

## 🛠️ Langkah-Langkah Konfigurasi

### 1. Masuk ke Mode Konfigurasi Global
Hubungkan ke OLT melalui SSH/Telnet, lalu ketik perintah berikut:
```text
ZXAN> enable
ZXAN# configure terminal
ZXAN(config)#
```

### 2. Daftarkan Server FTP Tujuan
Tentukan server FTP eksternal yang akan menampung file backup:
```text
ZXAN(config)# file-server auto-backup all server-index 1 ftp ipaddress <IP_SERVER_FTP> path <DIREKTORI_TUJUAN> user <USERNAME_FTP> password <PASSWORD_FTP>
```
* **Contoh Riil:**
  ```text
  ZXAN(config)# file-server auto-backup all server-index 1 ftp ipaddress 192.168.10.50 path /backup/olt user admin password rahasia
  ```

### 3. Atur Trigger & Jadwal Otomatisasi
Konfigurasikan agar sistem mendeteksi perubahan konfigurasi (*cfg-changed*) lalu melakukan pencadangan otomatis:
```text
ZXAN(config)# auto-backup condition cfg-changed hold-off-time 1 max-hold-off-time 2
```

### 4. Simpan Konfigurasi OLT
Pastikan perintah di atas tersimpan permanen di dalam memori OLT:
```text
ZXAN(config)# write
```

---

## 🔍 Perintah Verifikasi & Pengecekan

Gunakan perintah-perintah berikut untuk memantau status atau melakukan pengujian manual:

* **Melihat status konfigurasi auto backup:**
  ```text
  ZXAN(config)# show file-server auto-backup
  ```
* **Melakukan backup manual secara langsung (Uji Coba):**
  ```text
  ZXAN(config)# auto-backup start
  ```
* **Melihat log/riwayat proses pencadangan terakhir:**
  ```text
  ZXAN(config)# show auto-backup log
  ```

---

## ⚠️ Catatan Penting
1. **Konektivitas Jaringan:** Pastikan OLT bisa melakukan `ping` ke IP Server FTP.
2. **Hak Akses Server:** Pastikan akun FTP memiliki izin *write/upload* di direktori tujuan.
3. **Penyimpanan:** File yang diunggah biasanya berformat `.cfg` atau `.dat` sesuai konfigurasi sistem ZTE.