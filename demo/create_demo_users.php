<?php
/**
 * Demo Users and Test Data Creation Script
 * Run this script to create demo users and test data for testing
 */

require_once '../config/database.php';
require_once '../config/user_manager.php';

$database = new Database();
$db = $database->getConnection();
$userManager = new UserManager();

echo "Creating demo users and test data...\n";

try {
    // Get current semester
    $query = "SELECT * FROM semesters WHERE is_current = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $current_semester = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_semester) {
        echo "Error: No current semester found. Please create a semester first.\n";
        exit;
    }
    
    $db->beginTransaction();
    
    // Create demo students
    $students = [
        ['username' => 'student2', 'first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice.johnson@student.com', 'role' => 'student'],
        ['username' => 'student3', 'first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob.smith@student.com', 'role' => 'student'],
        ['username' => 'student4', 'first_name' => 'Carol', 'last_name' => 'Davis', 'email' => 'carol.davis@student.com', 'role' => 'student'],
        ['username' => 'student5', 'first_name' => 'David', 'last_name' => 'Wilson', 'email' => 'david.wilson@student.com', 'role' => 'student']
    ];
    
    // Create demo teachers
    $teachers = [
        ['username' => 'teacher2', 'first_name' => 'Prof. Michael', 'last_name' => 'Brown', 'email' => 'michael.brown@teacher.com', 'role' => 'teacher'],
        ['username' => 'teacher3', 'first_name' => 'Dr. Lisa', 'last_name' => 'Garcia', 'email' => 'lisa.garcia@teacher.com', 'role' => 'teacher'],
        ['username' => 'teacher4', 'first_name' => 'Prof. James', 'last_name' => 'Miller', 'email' => 'james.miller@teacher.com', 'role' => 'teacher']
    ];
    
    // Create students
    foreach ($students as $student) {
        $student['password'] = 'password';
        $result = $userManager->registerUser($student);
        if ($result['success']) {
            echo "Created student: {$student['first_name']} {$student['last_name']}\n";
        } else {
            echo "Error creating student {$student['first_name']}: " . implode(', ', $result['errors']) . "\n";
        }
    }
    
    // Create teachers
    foreach ($teachers as $teacher) {
        $teacher['password'] = 'password';
        $result = $userManager->registerUser($teacher);
        if ($result['success']) {
            echo "Created teacher: {$teacher['first_name']} {$teacher['last_name']}\n";
        } else {
            echo "Error creating teacher {$teacher['first_name']}: " . implode(', ', $result['errors']) . "\n";
        }
    }
    
    // Create some test transactions for students (borrow 1-2 books each)
    $query = "SELECT id FROM users WHERE role = 'student' ORDER BY id DESC LIMIT 4";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $student_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $query = "SELECT id FROM books WHERE status = 'available' LIMIT 8";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $book_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $book_index = 0;
    foreach ($student_ids as $student_id) {
        // Each student borrows 1-2 books
        $books_to_borrow = rand(1, 2);
        for ($i = 0; $i < $books_to_borrow && $book_index < count($book_ids); $i++) {
            $book_id = $book_ids[$book_index++];
            $due_date = date('Y-m-d', strtotime('+14 days'));
            
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active')";
            $stmt = $db->prepare($query);
            $stmt->execute([$student_id, $book_id, $current_semester['id'], $due_date]);
            
            // Update book status
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book_id]);
        }
    }
    
    // Create some test transactions for teachers (borrow 2-4 books each)
    $query = "SELECT id FROM users WHERE role = 'teacher' ORDER BY id DESC LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $teacher_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($teacher_ids as $teacher_id) {
        // Each teacher borrows 2-4 books
        $books_to_borrow = rand(2, 4);
        for ($i = 0; $i < $books_to_borrow && $book_index < count($book_ids); $i++) {
            $book_id = $book_ids[$book_index++];
            $due_date = date('Y-m-d', strtotime('+30 days'));
            
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active')";
            $stmt = $db->prepare($query);
            $stmt->execute([$teacher_id, $book_id, $current_semester['id'], $due_date]);
            
            // Update book status
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book_id]);
        }
    }
    
    $db->commit();
    
    echo "\nDemo users and test data created successfully!\n";
    echo "Created:\n";
    echo "- " . count($students) . " students\n";
    echo "- " . count($teachers) . " teachers\n";
    echo "- Test borrowing transactions\n";
    echo "\nYou can now test the system with real users!\n";
    echo "\nDemo accounts:\n";
    echo "Students: student2, student3, student4, student5 (password: password)\n";
    echo "Teachers: teacher2, teacher3, teacher4 (password: password)\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "Error creating demo data: " . $e->getMessage() . "\n";
}
?>
