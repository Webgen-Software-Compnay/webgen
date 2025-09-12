# 🚀 One Sri Lanka Complaint System API Documentation

## 📖 Overview

The One Sri Lanka Complaint System API provides comprehensive endpoints for managing citizen complaints against government departments, organizations, and private companies in Sri Lanka.

### 🔑 Base URL
```
http://localhost:8080/api
```

### 🔐 Authentication
- **Public endpoints**: No authentication required
- **User endpoints**: Requires Laravel Sanctum token
- **Admin endpoints**: Requires admin privileges + Sanctum token

---

## 📱 API Endpoints

### 🌍 Public Endpoints

#### Get Complaint Categories
```http
GET /public/complaint-categories
```
**Query Parameters:**
- `type` (optional): Filter by category type
- `parent_id` (optional): Filter by parent category

**Response:**
```json
{
  "success": true,
  "data": {
    "main_categories": [...],
    "total_main": 8,
    "total_sub": 12
  }
}
```

#### Get Common Complaint Templates
```http
GET /public/common-complaints
```
**Query Parameters:**
- `category_id` (optional): Filter by category

#### Get Entities (Departments/Organizations)
```http
GET /public/entities
```
**Query Parameters:**
- `type` (optional): Filter by entity type

#### Get Public Statistics
```http
GET /public/statistics
```

---

### 👤 User Endpoints (Authenticated)

#### Submit New Complaint
```http
POST /user/complaints
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "title": "Bus Service Delay Issue",
  "description": "Route 120 bus is frequently delayed by 30+ minutes",
  "complaint_category_id": 2,
  "entity_id": 1,
  "common_complaint_id": 2,
  "priority": "medium",
  "form_data": {
    "route_number": "120",
    "time_of_day": "Morning Peak",
    "delay_duration": "35 minutes"
  }
}
```

#### Get User's Complaints
```http
GET /user/complaints
Authorization: Bearer {token}
```
**Query Parameters:**
- `status` (optional): Filter by status
- `page` (optional): Page number
- `per_page` (optional): Items per page (max 50)

#### Get Specific Complaint
```http
GET /user/complaints/{id}
Authorization: Bearer {token}
```

#### Update Complaint (Only if pending)
```http
PUT /user/complaints/{id}
Authorization: Bearer {token}
```

#### Delete Complaint (Only if pending)
```http
DELETE /user/complaints/{id}
Authorization: Bearer {token}
```

---

### 🛡️ Admin Endpoints (Admin Only)

#### Get All Complaints
```http
GET /admin/complaints
Authorization: Bearer {admin_token}
```
**Query Parameters:**
- `status`: Filter by status
- `priority`: Filter by priority  
- `category_id`: Filter by category
- `entity_id`: Filter by entity
- `search`: Search in title/description/user name
- `date_from`: Filter from date
- `date_to`: Filter to date
- `sort_by`: Sort field (default: submitted_at)
- `sort_direction`: Sort direction (asc/desc)

#### Get Complaint Details
```http
GET /admin/complaints/{id}
Authorization: Bearer {admin_token}
```

#### Update Complaint Status
```http
PUT /admin/complaints/{id}/status
Authorization: Bearer {admin_token}
```

**Request Body:**
```json
{
  "status": "resolved",
  "resolution_notes": "Issue has been resolved"
}
```

#### Get Complaint Statistics
```http
GET /admin/complaints/statistics
Authorization: Bearer {admin_token}
```

#### Delete Complaint
```http
DELETE /admin/complaints/{id}
Authorization: Bearer {admin_token}
```

### 🏢 Entity Management (Admin Only)

#### Get All Entities
```http
GET /admin/entities
Authorization: Bearer {admin_token}
```

#### Create Entity
```http
POST /admin/entities
Authorization: Bearer {admin_token}
```

**Request Body:**
```json
{
  "name": "Ministry of Health",
  "entity_type": "government_dept",
  "description": "Government ministry responsible for health services"
}
```

#### Update Entity
```http
PUT /admin/entities/{id}
Authorization: Bearer {admin_token}
```

#### Delete Entity
```http
DELETE /admin/entities/{id}
Authorization: Bearer {admin_token}
```

#### Get Entity Statistics
```http
GET /admin/entities/statistics
Authorization: Bearer {admin_token}
```

---

## 📊 Data Models

### Complaint Status Values
- `pending`: Newly submitted
- `in_progress`: Being processed
- `resolved`: Successfully resolved
- `rejected`: Rejected with reason

### Priority Levels
- `low`: Non-urgent issues
- `medium`: Standard priority (default)
- `high`: Urgent issues

### Entity Types
- `government_dept`: Government departments
- `organization`: NGOs and organizations
- `private_company`: Private companies
- `civil_cat`: Civil service categories

### Category Types
- `civil_issue`: General civil issues
- `government_department`: Government service issues
- `organization`: Organization-related issues
- `private_company`: Private company issues

---

## 🔒 Error Handling

### Standard Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "errors": {...}
}
```

### HTTP Status Codes
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden (Admin access required)
- `404`: Not Found
- `422`: Validation Error
- `500`: Internal Server Error

---

## 🚀 Quick Start

### 1. Get Public Data
```bash
# Get all complaint categories
curl -X GET "http://localhost:8080/api/public/complaint-categories"

# Get all entities
curl -X GET "http://localhost:8080/api/public/entities"
```

### 2. User Authentication
```bash
# Login to get token (implement via SLUDI OAuth)
# Then use token for protected endpoints

# Submit complaint
curl -X POST "http://localhost:8080/api/user/complaints" \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Bus Service Issue",
    "description": "Frequent delays on Route 120",
    "complaint_category_id": 2,
    "entity_id": 1,
    "priority": "medium"
  }'
```

### 3. Admin Functions
```bash
# Get all complaints (admin only)
curl -X GET "http://localhost:8080/api/admin/complaints" \
  -H "Authorization: Bearer {admin_token}"

# Update complaint status
curl -X PUT "http://localhost:8080/api/admin/complaints/1/status" \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": "resolved"}'
```

---

## 📋 Features

✅ **Hierarchical Complaint Categories**  
✅ **Multi-Entity Support** (Gov/Private/NGO)  
✅ **Template-based Complaints**  
✅ **Priority & Status Management**  
✅ **Advanced Filtering & Search**  
✅ **Real-time Statistics**  
✅ **Admin Dashboard Support**  
✅ **User Permission Management**  
✅ **Comprehensive Error Handling**  
✅ **API Documentation (Swagger)**  

---

**🏛️ Built for Sri Lankan Government Digital Transformation**
