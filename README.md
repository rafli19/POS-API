# 🛒 POS System (Point of Sale)

Sistem Point of Sale (POS) berbasis Web yang dibuat menggunakan Laravel (Backend API) dan React (Frontend).  
Project ini mendukung manajemen produk, kategori, transaksi, laporan, dan sistem role (Owner, Admin, Kasir).

---

## 🚀 Fitur Utama

### 👤 Role Based Access Control

Terdapat 3 role dalam sistem:

- **Owner**
    - Dashboard (Read)
    - Reports (Read)
    - Transaction History (Read)
    - Products (Read)

- **Admin**
    - Dashboard (Read)
    - Products (CRUD)
    - Categories (CRUD)
    - Reports (Read)
    - Transaction History (Read)

- **Kasir**
    - Dashboard (Read)
    - Products (Read)
    - Create Transaction
    - Update Status Pembayaran

---

## Login Sistem

    - owner@mail.com
    - owner123

    - admin@mail.com
    - admin123

    - kasir@mail.com
    - kasir123

## 📦 Fitur Sistem

- ✅ Authentication (Login / Logout)
- ✅ Dashboard Statistik
- ✅ Manajemen Produk
- ✅ Manajemen Kategori
- ✅ Sistem Transaksi
- ✅ Multiple Payment Method
- ✅ Laporan Penjualan
- ✅ Transaction History
- ✅ Stock Management
- ✅ Status Transaksi (Pending, Completed, Cancelled)

---

## 🏗️ Tech Stack

### Backend

- Laravel
- MySQL
- REST API
- Sanctum / JWT (jika digunakan)

### Frontend

- React
- React Router
- Axios
- Tailwind / CSS
- Lucide Icons

---

## 🗄️ Database Structure

### Tables:

- users
- categories
- products
- transactions
- transaction_details
- payment_methods

### Relasi Utama:

- User (1) → (N) Transactions
- Category (1) → (N) Products
- Transaction (1) → (N) Transaction Details
- Product (1) → (N) Transaction Details
- Payment Method (1) → (N) Transactions

---

## 🧑‍💻 Installation Guide

### 1️⃣ Clone Repository

```bash
git clone https://github.com/username/pos-system.git
```
