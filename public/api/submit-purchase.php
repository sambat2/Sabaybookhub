<?php
require_once '../config/condb.php'; // Include database connection
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $contact = htmlspecialchars(trim($_POST['contact']));
    $bookPrice = htmlspecialchars(trim($_POST['bookPrice']));
    $receipt = $_FILES['receipt'];

    // Validate input
    if (!$name || !$contact || !$bookPrice || !$receipt) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    if (!is_numeric($bookPrice) || $bookPrice <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid book price."]);
        exit();
    }

    // Handle file upload
    $uploadDir = __DIR__ . '/../receipts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!$receipt || $receipt['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Receipt file is missing or invalid."]);
        exit();
    }

    // Sanitize and ensure unique file name
    $fileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", basename($receipt['name']));

    // Validate file type using MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($receipt['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid file type."]);
        exit();
    }

    // Define paths
    $relativePath = 'receipts/' . $fileName;
    $receiptPath = $uploadDir . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($receipt['tmp_name'], $receiptPath)) {
        error_log("Failed to move uploaded file to $receiptPath");
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to upload receipt."]);
        exit();
    }

    // Save purchase details in the database
    $stmt = $conn->prepare("INSERT INTO purchases (name, contact, receipt_path, book_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $contact, $relativePath, $bookPrice);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Purchase submitted successfully!"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to save purchase: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}