# TÓM TẮT ROUTES HỆ THỐNG

## Cấu trúc Routing

Hệ thống sử dụng pattern: `?act={action}` hoặc `?act={role}-{module}&action={action}`

---

## 1. ADMIN ROUTES (`?act=admin`)

**Pattern:** `?act=admin&module={module}&action={action}`

### Dashboard
- **URL:** `?act=admin` (không có module) → Default case → Dashboard
- **Controller:** `DashboardController::adminDashboard()`

### Modules:

#### Categories
- `?act=admin&module=categories&action=index`
- `?act=admin&module=categories&action=create`
- `?act=admin&module=categories&action=store`
- `?act=admin&module=categories&action=edit`
- `?act=admin&module=categories&action=update`
- `?act=admin&module=categories&action=delete`

#### Users
- `?act=admin&module=users&action=index`
- `?act=admin&module=users&action=create`
- `?act=admin&module=users&action=store`
- `?act=admin&module=users&action=edit`
- `?act=admin&module=users&action=update`
- `?act=admin&module=users&action=delete`
- `?act=admin&module=users&action=toggle-status`

#### Tours
- `?act=admin&module=tours&action=index`
- `?act=admin&module=tours&action=create`
- `?act=admin&module=tours&action=store`
- `?act=admin&module=tours&action=show`
- `?act=admin&module=tours&action=edit`
- `?act=admin&module=tours&action=update`
- `?act=admin&module=tours&action=changeStatus`
- `?act=admin&module=tours&action=delete`
- `?act=admin&module=tours&action=selectTemplate`
- `?act=admin&module=tours&action=createFromTemplate`
- `?act=admin&module=tours&action=getTemplateData` (AJAX)
- `?act=admin&module=tours&action=getDestinations` (AJAX)
- `?act=admin&module=tours&action=getServiceInfo` (AJAX)
- `?act=admin&module=tours&action=getServiceProviders` (AJAX)
- `?act=admin&module=tours&action=createPolicy` (AJAX)
- `?act=admin&module=tours&action=getPolicy` (AJAX)
- `?act=admin&module=tours&action=loadDayServicesEditor` (Component)
- `?act=admin&module=tours&action=loadItineraryManager` (Component)
- `?act=admin&module=tours&action=saveFormSession` (AJAX)
- `?act=admin&module=tours&action=clearTourSession` (AJAX)
- `?act=admin&module=tours&action=uploadImage` (AJAX)

#### Bookings
- `?act=admin&module=bookings&action=index`
- `?act=admin&module=bookings&action=create`
- `?act=admin&module=bookings&action=store`
- `?act=admin&module=bookings&action=show`
- `?act=admin&module=bookings&action=changeStatus`
- `?act=admin&module=bookings&action=storePayment`
- `?act=admin&module=bookings&action=importPassengers`
- `?act=admin&module=bookings&action=previewPassengers`
- `?act=admin&module=bookings&action=downloadTemplate`
- `?act=admin&module=bookings&action=storeBookingService`
- `?act=admin&module=bookings&action=deleteBookingService`
- `?act=admin&module=bookings&action=addPassengerToBooking`

#### Customers
- `?act=admin&module=customers&action=index`
- `?act=admin&module=customers&action=create`
- `?act=admin&module=customers&action=store`
- `?act=admin&module=customers&action=show`
- `?act=admin&module=customers&action=edit`
- `?act=admin&module=customers&action=update`
- `?act=admin&module=customers&action=delete`
- `?act=admin&module=customers&action=import`
- `?act=admin&module=customers&action=importStore`
- `?act=admin&module=customers&action=importResult`
- `?act=admin&module=customers&action=importLogs`
- `?act=admin&module=customers&action=downloadTemplate`

#### Schedules
- `?act=admin&module=schedules&action=index`
- `?act=admin&module=schedules&action=create`
- `?act=admin&module=schedules&action=store`
- `?act=admin&module=schedules&action=show`
- `?act=admin&module=schedules&action=edit`
- `?act=admin&module=schedules&action=update`
- `?act=admin&module=schedules&action=delete`
- `?act=admin&module=schedules&action=changeStatus`
- `?act=admin&module=schedules&action=assignGuideForm`
- `?act=admin&module=schedules&action=assignGuide`
- `?act=admin&module=schedules&action=cancelForm`
- `?act=admin&module=schedules&action=cancel`

#### Payments
- `?act=admin&module=payments&action=index`
- `?act=admin&module=payments&action=create`
- `?act=admin&module=payments&action=show`

#### Journals
- `?act=admin&module=journals&action=index`
- `?act=admin&module=journals&action=create`
- `?act=admin&module=journals&action=store`
- `?act=admin&module=journals&action=show`

#### Reports
- `?act=admin&module=reports&action=index` → Redirect to revenue
- `?act=admin&module=reports&action=revenue`
- `?act=admin&module=reports&action=bookings`

#### Settings
- `?act=admin&module=settings&action=general`
- `?act=admin&module=settings&action=email`

