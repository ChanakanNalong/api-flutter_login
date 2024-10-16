<?php
header('Access-Control-Allow-Origin: *'); // Allow any origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE'); // Allow specific methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers
header('Content-Type: application/json');

require "connect.php";

if (!$con) {
    echo json_encode(["status" => "error", "message" => "Connection error"]);
    exit();
}

// Get the raw POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true); // Decode JSON into an associative array

// Check if the required fields are present
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Email or password not set"]);
    exit();
}

$email = $data['email'];
$password = $data['password'];
$encrypted_pwd = md5($password);

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE email = ? AND password = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $email, $encrypted_pwd);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if ($count == 1) {
    echo json_encode(["status" => "success", "message" => "Login successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}

$stmt->close();
$con->close();
?>
