<?php
include_once("connection.php");


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set default week date (if none is selected)
$selectedDate = isset($_GET['weekDate']) ? $_GET['weekDate'] : date('Y-m-d');

// Calculate start and end dates of the week based on the selected date
$startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($selectedDate)));
$endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($selectedDate)));

// Query to fetch records within the selected week using prepared statements
$sql = "SELECT UserID, Date, TimeCompleted, Distance, Time, Description 
        FROM rowertrainingdonetbl 
        WHERE Date >= :startOfWeek AND Date <= :endOfWeek
        ORDER BY Date, TimeCompleted";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':startOfWeek', $startOfWeek);
$stmt->bindParam(':endOfWeek', $endOfWeek);
$stmt->execute();

// Fetch the data
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare($sql);
$stmt->bindParam(':startOfWeek', $startOfWeek);
$stmt->bindParam(':endOfWeek', $endOfWeek);
$stmt->execute();

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging output
if ($stmt->rowCount() > 0) {
    echo "Data fetched successfully!";
} else {
    echo "No data found for the specified date range.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Records</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

<h2>Records for Week (<?= htmlspecialchars($startOfWeek) ?> to <?= htmlspecialchars($endOfWeek) ?>)</h2>
<table>
    <thead>
        <tr>
            <th>UserID</th>
            <th>Date</th>
            <th>Time Completed</th>
            <th>Distance</th>
            <th>Time</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($records)): ?>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['UserID']) ?></td>
                    <td><?= htmlspecialchars($record['Date']) ?></td>
                    <td><?= htmlspecialchars($record['TimeCompleted']) ?></td>
                    <td><?= htmlspecialchars($record['Distance']) ?></td>
                    <td><?= htmlspecialchars($record['Time']) ?></td>
                    <td><?= htmlspecialchars($record['Description'] ?? 'N/A') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No records found for this week.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
