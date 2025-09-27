<?php
/**
 * Create Overdue Books for Penalty Testing
 * This script creates some overdue transactions to test penalty calculation
 */

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "Creating overdue books for penalty testing...\n";

try {
    $db->beginTransaction();
    
    // Get current semester
    $query = "SELECT * FROM semesters WHERE is_current = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $current_semester = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_semester) {
        echo "Error: No current semester found. Please create a semester first.\n";
        exit;
    }
    
    // Get some students and books
    $query = "SELECT id FROM users WHERE role = 'student' LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $student_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $query = "SELECT id FROM books WHERE status = 'available' LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $book_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($student_ids) || empty($book_ids)) {
        echo "Error: Need at least 3 students and 5 available books\n";
        exit;
    }
    
    // Create overdue transactions (due 5-15 days ago)
    foreach ($student_ids as $index => $student_id) {
        if ($index < count($book_ids)) {
            $book_id = $book_ids[$index];
            $days_overdue = rand(5, 15);
            $due_date = date('Y-m-d', strtotime("-{$days_overdue} days"));
            
            // Create overdue transaction
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active')";
            $stmt = $db->prepare($query);
            $stmt->execute([$student_id, $book_id, $current_semester['id'], $due_date]);
            
            // Update book status
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book_id]);
            
            echo "Created overdue book for student {$student_id}, book {$book_id}, due {$days_overdue} days ago\n";
        }
    }
    
    $db->commit();
    
    echo "\nOverdue books created successfully!\n";
    echo "You can now test penalty calculation in the staff dashboard.\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "Error creating overdue books: " . $e->getMessage() . "\n";
}
?>
