<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get employee data
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $position = sanitize($_POST['position']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $bank_account = sanitize($_POST['bank_account']);
    $account_number = sanitize($_POST['account_number']);
    $nip = sanitize($_POST['nip']);
    $present = sanitize($_POST['present']);
    $sick = sanitize($_POST['sick']);
    $permit = sanitize($_POST['permit']);
    $absent = sanitize($_POST['absent']);

    try {
        $stmt = $pdo->prepare("
            UPDATE employees SET 
                name = ?, position = ?, phone = ?, email = ?, 
                address = ?, bank_account = ?, account_number = ?, 
                nip = ?, present_count = ?, sick_count = ?, 
                permit_count = ?, absent_count = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $name, $position, $phone, $email, 
            $address, $bank_account, $account_number, 
            $nip, $present, $sick, $permit, $absent, $id
        ]);
        
        $_SESSION['success'] = "Employee data has been successfully updated.";
        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee - Admin Dashboard</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-green-700 text-white">
            <div class="p-4 border-b border-green-600">
                <div class="flex items-center">
                    <img src="/ACC/assets/images/logo.png" alt="Logo" class="w-8 h-8 mr-2">
                    <h2 class="text-xl font-semibold">DINAS PENDIDIKAN</h2>
                </div>
            </div>
            <nav class="p-4">
                <a href="../dashboard.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">dashboard</i>
                    Dashboard
                </a>
                <a href="./" class="block py-2.5 px-4 rounded bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">groups</i>
                    Data Employee
                </a>
                <a href="../payroll/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">payments</i>
                    Payroll Management
                </a>
                <a href="../../logout.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mt-8 text-red-300">
                    <i class="material-icons inline-block align-middle mr-2">logout</i>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700 mr-2">
                            <i class="material-icons">arrow_back</i>
                        </a>
                        <span class="text-xl font-semibold">Edit Employee Data</span>
                    </div>
                    <div class="flex items-center">
                        <span class="material-icons mr-2">account_circle</span>
                        <span>Admin</span>
                    </div>
                </div>
            </header>

            <!-- Edit Form -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <form method="POST" action="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Name
                                    </label>
                                    <input type="text" name="name" required value="<?php echo $employee['name']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Structural Position
                                    </label>
                                    <input type="text" name="position" required value="<?php echo $employee['position']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" name="phone" required value="<?php echo $employee['phone']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Bank Account
                                    </label>
                                    <select name="bank_account" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="BNI" <?php echo $employee['bank_account'] === 'BNI' ? 'selected' : ''; ?>>BNI</option>
                                        <option value="BRI" <?php echo $employee['bank_account'] === 'BRI' ? 'selected' : ''; ?>>BRI</option>
                                        <option value="Mandiri" <?php echo $employee['bank_account'] === 'Mandiri' ? 'selected' : ''; ?>>Mandiri</option>
                                        <option value="BCA" <?php echo $employee['bank_account'] === 'BCA' ? 'selected' : ''; ?>>BCA</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" required value="<?php echo $employee['email']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Account Number
                                    </label>
                                    <input type="text" name="account_number" required value="<?php echo $employee['account_number']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Address
                                    </label>
                                    <textarea name="address" required rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    ><?php echo $employee['address']; ?></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIP
                                    </label>
                                    <input type="text" name="nip" required value="<?php echo $employee['nip']; ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div class="grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Present
                                        </label>
                                        <input type="number" name="present" value="<?php echo $employee['present_count']; ?>" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Sick
                                        </label>
                                        <input type="number" name="sick" value="<?php echo $employee['sick_count']; ?>" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Permit
                                        </label>
                                        <input type="number" name="permit" value="<?php echo $employee['permit_count']; ?>" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Absent
                                        </label>
                                        <input type="number" name="absent" value="<?php echo $employee['absent_count']; ?>" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="index.php" 
                                    class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
