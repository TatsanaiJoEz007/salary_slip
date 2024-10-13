<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สลิปเงินเดือนพนักงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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
        <!-- ข้อมูลบริษัท -->
        <div class="company-info">
            <h2>บริษัท เอส.ที.เอ็ม. (2016) จำกัด</h2>
            <p>สลิปเงินเดือนพนักงาน</p>
            <p>วันที่จ่าย: 01 ตุลาคม 2024</p>
        </div>

        <!-- ข้อมูลพนักงาน -->
        <div class="employee-info">
            <h5>ข้อมูลพนักงาน</h5>
            <p><strong>ชื่อ:</strong> สมชาย ใจดี</p>
            <p><strong>แผนก:</strong> แผนกการเงิน</p>
            <p><strong>รหัสพนักงาน:</strong> EMP001</p>
        </div>

        <!-- รายละเอียดเงินเดือน -->
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
                        <td>เงินเดือน</td>
                        <td>25,000.00</td>
                    </tr>
                    <tr>
                        <td>ค่าล่วงเวลา (OT)</td>
                        <td>2,000.00</td>
                    </tr>
                    <tr>
                        <td>ค่าเดินทาง</td>
                        <td>1,500.00</td>
                    </tr>
                    <tr>
                        <td>โบนัส</td>
                        <td>3,000.00</td>
                    </tr>
                    <tr>
                        <td>รวมรายได้</td>
                        <td>31,500.00</td>
                    </tr>
                    <tr>
                        <td>ภาษี</td>
                        <td>1,500.00</td>
                    </tr>
                    <tr>
                        <td>ประกันสังคม</td>
                        <td>750.00</td>
                    </tr>
                    <tr>
                        <td>รวมรายการหัก</td>
                        <td>2,250.00</td>
                    </tr>
                    <tr>
                        <td><strong>สุทธิที่ได้รับ</strong></td>
                        <td><strong>29,250.00</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ปุ่มดาวน์โหลด PDF -->
        <div class="btn-download">
            <button id="downloadButton"><i class="fas fa-download"></i>&nbsp; ดาวน์โหลดสลิปเงินเดือน</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        document.getElementById('downloadButton').addEventListener('click', function () {
            alert('การดาวน์โหลดสลิปเงินเดือนจะเริ่มขึ้นในไม่ช้า');
        });
    </script>
</body>

</html>