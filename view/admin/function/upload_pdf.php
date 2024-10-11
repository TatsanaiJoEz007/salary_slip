<?php
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    // ตรวจสอบว่าไฟล์ที่อัปโหลดเป็น PDF หรือไม่
    if ($pdfFile['type'] == 'application/pdf') {
        // อ่านเนื้อหาไฟล์
        $pdfData = file_get_contents($pdfFile['tmp_name']);
        $pdfName = $pdfFile['name'];

        // เตรียมคำสั่ง SQL สำหรับการแทรกข้อมูล โดยใช้การอัปโหลดข้อมูล BLOB
        $stmt = $conn->prepare("INSERT INTO tb_pdf_files (pdf_name, pdf_data) VALUES (?, ?)");

        // ตรวจสอบว่าเตรียมคำสั่ง SQL สำเร็จหรือไม่
        if ($stmt) {
            // ผูกชื่อไฟล์และข้อมูลไฟล์ PDF เป็น BLOB
            $null = NULL;
            $stmt->bind_param("sb", $pdfName, $null);

            // ใช้ send_long_data เพื่อส่งข้อมูล BLOB
            $stmt->send_long_data(1, $pdfData);

            // ดำเนินการอัปโหลดข้อมูล
            if ($stmt->execute()) {
                // ดึง ID ของ PDF ที่เพิ่งอัปโหลดมา
                $pdf_id = $stmt->insert_id;

                // เก็บไฟล์ลงในเซิร์ฟเวอร์เพื่อให้ process.py เข้าถึงได้
                $uploaded_file_path = 'uploads/' . $pdfName;
                file_put_contents($uploaded_file_path, $pdfData);

                // เรียกใช้สคริปต์ process.py พร้อมส่ง path ของไฟล์ PDF
                $python_script_path = 'process_pdf.py';
                $command = escapeshellcmd("python $python_script_path $uploaded_file_path $pdf_id");
                $output = shell_exec($command);

                // ตรวจสอบการทำงานของ Python script
                if ($output) {
                    echo json_encode(['status' => 'success', 'message' => 'PDF uploaded and processed successfully', 'python_output' => $output]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'PDF uploaded but processing failed']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error uploading PDF: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Only PDF files are allowed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();
?>
