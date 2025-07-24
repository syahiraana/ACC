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
    $employee_id = sanitize($_POST['employee_id']);
    $month = sanitize($_POST['month']);
    $year = sanitize($_POST['year']);
    $basic_salary = sanitize($_POST['basic_salary']);
    $allowances = sanitize($_POST['allowances']);
    $deductions = sanitize($_POST['deductions']);
    $net_salary = $basic_salary + $allowances - $deductions;

    try {
        $stmt = $pdo->prepare("INSERT INTO payroll (employee_id, month, year, basic_salary, allowances, deductions, net_salary, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$employee_id, $month, $year, $basic_salary, $allowances, $deductions, $net_salary]);
        $success = "Payroll data has been successfully added.";
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
    <title>Payroll Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">

</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
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
                <a href="../dashboard.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">dashboard</i>
                    Dashboard
                </a>
                <a href="./" class="block py-2.5 px-4 rounded bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">payments</i>
                    Payroll Management
                </a>
                <a href="/ACC/admin/employee/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <span class="material-icons mr-2 align-middle">description</span>
                    Report Data
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
                        <span class="text-xl font-semibold">Payroll Management</span>
                    </div>
                    <div class="flex items-center">
                        <span class="material-icons mr-2">account_circle</span>
                        <span>Admin</span>
                    </div>
                </div>
            </header>

            <!-- Payroll Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <?php if (isset($success)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Payroll Form -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-6">Add New Payroll</h2>
                        <form method="POST" action="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Employee
                                    </label>
                                    <select name="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <?php
                                        $stmt = $pdo->query("SELECT id, name FROM employees ORDER BY name");
                                        while ($row = $stmt->fetch()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Month
                                    </label>
                                    <select name="month" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            echo "<option value='$i'>" . getMonthName($i) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Year
                                    </label>
                                    <select name="year" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = $currentYear; $i >= $currentYear - 2; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Basic Salary
                                    </label>
                                    <input type="number" name="basic_salary" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Allowances
                                    </label>
                                    <input type="number" name="allowances" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Deductions
                                    </label>
                                    <input type="number" name="deductions" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Submit Payroll
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Payroll List -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold">Recent Payroll Entries</h2>
                        </div>
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $pdo->query("
                                    SELECT p.*, e.name as employee_name 
                                    FROM payroll p 
                                    JOIN employees e ON p.employee_id = e.id 
                                    ORDER BY p.created_at DESC 
                                    LIMIT 10
                                ");
                                while ($row = $stmt->fetch()) {
                                    echo "<tr>";
                                    echo "<td class='px-6 py-4'>" . $row['employee_name'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . getMonthName($row['month']) . " " . $row['year'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . formatCurrency($row['basic_salary']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . formatCurrency($row['net_salary']) . "</td>";
                                    echo "<td class='px-6 py-4'><span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full " . 
                                        ($row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                        ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) . 
                                        "'>" . ucfirst($row['status']) . "</span></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
