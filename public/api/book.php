<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/condb.php'; // Include database connection

// Fetch books from the database
$sql = "SELECT id, title, author, price, image FROM books";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

$books = [];
while ($row = $result->fetch_assoc()) {
    $row['image'] = 'uploads/' . basename($row['image']);
    $books[] = $row;
}

echo json_encode($books);
$conn->close();