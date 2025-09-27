<?php
/**
 * Demo Penalties Creation Script
 * Run this script to create demo penalties for testing
 */

require_once '../config/database.php';
require_once '../config/penalty_manager.php';

$database = new Database();
$db = $database->getConnection();
$penaltyManager = new PenaltyManager();

echo "Creating demo penalties...\n";

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
    
    // Get some books
    $query = "SELECT * FROM books LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get student and teacher
    $query = "SELECT * FROM users WHERE role IN ('student', 'teacher') LIMIT 2";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($books) || empty($users)) {
        echo "Error: Need at least 3 books and 2 users (student/teacher) in database.\n";
        exit;
    }
    
    $db->beginTransaction();
    
    // Create some overdue transactions
    foreach ($users as $user) {
        foreach ($books as $book) {
            // Create borrow transaction with past due date
            $due_date = date('Y-m-d', strtotime('-10 days')); // 10 days ago
            
            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date, status) 
                      VALUES (?, ?, ?, 'borrow', ?, 'active')";
            $stmt = $db->prepare($query);
            $stmt->execute([$user['id'], $book['id'], $current_semester['id'], $due_date]);
            
            // Update book status to borrowed
            $query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book['id']]);
        }
    }
    
    // Mark transactions as overdue
    $query = "UPDATE transactions t 
              JOIN users u ON t.user_id = u.id 
              JOIN penalty_rates p ON u.role = p.user_role 
              SET t.status = 'overdue' 
              WHERE t.transaction_type = 'borrow' 
              AND t.status = 'active' 
              AND DATE_ADD(t.due_date, INTERVAL p.grace_period_days DAY) < CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $db->commit();
    
    echo "Demo penalties created successfully!\n";
    echo "Created overdue transactions for:\n";
    foreach ($users as $user) {
        echo "- {$user['first_name']} {$user['last_name']} ({$user['role']})\n";
    }
    echo "\nYou can now test the penalty management system.\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "Error creating demo penalties: " . $e->getMessage() . "\n";
}
?>
