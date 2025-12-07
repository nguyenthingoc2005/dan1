# Giải thích về service_type_id trong bảng service_providers

## Vấn đề hiện tại

Bảng `service_providers` có trường `service_type_id` (nullable), nhưng **một nhà cung cấp có thể cung cấp nhiều loại dịch vụ khác nhau**.

## Ví dụ thực tế

Một khách sạn có thể cung cấp:

- **Dịch vụ lưu trú** (service_type: "Khách sạn")
- **Dịch vụ ăn uống** (service_type: "Nhà hàng")
- **Dịch vụ spa** (service_type: "Spa & Wellness")
- **Dịch vụ vận chuyển** (service_type: "Vận chuyển")

Nhưng với thiết kế hiện tại, chỉ có thể gán **1 service_type_id** cho mỗi nhà cung cấp.

## Phân tích thiết kế

### Thiết kế hiện tại (KHÔNG HỢP LÝ)

```
service_providers
├── service_type_id (1) ← CHỈ CÓ THỂ CHỌN 1 LOẠI
└── services (n)
    └── service_type_id (1) ← Mỗi service có thể có loại riêng
```

**Vấn đề:**

- Một nhà cung cấp chỉ có thể được gán 1 loại dịch vụ chính
- Không thể linh hoạt khi nhà cung cấp cung cấp nhiều loại dịch vụ

### Thiết kế đúng (NÊN LÀM)

```
service_providers
├── (KHÔNG CÓ service_type_id) ← BỎ ĐI
└── services (n)
    └── service_type_id (1) ← Mỗi service có loại riêng
```

**Lý do:**

- Mỗi **service** (dịch vụ cụ thể) đã có `service_type_id` riêng
- Nhà cung cấp có thể cung cấp nhiều loại dịch vụ thông qua các services khác nhau
- Linh hoạt hơn, không bị giới hạn

## Giải pháp đề xuất

### Option 1: Bỏ service_type_id khỏi service_providers (KHUYẾN NGHỊ)

```sql
-- Bỏ service_type_id từ service_providers
ALTER TABLE `service_providers`
DROP COLUMN IF EXISTS `service_type_id`,
DROP FOREIGN KEY IF EXISTS `service_providers_ibfk_3`,
DROP INDEX IF EXISTS `service_type_id`;
```

**Ưu điểm:**

- Đơn giản, không cần bảng trung gian
- Mỗi service đã có service_type_id riêng
- Linh hoạt, một nhà cung cấp có thể có nhiều loại dịch vụ

**Nhược điểm:**

- Mất khả năng filter nhà cung cấp theo loại dịch vụ chính (nhưng có thể filter qua services)

### Option 2: Tạo bảng trung gian (NẾU CẦN FILTER NHANH)

```sql
-- Tạo bảng trung gian
CREATE TABLE `service_provider_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `service_provider_id` INT NOT NULL,
  `service_type_id` INT NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_provider_type` (`service_provider_id`, `service_type_id`),
  FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE CASCADE
);
```

**Ưu điểm:**

- Một nhà cung cấp có thể có nhiều loại dịch vụ
- Có thể đánh dấu loại dịch vụ chính (is_primary)
- Filter nhà cung cấp theo loại dịch vụ nhanh hơn

**Nhược điểm:**

- Phức tạp hơn, cần thêm bảng
- Có thể trùng lặp với thông tin từ bảng services

## Khuyến nghị

**Nên bỏ `service_type_id` khỏi `service_providers`** vì:

1. **Đã có thông tin ở bảng services**: Mỗi service đã có `service_type_id` riêng
2. **Linh hoạt hơn**: Một nhà cung cấp có thể cung cấp nhiều loại dịch vụ
3. **Đơn giản hơn**: Không cần maintain thêm trường không cần thiết
4. **Tránh mâu thuẫn**: Tránh trường hợp service_type_id của provider khác với service_type_id của services

## Cách filter nhà cung cấp theo loại dịch vụ (sau khi bỏ service_type_id)

```sql
-- Tìm nhà cung cấp có dịch vụ loại "Khách sạn"
SELECT DISTINCT sp.*
FROM service_providers sp
INNER JOIN services s ON s.service_provider_id = sp.id
INNER JOIN service_types st ON s.service_type_id = st.id
WHERE st.name = 'Khách sạn'
AND sp.status = 'active';
```

## Migration Script

Xem file: `remove_service_type_id_from_providers.sql`
