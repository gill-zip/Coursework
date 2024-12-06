<?php
session_start();

include_once("connection.php");
array_map("htmlspecialchars", $_POST);
print_r($_POST);
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
//1 = test_input()

$hashed_password = password_hash(test_input($_POST["pswd"]), PASSWORD_DEFAULT);
echo $hashed_password;


$stmt = $conn->prepare("INSERT INTO usertbl (UserType,FirstName,Lastname,Gender,Email,AccDescription,SportingScores,SportingAchivements,Password)
VALUES (:optradiotype,:firstname,:lastname,:optradiogender,:email,:accdescription,:sportsscore,:sportingachievments,:pswd)");

$stmt->bindParam(':optradiotype', test_input($_POST["optradiotype"]));
$stmt->bindParam(':firstname', test_input($_POST["firstname"]));
$stmt->bindParam(':lastname', test_input($_POST["lastname"]));
$stmt->bindParam(':optradiogender', test_input($_POST["optradiogender"]));
$stmt->bindParam(':email', test_input($_POST["email"]));
$stmt->bindParam(':accdescription',test_input($_POST["accdescrpiton"]));
$stmt->bindParam(':sportsscore', test_input($_POST["sportscore"]));
$stmt->bindParam(':sportingachievments',test_input($_POST["sportingachievments"]));
$stmt->bindParam(':pswd', $hashed_password);
$stmt->execute();
// Get the last inserted ID
$last_id = $conn->lastInsertId();

// Now retrieve the row using the last inserted ID
 $query = "SELECT * FROM usertbl WHERE UserID = :UserID";
$stmt = $conn->prepare($query);
$stmt->execute([':UserID' => $last_id]);
// Fetch the row data
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
     //echo "Inserted row data: Name: " . $row['UserID'];
     $_SESSION['UserID'] = $row['UserID'];
} else {
    echo "No results found.";
}
$conn=null;
header("Location: HomePage.html");
exit();
?>