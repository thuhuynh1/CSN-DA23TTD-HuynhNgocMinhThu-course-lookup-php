/* ===== MAIN JAVASCRIPT - UNIFIED SCRIPT FOR ALL PAGES ===== */

// Global variables
let currentUser = null;
let currentView = 'grid';
let filteredCourses = [];

// Course data
const coursesData = [
    {
        id: 1,
        title: 'Tiếng Anh giao tiếp',
        description: 'Phát triển kỹ năng giao tiếp tiếng Anh tự tin trong mọi tình huống. Khóa học tập trung vào việc cải thiện khả năng nói, nghe và phản xạ giao tiếp.',
        category: 'language',
        price: 1500000,
        duration: 6,
        icon: 'fas fa-comments',
        features: [
            'Lớp nhỏ 8-12 học viên',
            'Giáo viên bản ngữ',
            'Thời gian linh hoạt',
            'Tài liệu học tập miễn phí',
            'Thực hành giao tiếp hàng ngày'
        ],
        level: 'Cơ bản đến nâng cao',
        schedule: 'Thứ 2, 4, 6 - 19:00-21:00'
    },
    {
        id: 2,
        title: 'IELTS/TOEIC',
        description: 'Luyện thi chứng chỉ quốc tế với phương pháp hiệu quả. Cam kết đầu ra với điểm số mục tiêu và phương pháp học tập khoa học.',
        category: 'language',
        price: 2000000,
        duration: 4,
        icon: 'fas fa-certificate',
        features: [
            'Cam kết đầu ra',
            'Tài liệu độc quyền',
            'Mock test hàng tuần',
            'Chấm chữa bài chi tiết',
            'Hỗ trợ đăng ký thi'
        ],
        level: 'Trung cấp đến cao cấp',
        schedule: 'Thứ 3, 5, 7 - 18:30-20:30'
    },
    {
        id: 3,
        title: 'Tiếng Nhật - Hàn - Trung',
        description: 'Học ngôn ngữ châu Á với giáo trình chuẩn quốc tế. Kết hợp học ngôn ngữ và văn hóa để có trải nghiệm học tập toàn diện.',
        category: 'language',
        price: 1800000,
        duration: 8,
        icon: 'fas fa-language',
        features: [
            'Từ cơ bản đến nâng cao',
            'Văn hóa và giao tiếp',
            'Hỗ trợ du học',
            'Giáo viên bản ngữ',
            'Chứng chỉ quốc tế'
        ],
        level: 'Cơ bản đến nâng cao',
        schedule: 'Thứ 2, 4, 6 - 18:00-20:00'
    },
    {
        id: 4,
        title: 'Lập trình Web',
        description: 'Học HTML, CSS, JavaScript và các framework hiện đại. Xây dựng website từ cơ bản đến nâng cao với các dự án thực tế.',
        category: 'it',
        price: 2500000,
        duration: 6,
        icon: 'fas fa-code',
        features: [
            'Dự án thực tế',
            'Mentor 1-1',
            'Hỗ trợ tìm việc',
            'Học framework hiện đại',
            'Portfolio cá nhân'
        ],
        level: 'Cơ bản đến nâng cao',
        schedule: 'Thứ 3, 5, 7 - 19:00-21:30'
    },
    {
        id: 5,
        title: 'Quản trị Cơ sở dữ liệu',
        description: 'MySQL, PostgreSQL, MongoDB và các kỹ thuật tối ưu. Học cách thiết kế, quản lý và tối ưu hóa cơ sở dữ liệu chuyên nghiệp.',
        category: 'it',
        price: 2200000,
        duration: 4,
        icon: 'fas fa-database',
        features: [
            'Thực hành trên server',
            'Case study thực tế',
            'Chứng chỉ quốc tế',
            'Tối ưu hóa hiệu suất',
            'Backup và recovery'
        ],
        level: 'Trung cấp đến cao cấp',
        schedule: 'Thứ 2, 4, 6 - 18:30-21:00'
    },
    {
        id: 6,
        title: 'Tin học văn phòng',
        description: 'Word, Excel, PowerPoint từ cơ bản đến nâng cao. Nâng cao hiệu quả công việc với các kỹ năng tin học văn phòng chuyên nghiệp.',
        category: 'it',
        price: 800000,
        duration: 3,
        icon: 'fas fa-chart-line',
        features: [
            'Ứng dụng thực tế',
            'Macro và VBA',
            'Chứng chỉ MOS',
            'Thao tác nâng cao',
            'Mẫu văn bản chuyên nghiệp'
        ],
        level: 'Cơ bản đến nâng cao',
        schedule: 'Thứ 7, Chủ nhật - 14:00-17:00'
    }
];

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    checkAuthentication();
    
    // Initialize page-specific functionality
    const currentPage = getCurrentPage();
    
    switch(currentPage) {
        case 'index':
            initializeHomePage();
            break;
        case 'courses':
            initializeCoursesPage();
            break;
        case 'admin':
            initializeAdminPage();
            break;
        case 'admin-login':
            initializeAdminLogin();
            break;
    }
    

});

