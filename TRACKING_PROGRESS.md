# 📊 TRACKING PROGRESS - QUẢN LÝ TOUR DU LỊCH

**Dự án:** Admin Panel Quản Lý Tour Du Lịch  
**Bắt đầu:** 01/12/2024  
**Tech Stack:** PHP Native + MySQL + Vanilla JavaScript + Tailwind CSS  
**Status:** ✅ Phase 1 - Infrastructure Setup (In Progress)

---

## 📋 PHASE 1: CƠ SỞ HẠ TẦNG (INFRASTRUCTURE) ✅

### ✅ Bước 1: Thiết lập cấu trúc & Database

**Folder Structure:**

```
✅ /app
   ✅ /controllers
   ✅ /models
   ✅ /views
      ✅ /layouts
      ✅ /auth
      ✅ /admin
      ✅ /staff
      ✅ /guide
✅ /config
   ✅ config.php
   ✅ database.php
✅ /includes
   ✅ functions.php
✅ /public
   ✅ /assets
      ✅ /css
      ✅ /js
      ✅ /images
✅ /routes
   ✅ auth.php
   ✅ admin.php
   ✅ staff.php
   ✅ guide.php
✅ index.php (Main entry point)
```

**Files Created:**

- ✅ `config/config.php` - Cấu hình ứng dụng (site_name, timezone, constants)
- ✅ `config/database.php` - Kết nối PDO MySQL
- ✅ `includes/functions.php` - Hàm utility (format tiền, format ngày, validation, etc.)
- ✅ `index.php` - Entry point (router chính)
- ✅ `routes/auth.php` - Router cho auth (login, register, logout)
- ✅ `routes/admin.php` - Router cho admin (8 modules)
- ✅ `routes/staff.php` - Router cho staff (4 modules)
- ✅ `routes/guide.php` - Router cho guide (4 modules)

**Status:** ✅ COMPLETED

---

### ⏳ Bước 2: Xây dựng hệ thống Auth & User Management

**To-Do:**

- [ ] AuthController.php

  - [ ] login() - Validate email/password, set session
  - [ ] register() - Validate input, hash password, create user
  - [ ] logout() - Destroy session
  - [ ] forgotPassword() - Generate token, send email
  - [ ] resetPassword() - Validate token, update password

- [ ] UserModel.php

  - [ ] findByEmail($email)
  - [ ] create($data)
  - [ ] update($id, $data)
  - [ ] delete($id)
  - [ ] findById($id)
  - [ ] getAll() - Với pagination

- [ ] Views
  - [ ] `/auth/login.php` - Form login
  - [ ] `/auth/register.php` - Form register
  - [ ] `/auth/forgot-password.php` - Form forgot password
  - [ ] `/auth/reset-password.php` - Form reset password

**Dependencies:**

- ✅ functions.php (formatMoney, isValidEmail, sanitize, etc.)
- ✅ database.php (PDO connection)

**Status:** ⏳ PENDING

---

### ⏳ Bước 3: Tạo Layout & Components (Flat Design)

**To-Do:**

- [ ] `/layouts/header.php` - Header cố định trên

  - Tailwind CDN + Custom config
  - User menu (profile, logout)
  - Search bar (optional)

- [ ] `/layouts/sidebar.php` - Sidebar cố định trái

  - Menu theo role (admin/staff/guide)
  - Active state indicator
  - Collapse menu (mobile)

- [ ] `/layouts/footer.php` - Footer

  - Copyright, links

- [ ] `/layouts/main.html` - Main layout template

  - Header + Sidebar + Content + Footer
  - Grid layout

- [ ] Common Components
  - [ ] `/includes/alert.php` - Flash messages (success/error/warning/info)
  - [ ] `/includes/pagination.php` - Phân trang
  - [ ] `/includes/modal.php` - Modal dialog
  - [ ] `/includes/table.php` - Table template
  - [ ] `/includes/form.php` - Form template

**Design Guidelines:**

