<?php
session_start();
require_once 'classes/Authentication.php';

// Redirect if already logged in
if (Authentication::isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$auth = new Authentication();
$errors = [];
$success_message = '';

if ($_POST) {
    $result = $auth->register($_POST);
    
    if ($result['success']) {
        $success_message = $result['message'];
        // Clear form data
        $_POST = [];
    } else {
        $errors = $result['errors'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Smart Library System
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Create your account
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success_message); ?>
                    <br><a href="login.php" class="text-green-800 underline">Click here to login</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input id="first_name" name="first_name" type="text" required 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['first_name']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['first_name']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input id="last_name" name="last_name" type="text" required 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['last_name']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['last_name']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input id="username" name="username" type="text" required 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['username']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['username'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['username']); ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input id="email" name="email" type="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" required 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['role']) ? 'border-red-500' : ''; ?>">
                    <option value="">Select your role</option>
                    <option value="student" <?php echo (($_POST['role'] ?? '') === 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="teacher" <?php echo (($_POST['role'] ?? '') === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['role']); ?></p>
                <?php endif; ?>
            </div>
            
            <div id="student_id_field" style="display: none;">
                <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                <input id="student_id" name="student_id" type="text" 
                       value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
                       placeholder="e.g., 1349802"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['student_id']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['student_id'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['student_id']); ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['password']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="confirm_password" name="confirm_password" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Account
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
        
        // Show/hide student ID field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const studentIdField = document.getElementById('student_id_field');
            const studentIdInput = document.getElementById('student_id');
            
            if (this.value === 'student') {
                studentIdField.style.display = 'block';
                studentIdInput.required = true;
            } else {
                studentIdField.style.display = 'none';
                studentIdInput.required = false;
                studentIdInput.value = '';
            }
        });
        
        // Show student ID field if role is already selected (for form validation errors)
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const studentIdField = document.getElementById('student_id_field');
            const studentIdInput = document.getElementById('student_id');
            
            if (roleSelect.value === 'student') {
                studentIdField.style.display = 'block';
                studentIdInput.required = true;
            }
        });
    </script>
</body>
</html>
