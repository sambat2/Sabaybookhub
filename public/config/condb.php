<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sabaybookhub"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

?>