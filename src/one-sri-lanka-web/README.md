# Review Nation Web Application - Common Complaint Management

![Review Nation Logo](https://img.shields.io/badge/Review%20Nation-Web%20App-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-12-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)

## Overview

This is the **web application component** of the Review Nation system that provides a comprehensive interface for managing common complaints and enabling citizens to file complaints through dynamic forms. The application integrates with the Review Nation API to provide a seamless experience for both administrators and the public.

## 🏗️ Architecture

```
Web Application (Laravel 12)
├── 👥 Public Interface (Citizens)
│   ├── 📝 File Complaints with Dynamic Forms
│   ├── 🔍 Track Complaint Status & Timeline
│   └── ⭐ Provide Feedback & Ratings
├── 🛡️ Admin Interface
│   ├── 📋 Common Complaint Management
│   ├── 🎯 Dynamic Form Builder
│   ├── 📊 Analytics & Reports
│   └── 📈 Usage Statistics
└── 🔗 API Integration Layer
    └── Communicates with Review Nation API (Port 8080)
```

## ✨ Features Implemented

### 1. 📊 Common Complaint Management (Admin Dashboard)

**Location:** `resources/views/admin/common-complaints.blade.php`
**Controller:** `app/Http/Controllers/Admin/CommonComplaintController.php`

**Features:**
- ✅ Real-time statistics dashboard (Total, Active, Forms, Usage)
- ✅ Complete CRUD operations for common complaints
- ✅ Integrated drag-and-drop form builder
- ✅ JSON-based form structure management
- ✅ Export functionality (CSV, Excel, PDF)
- ✅ Category-based organization
- ✅ Usage analytics and trends
- ✅ Duplicate complaint functionality
- ✅ Recent activity tracking

### 2. 🎯 Public Complaint Filing System

**Location:** `resources/views/public/file-complaint.blade.php`
**Controller:** `app/Http/Controllers/Public/PublicComplaintController.php`

**Multi-Step Process:**
1. **Step 1:** 📂 Select complaint category from visual grid
2. **Step 2:** 🎯 Choose from common complaints or file custom
3. **Step 3:** 📝 Fill dynamic form with validation
4. **Step 4:** 👀 Review, agree to terms, and submit

**Features:**
- ✅ Dynamic form rendering based on complaint type
- ✅ File upload support (Images, PDFs, Documents - 5MB max)
- ✅ Real-time form validation
- ✅ Progress tracking with visual indicators
- ✅ Mobile-responsive design
- ✅ Accessibility compliant

### 3. 🔍 Advanced Complaint Tracking System

**Location:** `resources/views/public/track-complaint.blade.php`

**Features:**
- ✅ Track using tracking number + verification (phone/email)
- ✅ Visual status timeline with progress indicators
- ✅ Detailed complaint information display
- ✅ Context-aware available actions
- ✅ Star rating feedback system (1-5 stars)
- ✅ Comprehensive feedback forms
- ✅ Quick stats and activity summaries
- ✅ Mobile-optimized interface

### 4. 🎨 Dynamic Form Builder

**Supported Field Types:**
- 📝 **text** - Single line text input
- 📄 **textarea** - Multi-line text input  
- 📧 **email** - Email input with validation
- 🔢 **number** - Numeric input with constraints
- 📋 **select** - Dropdown selection with options
- ⚪ **radio** - Radio button groups
- ☑️ **checkbox** - Checkbox inputs
- 📅 **date** - Date picker
- ⏰ **time** - Time picker
- 📎 **file** - File upload with restrictions

**Form Structure Example:**
```json
[
  {
    "field_type": "text",
    "field_name": "incident_location",
    "label": "Incident Location",
    "required": true,
    "validation_rules": ["required", "string", "max:255"],
    "order": 1
  },
  {
    "field_type": "select",
    "field_name": "severity",
    "label": "Severity Level",
    "required": true,
    "options": ["Low", "Medium", "High", "Critical"],
    "order": 2
  }
]
```

## 🚀 Installation & Setup

### Prerequisites
- **PHP:** 8.2 or higher
- **Laravel:** 12.x
- **Database:** MySQL/MariaDB
- **Package Manager:** Composer
- **Frontend:** Node.js & NPM
- **Web Server:** Apache/Nginx

### 1. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Install dependencies
composer install
npm install
```

### 2. Database Setup

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=review_nation_web
DB_USERNAME=root
DB_PASSWORD=your_secure_password
```

### 3. API Integration Setup

```env
# Review Nation API Configuration
API_BASE_URL=http://127.0.0.1:8080/api

# File Upload Configuration
COMPLAINT_MAX_FILE_SIZE=5120
COMPLAINT_ALLOWED_EXTENSIONS="jpg,jpeg,png,pdf,doc,docx"

# Feature Flags
FEATURE_FEEDBACK_SYSTEM=true
FEATURE_FILE_UPLOADS=true
FEATURE_SMS_NOTIFICATIONS=true
FEATURE_EMAIL_NOTIFICATIONS=true
```

### 4. Database Migration & Seeding

```bash
php artisan migrate
php artisan db:seed
```

### 5. Asset Compilation

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server

```bash
# Default Laravel server
php artisan serve --port=8000

# Custom host and port
php artisan serve --host=0.0.0.0 --port=8000
```

## 🛠️ API Endpoints

### 🌐 Public Routes (Citizens)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/complaints/file` | Display complaint filing page |
| `GET` | `/complaints/track` | Display tracking page |
| `GET` | `/complaints/categories` | Get complaint categories |
| `GET` | `/complaints/common/{categoryId}` | Get common complaints by category |
| `GET` | `/complaints/form/{complaintId}` | Get form structure |
| `POST` | `/complaints/submit` | Submit new complaint |
| `POST` | `/complaints/track` | Track complaint by number |
| `POST` | `/complaints/feedback` | Submit feedback |

### 🔒 Admin Routes (Authentication Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/admin/common-complaints` | Admin dashboard |
| `GET` | `/admin/common-complaints/data` | Get complaints data |
| `GET` | `/admin/common-complaints/statistics` | Get statistics |
| `POST` | `/admin/common-complaints` | Create complaint |
| `PUT` | `/admin/common-complaints/{id}` | Update complaint |
| `DELETE` | `/admin/common-complaints/{id}` | Delete complaint |
| `GET` | `/admin/common-complaints/{id}/analytics` | Usage analytics |

## 🔒 Security Features

### 1. Input Validation & Sanitization
- ✅ Server-side validation for all inputs
- ✅ File type and size restrictions
- ✅ CSRF protection on all forms
- ✅ HTML input sanitization
- ✅ SQL injection prevention
- ✅ XSS protection

### 2. Rate Limiting
```env
RATE_LIMIT_COMPLAINTS_PER_HOUR=5
RATE_LIMIT_TRACKING_REQUESTS_PER_MINUTE=10
```

### 3. File Upload Security
- ✅ Strict file type validation
- ✅ File size limitations (5MB max)
- ✅ Secure file naming conventions
- ✅ Virus scanning capability (configurable)

## 📁 File Upload Handling

### Configuration
```env
COMPLAINT_MAX_FILE_SIZE=5120  # 5MB in KB
COMPLAINT_ALLOWED_EXTENSIONS="jpg,jpeg,png,pdf,doc,docx"
```

### Storage Structure
```
storage/app/public/complaints/
├── timestamp_index_originalname.ext
├── 1703845200_0_incident_photo.jpg
└── 1703845201_1_supporting_document.pdf
```

## ⚡ Performance Optimization

### 1. Caching Strategy
- 🔄 API response caching (5 minutes TTL)
- 🗂️ Static content caching (24 hours TTL)
- 🗄️ Database query caching
- 📊 Statistics caching for dashboard

### 2. Asset Optimization
- 🗜️ CSS/JS minification
- 🖼️ Image optimization
- ⏳ Lazy loading for heavy components
- 📱 Mobile-first responsive design

## 🔍 Error Handling & Monitoring

### 1. API Communication
- ♻️ Automatic retry with exponential backoff
- 🛡️ Graceful fallback responses
- 📊 Health check endpoints
- 📝 Comprehensive logging

### 2. User Experience
- 🎯 User-friendly error messages
- 🔄 Network connectivity handling
- 📤 File upload progress indicators
- ⚠️ Validation error highlighting

## 📊 Testing

### Running Tests
```bash
# All tests
php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only  
php artisan test --testsuite=Feature

# Browser tests (if configured)
php artisan dusk
```

## 🚀 Deployment

### Production Configuration

1. **Environment Settings:**
```env
APP_ENV=production
APP_DEBUG=false
FORCE_HTTPS=true
```

2. **Optimization Commands:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

3. **Asset Build:**
```bash
npm run build
```

### Docker Deployment
```dockerfile
FROM php:8.2-fpm
WORKDIR /var/www
COPY . .
RUN composer install --optimize-autoloader --no-dev
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
```

## 🔗 Integration Points

### Review Nation API Integration
- 🔑 Token-based authentication for admin operations
- 🌐 Public endpoints for citizen access
- 🔄 Real-time data synchronization
- 📤 File upload coordination
- 🛡️ Error recovery mechanisms

## 🌍 Customization & Localization

### Multi-language Support
```env
AVAILABLE_LOCALES="en,si,ta"  # English, Sinhala, Tamil
DEFAULT_LOCALE=en
FALLBACK_LOCALE=en
```

### Feature Toggles
```env
FEATURE_FEEDBACK_SYSTEM=true
FEATURE_FILE_UPLOADS=true
FEATURE_SMS_NOTIFICATIONS=true
FEATURE_EMAIL_NOTIFICATIONS=true
FEATURE_PUBLIC_STATISTICS=true
```

### Theming
- 🎨 Bootstrap 5 responsive framework
- 🏛️ Government-friendly color schemes
- 📱 Mobile-first design approach
- ♿ WCAG 2.1 accessibility compliance

## 🛠️ Troubleshooting

### Common Issues

1. **API Connection Problems:**
   ```bash
   # Check API connectivity
   curl http://127.0.0.1:8080/api/health
   
   # Verify environment
   php artisan config:show app.api_base_url
   ```

2. **File Upload Issues:**
   ```bash
   # Check permissions
   chmod -R 755 storage/
   chmod -R 755 public/
   
   # Clear cache
   php artisan cache:clear
   ```

3. **Database Connection:**
   ```bash
   # Test database connection
   php artisan tinker
   DB::connection()->getPdo();
   ```

### Debug Mode
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📋 Development Roadmap

### Phase 1 ✅ (Completed)
- [x] Common complaint management system
- [x] Dynamic form builder
- [x] Public complaint filing
- [x] Complaint tracking system
- [x] Feedback and rating system

### Phase 2 🚧 (In Progress)
- [ ] Advanced analytics dashboard
- [ ] SMS notification system
- [ ] Mobile application API
- [ ] Bulk operations interface

### Phase 3 📅 (Planned)
- [ ] AI-powered complaint categorization
- [ ] Geolocation integration
- [ ] Advanced reporting tools
- [ ] Multi-department workflow

## 👥 Contributing

### Development Standards
- 📝 PSR-12 coding standards
- 🏗️ Laravel best practices
- 📖 Comprehensive documentation
- ✅ Test coverage requirements

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/new-functionality

# Make changes and commit
git add .
git commit -m "feat: add new functionality"

# Push and create PR
git push origin feature/new-functionality
```

## 📞 Support & Contact

### Technical Support
- 📧 **Email:** support@reviewnation.lk
- 📚 **Documentation:** `/docs`
- 🔗 **API Reference:** `/api/documentation`
- 🐛 **Bug Reports:** GitHub Issues

### Development Team
- **Project Lead:** Review Nation Dev Team
- **Maintainer:** Webgen Software Company
- **Repository:** [webgen/review-nation](https://github.com/Webgen-Software-Company/webgen)

## 📄 License

This project is licensed under the **MIT License**. See [LICENSE](LICENSE) file for details.

---

<div align="center">

**🏛️ Review Nation Web Application**  
*Empowering Citizens, Improving Governance*

![Version](https://img.shields.io/badge/Version-1.0.0-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen?style=flat-square)
![Last Updated](https://img.shields.io/badge/Last%20Updated-September%202025-blue?style=flat-square)

</div>
