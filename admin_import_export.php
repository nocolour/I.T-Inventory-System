<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Start session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user has admin permission
if ($_SESSION['access_permission'] !== 'admin') {
    die('Access denied: Only admins can import and export data.');
}

// Variables for success and error tracking
$success_entries = []; // Store details of successfully imported rows
$duplicates = [];      // Store details of duplicate rows
$invalid_rows = [];    // Store details of invalid rows

// Reusable log function
function log_action($pdo, $user_id, $username, $action) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $username, $action]);
}

// Import functionality
if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];
    if ($file) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row (assumes the first row is a header)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Map row to variables (skip ID column if present)
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
            $purchase_date = $row[12] ?? null; // No conversion
            $warranty = $row[13] ?? null;      // No conversion
            $other_details = $row[14] ?? null;

            // Validate required fields
            if (empty($device_name) || empty($serial_number) || empty($mac_address)) {
                $invalid_rows[] = [
                    'device_name' => $device_name,
                    'serial_number' => $serial_number,
                    'mac_address' => $mac_address,
                    'reason' => 'Missing required fields'
                ];
                continue; // Skip this row
            }

            // Check for duplicates based on device_name, serial_number, and mac_address
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM computers WHERE device_name = ? OR serial_number = ? OR mac_address = ?");
            $stmt->execute([$device_name, $serial_number, $mac_address]);
            $duplicate_count = $stmt->fetchColumn();

            if ($duplicate_count > 0) {
                // Add to duplicates array
                $duplicates[] = [
                    'device_name' => $device_name,
                    'serial_number' => $serial_number,
                    'mac_address' => $mac_address,
                    'reason' => 'Duplicate entry'
                ];
                continue; // Skip this row
            }

            // Insert non-duplicated entry
            $stmt = $pdo->prepare("INSERT INTO computers (device_name, brand, model, serial_number, processor, ram, storage, ip_address, mac_address, location, existing_user, status, purchase_date, warranty, other_details)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $device_name, $brand, $model, $serial_number, $processor, $ram, $storage,
                $ip_address, $mac_address, $location, $existing_user, $status, $purchase_date,
                $warranty, $other_details
            ]);

            // Add to success entries
            $success_entries[] = [
                'device_name' => $device_name,
                'serial_number' => $serial_number,
                'mac_address' => $mac_address
            ];

            // Log individual imported computer
            log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Imported computer: $device_name");
        }

        // Log the bulk import action
        log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Completed importing multiple computers.");

        // Set success message with details of imported, duplicate, and invalid rows
        $_SESSION['success'] = [
            'message' => "Import completed successfully!",
            'imported' => $success_entries,
            'duplicates' => $duplicates,
            'invalid' => $invalid_rows
        ];

        header('Location: admin_import_export.php');
        exit();
    } else {
        $success = "Please upload a valid file!";
    }
}

// Export functionality
if (isset($_POST['export'])) {
    $stmt = $pdo->query("
        SELECT device_name, brand, model, serial_number, processor, ram, storage, ip_address, mac_address, location, existing_user, status, purchase_date, warranty, other_details 
        FROM computers
    ");
    $computers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add header row
    $sheet->fromArray([
        'Device Name', 'Brand', 'Model', 'Serial Number', 'Processor', 'RAM', 'Storage',
        'IP Address', 'MAC Address', 'Location', 'Existing User', 'Status', 'Purchase Date',
        'Warranty', 'Other Details'
    ], NULL, 'A1');

    // Add data rows
    $rowIndex = 2;
    foreach ($computers as $computer) {
        $sheet->fromArray(array_values($computer), NULL, "A$rowIndex");
        $rowIndex++;
    }

    // Log the export action
    log_action($pdo, $_SESSION['user_id'], $_SESSION['username'], "Exported data from computers table.");

    // Send file headers and output file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"computers_inventory.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import/Export | I.T. Inventory</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success, .error {
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    </style>
</head>
<body>
    <h1>Import/Export Computers Inventory</h1>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php">Back to Dashboard</a>

    <!-- Display Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
            <p><strong><?= $_SESSION['success']['message'] ?></strong></p>
            <p><strong>Imported Entries:</strong></p>
            <ul>
                <?php foreach ($_SESSION['success']['imported'] as $entry): ?>
                    <li><?= htmlspecialchars($entry['device_name']) ?> | Serial: <?= htmlspecialchars($entry['serial_number']) ?> | MAC: <?= htmlspecialchars($entry['mac_address']) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Duplicate Entries:</strong></p>
            <ul>
                <?php foreach ($_SESSION['success']['duplicates'] as $entry): ?>
                    <li><?= htmlspecialchars($entry['device_name']) ?> | Serial: <?= htmlspecialchars($entry['serial_number']) ?> | MAC: <?= htmlspecialchars($entry['mac_address']) ?> | Reason: <?= htmlspecialchars($entry['reason']) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Invalid Entries:</strong></p>
            <ul>
                <?php foreach ($_SESSION['success']['invalid'] as $entry): ?>
                    <li><?= htmlspecialchars($entry['device_name'] ?? 'Unknown') ?> | Serial: <?= htmlspecialchars($entry['serial_number'] ?? 'Unknown') ?> | MAC: <?= htmlspecialchars($entry['mac_address'] ?? 'Unknown') ?> | Reason: <?= htmlspecialchars($entry['reason']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Import Form -->
    <form method="POST" enctype="multipart/form-data">
        <h2>Import Data</h2>
        <input type="file" name="file" required>
        <button type="submit" name="import">Import</button>
    </form>

    <!-- Export Form -->
    <form method="POST">
        <h2>Export Data</h2>
        <button type="submit" name="export">Export</button>
    </form>
</body>
</html>
