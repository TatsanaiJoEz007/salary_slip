<?php
// แสดงข้อผิดพลาดทั้งหมด (สำหรับการพัฒนา)
// ในสภาพแวดล้อมการผลิตควรปิดการแสดงข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$host = "localhost"; 
$username = "root"; // แนะนำให้ใช้ผู้ใช้ที่ไม่ใช่ root ในการผลิต
$pass = ""; 
$db = "salary_slip";

// สร้างการเชื่อมต่อใหม่ด้วย MySQLi
$conn = new mysqli($host, $username, $pass, $db);

// ตั้งค่า charset เป็น utf8mb4
$conn->set_charset("utf8mb4");

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// หากคุณไม่ต้องการใช้ PDO ให้ลบโค้ดส่วนนี้ออก
/*
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $username, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
*/
?>
