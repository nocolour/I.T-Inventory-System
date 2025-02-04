<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Sorting functionality
$sort_columns = ['id', 'user_id', 'username', 'action', 'timestamp'];
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'timestamp';
$sort_direction = isset($_GET['dir']) && in_array($_GET['dir'], ['asc', 'desc']) ? $_GET['dir'] : 'desc';

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Fetch logs with search and sorting
$sql = "SELECT * FROM logs WHERE 
        username LIKE :search OR 
        action LIKE :search OR 
        timestamp LIKE :search 
        ORDER BY $sort_column $sort_direction 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total logs for pagination
$total_logs_stmt = $pdo->prepare("SELECT COUNT(*) FROM logs WHERE 
                                  username LIKE :search OR 
                                  action LIKE :search OR 
                                  timestamp LIKE :search");
$total_logs_stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$total_logs_stmt->execute();
$total_logs = $total_logs_stmt->fetchColumn();
$total_pages = ceil($total_logs / $items_per_page);

// Pagination controls
$max_pages_to_display = 20;
$start_page = max(1, $page - floor($max_pages_to_display / 2));
$end_page = min($total_pages, $start_page + $max_pages_to_display - 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activity Log | I.T. Inventory</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
        }
        .pagination {
            margin-top: 10px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 3px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007BFF;
            display: inline-block;
        }
        .pagination a.active {
            background-color: #007BFF;
            color: white;
        }
        .search-container {
            margin-bottom: 15px;
        }
        .search-container input, .search-container button {
            padding: 8px;
            font-size: 16px;
        }
        .print-button {
            margin-left: 10px;
            padding: 8px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #218838;
        }
    </style>
    <script>
        function printLogs() {
            let originalContent = document.body.innerHTML;
            let printContent = document.getElementById('log-table').outerHTML;
            document.body.innerHTML = "<h1>Activity Log</h1>" + printContent;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>
</head>
<body>
    <h1>Activity Log</h1>
    <button class="print-button" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
    <button class="print-button" onclick="printLogs()">Print Logs</button>
    <br><br>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search logs..." required>
            <button type="submit">Search</button>
            <a href="view_logs.php"><button type="button">Reset</button></a>
        </form>
    </div>

    <!-- Logs Table -->
    <table id="log-table">
        <thead>
            <tr>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=id&dir=<?= $sort_direction === 'asc' ? 'desc' : 'asc' ?>">ID</a></th>
                <th>User ID</th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=username&dir=<?= $sort_direction === 'asc' ? 'desc' : 'asc' ?>">Username</a></th>
                <th>Action</th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=timestamp&dir=<?= $sort_direction === 'asc' ? 'desc' : 'asc' ?>">Timestamp</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['id']) ?></td>
                    <td><?= htmlspecialchars($log['user_id']) ?></td>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5">No logs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><br>
    
    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?= htmlspecialchars($search) ?>&sort=<?= htmlspecialchars($sort_column) ?>&dir=<?= htmlspecialchars($sort_direction) ?>">First</a>
            <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>&sort=<?= htmlspecialchars($sort_column) ?>&dir=<?= htmlspecialchars($sort_direction) ?>">Previous</a>
        <?php endif; ?>
        
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&sort=<?= htmlspecialchars($sort_column) ?>&dir=<?= htmlspecialchars($sort_direction) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>&sort=<?= htmlspecialchars($sort_column) ?>&dir=<?= htmlspecialchars($sort_direction) ?>">Next</a>
            <a href="?page=<?= $total_pages ?>&search=<?= htmlspecialchars($search) ?>&sort=<?= htmlspecialchars($sort_column) ?>&dir=<?= htmlspecialchars($sort_direction) ?>">Last</a>
        <?php endif; ?>
    </div>

    <br>
</body>
</html>
