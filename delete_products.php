<?php
// delete_products.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

include 'db.php';

// Log the raw request data
error_log("Raw Request Data: " . file_get_contents('php://input'));

$data = json_decode(file_get_contents('php://input'), true);

// Check if 'ids' is set and is an array
if (isset($data['ids']) && is_array($data['ids'])) {
    $productIds = $data['ids'];

    // Check if any product IDs were selected
    if (empty($productIds)) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "No products selected for deletion"]);
        exit; // Stop further processing
    }

    // Sanitize product IDs (ensure they are integers)
    $productIds = array_map('intval', $productIds);

    // Use a prepared statement to prevent SQL injection
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $conn->prepare("DELETE FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($productIds)); // 'i' for integer

    if (!$stmt) { // Check if statement preparation failed
        $error = "Failed to prepare statement: " . $conn->error;
        http_response_code(500);
        echo json_encode(["error" => $error]);
        error_log($error); // Log to server error log
        exit;
    }

    $stmt->bind_param($types, ...$productIds);

    if ($stmt->execute()) {
        http_response_code(204); // No Content on successful delete
    } else {
        $error = "Failed to delete products: " . $stmt->error;
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => $error]);
        error_log($error); // Log to server error log
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    $error = "Invalid product IDs format or missing IDs";
    echo json_encode(["error" => $error]);
    error_log("Error Details: " . $error); // Log the error details
}

$conn->close();