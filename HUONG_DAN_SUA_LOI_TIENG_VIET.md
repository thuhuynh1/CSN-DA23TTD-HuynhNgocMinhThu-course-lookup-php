# 🔧 HƯỚNG DẪN SỬA LỖI CẬP NHẬT TRẠNG THÁI ĐĂNG KÝ KHÓA HỌC

## 🚨 VẤN ĐỀ HIỆN TẠI:
Bạn không thể cập nhật trạng thái đăng ký khóa học trong admin panel vì:
1. **Database có vấn đề**: Cấu trúc database không khớp với code
2. **Admin session chưa được thiết lập**: Hệ thống cần quyền admin
3. **API endpoints không nhất quán**: Code gọi API khác nhau

## 📋 HƯỚNG DẪN TỪNG BƯỚC:

### BƯỚC 1: KHỞI ĐỘNG WEB SERVER
1. **Mở XAMPP Control Panel**
2. **Start Apache** (nút Start màu xanh)
3. **Start MySQL** (nút Start màu xanh)
4. Đảm bảo cả 2 đều hiển thị màu xanh lá

### BƯỚC 2: CHẠY SCRIPT SỬA LỖI
1. **Mở trình duyệt web** (Chrome, Firefox, Edge...)
2. **Nhập địa chỉ**: `http://localhost/fix_enrollment_now.php`
3. **Nhấn Enter** và chờ script chạy
4. **Xem kết quả**: Script sẽ tự động sửa tất cả lỗi

**❗ NẾU BỊ LỖI "NOT FOUND":**
- Thử: `http://localhost/CSN/fix_enrollment_now.php`
- Hoặc: `http://localhost/htdocs/fix_enrollment_now.php`
- Hoặc kiểm tra thư mục chứa file của bạn

### BƯỚC 3: THIẾT LẬP QUYỀN ADMIN
1. **Sau khi script chạy xong**, click vào link **"Setup Admin Session"**
2. **Hoặc truy cập**: `http://localhost/dp/set_admin_session_quick.php`
3. **Hệ thống sẽ tự động** chuyển về trang admin

### BƯỚC 4: KIỂM TRA CHỨC NĂNG
1. **Truy cập Admin Panel**: `http://localhost/admin.html`
2. **Click vào "Đăng ký khóa học"** ở menu bên trái
3. **Thử thay đổi trạng thái** bằng dropdown
4. **Kiểm tra xem có cập nhật được không**

## 🎯 KẾT QUẢ MONG MUỐN:
- ✅ Hệ thống chỉ có 2 trạng thái: **"Chờ xử lý"** và **"Đã duyệt"**
- ✅ Có thể thay đổi trạng thái bằng dropdown
- ✅ Thông báo thành công khi cập nhật
- ✅ Dữ liệu được lưu vào database

## 🔍 NẾU VẪN CÓ VẤN ĐỀ:

### Vấn đề 1: Không truy cập được script
**Nguyên nhân**: Web server chưa chạy hoặc đường dẫn sai
**Giải pháp**:
- Kiểm tra XAMPP/WAMP đã start chưa
- Thử các đường dẫn khác nhau
- Kiểm tra file có tồn tại không

### Vấn đề 2: Script báo lỗi database
**Nguyên nhân**: Kết nối database thất bại
**Giải pháp**:
- Kiểm tra MySQL đã start chưa
- Xem file `dp/config.php` có đúng thông tin kết nối không
- Tạo database `user_auth_db` nếu chưa có

### Vấn đề 3: Vẫn không cập nhật được trạng thái
**Nguyên nhân**: Admin session chưa được thiết lập
**Giải pháp**:
- Chạy lại: `http://localhost/dp/set_admin_session_quick.php`
- Refresh trang admin panel
- Thử lại chức năng cập nhật

## 📞 HỖ TRỢ THÊM:

### Kiểm tra nhanh:
1. **Database có dữ liệu không**: Truy cập phpMyAdmin → user_auth_db → enrollments
2. **API có hoạt động không**: Truy cập `http://localhost/dp/admin_enrollments_no_auth.php`
3. **Admin session có OK không**: Xem có thông báo đỏ ở đầu trang admin không

### File quan trọng đã được tạo:
- `fix_enrollment_now.php` - Script sửa lỗi chính
- `test_enrollment_fix.html` - Trang test chức năng
- `HUONG_DAN_SUA_LOI_TIENG_VIET.md` - Hướng dẫn này

## 🎉 SAU KHI SỬA XONG:
- Hệ thống sẽ hoạt động bình thường
- Có thể cập nhật trạng thái đăng ký
- Admin panel hoạt động đầy đủ chức năng
- Dữ liệu được lưu trữ chính xác

**📝 Lưu ý**: Nếu vẫn gặp vấn đề, hãy chụp màn hình lỗi và báo lại để được hỗ trợ cụ thể hơn!