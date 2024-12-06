<?php
// Database connection using PDO
include_once("connection.php");
session_start();

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

// Fetch training program names for the user
$userId = $_SESSION['UserID'] ?? null;

if ($userId) {
    $query = "SELECT Name FROM trainingprogramtbl WHERE UserId = :userId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $names = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $names = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Weekly Schedule</title>
    <link rel="stylesheet" href="Unistyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }
  th, td {
    padding: 20px;
    text-align: center;
    vertical-align: top;
    border: 1px solid #ddd;
  }
  th {
    background-color: #f4f4f4;
    font-size: 1.2em;
  }
  .session-button {
    display: block;
    width: 100%;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    text-align: left;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: #333;
  }
  .session-button:hover {
    background-color: #f0f0f0;
  }
  .session-content .title {
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 1em;
  }
  .session-content .time, .session-content .distance, .session-content .location {
    font-size: 0.9em;
    color: #666;
  }
  .no-sessions {
    font-size: 0.9em;
    color: #999;
    text-align: center;
  }
</style>
</head>
<body>
    <nav class="ColourCoral navbar navbar-expand-sm navbar-dark justify-content-center">
        <div class="container-fluid justify-content-center">
            <a class="navbar-brand" href="#">
                <img src="black-circle-icon-download-transparent-17.png" alt="Avatar Logo" style="width:40px;" class="rounded-pill">
            </a>
        </div>
    </nav>

    <div class="row">
        <div class="col-sm-2 text-center" style="background-color: #fbeef6; height: 100vh;">
            <button class="btn CoralButton mt-3" data-bs-toggle="modal" data-bs-target="#myModal">Record Session</button>
            <button class="btn CoralButton mt-3" data-bs-toggle="modal" data-bs-target="#myModal2">New Program</button>
            <div class="dropdown mt-4">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Select Name</button>
                <ul class="dropdown-menu">
                    <?php if (count($names) > 0): ?>
                        <?php foreach ($names as $name): ?>
                            <li><a class="dropdown-item" href="#"><?= htmlspecialchars($name['Name']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a class="dropdown-item disabled">No programs available</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-sm-10">
            <div class="row align-items-center" style="height: 100px;">
                <div class="col-sm-3 text-center">
                    <a href="?weekDate=<?= htmlspecialchars($previousWeekDate) ?>" class="btn CoralButton">Previous Week</a>
                </div>
                <div class="col-sm-6 text-center">
                    <h2>Weekly Schedule (<?= htmlspecialchars($startOfWeek) ?> to <?= htmlspecialchars($endOfWeek) ?>)</h2>
                </div>
                <div class="col-sm-3 text-center">
                    <a href="?weekDate=<?= htmlspecialchars($nextWeekDate) ?>" class="btn CoralButton">Next Week</a>
                </div>
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
                            <a href="#" class="session-button">
                                <div class="session-content">
                                <div class="title"><?= htmlspecialchars($session['Description'] ?? 'No Title') ?></div>
                                <div class="time"><?= htmlspecialchars($session['TimeCompleted']) ?></div>
                                <div class="distance"><?= htmlspecialchars($session['Distance']) ?> Meters - <?= htmlspecialchars($session['Time']) ?> Mins</div>
                                <div class="location"><?= htmlspecialchars($session['Location'] ?? 'No Location') ?></div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-sessions">No sessions</div>
                        <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    </tr>
                </tbody>
                </table>
        </div>
    </div>
</body>
</html>
