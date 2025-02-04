<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only admin can access this
if ($_SESSION['access_permission'] !== 'admin') {
    die('Access denied: Only admins can import and export data.');
}

// Inventory tables list
$inventory_tables = ['computers', 'printers', 'tablets', 'phones', 'servers', 'network_equipment', 'accessories'];

// Validate selected table
$table = isset($_POST['table']) && in_array($_POST['table'], $inventory_tables) ? $_POST['table'] : null;

// Function to log actions
function log_action($pdo, $user_id, $username, $action) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $username, $action]);
}

// Import functionality with enhanced logging
if (isset($_POST['import']) && $table) {
    $file = $_FILES['file']['tmp_name'];
    if ($file) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $success_entries = [];
        $duplicates = [];
        $invalid_rows = [];

        for ($i = 1; $i < count($rows); $i++) { // Skip header row
            $row = $rows[$i];

            // Extract fields
            $device_name = $row[0] ?? null;
            $brand = $row[1] ?? null;
            $model = $row[2] ?? null;
            $serial_number = $row[3] ?? null;
            $processor = $row[4] ?? null;
            $ram = $row[5] ?? null;
            $storage = $row[6] ?? null;
            $ip_address = $row[7] ?? null;
            $mac_address = $row[8] ?? null;
            $location = $row[9] ?? null;
            $existing_user = $row[10] ?? null;
            $status = $row[11] ?? null;
            $purchase_date = $row[12] ?? null;
            $warranty = $row[13] ?? null;
            $other_details = $row[14] ?? null;

            // Validate required fields
            if (empty($device_name) || empty($serial_number) || empty($mac_address)) {
                $invalid_rows[] = ['device_name' => $device_name, 'serial_number' => $serial_number, 'reason' => 'Missing required fields'];
                log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Import failed: Missing fields ($table) - $device_name");
                continue;
            }

            // Check for duplicates
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE serial_number = ? OR mac_address = ?");
            $stmt->execute([$serial_number, $mac_address]);
            if ($stmt->fetchColumn() > 0) {
                $duplicates[] = ['device_name' => $device_name, 'serial_number' => $serial_number, 'reason' => 'Duplicate entry'];
                log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Duplicate skipped ($table) - $device_name");
                continue;
            }

            // Insert valid data
            $stmt = $pdo->prepare("INSERT INTO $table (device_name, brand, model, serial_number, processor, ram, storage, ip_address, mac_address, location, existing_user, status, purchase_date, warranty, other_details) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$device_name, $brand, $model, $serial_number, $processor, $ram, $storage, $ip_address, $mac_address, $location, $existing_user, $status, $purchase_date, $warranty, $other_details]);

            $success_entries[] = ['device_name' => $device_name, 'serial_number' => $serial_number];
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Import success ($table) - $device_name");
        }

        $_SESSION['import_status'] = compact('success_entries', 'duplicates', 'invalid_rows');
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Import completed for $table");

        header('Location: admin_import_export.php');
        exit();
    }
}

// Export functionality with logs
if (isset($_POST['export']) && $table) {
    $stmt = $pdo->query("SELECT device_name, brand, model, serial_number, processor, ram, storage, ip_address, mac_address, location, existing_user, status, purchase_date, warranty, other_details FROM $table");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header row
    $sheet->fromArray(['Device Name', 'Brand', 'Model', 'Serial Number', 'Processor', 'RAM', 'Storage', 'IP Address', 'MAC Address', 'Location', 'Existing User', 'Status', 'Purchase Date', 'Warranty', 'Other Details'], NULL, 'A1');

    // Data rows
    $rowIndex = 2;
    foreach ($items as $item) {
        $sheet->fromArray(array_values($item), NULL, "A$rowIndex");
        $rowIndex++;
    }

    // Log export action
    log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Exported $table inventory");

    // Set headers and output the file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$table}_inventory.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import/Export Inventory</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Import/Export I.T. Inventory</h1>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>

    <!-- Display Import Summary -->
    <?php if (isset($_SESSION['import_status'])): ?>
        <div class="success">
            <p><strong>Import Summary</strong></p>
            <p>Imported: <?= count($_SESSION['import_status']['success_entries']) ?> entries</p>
            <p>Duplicates: <?= count($_SESSION['import_status']['duplicates']) ?> entries</p>
            <p>Invalid: <?= count($_SESSION['import_status']['invalid_rows']) ?> entries</p>
        </div>
        <?php unset($_SESSION['import_status']); ?>
    <?php endif; ?>

    <!-- Import Form -->
    <form method="POST" enctype="multipart/form-data">
        <h2>Import Data</h2>
        <label>Select Inventory Type:</label>
        <select name="table" required>
            <option value="">--Select--</option>
            <?php foreach ($inventory_tables as $inv_table): ?>
                <option value="<?= $inv_table ?>"><?= ucfirst(str_replace('_', ' ', $inv_table)) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="file" required>
        <button type="submit" name="import">Import</button>
    </form>

    <!-- Export Form -->
    <form method="POST">
        <h2>Export Data</h2>
        <label>Select Inventory Type:</label>
        <select name="table" required>
            <option value="">--Select--</option>
            <?php foreach ($inventory_tables as $inv_table): ?>
                <option value="<?= $inv_table ?>"><?= ucfirst(str_replace('_', ' ', $inv_table)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="export">Export</button>
    </form>
</body>
</html>
