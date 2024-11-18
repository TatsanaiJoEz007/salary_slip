<?php
require_once('../../config/connect.php');
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 999) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selected_ids']) && !empty($_POST['selected_ids'])) {
        $selected_ids = $_POST['selected_ids'];

        $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

        // Prepare to select the slip_divide filenames before deleting the records
        $sql_select = "SELECT slip_divide FROM tb_payslips WHERE emp_id IN ($placeholders)";
        $stmt_select = $conn->prepare($sql_select);
        $types = str_repeat('s', count($selected_ids));
        $stmt_select->bind_param($types, ...$selected_ids);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $files_to_delete = [];
        while ($row = $result->fetch_assoc()) {
            $files_to_delete[] = $row['slip_divide'];
        }
        $stmt_select->close();

        // Delete records from database
        $sql = "DELETE FROM tb_payslips WHERE emp_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$selected_ids);
        if ($stmt->execute()) {
            $stmt->close();

            // Delete files from 'permanent_pdfs' directory
            foreach ($files_to_delete as $file) {
                $file_path = '../../permanent_pdfs/' . $file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่มีข้อมูลที่เลือก']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
