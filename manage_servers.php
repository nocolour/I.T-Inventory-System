<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check user permissions
$permission = $_SESSION['access_permission'];
if (!in_array($permission, ['view', 'add', 'edit', 'admin'])) {
    header('Location: dashboard.php');
    exit();
}

// Sorting, searching, and pagination
$search = $_GET['search'] ?? '';
$sort_column = $_GET['sort'] ?? 'id';
$sort_direction = $_GET['dir'] ?? 'ASC';
$valid_columns = ['id', 'device_name', 'brand', 'model', 'serial_number', 'processor', 'ram', 'storage', 'ip_address', 'mac_address', 'location', 'existing_user', 'status', 'purchase_date', 'warranty', 'other_details'];

// Ensure the sort column is valid to prevent SQL injection
if (!in_array($sort_column, $valid_columns)) {
    $sort_column = 'id';
}

// Toggle sort direction for next click
$next_sort_direction = ($sort_direction === 'ASC') ? 'DESC' : 'ASC';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Fetch servers data
$query = "SELECT * FROM servers WHERE 1=1";
if (!empty($search)) {
    $query .= " AND (
        device_name LIKE :search OR 
        brand LIKE :search OR 
        serial_number LIKE :search OR 
        model LIKE :search OR 
        processor LIKE :search OR 
        ram LIKE :search OR 
        storage LIKE :search OR 
        ip_address LIKE :search OR 
        mac_address LIKE :search OR 
        location LIKE :search OR 
        existing_user LIKE :search OR 
        status LIKE :search OR 
        purchase_date LIKE :search OR 
        warranty LIKE :search OR 
        other_details LIKE :search
    )";
}
$query .= " ORDER BY $sort_column $sort_direction LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$servers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total items for pagination
$total_items_query = "SELECT COUNT(*) FROM servers WHERE 1=1";
if (!empty($search)) {
    $total_items_query .= " AND (
        device_name LIKE :search OR 
        brand LIKE :search OR 
        serial_number LIKE :search OR 
        model LIKE :search OR 
        processor LIKE :search OR 
        ram LIKE :search OR 
        storage LIKE :search OR 
        ip_address LIKE :search OR 
        mac_address LIKE :search OR 
        location LIKE :search OR 
        existing_user LIKE :search OR 
        status LIKE :search OR 
        purchase_date LIKE :search OR 
        warranty LIKE :search OR 
        other_details LIKE :search
    )";
}
$total_items_stmt = $pdo->prepare($total_items_query);
if (!empty($search)) {
    $total_items_stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$total_items_stmt->execute();
$total_items = $total_items_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Handle actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add New Server
    if (isset($_POST['add_server']) && in_array($permission, ['add', 'edit', 'admin'])) {
        $stmt = $pdo->prepare("INSERT INTO servers (device_name, brand, model, serial_number, processor, ram, storage, ip_address, mac_address, location, existing_user, status, purchase_date, warranty, other_details)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['device_name'], $_POST['brand'], $_POST['model'], $_POST['serial_number'], $_POST['processor'],
            $_POST['ram'], $_POST['storage'], $_POST['ip_address'], $_POST['mac_address'], $_POST['location'],
            $_POST['existing_user'], $_POST['status'], $_POST['purchase_date'], $_POST['warranty'], $_POST['other_details']
        ]);
        $device_name = $_POST['device_name'];
        // Log the action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Added a new server: $device_name");
        $_SESSION['success'] = "Server '$device_name' added successfully!";
        header('Location: manage_servers.php');
        exit();
    }

    // Update existing server
    if (isset($_POST['update_server']) && in_array($permission, ['edit', 'admin'])) {
        $stmt = $pdo->prepare("UPDATE servers SET 
            device_name = ?, brand = ?, model = ?, serial_number = ?, processor = ?, ram = ?, storage = ?, 
            ip_address = ?, mac_address = ?, location = ?, existing_user = ?, status = ?, 
            purchase_date = ?, warranty = ?, other_details = ? WHERE id = ?");
        $stmt->execute([
            $_POST['device_name'], $_POST['brand'], $_POST['model'], $_POST['serial_number'], $_POST['processor'],
            $_POST['ram'], $_POST['storage'], $_POST['ip_address'], $_POST['mac_address'], $_POST['location'],
            $_POST['existing_user'], $_POST['status'], $_POST['purchase_date'], $_POST['warranty'], $_POST['other_details'], $_POST['id']
        ]);
        $device_name = $_POST['device_name'];
        // Log the action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Edited server: $device_name");
        $_SESSION['success'] = "Server '$device_name' updated successfully!";
        header('Location: manage_servers.php');
        exit();
    }

    // Delete server
    if (isset($_POST['delete_server']) && in_array($permission, ['edit', 'admin'])) {
        $stmt = $pdo->prepare("DELETE FROM servers WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $device_id = $_POST['id'];
        // Log the action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Deleted server with ID: $device_id");
        $_SESSION['success'] = "Server '$device_id' deleted successfully!";
        header('Location: manage_servers.php');
        exit();
    }
}

// Fetch data for the server being edited (if applicable)
$edit_server = null;
if (isset($_GET['edit_id']) && in_array($permission, ['edit', 'admin'])) {
    $stmt = $pdo->prepare("SELECT * FROM servers WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_server = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage servers | I.T. Inventory</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        table th {
            background-color: #f8f9fa;
            font-size: 14px;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 5px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007BFF;
        }
        .pagination a.active {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Manage servers</h1>
    <a href="dashboard.php"><button>Back to Dashboard</button></a> 
    <a href="logout.php"><button>Logout</button></a>
    <hr>

    <!-- Search -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by any column..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <button type="button" onclick="window.location.href='manage_servers.php'">Reset</button> <!-- ✅ Fix: Clears search -->
    </form>
	<!-- Print Table -->
    <h2>Print Servers Inventory</h2>
    <a href="print_servers.php" target="_blank"><button>Print Inventory</button></a>
    <hr>

    <!-- Add New Server -->
    <?php if (!$edit_server && in_array($permission, ['add', 'edit', 'admin'])): ?>
        <h2>Add New Server</h2>
        <form method="POST">
            <input type="text" name="device_name" placeholder="Device Name" required>
            <input type="text" name="brand" placeholder="Brand" required>
            <input type="text" name="model" placeholder="Model" required>
            <input type="text" name="serial_number" placeholder="Serial Number" required>
            <input type="text" name="processor" placeholder="Processor">
            <input type="text" name="ram" placeholder="RAM">
            <input type="text" name="storage" placeholder="Storage">
            <input type="text" name="ip_address" placeholder="IP Address">
            <input type="text" name="mac_address" placeholder="MAC Address">
            <input type="text" name="location" placeholder="Location">
            <input type="text" name="existing_user" placeholder="Existing User">
            <select name="status">
                <option value="Available">Available</option>
                <option value="Assigned">Assigned</option>
                <option value="Under Repair">Under Repair</option>
                <option value="Decommissioned">Decommissioned</option>
            </select>
            <input type="date" name="purchase_date" placeholder="Purchase Date">
            <input type="date" name="warranty" placeholder="Warranty Date">
            <textarea name="other_details" placeholder="Other Details"></textarea>
            <button type="submit" name="add_server">Add server</button>
        </form>
    <?php endif; ?>

    <!-- Edit server -->
    <?php if ($edit_server): ?>
        <h2>Edit server</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_server['id']) ?>">
            <input type="text" name="device_name" value="<?= htmlspecialchars($edit_server['device_name']) ?>" required>
            <input type="text" name="brand" value="<?= htmlspecialchars($edit_server['brand']) ?>" required>
            <input type="text" name="model" value="<?= htmlspecialchars($edit_server['model']) ?>" required>
            <input type="text" name="serial_number" value="<?= htmlspecialchars($edit_server['serial_number']) ?>" required>
            <input type="text" name="processor" value="<?= htmlspecialchars($edit_server['processor']) ?>">
            <input type="text" name="ram" value="<?= htmlspecialchars($edit_server['ram']) ?>">
            <input type="text" name="storage" value="<?= htmlspecialchars($edit_server['storage']) ?>">
            <input type="text" name="ip_address" value="<?= htmlspecialchars($edit_server['ip_address']) ?>">
            <input type="text" name="mac_address" value="<?= htmlspecialchars($edit_server['mac_address']) ?>">
            <input type="text" name="location" value="<?= htmlspecialchars($edit_server['location']) ?>">
            <input type="text" name="existing_user" value="<?= htmlspecialchars($edit_server['existing_user']) ?>">
            <select name="status">
                <option value="Available" <?= $edit_server['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                <option value="Assigned" <?= $edit_server['status'] === 'Assigned' ? 'selected' : '' ?>>Assigned</option>
                <option value="Under Repair" <?= $edit_server['status'] === 'Under Repair' ? 'selected' : '' ?>>Under Repair</option>
                <option value="Decommissioned" <?= $edit_server['status'] === 'Decommissioned' ? 'selected' : '' ?>>Decommissioned</option>
            </select>
            <input type="date" name="purchase_date" value="<?= htmlspecialchars($edit_server['purchase_date']) ?>">
            <input type="date" name="warranty" value="<?= htmlspecialchars($edit_server['warranty']) ?>">
            <textarea name="other_details"><?= htmlspecialchars($edit_server['other_details']) ?></textarea>
            <button type="submit" name="update_server">Update server</button>
            <button onclick="history.back()">Cancel</button>

        </form>
    <?php endif; ?>
    
    <!-- Display Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); // Clear the success message ?>
    <?php endif; ?>

    <!-- Display servers -->
    <h2>Servers Inventory</h2>
    <table>
        <thead>
            <tr>
                <?php foreach ($valid_columns as $column): ?>
                    <th>
                        <a href="?sort=<?= $column ?>&dir=<?= ($sort_column === $column && $sort_direction === 'ASC') ? 'DESC' : 'ASC' ?>">
                            <?= ucfirst(str_replace('_', ' ', $column)) ?>
                            <?= ($sort_column === $column) ? ($sort_direction === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servers as $server): ?>
                <tr>
                    <?php foreach ($valid_columns as $column): ?>
                        <td><?= htmlspecialchars($server[$column]) ?></td>
                    <?php endforeach; ?>
                    <td>
                        <?php if (in_array($permission, ['edit', 'admin'])): ?>
                            <a href="?edit_id=<?= $server['id'] ?>"><button>Edit</button></a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $server['id'] ?>">
                                <button type="submit" name="delete_server">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($servers) === 0): ?>
                <tr>
                    <td colspan="<?= count($valid_columns) + 1 ?>">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
     <br><br>           
<!-- Pagination Logic -->
<?php
    $max_pages_to_display = 20;
    $start_page = max(1, $page - floor($max_pages_to_display / 2));
    $end_page = min($total_pages, $start_page + $max_pages_to_display - 1);
    ?>

    <!-- Pagination UI -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_column) ?>&dir=<?= urlencode($sort_direction) ?>">First</a>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_column) ?>&dir=<?= urlencode($sort_direction) ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_column) ?>&dir=<?= urlencode($sort_direction) ?>" class="<?= ($i === $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_column) ?>&dir=<?= urlencode($sort_direction) ?>">Next</a>
            <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_column) ?>&dir=<?= urlencode($sort_direction) ?>">Last</a>
        <?php endif; ?>
    </div>
    <br>
</body>
</html>
