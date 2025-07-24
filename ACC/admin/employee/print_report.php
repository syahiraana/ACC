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
    <title>Employee Report - <?php echo getMonthName($month) . ' ' . $year; ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            background: white;
            color: black;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            background: white;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
        }
        th { 
            background-color: #f5f5f5;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
        }
        .footer { 
            text-align: center; 
            margin-top: 20px; 
            font-style: italic;
        }
        .print-btn {
            background: #1b5e20;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .attendance-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
            margin: 0 2px;
        }
        .present { background: #e8f5e9; color: #2e7d32; }
        .sick { background: #fff3e0; color: #f57c00; }
        .permit { background: #e3f2fd; color: #1976d2; }
        .absent { background: #ffebee; color: #c62828; }
        @media print {
            .no-print { 
                display: none; 
            }
            body { 
                margin: 0; 
            }
            table { 
                page-break-inside: auto;
            }
            tr { 
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Employee Attendance Report</h2>
        <p>Period: <?php echo getMonthName($month) . ' ' . $year; ?></p>
    </div>
    
    <button onclick="window.print()" class="print-btn no-print">
        Print Report
    </button>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Attendance</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td>
                    <?php echo $employee['name']; ?><br>
                    <small style="color: #666;">NIP: <?php echo $employee['nip']; ?></small>
                </td>
                <td><?php echo $employee['position']; ?></td>
                <td>
                    <span class="attendance-badge present">P: <?php echo $employee['present_count']; ?></span>
                    <span class="attendance-badge sick">S: <?php echo $employee['sick_count']; ?></span>
                    <span class="attendance-badge permit">I: <?php echo $employee['permit_count']; ?></span>
                    <span class="attendance-badge absent">A: <?php echo $employee['absent_count']; ?></span>
                </td>
                <td><?php echo formatCurrency($employee['salary']); ?></td>
                <td style="color: #2e7d32;">+<?php echo formatCurrency($employee['allowances']); ?></td>
                <td style="color: #c62828;">-<?php echo formatCurrency($employee['deductions']); ?></td>
                <td><strong><?php echo formatCurrency($employee['net_salary']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>
