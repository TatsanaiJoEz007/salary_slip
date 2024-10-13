<?php
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่ามีการส่ง id ของไฟล์ PDF มาหรือไม่
    if (isset($_POST['pdf_id'])) {
        $pdf_id = $_POST['pdf_id'];

        // เริ่มการทำธุรกรรม (transaction)
        $conn->begin_transaction();

        try {
            // ลบไฟล์ออกจากเซิร์ฟเวอร์ (สมมติว่าไฟล์อยู่ในโฟลเดอร์ 'uploads/')
            $stmt = $conn->prepare("SELECT pdf_name FROM tb_pdf_files WHERE pdf_id = ?");
            $stmt->bind_param("i", $pdf_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $pdf = $result->fetch_assoc();
                $file_path = "../uploads/" . $pdf['pdf_name'];
                if (file_exists($file_path)) {
                    unlink($file_path);  // ลบไฟล์จากโฟลเดอร์
                }

                // ลบข้อมูลไฟล์ออกจากฐานข้อมูล
                $stmt = $conn->prepare("DELETE FROM tb_pdf_files WHERE pdf_id = ?");
                $stmt->bind_param("i", $pdf_id);
                $stmt->execute();

                // ยืนยันการทำธุรกรรม
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'ลบไฟล์สำเร็จ']);
            } else {
                throw new Exception("ไม่พบไฟล์ในฐานข้อมูล");
            }
        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ยกเลิกการทำธุรกรรม (rollback)
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่ได้รับข้อมูลไฟล์ PDF']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'การร้องขอไม่ถูกต้อง']);
}
?>
