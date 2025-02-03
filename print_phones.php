<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pagination Logic
$limit = 10; // Number of rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_stmt = $pdo->query("SELECT COUNT(*) FROM phones");
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch paginated phones from the database
$stmt = $pdo->prepare("SELECT * FROM phones ORDER BY device_name ASC LIMIT :start, :limit");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Phones Inventory</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
        }
        h1 {
            margin-bottom: 15px;
        }
        .actions {
            margin-bottom: 15px;
        }
        .actions input, .actions select, .actions button {
            margin-right: 10px;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        table th {
            background-color: #f8f9fa;
            cursor: pointer;
            font-size: 12px;
        }
        .hidden {
            display: none;
        }
        .print-btn, .reset-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
        }
        .print-btn:hover, .reset-btn:hover {
            background-color: #0056b3;
        }
        .pagination {
            margin: 15px 0;
            text-align: center;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-decoration: none;
            color: #007BFF;
        }
        .pagination a:hover {
            background-color: #f8f9fa;
        }
        .pagination a.active {
            font-weight: bold;
            background-color: #007BFF;
            color: white;
        }
    </style>
    <script>
        let sortOrder = {};

        // Column Filter: Show/Hide Columns
        function toggleColumn(columnIndex) {
            const table = document.getElementById('phonesTable');
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('th, td');
                if (cells[columnIndex]) {
                    cells[columnIndex].classList.toggle('hidden');
                }
            });
        }

        // Reset Columns
        function resetColumns() {
            const table = document.getElementById('phonesTable');
            table.querySelectorAll('.hidden').forEach(cell => cell.classList.remove('hidden'));
        }

        // Search
        function searchTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#phonesTable tbody tr');
            rows.forEach(row => {
                const match = Array.from(row.querySelectorAll('td')).some(cell => cell.textContent.toLowerCase().includes(input));
                row.style.display = match ? '' : 'none';
            });
        }

        // Reset Search
        function resetSearch() {
            document.getElementById('searchInput').value = '';
            const rows = document.querySelectorAll('#phonesTable tbody tr');
            rows.forEach(row => row.style.display = '');
        }

        // Sorting
        function sortTable(columnIndex) {
            const table = document.getElementById('phonesTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            sortOrder[columnIndex] = !sortOrder[columnIndex];
            rows.sort((rowA, rowB) => {
                const cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim().toLowerCase();
                const cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim().toLowerCase();
                return cellA < cellB ? (sortOrder[columnIndex] ? -1 : 1) : (cellA > cellB ? (sortOrder[columnIndex] ? 1 : -1) : 0);
            });
            rows.forEach(row => table.querySelector('tbody').appendChild(row));
        }

        // Directly Print the Page
        function printTable() {
            const actions = document.querySelector('.actions');
            const pagination = document.querySelector('.pagination');
            
            // Temporarily hide unnecessary elements for printing
            actions.style.display = 'none';
            pagination.style.display = 'none';
            
            window.print(); // Directly trigger the print dialog
            
            // Restore the hidden elements after printing
            actions.style.display = '';
            pagination.style.display = '';
        }
    </script>
</head>
<body>
    <h1>Phones Inventory</h1>

    <!-- Actions: Search, Column Filter, and Reset -->
    <div class="actions">
        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
        <button class="reset-btn" onclick="resetSearch()">Reset Search</button>

        <!-- Column Filters -->
        <label>Show/Hide Columns:</label>
        <select onchange="toggleColumn(this.value)">
            <option value="">-- Select Column --</option>
            <option value="0">Device Name</option>
            <option value="1">Brand</option>
            <option value="2">Model</option>
            <option value="3">Serial Number</option>
            <option value="4">Processor</option>
            <option value="5">RAM</option>
            <option value="6">Storage</option>
            <option value="7">IP Address</option>
            <option value="8">MAC Address</option>
            <option value="9">Location</option>
            <option value="10">Existing User</option>
            <option value="11">Status</option>
            <option value="12">Purchase Date</option>
            <option value="13">Warranty</option>
            <option value="14">Other Details</option>
        </select>
        <button class="reset-btn" onclick="resetColumns()">Reset Columns</button>

        <!-- Print Button -->
        <button class="print-btn" onclick="printTable()">Print</button>
    </div>

    <!-- phones Table -->
    <table id="phonesTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Device Name</th>
                <th onclick="sortTable(1)">Brand</th>
                <th onclick="sortTable(2)">Model</th>
                <th onclick="sortTable(3)">Serial Number</th>
                <th onclick="sortTable(4)">Processor</th>
                <th onclick="sortTable(5)">RAM</th>
                <th onclick="sortTable(6)">Storage</th>
                <th onclick="sortTable(7)">IP Address</th>
                <th onclick="sortTable(8)">MAC Address</th>
                <th onclick="sortTable(9)">Location</th>
                <th onclick="sortTable(10)">Existing User</th>
                <th onclick="sortTable(11)">Status</th>
                <th onclick="sortTable(12)">Purchase Date</th>
                <th onclick="sortTable(13)">Warranty</th>
                <th onclick="sortTable(14)">Other Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($phones as $phone): ?>
                <tr>
                    <td><?= htmlspecialchars($phone['device_name']) ?></td>
                    <td><?= htmlspecialchars($phone['brand']) ?></td>
                    <td><?= htmlspecialchars($phone['model']) ?></td>
                    <td><?= htmlspecialchars($phone['serial_number']) ?></td>
                    <td><?= htmlspecialchars($phone['processor']) ?></td>
                    <td><?= htmlspecialchars($phone['ram']) ?></td>
                    <td><?= htmlspecialchars($phone['storage']) ?></td>
                    <td><?= htmlspecialchars($phone['ip_address']) ?></td>
                    <td><?= htmlspecialchars($phone['mac_address']) ?></td>
                    <td><?= htmlspecialchars($phone['location']) ?></td>
                    <td><?= htmlspecialchars($phone['existing_user']) ?></td>
                    <td><?= htmlspecialchars($phone['status']) ?></td>
                    <td><?= htmlspecialchars($phone['purchase_date']) ?></td>
                    <td><?= htmlspecialchars($phone['warranty']) ?></td>
                    <td><?= htmlspecialchars($phone['other_details']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</body>
</html>
