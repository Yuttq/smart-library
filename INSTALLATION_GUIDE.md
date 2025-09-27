# Smart Library Management System - Installation Guide

## 📋 Prerequisites

### System Requirements
- **Operating System**: Windows 10/11, macOS 10.14+, or Linux
- **RAM**: Minimum 4GB (8GB recommended)
- **Storage**: 2GB free space
- **Internet**: Required for CDN resources

### Software Requirements
- **XAMPP**: Version 8.0+ (includes Apache, MySQL, PHP)
- **Web Browser**: Chrome, Firefox, Safari, or Edge (latest version)
- **Text Editor**: VS Code, Sublime Text, or Notepad++ (optional)

## 🚀 Installation Steps

### Step 1: Download and Install XAMPP

1. **Download XAMPP**
   - Go to https://www.apachefriends.org/
   - Download XAMPP for your operating system
   - Choose the version with PHP 8.0+

2. **Install XAMPP**
   - Run the installer as administrator
   - Select components: Apache, MySQL, PHP, phpMyAdmin
   - Choose installation directory (default: `C:\xampp\`)
   - Complete installation

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** service
   - Start **MySQL** service
   - Verify both services are running (green status)

### Step 2: Setup the Project

1. **Download Project Files**
   - Extract the Smart Library project
   - Copy entire project folder to: `C:\xampp\htdocs\smart-library\`

2. **Verify File Structure**
   ```
   C:\xampp\htdocs\smart-library\
   ├── config/
   ├── database/
   ├── demo/
   ├── staff/
   ├── student/
   ├── teacher/
   ├── librarian/
   ├── admin/
   ├── index.php
   └── README.md
   ```

### Step 3: Database Setup

1. **Access phpMyAdmin**
   - Open browser: `http://localhost/phpmyadmin`
   - Login (no password by default)

2. **Create Database**
   - Click "New" in left sidebar
   - Database name: `smart_library`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import Database Schema**
   - Select `smart_library` database
   - Click "Import" tab
   - Choose file: `database/schema_v3.sql`
   - Click "Go" to import

4. **Verify Tables Created**
   - Check for tables: `users`, `books`, `transactions`, `semesters`, `reservations`

### Step 4: Create Demo Data

1. **Create Essential Users**
   - Open: `http://localhost/smart-library/demo/create_essential_users.php`
   - Verify users are created successfully

2. **Create Overdue Books for Testing**
   - Open: `http://localhost/smart-library/demo/create_overdue_for_testing.php`
   - Verify overdue transactions are created

### Step 5: Access the System

1. **Open Application**
   - Browser: `http://localhost/smart-library/`
   - You should see the login page

2. **Test Login**
   - Username: `librarian`
   - Password: `password`
   - Verify successful login

## 🔧 Configuration

### Database Configuration
File: `config/database.php`
```php
// Default configuration (usually works)
$host = 'localhost';
$dbname = 'smart_library';
$username = 'root';
$password = '';
```

### Apache Configuration (if needed)
File: `C:\xampp\apache\conf\httpd.conf`
- Ensure mod_rewrite is enabled
- Document root points to htdocs

### PHP Configuration
File: `C:\xampp\php\php.ini`
- Ensure PDO MySQL extension is enabled
- Set memory_limit to 256M or higher
- Enable error reporting for development

## 🧪 Testing the Installation

### Test 1: Basic Functionality
1. Login as different user roles
2. Navigate through dashboards
3. Verify all pages load correctly

### Test 2: Database Operations
1. Add a book (Librarian)
2. Borrow a book (Staff)
3. Return a book (Staff)
4. Check penalty calculations

### Test 3: User Management
1. Register a new user
2. Edit user information
3. Activate/Deactivate users

### Test 4: Penalty System
1. View overdue books
2. Check penalty calculations
3. Process penalty payments

## 🚨 Troubleshooting

### Common Issues

#### XAMPP Won't Start
- **Issue**: Apache/MySQL services fail to start
- **Solution**: 
  - Check if ports 80 and 3306 are in use
  - Run XAMPP as administrator
  - Restart computer and try again

#### Database Connection Error
- **Issue**: "Connection failed" error
- **Solution**:
  - Verify MySQL service is running
  - Check database credentials in `config/database.php`
  - Ensure database `smart_library` exists

#### Page Not Found (404)
- **Issue**: Pages return 404 error
- **Solution**:
  - Verify project is in `C:\xampp\htdocs\smart-library\`
  - Check Apache is running
  - Verify file permissions

#### Login Issues
- **Issue**: Cannot login with demo credentials
- **Solution**:
  - Run `create_essential_users.php` script
  - Check users table in database
  - Verify password hashing

#### Permission Denied
- **Issue**: File permission errors
- **Solution**:
  - Run XAMPP as administrator
  - Check folder permissions
  - Ensure files are not read-only

### Error Logs
- **Apache Logs**: `C:\xampp\apache\logs\error.log`
- **MySQL Logs**: `C:\xampp\mysql\data\*.err`
- **PHP Logs**: `C:\xampp\php\logs\php_error_log`

## 🔒 Security Considerations

### Development Environment
- Default XAMPP configuration is for development only
- Never use in production without proper security setup

### Production Deployment
- Change default passwords
- Enable HTTPS
- Configure proper file permissions
- Use environment variables for sensitive data

## 📱 Browser Testing

### Supported Browsers
- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+

### Browser Requirements
- JavaScript enabled
- Cookies enabled
- Local storage support
- Modern CSS support

## 🚀 Performance Optimization

### PHP Settings
```ini
# In php.ini
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
```

### MySQL Settings
```ini
# In my.ini
max_connections = 100
innodb_buffer_pool_size = 128M
```

## 📊 System Monitoring

### Health Checks
1. **Database Connection**: Test connection to MySQL
2. **File Permissions**: Verify read/write access
3. **Memory Usage**: Monitor PHP memory consumption
4. **Error Logs**: Check for PHP/MySQL errors

### Maintenance Tasks
- Regular database backups
- Log file cleanup
- Performance monitoring
- Security updates

## 🆘 Getting Help

### Documentation
- User Manual: `USER_MANUAL.md`
- README: `README.md`
- Code comments in source files

### Support Resources
- XAMPP Documentation: https://www.apachefriends.org/docs/
- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/

### Common Solutions
1. **Restart XAMPP**: Often fixes temporary issues
2. **Clear Browser Cache**: Resolves display issues
3. **Check File Permissions**: Ensures proper access
4. **Verify Database**: Confirm tables and data exist

---

**Installation Guide v1.0**  
*Smart Library Management System*
