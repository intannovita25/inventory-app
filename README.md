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


<img width="521" height="241" alt="image" src="https://github.com/user-attachments/assets/5950dc92-9a6c-432b-9b38-f40405a59f27" />

 

Dashboard 


<img width="566" height="260" alt="image" src="https://github.com/user-attachments/assets/cede1d76-aa9c-4782-8c35-b8ed74286836" />

 


Tambah produk 


<img width="481" height="345" alt="image" src="https://github.com/user-attachments/assets/ab654f17-d744-4f0a-b436-039e77bf3fd6" />

 

Tampilan produk, pencarian produk, kategori 


<img width="611" height="340" alt="image" src="https://github.com/user-attachments/assets/54514747-d5f6-4778-8dd4-39f9cc440375" />

 

Barang masuk 


<img width="578" height="285" alt="image" src="https://github.com/user-attachments/assets/539efb3d-ecd8-4d78-92c6-3c321acacd82" />

 

Barang keluar 


<img width="618" height="275" alt="image" src="https://github.com/user-attachments/assets/5276a944-8893-4c0a-a75b-824891c94724" />

 

Laporan 


<img width="943" height="173" alt="image" src="https://github.com/user-attachments/assets/dbae1d00-43e5-455c-a351-335bfc3efaa1" />

 

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
