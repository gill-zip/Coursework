<?php
session_start();
include_once("connection.php");
array_map("htmlspecialchars", $_POST);
print_r($_POST);
$time = ($_POST["Seconds"]) + 60*($_POST["Minutes"]) + 3600*($_POST["Hours"]);
$stmt = $conn->prepare("INSERT INTO rowertrainingdonetbl (UserID,Date,TimeCompleted,Distance,Time,Description)
VALUES (:UserID,:date,:timeInput,:distance,:time,:Description)");

$stmt->bindParam(':UserID', $_SESSION['UserID']); 
$stmt->bindParam(':date', $_POST["date"]);
$stmt->bindParam(':timeInput', $_POST["timeInput"]);
$stmt->bindParam(':distance', $_POST["distance"]);
$stmt->bindParam(':time', $time);
$stmt->bindParam(':Description',$_POST["description"]);
$stmt->execute();

$conn=null;
header("Location: sessiontest.php");
exit();
?>
