<?php
session_start();
require_once '../config/database.php';
require_once '../config/semester_manager.php';

// Check if user is logged in and is staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$semesterManager = new SemesterManager();
$current_semester = $semesterManager->getCurrentSemester();

// Handle transactions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'borrow':
            $user_id = $_POST['user_id'] ?? '';
            $book_id = $_POST['book_id'] ?? '';
            
            // Validate input
            if (empty($user_id) || empty($book_id)) {
                $error_message = "Please select both a user and a book to borrow.";
            } else {
                // Check if user exists and get details
                $query = "SELECT id, role, first_name, last_name, student_id, is_active FROM users WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    $error_message = "User not found. Please select a valid user.";
                } elseif (!$user['is_active']) {
                    $error_message = "User account is inactive. Cannot borrow books.";
                } else {
                // Check if book exists and get details (exclude archived from borrowing)
                $query = "SELECT id, title, author, status FROM books WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$book_id]);
                    $book = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$book) {
                        $error_message = "Book not found. Please select a valid book.";
                } elseif ($book['status'] === 'archived') {
                    $error_message = "Book '" . htmlspecialchars($book['title']) . "' is archived and cannot be borrowed.";
                } elseif ($book['status'] !== 'available') {
                        $status_message = ucfirst($book['status']);
                        $error_message = "Book '{$book['title']}' is currently {$status_message}. Only available books can be borrowed.";
                    } else {
                        // Check borrowing limits based on user role
                        if ($user['role'] === 'student') {
                            // Check if student can borrow more books this semester
                            $current_borrow_count = $semesterManager->getStudentBorrowingCount($user_id, $current_semester['id']);
                            if ($current_borrow_count >= 3) {
                                $error_message = "Student {$user['first_name']} {$user['last_name']} (ID: {$user['student_id']}) has reached the maximum borrowing limit (3 books per semester). Current: {$current_borrow_count}/3";
                            } else {
                                // Proceed with student borrowing
                                $due_date = date('Y-m-d', strtotime('+14 days')); // 14 days from now
                                
                                $db->beginTransaction();
                                try {
                                    // Update book status
                                    $query = "UPDATE books SET status = 'borrowed' WHERE id = ? AND status = 'available'";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute([$book_id]);
                                    
                                    if ($stmt->rowCount() > 0) {
                                        // Create transaction record with semester
                                        $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date) VALUES (?, ?, ?, 'borrow', ?)";
                                        $stmt = $db->prepare($query);
                                        $stmt->execute([$user_id, $book_id, $current_semester['id'], $due_date]);
                                        
                                        // Check if there are any reservations for this book and cancel them
                                        $query = "UPDATE reservations SET status = 'cancelled' WHERE book_id = ? AND status = 'active'";
                                        $stmt = $db->prepare($query);
                                        $stmt->execute([$book_id]);
                                        
                                        $db->commit();
                                        $success_message = "Book '{$book['title']}' borrowed successfully by {$user['first_name']} {$user['last_name']}! Due date: " . date('M d, Y', strtotime($due_date));
                                    } else {
                                        $db->rollback();
                                        $error_message = "Book '{$book['title']}' is no longer available. It may have been borrowed by someone else.";
                                    }
                                } catch (Exception $e) {
                                    $db->rollback();
                                    $error_message = "Error processing borrowing: " . $e->getMessage();
                                }
                            }
                        } else {
                            // Teacher or other roles - unlimited borrowing
                            $due_date = date('Y-m-d', strtotime('+30 days')); // 30 days for teachers
                            
                            $db->beginTransaction();
                            try {
                                // Update book status
                                $query = "UPDATE books SET status = 'borrowed' WHERE id = ? AND status = 'available'";
                                $stmt = $db->prepare($query);
                                $stmt->execute([$book_id]);
                                
                                if ($stmt->rowCount() > 0) {
                                    // Create transaction record with semester
                                    $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, due_date) VALUES (?, ?, ?, 'borrow', ?)";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute([$user_id, $book_id, $current_semester['id'], $due_date]);
                                    
                                    // Check if there are any reservations for this book and cancel them
                                    $query = "UPDATE reservations SET status = 'cancelled' WHERE book_id = ? AND status = 'active'";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute([$book_id]);
                                    
                                    $db->commit();
                                    $success_message = "Book '{$book['title']}' borrowed successfully by {$user['first_name']} {$user['last_name']}! Due date: " . date('M d, Y', strtotime($due_date));
                                } else {
                                    $db->rollback();
                                    $error_message = "Book '{$book['title']}' is no longer available. It may have been borrowed by someone else.";
                                }
                            } catch (Exception $e) {
                                $db->rollback();
                                $error_message = "Error processing borrowing: " . $e->getMessage();
                            }
                        }
                    }
                }
            }
            break;
            
        case 'return':
            $transaction_id = $_POST['transaction_id'] ?? '';
            
            if (empty($transaction_id)) {
                $error_message = "Please select a transaction to return.";
            } else {
                $db->beginTransaction();
                try {
                    // Get transaction details with user and book information
                    $query = "SELECT t.*, b.id as book_id, b.title as book_title, b.author, u.first_name, u.last_name, u.student_id
                             FROM transactions t 
                             JOIN books b ON t.book_id = b.id 
                             JOIN users u ON t.user_id = u.id
                             WHERE t.id = ? AND t.transaction_type = 'borrow' AND t.status = 'active'";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$transaction_id]);
                    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$transaction) {
                        $db->rollback();
                        $error_message = "Transaction not found or already completed. Please refresh the page and try again.";
                    } else {
                        // Update book status
                        $query = "UPDATE books SET status = 'available' WHERE id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$transaction['book_id']]);
                        
                        if ($stmt->rowCount() > 0) {
                            // Update transaction status
                            $query = "UPDATE transactions SET status = 'completed', return_date = NOW() WHERE id = ?";
                            $stmt = $db->prepare($query);
                            $stmt->execute([$transaction_id]);
                            
                            // Create return transaction record with semester_id
                            $query = "INSERT INTO transactions (user_id, book_id, semester_id, transaction_type, status) VALUES (?, ?, ?, 'return', 'completed')";
                            $stmt = $db->prepare($query);
                            $stmt->execute([$transaction['user_id'], $transaction['book_id'], $transaction['semester_id']]);
                            
                            $db->commit();
                            $user_name = $transaction['first_name'] . ' ' . $transaction['last_name'];
                            $student_id = $transaction['student_id'] ? ' (ID: ' . $transaction['student_id'] . ')' : '';
                            $success_message = "Book '{$transaction['book_title']}' returned successfully by {$user_name}{$student_id}!";
                        } else {
                            $db->rollback();
                            $error_message = "Failed to update book status. The book may have already been returned.";
                        }
                    }
                } catch (Exception $e) {
                    $db->rollback();
                    $error_message = "Error processing return: " . $e->getMessage();
                }
            }
            break;
    }
}

