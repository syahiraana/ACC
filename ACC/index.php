<?php

//login page code

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!file_exists('config/database.php')) {
    die('Error: config/database.php not found');
}
if (!file_exists('includes/functions.php')) {
    die('Error: includes/functions.php not found');
}

require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($pdo)) {
    die('Error: Database connection not established');
}

if (isset($_SESSION['user_id'])) {
    if (function_exists('isAdmin') && function_exists('isBPKAD')) {
        if (isAdmin()) {
            header('Location: admin/dashboard.php');
        } elseif (isBPKAD()) {
            header('Location: bpkad/dashboard.php');
        }
    } else {
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: bpkad/dashboard.php');
        }
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('sanitize')) {
        $username = sanitize($_POST['username']);
    } else {
        $username = trim($_POST['username']);
    }
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Changed from password_verify to direct comparison
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: bpkad/dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Database error occurred";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payroll System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/ACC/assets/css/style.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center login-bg">
    <div class="login-card p-8 rounded-lg shadow-lg w-96">
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-green-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                <span class="material-icons text-white text-4xl">school</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">DINAS PENDIDIKAN</h2>
            <p class="text-gray-600">Payroll Management System</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="username">
                    Username
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <span class="material-icons">person</span>
                    </span>
                    <input type="text" name="username" id="username" required
                        class="form-input pl-10">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <span class="material-icons">lock</span>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="form-input pl-10">
                </div>
            </div>

            <button type="submit"
                class="btn-primary w-full flex items-center justify-center gap-2">
                <span class="material-icons">login</span>
                Login
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600 mb-1">Default credentials:</p>
            <div class="space-y-1">
                <p class="text-gray-500"><span class="font-semibold">Admin:</span> admin / admin123</p>
                <p class="text-gray-500"><span class="font-semibold">BPKAD:</span> bpkad / bpkad123</p>
            </div>
        </div>
    </div>
</body>
</html>