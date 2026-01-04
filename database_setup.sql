-- =============================================
-- Database Setup Script for ABC Center
-- =============================================

-- Tạo database (nếu chưa có)
CREATE DATABASE IF NOT EXISTS user_auth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE user_auth_db;

-- =============================================
-- 1. BẢNG USERS - Quản lý người dùng
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 2. BẢNG SESSIONS - Quản lý phiên đăng nhập
-- =============================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 3. BẢNG ADMINS - Quản lý admin
-- =============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Thêm admin mặc định (password: admin123)
INSERT IGNORE INTO admins (username, full_name, email, password) VALUES 
('admin', 'Administrator', 'admin@abccenter.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =============================================
-- 4. BẢNG COURSES - Quản lý khóa học
-- =============================================
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL COMMENT 'Thời lượng tính bằng tháng',
    level VARCHAR(50) DEFAULT 'Cơ bản',
    schedule VARCHAR(255) DEFAULT 'Thứ 2, 4, 6 - 19:00-21:00',
    features TEXT COMMENT 'JSON array của các tính năng',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm dữ liệu khóa học mẫu
INSERT IGNORE INTO courses (title, description, category, price, duration, level, schedule, features, status) VALUES
('Tiếng Anh giao tiếp', 'Phát triển kỹ năng giao tiếp tiếng Anh tự tin trong mọi tình huống', 'language', 1500000, 6, 'Cơ bản', 'Thứ 2, 4, 6 - 19:00-21:00', '["Lớp học nhỏ tối đa 15 học viên", "Giảng viên bản ngữ", "Tài liệu học tập miễn phí", "Chứng chỉ hoàn thành"]', 'active'),
('IELTS Preparation', 'Khóa học luyện thi IELTS với mục tiêu đạt 6.5+ trong 4 kỹ năng', 'language', 2500000, 8, 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-20:30', '["Mock test hàng tuần", "Chấm bài writing chi tiết", "Speaking 1-1 với giảng viên", "Tài liệu Cambridge chính thức"]', 'active'),
('Tiếng Nhật N5-N4', 'Học tiếng Nhật từ cơ bản, chuẩn bị cho kỳ thi JLPT N5-N4', 'language', 1800000, 10, 'Cơ bản', 'Thứ 2, 4, 6 - 18:00-20:00', '["Học Hiragana, Katakana từ đầu", "Ngữ pháp cơ bản", "Từ vựng theo chủ đề", "Luyện nghe với audio bản ngữ"]', 'active'),
('Lập trình Web Frontend', 'Học HTML, CSS, JavaScript và React để trở thành Frontend Developer', 'it', 2500000, 8, 'Cơ bản', 'Thứ 3, 5, 7 - 19:00-21:30', '["Dự án thực tế", "Code review từ mentor", "Portfolio cá nhân", "Hỗ trợ tìm việc"]', 'active'),
('Lập trình Web Backend', 'Học PHP, MySQL, Node.js để phát triển ứng dụng web backend', 'it', 2800000, 10, 'Trung cấp', 'Thứ 2, 4, 6 - 19:00-21:30', '["API Development", "Database Design", "Security Best Practices", "Deploy lên server thật"]', 'active'),
('Python cho Data Science', 'Học Python, Pandas, NumPy để phân tích dữ liệu và machine learning', 'it', 3000000, 12, 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-21:00', '["Jupyter Notebook", "Real datasets", "Machine Learning cơ bản", "Visualization với Matplotlib"]', 'active'),
('Tin học văn phòng', 'Word, Excel, PowerPoint từ cơ bản đến nâng cao cho công việc', 'it', 800000, 3, 'Cơ bản', 'Thứ 7, Chủ nhật - 14:00-17:00', '["Microsoft Office 365", "Templates chuyên nghiệp", "Macro và VBA cơ bản", "Chứng chỉ MOS"]', 'active'),
('Photoshop & Illustrator', 'Thiết kế đồ họa chuyên nghiệp với Adobe Creative Suite', 'it', 2200000, 6, 'Cơ bản', 'Thứ 3, 5, 7 - 18:00-20:30', '["Adobe CC License", "Project thực tế", "Portfolio design", "Print & Digital design"]', 'active'),
('Digital Marketing', 'Facebook Ads, Google Ads, SEO và Social Media Marketing', 'marketing', 2000000, 6, 'Cơ bản', 'Thứ 2, 4, 6 - 19:30-21:30', '["Campaign thực tế", "Budget 500k để chạy ads", "Analytics & Reporting", "Certificate từ Google & Facebook"]', 'active'),
('Tiếng Hàn Topik I', 'Học tiếng Hàn từ cơ bản, chuẩn bị cho kỳ thi Topik I', 'language', 1700000, 8, 'Cơ bản', 'Thứ 3, 5, 7 - 18:30-20:30', '["Hangeul từ đầu", "Ngữ pháp cơ bản", "Văn hóa Hàn Quốc", "Mock test Topik"]', 'active');

-- =============================================
-- 5. BẢNG ENROLLMENTS - Quản lý đăng ký khóa học
-- =============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_title VARCHAR(255) NOT NULL,
    course_price DECIMAL(10,2) NOT NULL,
    course_duration INT NOT NULL,
    course_level VARCHAR(50),
    course_schedule VARCHAR(255),
    course_description TEXT,
    course_features TEXT,
    status ENUM('pending', 'active', 'completed') DEFAULT 'pending',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 6. BẢNG CONSULTATIONS - Quản lý tư vấn
-- =============================================
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    course_interest VARCHAR(255),
    message TEXT,
    status ENUM('new', 'processing', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 7. BẢNG WEBSITE_ANALYTICS - Thống kê truy cập
-- =============================================
CREATE TABLE IF NOT EXISTS website_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(255) NOT NULL,
    user_ip VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(255),
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visit_date (visit_date),
    INDEX idx_page_url (page_url)
);

-- =============================================
-- 8. BẢNG SITE_CONTENT - Quản lý nội dung
-- =============================================
CREATE TABLE IF NOT EXISTS site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_content_type (content_type),
    INDEX idx_status (status)
);

-- Thêm nội dung mặc định
INSERT IGNORE INTO site_content (content_type, title, content) VALUES
('homepage_banner', 'Banner trang chủ', 'Chào mừng đến với Trung tâm ABC - Nơi khởi đầu cho hành trình chinh phục ngôn ngữ và công nghệ của bạn'),
('about_us', 'Giới thiệu', 'Trung tâm ABC được thành lập với sứ mệnh đào tạo chất lượng cao trong lĩnh vực ngoại ngữ và tin học'),
('contact_info', 'Thông tin liên hệ', '{"address": "123 Đường ABC, Quận 1, TP.HCM", "phone": "(028) 1234 5678", "email": "info@abccenter.edu.vn"}'),
('announcement', 'Thông báo', 'Khai giảng các lớp mới tháng 1/2025. Ưu đãi 20% học phí cho 50 học viên đầu tiên!');

-- =============================================
-- HOÀN THÀNH SETUP
-- =============================================