// Get all users for borrowing
$query = "SELECT id, username, first_name, last_name, role, student_id FROM users ORDER BY role, first_name";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available books (exclude archived by status condition)
$query = "SELECT * FROM books WHERE status = 'available' ORDER BY title";
$stmt = $db->prepare($query);
$stmt->execute();
$available_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active borrows for current semester
$query = "SELECT t.*, u.first_name, u.last_name, u.role, u.student_id, b.title, b.author, b.price, s.name as semester_name
          FROM transactions t 
          JOIN users u ON t.user_id = u.id 
          JOIN books b ON t.book_id = b.id 
          JOIN semesters s ON t.semester_id = s.id
          WHERE t.transaction_type = 'borrow' AND t.status = 'active' AND t.semester_id = ?
          ORDER BY t.transaction_date DESC";
$stmt = $db->prepare($query);
$stmt->execute([$current_semester['id']]);
$active_borrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">Smart Library - Staff Dashboard</h1>
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

        <!-- Current Semester Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Current Semester: <?php echo htmlspecialchars($current_semester['name']); ?>
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Period: <?php echo date('M d, Y', strtotime($current_semester['start_date'])); ?> - <?php echo date('M d, Y', strtotime($current_semester['end_date'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
                <div class="space-x-4">
                    <a href="clearance_management.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Manage Clearance
                    </a>
                    <a href="../admin/user_management.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Manage Users
                    </a>
                    <a href="penalty_management.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Manage Penalties
                    </a>
                </div>
            </div>
        </div>

        <!-- Borrow Book Form -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Borrow Book</h2>
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Staff Borrowing Guidelines</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Students can borrow up to 3 books per semester (14 days each)</li>
                                <li>Teachers can borrow unlimited books (30 days each)</li>
                                <li>Staff can manually process any available book</li>
                                <li>Reservations are automatically cancelled when books are borrowed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" class="grid grid-cols-1 gap-4 sm:grid-cols-3" onsubmit="return validateBorrowForm()">
                <input type="hidden" name="action" value="borrow">
                <div>
                    <label for="user_search" class="block text-sm font-medium text-gray-700">Search User <span class="text-red-500">*</span></label>
                    <input type="text" id="user_search" placeholder="Search by name, student ID, email..." 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <select name="user_id" id="user_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" style="display: none;">
                        <option value="">Select User</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" data-name="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . ucfirst($user['role']) . ')' . ($user['student_id'] ? ' - ID: ' . $user['student_id'] : '')); ?>" 
                                    data-student-id="<?php echo htmlspecialchars($user['student_id'] ?? ''); ?>"
                                    data-email="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . ucfirst($user['role']) . ')' . ($user['student_id'] ? ' - ID: ' . $user['student_id'] : '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="user_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" style="display: none;"></div>
                </div>
                <div>
                    <label for="book_search" class="block text-sm font-medium text-gray-700">Search Book <span class="text-red-500">*</span></label>
                    <input type="text" id="book_search" placeholder="Search by title, author, ISBN, category..." 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <select name="book_id" id="book_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" style="display: none;">
                        <option value="">Select Book</option>
                        <?php foreach ($available_books as $book): ?>
                            <option value="<?php echo $book['id']; ?>" data-name="<?php echo htmlspecialchars($book['title'] . ' by ' . $book['author'] . ' [' . $book['category'] . '] ISBN: ' . $book['isbn']); ?>"
                                    data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                    data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                    data-isbn="<?php echo htmlspecialchars($book['isbn']); ?>"
                                    data-category="<?php echo htmlspecialchars($book['category']); ?>">
                                <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author'] . ' [' . $book['category'] . '] ISBN: ' . $book['isbn']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="book_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" style="display: none;"></div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Borrow Book
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Borrows -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Active Borrows</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($active_borrows as $borrow): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($borrow['first_name'] . ' ' . $borrow['last_name']); ?>
                                <span class="text-xs text-gray-500">(<?php echo ucfirst($borrow['role']); ?>)</span>
                                <?php if ($borrow['student_id']): ?>
                                    <br><span class="text-xs text-blue-600">ID: <?php echo htmlspecialchars($borrow['student_id']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($borrow['title']); ?>
                                <br><span class="text-xs text-gray-400">by <?php echo htmlspecialchars($borrow['author']); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($borrow['transaction_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($borrow['due_date'])); ?>
                                <?php if (strtotime($borrow['due_date']) < time()): ?>
                                    <span class="text-red-600 text-xs">(Overdue)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="transaction_id" value="<?php echo $borrow['id']; ?>">
                                    <button type="submit" class="text-green-600 hover:text-green-900">Return</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // User search functionality
        const userSearch = document.getElementById('user_search');
        const userSelect = document.getElementById('user_id');
        const userDropdown = document.getElementById('user_dropdown');
        const userOptions = Array.from(userSelect.options);

        userSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredUsers = userOptions.filter(option => {
                if (option.value === '') return false;
                const text = option.textContent.toLowerCase();
                const studentId = option.dataset.studentId ? option.dataset.studentId.toLowerCase() : '';
                const email = option.dataset.email ? option.dataset.email.toLowerCase() : '';
                return text.includes(searchTerm) || studentId.includes(searchTerm) || email.includes(searchTerm);
            });

            userDropdown.innerHTML = '';
            
            if (searchTerm.length > 0) {
                userDropdown.style.display = 'block';
                filteredUsers.forEach(option => {
                    if (option.value !== '') {
                        const div = document.createElement('div');
                        div.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50';
                        div.innerHTML = `
                            <div class="font-medium">${option.textContent}</div>
                            ${option.dataset.email ? `<div class="text-xs text-gray-500">${option.dataset.email}</div>` : ''}
                        `;
                        div.onclick = function() {
                            userSelect.value = option.value;
                            userSearch.value = option.textContent;
                            userDropdown.style.display = 'none';
                        };
                        userDropdown.appendChild(div);
                    }
                });
            } else {
                userDropdown.style.display = 'none';
            }
        });

        // Book search functionality
        const bookSearch = document.getElementById('book_search');
        const bookSelect = document.getElementById('book_id');
        const bookDropdown = document.getElementById('book_dropdown');
        const bookOptions = Array.from(bookSelect.options);

        bookSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredBooks = bookOptions.filter(option => {
                if (option.value === '') return false;
                const text = option.textContent.toLowerCase();
                const title = option.dataset.title ? option.dataset.title.toLowerCase() : '';
                const author = option.dataset.author ? option.dataset.author.toLowerCase() : '';
                const isbn = option.dataset.isbn ? option.dataset.isbn.toLowerCase() : '';
                const category = option.dataset.category ? option.dataset.category.toLowerCase() : '';
                return text.includes(searchTerm) || title.includes(searchTerm) || author.includes(searchTerm) || isbn.includes(searchTerm) || category.includes(searchTerm);
            });

            bookDropdown.innerHTML = '';
            
            if (searchTerm.length > 0) {
                bookDropdown.style.display = 'block';
                filteredBooks.forEach(option => {
                    if (option.value !== '') {
                        const div = document.createElement('div');
                        div.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50';
                        div.innerHTML = `
                            <div class="font-medium">${option.dataset.title}</div>
                            <div class="text-xs text-gray-500">by ${option.dataset.author} • ${option.dataset.category} • ISBN: ${option.dataset.isbn}</div>
                        `;
                        div.onclick = function() {
                            bookSelect.value = option.value;
                            bookSearch.value = option.textContent;
                            bookDropdown.style.display = 'none';
                        };
                        bookDropdown.appendChild(div);
                    }
                });
            } else {
                bookDropdown.style.display = 'none';
            }
        });

        // Hide dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#user_search') && !e.target.closest('#user_dropdown')) {
                userDropdown.style.display = 'none';
            }
            if (!e.target.closest('#book_search') && !e.target.closest('#book_dropdown')) {
                bookDropdown.style.display = 'none';
            }
        });

        // Form validation
        function validateBorrowForm() {
            const userId = document.getElementById('user_id').value;
            const bookId = document.getElementById('book_id').value;
            
            if (!userId) {
                alert('Please select a user to borrow the book.');
                document.getElementById('user_search').focus();
                return false;
            }
            
            if (!bookId) {
                alert('Please select a book to borrow.');
                document.getElementById('book_search').focus();
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>