- ✅ Tailwind CDN: https://cdn.tailwindcss.com
- ✅ Custom colors: primary (#1e293b), accent (#3b82f6), main (#f3f4f6)
- ✅ NO box-shadow, NO gradient, NO thick borders
- ✅ Dùng padding/gap để phân cách

**Status:** ⏳ PENDING

---

## 📦 PHASE 2: MASTER DATA (2 bước)

### ⏳ Bước 4: Categories & Destinations

**Models:**

- [ ] CategoryModel.php

  - [ ] CRUD (Create, Read, Update, Delete)
  - [ ] Parent-child relationship
  - [ ] Display order

- [ ] DestinationModel.php
  - [ ] CRUD
  - [ ] upload images
  - [ ] image management

**Controllers:**

- [ ] CategoryController.php (Admin only)
- [ ] DestinationController.php (Admin only)

**Views:**

- [ ] `/admin/categories/index.php`
- [ ] `/admin/categories/create.php`
- [ ] `/admin/categories/edit.php`
- [ ] `/admin/destinations/index.php`
- [ ] `/admin/destinations/create.php`
- [ ] `/admin/destinations/edit.php`

**Status:** ⏳ PENDING

---

### ⏳ Bước 5: Suppliers & Services

**Models:**

- [ ] SupplierModel.php

  - [ ] CRUD
  - [ ] validation

- [ ] ServiceTypeModel.php

  - [ ] CRUD

- [ ] ServiceModel.php
  - [ ] CRUD
  - [ ] Link service_type & supplier

**Controllers:**

- [ ] SupplierController.php (Admin only)
- [ ] ServiceController.php (Admin only)

**Views:**

- [ ] `/admin/suppliers/index.php`
- [ ] `/admin/suppliers/create.php`
- [ ] `/admin/suppliers/edit.php`
- [ ] `/admin/services/index.php`
- [ ] `/admin/services/create.php`
- [ ] `/admin/services/edit.php`

**Status:** ⏳ PENDING

---

## 🎯 PHASE 3: CORE BUSINESS (8 bước)

### ⏳ Bước 6: Tours (Staff tạo → Admin duyệt)

**Status:** ⏳ PENDING

### ⏳ Bước 7: Customers & Import Excel

**Status:** ⏳ PENDING

### ⏳ Bước 8: Bookings (Staff tạo → Admin duyệt)

**Status:** ⏳ PENDING

### ⏳ Bước 9: Payments

**Status:** ⏳ PENDING

### ⏳ Bước 10: Tour Assignments (Admin phân công HDV)

**Status:** ⏳ PENDING

### ⏳ Bước 11: Booking Services (Admin đặt dịch vụ thực tế)

**Status:** ⏳ PENDING

### ⏳ Bước 12: Supplier Payments (Admin thanh toán)

**Status:** ⏳ PENDING

### ⏳ Bước 13: Incurred Expenses (Guide ghi, Admin duyệt)

**Status:** ⏳ PENDING

---

## 🔧 PHASE 4: OPERATIONS & REPORTING (3 bước)

### ⏳ Bước 14: Tour Operations (Guide check-in, journal, review)

**Status:** ⏳ PENDING

### ⏳ Bước 15: Reports & Dashboards

**Status:** ⏳ PENDING

### ⏳ Bước 16: Utility Functions Enhancement

**Status:** ✅ COMPLETED (functions.php đã tạo)

---

## 🧪 PHASE 5: TESTING & QA

### ⏳ Bước 17: Test toàn bộ workflows & fix bugs

**Status:** ⏳ PENDING

---

## 📝 DATABASE SCHEMA STATUS

### ✅ Các bảng cần tạo:

**Module 1: Users & Roles**

- [ ] roles
- [ ] users
- [ ] password_resets

**Module 2: Categories & Destinations**

- [ ] categories
- [ ] destinations
- [ ] destination_images

**Module 3: Services & Suppliers**

- [ ] service_types
- [ ] suppliers
- [ ] services
- [ ] tour_services
- [ ] booking_services
- [ ] supplier_payments
- [ ] supplier_payment_details

**Module 4: Tours**

- [ ] tours
- [ ] tour_images
- [ ] tour_highlights
- [ ] tour_included_excluded
- [ ] tour_faqs
- [ ] itineraries
- [ ] policies
- [ ] tour_policies

**Module 5: Customers**

- [ ] customers
- [ ] customer_import_logs

**Module 6: Bookings**

- [ ] cancellation_policies
- [ ] discount_codes
- [ ] bookings
- [ ] booking_customers
- [ ] booking_status_history

**Module 7: Payments**

- [ ] payments
- [ ] payment_logs
- [ ] invoices
- [ ] refunds

**Module 8: Operations**

- [ ] tour_assignments
- [ ] customer_checkins
- [ ] journals
- [ ] journal_images
- [ ] incurred_expenses

**Module 9: Email**

- [ ] email_templates
- [ ] email_logs

**Module 10: Vehicles**

- [ ] vehicles

---

## 🚀 NEXT STEPS

### Bước tiếp theo: Bước 2 - Authentication & User Management

1. **Tạo database schema** - SQL script tạo tất cả 28 bảng
2. **AuthController.php** - Logic đăng nhập/đăng ký
3. **UserModel.php** - CRUD users
4. **Login/Register views** - HTML form với Tailwind CSS

---

## 📞 NOTES

- Tuân thủ Vibe Coding: Simple is Best
- Mỗi function phải có docblock
- Error handling graceful
- Session timeout: 3600 giây (1 giờ)
- Password hash: PASSWORD_BCRYPT

---

**Last Updated:** 01/12/2024 10:00  
**Updated By:** Senior PHP Developer (Vibe Coding)
