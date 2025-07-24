<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

checkLogin();
if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - Payroll System</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Custom Style -->
    <link href="../assets/css/style.css" rel="stylesheet">
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
            <a href="dashboard.php" class="block py-2.5 px-4 rounded bg-green-800 mb-2">
                <span class="material-icons mr-2 align-middle">dashboard</span>
                Dashboard
            </a>
            <a href="payroll/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                <span class="material-icons mr-2 align-middle">payments</span>
                Payroll Management
            </a>
            <a href="/ACC/admin/employee/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                <span class="material-icons mr-2 align-middle">description</span>
                Report Data
            </a>


            <a href="../logout.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mt-8 text-red-300">
                <span class="material-icons mr-2 align-middle">logout</span>
                Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-md">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Dashboard</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <span class="material-icons mr-2">account_circle</span>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-700">
                                <span class="material-icons">groups</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Employees</p>
                                <p class="text-lg font-semibold">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-700">
                                <span class="material-icons">pending_actions</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Pending Payroll</p>
                                <p class="text-lg font-semibold">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM payroll WHERE status = 'pending'");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-700">
                                <span class="material-icons">account_balance</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Budget</p>
                                <p class="text-lg font-semibold">
                                    <?php
                                    $stmt = $pdo->query("SELECT SUM(amount) FROM budget_submissions WHERE status = 'approved'");
                                    echo formatCurrency($stmt->fetchColumn() ?? 0);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
                    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM payroll ORDER BY created_at DESC LIMIT 5");
                                while ($row = $stmt->fetch()) {
                                    $statusClass = $row['status'] === 'approved' ? 'bg-green-100 text-green-800' :
                                        ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                                    echo "<tr>";
                                    echo "<td class='px-6 py-4'>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td class='px-6 py-4'>Payroll Processing</td>";
                                    echo "<td class='px-6 py-4'><span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusClass}'>" . ucfirst($row['status']) . "</span></td>";
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
</body>
</html>
