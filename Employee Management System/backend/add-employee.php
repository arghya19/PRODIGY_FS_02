<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
include 'utils.php'; // make sure generateEmployeeID() is here

header('Content-Type: application/json');

// Add CORS headers if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decode JSON input
        $input = json_decode(file_get_contents("php://input"), true);

        // If decoding fails
        if (!$input) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid JSON input",
                "raw" => file_get_contents("php://input")
            ]);
            exit;
        }

        // Get database connection
        $conn = connectDB();
        if (!$conn) {
            echo json_encode(["success" => false, "message" => "Database connection failed"]);
            exit;
        }

        // Get and sanitize input data
        $name       = trim($input['name'] ?? '');
        $email      = trim($input['email'] ?? '');
        $phone      = trim($input['phone'] ?? '');
        $department = trim($input['department'] ?? '');

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($department)) {
            echo json_encode([
                "success" => false, 
                "message" => "All fields are required",
                "received_data" => $input
            ]);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Invalid email format"]);
            exit;
        }

        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT e_id FROM emp WHERE e_email = ?");
        if ($checkStmt) {
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(["success" => false, "message" => "Email already exists"]);
                exit;
            }
            $checkStmt->close();
        }

        // Generate Employee ID
        $employeeId = generateEmployeeID($conn);
        if (empty($employeeId)) {
            echo json_encode(["success" => false, "message" => "Failed to generate employee ID"]);
            exit;
        }

        // Insert new employee
        $stmt = $conn->prepare("INSERT INTO emp (e_id, e_name, e_email, e_phno, e_desig) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("sssss", $employeeId, $name, $email, $phone, $department);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "employeeId" => $employeeId,
                    "message" => "Employee added successfully"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No rows were inserted"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "DB error: " . $stmt->error
            ]);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Exception: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. POST required."
    ]);
}
?>
