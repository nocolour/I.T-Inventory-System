<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if the logged-in user is an admin
if ($_SESSION['access_permission'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Search
$search = $_GET['search'] ?? '';

// Fetch users with pagination
$query = "SELECT * FROM users WHERE 1=1";
if (!empty($search)) {
    $query .= " AND (username LIKE :search OR email LIKE :search OR staff_id LIKE :search OR access_permission LIKE :search)";
}
$query .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total users for pagination
$total_items_query = "SELECT COUNT(*) FROM users WHERE 1=1";
if (!empty($search)) {
    $total_items_query .= " AND (username LIKE :search OR email LIKE :search OR staff_id LIKE :search OR access_permission LIKE :search)";
}
$total_items_stmt = $pdo->prepare($total_items_query);
if (!empty($search)) {
    $total_items_stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$total_items_stmt->execute();
$total_items = $total_items_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Handle Add, Edit, Reset Password, and Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new user
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $staff_id = $_POST['staff_id'];
        $contact_number = $_POST['contact_number'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $access_permission = $_POST['access_permission'];

        // Check for duplicates
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR staff_id = ?");
        $stmt->execute([$username, $email, $staff_id]);
        if ($stmt->rowCount() > 0) {
            $error = "Username, email, or staff ID already exists!";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, staff_id, contact_number, access_permission)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $email, $staff_id, $contact_number, $access_permission]);
            $_SESSION['success'] = "User '$username' added successfully!";
            // Log the action
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Created new user: $username");
            header('Location: manage_users.php');
            exit();
        }
    }

    // Edit existing user
    if (isset($_POST['edit_user'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $staff_id = $_POST['staff_id'];
        $contact_number = $_POST['contact_number'];
        $access_permission = $_POST['access_permission'];

        // Check for duplicates on update
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ? OR staff_id = ?) AND id != ?");
        $stmt->execute([$username, $email, $staff_id, $id]);
        if ($stmt->rowCount() > 0) {
            $error = "Username, email, or staff ID already exists!";
        } else {
            // Update user details
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, staff_id = ?, contact_number = ?, access_permission = ? WHERE id = ?");
            $stmt->execute([$username, $email, $staff_id, $contact_number, $access_permission, $id]);
            $_SESSION['success'] = "User '$username' updated successfully!";
            // Log the action
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Edited user: $username");
            header('Location: manage_users.php');
            exit();
        }
    }

    // Reset user password
    if (isset($_POST['reset_password'])) {
        $user_id = $_POST['id'];
        $new_password = password_hash('default123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $user_id]);
        // Log the action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Reset password user ID: $user_id");
        $_SESSION['success'] = "Password for user ID '$user_id' reset successfully!";
        header('Location: manage_users.php');
        exit();
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        //$stmt->execute([$_POST['id']]);
        $stmt->execute([$user_id]);
        $_SESSION['success'] = "User with ID '$user_id' deleted successfully!";
        // Log the action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Deleted user ID: $user_id");
        header('Location: manage_users.php');
        exit();
    }
}

// Fetch data for the user being edited (if applicable)
$edit_user = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users | I.T. Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
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
        .actions {
            margin-bottom: 20px;
        }
        .actions button, .actions input {
            padding: 8px 12px;
            margin-right: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover, .actions input:hover {
            background-color: #0056b3;
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
        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
 
</head>
<body>
    <h1>Manage Users</h1>
    <div class="actions">
        <a href="dashboard.php"><button>Back to Dashboard</button></a>
        <a href="print_users.php" target="_blank"><button>Print Users</button></a>
        <a href="logout.php"><button>Logout</button></a>
    </div>
    <hr>

    <br>
    
    <!-- Search -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by username, email, or staff ID..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <a href="manage_users.php"><button>Reset</button></a>
    </form>
    
    <!-- Add or Edit User -->
    <?php if ($edit_user): ?>
        <h2>Edit User</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_user['id']) ?>">
            <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required>
            <input type="text" name="staff_id" value="<?= htmlspecialchars($edit_user['staff_id']) ?>" required>
            <input type="text" name="contact_number" value="<?= htmlspecialchars($edit_user['contact_number']) ?>">
            <select name="access_permission" required>
                <option value="view" <?= $edit_user['access_permission'] === 'view' ? 'selected' : '' ?>>View</option>
                <option value="add" <?= $edit_user['access_permission'] === 'add' ? 'selected' : '' ?>>Add</option>
                <option value="edit" <?= $edit_user['access_permission'] === 'edit' ? 'selected' : '' ?>>Edit</option>
                <option value="admin" <?= $edit_user['access_permission'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit" name="edit_user">Update User</button>
        </form>
    <?php else: ?>
        <h2>Add New User</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <input type="text" name="contact_number" placeholder="Contact Number">
            <input type="password" name="password" placeholder="Password" required>
            <select name="access_permission" required>
                <option value="view">View</option>
                <option value="add">Add</option>
                <option value="edit">Edit</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
    <?php endif; ?>

    <hr>

    <!-- Display Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success" style="color: green; font-weight: bold;">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); // Clear the success message ?>
    <?php endif; ?>

    <!-- Display Users -->
    <h2>Users List</h2>

     <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Staff ID</th>
                <th>Contact Number</th>
                <th>Access Permission</th>
                <th>Actions</th>
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
                    <td>
                        <a href="?edit_id=<?= $user['id'] ?>"><button>Edit</button></a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" name="reset_password">Reset Password</button>
                        </form>
                        <?php if ($user['access_permission'] !== 'admin'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete_user">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($users) === 0): ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br><br>               
    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
        <?php endif; ?>
    </div>
    <br>
</body>
</html>
