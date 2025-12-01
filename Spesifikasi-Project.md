# MangroveTour untuk Ekowisata Mangrove Wonorejo

| Nama | NRP |
| --- | --- |
| Royan Habibi Alfatih | 5025241115 |
| Bara Semangat Rohmani | 5025241144 |

## 1. Deskripsi Umum 

MangroveTour adalah sistem manajemen berbasis web untuk lokasi ekowisata. Sistem ini membantu pengelola dalam mengelola pemesanan tiket, data pengunjung, jadwal kunjungan, rating & review, serta laporan pendapatan. Tujuannya adalah memberikan pengalaman digital yang mudah bagi pengunjung dan efisiensi bagi pengelola.

<br>

## 2. Tujuan Proyek 
- Mempermudah pemesanan tiket secara online.
- Menyediakan informasi lengkap tentang lokasi wisata.
- Mengelola data pengunjung dan status tiket.
- Menyediakan laporan pendapatan harian, mingguan, bulanan.
- Memberikan fitur rating & review untuk meningkatkan kualitas layanan.

  
<br>

## 3. Fitur Utama 
- Landing Page: Informasi umum tentang lokasi, galeri foto/video, artikel-artikel.
- Pemesanan Tiket: Formulir pemesanan, pembayaran (opsional).
- Status Tiket: Update status tiket (Aktif, Digunakan, Kadaluarsa).
- Manajemen Pengunjung: Tambah, ubah, hapus data pengunjung.
- Rating & Review: Pengunjung memberi nilai dan komentar.
- Laporan Keuangan: Pendapatan per periode.
- Autentikasi Admin: Login, logout, role-based access.
- Dashboard Statistik: Grafik jumlah pengunjung dan pendapatan.

<br>

## 4. Peran Pengguna 
- Admin: Mengelola seluruh data 
- Operator: Validasi tiket di lokasi
- Pelanggan: Melihat halaman utama dan informasi yang tersedia, Memberi rating dan review, Memesan tiket
 
<br>

## 5. Spesifikasi Teknis 
- Front-End: HTML5, CSS3, Bootstrap, JavaScript
- Back-End: PHP (versi 8.x)
- Database: MySQL / MariaDB
- Server: Apache (XAMPP / Laragon)
- Tools: VS Code, phpMyAdmin
- Integrasi API (opsional): Integrasi Payment Gateway 

<br>

## 6. Struktur Database (Tabel Utama)
- Tabel pengunjung: id_pengunjung, nama, no_hp, email
- Tabel tiket: id_tiket, id_pengunjung, tanggal_berkunjung, status
- Tabel review: id_review, id_pengunjung, rating, komentar, tanggal
- Tabel user: id_user, username, password, role (Admin/Operator)

<br>

## 7. Desain Tampilan (Front-End) 
- Landing Page: Menampilkan informasi utama, akses untuk membeli tiket, dan akses untuk memberikan review
- Halaman Pemesanan: Form pemesanan tiket, konfirmasi
- Halaman Review: Form rating dan review
- Halaman Admin: Menampilkan informasi dan akses khusus admin

<br>

## 8. Alur Proses Sistem 
- Pengunjung membuka Landing Page dan dapat melihat informasi tentang objek wisata
- Pengunjung memesan tiket berkunjung -> Sistem menyimpan data tiket -> Statut tiket Aktif
- Operator validasi tiket saat kunjungan
- Pengunjung memberikan review setelah berkunjung
- Admin melihat laporan pendapatan

<br>

## 9. API Endpoints (Opsional) 
- GET /api/pengunjung – Menampilkan semua data pelanggan
- POST /api/tiket – Menambah tiket baru
- GET /api/tiket/{id} – Menampilkan detail tiket
- PUT /api/tiket/{id} – Memperbarui status tiket
- GET /api/review – Menampilkan review

<br>

## 10. Keamanan dan Validasi 
- Validasi input di sisi client (JavaScript) dan server (PHP).
- Password dienkripsi menggunakan password_hash().
- Akses dibatasi menggunakan session dan role-based authentication.

<br>

## 11. Pengembangan Lanjutan (Versi 2.0)
- Fitur notifikasi WhatsApp.
- QR Code untuk tiket.
- Integrasi dengan Payment Gateway (Midtrans / DOKU).
- Dashboard analitik menggunakan Chart.js atau Google Charts.
- Integrasi dengan Google Maps

<br><br>
