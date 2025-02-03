<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, staff_id, contact_number FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ?, contact_number = ? WHERE id = ?");
            $stmt->execute([$email, $contact_number, $user_id]);
            $success = "Profile updated successfully!";
            // Log the action
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Updated their profile.");
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $hashed_password = $stmt->fetchColumn();

        if (!password_verify($current_password, $hashed_password)) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirm password do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hashed_password, $user_id]);
            $success = "Password changed successfully!";
            // Log the action
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Changed their password.");
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile | I.T. Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>User Profile</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <h2>Profile Information</h2>
        <label>Username (readonly):</label>
        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <label>Staff ID (readonly):</label>
        <input type="text" value="<?= htmlspecialchars($user['staff_id']) ?>" readonly>
        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>">
        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <hr>
    <form method="POST">
        <h2>Change Password</h2>
        <label>Current Password:</label>
        <input type="password" name="current_password" required>
        <label>New Password:</label>
        <input type="password" name="new_password" required>
        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>
</body>
</html>
