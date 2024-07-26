<?php
// db.php
$servername = "srv861.hstgr.io";
$username = "u493293162_datubaze";
$password = "Hokejs@26";
$dbname = "u493293162_datubaze";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); 
    die("Connection failed: " . $conn->connect_error); 
}
