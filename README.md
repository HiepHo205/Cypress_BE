# Cypress Backend 

Tài liệu này hướng dẫn nhanh cho thành viên mới cách cài đặt môi trường và khởi chạy dự án **Cypress Backend** trên máy cá nhân sau khi clone source code về.

---

# 1. Prerequisites

Trước khi bắt đầu, hãy đảm bảo máy của bạn đã cài đặt đầy đủ các thành phần sau:

| Software | Requirement                      |
| -------- | -------------------------------- |
| PHP      | >= 8.2                           |
| Composer | >= 2.0                           |
| Database | PostgreSQL (Managed by Supabase) |

---

#  2. Cài đặt & Khởi chạy dự án

Mở **Terminal** tại thư mục dự án vừa clone và thực hiện lần lượt các bước dưới đây.

---

## Bước 2.1 - Cài đặt các package PHP
```bash
composer install
```

---

## Bước 2.2 - Cấu hình môi trường (.env)
Copy file mẫu:
```bash
cp .env.example .env
```

Sau đó mở file **.env** và đảm bảo cấu hình PostgreSQL đúng như sau:

```env
APP_NAME=Laravel
APP_ENV=local
APP_URL=http://localhost

SESSION_DOMAIN=.cypresshub.com

# PostgreSQL Connection
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-northeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.cjmvdswpxnaeqihkbtht
DB_PASSWORD=1qasMs0qrEL0eYQR

# Local Server
SERVER_HOST=0.0.0.0
SERVER_PORT=80
```


---

## Bước 2.3 - Tạo Application Key
```bash
php artisan key:generate
```

---

## Bước 2.4 - Chạy Migration
Tạo toàn bộ cấu trúc bảng dữ liệu:

```bash
php artisan migrate
```

---

## Bước 2.5 - Xóa Cache hệ thống
```bash
php artisan optimize:clear
```

---
## Bước 2.6 - Khởi chạy dự án

```bash
php artisan serve
```

---

#  3. Kiểm tra sau khi cài đặt
Sau khi chạy:

```bash
php artisan serve
```

Terminal sẽ hiển thị:
```text
INFO  Server running on [http://0.0.0.0:80].

➜ Admin Portal: http://admin.cypresshub.com
➜ API Gateway : http://api.cypresshub.com

Press Ctrl+C to stop the server
```

---

## Kiểm tra API Gateway

Mở trình duyệt:

```text
http://api.cypresshub.com
```

Kết quả mong đợi:

```json
{
  "status": "success",
  "message": "CypressHub API Client Gateway is active (.com production mode).",
  "timestamp": "2026-07-14T10:29:37+00:00"
}
```

---

## Kiểm tra Admin Portal

Mở trình duyệt:

```text
http://admin.cypresshub.com
```

Kết quả mong đợi:

```text
CypressHub Admin Skeleton Boilerplate is Running!

The Vue.js/GraphQL admin skeleton is ready for development.
```

---

# Checklist

Đảm bảo toàn bộ các bước dưới đây đã hoàn thành:

- [ ] Đã clone source code thành công
- [ ] Chạy `composer install` thành công
- [ ] Đã tạo file `.env`
- [ ] Đã cấu hình PostgreSQL
- [ ] Chạy `php artisan key:generate` thành công
- [ ] Chạy `php artisan migrate` thành công
- [ ] Chạy `php artisan optimize:clear` thành công
- [ ] Chạy `php artisan serve` thành công
- [ ] Truy cập được **API Gateway**
- [ ] Truy cập được **Admin Portal**

---

#  Ghi chú

- Backend sử dụng **Laravel**.
- Database sử dụng **PostgreSQL** được cung cấp thông qua **Supabase PG Pooler**.
- Dự án chạy local với domain:

```text
http://admin.cypresshub.com
http://api.cypresshub.com
```

- Nếu thay đổi file `.env`, hãy chạy lại:

```bash
php artisan optimize:clear
```
