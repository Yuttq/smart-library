<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle reservations
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reserve') {
        $book_id = $_POST['book_id'] ?? '';
        
        if (!empty($book_id)) {
            // Check if book is available
            $query = "SELECT status FROM books WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$book_id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($book && $book['status'] === 'available') {
                // Check if user already has an active reservation for this book
                $query = "SELECT id FROM reservations WHERE user_id = ? AND book_id = ? AND status = 'active'";
                $stmt = $db->prepare($query);
                $stmt->execute([$_SESSION['user_id'], $book_id]);
                
                if (!$stmt->fetch()) {
                    // Create reservation
                    $query = "INSERT INTO reservations (user_id, book_id) VALUES (?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$_SESSION['user_id'], $book_id]);
                    
                    // Update book status
                    $query = "UPDATE books SET status = 'reserved' WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$book_id]);
                    
                    $success_message = "Book reserved successfully!";
                } else {
                    $error_message = "You already have an active reservation for this book";
                }
            } else {
                $error_message = "Book is not available for reservation";
            }
        }
    } elseif ($action === 'cancel_reservation') {
        $reservation_id = $_POST['reservation_id'] ?? '';
        
        if (!empty($reservation_id)) {
            $db->beginTransaction();
            try {
                // Get reservation details
                $query = "SELECT book_id FROM reservations WHERE id = ? AND user_id = ? AND status = 'active'";
                $stmt = $db->prepare($query);
                $stmt->execute([$reservation_id, $_SESSION['user_id']]);
                $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($reservation) {
                    // Update reservation status
                    $query = "UPDATE reservations SET status = 'cancelled' WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$reservation_id]);
                    
                    // Update book status back to available
                    $query = "UPDATE books SET status = 'available' WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$reservation['book_id']]);
                    
                    $db->commit();
                    $success_message = "Reservation cancelled successfully!";
                } else {
                    $db->rollback();
                    $error_message = "Reservation not found";
                }
            } catch (Exception $e) {
                $db->rollback();
                $error_message = "Error cancelling reservation: " . $e->getMessage();
            }
        }
    }
}

// Get available books
$query = "SELECT * FROM books WHERE status = 'available' ORDER BY title";
$stmt = $db->prepare($query);
$stmt->execute();
$available_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's active reservations
$query = "SELECT r.*, b.title, b.author 
          FROM reservations r 
          JOIN books b ON r.book_id = b.id 
          WHERE r.user_id = ? AND r.status = 'active' 
          ORDER BY r.reservation_date DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's borrowed books
$query = "SELECT t.*, b.title, b.author, t.due_date 
          FROM transactions t 
          JOIN books b ON t.book_id = b.id 
          WHERE t.user_id = ? AND t.transaction_type = 'borrow' AND t.status = 'active' 
          ORDER BY t.transaction_date DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check borrowing limit
$query = "SELECT COUNT(*) as count FROM transactions WHERE user_id = ? AND transaction_type = 'borrow' AND status = 'active'";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$borrow_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
$can_borrow = $borrow_count < 3;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">Smart Library - Student Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                    <a href="../logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if (isset($success_message)): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Borrowing Status -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Borrowing Status
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Books borrowed: <?php echo $borrow_count; ?>/3</p>
                        <?php if (!$can_borrow): ?>
                            <p class="text-red-600 font-medium">You have reached the maximum borrowing limit (3 books)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Borrowed Books -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">My Borrowed Books</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($borrowed_books)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No borrowed books</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($borrowed_books as $book): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                    <br><span class="text-xs text-gray-400">by <?php echo htmlspecialchars($book['author']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($book['transaction_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($book['due_date'])); ?>
                                    <?php if (strtotime($book['due_date']) < time()): ?>
                                        <span class="text-red-600 text-xs">(Overdue)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Reservations -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">My Reservations</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No active reservations</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($reservation['title']); ?>
                                    <br><span class="text-xs text-gray-400">by <?php echo htmlspecialchars($reservation['author']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="cancel_reservation">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Available Books -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Available Books</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($available_books as $book): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($book['author']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($book['isbn']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="reserve">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">Reserve</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
