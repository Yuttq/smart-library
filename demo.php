<?php
/**
 * Smart Library Management System - Demo Page
 * Interactive demo showcasing system features
 */

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Library - Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="landing.php" class="flex items-center">
                        <i class="fas fa-book-open text-2xl text-blue-600"></i>
                        <span class="ml-2 text-xl font-bold text-gray-900">Smart Library</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="landing.php" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Demo Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Interactive Demo
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                Experience the Smart Library Management System
            </p>
        </div>
    </section>

    <!-- Demo Credentials -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Demo Credentials
                </h2>
                <p class="text-xl text-gray-600">
                    Use these accounts to explore different user roles
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Librarian -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-shield text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Librarian</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Username:</strong> librarian</p>
                            <p><strong>Password:</strong> password</p>
                        </div>
                        <a href="login.php" class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-300">
                            Login as Librarian
                        </a>
                    </div>
                </div>

                <!-- Staff -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Staff</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Username:</strong> staff</p>
                            <p><strong>Password:</strong> password</p>
                        </div>
                        <a href="login.php" class="mt-4 inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition duration-300">
                            Login as Staff
                        </a>
                    </div>
                </div>

                <!-- Teacher -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Teacher</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Username:</strong> teacher1</p>
                            <p><strong>Password:</strong> password</p>
                        </div>
                        <a href="login.php" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
                            Login as Teacher
                        </a>
                    </div>
                </div>

                <!-- Student -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Student</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Username:</strong> student1</p>
                            <p><strong>Password:</strong> password</p>
                        </div>
                        <a href="login.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                            Login as Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Features -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    What You Can Test
                </h2>
                <p class="text-xl text-gray-600">
                    Explore the system features with different user roles
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Librarian Features -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-shield text-red-600 mr-3"></i>
                        Librarian Features
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Add, edit, and delete books</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Manage book inventory</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>User management and administration</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>View system reports and analytics</span>
                        </li>
                    </ul>
                </div>

                <!-- Staff Features -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-tie text-purple-600 mr-3"></i>
                        Staff Features
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Process book borrowing transactions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Handle book returns</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Manage penalties and fines</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Process clearance for users</span>
                        </li>
                    </ul>
                </div>

                <!-- Teacher Features -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chalkboard-teacher text-green-600 mr-3"></i>
                        Teacher Features
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Unlimited book borrowing</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Make book reservations</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>View borrowed books and fines</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Academic year tracking</span>
                        </li>
                    </ul>
                </div>

                <!-- Student Features -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-graduation-cap text-blue-600 mr-3"></i>
                        Student Features
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Borrow up to 3 books per semester</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Make book reservations</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>View borrowed books and fines</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Track borrowing history</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Scenarios -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Demo Scenarios
                </h2>
                <p class="text-xl text-gray-600">
                    Try these scenarios to experience the full system
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Scenario 1 -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Scenario 1: Book Management</h3>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li>1. Login as Librarian</li>
                        <li>2. Add new books to inventory</li>
                        <li>3. Edit book information</li>
                        <li>4. View book status</li>
                    </ol>
                </div>

                <!-- Scenario 2 -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Scenario 2: Borrowing Process</h3>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li>1. Login as Staff</li>
                        <li>2. Search for a student</li>
                        <li>3. Search for a book</li>
                        <li>4. Process borrowing transaction</li>
                    </ol>
                </div>

                <!-- Scenario 3 -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Scenario 3: Penalty Management</h3>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li>1. Login as Staff</li>
                        <li>2. Go to Penalty Management</li>
                        <li>3. View overdue books</li>
                        <li>4. Process penalty payments</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Ready to Explore?
            </h2>
            <p class="text-xl mb-8 text-blue-100">
                Choose a role and start exploring the system features.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="login.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Start Demo
                </a>
                <a href="landing.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2024 Smart Library Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.js"></script>
</body>
</html>
