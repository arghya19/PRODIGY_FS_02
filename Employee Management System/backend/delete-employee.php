<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

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

    $input = file_get_contents("php://input");
    if (empty($input)) {
        throw new Exception("No input data received");
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data: " . json_last_error_msg());
    }

    if (!isset($data["employeeId"]) || empty(trim($data["employeeId"]))) {
        throw new Exception("Employee ID is required");
    }

    $employeeId = $conn->real_escape_string(trim($data["employeeId"]));

    // Check if employee exists
    $checkSql = "SELECT e_id FROM emp WHERE e_id = '$employeeId' LIMIT 1";
    $checkResult = $conn->query($checkSql);

    if (!$checkResult) {
        throw new Exception("Database query error: " . $conn->error);
    }

    if ($checkResult->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No employee found with ID '$employeeId'"
        ]);
        exit();
    }

    // Delete employee
    $deleteSql = "DELETE FROM emp WHERE e_id = '$employeeId'";
    if ($conn->query($deleteSql) === TRUE) {
        echo json_encode([
            "success" => true,
            "message" => "Employee deleted successfully",
            "employee_id" => $employeeId
        ]);
    } else {
        throw new Exception("Error deleting employee: " . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    error_log("Error in delete-employee.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "error_type" => "server_error"
    ]);
}
?>
