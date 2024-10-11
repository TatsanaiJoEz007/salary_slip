<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../config/connect.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
<head>
    <meta charset="UTF-8">
    <title>Upload sss</title>
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Add Bootstrap CSS for responsiveness -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh; /* Full height */
            display: flex;
            align-items: center; /* Center content vertically */
            justify-content: center; /* Center content horizontally */
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .heading {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }

        .sub-heading {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }

        .section {
            margin-bottom: 50px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #FF0000; /* เปลี่ยนสีเป็นสีแดง */
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block; /* จัดให้อยู่ในบรรทัดเดียวกัน */
            margin: 0 10px; /* เพิ่มระยะห่างระหว่างปุ่ม */
            font-size: 18px;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #cc0000; /* เปลี่ยนสีเมื่อ hover */
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .file-input {
            display: none;
        }

        .output-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            word-wrap: break-word;
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            max-height: 300px;
            overflow-y: auto;
        }

        .fa {
            margin-right: 8px;
        }

        .file-info {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }

        .file-info i {
            margin-right: 10px;
            font-size: 24px;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        .home-section {
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .instruction-box {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .instruction-box h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .instruction-box ol {
            padding-left: 20px;
        }

        .instruction-box li {
            margin-bottom: 10px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="heading">Upload Excel (.xlsx)</h1>
       
        <div class="instruction-box">
            <h2>วิธีการใช้งาน</h2>
            <ol>
                <li>กด <b>Choose Excel File</b> เพื่อเลือกไฟล์ Excel ที่ต้องการอัปโหลด</li>
                <li>กด <b>Import to Database</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
            </ol>
        </div>
        <div class="section">
            <h3 class="sub-heading">Upload and convert your Excel File</h3>
            <div class="buttons">
                <form action="function/upload_xlsx.php" method="post" enctype="multipart/form-data">
                    <label for="xlsxFileInput" class="btn-custom btn-upload">
                        <i class="fas fa-file-upload"></i>
                        <span id="fileInputText">&nbsp;Choose Excel File</span>
                        <input type="file" id="xlsxFileInput" name="xlsxFile" class="file-input" accept=".xlsx">
                    </label>
                    <button type="submit" class="btn-custom" id="importButton" disabled>
                        <i class="fas fa-database"></i>
                        <span>&nbsp;Import to Database</span>
                    </button>
                </form>
            </div>

            <!-- Display file name and icon -->
            <div class="file-info" id="fileInfo">
                <!-- This will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Add SweetAlert2 library -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
       // Handle file selection
       const fileInput = document.getElementById('xlsxFileInput');
       const fileInfo = document.getElementById('fileInfo');
       const importButton = document.getElementById('importButton');

       fileInput.addEventListener('change', function() {
           const file = fileInput.files[0];
           if (file) {
               // Display the file name and enable the Import button
               fileInfo.innerHTML = '<i class="fas fa-file-excel"></i>' + file.name;
               importButton.disabled = false;
           } else {
               // Clear file info and disable the Import button
               fileInfo.innerHTML = '';
               importButton.disabled = true;
           }
       });
   </script>

</body>

</html>
