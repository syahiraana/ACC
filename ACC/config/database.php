<?php
$host = 'localhost';
$dbname = 'payroll_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Gunakan error_log agar tidak mengganggu header()
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection error.");
}
