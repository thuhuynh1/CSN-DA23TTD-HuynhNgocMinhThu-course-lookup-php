# 🔧 Troubleshooting: Không thể cập nhật trạng thái đăng ký

## ✅ Checklist để kiểm tra vấn đề

### 1. 🔐 Kiểm tra Admin Session
- [ ] Đã đăng nhập admin chưa?
- [ ] Truy cập: `dp/set_admin_session.php` để thiết lập session tạm thời
- [ ] Kiểm tra: `dp/debug_enrollment_update.php` để xem session status

### 2. 🗄️ Kiểm tra Database
- [ ] Chạy migration: `dp/migrate_enrollment_status.php`
- [ ] Kiểm tra dữ liệu: `dp/test_enrollment_status.php`
- [ ] Xem có enrollment nào trong database không

### 3. 🌐 Kiểm tra API
- [ ] Test API: `dp/admin_enrollments_test.php` (bỏ qua admin check)
- [ ] Kiểm tra network tab trong browser
- [ ] Xem có lỗi JavaScript không

### 4. 🖥️ Kiểm tra Frontend
- [ ] Clear browser cache
- [ ] Reload trang admin
- [ ] Kiểm tra console errors
- [ ] Test với: `test_enrollment_update.html`

## 🚀 Các bước khắc phục

### Bước 1: Thiết lập Admin Session
```
1. Truy cập: http://localhost/dp/set_admin_session.php
2. Xác nhận session được thiết lập
3. Quay lại admin panel
```

### Bước 2: Migrate Database (nếu cần)
```
1. Truy cập: http://localhost/dp/migrate_enrollment_status.php
2. Kiểm tra migration thành công
3. Xem trạng thái mới trong database
```

### Bước 3: Test API
```
1. Truy cập: http://localhost/test_enrollment_update.html
2. Nhấn "Load Enrollments"
3. Thử cập nhật trạng thái
4. Kiểm tra debug results
```

### Bước 4: Debug Chi Tiết
```
1. Truy cập: http://localhost/dp/debug_enrollment_update.php
2. Xem tất cả thông tin debug
3. Kiểm tra từng bước
```

## 🔍 Các lỗi thường gặp

### Lỗi: "Không có quyền truy cập"
**Nguyên nhân:** Chưa đăng nhập admin
**Khắc phục:** Chạy `dp/set_admin_session.php`

### Lỗi: "Dữ liệu không hợp lệ"
**Nguyên nhân:** Trạng thái không đúng format
**Khắc phục:** Kiểm tra chỉ có 'pending' và 'approved'

### Lỗi: JavaScript không hoạt động
**Nguyên nhân:** Browser cache hoặc syntax error
**Khắc phục:** Clear cache, kiểm tra console

### Lỗi: Database connection
**Nguyên nhân:** Config database sai
**Khắc phục:** Kiểm tra `dp/config.php`

## 📞 Nếu vẫn không được

1. Chạy `dp/debug_enrollment_update.php` và gửi kết quả
2. Kiểm tra browser console errors
3. Kiểm tra network tab khi cập nhật trạng thái
4. Thử với `test_enrollment_update.html` để isolate vấn đề

## 🎯 Expected Behavior

Khi hoạt động đúng:
- Admin panel hiển thị dropdown với 2 options: "Chờ xử lý" và "Đã duyệt"
- Khi thay đổi dropdown, status được cập nhật ngay lập tức
- Trang reload và hiển thị trạng thái mới
- Không có lỗi trong console