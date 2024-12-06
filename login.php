<?php
session_start();

include_once("connection.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = test_input($_POST['email']);
    $password = test_input($_POST['pswd']);
    
    try {
        // Query to find the user by email
        $query = "SELECT * FROM usertbl WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo '<pre>';
        print_r($result);
        echo '</pre>';

        
        if ($result && password_verify($password, $result['Password'])) {
            echo "Login successful.";
            $_SESSION["UserID"]= $result['UserID'];
            if ($result["UserType"] == "Athlete") {
                header("Location: HomePage.html");
            } else {
                header("Location: CoachHomePage.html");
            }
        } else {
            echo "Invalid email or password.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
