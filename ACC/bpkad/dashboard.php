<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

checkLogin();
if (!isBPKAD()) {
    header('Location: /index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPKAD Dashboard - Payroll System</title>
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
        <div class="w-64 bg-green-700 text-white">
            <div class="p-4 border-b border-green-600">
                <h2 class="text-xl font-semibold">BPKAD Section</h2>
            </div>
            <nav class="p-4">
                <a href="dashboard.php" class="block py-2.5 px-4 rounded bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">dashboard</i>
                    Dashboard
                </a>
                <a href="budget/" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">account_balance_wallet</i>
                    Budget Approval
                </a>
                <a href="../logout.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mt-8 text-red-300">
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
                        <span class="text-xl font-semibold">BPKAD Dashboard</span>
                    </div>
                    <div class="flex items-center">
                        <span class="material-icons mr-2">account_circle</span>
                        <span>BPKAD Officer</span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Pending Approvals Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-700">
                                    <i class="material-icons">pending_actions</i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Pending Approvals</p>
                                    <p class="text-lg font-semibold">
                                        <?php
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM budget_submissions WHERE status = 'pending'");
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Approved Budget Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-700">
                                    <i class="material-icons">check_circle</i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Approved Budget</p>
                                    <p class="text-lg font-semibold">
                                        <?php
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM budget_submissions WHERE status = 'approved'");
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Budget Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-700">
                                    <i class="material-icons">account_balance</i>
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

                    <!-- Recent Budget Submissions -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Recent Budget Submissions</h2>
                        <div class="bg-white rounded-lg shadow-md">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT * FROM budget_submissions 
                                        ORDER BY created_at DESC 
                                        LIMIT 10
                                    ");
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td class='px-6 py-4 whitespace-nowrap'>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                        echo "<td class='px-6 py-4'>" . $row['title'] . "</td>";
                                        echo "<td class='px-6 py-4'>" . formatCurrency($row['amount']) . "</td>";
                                        echo "<td class='px-6 py-4'><span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full " . 
                                            ($row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                            ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) . 
                                            "'>" . ucfirst($row['status']) . "</span></td>";
                                        echo "<td class='px-6 py-4'>";
                                        if ($row['status'] === 'pending') {
                                            echo "<a href='budget/approve.php?id=" . $row['id'] . "' class='text-green-600 hover:text-green-900 mr-3'>Approve</a>";
                                            echo "<a href='budget/reject.php?id=" . $row['id'] . "' class='text-red-600 hover:text-red-900'>Reject</a>";
                                        }
                                        echo "</td>";
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
