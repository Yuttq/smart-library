<?php
/**
 * Create Essential Users Only
 * Creates only the essential users needed for testing
 */

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Creating essential users...</h2>";

try {
    $db->beginTransaction();
    
    // Create essential users only
    $essential_users = [
        ['username' => 'librarian', 'first_name' => 'Library', 'last_name' => 'Admin', 'email' => 'librarian@library.com', 'role' => 'librarian'],
        ['username' => 'staff', 'first_name' => 'Library', 'last_name' => 'Staff', 'email' => 'staff@library.com', 'role' => 'staff'],
        ['username' => 'student1', 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@student.com', 'role' => 'student'],
        ['username' => 'student2', 'first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice.johnson@student.com', 'role' => 'student'],
        ['username' => 'student3', 'first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob.smith@student.com', 'role' => 'student'],
        ['username' => 'teacher1', 'first_name' => 'Prof. Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@teacher.com', 'role' => 'teacher'],
        ['username' => 'teacher2', 'first_name' => 'Prof. Michael', 'last_name' => 'Brown', 'email' => 'michael.brown@teacher.com', 'role' => 'teacher']
    ];
    
    foreach ($essential_users as $user_data) {
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
    
    echo "<h3>✅ Essential users created successfully!</h3>";
    echo "<h4>Login Credentials:</h4>";
    echo "<ul>";
    echo "<li><strong>Librarian:</strong> librarian / password</li>";
    echo "<li><strong>Staff:</strong> staff / password</li>";
    echo "<li><strong>Students:</strong> student1, student2, student3 / password</li>";
    echo "<li><strong>Teachers:</strong> teacher1, teacher2 / password</li>";
    echo "</ul>";
    
    echo "<h4>Next Steps:</h4>";
    echo "<ol>";
    echo "<li>Add some books through the Librarian dashboard</li>";
    echo "<li>Run the overdue books script to test penalties</li>";
    echo "</ol>";
    
    echo "<p><a href='../index.php' style='background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error creating users: " . $e->getMessage() . "<br>";
}
?>
