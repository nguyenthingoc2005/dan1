# FLOW: CHECK-IN CHI TIẾT THEO HOẠT ĐỘNG (ACTIVITY CHECK-IN)

## 📋 TỔNG QUAN

Tính năng check-in chi tiết của HDV theo các hoạt động tour:
- Lên xe (boarding)
- Ăn (meal: breakfast, lunch, dinner, snack)
- Ngủ (accommodation: check_in, check_out)
- Di chuyển (transfer)
- Hoạt động (activity)

**Đặc điểm:**
- Check-in qua web
- Không cần GPS
- Không real-time (không cần thông báo ngay khi khách vắng)

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: THIẾT LẬP CHECKPOINT (KHI TẠO TOUR)

**Actor:** Admin/Tour Manager

**Thời điểm:** Khi tạo tour mới

**Hành động:**
1. Tạo Activity Checkpoint Templates cho tour
   ```sql
   INSERT INTO activity_checkpoint_templates (tour_id, checkpoint_code, checkpoint_name, checkpoint_type, ...)
   VALUES
   (?, 'BOARDING_1', 'Lên xe điểm đón', 'boarding', ...),
   (?, 'MEAL_BREAKFAST_DAY1', 'Ăn sáng ngày 1', 'meal', 'breakfast', ...),
   (?, 'ACCOMMODATION_CHECKIN_DAY1', 'Check-in khách sạn ngày 1', 'accommodation', 'check_in', ...),
   ...
   ```

**Dữ liệu tạo:**
- `activity_checkpoint_templates` (theo tour)

---

### BƯỚC 2: COPY CHECKPOINT KHI TẠO TOUR SCHEDULE

**Actor:** System (Tự động) hoặc Admin

**Thời điểm:** Khi tạo tour schedule

**Hành động:**
1. Copy templates thành checkpoints thực tế
   ```sql
   INSERT INTO activity_checkpoints (
       tour_schedule_id,
       template_id,
       checkpoint_code,
       checkpoint_name,
       checkpoint_type,
       scheduled_date, -- Tính từ start_date + scheduled_date_offset
       scheduled_time,
       ...
   )
   SELECT 
       ? AS tour_schedule_id,
       id AS template_id,
       checkpoint_code,
       checkpoint_name,
       checkpoint_type,
       DATE_ADD(?, INTERVAL scheduled_date_offset DAY) AS scheduled_date,
       scheduled_time,
       ...
   FROM activity_checkpoint_templates
   WHERE tour_id = ? AND status = 'active';
   ```

**Dữ liệu tạo:**
- `activity_checkpoints` (từ templates)

---

### BƯỚC 3: HDV BẮT ĐẦU CHECKPOINT

**Actor:** HDV

**Thời điểm:** Khi đến thời gian checkpoint

**Hành động:**
1. HDV mở web → chọn tour schedule → chọn checkpoint
2. Bắt đầu checkpoint
   ```sql
   INSERT INTO activity_checkin_summary (
       tour_schedule_id,
       activity_checkpoint_id,
       checkpoint_date,
       scheduled_start_time,
       actual_start_time,
       total_customers,
       started_by,
       status
   )
   VALUES (?, ?, ?, ?, NOW(), ?, ?, 'in_progress');
   ```

3. Hiển thị danh sách khách cần check-in
   ```sql
   SELECT 
       bc.id AS booking_customer_id,
       bc.customer_id,
       bc.booking_id,
       c.full_name,
       c.gender,
       bc.age_type
   FROM booking_customers bc
   JOIN customers c ON bc.customer_id = c.id
   JOIN bookings b ON bc.booking_id = b.id
   WHERE b.tour_schedule_id = ?
     AND b.payment_status = 'paid'
   ORDER BY c.full_name;
   ```

**Dữ liệu tạo:**
- `activity_checkin_summary` (status: in_progress)

---

### BƯỚC 4: CHECK-IN TỪNG KHÁCH

**Actor:** HDV

**Hành động:**
1. HDV chọn khách → chọn trạng thái
   - Present (có mặt)
   - Absent (vắng mặt)
   - Late (muộn) → nhập số phút muộn
   - Early (sớm) → nhập số phút sớm
   - Excused (được miễn) → nhập lý do

