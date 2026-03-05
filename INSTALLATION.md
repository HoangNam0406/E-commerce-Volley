# E-Commerce Volley - Installation & Setup Guide

## 📋 Yêu cầu hệ thống

- **PHP:** 7.4 hoặc cao hơn
- **MySQL:** 5.7 hoặc cao hơn
- **Apache:** (XAMPP)
- **Extensions:** PDO, PDO_MySQL, GD (optional)
- **Browser:** Modern browser (Chrome, Firefox, Safari, Edge)

## 🚀 Hướng dẫn cài đặt

### Bước 1: Chuẩn bị XAMPP

1. **Tải XAMPP** từ https://www.apachefriends.org
2. **Cài đặt** XAMPP trên máy tính
3. **Khởi động** Apache & MySQL từ XAMPP Control Panel

### Bước 2: Copy dự án

1. Dự án đã được đặt tại: `c:\xampp\htdocs\E-commerce Volley\`
2. Đảm bảo cấu trúc thư mục đầy đủ (như mô tả trong `README.md`)

### Bước 3: Tạo Database

1. **Mở phpMyAdmin:**
   - Truy cập: http://localhost/phpmyadmin
   - Default username: `root`
   - Default password: (để trống)

2. **Tạo Database:**
   - Nhấp "New" trên thanh menu trái
   - Database name: `ecommerce_volley`
   - Charset: `utf8mb4`
   - Collation: `utf8mb4_unicode_ci`
   - Nhấp "Create"

3. **Import SQL:**
   - Chọn database `ecommerce_volley`
   - Đi tới tab "Import"
   - Chọn file: `config/database.sql`
   - Nhấp "Import"
   - Kiểm tra không có lỗi

### Bước 4: Cấu hình ứng dụng

1. **Mở file:** `config/config.php`

2. **Kiểm tra cài đặt Database:**
   ```php
   define('DB_HOST', 'localhost');       // ✓ Đúng
   define('DB_USER', 'root');            // ✓ Đúng
   define('DB_PASS', '');                // ✓ Đúng (không có password)
   define('DB_NAME', 'ecommerce_volley');// ✓ Đúng
   ```

3. **Cấu hình VNPay** (nếu muốn sử dụng)
   ```php
   define('VNPAY_TMNCODE', 'YOUR_TMN_CODE');
   define('VNPAY_HASHSECRET', 'YOUR_HASH_SECRET');
   ```

4. **Cấu hình đường dẫn:**
   ```php
   define('APP_URL', 'http://localhost/E-commerce%20Volley');
   ```

### Bước 5: Chạy ứng dụng

1. **Mở trình duyệt:**
   - Truy cập: http://localhost/E-commerce%20Volley

2. **Trang Home sẽ hiển thị:**
   - Navigation header
   - Product categories
   - Featured products
   - Product listings

### Bước 6: Kiểm tra Module

#### 🔐 Authentication System
```
✓ Login: http://localhost/E-commerce%20Volley/login
✓ Register: http://localhost/E-commerce%20Volley/register
✓ Logout: Click logout in header

