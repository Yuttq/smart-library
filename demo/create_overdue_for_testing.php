<?php
/**
 * Create Overdue Books for Immediate Penalty Testing
 * This script creates overdue transactions with past due dates for testing penalties
 */

require_once '../config/database.php';
require_once '../config/semester_manager.php';

$database = new Database();
$db = $database->getConnection();
$semesterManager = new SemesterManager();

echo "<h2>Creating overdue books for penalty testing...</h2>";

try {
    $db->beginTransaction();
    
    // Get current semester
    $current_semester = $semesterManager->getCurrentSemester();
    if (!$current_semester) {
        echo "❌ No current semester found. Please create a semester first.<br>";
        exit;
    }
    
    // Get some students and available books
    $query = "SELECT id, username, first_name, last_name FROM users WHERE role = 'student' LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $query = "SELECT id, title, author FROM books WHERE status = 'available' LIMIT 8";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($students)) {
        echo "❌ No students found. Please create some students first.<br>";
        exit;
    }
    
    if (empty($books)) {
        echo "❌ No available books found. Please add some books first.<br>";
        exit;
    }
    
    echo "✅ Found " . count($students) . " students and " . count($books) . " available books<br>";
    
    // Create overdue transactions with different overdue periods
    $overdue_periods = [5, 10, 15, 20, 25, 30]; // Days overdue
    $book_index = 0;
    
    foreach ($students as $index => $student) {
        if ($book_index < count($books)) {
            $book = $books[$book_index];
            $days_overdue = $overdue_periods[$index % count($overdue_periods)];
            $due_date = date('Y-m-d', strtotime("-{$days_overdue} days"));
            
            // Create overdue transaction
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status, transaction_date) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active', ?)";
            $stmt = $db->prepare($query);
            $borrow_date = date('Y-m-d', strtotime("-" . ($days_overdue + 14) . " days")); // Borrowed 14 days before due
            $stmt->execute([
                $student['id'], 
                $book['id'], 
                $current_semester['id'], 
                $due_date, 
                $borrow_date
            ]);
            
            // Update book status to borrowed
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book['id']]);
            
            echo "✅ Created overdue transaction:<br>";
            echo "&nbsp;&nbsp;👤 Student: {$student['first_name']} {$student['last_name']} ({$student['username']})<br>";
            echo "&nbsp;&nbsp;📚 Book: {$book['title']} by {$book['author']}<br>";
            echo "&nbsp;&nbsp;📅 Due: {$due_date} ({$days_overdue} days overdue)<br>";
            echo "&nbsp;&nbsp;💰 Penalty: ₱" . number_format($days_overdue * 10, 2) . " (₱10/day)<br><br>";
            
            $book_index++;
        }
    }
    
    // Create some teacher overdue books too
    $query = "SELECT id, username, first_name, last_name FROM users WHERE role = 'teacher' LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $query = "SELECT id, title, author FROM books WHERE status = 'available' LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $teacher_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($teachers as $index => $teacher) {
        if ($index < count($teacher_books)) {
            $book = $teacher_books[$index];
            $days_overdue = $overdue_periods[$index + 3]; // Different overdue periods
            $due_date = date('Y-m-d', strtotime("-{$days_overdue} days"));
            
            // Create overdue transaction
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status, transaction_date) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active', ?)";
            $stmt = $db->prepare($query);
            $borrow_date = date('Y-m-d', strtotime("-" . ($days_overdue + 30) . " days")); // Borrowed 30 days before due
            $stmt->execute([
                $teacher['id'], 
                $book['id'], 
                $current_semester['id'], 
                $due_date, 
                $borrow_date
            ]);
            
            // Update book status to borrowed
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book['id']]);
            
            echo "✅ Created teacher overdue transaction:<br>";
            echo "&nbsp;&nbsp;👤 Teacher: {$teacher['first_name']} {$teacher['last_name']} ({$teacher['username']})<br>";
            echo "&nbsp;&nbsp;📚 Book: {$book['title']} by {$book['author']}<br>";
            echo "&nbsp;&nbsp;📅 Due: {$due_date} ({$days_overdue} days overdue)<br>";
            echo "&nbsp;&nbsp;💰 Penalty: ₱" . number_format($days_overdue * 5, 2) . " (₱5/day)<br><br>";
        }
    }
    
    $db->commit();
    
    echo "<h3>🎉 Overdue books created successfully!</h3>";
    echo "<h4>Now you can test:</h4>";
    echo "<ul>";
    echo "<li>✅ <strong>Staff Dashboard</strong> - View overdue books and penalties</li>";
    echo "<li>✅ <strong>Penalty Management</strong> - Process penalty payments</li>";
    echo "<li>✅ <strong>Student/Teacher Fines</strong> - View outstanding fines</li>";
    echo "<li>✅ <strong>Clearance Management</strong> - Check clearance status</li>";
    echo "</ul>";
    
    echo "<h4>Test Links:</h4>";
    echo "<ul>";
    echo "<li><a href='../staff/dashboard.php' style='color: #4F46E5;'>Staff Dashboard</a></li>";
    echo "<li><a href='../staff/penalty_management.php' style='color: #4F46E5;'>Penalty Management</a></li>";
    echo "<li><a href='../staff/clearance_management.php' style='color: #4F46E5;'>Clearance Management</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error creating overdue books: " . $e->getMessage() . "<br>";
}
?>
