# diJajanin POS - Backend API

RESTful API untuk sistem Point of Sale (POS) yang dibangun dengan Laravel 12. API ini menyediakan authentication, manajemen produk, transaksi, dan reporting dengan role-based access control.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/license-MIT-green)

## 🔗 Links

- **Frontend Repository:** [dijajanin-pos-frontend](https://github.com/yourusername/dijajanin-pos-frontend)
- **Live Demo:** https://pos.rafvoid.my.id
- **API Documentation:** https://pos-api.rafvoid.my.id/api/documentation

## ✨ Fitur Utama

- 🔐 **Authentication & Authorization** dengan Laravel Sanctum
- 👥 **Role-based Access Control** (Admin, Owner, Kasir)
- 📦 **Product Management** dengan kategori, stok, dan image upload
- 💰 **Transaction Processing** dengan multiple payment methods
- 📊 **Real-time Dashboard** dengan statistics
- 📈 **Reports Generation** (Sales, Stock, Kasir Performance)
- 🔔 **Low Stock Notifications**
- 🎨 **RESTful API** dengan consistent response format
- 📱 **CORS Support** untuk frontend integration

## 🛠 Tech Stack

- **Framework:** Laravel 12
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0+ / MariaDB 10.11+
- **Authentication:** Laravel Sanctum (Token-based)
- **Storage:** Local filesystem / S3-compatible
- **Cache:** File / Redis
- **Queue:** Database / Redis

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 atau MariaDB >= 10.11
- Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/rafli19/POS-API.git
cd POS-API
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_database
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Run seeders (creates default users & sample data)
php artisan db:seed
```

### 6. Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### 7. Start Development Server

```bash
php artisan serve
```

API akan berjalan di: `http://localhost:8000`

## 🔑 Default Login Credentials

Setelah running seeders:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@example.com | password |
| **Owner** | owner@example.com | password |
| **Kasir** | kasir@example.com | password |

## 📡 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Authentication

```http
POST   /register              # Register new user
POST   /login                 # Login user
POST   /logout                # Logout user
GET    /me                    # Get current user
```

### Products

```http
GET    /products              # List products (All authenticated)
GET    /products/{id}         # Get product detail
POST   /products              # Create product (Admin only)
PUT    /products/{id}         # Update product (Admin only)
DELETE /products/{id}         # Delete product (Admin only)
GET    /products/low-stock    # Get low stock products
GET    /products/statistics   # Get product statistics
```

### Categories

```http
GET    /categories            # List categories (Admin & Owner)
GET    /categories/{id}       # Get category detail
POST   /categories            # Create category (Admin only)
PUT    /categories/{id}       # Update category (Admin only)
DELETE /categories/{id}       # Delete category (Admin only)
```

### Transactions

```http
GET    /transactions                  # List transactions (Admin & Owner)
GET    /transactions/{id}             # Get transaction detail
POST   /transactions                  # Create transaction (Kasir only)
PUT    /transactions/{id}             # Update transaction (Admin only)
DELETE /transactions/{id}             # Delete transaction (Admin only)
POST   /transactions/{id}/confirm     # Confirm payment (Kasir only)
POST   /transactions/{id}/cancel      # Cancel payment (Kasir only)
GET    /transactions/statistics       # Get statistics
GET    /transactions/daily-report     # Get daily report
```

### Dashboard

```http
GET    /dashboard/summary         # Get dashboard summary
GET    /dashboard/notifications   # Get notifications
GET    /dashboard/chart-data      # Get chart data
GET    /dashboard/quick-stats     # Get quick statistics
```

### Reports (Admin & Owner only)

```http
GET    /reports/sales          # Sales report
GET    /reports/stock          # Stock report
GET    /reports/transactions   # Transaction report
GET    /reports/kasir          # Kasir performance report
```

### Payment Methods

```http
GET    /payment-methods        # Get active payment methods
```

## 📝 API Request Examples

### Login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "role": "admin"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

### Get Products (Authenticated)

```bash
curl http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Create Product (Admin only)

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "category_id=1" \
  -F "name=Nasi Goreng" \
  -F "sku=FOOD-001" \
  -F "price=25000" \
  -F "stock=100" \
  -F "min_stock=10" \
  -F "image=@/path/to/image.jpg"
```

## 🔐 Role & Permissions

### Admin
- ✅ Dashboard
- ✅ Products (Full CRUD)
- ✅ Categories (Full CRUD)
- ✅ Transaction History (View only)
- ✅ Reports (All)
- ❌ Create Transaction

### Owner
- ✅ Dashboard
- ✅ Products (Read only)
- ✅ Transaction History (View only)
- ✅ Reports (All)
- ❌ Products/Categories CRUD
- ❌ Create Transaction

### Kasir
- ✅ Dashboard
- ✅ Products (Read only)
- ✅ Create Transaction
- ✅ Verify Payment
- ❌ Transaction History
- ❌ Reports
- ❌ Products/Categories CRUD

## 📁 Project Structure

```
POS-API/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── DashboardController.php
│   │   │       ├── ProductController.php
│   │   │       ├── ReportController.php
│   │   │       └── TransactionController.php
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
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── storage/
│   └── app/
│       └── public/
└── .env.example
```

## 🚀 Deployment

### Production Server (cPanel / Shared Hosting)

#### 1. Upload Files
```bash
# Upload semua file ke server
# Path: /home/username/pos-api.domain.com/
```

#### 2. Set Document Root
```
Document Root: /home/username/pos-api.domain.com/public
```

#### 3. Configure Environment
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pos-api.yourdomain.com

DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=database

CORS_ALLOWED_ORIGINS=https://pos.yourdomain.com
```

#### 4. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
```

#### 5. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### 6. Run Migrations
```bash
php artisan migrate --force
```

### VPS / Dedicated Server

Lengkapnya bisa lihat di [DEPLOYMENT.md](DEPLOYMENT.md)

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ProductTest

# With coverage
php artisan test --coverage
```

## 🐛 Troubleshooting

### Error: "SQLSTATE[HY000] [2002]"
```bash
# Cek koneksi database
php artisan tinker
>>> DB::connection()->getPdo();
```

### Error: "Route not found"
```bash
php artisan route:clear
php artisan route:cache
```

### Error: "Class not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error: Permission denied on storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 📊 Database Schema

### Users Table
- id, name, email, password, role, is_active

### Categories Table
- id, name, description, image

### Products Table
- id, category_id, name, sku, description, price, stock, min_stock, image, is_active

### Transactions Table
- id, user_id, transaction_code, customer_name, total_amount, payment_method_id, amount_paid, change_amount, status, transaction_date

### Transaction Details Table
- id, transaction_id, product_id, quantity, price, subtotal

## 🔄 API Response Format

### Success Response
```json
{
  "success": true,
  "message": "Success message",
  "data": {
    // ... response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": {
    "data": [...],
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

## 📚 Additional Documentation

- [API Documentation](docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Database Schema](docs/DATABASE.md)

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Developer

**Rafael Void**
- Email: rafael@rafvoid.my.id
- GitHub: [@rafli19](https://github.com/rafli19)
- Website: https://rafvoid.my.id

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [MySQL](https://www.mysql.com/)

---

**Made with ❤️ by Rafael Void**

*Last Updated: February 2026*
