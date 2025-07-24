<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isBPKAD()) {
    header('Location: /index.php');
    exit();
}

// Handle search/filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where_clause = "WHERE 1=1";
if ($status_filter) {
    $where_clause .= " AND status = '" . $status_filter . "'";
}
if ($search) {
    $where_clause .= " AND (title LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Approval - BPKAD Dashboard</title>
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
                <a href="../dashboard.php" class="block py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">dashboard</i>
                    Dashboard
                </a>
                <a href="./" class="block py-2.5 px-4 rounded bg-green-800 mb-2">
                    <i class="material-icons inline-block align-middle mr-2">account_balance_wallet</i>
                    Budget Approval
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
                        <span class="text-xl font-semibold">Budget Approval Submissions</span>
                    </div>
                    <div class="flex items-center">
                        <span class="material-icons mr-2">account_circle</span>
                        <span>BPKAD Officer</span>
                    </div>
                </div>
            </header>

            <!-- Budget Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form method="GET" class="flex gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="<?php echo $search; ?>" 
                                    placeholder="Search submissions..." 
                                    class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div class="w-48">
                                <select name="status" 
                                    class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Filter
                            </button>
                        </form>
                    </div>

                    <!-- Budget Submissions Table -->
                    <div class="bg-white rounded-lg shadow-md">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $pdo->query("
                                    SELECT * FROM budget_submissions 
                                    $where_clause
                                    ORDER BY created_at DESC
                                ");
                                while ($row = $stmt->fetch()) {
                                    echo "<tr>";
                                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['title'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . substr($row['description'], 0, 50) . "...</td>";
                                    echo "<td class='px-6 py-4'>" . formatCurrency($row['amount']) . "</td>";
                                    echo "<td class='px-6 py-4'><span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full " . 
                                        ($row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                        ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) . 
                                        "'>" . ucfirst($row['status']) . "</span></td>";
                                    echo "<td class='px-6 py-4'>";
                                    if ($row['status'] === 'pending') {
                                        echo "<div class='flex space-x-2'>";
                                        echo "<form method='POST' action='approve.php' class='inline'>";
                                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' class='text-green-600 hover:text-green-900'>Approve</button>";
                                        echo "</form>";
                                        echo "<form method='POST' action='reject.php' class='inline'>";
                                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' class='text-red-600 hover:text-red-900'>Reject</button>";
                                        echo "</form>";
                                        echo "</div>";
                                    }
                                    echo "</td>";
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
