<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user details
$username = $_SESSION['username'];
$access_permission = $_SESSION['access_permission'];

// Quick Stats (Fetch counts)
$total_devices_query = "SELECT COUNT(*) AS total FROM (
    SELECT id FROM computers UNION ALL
    SELECT id FROM printers UNION ALL
    SELECT id FROM tablets UNION ALL
    SELECT id FROM phones UNION ALL
    SELECT id FROM servers UNION ALL
    SELECT id FROM network_equipment UNION ALL
    SELECT id FROM accessories
) AS all_devices";
$total_devices_stmt = $pdo->query($total_devices_query);
$total_devices = $total_devices_stmt->fetchColumn();

$total_assigned_query = "SELECT COUNT(*) AS total FROM (
    SELECT id FROM computers WHERE status = 'Assigned' UNION ALL
    SELECT id FROM printers WHERE status = 'Assigned' UNION ALL
    SELECT id FROM tablets WHERE status = 'Assigned' UNION ALL
    SELECT id FROM phones WHERE status = 'Assigned' UNION ALL
    SELECT id FROM servers WHERE status = 'Assigned' UNION ALL
    SELECT id FROM network_equipment WHERE status = 'Assigned' UNION ALL
    SELECT id FROM accessories WHERE status = 'Assigned'
) AS assigned_devices";
$total_assigned_stmt = $pdo->query($total_assigned_query);
$total_assigned = $total_assigned_stmt->fetchColumn();

$total_available_query = "SELECT COUNT(*) AS total FROM (
    SELECT id FROM computers WHERE status = 'Available' UNION ALL
    SELECT id FROM printers WHERE status = 'Available' UNION ALL
    SELECT id FROM tablets WHERE status = 'Available' UNION ALL
    SELECT id FROM phones WHERE status = 'Available' UNION ALL
    SELECT id FROM servers WHERE status = 'Available' UNION ALL
    SELECT id FROM network_equipment WHERE status = 'Available' UNION ALL
    SELECT id FROM accessories WHERE status = 'Available'
) AS available_devices";
$total_available_stmt = $pdo->query($total_available_query);
$total_available = $total_available_stmt->fetchColumn();

$total_users_query = "SELECT COUNT(*) FROM users";
$total_users_stmt = $pdo->query($total_users_query);
$total_users = $total_users_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | I.T. Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Responsive Dashboard Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2, h3, p {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1 1 200px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background: #f9f9f9;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .stat-box h3 {
            font-size: 1.2rem;
            color: #333;
        }
        .stat-box p {
            font-size: 1.5rem;
            color: #007BFF;
            font-weight: bold;
        }
        .dashboard-section {
            margin-bottom: 20px;
        }
        @media screen and (max-width: 768px) {
            .stats-container {
                flex-direction: column;
            }
            .stat-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
    <a href="logout.php">Logout</a> |
    <a href="profile.php">My Profile</a>
    <hr>

    <!-- Quick Stats Section -->
    <h2>Quick Stats</h2>
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Devices</h3>
            <p><?= $total_devices ?></p>
        </div>
        <div class="stat-box">
            <h3>Assigned Devices</h3>
            <p><?= $total_assigned ?></p>
        </div>
        <div class="stat-box">
            <h3>Available Devices</h3>
            <p><?= $total_available ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
    </div>

    <!-- Admin Panel -->
    <?php if ($access_permission === 'admin'): ?>
        <div class="dashboard-section">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="admin_import_export.php">Import/Export Data</a></li>
                <li><a href="view_logs.php">Activitiy Log</a></li>                
            </ul>
        </div>
    <?php endif; ?>

    <!-- Inventory Management -->
    <div class="dashboard-section">
        <h2>Inventory Management</h2>
        <ul>
            <?php if (in_array($access_permission, ['view', 'add', 'edit', 'admin'])): ?>
                <li><a href="manage_computers.php">Manage Computers</a></li>
                <li><a href="manage_printers.php">Manage Printers</a></li>
                <li><a href="manage_tablets.php">Manage Tablets</a></li>
                <li><a href="manage_phones.php">Manage Phones</a></li>
                <li><a href="manage_servers.php">Manage Servers</a></li>
                <li><a href="manage_network_equipment.php">Manage Network Equipments</a></li>
                <li><a href="manage_accessories.php">Manage Accessories</a></li>
            <?php else: ?>
                <p>You don't have access to manage inventory.</p>
            <?php endif; ?>
        </ul>
    </div>

    <!-- User Role Information -->
    <div class="dashboard-section">
        <?php if ($access_permission === 'view'): ?>
            <p><strong>Note:</strong> You have view-only access to the inventory.</p>
        <?php elseif ($access_permission === 'add'): ?>
            <p><strong>Note:</strong> You can add new inventory items but cannot edit or delete them.</p>
        <?php elseif ($access_permission === 'edit'): ?>
            <p><strong>Note:</strong> You can edit and delete inventory items.</p>
        <?php elseif ($access_permission === 'admin'): ?>
            <p><strong>Note:</strong> You have full admin access, including user management and inventory control.</p>
        <?php endif; ?>
    </div>
</body>
</html>
