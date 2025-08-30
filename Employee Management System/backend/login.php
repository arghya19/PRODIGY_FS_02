<?php
session_start();
header('Content-Type: application/json');
require_once "db.php";

// Connect to DB
$conn = connectDB();
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Read JSON from AJAX
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'], $data['password'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

// Secure query
$stmt = $conn->prepare("SELECT u_id, u_name, u_pass FROM users WHERE u_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['u_pass'])) {
        // Store session
        $_SESSION['admin_id'] = $row['u_id'];
        $_SESSION['username'] = $row['u_name'];
        $_SESSION['logged_in'] = true;

        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
