<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Check if an admin account exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE access_permission = 'admin'");
$stmt->execute();
$admin_count = $stmt->fetchColumn();

// Create Admin Account Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $staff_id = $_POST['staff_id'];
    $contact_number = $_POST['contact_number'];

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "Username or email already exists!";
    } else {
        // Create admin account
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, staff_id, contact_number, access_permission) 
                               VALUES (?, ?, ?, ?, ?, 'admin')");
        $stmt->execute([$username, $password, $email, $staff_id, $contact_number]);
        $success = "Admin account created successfully! Please log in.";
    }
}

// Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['access_permission'] = $user['access_permission'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}

// Sign-Up Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $staff_id = $_POST['staff_id'];
    $contact_number = $_POST['contact_number'];

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "Username or email already exists!";
    } else {
        // Insert new user with default "view" permission
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, staff_id, contact_number, access_permission) 
                               VALUES (?, ?, ?, ?, ?, 'view')");
        $stmt->execute([$username, $password, $email, $staff_id, $contact_number]);
        $success = "Account created successfully! Please log in.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | I.T. Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>I.T. Inventory System</h1>

    <!-- Show Create Admin Account Form if no admin exists -->
    <?php if ($admin_count == 0): ?>
        <h2>Create Admin Account</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <input type="text" name="contact_number" placeholder="Contact Number">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="create_admin">Create Admin Account</button>
        </form>
        <hr>
    <?php else: ?>
        <!-- Login Form -->
        <form method="POST">
            <h2>Login</h2>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <hr>

        <!-- Sign-Up Form -->
        <form method="POST">
            <h2>Sign-Up</h2>
            <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <input type="text" name="contact_number" placeholder="Contact Number">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signup">Sign Up</button>
        </form>
    <?php endif; ?>
</body>
</html>
