<?php
/**
 * Create Test Users for Penalty Testing
 * Creates basic users needed for testing penalties
 */

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Creating test users for penalty testing...</h2>";

try {
    $db->beginTransaction();
    
    // Create test users if they don't exist
    $test_users = [
        ['username' => 'librarian', 'first_name' => 'Library', 'last_name' => 'Admin', 'email' => 'librarian@library.com', 'role' => 'librarian'],
        ['username' => 'staff', 'first_name' => 'Library', 'last_name' => 'Staff', 'email' => 'staff@library.com', 'role' => 'staff'],
        ['username' => 'student1', 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@student.com', 'role' => 'student'],
        ['username' => 'student2', 'first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice.johnson@student.com', 'role' => 'student'],
        ['username' => 'student3', 'first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob.smith@student.com', 'role' => 'student'],
        ['username' => 'teacher1', 'first_name' => 'Prof. Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@teacher.com', 'role' => 'teacher'],
        ['username' => 'teacher2', 'first_name' => 'Prof. Michael', 'last_name' => 'Brown', 'email' => 'michael.brown@teacher.com', 'role' => 'teacher']
    ];
    
    foreach ($test_users as $user_data) {
        // Check if user exists
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_data['username']]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_user) {
            echo "✅ User {$user_data['username']} already exists<br>";
        } else {
            // Create user directly in database
            $hashed_password = password_hash('password', PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password, first_name, last_name, email, role, is_active, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
            $stmt = $db->prepare($query);
            $stmt->execute([
                $user_data['username'],
                $hashed_password,
                $user_data['first_name'],
                $user_data['last_name'],
                $user_data['email'],
                $user_data['role']
            ]);
            echo "✅ Created user: {$user_data['username']}<br>";
        }
    }
    
    // Create some demo books if they don't exist
    $demo_books = [
        ['title' => 'Introduction to Programming', 'author' => 'Dr. Sarah Wilson', 'isbn' => '978-1111111111', 'price' => 500.00],
        ['title' => 'Database Design', 'author' => 'Prof. Mark Davis', 'isbn' => '978-2222222222', 'price' => 600.00],
        ['title' => 'Web Development', 'author' => 'Dr. Lisa Garcia', 'isbn' => '978-3333333333', 'price' => 550.00],
        ['title' => 'Data Structures', 'author' => 'Prof. James Miller', 'isbn' => '978-4444444444', 'price' => 700.00],
        ['title' => 'Software Engineering', 'author' => 'Dr. Anna Taylor', 'isbn' => '978-5555555555', 'price' => 650.00],
        ['title' => 'Computer Networks', 'author' => 'Prof. Robert Brown', 'isbn' => '978-6666666666', 'price' => 580.00],
        ['title' => 'Operating Systems', 'author' => 'Dr. Maria Rodriguez', 'isbn' => '978-7777777777', 'price' => 620.00],
        ['title' => 'Artificial Intelligence', 'author' => 'Prof. David Lee', 'isbn' => '978-8888888888', 'price' => 750.00]
    ];
    
    foreach ($demo_books as $book_data) {
        // Check if book already exists by title and author
        $query = "SELECT id FROM books WHERE title = ? AND author = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$book_data['title'], $book_data['author']]);
        $existing_book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_book) {
            echo "✅ Book '{$book_data['title']}' already exists<br>";
        } else {
            // Check if ISBN already exists
            $query = "SELECT id FROM books WHERE isbn = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book_data['isbn']]);
            $existing_isbn = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing_isbn) {
                // Use NULL ISBN if conflict
                $query = "INSERT INTO books (title, author, isbn, price, status) VALUES (?, ?, NULL, ?, 'available')";
                $stmt = $db->prepare($query);
                $stmt->execute([$book_data['title'], $book_data['author'], $book_data['price']]);
                echo "✅ Created book: {$book_data['title']} (ISBN set to NULL due to conflict)<br>";
            } else {
                $query = "INSERT INTO books (title, author, isbn, price, status) VALUES (?, ?, ?, ?, 'available')";
                $stmt = $db->prepare($query);
                $stmt->execute([$book_data['title'], $book_data['author'], $book_data['isbn'], $book_data['price']]);
                echo "✅ Created book: {$book_data['title']}<br>";
            }
        }
    }
    
    // Create current semester if it doesn't exist
    $query = "SELECT id FROM semesters WHERE is_current = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $current_semester = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_semester) {
        $query = "INSERT INTO semesters (name, start_date, end_date, is_current) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute(['First Semester 2024', '2024-08-01', '2024-12-15', 1]);
        echo "✅ Created current semester<br>";
    } else {
        echo "✅ Current semester already exists<br>";
    }
    
    $db->commit();
    
    echo "<h3>✅ Test users and data created successfully!</h3>";
    echo "<h4>Login Credentials:</h4>";
    echo "<ul>";
    echo "<li><strong>Librarian:</strong> librarian / password</li>";
    echo "<li><strong>Staff:</strong> staff / password</li>";
    echo "<li><strong>Students:</strong> student1, student2, student3 / password</li>";
    echo "<li><strong>Teachers:</strong> teacher1, teacher2 / password</li>";
    echo "</ul>";
    
    echo "<p><a href='create_overdue_for_testing.php' style='background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Create Overdue Books for Penalty Testing</a></p>";
    
} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error creating test users: " . $e->getMessage() . "<br>";
}
?>