// Get current page
function getCurrentPage() {
    const path = window.location.pathname;
    const page = path.split('/').pop().split('.')[0];
    return page || 'index';
}

// Initialize app
function initializeApp() {
    // Add notification styles
    addNotificationStyles();
    
    // Initialize filtered courses
    filteredCourses = [...coursesData];
}

// Check authentication on page load
function checkAuthentication() {
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        updateAuthUI();
    }
}

// ===== EVENT LISTENERS =====
function setupEventListeners() {
    // Mobile navigation
    setupMobileNavigation();
    
    // Smooth scrolling
    setupSmoothScrolling();
    
    // Header scroll effect
    setupHeaderScrollEffect();
    
    // Modal functionality
    setupModalFunctionality();
    
    // Authentication forms
    setupAuthenticationForms();
    
    // Contact form
    setupContactForm();
    
    // Animation observers
    setupAnimationObservers();
    
    // Keyboard navigation
    setupKeyboardNavigation();
    
    // Print styles
    setupPrintStyles();
}

// Mobile navigation
function setupMobileNavigation() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    }
}

// Smooth scrolling
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Header scroll effect
function setupHeaderScrollEffect() {
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.header');
        if (header) {
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        }
    });
}

// Modal functionality - No longer needed for separate pages
function setupModalFunctionality() {
    // Modal functionality is no longer needed
}

// Authentication forms - No longer needed for separate pages
function setupAuthenticationForms() {
    // Authentication is now handled on separate pages
}

// Contact form
function setupContactForm() {
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactForm);
    }
}

// Animation observers
function setupAnimationObservers() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.course-card, .feature, .stat-item, .course-preview-card');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Stats counter animation
    setupStatsAnimation();
}

// Stats animation
function setupStatsAnimation() {
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-item h3, .stat-card h3');
                statNumbers.forEach(stat => {
                    const text = stat.textContent;
                    const number = parseInt(text.replace(/\D/g, ''));
                    if (number > 0) {
                        stat.textContent = '0' + (text.includes('+') ? '+' : '') + 
                                          (text.includes('%') ? '%' : '');
                        animateCounter(stat, number, text);
                    }
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats');
    
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
}

// Counter animation
function animateCounter(element, target, originalText, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = originalText;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + (originalText.includes('+') ? '+' : '') + 
                                 (originalText.includes('%') ? '%' : '');
        }
    }, 16);
}

// Keyboard navigation
function setupKeyboardNavigation() {
    document.addEventListener('keydown', (e) => {
        const navMenu = document.querySelector('.nav-menu');
        if (e.key === 'Escape' && navMenu && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
        }
    });
}

// Print styles
function setupPrintStyles() {
    window.addEventListener('beforeprint', () => {
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', () => {
        document.body.classList.remove('printing');
    });
}

// ===== AUTHENTICATION =====





// Simplified validation - removed complex validation functions

