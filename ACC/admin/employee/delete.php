<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // First check if there are any related payroll records
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM payroll WHERE employee_id = ?");
        $stmt->execute([$id]);
        $hasPayrollRecords = $stmt->fetchColumn() > 0;

        if ($hasPayrollRecords) {
            $_SESSION['error'] = "Cannot delete employee. There are payroll records associated with this employee.";
        } else {
            // If no payroll records exist, proceed with deletion
            $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Employee has been successfully deleted.";
            } else {
                $_SESSION['error'] = "Employee not found.";
            }
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to the employee list
header('Location: index.php');
exit();
?>
