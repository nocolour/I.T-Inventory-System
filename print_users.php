<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .actions {
            margin-bottom: 15px;
        }
        .actions input, .actions button {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            cursor: pointer;
        }
        .actions button {
            background-color: #007BFF;
            color: white;
        }
        .actions button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .actions {
                display: none; /* Hide search bar and buttons when printing */
            }
        }
    </style>
    <script>
        // Search Functionality
        function searchTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('usersTable');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const match = Array.from(cells).some(cell => 
                    cell.textContent.toLowerCase().includes(input)
                );
                row.style.display = match ? '' : 'none';
            });
        }

        // Print the Visible Table
        function printFilteredTable() {
            window.print();
        }

        // Close the Page
        function closePage() {
            window.close();
        }
    </script>
</head>
<body>
    <h1>Users List</h1>

    <!-- Actions -->
    <div class="actions">
        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="Search by any field..." onkeyup="searchTable()">
        <!-- Print Button -->
        <button onclick="printFilteredTable()">Print</button>
        <!-- Close Button -->
        <button onclick="closePage()">Close Page</button>
    </div>

    <!-- Users Table -->
    <table id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Staff ID</th>
                <th>Contact Number</th>
                <th>Access Permission</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['staff_id']) ?></td>
                    <td><?= htmlspecialchars($user['contact_number']) ?></td>
                    <td><?= htmlspecialchars($user['access_permission']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
