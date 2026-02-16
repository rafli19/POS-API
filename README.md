# diJajanin POS - Point of Sale System

Sistem Point of Sale (POS) modern yang dibangun dengan React dan Laravel 12. Aplikasi ini menyediakan manajemen lengkap untuk toko/restoran dengan fitur role-based access control.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![React](https://img.shields.io/badge/React-18.x-blue)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-cyan)

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Screenshots](#-screenshots)
- [Instalasi](#-instalasi)
- [Struktur Project](#-struktur-project)
- [API Documentation](#-api-documentation)
- [Role & Permissions](#-role--permissions)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Fitur Utama

### Backend (Laravel 12 API)
- 🔐 **Authentication & Authorization** dengan Laravel Sanctum
- 👥 **Role-based Access Control** (Admin, Owner, Kasir)
- 📦 **Manajemen Produk** dengan kategori, stok, dan gambar
- 💰 **Sistem Transaksi** lengkap dengan multiple payment methods
- 📊 **Dashboard Analytics** real-time
- 📈 **Laporan Penjualan** (daily, monthly, kasir performance)
- 🔔 **Notifikasi** untuk stok menipis
- 🎨 **RESTful API** dengan response format konsisten

### Frontend (React + Vite)
- ⚡ **Fast & Responsive** dengan Vite
- 🎨 **Modern UI** dengan Tailwind CSS
- 📱 **Mobile-First Design** - responsive di semua device
- 🔄 **Real-time Updates** setiap 10 detik
- 🖼️ **Image Management** dengan lazy loading
- 🔍 **Search & Filter** produk
- 📄 **Pagination** untuk performa optimal
- 🎯 **Role-based UI** - tampilan sesuai user role

## 🛠 Tech Stack

### Backend
- **Framework:** Laravel 12
- **Database:** MySQL 8.0+ / MariaDB 10.11+
- **Authentication:** Laravel Sanctum
- **API:** RESTful standards
- **PHP Version:** 8.2+

### Frontend
- **Framework:** React 18.x
- **Build Tool:** Vite 5.x
- **Styling:** Tailwind CSS 3.x
- **Routing:** React Router DOM 6.x
- **HTTP Client:** Axios
- **Icons:** Lucide React
- **State Management:** React Context API

## 📸 Screenshots

### Dashboard
![Dashboard](screenshots/dashboard.png)
*Dashboard dengan real-time statistics dan recent transactions*

### Products Management
![Products](screenshots/products.png)
*Manajemen produk dengan search, filter, dan pagination*

### Transaction
![Transaction](screenshots/transaction.png)
*Interface transaksi untuk kasir*

## 🚀 Instalasi

### Prerequisites
- Node.js 18+ dan npm/yarn
- PHP 8.2+
- Composer
- MySQL 8.0+ atau MariaDB 10.11+
- Git

### Backend Setup

```bash
# 1. Clone repository
git clone https://github.com/rafli19/POS-API.git
cd POS-API

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_database
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations & seeders
php artisan migrate --seed

# 7. Create storage link
php artisan storage:link

# 8. Set permissions
chmod -R 775 storage bootstrap/cache

# 9. Start development server
php artisan serve
```

Backend akan jalan di: `http://localhost:8000`

### Frontend Setup

```bash
# 1. Clone repository
git clone https://github.com/yourusername/dijajanin-pos-frontend.git
cd dijajanin-pos-frontend

# 2. Install dependencies
npm install
# atau
yarn install

# 3. Copy environment file
cp .env.example .env

# 4. Configure API URL di .env
VITE_API_BASE_URL=http://localhost:8000/api/v1
VITE_PUBLIC_URL=http://localhost:8000

# 5. Start development server
npm run dev
# atau
yarn dev
```

Frontend akan jalan di: `http://localhost:5173`

### Default Login Credentials

Setelah seeding, gunakan credentials berikut:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Owner | owner@example.com | password |
| Kasir | kasir@example.com | password |

## 📁 Struktur Project

### Backend (Laravel)

```
POS-API/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── ProductController.php
│   │   │       ├── TransactionController.php
│   │   │       ├── ReportController.php
│   │   │       └── DashboardController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Category.php
│       ├── Product.php
│       ├── Transaction.php
│       ├── TransactionDetail.php
│       └── PaymentMethod.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── storage/
    └── app/
        └── public/
```

### Frontend (React)

```
dijajanin-pos-frontend/
├── src/
│   ├── components/
│   │   └── Layout.jsx
│   ├── context/
│   │   └── AuthContext.jsx
│   ├── pages/
│   │   ├── Login.jsx
│   │   ├── Dashboard.jsx
│   │   ├── Products.jsx
│   │   ├── Categories.jsx
│   │   └── Transaction.jsx
│   ├── services/
│   │   └── api.js
│   ├── App.jsx
│   └── main.jsx
├── public/
├── .env
└── package.json
```

## 📡 API Documentation

Base URL: `http://your-domain.com/api/v1`

### Authentication Endpoints

```http
POST /register
POST /login
POST /logout
GET  /me
```

### Products Endpoints

```http
GET    /products              # List produk (All roles)
GET    /products/{id}         # Detail produk (All roles)
POST   /products              # Create produk (Admin only)
PUT    /products/{id}         # Update produk (Admin only)
DELETE /products/{id}         # Delete produk (Admin only)
GET    /products/low-stock    # Low stock products (All roles)
```

### Categories Endpoints

```http
GET    /categories            # List kategori (Admin & Owner only)
GET    /categories/{id}       # Detail kategori (Admin & Owner only)
POST   /categories            # Create kategori (Admin only)
PUT    /categories/{id}       # Update kategori (Admin only)
DELETE /categories/{id}       # Delete kategori (Admin only)
```

### Transactions Endpoints

```http
GET    /transactions                    # List transaksi (Admin & Owner only)
GET    /transactions/{id}               # Detail transaksi (Admin & Owner only)
POST   /transactions                    # Create transaksi (Kasir only)
PUT    /transactions/{id}               # Update transaksi (Admin only)
DELETE /transactions/{id}               # Delete transaksi (Admin only)
POST   /transactions/{id}/confirm       # Confirm payment (Kasir only)
POST   /transactions/{id}/cancel        # Cancel payment (Kasir only)
GET    /transactions/statistics         # Statistics (Admin & Owner only)
GET    /transactions/daily-report       # Daily report (Admin & Owner only)
```

### Dashboard Endpoints

```http
GET /dashboard/summary         # Dashboard summary
GET /dashboard/notifications   # Notifications
GET /dashboard/chart-data      # Chart data
GET /dashboard/quick-stats     # Quick statistics
```

### Reports Endpoints (Admin & Owner only)

```http
GET /reports/sales          # Sales report
GET /reports/stock          # Stock report
GET /reports/transactions   # Transaction report
GET /reports/kasir          # Kasir performance report
```

### Request Example

```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Get Products (with token)
curl http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Response Format

Success:
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    // ... data here
  }
}
```

Error:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

## 👥 Role & Permissions

### Permission Matrix

| Feature | Admin | Owner | Kasir |
|---------|:-----:|:-----:|:-----:|
| **Dashboard** | ✅ | ✅ | ✅ |
| **Products - View** | ✅ | ✅ | ✅ |
| **Products - CRUD** | ✅ | ❌ | ❌ |
| **Categories - View** | ✅ | ✅ | ❌ |
| **Categories - CRUD** | ✅ | ❌ | ❌ |
| **Create Transaction** | ❌ | ❌ | ✅ |
| **Transaction History** | ✅ | ✅ | ❌ |
| **Verifikasi Pembayaran** | ❌ | ❌ | ✅ |
| **Reports** | ✅ | ✅ | ❌ |

### Detailed Permissions by Role

#### 🔴 Admin
**Full management access untuk data master, lihat history, tapi tidak buat transaksi**

- ✅ Dashboard (view statistics)
- ✅ Products - Full CRUD
- ✅ Categories - Full CRUD  
- ✅ Transaction History - View & filter
- ✅ Reports - All reports
- ❌ Create Transaction (only kasir)
- ❌ Verifikasi Pembayaran (only kasir)

#### 🟡 Owner  
**Read-only untuk monitoring & business intelligence**

- ✅ Dashboard (view statistics)
- ✅ Products - Read only
- ✅ Transaction History - View & filter
- ✅ Reports - All reports
- ❌ Products/Categories CRUD
- ❌ Create Transaction
- ❌ Verifikasi Pembayaran

#### 🟢 Kasir
**Operasional transaksi & verifikasi pembayaran**

- ✅ Dashboard (view statistics)
- ✅ Products - Read only (untuk transaksi)
- ✅ Create Transaction
- ✅ Verifikasi Pembayaran (confirm/cancel)
- ❌ Transaction History
- ❌ Reports
- ❌ Products/Categories CRUD

## 🚀 Deployment

### Backend Deployment (Shared Hosting / cPanel)

```bash
# 1. Upload files ke server
# Upload semua file ke: /home/username/domain.com/

# 2. Set document root ke folder public
# Di cPanel → Domains → domain.com
# Document Root: /home/username/domain.com/public

# 3. Configure .env untuk production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pos-api.yourdomain.com

# 4. Set permissions
chmod -R 755 storage bootstrap/cache

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Run migrations
php artisan migrate --force
```

### Frontend Deployment (Vercel / Netlify)

```bash
# 1. Build for production
npm run build

# 2. Preview build
npm run preview

# 3. Deploy ke Vercel
npm install -g vercel
vercel --prod

# Atau deploy ke Netlify
# Drop folder 'dist' ke Netlify dashboard
```

### Environment Variables (Production)

**Backend (.env):**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pos-api.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=database
```

**Frontend (.env):**
```env
VITE_API_BASE_URL=https://pos-api.yourdomain.com/api/v1
VITE_PUBLIC_URL=https://pos-api.yourdomain.com
```

## 🐛 Troubleshooting

### CORS Error
**Problem:** Frontend tidak bisa akses backend API

**Solution:**
```php
// Backend: .env
CORS_ALLOWED_ORIGINS=https://yourfrontend.com,http://localhost:5173

// Lalu:
php artisan config:clear
php artisan config:cache
```

### 403 Forbidden on Categories
**Problem:** Kasir tidak bisa akses categories endpoint

**Solution:** Pastikan routes sudah benar di `routes/api.php`:
```php
Route::middleware('role:admin,owner,kasir')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
});
```

### Images Not Showing
**Problem:** Gambar produk tidak muncul

**Solution:**
```bash
# Backend
php artisan storage:link
chmod -R 755 storage/app/public
```

### Lag on Products Page
**Problem:** Halaman products lambat

**Solution:**
- Backend: Pastikan pagination aktif (max 20 items)
- Frontend: Gunakan lazy loading untuk gambar
- Optimize gambar sebelum upload (max 2MB)

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.0.0 (2026-02-17)
- ✅ Initial release
- ✅ Authentication & Authorization
- ✅ Product & Category Management
- ✅ Transaction Processing
- ✅ Dashboard Analytics
- ✅ Reports Generation
- ✅ Role-based Access Control

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Developer

**Rafael Void**
- Email: rafael@rafvoid.my.id
- GitHub: [@rafli19](https://github.com/rafli19)
- Website: https://rafvoid.my.id

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - The PHP Framework
- [React](https://react.dev/) - JavaScript Library
- [Tailwind CSS](https://tailwindcss.com/) - CSS Framework
- [Vite](https://vitejs.dev/) - Build Tool
- [Lucide Icons](https://lucide.dev/) - Beautiful Icons

---

**Made with ❤️ by Rafael Void**

*Last Updated: February 2026*
