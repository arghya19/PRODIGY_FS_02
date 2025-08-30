<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ems'; // Your database name

// Function to create and return database connection
function connectDB() {
    global $host, $username, $password, $database;
    
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return false; // Return false instead of exiting
    }
    
    // Set charset to utf8mb4 for proper Unicode support
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Optional: Set timezone
date_default_timezone_set('Asia/Kolkata');
?>