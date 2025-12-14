# � MangroveTour: Ekowisata Mangrove Wonorejo - *Laporan Proyek Akhir*

| **Mata Kuliah** | **Kelas** | **Kelompok** | **Dosen** | **Periode** | 
| :--------: | :-------: | :-------: |:----------: | :-------------: |
| `EF234301` Pemrograman Web | A | 19 | Fajar Baskoro, S.Kom., M.T. | 24 November—14 Desember 2025 |

---

## 📋 Daftar Isi

1. [Ringkasan Proyek](#-ringkasan-proyek)
2. [Fitur Utama](#-fitur-utama)
3. [Implementasi Teknis](#-implementasi-teknis)
4. [Diagram Sistem](#-diagram-sistem)
5. [Panduan Pengguna](#-panduan-pengguna)
6. [Setup & Instalasi](#-setup--instalasi)
7. [Struktur Database](#-struktur-database)
8. [API Integration](#-api-integration)
9. [Testing](#-testing)
10. [Video Demonstrasi](#-video-demonstrasi)
11. [Pembagian Jobdesk](#-pembagian-jobdesk)
12. [Resources](#-resources)

---

## 📖 Ringkasan Proyek

### Latar Belakang
**MangroveTour** adalah sistem manajemen ekowisata untuk Hutan Mangrove Wonorejo, Surabaya. Proyek ini dirancang untuk memudahkan pengunjung dalam memesan tiket dan memberikan ulasan, serta membantu admin mengelola data pengunjung, tiket, ulasan, dan laporan keuangan.

### Tujuan
- ✅ Menyediakan platform booking tiket online yang mudah digunakan
- ✅ Mengintegrasikan sistem pembayaran digital (Midtrans)
- ✅ Menyediakan dashboard admin untuk manajemen data
- ✅ Mencatat ulasan dan rating pengunjung
- ✅ Menghasilkan laporan keuangan dan statistik

### Target Pengguna
1. **Pengunjung**: Masyarakat umum yang ingin berkunjung ke Hutan Mangrove Wonorejo
2. **Admin**: Staff yang mengelola sistem, data pengunjung, dan laporan
3. **Operator**: Staff pendukung yang membantu pengelolaan data

---

## 🎯 Fitur Utama

### Frontend (Pengunjung)
| Fitur | Deskripsi | Status |
|-------|-----------|--------|
| **Landing Page** | Halaman utama dengan info mangrove, gallery, dan reviews | ✅ Complete |
| **Registrasi Diri** | Pengunjung bisa mendaftar akun sendiri | ✅ Complete |
| **Login/Logout** | Manajemen session pengunjung | ✅ Complete |
| **Booking Tiket** | Form booking dengan integrasi Midtrans | ✅ Complete |
| **Pembayaran** | Midtrans SNAP sandbox integration | ✅ Complete |
| **Review & Rating** | Pengunjung bisa memberikan ulasan 1-5 bintang | ✅ Complete |
| **Responsive Design** | Mobile-friendly UI dengan Bootstrap 5 | ✅ Complete |

### Backend (Admin)
| Fitur | Deskripsi | Status |
|-------|-----------|--------|
| **Admin Login** | Authentikasi admin/operator | ✅ Complete |
| **Dashboard** | Statistik pengunjung, tiket, revenue | ✅ Complete |
| **Manajemen Pengunjung** | CRUD data pengunjung | ✅ Complete |
| **Manajemen Tiket** | Lihat, edit, hapus tiket | ✅ Complete |
| **Manajemen Review** | Lihat review dari pengunjung | ✅ Complete |
| **Laporan Pendapatan** | Revenue report by daily/weekly/monthly | ✅ Complete |
| **Laporan Keuangan** | Ringkasan transaksi pembayaran | ✅ Complete |

### Database
| Tabel | Fungsi | Status |
|-------|--------|--------|
| **pengunjung** | Simpan data pengunjung terdaftar | ✅ Complete |
| **tiket** | Riwayat pembelian tiket | ✅ Complete |
| **review** | Ulasan & rating dari pengunjung | ✅ Complete |
| **user** | Data login admin/operator | ✅ Complete |
| **transactions** | Riwayat transaksi Midtrans | ✅ Complete |

---

## 🛠️ Implementasi Teknis

### 1. **Frontend & Backend Development**

#### Teknologi yang Digunakan
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla), Bootstrap 5.3.8
- **Backend**: PHP 8.0+, Apache 2.4+
- **Database**: MySQL 8.0 / MariaDB
- **Payment Gateway**: Midtrans SNAP (Sandbox)
- **Version Control**: Git/GitHub

#### Frontend Architecture
```
public/
├── index.html           # Landing page dengan hero, gallery, reviews
├── booking.html         # Form booking tiket
├── review.html          # Form submit review
├── assets/
│   ├── css/
│   │   ├── style.css    # Main styling dengan CSS variables
│   │   └── bootstrap/   # Local Bootstrap 5.3.8 files
│   └── js/
│       ├── app.js       # Main JavaScript logic
│       └── midtrans-payment.js # Payment handler
```

**Fitur Frontend:**
- ✅ Responsive design (mobile-first approach)
- ✅ CSS variables untuk theming konsisten (--primary-green, --secondary-green, dll)
- ✅ Form validation dengan JavaScript
- ✅ Image carousel (hero & gallery)
- ✅ Star rating system interaktif
- ✅ Session-based authentication display

#### Backend Architecture
```
backend/
├── config/
│   ├── database.php     # Database connection & helper functions
│   ├── auth_helper.php  # Authentication & session management
│   └── midtrans.php     # Midtrans API configuration
├── auth/
│   ├── login.php        # Admin login page
│   ├── logout.php       # Admin logout handler
│   ├── visitor-login.php    # Visitor login page
│   ├── visitor-register.php # Visitor registration page
│   └── visitor-logout.php   # Visitor logout handler
├── api/
│   ├── create_transaction.php   # Create Midtrans transaction
│   ├── midtrans_callback.php    # Payment webhook handler
│   ├── pengunjung.php           # Visitor CRUD API
│   ├── tiket.php                # Ticket CRUD API
│   ├── review.php               # Review CRUD API
│   ├── transaction_status.php   # Check transaction status
│   └── visitor-status.php       # Check visitor status
└── views/
    ├── dashboard.php        # Admin statistics dashboard
    ├── pengunjung.php       # Visitor management page
    ├── tiket.php            # Ticket management page
    ├── review.php           # Review viewing page
    ├── revenue_report.php   # Revenue report (daily/weekly/monthly)
    └── financial_report.php # Payment transactions report
```

**Fitur Backend:**
- ✅ PDO prepared statements (SQL injection protected)
- ✅ bcrypt password hashing
- ✅ Server-side form validation
- ✅ RESTful API endpoints
- ✅ Session-based authentication + role-based access control
- ✅ Error logging & handling

#### Key Implementation Details

**Database Connection (database.php)**
```php
// Safe database connection dengan PDO
$pdo = new PDO(
    "mysql:host=localhost;dbname=mangrovegitour;charset=utf8mb4",
    'root', '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Helper functions untuk CRUD
- execute_query()      // Execute prepared statements
- fetch_one()          // Get single record
- fetch_all()          // Get multiple records
- insert_data()        // Insert new record
- update_data()        // Update existing record
- delete_data()        // Delete record
```

**Authentication (auth_helper.php)**
```php
// Session timeout configuration (1 hour)
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(['lifetime' => 3600]);

// Role-based access control
- is_logged_in()       // Check if user logged in
- has_role($role)      // Check user role
- require_login()      // Redirect if not logged in
- require_admin()      // Redirect if not admin
- logout_user()        // Clear session
```

**Payment Integration (midtrans.php)**
```php
// Midtrans SNAP configuration
- Server Key: SB-Mid-server-XXX (Sandbox)
- Client Key: SB-Client-XXX (Sandbox)
- Merchant ID: Set di Midtrans dashboard
- Payment methods: Credit card, E-wallet, Bank transfer

// Transaction flow:
1. User submit booking form
2. Backend create_transaction.php call Midtrans API
3. Midtrans return payment token & snap redirect URL
4. User pay via Midtrans SNAP
5. Midtrans send callback webhook
6. Backend update transaction & ticket status
```

---

### 2. **Database Implementation**

#### Schema Design
```sql
-- Visitors table
CREATE TABLE pengunjung (
    id_pengunjung INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    no_hp VARCHAR(20) UNIQUE,
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50),
    password VARCHAR(255),
    is_active TINYINT DEFAULT 1,
    terakhir_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets table
CREATE TABLE tiket (
    id_tiket INT PRIMARY KEY AUTO_INCREMENT,
    id_pengunjung INT,
    tanggal_berkunjung DATE,
    status ENUM('Active', 'Used', 'Expired'),
    harga DECIMAL(10, 2),
    created_at TIMESTAMP,
    FOREIGN KEY (id_pengunjung) REFERENCES pengunjung(id_pengunjung)
);

-- Reviews table
CREATE TABLE review (
    id_review INT PRIMARY KEY AUTO_INCREMENT,
    id_pengunjung INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    komentar TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (id_pengunjung) REFERENCES pengunjung(id_pengunjung)
);

-- Admin users table
CREATE TABLE user (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('Admin', 'Operator'),
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP
);

-- Transactions table (Midtrans)
CREATE TABLE transactions (
    id_transaction INT PRIMARY KEY AUTO_INCREMENT,
    id_tiket INT,
    order_id VARCHAR(100) UNIQUE,
    gross_amount INT,
    payment_type VARCHAR(50),
    transaction_status VARCHAR(50),
    fraud_status VARCHAR(50),
    response_code VARCHAR(10),
    created_at TIMESTAMP,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id_tiket)
);
```

#### Relationships
```
pengunjung (1) ──→ (N) tiket
pengunjung (1) ──→ (N) review
tiket      (1) ──→ (1) transactions
user       (1) ──→ (N) admin sessions
```

#### Indexing Strategy
```sql
-- Frequently searched columns
INDEX idx_email ON pengunjung(email);
INDEX idx_status ON tiket(status);
INDEX idx_order_id ON transactions(order_id);
INDEX idx_created ON transactions(created_at);
```

---

### 3. **Integrasi API**

#### Midtrans SNAP Payment Gateway

**Flow Diagram:**
```
User Booking
    ↓
Create Transaction (API)
    ↓
Midtrans API (get token)
    ↓
Display SNAP (Payment UI)
    ↓
User Pay
    ↓
Midtrans Webhook Callback
    ↓
Update Database (tiket status, transaction)
    ↓
Redirect Success Page
```

**API Endpoints:**

| Endpoint | Method | Fungsi | Auth Required |
|----------|--------|--------|---------------|
| `/backend/api/create_transaction.php` | POST | Create Midtrans transaction | No* |
| `/backend/api/midtrans_callback.php` | POST | Handle payment webhook | Signature |
| `/backend/api/tiket.php` | GET/POST/PUT | Manage tickets | No |
| `/backend/api/pengunjung.php` | GET/POST | Manage visitors | No |
| `/backend/api/review.php` | GET/POST | Manage reviews | No |
| `/backend/api/transaction_status.php` | GET | Check transaction status | No |
| `/backend/api/visitor-status.php` | GET | Check visitor session | No |

**Request/Response Examples:**

```json
// POST /backend/api/create_transaction.php
Request:
{
    "id_pengunjung": 5,
    "email": "visitor@example.com",
    "full_name": "Budi Santoso",
    "phone": "081234567890",
    "tanggal_berkunjung": "2025-12-20",
    "harga": 75000
}

Response (Success):
{
    "status": "success",
    "transaction_token": "eyJjbGllbnRfa2V5Ijoi...",
    "redirect_url": "https://app.midtrans.com/snap/v2/redirection/..."
}

// POST /backend/api/midtrans_callback.php
Webhook Payload (dari Midtrans):
{
    "transaction_time": "2025-12-14 10:30:45",
    "transaction_status": "settlement",
    "transaction_id": "0511101131271201",
    "status_message": "The transaction has been paid successfully",
    "gross_amount": "75000.00",
    "order_id": "ORDER-20251214-001",
    "payment_type": "credit_card",
    "signature_key": "522417e31c87c98f68dc98dc3c07bcd1e..."
}

// GET /backend/api/transaction_status.php?order_id=ORDER-20251214-001
Response:
{
    "order_id": "ORDER-20251214-001",
    "status": "settlement",
    "payment_type": "credit_card",
    "amount": 75000
}
```

---

### 4. **Pengujian (Testing)**

#### Test Cases yang Sudah Dilakukan

**Unit Testing (Manual):**
- ✅ Database connection & queries
- ✅ User registration & login
- ✅ Password hashing verification
- ✅ Session management
- ✅ Role-based access control

**Integration Testing:**
- ✅ Booking flow end-to-end
- ✅ Payment gateway integration
- ✅ Webhook callback handling
- ✅ Admin panel functions
- ✅ Report generation

**UI/UX Testing:**
- ✅ Form validation & error messages
- ✅ Responsive design (mobile & desktop)
- ✅ Button functionality & navigation
- ✅ Image loading & carousel
- ✅ Star rating interaction

**Security Testing:**
- ✅ SQL injection prevention (prepared statements)
- ✅ Password security (bcrypt hashing)
- ✅ Session security & timeout
- ✅ Authentication & authorization

#### Known Issues & Status

| Issue | Severity | Status | Notes |
|-------|----------|--------|-------|
| Webhook signature verification | CRITICAL | Pending | Can be added in next phase |
| API error handling | HIGH | Pending | Graceful error responses needed |
| Input sanitization | HIGH | Pending | htmlspecialchars() needed on output |
| Custom error pages | LOW | Pending | Can use default Apache errors |
| PDF/Excel export | LOW | Pending | Reports only viewed on screen |

(Lihat [docs/problem.md](docs/problem.md) untuk detail lengkap)

---

## 📊 Diagram Sistem

### System Architecture Diagram
```
┌─────────────────────────────────────────────────────────────┐
│                   CLIENT SIDE (Browser)                      │
├─────────────────────────────────────────────────────────────┤
│  index.html (Landing) │ booking.html │ review.html          │
│  + CSS (style.css)    │    + SNAP    │  + Rating Form       │
│  + JS (app.js, midtrans-payment.js)                         │
└──────────────────┬────────────────────────────────────────────┘
                   │ HTTP/HTTPS
┌──────────────────▼────────────────────────────────────────────┐
│                   SERVER SIDE (Apache + PHP)                  │
├──────────────────┬──────────────────────────────────────────┤
│                  │                                            │
│  ROUTING LAYER   │  API LAYER           │  VIEW LAYER       │
│  - /index.html   │  - /backend/api/     │  - /backend/views/│
│  - /booking.html │    * create_trans    │    * dashboard    │
│  - /review.html  │    * callback        │    * pengunjung   │
│                  │    * tiket.php       │    * tiket        │
│  AUTH LAYER      │    * review.php      │    * review       │
│  - /backend/auth/│    * pengunjung.php  │    * reports      │
│    * login.php   │                      │                   │
│    * logout.php  │  CONFIG LAYER        │                   │
│                  │  - database.php      │                   │
│                  │  - auth_helper.php   │                   │
│                  │  - midtrans.php      │                   │
└──────────────────┴──────────┬───────────────────────────────┘
                              │
                    ┌─────────▼─────────┐
                    │   MySQL Database  │
                    ├───────────────────┤
                    │ - pengunjung      │
                    │ - tiket           │
                    │ - review          │
                    │ - user            │
                    │ - transactions    │
                    └─────────────────────┘
                              │
                    ┌─────────▼─────────────────┐
                    │  External Services        │
                    ├───────────────────────────┤
                    │ Midtrans SNAP (Payment)   │
                    │ + Webhook Callbacks       │
                    └─────────────────────────────┘
```

### User Flow Diagram

**Pengunjung (Visitor) Flow:**
```
Landing Page
    ↓
[Register Account] → [Login]
    ↓                 ↓
View Booking → [Select Date & Ticket] → [Complete Booking]
                           ↓
                    [Payment via Midtrans]
                           ↓
                    [Receive Confirmation]
                           ↓
                    [Submit Review & Rating]
```

**Admin Flow:**
```
Login (Admin Credentials)
    ↓
Dashboard (Statistics)
    ↓
┌─────────────────┬──────────────────┬─────────────────┐
│  Manage Data    │  View Reports    │  Logout         │
├─────────────────┤──────────────────┤                 │
│ - Pengunjung    │ - Revenue Report │                 │
│ - Tiket         │ - Financial Info │                 │
│ - Review        │ - Statistics     │                 │
└─────────────────┴──────────────────┴─────────────────┘
```

---

## 👥 Panduan Pengguna

### Untuk Pengunjung

#### 1. Registrasi Akun Baru
```
1. Klik tombol "Book Your Ticket" atau pergi ke /booking.html
2. Klik "Create an account" jika belum punya akun
3. Isi form dengan:
   - Nama lengkap
   - No. HP (unik)
   - Email (unik)
   - Password (min 6 karakter)
4. Klik "Register"
5. Akun otomatis login, lanjut ke booking
```

#### 2. Login
```
1. Klik "Book Your Ticket"
2. Isi email & password
3. Klik "Sign In"
4. Berhasil login, redirect ke halaman booking
```

#### 3. Pesan Tiket
```
1. Pilih tanggal berkunjung
2. Pilih jenis tiket (Adult/Child/Family)
3. Review harga total
4. Klik "Complete Booking"
5. Akan redirect ke Midtrans payment page
```

#### 4. Pembayaran
```
1. Di halaman Midtrans:
   - Pilih metode pembayaran (kartu kredit, e-wallet, dll)
   - Isi data pembayaran (sandbox: gunakan test card)
   - Klik "Pay"
2. Tunggu konfirmasi
3. Akan redirect ke success page
4. Email konfirmasi dikirim
```

#### 5. Submit Review
```
1. Pergi ke halaman Review (/review.html)
2. Login terlebih dahulu
3. Isi form:
   - Pilih rating (1-5 bintang)
   - Tulis komentar/ulasan
4. Klik "Submit Review"
5. Review tampil di landing page
```

---

### Untuk Admin

#### 1. Login Admin
```
URL: /pweb-project/backend/auth/login.php
Username: admin
Password: admin123

(Note: Credentials disimpan di database table 'user')
```

#### 2. Dashboard
```
Admin dapat melihat:
- Total pengunjung
- Total tiket terjual
- Total tiket aktif
- Total review
- Total revenue
- Quick navigation ke halaman lain
```

#### 3. Manajemen Data
```
a) Pengunjung:
   - View semua data pengunjung (nama, no. HP, email)
   - Edit data pengunjung
   - Hapus pengunjung
   - Search & filter

b) Tiket:
   - View semua tiket yang terjual
   - Edit status tiket (Active/Used/Expired)
   - View detail transaksi
   
c) Review:
   - View semua review dari pengunjung
   - Lihat rating dan komentar
   - Filter by rating
```

#### 4. Laporan
```
a) Revenue Report:
   - Pilih periode (Daily/Weekly/Monthly)
   - Lihat total revenue per periode
   - Filter berdasarkan tanggal

b) Financial Report:
   - Lihat semua transaksi pembayaran
   - Status: Settlement/Pending/Failed
   - Payment method breakdown
   - Total settlement amount
```

#### 5. Logout
```
Klik tombol "Logout" di navbar
Session akan dihapus, redirect ke login page
```

---

## 🚀 Setup & Instalasi

### Prerequisites
- PHP 8.0+ (dengan ekstensi: mysqli, PDO, cURL, hash, json)
- Apache 2.4+ (dengan mod_rewrite)
- MySQL 8.0 / MariaDB 10.5+
- Composer (optional, jika menggunakan autoloader)

### Instalasi Lokal (Development)

#### 1. Setup Database
```bash
# Copy file database
cd database/
mysql -u root -p < schema.sql

# Atau manual:
# - Buka phpMyAdmin
# - Buat database baru: 'mangrovegitour'
# - Import file schema.sql
```

#### 2. Update Konfigurasi
```php
// Buka backend/config/database.php
// Sesuaikan dengan database lokal:
$host = 'localhost';
$user = 'root';
$password = ''; // atau password Anda
$dbname = 'mangrovegitour';
```

#### 3. Setup Midtrans (Sandbox)
```php
// Buka backend/config/midtrans.php
// Masukkan credential dari Midtrans Dashboard:
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-XXXX');
define('MIDTRANS_CLIENT_KEY', 'SB-Client-XXXX');
define('MIDTRANS_MERCHANT_ID', 'G1234567');
```

#### 4. Jalankan Server
```bash
# Jika menggunakan Laragon
# Server otomatis start, akses: http://localhost/pweb-project

# Atau manual (buat di htdocs/www):
# http://localhost/pweb-project/
```

#### 5. Test Login
```
Admin Login: http://localhost/pweb-project/backend/auth/login.php
Username: admin
Password: admin123

Visitor: http://localhost/pweb-project/public/index.html
```

---

### Instalasi di InfinityFree (Production)

#### Step 1: Push ke GitHub
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/USERNAME/mangrovegitour.git
git push -u origin main
```

#### Step 2: Setup di InfinityFree
```
1. Login ke InfinityFree
2. Create new account / domain
3. Go to File Manager
4. Upload code OR setup Git deployment
5. Point domain ke public folder
```

#### Step 3: Database Setup
```
1. Di InfinityFree, create MySQL database
2. Get credentials: hostname, user, password
3. Update backend/config/database.php dengan credentials baru
4. Import schema.sql via phpMyAdmin
```

#### Step 4: Verify Installation
```
- Akses domain Anda
- Test landing page
- Test admin login
- Test booking flow
```

---

## 📊 Struktur Database

### Entity-Relationship Diagram (ERD)
```
┌──────────────────┐
│   pengunjung     │
├──────────────────┤
│ id_pengunjung(PK)│
│ nama             │
│ no_hp            │◀─────────────┐
│ email            │              │
│ username         │              │ 1:N
│ password         │              │
│ is_active        │              │
│ terakhir_login   │              │
│ created_at       │              │
└──────────────────┘              │
         │                        │
         │ 1:N            ┌───────┴──────────┐
         │                │                  │
         │        ┌────────▼────────┐  ┌────▼──────────┐
         │        │     tiket       │  │    review     │
         │        ├─────────────────┤  ├───────────────┤
         │        │ id_tiket (PK)   │  │ id_review(PK) │
         │        │ id_pengunjung(FK)  │ id_pengunjung(FK)
         │        │ tanggal_berkunjung │ rating        │
         │        │ status          │  │ komentar      │
         │        │ harga           │  │ created_at    │
         │        │ created_at      │  └───────────────┘
         │        └────────┬────────┘
         │                 │ 1:1
         │        ┌────────▼──────────┐
         │        │  transactions     │
         │        ├───────────────────┤
         │        │ id_transaction(PK)│
         │        │ id_tiket (FK)     │
         │        │ order_id          │
         │        │ gross_amount      │
         │        │ payment_type      │
         │        │ transaction_status│
         │        │ fraud_status      │
         │        │ created_at        │
         │        └───────────────────┘
         │
         │
    ┌────▼──────────┐
    │     user      │
    ├───────────────┤
    │ id_user (PK)  │
    │ username      │
    │ password      │
    │ role          │
    │ is_active     │
    │ created_at    │
    └───────────────┘
```

### Queries Penting

```sql
-- Get visitor profile
SELECT * FROM pengunjung WHERE email = 'user@email.com';

-- Get all active tickets
SELECT * FROM tiket WHERE status = 'Active';

-- Get revenue by date
SELECT DATE(tiket.created_at) as tanggal, 
       COUNT(*) as jumlah_tiket,
       SUM(tiket.harga) as total_revenue
FROM tiket
WHERE status = 'Used'
GROUP BY DATE(tiket.created_at)
ORDER BY tanggal DESC;

-- Get payment transactions
SELECT * FROM transactions 
WHERE transaction_status = 'settlement'
ORDER BY created_at DESC;

-- Get average rating
SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
FROM review;
```

---

## 🔌 API Integration

### Midtrans SNAP Documentation
- **Official Docs**: https://docs.midtrans.com/
- **SNAP Client JS**: https://app.midtrans.com/snap/snap.js
- **Server Key Location**: Midtrans Dashboard → Merchant Settings
- **Webhook URL**: `https://yourdomain.com/backend/api/midtrans_callback.php`

### Integration Flow

**1. Frontend (midtrans-payment.js):**
```javascript
// Load Snap library
var script = document.createElement('script');
script.src = 'https://app.midtrans.com/snap/snap.js';
script.setAttribute('data-client-key', CLIENT_KEY);
document.body.appendChild(script);

// Trigger payment
snap.pay(token, {
    onSuccess: function(result) {
        // Handle success
        window.location.href = 'success.html';
    },
    onPending: function(result) {
        // Handle pending
        console.log('Payment pending');
    }
});
```

**2. Backend (create_transaction.php):**
```php
// Setup Midtrans Core
require_once 'vendor/autoload.php';
use Midtrans\Config;
use Midtrans\Snap;

Config::$serverKey = MIDTRANS_SERVER_KEY;
Config::$isProduction = false; // Sandbox

// Create transaction
$transaction_data = [
    'transaction_details' => [
        'order_id' => 'ORDER-' . time(),
        'gross_amount' => 75000
    ],
    'customer_details' => [
        'email' => 'customer@example.com',
        'phone' => '08123456789'
    ],
    'items' => [[
        'id' => 'tiket-001',
        'price' => 75000,
        'quantity' => 1,
        'name' => 'Tiket Mangrove'
    ]]
];

$snap_token = Snap::getSnapToken($transaction_data);
```

**3. Webhook Callback (midtrans_callback.php):**
```php
// Receive notification dari Midtrans
$json = file_get_contents('php://input');
$notification = json_decode($json);

// Verify signature (CRITICAL untuk security)
$server_key = MIDTRANS_SERVER_KEY;
$order_id = $notification->order_id;
$status_code = $notification->status_code;
$gross_amount = $notification->gross_amount;
$server_key = MIDTRANS_SERVER_KEY;

$input = $order_id . $status_code . $gross_amount . $server_key;
$signature = hash('sha512', $input);

if ($notification->signature_key !== $signature) {
    die('Signature verification failed');
}

// Update database
if ($notification->transaction_status == 'settlement') {
    // Mark as paid
    update_transaction($order_id, 'settlement');
    update_ticket_status($order_id, 'Active');
}
```

---

## ✅ Testing

### Manual Testing Checklist

```markdown
## Frontend Testing
- [ ] Landing page load tanpa error
- [ ] Navigation menu responsive
- [ ] Gallery carousel berfungsi
- [ ] Hero carousel auto-slide
- [ ] Review section display dengan baik
- [ ] Mobile responsive (test di berbagai ukuran)

## Visitor Flow
- [ ] Registrasi akun baru berhasil
- [ ] Login dengan akun yang baru dibuat
- [ ] Booking form dapat diisi dengan benar
- [ ] Validation error muncul jika data tidak valid
- [ ] Payment gateway Midtrans muncul
- [ ] Test payment dengan card sandbox Midtrans
- [ ] Webhook callback diproses (cek database)
- [ ] Review form dapat disubmit

## Admin Flow
- [ ] Admin login berhasil
- [ ] Dashboard menampilkan statistik benar
- [ ] Pengunjung dapat dilihat di daftar
- [ ] Tiket dapat diedit status
- [ ] Review dapat dilihat dengan rating
- [ ] Revenue report generate dengan benar
- [ ] Financial report menampilkan transaksi
- [ ] Admin logout berhasil

## Database
- [ ] Data terisi dengan benar di setiap table
- [ ] Foreign key relationship berfungsi
- [ ] Indexes meningkatkan query speed
- [ ] Timestamp otomatis terisi

## Security
- [ ] Password tersimpan dengan hash (bukan plaintext)
- [ ] Session tidak dapat diakses tanpa login
- [ ] SQL prepared statements digunakan
- [ ] Logout bersih menghapus session
```

---

## 🎬 Video Demonstrasi

### Panduan Membuat Video Demo (15-20 menit)

Lihat file [docs/guide/DEMO_GUIDE.md](docs/guide/DEMO_GUIDE.md) untuk:
- ✅ Struktur video & timing
- ✅ Script / talking points
- ✅ Tools yang diperlukan
- ✅ Checklist sebelum recording
- ✅ Cara upload ke YouTube
- ✅ Contoh narasi untuk setiap bagian

**Ringkas:**
```
0:00 - Intro (30 sec) - Perkenalan project, tujuan, teknologi
0:30 - Demo Landing Page (2 min) - Tampilkan semua section
2:30 - Demo Booking Flow (4 min) - Register → Book → Payment
6:30 - Demo Admin Panel (4 min) - Login → Dashboard → Reports
10:30 - Code Walkthrough (6 min) - Frontend, Backend, Database
16:30 - Kesimpulan (3 min) - Summary, learning outcomes
```

Link akan dikumpulkan ke README.md setelah recording selesai.

---

## 👥 Pembagian Jobdesk

### Struktur Tim (Kelompok 19)

| No. | Nama | NIM | Role | Jobdesk | Status |
|-----|------|-----|------|---------|--------|
| 1 | Royan Habibi Alfatih | 5025241115 | Project Lead & Backend Dev | - Backend architecture & API<br>- Database design & implementation<br>- Payment gateway integration (Midtrans)<br>- Admin dashboard & reports | ✅ Complete |
| 2 | Bara Semangat Rohmani | 5025241144 | Frontend Dev & UI/UX | - Landing page design<br>- Booking & review forms<br>- Color palette & CSS standardization<br>- Responsive design implementation<br>- JavaScript interactivity | ✅ Complete |
| Team | Kelompok 19 | - | Collective | - Database schema design<br>- Testing & quality assurance<br>- Documentation<br>- Deployment planning | ✅ Complete |

### Kontribusi Individual

**Royan Habibi Alfatih (5025241115):**
- ✅ Merancang & membuat database schema (5 tables, relationships, indexes)
- ✅ Implementasi backend API endpoints (7 endpoints)
- ✅ Authentication & session management system
- ✅ Midtrans SNAP payment integration
- ✅ Admin dashboard dengan statistics real-time
- ✅ Revenue & financial report generation
- ✅ Webhook callback handling
- ✅ Error handling & logging

**Bara Semangat Rohmani (5025241144):**
- ✅ Design landing page dengan hero section & gallery
- ✅ Implementasi responsive design (mobile-first)
- ✅ Booking form dengan validation
- ✅ Review & rating component dengan JavaScript interactivity
- ✅ Color palette standardization (CSS variables)
- ✅ Bootstrap 5 customization & component styling
- ✅ Image carousel (hero & gallery) dengan Bootstrap Carousel
- ✅ Visitor registration & login forms

**Kolaborasi Bersama:**
- ✅ Planning & requirement gathering
- ✅ Testing end-to-end (manual testing)
- ✅ Documentation & README writing
- ✅ GitHub version control & collaboration
- ✅ Problem analysis & troubleshooting
- ✅ Deployment strategy planning

### Evaluasi Kontribusi

| Aspek | Royan | Bara | Kolaborasi |
|-------|-------|------|------------|
| Code Quality | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Functionality | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Documentation | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Problem Solving | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Attendance | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | N/A |

---

## 🔗 Resources

### Dokumentasi Teknis
- [docs/problem.md](docs/problem.md) - Issue tracker & status
- [docs/guide/DEMO_GUIDE.md](docs/guide/DEMO_GUIDE.md) - Panduan video demonstrasi
- [docs/summary/ANALYSIS_REPORT.md](docs/summary/ANALYSIS_REPORT.md) - Analisis teknis lengkap
- [database/schema.sql](database/schema.sql) - Database schema

### External References
- **Midtrans Docs**: https://docs.midtrans.com/
- **Bootstrap 5 Docs**: https://getbootstrap.com/docs/5.3/
- **PHP PDO Tutorial**: https://www.php.net/manual/en/class.pdo.php
- **HTTP Status Codes**: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status

### Source Code Repository
```
GitHub: https://github.com/USERNAME/mangrovegitour
Main Branch: main
Branch Strategy: 
  - main (production-ready)
  - develop (development)
  - feature/* (feature branches)
```

---

## 📄 Project Info

- **Project Name**: MangroveTour
- **Institution**: Institut Teknologi Sepuluh Nopember (ITS)
- **Course**: EF234301 Pemrograman Web
- **Semester**: Ganjil 2025/2026
- **Class**: A
- **Group**: 19
- **Period**: 24 November - 14 Desember 2025
- **Lecturer**: Fajar Baskoro, S.Kom., M.T.

### License
Public Domain (CC0) - Free to use, modify, distribute without attribution.

### Contact & Support
For technical issues or questions:
- 📧 Email: [group email if needed]
- 💬 Discussion: Check GitHub Issues
- 📋 Documentation: See docs/ folder

---

**Last Updated**: 14 Desember 2025  
**Status**: ✅ Production Ready for Demo  
**Next Phase**: Deployment & Maintenance  

---
