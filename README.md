# Smart Library System - MVP

A simple library management system built with PHP, MySQL, and Tailwind CSS.

## Features (MVP)

### 🔐 Login & Role-Based Access
- Simple login page with role-based redirection
- Four user roles: Student, Teacher, Librarian, Staff
- Hardcoded demo users for testing

### 📚 Book Inventory (Librarian Only)
- Add new books (Title, Author, ISBN)
- View all books in the system
- Edit book details
- Delete books from inventory
- Track book status (Available, Borrowed, Reserved)

### 📖 Borrowing & Returning (Staff Only)
- Students can borrow up to 3 books maximum
- Teachers can borrow unlimited books
- Staff facilitates all borrow/return transactions
- Automatic due date calculation (14 days for students, 30 days for teachers)
- Real-time status updates

### 📋 Reservation System (Students & Teachers)
- Reserve available books
- View active reservations
- Cancel reservations
- Automatic status management

### 💰 Penalty & Fine Management (NEW!)
- **Automatic penalty calculation** based on overdue days and user role
- **Role-based penalty rates**: Students (₱10/day, max ₱500), Teachers (₱5/day, max ₱250)
- **Grace periods**: 3 days for students, 5 days for teachers
- **Staff penalty management dashboard** with payment processing
- **User fines viewing** for students and teachers
- **Penalty waiver system** for staff
- **Automated overdue status updates** via cron job
- **Comprehensive penalty statistics** and reporting

### 👥 User Registration & Management (NEW!)
- **Public registration system** for students and teachers
- **Comprehensive user validation** with real-time feedback
- **Admin user management dashboard** for librarians and staff
- **User search and filtering** with pagination
- **User status management** (activate/deactivate accounts)
- **Role-based user creation** and editing
- **User statistics and analytics** dashboard
- **Secure password handling** with proper validation

## Tech Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Tailwind CSS + Flowbite
- **Server**: XAMPP (Apache + MySQL)

## Setup Instructions

### 1. Database Setup
1. Start XAMPP and ensure MySQL is running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import the database schema:
   - Go to "Import" tab
   - Choose file: `database/schema_v2.sql` (includes penalty system)
   - Click "Go" to import

### 2. Project Setup
1. Place the project folder in `C:\xampp\htdocs\smart-library`
2. Ensure the database connection settings in `config/database.php` match your MySQL configuration
3. Access the application at: `http://localhost/smart-library`

### 3. Demo Accounts
The system comes with pre-configured demo accounts:

| Role | Username | Password |
|------|----------|----------|
| Librarian | librarian1 | password |
| Staff | staff1 | password |
| Teacher | teacher1 | password |
| Student | student1 | password |

## User Roles & Permissions

### Librarian
- Full book inventory management
- Add, edit, delete books
- View all books and their status

### Staff
- Process book borrowing transactions
- Process book return transactions
- View all active borrows
- Handle both student and teacher borrowing

### Teacher
- View available books
- Reserve books
- View borrowed books
- View active reservations
- Unlimited borrowing capacity

### Student
- View available books
- Reserve books
- View borrowed books (max 3)
- View active reservations
- Limited borrowing capacity (3 books max)

## File Structure
```
smart-library/
├── config/
│   ├── database.php          # Database connection
│   ├── penalty_manager.php   # Penalty calculation and management
│   └── user_manager.php      # User registration and management
├── database/
│   ├── schema.sql            # Original database schema (MVP)
│   └── schema_v2.sql         # Enhanced schema with penalty system
├── cron/
│   └── update_overdue.php    # Automated overdue status update
├── admin/
│   └── user_management.php   # User management dashboard
├── librarian/
│   └── dashboard.php         # Librarian interface
├── staff/
│   ├── dashboard.php         # Staff interface
│   └── penalty_management.php # Penalty management dashboard
├── teacher/
│   ├── dashboard.php         # Teacher interface
│   └── fines.php             # Teacher fines viewing
├── student/
│   ├── dashboard.php         # Student interface
│   └── fines.php             # Student fines viewing
├── index.php                 # Main entry point
├── login.php                 # Login page
├── register.php              # User registration page
├── logout.php                # Logout handler
└── README.md                 # This file
```

## Development Status
✅ **Phase 1 Complete** - MVP features implemented:
- Login system with role-based access
- Book inventory management (Librarian)
- Borrow/Return transactions (Staff)
- Reservation system (Students/Teachers)
- Clean, simple UI with Tailwind CSS

✅ **Phase 2 Complete** - Penalty & Fine Management:
- Automatic penalty calculation system
- Role-based penalty rates and grace periods
- Staff penalty management dashboard
- User fines viewing (Students/Teachers)
- Penalty payment and waiver processing
- Automated overdue status updates

✅ **Phase 3 Complete** - User Registration & Management:
- Public user registration system
- Comprehensive user validation
- Admin user management dashboard
- User search and pagination
- User status management
- Role-based user creation
- User statistics and analytics

## Future Enhancements (Phase 4+)
- Semester-end clearance check
- Reports & analytics dashboard
- Advanced UI with animations
- Email notifications for due dates
- Book categories and advanced search
- Enhanced fine management features
- User profile management
- Library statistics and insights
- Bulk user import/export
- Advanced reporting features

## Testing the System
1. **Setup**: Import `database/schema_v2.sql` into MySQL via phpMyAdmin
2. **Basic Flow**:
   - Login as Librarian → Add some books
   - Login as Staff → Process borrowing transactions
   - Login as Student/Teacher → Test reservations
3. **User Registration Testing**:
   - Visit `register.php` to test public registration
   - Login as Librarian/Staff → Go to "Manage Users" to test admin features
   - Test user search, editing, and status management
   - View user statistics dashboard
4. **Penalty System Testing**:
   - Create overdue transactions (manually set due dates in database)
   - Login as Staff → Go to "Manage Penalties" to view overdue books
   - Test penalty calculation and payment processing
   - Login as Student/Teacher → View "My Fines" to see outstanding penalties
5. **Automation**: Set up cron job for `cron/update_overdue.php` to run daily

The system is now ready for demonstration and testing with full user management and penalty systems!
