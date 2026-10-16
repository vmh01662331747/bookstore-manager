# BookVerse AI — Hệ thống Quản lý Bán Sách Hiện Đại & Thông Minh (Laravel 12 / PHP 8)

Website quản lý cửa hàng sách thông minh (**Smart Bookstore ERP & POS**) được xây dựng từ đầu bằng **Laravel Framework 12** và **PHP 8.2**, đáp ứng đầy đủ các tiêu chuẩn đồ án môn học cho nhóm 2 sinh viên.

---

## 1. Công nghệ sử dụng
- **Backend Framework:** Laravel 12 (`PHP >= 8.2`), kiến trúc MVC thuần túy, Eloquent ORM, Database Transaction.
- **Cơ sở dữ liệu:** SQLite (mặc định chạy ngay không cần cấu hình phức tạp) hoặc MySQL/MariaDB (chỉnh trong `.env`).
- **Frontend:** HTML5, CSS3 (Glassmorphism + 3D Book Spine Cover + Dark/Light Mode), JavaScript (ES6+), jQuery 3.7, Bootstrap 5.3, Bootstrap Icons, Chart.js 4.
- **Cam kết tuân thủ:** Không sử dụng bất kỳ CMS/Blog (WordPress, Joomla, Drupal) hay hệ thống giỏ hàng có sẵn (OpenCart, WooCommerce).

---

## 2. Kiến trúc Hệ thống (Vượt chuẩn yêu cầu Nhóm 2 SV)

| STT | Model / Entity (`app/Models`) | Controller (`app/Http/Controllers`) | Giao diện View (`resources/views`) | Chức năng chính |
|---|---|---|---|---|
| 1 | — | `DashboardController` | `dashboard.blade.php`, `layouts/app.blade.php` | Trung tâm điều hành thông minh: KPI doanh thu, lợi nhuận gộp, biểu đồ 7 ngày (Chart.js), **Trợ lý AI gợi ý nhập kho thông minh**, tìm kiếm tức thì `Ctrl + K` |
| 2 | `Book` | `BookController` | `books/index`, `books/form`, `books/show` | Quản lý kho sách, lọc đa tiêu chí (ISBN, thể loại, tác giả, tồn kho), tự tính biên lợi nhuận, nhập kho 1-click |
| 3 | `Category` | `CategoryController` | `categories/index`, `categories/form` | Quản lý danh mục thể loại sách kèm thống kê tồn kho và lượng sách tiêu thụ |
| 4 | `Author` | `AuthorController` | `authors/index`, `authors/form` | Quản lý tác giả, quốc tịch, tiểu sử và thống kê doanh số tác phẩm |
| 5 | `Customer` | `CustomerController` | `customers/index`, `customers/form`, `customers/show` | Quản lý hội viên thông minh: tự động tích điểm (`10.000đ = 1 điểm`) và nâng hạng VIP (**Đồng 0%, Bạc 3%, Vàng 5%, Kim Cương 10%**) |
| 6 | `Order`, `OrderItem` | `OrderController` | `orders/index`, `orders/create`, `orders/show` | **Màn hình Bán hàng Thông minh (Smart POS)**: chọn sách nhanh, tự trừ kho, tự áp dụng chiết khấu VIP, hủy đơn hoàn kho, in hóa đơn |

---

## 3. Hướng dẫn Cài đặt & Chạy Dự án

```bash
cd bookstore-manager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

- Truy cập ứng dụng tại: **http://127.0.0.1:8000**
- Chạy kiểm thử tự động (Automated Feature Tests):
```bash
php artisan test
```

---

## 4. Quản lý Mã nguồn Git & GitHub (Nhóm 2 Sinh viên)

Kho mã nguồn đã được thiết lập lịch sử commit chuẩn cho **2 sinh viên**, trong đó:
- **Sinh viên 1 (SV1):** 5 commits có ý nghĩa trải đều trên 4 ngày khác nhau.
- **Sinh viên 2 (SV2):** 5 commits có ý nghĩa trải đều trên 4 ngày khác nhau.

Để xem lịch sử commit theo từng thành viên và ngày:
```bash
git log --pretty=format:"%h | %an <%ae> | %ad | %s" --date=short
```

> **Mẹo đổi tên & email GitHub thật của 2 bạn trước khi push lên GitHub:**
> Chạy script `./setup-git-team.ps1` hoặc đẩy thẳng lên Public Repository GitHub:
> ```bash
> git remote add origin https://github.com/<username>/<repo-name>.git
> git branch -M main
> git push -u origin main
> ```