// Update UI based on authentication state
function updateAuthUI() {
    const authButtons = document.querySelector('.auth-buttons');
    
    if (currentUser && authButtons) {
        // Hide auth buttons
        authButtons.style.display = 'none';
        
        // Update user dashboard if exists
        const userName = document.getElementById('userName');
        const userEmail = document.getElementById('userEmail');
        const userPhone = document.getElementById('userPhone');
        
        if (userName) userName.textContent = currentUser.name || currentUser.firstName + ' ' + currentUser.lastName;
        if (userEmail) userEmail.textContent = currentUser.email;
        if (userPhone) userPhone.textContent = currentUser.phone || 'Chưa cập nhật';
        
        // Show user menu in navigation
        const userMenu = createUserMenu();
        authButtons.parentNode.appendChild(userMenu);
    } else if (authButtons) {
        // Show auth buttons
        authButtons.style.display = 'flex';
        
        // Remove user menu if exists
        const existingUserMenu = document.querySelector('.user-menu');
        if (existingUserMenu) {
            existingUserMenu.remove();
        }
    }
}

function createUserMenu() {
    const userMenu = document.createElement('div');
    userMenu.className = 'user-menu';
    userMenu.innerHTML = `
        <div class="user-info" onclick="toggleUserMenu()">
            <i class="fas fa-user"></i>
            <span class="user-name">${currentUser.name || currentUser.firstName}</span>
            <i class="fas fa-chevron-down user-menu-arrow"></i>
        </div>
        <div class="user-dropdown">
            <div class="user-email">@${currentUser.username || currentUser.email}</div>
            <a href="my-courses.html" class="user-menu-link">
                <i class="fas fa-book"></i>
                Khóa học của tôi
            </a>
            <button class="btn-logout" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                Đăng xuất
            </button>
        </div>
    `;
    return userMenu;
}

function toggleUserMenu() {
    const dropdown = document.querySelector('.user-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.querySelector('.user-dropdown');
    
    if (userMenu && dropdown && !userMenu.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});

function toggleUserDashboard() {
    const dashboard = document.getElementById('userDashboard');
    if (dashboard) {
        dashboard.classList.toggle('active');
    }
}

function logout() {
    currentUser = null;
    localStorage.removeItem('currentUser');
    sessionStorage.removeItem('currentUser');
    
    const dashboard = document.getElementById('userDashboard');
    if (dashboard) {
        dashboard.classList.remove('active');
    }
    
    updateAuthUI();
    showNotification('Đã đăng xuất thành công!', 'success');
}

// ===== CONTACT FORM =====
function handleContactForm(e) {
    e.preventDefault();
    
    // Get form data
    const name = e.target.querySelector('input[type="text"]').value;
    const email = e.target.querySelector('input[type="email"]').value;
    const phone = e.target.querySelector('input[type="tel"]').value;
    const course = e.target.querySelector('select').value;
    const message = e.target.querySelector('textarea').value;
    
    // Simple validation
    if (!name || !email || !phone) {
        showNotification('Vui lòng điền đầy đủ thông tin bắt buộc!', 'error');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Vui lòng nhập email hợp lệ!', 'error');
        return;
    }
    
    // Phone validation (Vietnamese phone number)
    const phoneRegex = /^[0-9]{10,11}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        showNotification('Vui lòng nhập số điện thoại hợp lệ!', 'error');
        return;
    }
    
    // Save consultation to localStorage
    const consultations = JSON.parse(localStorage.getItem('consultations') || '[]');
    const consultation = {
        id: Date.now(),
        name: name,
        email: email,
        phone: phone,
        course: course,
        message: message,
        submittedAt: new Date().toISOString(),
        status: 'new'
    };
    
    consultations.push(consultation);
    localStorage.setItem('consultations', JSON.stringify(consultations));
    
    // Simulate form submission
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Đang gửi...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        showNotification('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.', 'success');
        e.target.reset();
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// ===== COURSES PAGE FUNCTIONALITY =====
function initializeCoursesPage() {
    if (getCurrentPage() !== 'courses') return;
    
    filteredCourses = [...coursesData];
    displayCourses();
    setupCoursesEventListeners();
    updateResultsCount();
}

function setupCoursesEventListeners() {
    // Search functionality
    const courseSearch = document.getElementById('courseSearch');
    if (courseSearch) {
        courseSearch.addEventListener('input', filterCourses);
    }
    
    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterCourses);
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortCourses);
    }
    
    // View toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            switchView(view);
        });
    });
    
    // Clear filters
    const clearFilters = document.getElementById('clearFilters');
    if (clearFilters) {
        clearFilters.addEventListener('click', function(e) {
            e.preventDefault();
            clearAllFilters();
        });
    }
}

