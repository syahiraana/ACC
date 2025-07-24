<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

checkLogin();
if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}

// Get filter parameters
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query conditions
$where = [];
$params = [];

if ($status) {
    if ($status === 'present') {
        $where[] = "present_count > 0";
    } elseif ($status === 'absent') {
        $where[] = "absent_count > 0";
    }
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get employees with their attendance
$query = "
    SELECT 
        e.*,
        COALESCE(p.basic_salary, 0) as salary,
        COALESCE(p.allowances, 0) as allowances,
        COALESCE(p.deductions, 0) as deductions,
        COALESCE(p.net_salary, 0) as net_salary
    FROM employees e
    LEFT JOIN payroll p ON e.id = p.employee_id 
        AND p.month = ? AND p.year = ?
    $whereClause
    ORDER BY e.name
";

$stmt = $pdo->prepare($query);
$stmt->execute(array_merge([$month, $year], $params));
$employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Report - Admin Dashboard</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
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
                <a href="./" class="flex items-center py-2.5 px-4 rounded bg-green-800 mb-2">
                    <i class="material-icons">groups</i>
                    <span class="ml-2 sidebar-text">Data Employee</span>
                </a>
                <a href="../payroll/" class="flex items-center py-2.5 px-4 rounded hover:bg-green-800 mb-2">
                    <i class="material-icons">payments</i>
                    <span class="ml-2 sidebar-text">Payroll Management</span>
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
                        <a href="index.php" class="text-gray-500 hover:text-gray-700 mr-2">
                            <i class="material-icons">arrow_back</i>
                        </a>
                        <span class="text-xl font-semibold">Employee Report</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="print_report.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>&status=<?php echo $status; ?>" 
                           target="_blank"
                           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            <i class="material-icons inline-block align-middle mr-1">print</i>
                            Print Report
                        </a>
                    </div>
                </div>
            </header>

            <!-- Report Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <form method="GET" class="flex items-center space-x-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                                <select name="month" class="form-input">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $month == $i ? 'selected' : ''; ?>>
                                            <?php echo getMonthName($i); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <select name="year" class="form-input">
                                    <?php for ($i = date('Y'); $i >= date('Y')-5; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $year == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="form-input">
                                    <option value="">All</option>
                                    <option value="present" <?php echo $status === 'present' ? 'selected' : ''; ?>>Present</option>
                                    <option value="absent" <?php echo $status === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    <i class="material-icons inline-block align-middle mr-1">filter_list</i>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Report Table -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($employees as $employee): ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $employee['name']; ?></div>
                                            <div class="text-sm text-gray-500">NIP: <?php echo $employee['nip']; ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo $employee['position']; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <span class="attendance-badge attendance-present">P: <?php echo $employee['present_count']; ?></span>
                                                <span class="attendance-badge attendance-sick">S: <?php echo $employee['sick_count']; ?></span>
                                                <span class="attendance-badge attendance-permit">I: <?php echo $employee['permit_count']; ?></span>
                                                <span class="attendance-badge attendance-absent">A: <?php echo $employee['absent_count']; ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo formatCurrency($employee['salary']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-green-600">
                                            +<?php echo formatCurrency($employee['allowances']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-600">
                                            -<?php echo formatCurrency($employee['deductions']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <?php echo formatCurrency($employee['net_salary']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../../assets/js/sidebar.js"></script>
</body>
</html>
