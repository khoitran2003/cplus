# C+ Scoring System API Documentation

## Tổng quan
API này cung cấp các endpoint để quản lý hệ thống chấm điểm đa tiêu chí cho việc so sánh các địa điểm.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Hiện tại API chưa có authentication, sẽ được thêm sau.

## Endpoints

### 1. Criteria Management

#### Lấy danh sách tiêu chí
```http
GET /api/criteria
```

**Query Parameters:**
- `type_id`: Lọc theo loại tiêu chí
- `client_id`: Lọc theo client
- `industry_id`: Lọc theo ngành công nghiệp
- `hierarchy`: true/false - Bao gồm cấu trúc phân cấp

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "criteria_name": "Land cost",
      "criteriaTypeId": null,
      "parentId": null,
      "clientId": null,
      "criteriaPercent": null,
      "criteriaType": null,
      "parent": null,
      "client": null
    }
  ]
}
```

#### Lấy cấu trúc phân cấp tiêu chí
```http
GET /api/criteria/hierarchy
```

#### Lấy tiêu chí theo loại
```http
GET /api/criteria/by-type/{typeId}
```

#### Lấy tiêu chí theo client
```http
GET /api/criteria/by-client/{clientId}
```

#### Lấy tiêu chí theo ngành
```http
GET /api/criteria/by-industry/{industryId}
```

#### Tạo tiêu chí mới
```http
POST /api/criteria
```

**Body:**
```json
{
  "criteria_name": "New Criteria",
  "criteriaTypeId": 1,
  "parentId": null,
  "clientId": null,
  "criteriaPercent": 25.00
}
```

#### Cập nhật tiêu chí
```http
PUT /api/criteria/{id}
```

#### Xóa tiêu chí
```http
DELETE /api/criteria/{id}
```

### 2. Project Management

#### Lấy danh sách dự án
```http
GET /api/projects
```

**Query Parameters:**
- `client_id`: Lọc theo client
- `user_id`: Lọc theo user

#### Lấy chi tiết dự án
```http
GET /api/projects/{id}
```

#### Lấy dữ liệu chấm điểm cho dự án
```http
GET /api/projects/{id}/scoring
```

#### Lưu điểm chấm điểm
```http
POST /api/projects/{id}/scoring
```

**Body:**
```json
{
  "scores": [
    {
      "criteriaId": 1,
      "criteria_point": 8.5,
      "criteria_percentage": 85,
      "criteria_parent_id": null,
      "criteria_type": 1,
      "criteria_name": "Land cost"
    }
  ]
}
```

#### Xuất báo cáo dự án
```http
GET /api/projects/{id}/export/{format}
```

**Format:** `pdf` hoặc `excel`

#### Lấy thống kê dự án
```http
GET /api/projects/{id}/statistics
```

### 3. Judgment Details Management

#### Lấy danh sách đánh giá
```http
GET /api/judgment/details
```

**Query Parameters:**
- `project_id`: Lọc theo dự án
- `criteria_id`: Lọc theo tiêu chí
- `session_id`: Lọc theo phiên

#### Tạo đánh giá mới
```http
POST /api/judgment/details
```

#### Cập nhật đánh giá
```http
PUT /api/judgment/details/{id}
```

#### Xóa đánh giá
```http
DELETE /api/judgment/details/{id}
```

#### Cập nhật hàng loạt
```http
POST /api/judgment/bulk-update
```

#### Lấy đánh giá theo dự án
```http
GET /api/judgment/by-project/{projectId}
```

#### Lấy đánh giá theo tiêu chí
```http
GET /api/judgment/by-criteria/{criteriaId}
```

#### Lấy tóm tắt chấm điểm
```http
GET /api/judgment/scoring-summary/{projectId}
```

### 4. Dashboard & Statistics

#### Thống kê tổng quan
```http
GET /api/dashboard/statistics
```

#### Dự án gần đây
```http
GET /api/dashboard/recent-projects
```

#### Trạng thái chấm điểm
```http
GET /api/dashboard/scoring-status
```

#### So sánh dự án
```http
GET /api/dashboard/project-comparison
```

#### Sử dụng tiêu chí
```http
GET /api/dashboard/criteria-usage
```

#### Thống kê client
```http
GET /api/dashboard/client-statistics
```

#### Thống kê hàng tháng
```http
GET /api/dashboard/monthly-statistics?months=12
```

#### Top địa điểm
```http
GET /api/dashboard/top-locations
```

#### Xu hướng chấm điểm
```http
GET /api/dashboard/scoring-trends?criteria_id=1&project_id=1
```

### 5. Master Data

#### Lấy danh sách loại tiêu chí
```http
GET /api/criteria/types
```

#### Lấy danh sách ngành công nghiệp
```http
GET /api/industries
```

## Response Format

Tất cả API responses đều có format chuẩn:

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Examples

### Tạo một project scoring hoàn chỉnh

1. **Lấy criteria hierarchy:**
```http
GET /api/criteria/hierarchy
```

2. **Tạo judgment details:**
```http
POST /api/judgment/details
```

3. **Lưu scoring:**
```http
POST /api/projects/{id}/scoring
```

4. **Xem kết quả:**
```http
GET /api/projects/{id}/statistics
```

### Dashboard Integration

```javascript
// Lấy thống kê dashboard
fetch('/api/dashboard/statistics')
  .then(response => response.json())
  .then(data => {
    console.log('Dashboard stats:', data.data);
  });

// Lấy project comparison chart
fetch('/api/dashboard/project-comparison')
  .then(response => response.json())
  .then(data => {
    // Render chart với data.data
  });
```

## Notes

- Tất cả timestamps đều ở format ISO 8601
- Pagination được hỗ trợ cho các endpoint list
- Soft delete được sử dụng cho hầu hết models
- Foreign key constraints được enforce ở database level
