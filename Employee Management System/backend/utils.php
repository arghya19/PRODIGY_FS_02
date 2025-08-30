<?php
function generateUserID($conn) {
    $year = date("Y"); // Get current year, e.g., 2025
    $prefix = "E" . $year;

    // Query to get the last u_id for the current year
    $query = "SELECT u_id FROM users WHERE u_id LIKE '{$prefix}%' ORDER BY u_id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $lastID = $row['u_id']; // e.g., E2025003

        // Extract the numeric part dynamically
        $numPart = substr($lastID, strlen($prefix)); 
        $newNum = (int)$numPart + 1;
    } else {
        // If no record exists for the year, start from 1
        $newNum = 1;
    }

    // Format new ID (e.g., E2025001)
    $newID = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);

    return $newID;
}

function getTotalEmployees($conn) {
    $sql = "SELECT COUNT(*) AS total FROM emp";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'];
    } else {
        return 0;
    }
}

function getLastEmployee($conn) {
    $sql = "SELECT * FROM emp ORDER BY e_id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row; // returns full row
    } else {
        return null;
    }
}



function generateEmployeeID($conn) {
    // Get last two digits of the current year
    $year = date("y"); // e.g., "25"
    $prefix = "E" . $year; // E25

    // Query to get the last e_id for the current year from emp table
    $query = "SELECT e_id FROM emp WHERE e_id LIKE '{$prefix}%' ORDER BY e_id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $lastID = $row['e_id']; // e.g., E25003

        // Extract the numeric part dynamically
        $numPart = substr($lastID, strlen($prefix)); // e.g., "003"
        $newNum = (int)$numPart + 1;
    } else {
        // If no record exists for the year, start from 1
        $newNum = 1;
    }

    // Format new ID (e.g., E25001, E25002...)
    $newID = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);

    return $newID;
}