2. Tạo check-in record
   ```sql
   INSERT INTO activity_checkins (
       tour_schedule_id,
       activity_checkpoint_id,
       booking_customer_id,
       customer_id,
       booking_id,
       checkpoint_date,
       scheduled_time,
       actual_time,
       checkin_datetime,
       status,
       minutes_late,
       minutes_early,
       checked_by,
       notes,
       excused_reason
   )
   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?);
   ```

3. Có thể check-in nhiều khách cùng lúc (batch check-in)

**Dữ liệu tạo:**
- `activity_checkins` (từng khách)

---

### BƯỚC 5: HOÀN THÀNH CHECKPOINT

**Actor:** HDV

**Hành động:**
1. HDV đánh dấu hoàn thành checkpoint
2. Hệ thống tự động tính toán thống kê
   ```sql
   UPDATE activity_checkin_summary
   SET 
       actual_end_time = NOW(),
       present_count = (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'present'),
       absent_count = (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'absent'),
       late_count = (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'late'),
       early_count = (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'early'),
       excused_count = (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'excused'),
       average_late_minutes = (SELECT AVG(minutes_late) FROM activity_checkins WHERE activity_checkpoint_id = ? AND status = 'late'),
       completed_by = ?,
       status = 'completed'
   WHERE id = ?;
   ```

**Dữ liệu cập nhật:**
- `activity_checkin_summary` (thống kê)

---

## 📊 QUERY HỮU ÍCH

### Xem danh sách checkpoint của tour schedule
```sql
SELECT 
    ac.id,
    ac.checkpoint_code,
    ac.checkpoint_name,
    ac.checkpoint_type,
    ac.scheduled_date,
    ac.scheduled_time,
    ac.location_name,
    acs.status AS summary_status,
    acs.present_count,
    acs.absent_count,
    acs.late_count
FROM activity_checkpoints ac
LEFT JOIN activity_checkin_summary acs ON ac.id = acs.activity_checkpoint_id 
    AND acs.checkpoint_date = ac.scheduled_date
WHERE ac.tour_schedule_id = ?
ORDER BY ac.scheduled_date, ac.scheduled_time;
```

### Xem check-in của một khách trong tour
```sql
SELECT 
    ac.checkpoint_name,
    ac.checkpoint_type,
    ac.scheduled_date,
    aci.status,
    aci.actual_time,
    aci.minutes_late,
    aci.notes
FROM activity_checkins aci
JOIN activity_checkpoints ac ON aci.activity_checkpoint_id = ac.id
WHERE aci.customer_id = ?
  AND aci.tour_schedule_id = ?
ORDER BY ac.scheduled_date, ac.scheduled_time;
```

### Xem tổng hợp check-in của tour
```sql
SELECT 
    ac.checkpoint_name,
    ac.checkpoint_type,
    ac.scheduled_date,
    acs.total_customers,
    acs.present_count,
    acs.absent_count,
    acs.late_count,
    acs.average_late_minutes
FROM activity_checkin_summary acs
JOIN activity_checkpoints ac ON acs.activity_checkpoint_id = ac.id
WHERE acs.tour_schedule_id = ?
ORDER BY ac.scheduled_date, ac.scheduled_time;
```

---

## ⚠️ BUSINESS RULES

1. **Một khách chỉ check-in 1 lần cho 1 checkpoint trong 1 ngày**
2. **Checkpoint bắt buộc (`is_required=true`): phải check-in, nếu vắng cần lý do**
3. **Thời gian: `actual_time` không được quá sớm/trễ so với `scheduled_time` (có thể set ngưỡng)**
4. **Summary: tự động cập nhật khi có check-in mới**

---

## 🔄 TRƯỜNG HỢP ĐẶC BIỆT

### Check-in muộn
- HDV chọn status = 'late'
- Nhập số phút muộn
- Hệ thống tự động tính `average_late_minutes`

### Khách vắng mặt
- HDV chọn status = 'absent'
- Có thể nhập lý do trong `notes`
- Nếu checkpoint bắt buộc → cảnh báo

### Check-in nhóm
- HDV có thể chọn nhiều khách cùng lúc
- Chọn trạng thái chung cho tất cả
- Hoặc check-in từng người một

