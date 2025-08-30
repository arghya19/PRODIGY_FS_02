<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';
require_once 'utils.php';

function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data   // 👈 always use "data"
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
}

try {
    $newUserID = generateUserID($conn);

    if ($newUserID) {
        sendResponse(true, 'New User ID generated successfully.', [
            'next_user_id' => $newUserID   // 👈 matches frontend expectation
        ]);
    } else {
        sendResponse(false, 'Failed to generate new User ID.', null);
    }

} catch (Exception $e) {
    error_log("Get new User ID error: " . $e->getMessage());
    http_response_code(500);
    sendResponse(false, 'An error occurred while generating new User ID.', null);
}
