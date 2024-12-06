<?php
session_start();
include_once("connection.php");
array_map("htmlspecialchars", $_POST);
print_r($_POST);
$stmt = $conn->prepare("INSERT INTO trainingprogramtbl (UserId,Name)
VALUES (:UserID,:Name)");

$stmt->bindParam(':UserID', $_SESSION['UserID']); 
$stmt->bindParam(':Name', $_POST["Name"]);

$stmt->execute();

$conn=null;
header("Location: CreateProgramm.php");
exit();
?>