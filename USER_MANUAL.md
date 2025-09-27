# Smart Library Management System - User Manual

## 📚 Table of Contents
1. [System Overview](#system-overview)
2. [Getting Started](#getting-started)
3. [User Roles & Permissions](#user-roles--permissions)
4. [Feature Guide](#feature-guide)
5. [Troubleshooting](#troubleshooting)
6. [Technical Support](#technical-support)

## 🎯 System Overview

The Smart Library Management System is a comprehensive web-based solution designed to manage library operations efficiently. It supports multiple user roles, automated penalty calculations, and semester-based clearance management.

### Key Features
- **Role-Based Access Control**: Different interfaces for Students, Teachers, Staff, and Librarians
- **Book Inventory Management**: Complete CRUD operations for library books
- **Borrowing & Returning System**: Automated transaction management
- **Penalty & Fine Management**: Automated calculation and processing
- **Semester-Based Operations**: Academic year management
- **User Registration & Management**: Complete user lifecycle management
- **Clearance System**: Semester-end clearance processing

## 🚀 Getting Started

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Safari, Edge)
- Internet connection (for CDN resources)

### Installation Steps

1. **Download and Install XAMPP**
   - Download from https://www.apachefriends.org/
   - Install and start Apache and MySQL services

2. **Setup the Project**
   ```bash
   # Copy project to Xampp htdocs folder
   C:\xampp\htdocs\smart-library\
   ```

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create database: `smart_library`
   - Import the schema: `database/schema_v3.sql`

4. **Create Demo Data**
   - Run: `http://localhost/smart-library/demo/create_essential_users.php`
   - Run: `http://localhost/smart-library/demo/create_overdue_for_testing.php`

5. **Access the System**
   - Open: `http://localhost/smart-library/`
   - Login with demo credentials

## 👥 User Roles & Permissions

### 🔐 Login Credentials

| Role | Username | Password | Access Level |
|------|----------|----------|--------------|
| **Librarian** | librarian | password | Full system access |
| **Staff** | staff | password | Borrowing, returns, penalties |
| **Student** | student1 | password | Limited borrowing (3 books) |
| **Teacher** | teacher1 | password | Unlimited borrowing |

### 📋 Role Permissions

#### **Librarian**
- ✅ Add, edit, delete books
- ✅ Manage book inventory
- ✅ View all transactions
- ✅ Access user management
- ✅ Full system administration

#### **Staff**
- ✅ Process book borrowing
- ✅ Handle book returns
- ✅ Manage penalties and fines
- ✅ Process clearance
- ✅ View user status

#### **Student**
- ✅ Borrow up to 3 books per semester
- ✅ View borrowed books
- ✅ Make reservations
- ✅ View fines and penalties
- ✅ Access student dashboard

#### **Teacher**
- ✅ Borrow unlimited books
- ✅ View borrowed books
- ✅ Make reservations
- ✅ View fines and penalties
- ✅ Access teacher dashboard

## 🎯 Feature Guide

### 📖 Book Management (Librarian)

#### Adding Books
1. Login as Librarian
2. Go to Librarian Dashboard
3. Fill in book details:
   - Title (required)
   - Author (required)
   - ISBN (optional)
   - Price (for penalty calculations)
4. Click "Add Book"

#### Managing Books
- **View All Books**: See complete inventory
- **Edit Books**: Update book information
- **Archive Books**: Remove from active inventory
- **Search Books**: Find specific titles

### 📚 Borrowing System (Staff)

#### Processing Borrows
1. Login as Staff
2. Go to Staff Dashboard
3. Use search functionality:
   - Type to search users
   - Type to search books
4. Select user and book
5. Click "Borrow Book"

#### Processing Returns
1. Go to "Active Borrows" section
2. Find the transaction
3. Click "Return" button
4. Confirm return

### 💰 Penalty Management

#### Viewing Penalties
1. Login as Staff
2. Go to "Penalty Management"
3. View all overdue books
4. See calculated penalties:
   - Students: ₱10 per day
   - Teachers: ₱5 per day

#### Processing Penalty Payments
1. Find overdue transaction
2. Click "Process Payment"
3. Enter payment amount
4. Confirm payment

### 🎓 Clearance System

#### Student Clearance
- Students must return all books
- Pay all outstanding fines
- Clearance status updated automatically

#### Teacher Clearance
- Teachers must return all books
- Pay all outstanding fines
- Clearance status updated automatically

### 👤 User Management (Admin)

#### Managing Users
1. Login as Librarian/Staff
2. Go to User Management
3. View all users
4. Edit user information
5. Activate/Deactivate users

#### User Registration
1. Go to Registration page
2. Fill in user details
3. Select role
4. Submit registration

## 🔧 System Features

### 🔍 Search Functionality
- **User Search**: Type to find users quickly
- **Book Search**: Type to find books quickly
- **Real-time filtering**: Instant results

### 📊 Dashboard Features
- **Role-specific dashboards**: Customized for each user type
- **Real-time data**: Live updates
- **Quick actions**: One-click operations

### 💳 Penalty System
- **Automatic calculation**: Based on overdue days
- **Role-based rates**: Different rates for students/teachers
- **Payment processing**: Complete payment workflow

### 📅 Semester Management
- **Academic year tracking**: Semester-based operations
- **Borrowing limits**: Enforced per semester
- **Clearance processing**: End-of-semester clearance

## 🛠️ Troubleshooting

### Common Issues

#### Login Problems
- **Issue**: Cannot login with demo credentials
- **Solution**: Run `create_essential_users.php` script
- **Check**: Ensure database is properly set up

#### Book Borrowing Issues
- **Issue**: Students can borrow more than 3 books
- **Solution**: System enforces semester-based limits
- **Check**: Current semester is active

#### Penalty Calculation Issues
- **Issue**: Penalties not showing
- **Solution**: Run `create_overdue_for_testing.php`
- **Check**: Overdue transactions exist

#### Database Errors
- **Issue**: SQL errors or connection issues
- **Solution**: Check XAMPP services are running
- **Check**: Database exists and is accessible

### Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "User not found" | Invalid credentials | Check username/password |
| "Book not available" | Book already borrowed | Check book status |
| "Borrowing limit reached" | Student has 3 books | Return a book first |
| "Database connection failed" | XAMPP not running | Start Apache and MySQL |

## 📞 Technical Support

### System Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: 2.4 or higher
- **Browser**: Modern browser with JavaScript enabled

### File Structure
```
smart-library/
├── config/           # Configuration files
├── database/         # Database schemas
├── demo/            # Demo data scripts
├── staff/           # Staff interface
├── student/         # Student interface
├── teacher/         # Teacher interface
├── librarian/       # Librarian interface
├── admin/           # Admin interface
└── index.php        # Main entry point
```

### Database Tables
- **users**: User accounts and information
- **books**: Library book inventory
- **transactions**: Borrowing and returning records
- **semesters**: Academic year management
- **reservations**: Book reservation system

## 🎯 Demo Scenarios

### Scenario 1: Student Borrowing
1. Login as student1
2. Go to Student Dashboard
3. View available books
4. Make a reservation
5. Check borrowing status

### Scenario 2: Staff Processing
1. Login as staff
2. Process book borrowing
3. Handle book returns
4. Manage penalties
5. Process clearance

### Scenario 3: Librarian Management
1. Login as librarian
2. Add new books
3. Manage inventory
4. View system reports
5. Manage users

### Scenario 4: Penalty Testing
1. Create overdue books
2. View penalty calculations
3. Process penalty payments
4. Update clearance status

## 📈 System Benefits

### For Students
- Easy book borrowing process
- Clear penalty information
- Online reservation system
- Transparent borrowing limits

### For Teachers
- Unlimited book access
- Flexible borrowing terms
- Clear fine management
- Academic year tracking

### For Staff
- Efficient transaction processing
- Automated penalty calculations
- Clear user management
- Comprehensive reporting

### For Librarians
- Complete inventory control
- User management capabilities
- System administration
- Data analytics

## 🔒 Security Features

- **Password hashing**: Secure password storage
- **Session management**: Secure user sessions
- **Role-based access**: Restricted functionality
- **Input validation**: Data sanitization
- **SQL injection protection**: Prepared statements

## 📱 Browser Compatibility

- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
- ✅ Mobile browsers

## 🚀 Future Enhancements

- Mobile app development
- Advanced reporting
- Email notifications
- Barcode scanning
- Advanced analytics
- Multi-language support

---

**Smart Library Management System v1.0**  
*Developed with PHP, MySQL, and Tailwind CSS*
