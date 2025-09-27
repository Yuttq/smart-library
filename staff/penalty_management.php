<?php
session_start();
require_once '../config/database.php';
require_once '../config/penalty_manager.php';

// Check if user is logged in and is staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../login.php');
    exit();
}

$penalty_manager = new PenaltyManager();
$database = new Database();
$db = $database->getConnection();

// Handle penalty operations
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_overdue':
            $updated = $penalty_manager->updateOverdueStatus();
            $success_message = "Updated {$updated} transactions to overdue status";
            break;
            
        case 'process_payment':
            $transaction_id = $_POST['transaction_id'] ?? '';
            $amount_paid = $_POST['amount_paid'] ?? '';
            
            if (!empty($transaction_id) && !empty($amount_paid)) {
                try {
                    $penalty_manager->processPenaltyPayment($transaction_id, $amount_paid);
                    $success_message = "Penalty payment processed successfully!";
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                }
            }
            break;
            
        case 'waive_penalty':
            $transaction_id = $_POST['transaction_id'] ?? '';
            
            if (!empty($transaction_id)) {
                try {
                    $query = "UPDATE transactions SET penalty_paid = TRUE, penalty_amount = 0 WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$transaction_id]);
                    
                    $query = "INSERT INTO fines (user_id, transaction_id, amount, reason, status) 
                              SELECT user_id, ?, 0, 'overdue', 'waived' FROM transactions WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$transaction_id, $transaction_id]);
                    
                    $success_message = "Penalty waived successfully!";
                } catch (Exception $e) {
                    $error_message = "Error waiving penalty: " . $e->getMessage();
                }
            }
            break;
    }
}

// Get overdue transactions
$overdue_transactions = $penalty_manager->getOverdueTransactions();

// Get penalty statistics
$stats = $penalty_manager->getPenaltyStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penalty Management - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">Penalty Management</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Overdue</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['total_overdue'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Outstanding</dt>
                                <dd class="text-lg font-medium text-gray-900">₱<?php echo number_format($stats['total_outstanding'] ?? 0, 2); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Collected</dt>
                                <dd class="text-lg font-medium text-gray-900">₱<?php echo number_format($stats['total_collected'] ?? 0, 2); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg Outstanding</dt>
                                <dd class="text-lg font-medium text-gray-900">₱<?php echo number_format($stats['avg_outstanding'] ?? 0, 2); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Overdue Status -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Update Overdue Status</h2>
                    <p class="text-sm text-gray-500">Mark transactions as overdue based on due dates and grace periods</p>
                </div>
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="update_overdue">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Overdue Transactions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Overdue Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penalty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($overdue_transactions)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No overdue transactions</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($overdue_transactions as $transaction): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']); ?>
                                    <br><span class="text-xs text-gray-500">(<?php echo ucfirst($transaction['role']); ?>)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($transaction['title']); ?>
                                    <br><span class="text-xs text-gray-400">by <?php echo htmlspecialchars($transaction['author']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($transaction['due_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <?php echo $transaction['days_overdue']; ?> days
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ₱<?php echo number_format($transaction['calculated_penalty'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button onclick="openPaymentModal(<?php echo $transaction['id']; ?>, <?php echo $transaction['calculated_penalty']; ?>)" 
                                            class="text-green-600 hover:text-green-900">Pay</button>
                                    <button onclick="openWaiveModal(<?php echo $transaction['id']; ?>)" 
                                            class="text-yellow-600 hover:text-yellow-900">Waive</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Process Penalty Payment</h3>
                <form method="POST" id="paymentForm">
                    <input type="hidden" name="action" value="process_payment">
                    <input type="hidden" name="transaction_id" id="payment_transaction_id">
                    <div class="mb-4">
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amount_paid" step="0.01" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePaymentModal()" 
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Process Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Waive Modal -->
    <div id="waiveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Waive Penalty</h3>
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to waive this penalty? This action cannot be undone.</p>
                <form method="POST" id="waiveForm">
                    <input type="hidden" name="action" value="waive_penalty">
                    <input type="hidden" name="transaction_id" id="waive_transaction_id">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeWaiveModal()" 
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Waive Penalty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(transactionId, penaltyAmount) {
            document.getElementById('payment_transaction_id').value = transactionId;
            document.getElementById('amount_paid').value = penaltyAmount;
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function openWaiveModal(transactionId) {
            document.getElementById('waive_transaction_id').value = transactionId;
            document.getElementById('waiveModal').classList.remove('hidden');
        }

        function closeWaiveModal() {
            document.getElementById('waiveModal').classList.add('hidden');
        }
    </script>
</body>
</html>
