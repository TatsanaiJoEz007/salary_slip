<?php
require_once('../../config/connect.php');
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 999) {
    echo "Access Denied";
    exit();
}

if (isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];

    // ดึงข้อมูลไฟล์จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT emp_name, slip_divide FROM tb_payslips WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $payslip = $result->fetch_assoc();
        $slip_divide = $payslip['slip_divide'];
        $emp_name = $payslip['emp_name'];

        // แปลงเส้นทางไฟล์ให้ถูกต้อง
        // แทนที่ backslashes (\) และ slashes (/) ด้วย DIRECTORY_SEPARATOR เพื่อรองรับระบบปฏิบัติการ
        $relative_file_path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $slip_divide);

        // สร้างเส้นทางไฟล์เต็ม
        $file_path = __DIR__ . DIRECTORY_SEPARATOR . $relative_file_path;

        if (file_exists($file_path)) {
            // กำหนดชื่อไฟล์ดาวน์โหลดเป็นชื่อพนักงาน
            $download_name = $emp_name . '.pdf';

            // ตั้งค่า header สำหรับการดาวน์โหลดไฟล์
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($download_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit();
        } else {
            echo "ไฟล์ไม่พบ";
        }
    } else {
        echo "ไม่มีข้อมูลพนักงานนี้";
    }
} else {
    echo "ไม่มีการระบุพารามิเตอร์ที่ถูกต้อง";
}
?>
