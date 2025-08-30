<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    $dbPath = __DIR__ . "/db.php";
    if (!file_exists($dbPath)) {
        throw new Exception("Database configuration file not found at: " . $dbPath);
    }

    require_once $dbPath;

    if (!function_exists("connectDB")) {
        throw new Exception("connectDB() function not found in db.php");
    }

    $conn = connectDB();
    if (!$conn) {
        throw new Exception("Failed to establish database connection");
    }

    // ✅ Check if session has admin ID
    if (!isset($_SESSION["admin_id"]) || empty($_SESSION["admin_id"])) {
        throw new Exception("Unauthorized: Admin not logged in");
    }

    $adminId = $conn->real_escape_string(trim($_SESSION["admin_id"]));

    // Check if admin exists
    $checkSql = "SELECT u_id FROM users WHERE u_id = '$adminId' LIMIT 1";
    $checkResult = $conn->query($checkSql);

    if (!$checkResult) {
        throw new Exception("Database query error: " . $conn->error);
    }

    if ($checkResult->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No admin found with ID '$adminId'"
        ]);
        exit();
    }

    // Delete admin
    $deleteSql = "DELETE FROM users WHERE u_id = '$adminId'";
    if ($conn->query($deleteSql) === TRUE) {
        // ✅ Destroy session after deletion
        session_unset();
        session_destroy();

        echo json_encode([
            "success" => true,
            "message" => "Admin account deleted successfully",
            "admin_id" => $adminId
        ]);
    } else {
        throw new Exception("Error deleting admin: " . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    error_log("Error in delete-admin.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "error_type" => "server_error"
    ]);
}
?>
