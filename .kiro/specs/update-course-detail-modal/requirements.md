# Requirements Document

## Introduction

Cập nhật lại các thông tin hiển thị trong modal "Xem chi tiết" khóa học để đảm bảo thông tin chính xác và đầy đủ cho người dùng.

## Glossary

- **Modal Chi tiết khóa học**: Cửa sổ popup hiển thị thông tin chi tiết về khóa học khi người dùng nhấn "Xem chi tiết"
- **Thông tin khóa học**: Các dữ liệu về khóa học bao gồm tên, mô tả, học phí, thời lượng, lịch học, v.v.
- **Trạng thái đăng ký**: Tình trạng hiện tại của việc đăng ký khóa học (Chờ xác nhận/Đã xác nhận)

## Requirements

### Requirement 1

**User Story:** Là một học viên đã đăng ký khóa học, tôi muốn xem thông tin chi tiết chính xác về khóa học của mình, để tôi có thể nắm rõ các thông tin quan trọng.

#### Acceptance Criteria

1. WHEN người dùng nhấn nút "Xem chi tiết" THEN hệ thống SHALL hiển thị modal với thông tin đầy đủ về khóa học
2. WHEN modal được mở THEN hệ thống SHALL hiển thị tên khóa học chính xác từ database
3. WHEN modal được mở THEN hệ thống SHALL hiển thị trạng thái đăng ký hiện tại (Chờ xác nhận/Đã xác nhận)
4. WHEN modal được mở THEN hệ thống SHALL hiển thị ngày đăng ký chính xác
5. WHEN modal được mở THEN hệ thống SHALL hiển thị thông tin khóa học bao gồm thời lượng, học phí, cấp độ, lịch học

### Requirement 2

**User Story:** Là một học viên, tôi muốn thấy mô tả chi tiết và các tính năng nổi bật của khóa học, để tôi hiểu rõ hơn về nội dung học tập.

#### Acceptance Criteria

1. WHEN modal chi tiết được hiển thị THEN hệ thống SHALL hiển thị mô tả đầy đủ của khóa học
2. WHEN khóa học có tính năng nổi bật THEN hệ thống SHALL hiển thị danh sách các tính năng dưới dạng bullet points
3. WHEN khóa học không có tính năng nổi bật THEN hệ thống SHALL ẩn phần tính năng nổi bật
4. WHEN mô tả khóa học trống THEN hệ thống SHALL hiển thị thông báo "Chưa có mô tả"

### Requirement 3

**User Story:** Là một học viên có khóa học đã được xác nhận, tôi muốn thấy tiến trình học tập của mình, để tôi có thể theo dõi quá trình học.

#### Acceptance Criteria

1. WHEN khóa học có trạng thái "Đã xác nhận" THEN hệ thống SHALL hiển thị phần tiến trình học tập
2. WHEN khóa học có trạng thái "Chờ xác nhận" THEN hệ thống SHALL ẩn phần tiến trình học tập
3. WHEN hiển thị tiến trình THEN hệ thống SHALL tính toán phần trăm hoàn thành dựa trên thời gian đã học
4. WHEN hiển thị tiến trình THEN hệ thống SHALL hiển thị thời gian còn lại của khóa học

### Requirement 4

**User Story:** Là một học viên, tôi muốn có thông tin liên hệ hỗ trợ trong modal chi tiết, để tôi có thể liên hệ khi cần hỗ trợ về khóa học.

#### Acceptance Criteria

1. WHEN modal chi tiết được hiển thị THEN hệ thống SHALL hiển thị thông tin liên hệ hỗ trợ
2. WHEN hiển thị thông tin liên hệ THEN hệ thống SHALL bao gồm số điện thoại hotline
3. WHEN hiển thị thông tin liên hệ THEN hệ thống SHALL bao gồm email hỗ trợ
4. WHEN người dùng nhấn vào số điện thoại THEN hệ thống SHALL mở ứng dụng gọi điện
5. WHEN người dùng nhấn vào email THEN hệ thống SHALL mở ứng dụng email

### Requirement 5

**User Story:** Là một học viên, tôi muốn có thể hủy đăng ký khóa học trực tiếp từ modal chi tiết, để tôi có thể thực hiện hành động này một cách thuận tiện.

#### Acceptance Criteria

1. WHEN modal chi tiết được hiển thị THEN hệ thống SHALL hiển thị nút "Hủy đăng ký" với màu đỏ
2. WHEN người dùng nhấn "Hủy đăng ký" THEN hệ thống SHALL hiển thị dialog xác nhận
3. WHEN người dùng xác nhận hủy đăng ký THEN hệ thống SHALL gọi API hủy đăng ký
4. WHEN hủy đăng ký thành công THEN hệ thống SHALL đóng modal và cập nhật danh sách khóa học
5. WHEN hủy đăng ký thất bại THEN hệ thống SHALL hiển thị thông báo lỗi