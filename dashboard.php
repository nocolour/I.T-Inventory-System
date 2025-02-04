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

// Query to get device stats for each category
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
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 18px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-box h3 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 1.4rem;
            color: #007BFF;
            font-weight: bold;
        }
        ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            padding: 0;
        }
        ul li {
            list-style: none;
        }
        @media screen and (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
    
    <!-- Navigation Buttons -->
    <div class="btn-container">
        <a class="btn" href="logout.php">Logout</a>
        <a class="btn" href="profile.php">My Profile</a>
    </div>

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
            <li><a class="btn" href="manage_users.php">Manage Users</a></li>
            <li><a class="btn" href="admin_import_export.php">Import/Export Data</a></li>
            <li><a class="btn" href="view_logs.php">Activity Log</a></li>
        </ul>
    <?php endif; ?>

    <!-- Inventory Management -->
    <h2>Inventory Management</h2>
    <ul>
        <?php if (in_array($access_permission, ['view', 'add', 'edit', 'admin'])): ?>
            <li><a class="btn" href="manage_computers.php">Manage Computers</a></li>
            <li><a class="btn" href="manage_printers.php">Manage Printers</a></li>
            <li><a class="btn" href="manage_tablets.php">Manage Tablets</a></li>
            <li><a class="btn" href="manage_phones.php">Manage Phones</a></li>
            <li><a class="btn" href="manage_servers.php">Manage Servers</a></li>
            <li><a class="btn" href="manage_network_equipment.php">Manage Network Equipment</a></li>
            <li><a class="btn" href="manage_accessories.php">Manage Accessories</a></li>
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
