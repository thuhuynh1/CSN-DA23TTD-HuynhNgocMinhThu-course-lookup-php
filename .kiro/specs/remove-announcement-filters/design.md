# Design Document

## Overview

Thiết kế lại trang thông báo để loại bỏ các phần tử phức tạp không cần thiết, tạo ra một giao diện đơn giản, tập trung vào nội dung thông báo. Việc này sẽ cải thiện trải nghiệm người dùng và hiệu suất trang.

## Architecture

### Current Architecture
- Trang thông báo với phần hero, phần lọc, và phần nội dung
- JavaScript xử lý lọc thông báo theo trạng thái
- CSS cho các nút lọc và trạng thái hiển thị

### Target Architecture
- Trang thông báo đơn giản với phần hero và phần nội dung
- JavaScript chỉ tải và hiển thị thông báo active
- CSS tối ưu, loại bỏ styles không cần thiết

## Components and Interfaces

### Components to Remove
1. **Filter Section** (`announcements-filters`)
   - HTML section chứa các nút lọc
   - CSS styles cho `.announcements-filters`, `.filters-container`, `.filter-btn`
   - JavaScript functions: `setupFilters()`, `filterAnnouncements()`

2. **Status Display** (`announcement-meta`)
   - HTML div hiển thị trạng thái trong mỗi thẻ thông báo
   - CSS styles cho `.announcement-status`, `.status-active`, `.status-inactive`

### Components to Keep
1. **Hero Section** - Giữ nguyên
2. **Announcement Cards** - Giữ nguyên nhưng loại bỏ phần meta
3. **Loading/Empty States** - Giữ nguyên
4. **Responsive Design** - Giữ nguyên

## Data Models

### Announcement Display Model
```javascript
{
  title: string,           // Tiêu đề thông báo
  content: string,         // Nội dung thông báo  
  created_at: datetime,    // Ngày tạo
  // Loại bỏ: status field không cần thiết cho frontend
}
```

### API Response Model
- Chỉ trả về các thông báo có `status = 'active'`
- Frontend không cần xử lý logic lọc

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Filter removal completeness
*For any* page load, the announcements page should not contain any filter-related HTML elements, CSS classes, or JavaScript functions
**Validates: Requirements 1.1, 1.5**

### Property 2: Status display removal
*For any* announcement card displayed, it should not contain status information ("Đang hiển thị" or "Không hiển thị")
**Validates: Requirements 1.2**

### Property 3: Active announcements only
*For any* API call to load announcements, only announcements with active status should be returned and displayed
**Validates: Requirements 2.2**

### Property 4: UI simplification preservation
*For any* page load, the simplified UI should maintain responsive design and animations while removing unnecessary elements
**Validates: Requirements 1.4**

### Property 5: Performance optimization
*For any* page load, the removed CSS and JavaScript should result in faster loading times without affecting core functionality
**Validates: Requirements 2.1**

## Error Handling

### Error Scenarios
1. **API Load Failure**: Hiển thị thông báo lỗi rõ ràng
2. **Empty Data**: Hiển thị empty state phù hợp
3. **Network Issues**: Graceful degradation với thông báo lỗi

### Error Display
- Giữ nguyên cơ chế error handling hiện tại
- Đảm bảo error messages rõ ràng và hữu ích

## Testing Strategy

### Unit Testing
- Test API calls chỉ trả về active announcements
- Test UI rendering không có filter elements
- Test responsive behavior sau khi loại bỏ elements

### Property-Based Testing
- Sử dụng Jest cho JavaScript testing
- Mỗi property test chạy tối thiểu 100 iterations
- Test format: `**Feature: remove-announcement-filters, Property {number}: {property_text}**`

### Integration Testing
- Test full page load và display
- Test error scenarios
- Test responsive design trên các device khác nhau