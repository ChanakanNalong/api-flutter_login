<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE'); 
header('Access-Control-Allow-Headers: Content-Type'); 
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require "connect.php";

// ดึงข้อมูลล่าสุด 23 รายการ
$sql = "SELECT timestamp, value FROM graph ORDER BY timestamp DESC LIMIT 23"; // เพิ่ม ORDER BY และ LIMIT
$result = $con->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $timestamp = strtotime($row['timestamp']);
        $hour = date('H', $timestamp);

        if ($hour >= 6 && $hour < 12) {
            $timePeriod = 'morning'; // เช้า
        } elseif ($hour >= 12 && $hour < 18) {
            $timePeriod = 'afternoon'; // กลางวัน
        } elseif ($hour >= 18 && $hour < 24) {
            $timePeriod = 'evening'; // เย็น
        } else {
            $timePeriod = 'night'; // กลางคืน (กรณีที่มีข้อมูลช่วงเที่ยงคืนถึงตีห้า)
        }

        $data[] = [
            'timestamp' => $row['timestamp'],
            'value' => $row['value'],
            'timePeriod' => $timePeriod, // เก็บช่วงเวลาที่คำนวณแล้ว
        ];
    }
} else {
    $data = [];
}

echo json_encode($data);
$con->close();
?>
