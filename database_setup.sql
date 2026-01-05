-- =============================================
-- Database Setup Script for ABC Center (Updated from XAMPP)
-- Generated: 2026-01-05 13:57:08
-- =============================================

-- Tạo database (nếu chưa có)
CREATE DATABASE IF NOT EXISTS user_auth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE user_auth_db;

-- =============================================
-- 1. BẢNG USERS
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng users
INSERT IGNORE INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`) VALUES
('6', 'Lê Văn Thuận', 'thuan@gmail.com', '$2y$10$qlQV2HiCGb3YOMvm1.xPkOkwcqm69x6ERmut65ujnZM.YMtmCn956', '2025-12-21 14:59:56'),
('7', 'Võ Phạm Quang Trường', 'truong@gmail.com', '$2y$10$zJZwf6U0IkWwn1T86Py5lOaqIMiA9uFM7wCgz2XmwaZxStiF4K2V2', '2025-12-21 15:00:45'),
('8', 'Hem Sa Ra Vuth', 'vuth@gmail.com', '$2y$10$K3pBOMNxMx/3nvaIVSdPs.Om/TRpb8LQOs64LAeHZ7nlKJBTl7GRe', '2025-12-21 15:01:17');

-- =============================================
-- 2. BẢNG SESSIONS
-- =============================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng sessions
INSERT IGNORE INTO `sessions` (`id`, `user_id`, `session_token`, `created_at`, `expires_at`) VALUES
('17', '6', 'e28365894d4e4ed10af2572e4df8a96cd511cb92e94a6a07354a77c6ddd0cf1b', '2025-12-21 15:25:37', '2025-12-21 10:25:37'),
('21', '6', '689b0a0f20f9338ba0aff52b800b876990ba0925d1714f665b9e323ca7fd0df6', '2025-12-23 11:11:35', '2025-12-23 06:11:35'),
('26', '6', '25a11b115f552005ecc37f3353c22d9abc07b67e797c8619aab66c48ba833956', '2025-12-26 14:39:54', '2025-12-26 09:39:54'),
('28', '6', '5d1eaea2bce7e32c4ab0d146ec0de6fce0ef8899d09d7a215355a2528e87cd85', '2026-01-02 14:35:34', '2026-01-02 09:35:34'),
('29', '6', '5a0cdeaa4611ca809678ac6eba581b51510977235482d1b4aae180d695c671cc', '2026-01-04 20:28:19', '2026-01-04 15:28:19');

-- =============================================
-- 3. BẢNG ADMINS
-- =============================================
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','super_admin') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng admins
INSERT IGNORE INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
('2', 'adthu', 'adthu@gmail.com', '$2y$10$MvptaaeDjrg8/CwO4Q.Hm.GRFH1dx9yUmkn9f0C61hdtx1VF6hin2', 'Huỳnh Ngọc Minh Thư', 'admin', 'active', '2026-01-04 20:37:21', '2025-12-21 15:07:15', '2026-01-04 20:37:21');

-- =============================================
-- 4. BẢNG COURSES
-- =============================================
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Thời lượng tính bằng tháng',
  `level` varchar(50) DEFAULT 'Cơ bản',
  `schedule` varchar(255) DEFAULT 'Thứ 2, 4, 6 - 19:00-21:00',
  `features` text DEFAULT NULL COMMENT 'JSON array của các tính năng',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng courses
INSERT IGNORE INTO `courses` (`id`, `title`, `description`, `category`, `price`, `duration`, `level`, `schedule`, `features`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Tiếng Anh giao tiếp', 'Phát triển kỹ năng giao tiếp tiếng Anh tự tin trong mọi tình huống', 'language', '1500000.00', '3', 'Cơ bản', 'Thứ 2, 6 - 19:00-21:00', '[\"L\\u1edbp h\\u1ecdc nh\\u1ecf t\\u1ed1i \\u0111a 15 h\\u1ecdc vi\\u00ean\",\"Gi\\u1ea3ng vi\\u00ean b\\u1ea3n ng\\u1eef\",\"T\\u00e0i li\\u1ec7u h\\u1ecdc t\\u1eadp mi\\u1ec5n ph\\u00ed\",\"Ch\\u1ee9ng ch\\u1ec9 ho\\u00e0n th\\u00e0nh\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:56:04'),
('2', 'Luyện Thi IELTS', 'Khóa học luyện thi IELTS với mục tiêu đạt 6.5+ trong 4 kỹ năng', 'language', '2500000.00', '3', 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-20:30', '[\"Mock test h\\u00e0ng tu\\u1ea7n\",\"Ch\\u1ea5m b\\u00e0i writing chi ti\\u1ebft\",\"Speaking 1-1 v\\u1edbi gi\\u1ea3ng vi\\u00ean\",\"T\\u00e0i li\\u1ec7u Cambridge ch\\u00ednh th\\u1ee9c\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:38:38'),
('3', 'Tiếng Nhật Cơ Bản', 'Học tiếng Nhật từ cơ bản, chuẩn bị cho kỳ thi JLPT N5-N4', 'language', '1800000.00', '3', 'Cơ bản', 'Thứ 2, 4, 6 - 18:00-20:00', '[\"H\\u1ecdc Hiragana, Katakana t\\u1eeb \\u0111\\u1ea7u\",\"Ng\\u1eef ph\\u00e1p c\\u01a1 b\\u1ea3n\",\"T\\u1eeb v\\u1ef1ng theo ch\\u1ee7 \\u0111\\u1ec1\",\"Luy\\u1ec7n nghe v\\u1edbi audio b\\u1ea3n ng\\u1eef\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:38:45'),
('4', 'Lập trình Web Frontend', 'Học HTML, CSS, JavaScript và React để trở thành Frontend Developer', 'it', '2500000.00', '3', 'Cơ bản', 'Thứ 3, 5, 7 - 19:00-21:30', '[\"D\\u1ef1 \\u00e1n th\\u1ef1c t\\u1ebf\",\"Code review t\\u1eeb mentor\",\"Portfolio c\\u00e1 nh\\u00e2n\",\"H\\u1ed7 tr\\u1ee3 t\\u00ecm vi\\u1ec7c\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:38:53'),
('5', 'Lập trình Web Backend', 'Học PHP, MySQL, Node.js để phát triển ứng dụng web backend', 'it', '2800000.00', '3', 'Trung cấp', 'Thứ 2, 4, 6 - 19:00-21:30', '[\"API Development\",\"Database Design\",\"Security Best Practices\",\"Deploy l\\u00ean server th\\u1eadt\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:39:00'),
('6', 'Python', 'Học Python, Pandas, NumPy để phân tích dữ liệu và machine learning', 'it', '3000000.00', '3', 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-21:00', '[\"Jupyter Notebook\",\"Real datasets\",\"Machine Learning c\\u01a1 b\\u1ea3n\",\"Visualization v\\u1edbi Matplotlib\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:39:08'),
('7', 'Tin học văn phòng', 'Word, Excel, PowerPoint từ cơ bản đến nâng cao cho công việc', 'it', '800000.00', '3', 'Cơ bản', 'Thứ 7, Chủ nhật - 14:00-17:00', '[\"Microsoft Office 365\", \"Templates chuyên nghiệp\", \"Macro và VBA cơ bản\", \"Chứng chỉ MOS\"]', 'active', '2025-12-16 18:35:43', '2025-12-16 18:35:43'),
('8', 'Photoshop & Illustrator', 'Thiết kế đồ họa chuyên nghiệp với Adobe Creative Suite', 'it', '2200000.00', '3', 'Cơ bản', 'Thứ 3, 5, 7 - 18:00-20:30', '[\"Adobe CC License\",\"Project th\\u1ef1c t\\u1ebf\",\"Portfolio design\",\"Print & Digital design\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:39:13'),
('10', 'Giao Tiếp Tiếng Hàn', 'Học tiếng Hàn từ cơ bản, Giao tiếp với người bản địa', 'language', '1700000.00', '3', 'Cơ bản', 'Thứ 3, 5, 7 - 18:30-20:30', '[\"Hangeul t\\u1eeb \\u0111\\u1ea7u\",\"Ng\\u1eef ph\\u00e1p c\\u01a1 b\\u1ea3n\",\"V\\u0103n h\\u00f3a H\\u00e0n Qu\\u1ed1c\",\"Mock test Topik\"]', 'active', '2025-12-16 18:35:43', '2026-01-02 13:39:27');

-- =============================================
-- 5. BẢNG ENROLLMENTS
-- =============================================
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_title` varchar(255) NOT NULL,
  `course_price` decimal(10,2) NOT NULL,
  `course_duration` int(11) NOT NULL,
  `course_level` varchar(100) NOT NULL,
  `course_schedule` varchar(255) NOT NULL,
  `course_description` text NOT NULL,
  `course_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`course_features`)),
  `status` enum('pending','approved') DEFAULT 'pending',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_course` (`user_id`,`course_title`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng enrollments
INSERT IGNORE INTO `enrollments` (`id`, `user_id`, `course_title`, `course_price`, `course_duration`, `course_level`, `course_schedule`, `course_description`, `course_features`, `status`, `enrolled_at`, `updated_at`) VALUES
('41', '7', 'Tiếng Anh giao tiếp', '1500000.00', '6', 'Cơ bản', 'Sẽ thông báo sau', 'Phát triển kỹ năng giao tiếp tiếng Anh tự tin trong mọi tình huống', '[]', 'approved', '2025-12-25 23:00:02', '2026-01-04 20:29:31');

-- =============================================
-- 6. BẢNG CONSULTATIONS
-- =============================================
CREATE TABLE IF NOT EXISTS `consultations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `course_interest` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','processing','completed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng consultations trống

-- =============================================
-- 7. BẢNG WEBSITE_ANALYTICS
-- =============================================
CREATE TABLE IF NOT EXISTS `website_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_url` varchar(255) NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_visit_date` (`visit_date`),
  KEY `idx_page_url` (`page_url`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng website_analytics
-- =============================================
-- 8. BẢNG SITE_CONTENT
-- =============================================
CREATE TABLE IF NOT EXISTS `site_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_content_type` (`content_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu cho bảng site_content
INSERT IGNORE INTO `site_content` (`id`, `content_type`, `title`, `content`, `status`, `created_at`, `updated_at`) VALUES
('3', 'contact_info', 'Số Điện thoại', '0931089773
0981334562', 'active', '2025-12-17 15:56:55', '2026-01-02 13:27:34'),
('4', 'announcement', 'Thông báo', 'Khai giảng các lớp mới tháng 1/2025. Ưu đãi 20% học phí cho 50 học viên đầu tiên!', 'active', '2025-12-17 15:56:55', '2025-12-17 15:56:55'),
('7', 'contact_info', 'Địa chỉ', '126, Nguyễn Thiện Thành, Trà Vinh', 'active', '2025-12-17 15:57:27', '2025-12-21 14:41:50'),
('8', 'announcement', 'Thông báo', 'Khai giảng các lớp mới tháng 1/2025. Ưu đãi 20% học phí cho 50 học viên đầu tiên!', 'active', '2025-12-17 15:57:27', '2025-12-17 15:57:27'),
('10', 'contact_info', 'Email', '110123184@st.tvu.edu.vn
minhthuhb@gmail.com', 'active', '2025-12-21 14:43:20', '2025-12-21 14:43:20');

-- =============================================
-- HOÀN THÀNH SETUP
-- =============================================
