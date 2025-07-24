<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication functions
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php');
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isBPKAD() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'bpkad';
}

// Sanitization and validation
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format functions
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function getMonthName($month) {
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March',
        4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September',
        10 => 'October', 11 => 'November', 12 => 'December'
    ];
    return $months[$month] ?? '';
}

// Navigation functions
function getActivePage() {
    $path = $_SERVER['PHP_SELF'];
    if (strpos($path, 'dashboard.php') !== false) return 'dashboard';
    if (strpos($path, 'employee') !== false) return 'employee';
    if (strpos($path, 'payroll') !== false) return 'payroll';
    if (strpos($path, 'budget') !== false) return 'budget';
    return '';
}

// Database helper functions
function getEmployeeById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getEmployeePayroll($pdo, $employee_id, $month, $year) {
    $stmt = $pdo->prepare("
        SELECT * FROM payroll 
        WHERE employee_id = ? AND month = ? AND year = ?
    ");
    $stmt->execute([$employee_id, $month, $year]);
    return $stmt->fetch();
}

function calculateNetSalary($basic_salary, $allowances, $deductions) {
    return $basic_salary + $allowances - $deductions;
}

// Attendance helper functions
function updateAttendance($pdo, $employee_id, $type, $count) {
    $valid_types = ['present_count', 'sick_count', 'permit_count', 'absent_count'];
    if (!in_array($type, $valid_types)) return false;

    $stmt = $pdo->prepare("
        UPDATE employees 
        SET $type = ? 
        WHERE id = ?
    ");
    return $stmt->execute([$count, $employee_id]);
}

// Report helper functions
function getMonthlyReport($pdo, $month, $year) {
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            p.basic_salary,
            p.allowances,
            p.deductions,
            p.net_salary
        FROM employees e
        LEFT JOIN payroll p ON e.id = p.employee_id 
            AND p.month = ? AND p.year = ?
        ORDER BY e.name
    ");
    $stmt->execute([$month, $year]);
    return $stmt->fetchAll();
}

// Error handling
function displayError($message) {
    $_SESSION['error'] = $message;
}

function displaySuccess($message) {
    $_SESSION['success'] = $message;
}

function showMessages() {
    $output = '';
    if (isset($_SESSION['error'])) {
        $output .= '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . 
                  $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        $output .= '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . 
                  $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    return $output;
}
