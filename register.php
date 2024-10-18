<?php
header('Access-Control-Allow-Origin: *'); // Allow any origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE'); // Allow specific methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers
header('Content-Type: application/json');

require "connect.php";

// Check database connection
if (!$con) {
    echo json_encode(["status" => "error", "message" => "Connection error"]);
    exit();
}


// Get the raw POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true); // Decode JSON into an associative array

$name = isset($data['name']) ? $data['name'] : '';
$password = isset($data['password']) ? $data['password'] : '';
$email = isset($data['email']) ? $data['email'] : '';

if (empty($name) || empty($password) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

// Encrypt the password
$encrypted_pwd = md5($password);

// Check if the email already exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if ($count > 0) {
    // If the email already exists, return an error message
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
} else {
    // Insert new user using Prepared Statements
    $insert = "INSERT INTO users (name, password, email) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insert);
    $stmt->bind_param("sss", $name, $encrypted_pwd, $email);
    
    // Check if the insert was successful
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error in registration"]);
    }
}

// Close statement and database connection
$stmt->close();
$con->close();
?>