#### Policies
- `?act=admin&module=policies&action=index`
- `?act=admin&module=policies&action=create`
- `?act=admin&module=policies&action=store`
- `?act=admin&module=policies&action=edit`
- `?act=admin&module=policies&action=update`
- `?act=admin&module=policies&action=delete`

#### Location Services
- Nhiều actions cho quản lý location services (xem file routes/admin.php)

---

## 2. STAFF ROUTES (`?act=staff-{module}`)

**Pattern:** `?act=staff-{module}&action={action}`

### Dashboard
- **URL:** `?act=staff-dashboard` hoặc `?act=staff-` (module rỗng)
- **Controller:** `DashboardController::staffDashboard()`

### Modules:

#### Tours
- `?act=staff-tours&action=index`
- `?act=staff-tours&action=selectTemplate`
- `?act=staff-tours&action=create`
- `?act=staff-tours&action=createFromTemplate`
- `?act=staff-tours&action=store`
- `?act=staff-tours&action=show`
- `?act=staff-tours&action=edit`
- `?act=staff-tours&action=update`
- `?act=staff-tours&action=getDestinations` (AJAX)
- `?act=staff-tours&action=getServiceInfo` (AJAX)

#### Bookings
- `?act=staff-bookings&action=index`
- `?act=staff-bookings&action=create`
- `?act=staff-bookings&action=store`
- `?act=staff-bookings&action=show`
- `?act=staff-bookings&action=storePayment`
- `?act=staff-bookings&action=previewPassengers`
- `?act=staff-bookings&action=downloadTemplate`

#### Customers
- `?act=staff-customers&action=index`
- `?act=staff-customers&action=create`
- `?act=staff-customers&action=store`
- `?act=staff-customers&action=show`
- `?act=staff-customers&action=edit`
- `?act=staff-customers&action=update`
- `?act=staff-customers&action=import`
- `?act=staff-customers&action=importStore`
- `?act=staff-customers&action=importResult`
- `?act=staff-customers&action=importLogs`
- `?act=staff-customers&action=downloadTemplate`

#### Payments (Read Only)
- `?act=staff-payments&action=index`
- `?act=staff-payments&action=show`

#### Schedules (Read Only)
- `?act=staff-schedules&action=index`
- `?act=staff-schedules&action=show`

---

## 3. GUIDE ROUTES (`?act=guide-{module}`)

**Pattern:** `?act=guide-{module}&action={action}`

### Dashboard
- **URL:** `?act=guide-dashboard` hoặc `?act=guide-` (module rỗng)
- **Controller:** `Guide\DashboardController::index()`

### Modules:

#### Tours
- `?act=guide-tours&action=index`
- `?act=guide-tours&action=show`

#### Check-in
- `?act=guide-checkin&action=index`
- `?act=guide-checkin&action=show`
- `?act=guide-checkin&action=store`
- `?act=guide-checkin&action=printManifest`

#### Journals
- `?act=guide-journals&action=index` hoặc `?act=guide-journal&action=index`
- `?act=guide-journals&action=create`
- `?act=guide-journals&action=store`
- `?act=guide-journals&action=show`
- `?act=guide-journals&action=edit`
- `?act=guide-journals&action=update`
- `?act=guide-journals&action=delete`

---

## 4. PROFILE ROUTES (`?act=profile`)

**Pattern:** `?act=profile/{action}`

- `?act=profile` hoặc `?act=profile/index` → Profile index
- `?act=profile/edit` → Edit profile form
- `?act=profile/update` → Update profile (POST)
- `?act=profile/change-password` → Change password form
- `?act=profile/update-password` → Update password (POST)

---

## 5. PUBLIC ROUTES

- `?act=login` hoặc `?act=` (rỗng) → Login page
- `?act=logout` → Logout
- `?act=access-denied` → Access denied page

---

## Redirect Functions

### `redirect_to_dashboard()`
Redirect user về dashboard theo role:
- **Admin:** `?act=admin`
- **Staff:** `?act=staff-dashboard`
- **Guide:** `?act=guide-dashboard`

---

## Lưu ý

1. **Admin routes:** Khi `module` rỗng hoặc không match → Default case → Dashboard
2. **Staff routes:** Khi `module` rỗng hoặc `module = 'dashboard'` → Dashboard
3. **Guide routes:** Khi `module` rỗng hoặc `module = 'dashboard'` → Dashboard
4. Tất cả routes đều có permission check (require_admin, require_staff, require_guide)
5. AJAX endpoints thường không có view, chỉ return JSON

---

## Các vấn đề đã sửa

1. ✅ Xóa duplicate case 'store' trong admin tours module
2. ✅ Sửa redirect_to_dashboard() để match với routes (admin: `?act=admin` thay vì `?act=admin-dashboard`)

---

## Các vấn đề cần kiểm tra

1. ⚠️ `getTemplateData()` method trong TourController - cần kiểm tra xem có tồn tại không

