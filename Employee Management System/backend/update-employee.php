<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting but don't display errors (they break JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Check if db.php exists before including
    $dbPath = __DIR__ . "/db.php";
    if (!file_exists($dbPath)) {
        throw new Exception("Database configuration file not found at: " . $dbPath);
    }

    // Include database connection
    require_once $dbPath;
    
    // Check if connectDB function exists
    if (!function_exists('connectDB')) {
        throw new Exception("connectDB() function not found in db.php");
    }
    
    $conn = connectDB();
    
    if (!$conn) {
        throw new Exception("Failed to establish database connection");
    }

    // Get JSON input
    $input = file_get_contents("php://input");
    
    if (empty($input)) {
        throw new Exception("No input data received");
    }
    
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data: " . json_last_error_msg());
    }

    // Validate required fields
    if (!isset($data['employeeId']) || empty($data['employeeId'])) {
        throw new Exception("Employee ID is required");
    }

    // Sanitize input data
    $id = $conn->real_escape_string(trim($data['employeeId']));
    $name = $conn->real_escape_string(trim($data['name'] ?? ''));
    $email = $conn->real_escape_string(trim($data['email'] ?? ''));
    $phone = $conn->real_escape_string(trim($data['phone'] ?? ''));
    $department = $conn->real_escape_string(trim($data['department'] ?? ''));

    // Validate required fields
    if (empty($name) || empty($email) || empty($department)) {
        throw new Exception("Name, email, and department are required fields");
    }

    // Check if employee exists first
    $checkSql = "SELECT e_id FROM emp WHERE e_id = '$id' LIMIT 1";
    $checkResult = $conn->query($checkSql);
    
    if (!$checkResult) {
        throw new Exception("Database query error: " . $conn->error);
    }
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("Employee with ID '$id' not found");
    }

    // Update employee data
    $sql = "UPDATE emp 
            SET e_name = '$name', 
                e_email = '$email', 
                e_phno = '$phone', 
                e_desig = '$department' 
            WHERE e_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo json_encode([
                "success" => true, 
                "message" => "Employee updated successfully",
                "affected_rows" => $conn->affected_rows,
                "employee_id" => $id
            ]);
        } else {
            echo json_encode([
                "success" => false, 
                "message" => "No changes were made to employee data"
            ]);
        }
    } else {
        throw new Exception("Database update error: " . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error in update-employee.php: " . $e->getMessage());
    
    // Return JSON error response
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => $e->getMessage(),
        "error_type" => "server_error"
    ]);
}
?>