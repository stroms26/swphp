<?php
// add_product.php

header('Content-Type: application/json');

$servername = "srv861.hstgr.io";
$username = "u493293162_datubaze";
$password = "Hokejs@26";
$dbname = "u493293162_datubaze";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Parse the incoming JSON data
$data = json_decode(file_get_contents("php://input"));

if(isset($data->sku) && isset($data->NAME) && isset($data->price) && isset($data->TYPE)) {

    $sku = $data->sku;
    $NAME = $data->NAME;
    $price = $data->price;
    $TYPE = $data->TYPE;
    $size = isset($data->size) ? $data->size : null;
    $weight = isset($data->weight) ? $data->weight : null;
    $height = isset($data->height) ? $data->height : null;
    $width = isset($data->width) ? $data->width : null;
    $LENGTH = isset($data->LENGTH) ? $data->LENGTH : null;

    // Use prepared statements to prevent SQL injection
    $sql = "INSERT INTO products (sku, NAME, price, TYPE, size, weight, height, width, LENGTH) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssss", $sku, $NAME, $price, $TYPE, $size, $weight, $height, $width, $LENGTH);

    if ($stmt->execute()) {
        echo json_encode(["message" => "New product added successfully"]);
    } else {
        error_log("Error adding product: " . $stmt->error); // Log the error for debugging
        echo json_encode(["error" => "Failed to add product"]); 
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid input"]);
}

$conn->close();