// Filter courses
function filterCourses() {
    const searchTerm = document.getElementById('courseSearch')?.value.toLowerCase() || '';
    const selectedCategory = document.getElementById('categoryFilter')?.value || '';
    
    filteredCourses = coursesData.filter(course => {
        const matchesSearch = course.title.toLowerCase().includes(searchTerm) ||
                            course.description.toLowerCase().includes(searchTerm) ||
                            course.features.some(feature => feature.toLowerCase().includes(searchTerm));
        
        const matchesCategory = !selectedCategory || course.category === selectedCategory;
        
        return matchesSearch && matchesCategory;
    });
    
    displayCourses();
    updateResultsCount();
}

// Sort courses
function sortCourses() {
    const sortBy = document.getElementById('sortSelect')?.value || 'name';
    
    switch(sortBy) {
        case 'name':
            filteredCourses.sort((a, b) => a.title.localeCompare(b.title));
            break;
        case 'price-low':
            filteredCourses.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            filteredCourses.sort((a, b) => b.price - a.price);
            break;
        case 'duration':
            filteredCourses.sort((a, b) => a.duration - b.duration);
            break;
    }
    
    displayCourses();
}

// Switch view
function switchView(view) {
    currentView = view;
    
    // Update active button
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-view="${view}"]`)?.classList.add('active');
    
    // Show/hide appropriate container
    const gridContainer = document.getElementById('coursesGrid');
    const listContainer = document.getElementById('coursesList');
    
    if (gridContainer && listContainer) {
        if (view === 'grid') {
            gridContainer.classList.add('active');
            listContainer.classList.remove('active');
        } else {
            gridContainer.classList.remove('active');
            listContainer.classList.add('active');
        }
    }
    
    displayCourses();
}

// Display courses
function displayCourses() {
    if (filteredCourses.length === 0) {
        showNoResults();
        return;
    }
    
    hideNoResults();
    
    if (currentView === 'grid') {
        displayGridView();
    } else {
        displayListView();
    }
}

// Display grid view
function displayGridView() {
    const container = document.getElementById('coursesGrid');
    if (!container) return;
    
    container.innerHTML = filteredCourses.map(course => `
        <div class="course-card-detailed">
            <div class="course-image">
                <i class="${course.icon}"></i>
            </div>
            <div class="course-content">
                <span class="course-category-badge">
                    ${course.category === 'language' ? 'Ngoại ngữ' : 'Tin học'}
                </span>
                <h3 class="course-title">${course.title}</h3>
                <p class="course-description">${course.description}</p>
                
                <ul class="course-features">
                    ${course.features.slice(0, 3).map(feature => `<li>${feature}</li>`).join('')}
                </ul>
                
                <div class="course-footer">
                    <div>
                        <div class="course-price">${course.price.toLocaleString('vi-VN')}đ/tháng</div>
                        <div class="course-duration">${course.duration} tháng</div>
                    </div>
                    <button class="enroll-btn" onclick="enrollCourse(${course.id})">
                        Đăng ký ngay
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Display list view
function displayListView() {
    const container = document.getElementById('coursesList');
    if (!container) return;
    
    container.innerHTML = filteredCourses.map(course => `
        <div class="course-list-item">
            <div class="course-list-icon">
                <i class="${course.icon}"></i>
            </div>
            <div class="course-list-content">
                <span class="course-category-badge">
                    ${course.category === 'language' ? 'Ngoại ngữ' : 'Tin học'}
                </span>
                <h3 class="course-title">${course.title}</h3>
                <p class="course-description">${course.description}</p>
                <div style="display: flex; gap: 2rem; margin-top: 1rem; font-size: 0.9rem; color: #64748b;">
                    <span><i class="fas fa-clock"></i> ${course.duration} tháng</span>
                    <span><i class="fas fa-signal"></i> ${course.level}</span>
                    <span><i class="fas fa-calendar"></i> ${course.schedule}</span>
                </div>
            </div>
            <div class="course-list-actions">
                <div class="course-price">${course.price.toLocaleString('vi-VN')}đ/tháng</div>
                <button class="enroll-btn" onclick="enrollCourse(${course.id})">
                    Đăng ký ngay
                </button>
            </div>
        </div>
    `).join('');
}

