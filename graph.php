<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE'); 
header('Access-Control-Allow-Headers: Content-Type'); 
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require "connect.php";

$sql = "SELECT timestamp, value FROM graph";
$result = $con->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'timestamp' => $row['timestamp'],
            'value' => $row['value']
        ];
    }
} else {
    $data = [];
}

echo json_encode($data);
$con->close();
?>
