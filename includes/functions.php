<?php

function log_action($pdo, $user_id, $username, $action) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $username, $action]);
}


// Helper function to create sorting queries
function getSortingQuery($column, $direction, $defaultColumn = 'id', $defaultDirection = 'ASC') {
    $allowedColumns = [
        'id', 'device_name', 'brand', 'serial_number', 'processor', 'ram', 'storage',
        'ip_address', 'mac_address', 'location', 'existing_user', 'status', 
        'purchase_date', 'warranty', 'other_details'
    ];
    $allowedDirections = ['ASC', 'DESC'];

    $column = in_array($column, $allowedColumns) ? $column : $defaultColumn;
    $direction = in_array($direction, $allowedDirections) ? $direction : $defaultDirection;

    return "$column $direction";
}
?>
