<?php
// เพิ่มหน่วยความจำและเวลาการประมวลผล
ini_set('memory_limit', '1024M'); // เพิ่มหน่วยความจำเป็น 1024MB
ini_set('max_execution_time', 300); // เพิ่มเวลาในการประมวลผลเป็น 300 วินาที (5 นาที)

header('Content-Type: application/json'); // Ensure the response is JSON

require_once('../../config/connect.php');
require '../../../vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xlsxFile'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $file = $_FILES['xlsxFile'];

    // ตรวจสอบชนิดของไฟล์
    $fileType = $file['type'];
    $allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Only .xlsx files are allowed.']);
        exit;
    }

    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(); // Convert the spreadsheet rows to an array

        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();

        // ตรวจสอบให้แน่ใจว่าแถวแรกเป็น header และไม่บันทึกลง DB
        $isFirstRow = true;
        foreach ($rows as $row) {
            // Skip the header row
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // ตรวจสอบว่าแถวมีข้อมูลครบถ้วนตามที่ต้องการ
            if (count($row) >= 6) {
                $stmt = $pdo->prepare("INSERT INTO tb_salary_slip (emp_id, emp_name, dep_name, pay_date_start, pay_date_end, pay_date, account_no, income_daily_rate, income_daily, income_holiday, income_75, income_1_5, income_2, income_3, income_ot, income_bonus, income_position_allowance, income_other_allowance, income_total, deduction_ss, deduction_advance, deduction_uniform, deduction_other, deduction_total, net_income) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute($row);
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Return a success message
        echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        // Return an error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (Exception $e) {
        // Handle any errors with file reading
        echo json_encode(['status' => 'error', 'message' => 'Error reading Excel file: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing xlsxFile']);
}
