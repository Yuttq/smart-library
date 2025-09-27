<?php
/**
 * Smart Library Management System - Landing Page
 * Professional landing page with system overview and features
 */

// Check if user is already logged in
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect to appropriate dashboard based on role
    $role = $_SESSION['role'];
    switch ($role) {
        case 'librarian':
            header('Location: librarian/dashboard.php');
            break;
        case 'staff':
            header('Location: staff/dashboard.php');
            break;
        case 'teacher':
            header('Location: teacher/dashboard.php');
            break;
        case 'student':
            header('Location: student/dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Library Management System</title>
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
                    <div class="flex-shrink-0">
                        <i class="fas fa-book-open text-2xl text-blue-600"></i>
                        <span class="ml-2 text-xl font-bold text-gray-900">Smart Library</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="demo.php" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition duration-300">
                        <i class="fas fa-play mr-2"></i>Demo
                    </a>
                    <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="register.php" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Smart Library Management System
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Modernize your library operations with our comprehensive digital solution
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="demo.php" class="bg-purple-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-600 transition duration-300">
                        <i class="fas fa-play mr-2"></i>Try Demo
                    </a>
                    <a href="login.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Get Started
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        <i class="fas fa-info-circle mr-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Powerful Features
                </h2>
                <p class="text-xl text-gray-600">
                    Everything you need to manage your library efficiently
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Role-Based Access</h3>
                        <p class="text-gray-600">Different interfaces for Students, Teachers, Staff, and Librarians with appropriate permissions.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Book Management</h3>
                        <p class="text-gray-600">Complete inventory management with add, edit, delete, and status tracking capabilities.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exchange-alt text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Borrowing System</h3>
                        <p class="text-gray-600">Automated borrowing and returning with real-time search and transaction tracking.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calculator text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Penalty Management</h3>
                        <p class="text-gray-600">Automatic penalty calculations with role-based rates and payment processing.</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-alt text-2xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Semester Management</h3>
                        <p class="text-gray-600">Academic year tracking with semester-based borrowing limits and clearance processing.</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Search</h3>
                        <p class="text-gray-600">Real-time search functionality for users and books with instant results.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles Section -->
    <section class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Designed for Everyone
                </h2>
                <p class="text-xl text-gray-600">
                    Tailored interfaces for different user types
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Student -->
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Students</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Borrow up to 3 books</li>
                        <li>• Make reservations</li>
                        <li>• View fines</li>
                        <li>• Track borrowing history</li>
                    </ul>
                </div>

                <!-- Teacher -->
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Teachers</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Unlimited borrowing</li>
                        <li>• Make reservations</li>
                        <li>• View fines</li>
                        <li>• Academic year tracking</li>
                    </ul>
                </div>

                <!-- Staff -->
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Staff</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Process borrowing</li>
                        <li>• Handle returns</li>
                        <li>• Manage penalties</li>
                        <li>• Process clearance</li>
                    </ul>
                </div>

                <!-- Librarian -->
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Librarians</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Manage book inventory</li>
                        <li>• User management</li>
                        <li>• System administration</li>
                        <li>• View reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Why Choose Smart Library?
                </h2>
                <p class="text-xl text-gray-600">
                    Modern technology meets library management
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Streamlined Operations</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-green-100 w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Automated Processes</h4>
                                <p class="text-gray-600">Reduce manual work with automated penalty calculations and transaction processing.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 w-8 h-8 rounded-full flex items-center mr-4 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Real-time Updates</h4>
                                <p class="text-gray-600">Live status updates and instant search results for better user experience.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 w-8 h-8 rounded-full flex items-center mr-4 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Error Prevention</h4>
                                <p class="text-gray-600">Built-in validation and error handling to prevent common mistakes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 p-8 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">Performance Metrics</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Efficiency</span>
                                <span class="font-semibold text-green-600">95%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">User Satisfaction</span>
                                <span class="font-semibold text-green-600">98%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Error Reduction</span>
                                <span class="font-semibold text-green-600">90%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Ready to Modernize Your Library?
            </h2>
            <p class="text-xl mb-8 text-blue-100">
                Join the digital transformation and improve your library operations today.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="login.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login Now
                </a>
                <a href="register.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-book-open text-2xl text-blue-400 mr-2"></i>
                        <span class="text-xl font-bold">Smart Library</span>
                    </div>
                    <p class="text-gray-400">
                        Modern library management system designed to streamline operations and improve user experience.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition duration-300">Login</a></li>
                        <li><a href="register.php" class="text-gray-400 hover:text-white transition duration-300">Register</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition duration-300">Features</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <div class="space-y-2 text-gray-400">
                        <p><i class="fas fa-envelope mr-2"></i>support@smartlibrary.com</p>
                        <p><i class="fas fa-phone mr-2"></i>+1 hahahahahahahah </p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>CTU, University Campus</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Smart Library Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.js"></script>
</body>
</html>
