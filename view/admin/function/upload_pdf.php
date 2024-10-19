<?php
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    // ตรวจสอบว่าไฟล์ที่อัปโหลดเป็นไฟล์ PDF
    if ($pdfFile['type'] == 'application/pdf') {
        $pdfData = file_get_contents($pdfFile['tmp_name']);
        $originalPdfName = $pdfFile['name'];

        // สร้างชื่อไฟล์ที่ปลอดภัยสำหรับการบันทึก
        $pdfExtension = pathinfo($originalPdfName, PATHINFO_EXTENSION);
        $safeFileName = uniqid('pdf_', true) . '.' . $pdfExtension;

        // บันทึกข้อมูลไฟล์ลงฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO tb_pdf_files (pdf_name, pdf_data, original_pdf_name) VALUES (?, ?, ?)");
        if ($stmt) {
            $null = NULL; // ค่า null ใช้สำหรับการ bind binary data
            $stmt->bind_param("sbs", $safeFileName, $null, $originalPdfName);
            $stmt->send_long_data(1, $pdfData);

            // หากการบันทึกลงฐานข้อมูลสำเร็จ
            if ($stmt->execute()) {
                $pdf_id = $stmt->insert_id;  // รับ PDF ID ที่เพิ่งบันทึกลงฐานข้อมูล
                $uploaded_file_path = 'uploads/' . $safeFileName;

                // บันทึกไฟล์ PDF ลงในโฟลเดอร์ uploads
                file_put_contents($uploaded_file_path, $pdfData);

                // เรียก API เพื่อประมวลผล PDF ใน process_pdf.js
                $api_url = 'http://localhost:3000/process-pdf';  // URL ของ API ใน Node.js

                // ใช้ cURL ส่งข้อมูลไปที่ API ของ Node.js
                $ch = curl_init($api_url);
                $data = [
                    'filePath' => $uploaded_file_path,  // path ของไฟล์ที่อัปโหลด
                    'pdfId' => $pdf_id,                 // ID ของไฟล์ PDF
                ];

                // กำหนดค่าการส่งข้อมูลแบบ POST
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

                $response = curl_exec($ch);
                curl_close($ch);

                // ตรวจสอบผลลัพธ์ที่ได้จาก API
                if ($response) {
                    $response_data = json_decode($response, true);
                    if (isset($response_data['status']) && $response_data['status'] == 'success') {
                        echo "<script>
                            window.location.href = '../upload_page.php?status=success&message=PDF uploaded and processed successfully';
                        </script>";
                    } else {
                        echo "<script>
                            window.location.href = '../upload_page.php?status=error&message=PDF uploaded but processing failed: {$response_data['message']}';
                        </script>";
                    }
                } else {
                    // กรณีที่ไม่สามารถติดต่อกับ API ได้
                    echo "<script>
                        window.location.href = '../upload_page.php?status=error&message=Failed to communicate with the processing server';
                    </script>";
                }
            } else {
                // กรณีที่เกิดข้อผิดพลาดในการบันทึกลงฐานข้อมูล
                echo "<script>
                    window.location.href = '../upload_page.php?status=error&message=Error uploading PDF: {$stmt->error}';
                </script>";
            }
            $stmt->close();
        } else {
            // กรณีที่ไม่สามารถเตรียมคำสั่ง SQL ได้
            echo "<script>
                window.location.href = '../upload_page.php?status=error&message=Failed to prepare SQL statement: {$conn->error}';
            </script>";
        }
    } else {
        // กรณีที่ไฟล์ที่อัปโหลดไม่ใช่ไฟล์ PDF
        echo "<script>
            window.location.href = '../upload_page.php?status=error&message=Only PDF files are allowed.';
        </script>";
    }
} else {
    // กรณีที่ไม่มีการอัปโหลดไฟล์หรือการร้องขอไม่ถูกต้อง
    echo "<script>
        window.location.href = '../upload_page.php?status=error&message=Invalid request';
    </script>";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
