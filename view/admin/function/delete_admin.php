<?php
header('Content-Type: application/json');
require_once('../../config/connect.php'); // ตรวจสอบเส้นทางให้ถูกต้อง

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูล JSON ที่ส่งมาจาก AJAX
    $data = json_decode(file_get_contents("php://input"), true);
    $user_ids = isset($data['user_ids']) ? $data['user_ids'] : [];

    // ตรวจสอบว่ามี user_ids ที่ส่งมาและไม่เป็นค่าว่าง
    if (!empty($user_ids)) {
        // เตรียมคำสั่ง SQL สำหรับลบผู้ใช้ที่เลือก
        $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
        $sql = "DELETE FROM tb_user WHERE user_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // สร้าง array สำหรับ `bind_param` ที่ต้องการ
            $types = str_repeat('i', count($user_ids)); // สร้าง string ที่มีจำนวน 'i' เท่ากับจำนวน user_ids
            $stmt->bind_param($types, ...$user_ids);

            // ดำเนินการ execute SQL
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'ลบผู้ใช้ที่เลือกเรียบร้อยแล้ว';
            } else {
                $response['success'] = false;
                $response['message'] = 'เกิดข้อผิดพลาดในการลบผู้ใช้: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error;
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'ไม่มีผู้ใช้ที่เลือกสำหรับการลบ';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>
