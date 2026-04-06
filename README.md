# Website Perpustakaan Digital

Panduan lengkap untuk menjalankan project perpustakaan digital di komputer lokal menggunakan XAMPP.

---

## Prasyarat

Sebelum memulai, pastikan kamu sudah mengunduh dan menginstall **XAMPP**:

> [Download XAMPP di sini](https://www.apachefriends.org/download.html) — install seperti biasa setelah download selesai.

---

## Langkah-Langkah Setup

### 1. Letakkan Folder Project

Download repository ini, lalu masukkan folder project ke dalam direktori `htdocs` milik XAMPP:

```
C:/xampp/htdocs/(nama-folder-project)
```

---

### 2. Jalankan XAMPP

Buka folder project di **VS Code**, lalu jalankan XAMPP. Pastikan kedua modul berikut sudah aktif (berstatus **Running**):

- **Apache**
- **MySQL**

---

### 3. Konfigurasi Database

Buka file konfigurasi database yang ada di:

```
config/database.php
```

Pastikan nilai `DB_NAME` sesuai dengan nama database yang akan kamu buat di langkah berikutnya.

> Kamu bisa mengubah nama database sesuai keinginanmu.

---

### 4. Buat Database di phpMyAdmin

Masuk ke **phpMyAdmin** dengan menekan tombol **Admin** pada bagian MySQL di XAMPP:

![Tampilan XAMPP Admin](tutorial/xampp-admin.png)

Setelah masuk ke phpMyAdmin, ikuti langkah berikut:

1. Klik **`+ Baru`** di panel kiri
2. Isi nama database — samakan dengan `DB_NAME` di `database.php`
3. Klik **Buat**

![Halaman phpMyAdmin](tutorial/phpmyadmin.png)
![Form Buat Database](tutorial/database.png)

---

### 5. Import File SQL

Setelah database berhasil dibuat:

1. Pergi ke tab **SQL** (tombol ada di bagian atas halaman)
2. *Paste* seluruh isi file `perpustakaan.sql` ke dalam kolom yang tersedia
3. Klik **Kirim** di bagian bawah

![Tab SQL phpMyAdmin](tutorial/sql.png)

Database dan seluruh datanya kini sudah terisi.

---

### 6. Jalankan Setup Website

Buka browser dan akses URL berikut:

```
localhost/(nama-folder)/setup.php
```

Akan muncul halaman setup yang menampilkan **Username** dan **Password** akun admin. Kamu bisa login menggunakan akun admin tersebut, atau membuat akun user biasa terlebih dahulu.

![Halaman Setup](tutorial/setup.png)

Setelah halaman ini muncul, klik **"Pergi ke halaman Login"** untuk langsung masuk.

---

### 7. Akses Website Utama

Untuk membuka halaman utama website perpustakaan, akses URL berikut di browser:

```
localhost/(nama-folder)/index.php
```

![Preview Website](tutorial/preview.png)