<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';
include 'utils.php';

header('Content-Type: application/json');

$conn = connectDB();
if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$nextId = generateEmployeeID($conn);
echo json_encode(["success" => true, "nextId" => $nextId]);
$conn->close();
