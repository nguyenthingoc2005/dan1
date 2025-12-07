# DEBUG TOUR CREATION - HƯỚNG DẪN KIỂM TRA

## VẤN ĐỀ
1. Form không submit được (không có gì xảy ra)
2. Khi thêm dịch vụ, không đổ ra được dịch vụ và nhà cung cấp

## CÁC SỬA ĐỔI ĐÃ THỰC HIỆN

### 1. Sửa buildServiceOptions và buildServiceProviderOptions
- ✅ Xử lý cả array và object format
- ✅ Nếu services là array: dùng `forEach` với `service.id`
- ✅ Nếu services là object: dùng `Object.entries`
- ✅ Xóa function duplicate

### 2. Thêm debug logging
- ✅ Console.log services và serviceProviders khi load
- ✅ Console.log khi mở modal
- ✅ Console.log số lượng options được generate

### 3. Sửa form submit
- ✅ Thêm event listener để lưu TinyMCE content
- ✅ Thêm validation cơ bản
- ✅ Hiển thị loading state

## CÁCH KIỂM TRA

### Bước 1: Mở Console (F12)
Kiểm tra các log:
```
Services loaded: [...]
ServiceProviders loaded: [...]
Services count: X
ServiceProviders count: Y
```

### Bước 2: Click "Thêm dịch vụ"
Kiểm tra console:
```
openAddServiceModal called for day X
Services available: [...]
Creating new modal for day X
Service options generated: X options
Provider options generated: Y options
Service select found, options count: X
```

### Bước 3: Kiểm tra dropdown
- Mở modal "Thêm dịch vụ"
- Kiểm tra dropdown "Chọn dịch vụ" có options không
- Kiểm tra dropdown "Chọn nhà dịch vụ" có options không

### Bước 4: Kiểm tra form submit
- Điền đầy đủ thông tin
- Click "Hoàn tất & Lưu"
- Kiểm tra console có lỗi không
- Kiểm tra network tab xem có request gửi đi không

## NẾU VẪN KHÔNG HOẠT ĐỘNG

### Kiểm tra 1: Services có được load không?
```javascript
// Trong console, chạy:
console.log(services);
console.log(serviceProviders);
```

Nếu `undefined` hoặc `null`:
- Kiểm tra PHP có trả về data không
- Kiểm tra `$services` và `$service_providers` trong controller

### Kiểm tra 2: Modal có được tạo không?
```javascript
// Trong console, chạy:
const modal = document.getElementById('add-service-modal-day-1');
console.log(modal);
```

Nếu `null`:
- Function `createServiceModal` không chạy
- Kiểm tra `openAddServiceModal` có được gọi không

### Kiểm tra 3: Select có options không?
```javascript
// Trong console, sau khi mở modal:
const select = document.getElementById('modal-service-id-day-1');
console.log(select);
console.log(select.options.length);
console.log(select.innerHTML);
```

Nếu `options.length <= 1`:
- `buildServiceOptions` không generate options
- Kiểm tra `services` có data không

### Kiểm tra 4: Form submit có bị chặn không?
```javascript
// Trong console:
const form = document.getElementById('tourForm');
form.addEventListener('submit', function(e) {
    console.log('Form submitting...');
    console.log(e);
});
```

Nếu không log:
- Event listener không được attach
- Form có thể bị preventDefault ở đâu đó

## SỬA LỖI THƯỜNG GẶP

### Lỗi: Services là array nhưng code dùng Object.entries
**Giải pháp:** Đã sửa trong `buildServiceOptions` - kiểm tra `Array.isArray()`

### Lỗi: Services không có id field
**Giải pháp:** Kiểm tra database có dữ liệu không, kiểm tra Service model

### Lỗi: Modal không hiển thị
**Giải pháp:** Kiểm tra z-index, kiểm tra class 'hidden' có được remove không

### Lỗi: Form không submit
**Giải pháp:** 
- Kiểm tra validation có chặn không
- Kiểm tra có preventDefault ở đâu không
- Kiểm tra network tab xem có request không

