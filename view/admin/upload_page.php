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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #800000;
            color: #fff;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            margin-top: 0;
        }

        .sidebar a, .logout-btn {
            display: block;
            padding: 10px;
            margin: 10px 0;
            text-decoration: none;
            color: #fff;
            background-color: #a50000;
            border: none;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
        }

        .sidebar a:hover, .logout-btn:hover {
            background-color: #ff5555;
        }

        .user-info {
            margin-top: auto;
            text-align: center;
        }

        .user-info p {
            margin: 0;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            box-sizing: border-box;
            transition: box-shadow 0.3s ease;
        }

        .content.with-shadow {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .navbar {
            display: flex;
            background-color: #800000;
            color: #fff;
            padding: 10px;
            box-sizing: border-box;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            align-items: center;
            justify-content: space-between;
        }

        .navbar .menu-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            .sidebar.active {
                transform: translateX(0);
            }

            .navbar {
                display: flex;
            }
        }

        @media (min-width: 769px) {
            .navbar {
                display: none;
            }
        }

        /* Custom styles for upload page */
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
            background-color: #FF0000;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0 10px;
            font-size: 18px;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #cc0000;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .file-input {
            display: none;
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
    </style>
</head>
<body>
    <div class="navbar" id="navbar">
        <button class="menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2>Sidebar - admin</h2>
    </div>
    <div class="sidebar" id="sidebar">
        <h2>Salary Slip System</h2>
        <a href="upload_page.php">อัปโหลดไฟล์ Excel</a>
        <a href="file_page.php">ไฟล์ Excel</a>
        <a href="admin_system.php">ตารางข้อมูลผู้ดูแลระบบ</a>
        <div class="user-info">
            <button class="logout-btn" onclick="logout()">ชื่อผู้ใช้: ดึงจาก session ที่ login เข้ามา <i class="fas fa-sign-out-alt"></i></button>
        </div>
    </div>
    <div class="content" id="content">
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
                <h3 class="sub-heading">Upload your Excel File</h3>
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
    </div>

    <div class="content" id="content">
        <div class="container">
            <h1 class="heading">Upload PDF (.pdf)</h1>
            <div class="instruction-box">
                <h2>วิธีการใช้งาน</h2>
                <ol>
                    <li>กด <b>Choose Excel File</b> เพื่อเลือกไฟล์ Excel ที่ต้องการอัปโหลด</li>
                    <li>กด <b>Import to Database</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
                </ol>
            </div>
            <div class="section">
                <h3 class="sub-heading">Upload your PDF File</h3>
                <div class="buttons">
                    <form action="function/upload_pdf.php" method="post" enctype="multipart/form-data">
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
    </div>
    


    <!-- Add SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const navbar = document.getElementById('navbar');
            sidebar.classList.toggle('active');
            content.classList.toggle('with-shadow');
            if (sidebar.classList.contains('active')) {
                navbar.style.display = 'none';
            } else {
                navbar.style.display = 'flex';
            }
        }

        document.getElementById('content').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const navbar = document.getElementById('navbar');
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                content.classList.remove('with-shadow');
                navbar.style.display = 'flex';
            }
        });

        function logout() {
            // Implement logout logic here
            alert('Logging out...');
        }

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