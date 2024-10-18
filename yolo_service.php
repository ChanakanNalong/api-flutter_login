<?php
// Set headers to allow cross-origin requests and specify content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Set up database connection
$con = mysqli_connect('localhost', 'root', '', 'flutter_login');

// ตรวจสอบการเชื่อมต่อ
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ตรวจสอบว่าเป็นการเรียกแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // อ่านข้อมูลจาก JSON request body
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่า value เป็น int หรือไม่
    if (isset($data['value']) && is_int($data['value'])) {
        $value = $data['value'];

        // เตรียมและรัน SQL statement
        $stmt = $conn->prepare("INSERT INTO parking_data (value) VALUES (?)");
        $stmt->bind_param("i", $value);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['message' => 'Data saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error saving data']);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid data format']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}

// ปิดการเชื่อมต่อ
$conn->close();

?>