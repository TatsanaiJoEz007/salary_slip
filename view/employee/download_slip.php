<?php
// เชื่อมต่อฐานข้อมูล
require_once('../config/connect.php');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับ emp_id จาก GET
$emp_id = isset($_GET['emp_id']) ? trim($_GET['emp_id']) : '';

if (empty($emp_id)) {
    echo "ไม่มีรหัสพนักงานที่ถูกต้อง";
    exit;
}

// ดึงชื่อไฟล์ PDF จากฐานข้อมูล
$stmt = $conn->prepare("SELECT slip_divide FROM tb_payslips WHERE emp_id = ? LIMIT 1");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payslip = $result->fetch_assoc();
    $fileName = $payslip['slip_divide'];  // ชื่อไฟล์ PDF
    $filePath = "../admin/function/" . $fileName;  // เส้นทางของไฟล์ PDF

    if (file_exists($filePath)) {
        // กำหนด headers สำหรับการดาวน์โหลด PDF
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // ล้าง output buffer และส่งไฟล์ PDF
        ob_clean();
        flush();
        readfile($filePath);  // ส่งไฟล์ไปยังเบราว์เซอร์
        exit;
    } else {
        echo "ไม่พบไฟล์ PDF ในเซิร์ฟเวอร์";
    }
} else {
    echo "ไม่พบข้อมูลสลิปเงินเดือนสำหรับรหัสพนักงานนี้";
}

$stmt->close();
$conn->close();
?>
