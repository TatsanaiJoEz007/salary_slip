<?php
// session_start(); // ถ้าต้องการเช็คการเข้าสู่ระบบ

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

// เตรียมคำสั่ง SQL เพื่อดึงข้อมูลสลิปเงินเดือน
$stmt = $conn->prepare("SELECT emp_name, emp_dep, salary_income, salary_deduction, salary_net, content FROM tb_payslips WHERE emp_id = ?");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $contents = [];
    $emp_name = '';
    $emp_dep = '';
    while ($row = $result->fetch_assoc()) {
        $emp_name = $row['emp_name'];
        $emp_dep = $row['emp_dep'];
        $contents[] = $row['content'];
        $salary_income = $row['salary_income'];
        $salary_deduction = $row['salary_deduction'];
        $salary_net = $row['salary_net'];
    }
} else {
    echo "ไม่พบข้อมูลสลิปเงินเดือนสำหรับรหัสพนักงานนี้";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สลิปเงินเดือนพนักงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo/logo.png" type="image/x-icon">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Kanit', sans-serif;
        }

        .salary-slip-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info h2 {
            font-size: 28px;
            font-weight: bold;
        }

        .employee-info,
        .salary-info {
            margin-bottom: 20px;
        }

        .employee-info h5,
        .salary-info h5 {
            font-size: 20px;
            font-weight: bold;
        }

        .salary-info table {
            width: 100%;
        }

        .salary-info table th,
        .salary-info table td {
            padding: 8px;
            text-align: left;
        }

        .btn-download {
            margin-top: 20px;
            text-align: center;
        }

        .btn-download button {
            background-color: #FF0000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-download button:hover {
            background-color: #cc0000;
            transform: scale(1.05);
        }


    </style>
</head>

<body>
    <div class="salary-slip-container">
        <div class="company-info">
            <h2>บริษัท เอส.ที.เอ็ม. โปร เซอร์วิส จำกัด</h2>
            <p>สลิปเงินเดือนพนักงาน</p>
        </div>

        <div class="employee-info">
            <h5>ข้อมูลพนักงาน</h5>

            <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($emp_name); ?></p>
            <p><strong>แผนก:</strong> <?php echo htmlspecialchars($emp_dep); ?></p>
            <p><strong>รหัสพนักงาน:</strong> <?php echo htmlspecialchars($emp_id); ?></p>
        </div>

        <div class="salary-info">
            <h5>รายละเอียดเงินเดือน</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>รายการ</th>
                        <th>จำนวนเงิน (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>รวมเงินได้</td>
                        <td><?php echo number_format($salary_income); ?></td>
                    </tr>
                    <tr>
                        <td>รวมเงินหัก</td>
                        <td><?php echo number_format($salary_deduction); ?></td>
                    </tr>
                    <tr>
                        <td>#เงินได้สุทธิ</td>
                        <td><?php echo number_format($salary_net); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="btn-download">
            <button id="downloadButton">
                <i class="fas fa-download"></i>&nbsp; ดาวน์โหลดสลิปเงินเดือน
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        document.getElementById('downloadButton').addEventListener('click', function() {
            window.location.href = `download_slip.php?emp_id=<?php echo urlencode($emp_id); ?>`;
        });
    </script>
</body>

</html>