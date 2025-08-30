<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

try {
    $conn = connectDB();
    
    // Fetch all employees - updated to match your table structure
    $query = "SELECT e_id, e_name, e_email, e_phno, e_desig, created_at 
              FROM emp 
              ORDER BY created_at DESC";
    
    $result = $conn->query($query);
    
    $employees = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'employees' => $employees,
        'total' => count($employees)
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching employees: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to fetch employees'
    ]);
}
?>