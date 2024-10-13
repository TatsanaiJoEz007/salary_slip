<!DOCTYPE html>
<html lang="th">

<?php require_once('config/connect.php'); ?>

<head>
    <title>Track Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add Favicon -->
    <link rel="icon" href="assets/img/logo/logo.png" type="image/x-icon">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Kanit', sans-serif;
        }

        /* Card Styling */
        .track-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
        }

        .track-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .tracking-input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            justify-content: center;
        }

        .track-button {
            border: none;
            height: 60px;
            background-color: #FF0000;
            color: white;
            padding: 0 20px;
            border-radius: 25px;
            font-weight: 700;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        .track-button:hover {
            background-color: #cc0000;
        }

        .tracking-input {
            border: none;
            height: 50px;
            outline: none;
            border-radius: 25px;
            padding: 0 10px;
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Styling */
        @media (min-width: 768px) {
            .track-card {
                flex-direction: column;
                max-width: 600px;
            }

            .tracking-input {
                height: 60px;
            }
        }

        @media (max-width: 768px) {
            .tracking-input-group {
                width: 100%;
                flex-direction: column;
            }

            .track-card {
                padding: 15px;
            }

            .track-button {
                width: 100%;
            }

            .tracking-input {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="track-card">
        <h3>ระบบตรวจสอบสลิปเงินเดือนพนักงาน</h3>
        <!-- Responsive Image -->
        <img src="assets/img/logo/logo.png" alt="logo" class="logo" style="width : 300px">

        <!-- Input and Button in Card -->
        <div class="tracking-input-group">
            <input type="text" class="form-control tracking-input" id="trackingIdInput" name="trackingId"
                placeholder="กรอกเลขรหัสพนักเพื่อตรวจสอบสลิปเงินเดือน (Ex. P#####)" required>
            <button class="track-button">ตรวจสอบ</button>
        </div>
    </div>

    <script>
        document.querySelector('.track-button').addEventListener('click', function () {
            var trackingId = document.getElementById('trackingIdInput').value.trim();

            if (trackingId === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณากรอกเลขพนักงาน',
                    text: 'เลขพนักงานไม่ควรเป็นค่าว่าง'
                });
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'function/search_tracking.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    console.log(xhr.responseText); // ดูว่ามีอะไรที่ส่งกลับมา
                    if (xhr.status == 200) {
                        try {
                            var response = JSON.parse(xhr.responseText.trim());
                            if (response.status === 'match') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'พบข้อมูลพนักงาน',
                                    text: 'กำลังนำคุณไปยังสลิปเงินเดือน...',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(function () {
                                    window.location.href = `employee/salary_slip?emp_id=${encodeURIComponent(trackingId)}`;
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ไม่พบเลขพนักงาน',
                                    text: 'กรุณาตรวจสอบเลขพนักงานอีกครั้ง'
                                });
                            }
                        } catch (e) {
                            console.error('JSON parsing error:', e); // แสดง error ถ้า JSON ผิดพลาด
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถประมวลผลข้อมูลได้'
                            });
                        }
                    } else {
                        console.error('Error:', xhr.status, xhr.statusText);
                    }
                }
            };
            xhr.send('trackingId=' + encodeURIComponent(trackingId));
        });

    </script>
</body>

</html>
