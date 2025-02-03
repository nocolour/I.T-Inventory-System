<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user details
$username = $_SESSION['username'];
$access_permission = $_SESSION['access_permission'];

// Function to log activity
function log_activity($pdo, $user_id, $username, $action) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $username, $action]);
}

// Log dashboard access
// log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "Accessed Dashboard");

// Optimized query to get device stats for each category
$query = "SELECT category, 
                 COUNT(*) AS total, 
                 SUM(CASE WHEN status = 'Assigned' THEN 1 ELSE 0 END) AS total_assigned, 
                 SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) AS total_available
          FROM (
              SELECT 'Computers' AS category, status FROM computers
              UNION ALL
              SELECT 'Printers', status FROM printers
              UNION ALL
              SELECT 'Phones', status FROM phones
              UNION ALL
              SELECT 'Tablets', status FROM tablets
              UNION ALL
              SELECT 'Servers', status FROM servers
              UNION ALL
              SELECT 'Network Equipment', status FROM network_equipment
              UNION ALL
              SELECT 'Accessories', status FROM accessories
          ) AS inventory
          GROUP BY category";

$stmt = $pdo->query($query);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | I.T. Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        h1, h2 {
            text-align: center;
        }
        .btn {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1 1 250px;
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
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 10px;
        }
        @media screen and (max-width: 768px) {
            .stats-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
    <a class="btn" href="logout.php">Logout</a>
    <a class="btn" href="profile.php">My Profile</a>
    
    <hr>

    <!-- Quick Stats Section -->
    <h2>Quick Stats</h2>
    <div class="stats-container">
        <?php foreach ($stats as $stat): ?>
            <div class="stat-box">
                <h3><?= htmlspecialchars($stat['category']) ?></h3>
                <p>Total: <?= $stat['total'] ?></p>
                <p>Assigned: <?= $stat['total_assigned'] ?></p>
                <p>Available: <?= $stat['total_available'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin Panel -->
    <?php if ($access_permission === 'admin'): ?>
        <h2>Admin Panel</h2>
        <ul>
            <a class="btn" href="manage_users.php">Manage Users</a>
            <a class="btn" href="admin_import_export.php">Import/Export Data</a>
            <a class="btn" href="view_logs.php">Activity Log</a>
        </ul>
    <?php endif; ?>

    <!-- Inventory Management -->
    <h2>Inventory Management</h2>
    <ul>
        <?php if (in_array($access_permission, ['view', 'add', 'edit', 'admin'])): ?>
            <a class="btn" href="manage_computers.php">Manage Computers</a>
            <a class="btn" href="manage_printers.php">Manage Printers</a>
            <a class="btn" href="manage_tablets.php">Manage Tablets</a>
            <a class="btn" href="manage_phones.php">Manage Phones</a>
            <a class="btn" href="manage_servers.php">Manage Servers</a>
            <a class="btn" href="manage_network_equipment.php">Manage Network Equipments</a>
            <a class="btn" href="manage_accessories.php">Manage Accessories</a>
        <?php else: ?>
            <p>You don't have access to manage inventory.</p>
        <?php endif; ?>
    </ul>

    <!-- User Role Information -->
    <p><strong>Note:</strong> 
        <?php
        $roles = [
            'view' => 'You have view-only access to the inventory.',
            'add' => 'You can add new inventory items but cannot edit or delete them.',
            'edit' => 'You can edit and delete inventory items.',
            'admin' => 'You have full admin access, including user management and inventory control.'
        ];
        echo $roles[$access_permission] ?? 'Unknown role';
        ?>
    </p>
</div>
</body>
</html>
