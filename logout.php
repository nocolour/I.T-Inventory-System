<?php
session_start();
require_once 'includes/db.php';

// Function to log activity
function log_activity($pdo, $user_id, $username, $action) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $username, $action]);
}

// Log the logout action before destroying session
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "User logged out");
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
