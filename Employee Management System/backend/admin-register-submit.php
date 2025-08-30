<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection and utilities
require_once 'db.php';
require_once 'utils.php';

// Function to send JSON response
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
}

// Initialize connection variable
$conn = null;

try {
    // Connect to database
    $conn = connectDB();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // If no JSON input, try to get from POST data
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    if (empty($input['u_id']) || empty($input['u_name']) || empty($input['u_pass'])) {
        sendResponse(false, 'All fields are required: User ID, Full Name, and Password.');
    }
    
    // Sanitize input data
    $u_id = trim($input['u_id']);
    $u_name = trim($input['u_name']);
    $u_pass = trim($input['u_pass']);
    
    // Validate input data
    if (strlen($u_pass) < 6) {
        sendResponse(false, 'Password must be at least 6 characters long.');
    }
    
    if (strlen($u_name) < 2) {
        sendResponse(false, 'Full name must be at least 2 characters long.');
    }
    
    // Check if User ID already exists
    $checkIDQuery = "SELECT u_id FROM users WHERE u_id = ?";
    $checkIDStmt = $conn->prepare($checkIDQuery);
    
    if (!$checkIDStmt) {
        sendResponse(false, 'Database error: ' . $conn->error);
    }
    
    $checkIDStmt->bind_param("s", $u_id);
    $checkIDStmt->execute();
    $idResult = $checkIDStmt->get_result();
    
    if ($idResult->num_rows > 0) {
        $checkIDStmt->close();
        sendResponse(false, 'User ID already exists. Please use a different User ID.');
    }
    
    $checkIDStmt->close();
    
    // Check if User Name already exists
    $checkNameQuery = "SELECT u_name FROM users WHERE u_name = ?";
    $checkNameStmt = $conn->prepare($checkNameQuery);
    
    if (!$checkNameStmt) {
        sendResponse(false, 'Database error: ' . $conn->error);
    }
    
    $checkNameStmt->bind_param("s", $u_name);
    $checkNameStmt->execute();
    $nameResult = $checkNameStmt->get_result();
    
    if ($nameResult->num_rows > 0) {
        $checkNameStmt->close();
        sendResponse(false, 'User name already exists. Please use a different name.');
    }
    
    $checkNameStmt->close();
    
    // Hash the password securely
    $hashedPassword = password_hash($u_pass, PASSWORD_DEFAULT);
    
    // Set role as admin by default
    $u_role = 'admin';
    
    // Get current timestamp
    $created_at = date('Y-m-d H:i:s');
    
    // Prepare SQL statement to insert new admin user
    $insertQuery = "INSERT INTO users (u_id, u_name, u_pass, u_role, created_at) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    
    if (!$insertStmt) {
        sendResponse(false, 'Database error: ' . $conn->error);
    }
    
    // Bind parameters
    $insertStmt->bind_param("sssss", $u_id, $u_name, $hashedPassword, $u_role, $created_at);
    
    // Execute the statement
    if ($insertStmt->execute()) {
        $insertStmt->close();
        
        // Generate new User ID for next registration - FIX: Pass $conn parameter
        $newUserID = generateUserID($conn);
        
        sendResponse(true, 'Admin account created successfully!', [
            'user_id' => $u_id,
            'user_name' => $u_name,
            'user_role' => $u_role,
            'next_user_id' => $newUserID,
            'created_at' => $created_at
        ]);
        
    } else {
        $insertStmt->close();
        sendResponse(false, 'Failed to create admin account. Please try again.');
    }
    
} catch (Exception $e) {
    error_log("Admin registration error: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again.');
} finally {
    // Close database connection if it exists
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>