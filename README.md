# E-Commerce Volley - Multi-Vendor Platform

Một nền tảng thương mại điện tử hiện đại, được xây dựng bằng PHP thuần theo mô hình MVC, tương tự như Shopee.

## 🚀 Tính năng chính

### 1. Hệ thống xác thực
- ✅ Đăng ký (Register) - Customer & Seller
- ✅ Đăng nhập (Login) - Tất cả role
- ✅ Đăng xuất (Logout)
- ✅ Quản lý phiên Session
- ✅ Phân quyền theo vai trò (Admin, Seller, Customer)

### 2. Giao diện người dùng
- ✅ Header dùng chung (Home, Categories, Products, Cart, Orders, Account)
- ✅ Footer dùng chung (Contact, Policy, Language)
- ✅ Responsive design (Mobile, Tablet, Desktop)
- ✅ Tailwind CSS + Custom CSS

### 3. Quản lý sản phẩm
- ✅ Hiển thị sản phẩm (Home, Categories, Products, Search)
- ✅ Chi tiết sản phẩm (Images, Price, Discount, Stock, Reviews)
- ✅ Khuyến mãi theo thời gian (Discount Start/End Date)
- ✅ Sản phẩm bán chạy (Bestsellers)
- ✅ Sản phẩm đang khuyến mãi (On Sale)

### 4. Giỏ hàng & Đặt hàng
- ✅ Thêm/xóa sản phẩm khỏi giỏ hàng
- ✅ Chỉnh số lượng sản phẩm
- ✅ Tính tổng tiền tự động
- ✅ Checkout với thông tin giao hàng
- ✅ Đặt hàng theo từng seller

### 5. Thanh toán
- ✅ COD (Thanh toán khi nhận hàng)
- ✅ VNPay (Thanh toán online - QR Code)
- ✅ Xử lý giao dịch an toàn
- ✅ Cập nhật trạng thái đơn hàng tự động

### 6. Ví điện tử (E-Wallet)
- ✅ Ví cho Seller (nhận tiền từ Admin)
- ✅ Tính toán phí sàn & phí giao dịch tự động
- ✅ Công thức: Tiền Seller = Giá - Phí sàn - Phí VNPay
- ✅ Lịch sử giao dịch

### 7. Seller Dashboard
- ✅ Thống kê doanh thu (Real-time, Report)
- ✅ Quản lý sản phẩm (CRUD)
- ✅ Quản lý đơn hàng (Duyệt, Hủy, Cập nhật trạng thái)
- ✅ Quản lý ví điện tử

### 8. Admin Dashboard
- ✅ Thống kê toàn hệ thống
- ✅ Quản lý Users (Customer, Seller)
- ✅ Quản lý Categories
- ✅ Quản lý Banners
- ✅ Quản lý phí sàn & phí giao dịch

### 9. Wishlist
- ✅ Thêm/xóa sản phẩm yêu thích
- ✅ Xem danh sách wishlist

### 10. Reviews & Ratings
- ✅ Gửi đánh giá sản phẩm
- ✅ Xem đánh giá từ khách hàng khác
- ✅ Duyệt đánh giá (Admin)

## 📦 Cấu trúc thư mục

```
E-commerce Volley/
├── app/
│   ├── models/          # Models (User, Product, Order, Cart, Wallet, etc)
│   ├── controllers/     # Controllers (Auth, Home, Product, etc)
│   └── views/          # Views (HTML + Tailwind)
├── config/             # Configuration files
├── core/               # Base classes (Database, Model, Controller, View)
├── database/           # Database schema
├── helpers/            # Helper functions
├── public/
│   ├── css/           # CSS files
│   ├── js/            # JavaScript files
│   └── images/        # Uploaded images
├── resources/
│   └── lang/          # Language files (en, vi)
├── index.php          # Entry point
└── README.md
```

## 🗄️ Database

**Bảng chính:**
- `users` - Users (Admin, Seller, Customer)
- `categories` - Product categories
- `products` - Sản phẩm
- `product_images` - Hình ảnh sản phẩm
- `carts` - Giỏ hàng
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng
- `banners` - Banner quảng cáo
- `wallets` - Ví điện tử
- `wallet_transactions` - Giao dịch ví
- `vnpay_transactions` - Giao dịch VNPay
- `wishlists` - Danh sách yêu thích
- `reviews` - Đánh giá sản phẩm
- `admin_settings` - Cài đặt hệ thống

## 🔐 Tài khoản Demo

**Admin:**
- Email: `admin@gmail.com`
- Password: `admin123`

## 🛠️ Công nghệ sử dụng

- **Backend:** PHP 7.4+ (OOP, MVC)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **UI Framework:** Tailwind CSS
- **Web Server:** Apache (XAMPP)
- **Payment:** VNPay Gateway
- **Timezone:** Asia/Ho_Chi_Minh

## 📋 Yêu cầu

- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Apache web server (XAMPP)
- Các extensions: PDO, PDO_MySQL

## ⚙️ Cài đặt

### 1. Clone hoặc download dự án
```bash
# Dự án nằm trong: c:\xampp\htdocs\E-commerce Volley
```

### 2. Import database
```
Mở phpMyAdmin: http://localhost/phpmyadmin
Chọn "Import" → Upload file config/database.sql
```

### 3. Cấu hình
- Mở `config/config.php`
- Cập nhật thông tin Database (nếu cần)
- Cập nhật VNPay credentials

### 4. Chạy ứng dụng
```
http://localhost/E-commerce Volley/
```

## 🔄 Luồng hoạt động

### Customer
1. Đăng ký/Đăng nhập
2. Browse sản phẩm → Chi tiết sản phẩm
3. Thêm vào giỏ hàng
4. Checkout (COD hoặc VNPay)
5. Xem đơn hàng & Thanh toán
6. Nhận hàng & Đánh giá

### Seller
1. Đăng ký/Đăng nhập
2. Đăng sản phẩm (Add, Edit, Delete, Manage Stock)
3. Thiết lập khuyến mãi
4. Duyệt/Cập nhật đơn hàng
5. Xem doanh thu & Ví điện tử
6. Rút tiền (có phí sàn & phí VNPay)

### Admin
1. Quản lý Users (Block, Activate)
2. Quản lý Categories
3. Quản lý Banners
4. Cài đặt phí sàn & phí VNPay
5. Xem thống kê hệ thống
6. Quản lý duyệt đánh giá

## 💳 Tính toán phí

```
Giả sử đơn hàng: 100,000 VND

Phí Sàn (5%):        5,000 VND
Phí VNPay (1.5%):    1,500 VND
Tiền Seller nhận:    100,000 - 5,000 - 1,500 = 93,500 VND
```

## 🚨 Lưu ý quan trọng

- Không hard-code ID - Luôn sử dụng từ database
- Sử dụng khóa ngoại (Foreign Key) rõ ràng
- Validate & Sanitize dữ liệu đầu vào
- Sử dụng stored procedures hoặc transactions cho các thao tác phức tạp
- Tất cả code viết bằng English
- Không seed dữ liệu demo - Dữ liệu thực tế

## 📈 Mở rộng trong tương lai

- AI recommendation engine (không thay đổi database)
- Chat functionality giữa Seller & Customer
- Notification system
- Email integration
- Advanced analytics & reporting
- Multi-currency support
- Affiliate program

## 📝 License

MIT License - Tự do sử dụng cho mục đích cá nhân & thương mại

## 👥 Support

Liên hệ: contact@volley.com

---

**Version:** 1.0.0
**Last Updated:** March 2, 2026
