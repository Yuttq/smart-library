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
   - Choose file: `database/schema.sql`
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
│   └── database.php          # Database connection
├── database/
│   └── schema.sql            # Database schema and sample data
├── librarian/
│   └── dashboard.php         # Librarian interface
├── staff/
│   └── dashboard.php         # Staff interface
├── teacher/
│   └── dashboard.php         # Teacher interface
├── student/
│   └── dashboard.php         # Student interface
├── index.php                 # Main entry point
├── login.php                 # Login page
├── logout.php                # Logout handler
└── README.md                 # This file
```

## MVP Status
✅ **Phase 1 Complete** - All core MVP features implemented:
- Login system with role-based access
- Book inventory management (Librarian)
- Borrow/Return transactions (Staff)
- Reservation system (Students/Teachers)
- Clean, simple UI with Tailwind CSS

## Future Enhancements (Not in MVP)
- Penalty calculation & clearance
- Semester-end clearance check
- Full user registration system
- Reports & analytics
- Advanced UI with animations
- Email notifications
- Book categories and search
- Fine management system

## Testing the System
1. Login as Librarian to add some books
2. Login as Staff to process borrowing transactions
3. Login as Student/Teacher to test reservations
4. Test the borrowing limits (students max 3 books)
5. Test the return process through Staff interface

The system is now ready for demonstration and testing!
