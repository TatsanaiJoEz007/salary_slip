<?php
require_once('../../config/connect.php');

if (isset($_GET['id'])) {
    $pdf_id = intval($_GET['id']);

    // Query เพื่อดึงข้อมูลไฟล์ PDF จากฐานข้อมูล
    $sql = "SELECT pdf_name, pdf_data FROM tb_pdf_files WHERE pdf_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $pdf_id);
    $stmt->execute();
    $stmt->bind_result($pdf_name, $pdf_data);
    $stmt->fetch();
    $stmt->close();

    // ตรวจสอบว่ามีข้อมูลไฟล์หรือไม่
    if ($pdf_data) {
        // ตั้งค่า header สำหรับการดาวน์โหลดไฟล์ PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $pdf_name . '"');
        header('Content-Length: ' . strlen($pdf_data));

        // แสดงข้อมูลไฟล์ PDF
        echo $pdf_data;
    } else {
        echo "ไฟล์ PDF ไม่พบหรือเกิดข้อผิดพลาด.";
    }
} else {
    echo "ไม่มีข้อมูล ID ของไฟล์ PDF ที่ถูกต้อง.";
}

$conn->close();
?>
