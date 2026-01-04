# Design Document

## Overview

Cập nhật modal "Xem chi tiết" khóa học trong trang my-courses.html để hiển thị thông tin chính xác và đầy đủ hơn. Modal sẽ được cải thiện về mặt giao diện và chức năng để mang lại trải nghiệm tốt hơn cho người dùng.

## Architecture

### Current Architecture
- Modal hiện tại sử dụng dữ liệu từ `window.coursesData` được load từ API `dp/get_my_courses.php`
- Thông tin được hiển thị thông qua JavaScript function `showCourseDetail()`
- Modal có cấu trúc HTML tĩnh với các element được cập nhật động

### Updated Architecture
- Giữ nguyên cấu trúc hiện tại nhưng cải thiện cách hiển thị dữ liệu
- Cập nhật logic tính toán tiến trình học tập
- Cải thiện validation và error handling
- Tối ưu hóa responsive design

## Components and Interfaces

### 1. Modal Structure
```html
<div id="courseDetailModal" class="modal-overlay">
  <div class="modal-container">
    <div class="modal-header">
      <!-- Course title and close button -->
    </div>
    <div class="modal-body">
      <!-- Status section -->
      <!-- Info grid (4 cards) -->
      <!-- Description section -->
      <!-- Features section -->
      <!-- Progress section (conditional) -->
      <!-- Contact section -->
    </div>
    <div class="modal-footer">
      <!-- Action buttons -->
    </div>
  </div>
</div>
```

### 2. JavaScript Functions
- `showCourseDetail(courseId, status, enrollDate)` - Main function to display modal
- `calculateProgress(enrollDate, duration)` - Calculate learning progress
- `formatCurrency(amount)` - Format price display
- `formatFeatures(features)` - Format features list
- `closeCourseDetail()` - Close modal function

### 3. Data Flow
```
coursesData (from API) → showCourseDetail() → Update Modal Elements → Display Modal
```

## Data Models

### Course Data Structure
```javascript
{
  id: number,
  title: string,
  description: string,
  price: number,
  duration: number,
  level: string,
  schedule: string,
  features: array,
  status: string,
  enrollDate: string
}
```

### Progress Calculation
```javascript
{
  percentage: number (0-100),
  remainingMonths: number,
  elapsedDays: number,
  totalDays: number
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Modal Data Consistency
*For any* course data loaded from API, when displaying in modal, all displayed information should match the source data exactly
**Validates: Requirements 1.1, 1.2**

### Property 2: Status-based Progress Display
*For any* course with status "Đã xác nhận", the progress section should be visible; for status "Chờ xác nhận", the progress section should be hidden
**Validates: Requirements 3.1, 3.2**

### Property 3: Progress Calculation Accuracy
*For any* confirmed course, the calculated progress percentage should be between 0-100 and based on actual enrollment duration
**Validates: Requirements 3.3, 3.4**

### Property 4: Feature List Display
*For any* course with features array, each feature should be displayed as a list item; for empty features, the section should be hidden
**Validates: Requirements 2.2, 2.3**

### Property 5: Contact Information Accessibility
*For any* displayed contact information, phone numbers should be clickable tel: links and emails should be clickable mailto: links
**Validates: Requirements 4.4, 4.5**

## Error Handling

### Data Validation
- Check if course data exists before displaying modal
- Validate required fields (title, price, duration)
- Handle missing or null values gracefully
- Display fallback text for empty descriptions

### Progress Calculation
- Handle invalid enrollment dates
- Prevent negative progress values
- Cap progress at 100%
- Handle edge cases for very old or future enrollments

### UI Error States
- Show loading state while data is being processed
- Display error message if course data is corrupted
- Graceful degradation for missing features or descriptions

## Testing Strategy

### Unit Tests
- Test progress calculation with various enrollment dates
- Test currency formatting with different price values
- Test feature list rendering with empty and populated arrays
- Test modal open/close functionality

### Property-Based Tests
- **Property 1**: Generate random course data and verify modal displays match source data
- **Property 2**: Test status-based progress visibility with all possible status values
- **Property 3**: Generate random enrollment dates and verify progress calculations are within bounds
- **Property 4**: Test feature display logic with various array configurations
- **Property 5**: Verify contact link generation for different contact formats

### Integration Tests
- Test modal interaction with course data API
- Test cancel enrollment functionality from modal
- Test responsive behavior on different screen sizes

## Implementation Details

### CSS Updates
- Improve responsive design for mobile devices
- Enhance visual hierarchy with better typography
- Add smooth animations for modal open/close
- Improve accessibility with proper focus management

### JavaScript Enhancements
- Add input validation for all data fields
- Implement proper error handling for API failures
- Add loading states for better user experience
- Optimize performance for large course lists

### Accessibility Improvements
- Add proper ARIA labels for screen readers
- Ensure keyboard navigation works correctly
- Implement focus trapping within modal
- Add high contrast mode support

## Performance Considerations

- Lazy load modal content only when needed
- Cache course data to avoid repeated API calls
- Optimize DOM manipulation for smooth animations
- Minimize reflows and repaints during updates

## Security Considerations

- Sanitize all user-generated content (descriptions, features)
- Validate enrollment IDs before API calls
- Prevent XSS attacks through proper escaping
- Implement CSRF protection for cancel enrollment action