// Show/hide no results
function showNoResults() {
    const noResults = document.getElementById('noResults');
    const coursesGrid = document.getElementById('coursesGrid');
    const coursesList = document.getElementById('coursesList');
    
    if (noResults) noResults.style.display = 'block';
    if (coursesGrid) coursesGrid.style.display = 'none';
    if (coursesList) coursesList.style.display = 'none';
}

function hideNoResults() {
    const noResults = document.getElementById('noResults');
    const coursesGrid = document.getElementById('coursesGrid');
    const coursesList = document.getElementById('coursesList');
    
    if (noResults) noResults.style.display = 'none';
    if (coursesGrid) coursesGrid.style.display = currentView === 'grid' ? 'grid' : 'none';
    if (coursesList) coursesList.style.display = currentView === 'list' ? 'flex' : 'none';
}

// Update results count
function updateResultsCount() {
    const resultsCount = document.getElementById('resultsCount');
    if (resultsCount) {
        const count = filteredCourses.length;
        const countText = count === 1 ? '1 khóa học' : `${count} khóa học`;
        resultsCount.textContent = `Hiển thị ${countText}`;
    }
}

// Clear all filters
function clearAllFilters() {
    const courseSearch = document.getElementById('courseSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortSelect = document.getElementById('sortSelect');
    
    if (courseSearch) courseSearch.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (sortSelect) sortSelect.value = 'name';
    
    filteredCourses = [...coursesData];
    sortCourses();
    updateResultsCount();
}

// Enroll in course
function enrollCourse(courseId) {
    if (!currentUser) {
        // Redirect to login page if not logged in
        showNotification('Vui lòng đăng nhập để đăng ký khóa học!', 'error');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
        return;
    }
    
    const course = coursesData.find(c => c.id === courseId);
    if (!course) return;
    
    // Check if already enrolled
    const enrollments = JSON.parse(localStorage.getItem('enrollments') || '[]');
    const existingEnrollment = enrollments.find(e => 
        e.userId === currentUser.email && e.courseId === courseId
    );
    
    if (existingEnrollment) {
        showNotification('Bạn đã đăng ký khóa học này rồi!', 'error');
        return;
    }
    
    // Create enrollment
    const enrollment = {
        id: Date.now(),
        userId: currentUser.email,
        courseId: courseId,
        courseName: course.title,
        studentName: `${currentUser.firstName} ${currentUser.lastName}`,
        enrolledAt: new Date().toISOString(),
        status: 'pending'
    };
    
    enrollments.push(enrollment);
    localStorage.setItem('enrollments', JSON.stringify(enrollments));
    
    // Show success message and redirect
    showNotification(`Đăng ký khóa học "${course.title}" thành công! Đang chuyển đến trang quản lý...`, 'success');
    
    // Redirect to dashboard after 2 seconds
    setTimeout(() => {
        window.location.href = 'my-courses.html';
    }, 2000);
}

// ===== HOME PAGE FUNCTIONALITY =====
function initializeHomePage() {
    if (getCurrentPage() !== 'index') return;
    
    // Course card hover effects
    document.querySelectorAll('.course-card, .course-preview-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add click to call functionality for phone numbers
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Bạn có muốn gọi điện thoại đến số ' + this.textContent + '?')) {
                e.preventDefault();
            }
        });
    });
}

// ===== ADMIN FUNCTIONALITY =====
function initializeAdminPage() {
    if (getCurrentPage() !== 'admin') return;
    
    // TẮTT KIỂM TRA ADMIN - TRÁNH REDIRECT LOOP
    console.log('Admin page loaded - skipping authentication check');
    
    // Initialize admin panel trực tiếp
    loadAdminDashboard();
    setupAdminEventListeners();
}

function initializeAdminLogin() {
    if (getCurrentPage() !== 'admin-login') return;
    
    const adminLoginForm = document.getElementById('adminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', handleAdminLogin);
    }
}

function handleAdminLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('adminEmail').value;
    const password = document.getElementById('adminPassword').value;
    const errorMessage = document.getElementById('errorMessage');
    const loginBtn = document.getElementById('loginBtn');
    
    // Clear previous error
    if (errorMessage) errorMessage.style.display = 'none';
    
    // Disable button
    if (loginBtn) {
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
    }
    
    // Simulate login delay
    setTimeout(() => {
        // Check admin credentials
        if (email === 'admin@abccenter.edu.vn' && password === 'admin123') {
            // Create admin user object
            const adminUser = {
                firstName: 'Admin',
                lastName: 'User',
                email: email,
                role: 'admin',
                loginAt: new Date().toISOString()
            };
            
            // Save to localStorage
            localStorage.setItem('currentUser', JSON.stringify(adminUser));
            
            // Redirect to admin panel
            window.location.href = 'admin.html';
        } else {
            // Show error
            if (errorMessage) {
                errorMessage.textContent = 'Email hoặc mật khẩu không đúng!';
                errorMessage.style.display = 'block';
            }
            
            // Re-enable button
            if (loginBtn) {
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng nhập';
            }
        }
    }, 1000);
}

function loadAdminDashboard() {
    // Load admin dashboard data
    const users = JSON.parse(localStorage.getItem('registeredUsers') || '[]');
    const enrollments = JSON.parse(localStorage.getItem('enrollments') || '[]');
    const consultations = JSON.parse(localStorage.getItem('consultations') || '[]');
    
    // Update stats
    const totalUsers = document.getElementById('totalUsers');
    const totalEnrollments = document.getElementById('totalEnrollments');
    const totalConsultations = document.getElementById('totalConsultations');
    
    if (totalUsers) totalUsers.textContent = users.length;
    if (totalEnrollments) totalEnrollments.textContent = enrollments.length;
    if (totalConsultations) totalConsultations.textContent = consultations.length;
}

function setupAdminEventListeners() {
    // Admin menu navigation
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function() {
            const section = this.dataset.section;
            showAdminSection(section);
            
            // Update active menu item
            document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function showAdminSection(sectionName) {
    document.querySelectorAll('.admin-section').forEach(section => {
        section.classList.remove('active');
    });
    
    const targetSection = document.getElementById(sectionName);
    if (targetSection) {
        targetSection.classList.add('active');
    }
}

// ===== NOTIFICATION SYSTEM =====
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 
                              type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add notification styles
function addNotificationStyles() {
    const notificationStyles = `
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 3000;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        }
        
        .notification-success {
            background: #10b981;
            color: white;
        }
        
        .notification-error {
            background: #ef4444;
            color: white;
        }
        
        .notification-info {
            background: #2563eb;
            color: white;
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .notification.fade-out {
            animation: slideOutRight 0.3s ease;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;

    // Add styles to head
    const styleSheet = document.createElement('style');
    styleSheet.textContent = notificationStyles;
    document.head.appendChild(styleSheet);
}

// ===== UTILITY FUNCTIONS =====
function closeAdminModal(modalId) {
    closeModal(modalId);
}

function adminLogout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        localStorage.removeItem('currentUser');
        sessionStorage.removeItem('currentUser');
        window.location.href = 'index.html';
    }
}

// Create demo user for testing
function createDemoUser() {
    const users = JSON.parse(localStorage.getItem('registeredUsers') || '[]');
    
    // Check if demo user already exists
    if (!users.find(u => u.email === 'demo@test.com' || u.username === 'demo')) {
        const demoUser = {
            name: 'Demo User',
            username: 'demo',
            email: 'demo@test.com',
            password: '123',
            role: 'student',
            registeredAt: new Date().toISOString()
        };
        
        users.push(demoUser);
        localStorage.setItem('registeredUsers', JSON.stringify(users));
        showNotification('Demo user created! Username: demo, Email: demo@test.com, Password: 123', 'info');
    }
}

// Make functions and data globally available
window.toggleUserDashboard = toggleUserDashboard;
window.toggleUserMenu = toggleUserMenu;
window.logout = logout;
window.enrollCourse = enrollCourse;
window.closeAdminModal = closeAdminModal;
window.adminLogout = adminLogout;
window.createDemoUser = createDemoUser;
window.coursesData = coursesData;