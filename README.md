# Inventory Management System (Mini Project 3)

Sistem Manajemen Inventaris berbasis web yang dikembangkan menggunakan **PHP (Native)** dengan pendekatan keamanan PDO, relasi database MySQL, serta manajemen transaksi stok yang konsisten.

## Fitur Utama
1. **Autentikasi & Hak Akses**: Sistem Login dan Logout dengan manajemen *session* serta pemisahan role (Admin / Staff).
2. **Manajemen Master Data**: CRUD lengkap untuk Kategori, Produk (dengan kode unik SKU), dan Supplier.
3. **Relasi Database**: Menerapkan relasi *One-to-Many* (Kategori ke Produk) dan *Many-to-Many* (Produk dan Supplier melalui *junction table*).
4. **Transaksi Stok Aman**: Pencatatan Barang Masuk (IN) dan Barang Keluar (OUT) menggunakan `BEGIN TRANSACTION`, `FOR UPDATE` (Row Locking), dan `ROLLBACK` untuk menjaga konsistensi stok agar tidak bernilai negatif.
5. **Pencarian, Filter, & Pagination**: Mempermudah navigasi data produk dalam jumlah banyak.
6. **Keamanan Ekstra**: Perlindungan terhadap *SQL Injection* menggunakan Prepared Statements dan pencegahan *XSS* dengan fungsi *Output Escaping*.

---

## Screenshot Aplikasi
Login admin
<img width="383" height="177" alt="image" src="https://github.com/user-attachments/assets/9391bf2a-f192-4fea-950d-ed1cc0bf9022" />
 

Dashboard 
<img width="403" height="185" alt="image" src="https://github.com/user-attachments/assets/2860149e-a7cd-4a57-b35f-f443ca2ffc26" />
 


Tambah produk 
<img width="396" height="284" alt="image" src="https://github.com/user-attachments/assets/82f4fcec-9439-4844-8e5f-f83e879396a7" />
 

Tampilan produk, pencarian produk, kategori 
<img width="454" height="253" alt="image" src="https://github.com/user-attachments/assets/ff9a9143-8860-415d-b540-a8dacdd8bc66" />
 

Barang masuk 
<img width="463" height="228" alt="image" src="https://github.com/user-attachments/assets/acb12489-d854-4f90-87f0-2dfcfef22a24" />
 

Barang keluar 
<img width="453" height="202" alt="image" src="https://github.com/user-attachments/assets/1164f52f-df8a-480f-86c7-3a9d9a24f9c4" />
 

Laporan 
<img width="560" height="103" alt="image" src="https://github.com/user-attachments/assets/c3c25988-3d86-4cc5-99bb-603756793845" />
 

---
## Cara Instalasi & Menjalankan Proyek

1. **Clone atau Simpan Proyek**
   Simpan seluruh folder proyek ke dalam direktori `htdocs` XAMPP Anda (`C:\xampp\htdocs\inventory-app`).

2. **Konfigurasi Database**
   - Buka XAMPP Control Panel dan aktifkan **Apache** serta **MySQL**.
   - Buka browser dan akses `http://localhost/phpmyadmin`.
   - Buat database baru dengan nama `inventory_db`.
   - Import file skema database `database/schema.sql`.

3. **Membuat Akun Admin**
   Jalankan query SQL berikut di phpMyAdmin untuk menambahkan akun admin default:
   ```sql
   INSERT INTO users (username, password_hash, role) 
   VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
   ```

4. **Menjalankan Aplikasi**
   Buka browser Anda dan akses URL berikut:
   ```text
   http://localhost/inventory-app/public/login.php
   ```
   * **Username**: `admin`
   * **Password**: `admin123`
