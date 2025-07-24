<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isBPKAD()) {
    header('Location: /index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = sanitize($_POST['id']);
    
    try {
        // Update the budget submission status
        $stmt = $pdo->prepare("
            UPDATE budget_submissions 
            SET status = 'rejected', 
                approved_by = ?, 
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ? AND status = 'pending'
        ");
        $stmt->execute([$_SESSION['user_id'], $id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Budget submission has been rejected.";
        } else {
            $_SESSION['error'] = "Unable to reject the budget submission. It may have already been processed.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to the budget submissions page
header('Location: index.php');
exit();
?>
