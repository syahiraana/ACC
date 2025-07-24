<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isAdmin()) {
    header('Location: /index.php');
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
            INSERT INTO employees (
                name, position, phone, email, address, 
                bank_account, account_number, nip,
                present_count, sick_count, permit_count, absent_count
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $name, $position, $phone, $email, $address,
            $bank_account, $account_number, $nip,
            $present, $sick, $permit, $absent
        ]);
        
        $_SESSION['success'] = "Employee data has been successfully added.";
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
    <title>Employee Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-green-700 text-white transition-all duration-300 ease-in-out">
            <div class="p-4 border-b border-green-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="/ACC/assets/images/logo.png" alt="Logo" class="w-8 h-8 mr-2">
                        <h2 class="text-xl font-semibold sidebar-text">DINAS PENDIDIKAN</h2>
                    </div>
                    <button id="sidebarToggle" class="text-white p-2 rounded-lg hover:bg-green-800 focus:outline-none">
                        <span class="material-icons transform transition-transform duration-300">chevron_left</span>
                    </button>
                </div>
            </div>
            <nav class="p-4">
                <a href="../dashboard.php" class="flex items-center py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons">dashboard</i>
                    <span class="ml-2 sidebar-text">Dashboard</span>
                </a>
                <a href="../payroll/" class="flex items-center py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons">payments</i>
                    <span class="ml-2 sidebar-text">Payroll Management</span>
                </a>
                <a href="../employee/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <span class="material-icons mr-2 align-middle">description</span>
                    Report Data
                </a>

                <a href="../../logout.php" class="flex items-center py-2.5 px-4 rounded hover:bg-green-800 mt-8 text-red-300">
                    <i class="material-icons">logout</i>
                    <span class="ml-2 sidebar-text">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out">
            <!-- Top Bar -->
            <header class="bg-white shadow-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <span class="text-xl font-semibold">Data Employee</span>
                    </div>
                    <div class="flex items-center">
                        <span class="material-icons mr-2">account_circle</span>
                        <span>Admin</span>
                    </div>
                </div>
            </header>

            <!-- Employee Form -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-6">Create Data Employee</h2>
                        <form method="POST" action="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Name
                                    </label>
                                    <input type="text" name="name" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Structural Position
                                    </label>
                                    <input type="text" name="position" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" name="phone" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Bank Account
                                    </label>
                                    <select name="bank_account" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="BNI">BNI</option>
                                        <option value="BRI">BRI</option>
                                        <option value="Mandiri">Mandiri</option>
                                        <option value="BCA">BCA</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Account Number
                                    </label>
                                    <input type="text" name="account_number" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Address
                                    </label>
                                    <textarea name="address" required rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIP
                                    </label>
                                    <input type="text" name="nip" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div class="grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Present
                                        </label>
                                        <input type="number" name="present" value="0" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Sick
                                        </label>
                                        <input type="number" name="sick" value="0" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Permit
                                        </label>
                                        <input type="number" name="permit" value="0" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Absent
                                        </label>
                                        <input type="number" name="absent" value="0" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 text-right">
                                <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Employee List -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold">Employee List</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank Info</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM employees ORDER BY name");
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td class='px-6 py-4'>" . $row['name'] . "<br><span class='text-sm text-gray-500'>NIP: " . $row['nip'] . "</span></td>";
                                        echo "<td class='px-6 py-4'>" . $row['position'] . "</td>";
                                        echo "<td class='px-6 py-4'>" . $row['phone'] . "<br>" . $row['email'] . "</td>";
                                        echo "<td class='px-6 py-4'>" . $row['bank_account'] . "<br>" . $row['account_number'] . "</td>";
                                        echo "<td class='px-6 py-4'>
                                            <span class='text-green-600'>P: " . $row['present_count'] . "</span> 
                                            <span class='text-yellow-600'>S: " . $row['sick_count'] . "</span> 
                                            <span class='text-blue-600'>I: " . $row['permit_count'] . "</span> 
                                            <span class='text-red-600'>A: " . $row['absent_count'] . "</span>
                                        </td>";
                                        echo "<td class='px-6 py-4'>
                                            <a href='edit.php?id=" . $row['id'] . "' class='text-blue-600 hover:text-blue-900 mr-3'>Edit</a>
                                            <a href='delete.php?id=" . $row['id'] . "' class='text-red-600 hover:text-red-900' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../../assets/js/sidebar.js"></script>
</body>
</html>
