<?php
require_once('../../config/connect.php');
require('../../../vendor/autoload.php');  // รวม FPDI ที่ติดตั้งผ่าน Composer

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    // ตรวจสอบว่าไฟล์ที่อัปโหลดเป็นไฟล์ PDF
    if ($pdfFile['type'] == 'application/pdf') {
        $originalPdfName = $pdfFile['name'];
        $pdfExtension = pathinfo($originalPdfName, PATHINFO_EXTENSION);
        $safeFileName = uniqid('pdf_', true) . '.' . $pdfExtension;

        // สร้างโฟลเดอร์ permanent_pdfs หากยังไม่มี
        $permanentFolder = 'permanent_pdfs';
        if (!is_dir($permanentFolder)) {
            mkdir($permanentFolder, 0777, true);
        }

        // บันทึกไฟล์ PDF ลงในโฟลเดอร์ uploads
        $uploaded_file_path = 'uploads/' . $safeFileName;
        if (!move_uploaded_file($pdfFile['tmp_name'], $uploaded_file_path)) {
            die("Error: Failed to upload PDF file.");
        }

        // ตรวจสอบว่าไฟล์ที่อัปโหลดถูกบันทึกไว้หรือไม่
        if (!file_exists($uploaded_file_path)) {
            die("Error: Uploaded file does not exist.");
        }

        // เปิดไฟล์ PDF โดยใช้ FPDI
        $pdf = new Fpdi();

        // นำเข้าไฟล์ PDF
        try {
            $pageCount = $pdf->setSourceFile($uploaded_file_path);
            echo "Total pages: $pageCount<br>";
        } catch (Exception $e) {
            die("Error: Cannot open PDF file. " . $e->getMessage());
        }

        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            // นำเข้าหน้า PDF
            try {
                $tplId = $pdf->importPage($pageNum);

                // ตรวจสอบว่าหน้านั้นถูกนำเข้าแล้วหรือไม่
                if ($tplId === false) {
                    throw new Exception("Failed to import page $pageNum from the PDF.");
                } else {
                    echo "Page $pageNum imported successfully. tplId: $tplId<br>";
                }
            } catch (Exception $e) {
                die("Error: Cannot import page. " . $e->getMessage());
            }

            // กำหนดขนาดกระดาษ A4 เอง
            $pageHeight = 297;  // สูง 297 มม. สำหรับกระดาษ A4
            $pageWidth = 210;   // กว้าง 210 มม.
            $halfHeight = $pageHeight / 2;

            // ครึ่งบน
            try {
                $pdfTop = new Fpdi();
                $pdfTop->AddPage();
                $pdfTop->useTemplate($tplId, 0, 0, $pageWidth, $halfHeight);  // ตั้งความสูงเป็นครึ่งบน
                $topPdfPath = $permanentFolder . '/top_' . $pageNum . '_' . $safeFileName;
                $pdfTop->Output('F', $topPdfPath);  // บันทึกครึ่งบนเป็นไฟล์ PDF
                echo "Top half of page $pageNum created successfully.<br>";
            } catch (Exception $e) {
                die("Error: Cannot create top half of the page. " . $e->getMessage());
            }

            // ครึ่งล่าง
            try {
                $pdfBottom = new Fpdi();
                $pdfBottom->AddPage();
                $pdfBottom->useTemplate($tplId, 0, -$halfHeight, $pageWidth, $halfHeight);  // ตั้งความสูงเป็นครึ่งล่าง
                $bottomPdfPath = $permanentFolder . '/bottom_' . $pageNum . '_' . $safeFileName;
                $pdfBottom->Output('F', $bottomPdfPath);  // บันทึกครึ่งล่างเป็นไฟล์ PDF
                echo "Bottom half of page $pageNum created successfully.<br>";
            } catch (Exception $e) {
                die("Error: Cannot create bottom half of the page. " . $e->getMessage());
            }
        }

        echo "<script>
            window.location.href = '../upload_page.php?status=success&message=PDF uploaded and split successfully';
        </script>";
    } else {
        // กรณีที่ไฟล์ที่อัปโหลดไม่ใช่ไฟล์ PDF
        echo "<script>
            window.location.href = '../upload_page.php?status=error&message=Only PDF files are allowed.';
        </script>";
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