Default Account:
- Email: admin@gmail.com
- Password: admin123
- Role: Admin
```

#### 🏠 Customer Features
```
✓ Home page: http://localhost/E-commerce%20Volley/
✓ Product listing: http://localhost/E-commerce%20Volley/products
✓ Product detail: http://localhost/E-commerce%20Volley/product/1
✓ Search: http://localhost/E-commerce%20Volley/search?q=keyword
✓ Categories: http://localhost/E-commerce%20Volley/categories
✓ Cart: http://localhost/E-commerce%20Volley/cart
✓ Checkout: http://localhost/E-commerce%20Volley/order-checkout
✓ Orders: http://localhost/E-commerce%20Volley/orders
✓ Account: http://localhost/E-commerce%20Volley/account-profile
✓ Wishlist: http://localhost/E-commerce%20Volley/account-wishlist
✓ Wallet: http://localhost/E-commerce%20Volley/account-wallet
```

#### 👔 Seller Features
```
✓ Seller dashboard: http://localhost/E-commerce%20Volley/seller-dashboard
✓ Products: http://localhost/E-commerce%20Volley/seller-products
✓ Add product: http://localhost/E-commerce%20Volley/seller-product-add
✓ Orders: http://localhost/E-commerce%20Volley/seller-orders
✓ Wallet: http://localhost/E-commerce%20Volley/account-wallet
```

#### 🔧 Admin Features
```
✓ Admin dashboard: http://localhost/E-commerce%20Volley/admin-dashboard
✓ Users: http://localhost/E-commerce%20Volley/admin-users
✓ Categories: http://localhost/E-commerce%20Volley/admin-categories
✓ Banners: http://localhost/E-commerce%20Volley/admin-banners
```

## 🧪 Kiểm tra nhanh

### 1. Đăng nhập Admin
```
1. Truy cập: http://localhost/E-commerce%20Volley/login
2. Email: admin@gmail.com
3. Password: admin123
4. Click "Login"
```

### 2. Tạo Sample Data
```
Admin Dashboard → Categories → Add Category
Thêm một vài danh mục để test
```

### 3. Tạo Sample Products
```
1. Đăng xuất & đăng ký tài khoản Seller mới
2. Login với tài khoản Seller
3. Seller Dashboard → My Products → Add Product
4. Điền thông tin & Upload hình ảnh
```

### 4. Kiểm tra Customer Flow
```
1. Đăng xuất & đăng ký tài khoản Customer
2. Browse Products
3. Click vào sản phẩm xem chi tiết
4. Add to Cart
5. Go to Cart & Checkout
6. Place Order (COD)
7. View Order in My Orders
```

## 🔧 Troubleshooting

### ❌ "Database Connection Failed"
**Solution:**
1. Kiểm tra MySQL đang chạy
2. Kiểm tra `config/config.php` - Database credentials
3. Kiểm tra database `ecommerce_volley` tồn tại

### ❌ "Class not found"
**Solution:**
1. Kiểm tra tên file & folder (case-sensitive)
2. Kiểm tra autoloader `core/Autoloader.php`
3. Kiểm tra không có typo trong tên class

### ❌ File upload không hoạt động
**Solution:**
1. Kiểm tra folder `public/images/` tồn tại
2. Cấp quyền write: Right-click → Properties → Security
3. Kiểm tra `MAX_FILE_SIZE` trong `config/config.php`

### ❌ VNPay payment error
**Solution:**
1. Kiểm tra `VNPAY_TMNCODE` & `VNPAY_HASHSECRET`
2. Sử dụng sandbox mode cho test
3. Kiểm tra URL return trong VNPay dashboard

### ❌ Session không hoạt động
**Solution:**
1. Kiểm tra `session_start()` trong `index.php`
2. Kiểm tra `ini_set('session.save_path', ...)` nếu cần
3. Clear browser cache & cookies

## 📊 Database Tables Structure

```sql
✓ users           - Người dùng (Admin, Seller, Customer)
✓ categories      - Danh mục sản phẩm
✓ products        - Sản phẩm
✓ product_images  - Hình ảnh sản phẩm
✓ carts           - Giỏ hàng
✓ orders          - Đơn hàng
✓ order_items     - Chi tiết đơn hàng
✓ banners         - Banner quảng cáo
✓ wallets         - Ví điện tử
✓ wallet_transactions - Giao dịch ví
✓ vnpay_transactions  - Giao dịch VNPay
✓ wishlists       - Danh sách yêu thích
✓ reviews         - Đánh giá sản phẩm
✓ admin_settings  - Cài đặt hệ thống
```

## 🔑 Key Features Verification

- [x] **Multi-vendor support** - Seller có thể bán đồng thời
- [x] **E-wallet system** - Tính phí tự động
- [x] **VNPay integration** - Thanh toán online
- [x] **Role-based access** - Admin, Seller, Customer
- [x] **Responsive design** - Mobile-friendly
- [x] **Search functionality** - Tìm kiếm sản phẩm
- [x] **Cart management** - Add, remove, update quantity
- [x] **Order tracking** - Theo dõi đơn hàng
- [x] **Wishlist** - Sản phẩm yêu thích
- [x] **Reviews** - Đánh giá & rating

## 📝 Next Steps (Optional Enhancements)

1. **Email Integration**
   - Order confirmation emails
   - Payment receipt emails

2. **Advanced Analytics**
   - Sales reports by period
   - Top products
   - Customer analytics

3. **Chat System**
   - Seller-Customer chat
   - Support ticket system

4. **Notification System**
   - Real-time order updates
   - Order status notifications

5. **AI Recommendations**
   - Product recommendations
   - Personalization

6. **Mobile App**
   - React Native or Flutter
   - Uses same API

## 📞 Support & Documentation

- **Documentation:** Xem `README.md`
- **Code Comments:** Tất cả files đều có comment
- **Database Schema:** Xem `config/database.sql`

## ✅ Checklist trước khi deploy

- [ ] Database được backup
- [ ] Tất cả config files được kiểm tra
- [ ] Images folders có write permission
- [ ] VNPay credentials đã cập nhật
- [ ] Email configuration được setup (nếu có)
- [ ] Security headers được cấu hình
- [ ] Database query được optimize
- [ ] Error logging được setup
- [ ] Backup strategy đã xác định

---

**Last Updated:** March 2, 2026
**Version:** 1.0.0
