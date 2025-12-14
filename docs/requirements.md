# 🌱 **MangroveTour — Full-Stack Website Project**

MangroveTour is a full-stack web-based management system for ecotourism at **Mangrove Wonorejo**.
The system streamlines ticket booking, visitor management, visit schedules, ratings & reviews, and revenue reporting — providing an easy digital experience for visitors and efficient operations for administrators and operators.

Built with:

* **Native PHP 8**
* **Native HTML + Bootstrap 5**
* **MySQL / MariaDB**
* Optimized for **GitHub Copilot in VS Code**

---

## 🚀 **1. Project Scope**

### **Front-end**

* Native **HTML5, CSS3, JavaScript**
* **Bootstrap 5 only** for layout & UI components
* No SPA frameworks (React, Vue, Svelte)

### **Back-end**

* **Native PHP 8.x**
* Apache server (XAMPP / Laragon / cPanel)
* Optional JSON API endpoints
* Session-based authentication system
* **Payment gateway integration: Midtrans SNAP (Sandbox mode)**
  - Online ticket payment processing
  - Multiple payment methods support
  - Real-time payment notifications

### **Database**

* MySQL or MariaDB
* Managed using phpMyAdmin

### **Deployment**

* **Front-end:** GitHub Pages
* **Back-end:** Shared Hosting (cPanel with PHP + MySQL)

---

## 📌 **2. Project Goals**

* Simplify online ticket booking
* Provide complete tourism site information
* Manage visitor data and ticket status
* Generate revenue reports (daily, weekly, monthly)
* Enable rating & review submissions
* Provide admin dashboard with visual analytics

---

## ⭐ **3. Main Features**


### **Landing Page**

* General information about Mangrove Wonorejo
* Photo/video gallery
* Articles / updates
* Ticket booking button
* Review submission access

### **Ticket Management**

* Ticket booking form
* **Integrated Midtrans SNAP payment gateway**
* Ticket status: **Active**, **Used**, **Expired**
* Automatic ticket validation after successful payment

### **🆕 MIDTRANS PAYMENT INTEGRATION**

* **Online Payment Processing:** Secure payment via Midtrans SNAP gateway
* **Multiple Payment Methods:** Bank Transfer, Credit Card, E-Wallet (GoPay, OVO, DANA), QRIS, Virtual Account
* **Sandbox Testing:** Full Sandbox environment for development and testing
* **Payment Tracking:** Real-time payment status updates via webhooks
* **Transaction Management:** Complete transaction history and financial reporting
* **Financial Dashboard:** Separate Financial Report showing all payment transactions
* **Revenue Dashboard:** Separate Revenue Report showing validated ticket usage
* **Automatic Updates:** Ticket status automatically updates after successful payment
### **Visitor Management (Admin)**

* Create / Read / Update / Delete visitors
* Assign ticket to visitor
* Visit date management

### **Review System**

* Visitor rating & comment submission
* Display reviews publicly or in admin dashboard

### **Revenue Reports** (Two Comprehensive Reports)

**Revenue Report:**
* Daily / Weekly / Monthly ticket sales summaries
* Tracks completed/validated visits
* Operational performance metrics

**Financial Report:**
* Payment transaction tracking from Midtrans
* Payment method breakdown analysis
* Transaction status monitoring (settled, pending, failed)
* Printable financial reports for accounting

### **Authentication**

* Login, Logout
* Role-based access: Admin, Operator
* Session-based authorization guard

### **Admin Dashboard**

* Visitor analytics
* Ticket statistics
* Revenue charts (Chart.js / Google Charts)

### **Operator Tools**

* Ticket validation at gate
* Mark ticket as Used

---

## 👥 **4. User Roles**

| Role         | Description                                     |
| ------------ | ----------------------------------------------- |
| **Admin**    | Full access to all data & modules               |
| **Operator** | Ticket validation & basic operations            |
| **Visitor**  | View landing page, book tickets, submit reviews |

---

## 🛠️ **5. Technical Specifications**

### **Front-End**

* HTML5, CSS3
* JavaScript (native)
* Bootstrap 5

### **Back-End**

* Native PHP 8.x
* PDO / MySQLi with prepared statements
* Session & cookie handling
* Optional REST API for front-end or external integration

### **Database Structure (Main Tables)**

| Table          | Columns                                                     |
| -------------- | ----------------------------------------------------------- |
| **pengunjung** | id_pengunjung, nama, no_hp, email                           |
| **tiket**      | id_tiket, id_pengunjung, tanggal_berkunjung, status         |
| **review**     | id_review, id_pengunjung, rating, komentar, tanggal         |
| **user**       | id_user, username, password (hashed), role (Admin/Operator) |

---

## 🎨 **7. UI/UX Design Requirements**

### **Visitor Pages**

* Landing page (information, gallery, CTA buttons)
* Ticket booking form
* Review submission page
* Ticket status lookup

### **Admin Pages**

* Dashboard (statistics & charts)
* Visitor management CRUD
* Ticket management
* Review moderation
* Revenue reports

### **Operator Pages**

* Ticket validation interface

---

## 🔁 **8. System Flow**

1. Visitor opens landing page
2. Visitor books a ticket
3. System stores ticket → default status **Active**
4. Operator validates ticket during onsite visit
5. Visitor submits review afterward
6. Admin checks revenue, statistics, and visitor data

---

## 🔌 **9. Optional API Endpoints**

| Method | Endpoint          | Description          |
| ------ | ----------------- | -------------------- |
| GET    | `/api/pengunjung` | List all visitors    |
| POST   | `/api/tiket`      | Create new ticket    |
| GET    | `/api/tiket/{id}` | Get ticket details   |
| PUT    | `/api/tiket/{id}` | Update ticket status |
| GET    | `/api/review`     | Retrieve all reviews |

---

## 🔒 **10. Security & Validation**

* Client-side form validation (JS)
* Server-side validation (PHP)
* Password hashing using `password_hash()`
* Prevent SQL Injection via prepared statements
* Session-based access control
* Role-based restrictions (Admin/Operator)
* CSRF protection (optional)

---

## 🚧 **11. Future Enhancements (Version 2.0)**

* WhatsApp notifications for booking
* QR Code for ticket validation
* Payment gateway integration (Midtrans / DOKU)
* Advanced analytics with Chart.js
* Google Maps integration for tourism route

---

## 📦 **12. Recommended Project Structure**

```
/pweb-project
│── /public
│   ├── index.html
│   ├── booking.html
│   ├── review.html
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│
│── /backend
│   ├── config/
│   │   └── database.php
│   ├── models/
│   ├── controllers/
│   ├── views/
│   ├── api/ (optional)
│   └── auth/
│       ├── login.php
│       └── logout.php
│
│── /database
│   └── schema.sql
│
│── /docs
│   └── requirements.md
│
├── project-specifications.md
└── README.md
```