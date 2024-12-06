<?php
include_once("connection.php");



// Set default week date (if none is selected)
$selectedDate = isset($_GET['weekDate']) ? $_GET['weekDate'] : date('Y-m-d');

// Calculate start and end dates of the week based on the selected date
$startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($selectedDate)));
$endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($selectedDate)));

// Calculate previous and next week dates
$previousWeekDate = date('Y-m-d', strtotime($selectedDate . ' -7 days'));
$nextWeekDate = date('Y-m-d', strtotime($selectedDate . ' +7 days'));

// Query to fetch sessions within the selected week
$sql = "SELECT UserID, Date, TimeCompleted, Distance, Time, Description 
        FROM rowertrainingdonetbl 
        WHERE Date >= :startOfWeek AND Date <= :endOfWeek
        ORDER BY Date, TimeCompleted";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':startOfWeek', $startOfWeek);
$stmt->bindParam(':endOfWeek', $endOfWeek);
$stmt->execute();

// Initialize an empty array to hold sessions grouped by day
$sessionsByDay = [
    'Monday' => [],
    'Tuesday' => [],
    'Wednesday' => [],
    'Thursday' => [],
    'Friday' => [],
    'Saturday' => [],
    'Sunday' => []
];

// Populate the sessionsByDay array
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dayOfWeek = date('l', strtotime($row['Date'])); // Get day name (e.g., 'Monday')
    $sessionsByDay[$dayOfWeek][] = $row; // Add session to the corresponding day
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; vertical-align: top; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; text-align: center; }
        .session-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            text-align: left;
        }
        .session-content .category { font-size: 0.9em; color: #555; }
        .session-content .time { font-size: 0.85em; color: #333; }
        .session-content .title { font-weight: bold; margin-top: 5px; font-size: 1.1em; }
        .session-content .location { font-size: 0.9em; color: #777; }
    </style>
</head>
<body>

<h2>Weekly Schedule (<?= htmlspecialchars($startOfWeek) ?> to <?= htmlspecialchars($endOfWeek) ?>)</h2>

<!-- Week Navigation Buttons -->
<div class="text-center mb-4">
    <a href="?weekDate=<?= $previousWeekDate ?>" class="btn btn-primary">Previous Week</a>
    <a href="?weekDate=<?= $nextWeekDate ?>" class="btn btn-primary">Next Week</a>
</div>

<table>
    <thead>
        <tr>
            <?php foreach (array_keys($sessionsByDay) as $day): ?>
                <th><?= $day ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php foreach ($sessionsByDay as $day => $sessions): ?>
                <td>
                    <?php if (!empty($sessions)): ?>
                        <?php foreach ($sessions as $session): ?>
                            <a href="#" class="btn btn-light session-button">
                                <div class="session-content">
                                    <div class="category">Co-Curricular</div>
                                    <div class="time">
                                        <?= htmlspecialchars($session['TimeCompleted']) ?> - <?= gmdate("H:i:s", htmlspecialchars($session['Time'])) ?>
                                    </div>
                                    <div class="title"><?= htmlspecialchars($session['Description'] ?? 'No Title') ?></div>
                                    <div class="location"><?= htmlspecialchars($session['Location'] ?? 'No Location') ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>No sessions</div>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>

</body>
</html>
