<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../config/connect.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    function fetchUserProfile($conn, $userId) {
        $stmt = $conn->prepare("SELECT user_firstname, user_lastname FROM tb_user WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return [
                'user_firstname' => 'Guest',
                'user_lastname' => ''
            ];
        }
    }
    
    // ตรวจสอบสถานะและข้อความจากการอัปโหลด PDF
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $message = isset($_GET['message']) ? $_GET['message'] : '';


    $userId = $_SESSION['user_id'];
    $myprofile = fetchUserProfile($conn, $userId);
    
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF</title>
    <!-- Add SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Add Bootstrap CSS for responsiveness -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Kanit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add custom styles -->
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            font : 18px ;
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

        .sidebar h2, .sidebar h4 {
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
            font-size: 18px;
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
            display: flex;
            justify-content: center;
            align-items: center;
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
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
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
        <img src="../assets/img/logo/logo.png" alt="logo of wehome" style="padding-left:8px; padding-right:10px;" />
        <h4>Salary Slip System</h4>
        <a href="upload_page.php">อัปโหลดไฟล์ PDF</a>
        <a href="file_page.php">ไฟล์ PDF</a>
        <a href="admin_system.php">ตารางข้อมูลผู้ดูแลระบบ</a>
        <div class="user-info">
            <p>ชื่อผู้ใช้: <?php echo $myprofile['user_firstname'] . ' ' . $myprofile['user_lastname']; ?></p>
            <a class="logout-btn" onclick="logout()">ออกจากระบบ <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="content" id="content">
        <div class="container">
            <h1 class="heading">Upload PDF (.pdf)</h1>
            <div class="section">
                <h3 class="sub-heading">Upload your PDF File</h3>
                <div class="buttons">
                    <form action="function/upload_pdf.php" method="post" enctype="multipart/form-data">
                        <label for="pdfFileInput" class="btn-custom btn-upload">
                            <i class="fas fa-file-upload"></i>
                            <span id="pdfFileInputText">&nbsp;Choose PDF File</span>
                            <input type="file" id="pdfFileInput" name="pdfFile" class="file-input" accept=".pdf">
                        </label>
                        <button type="submit" class="btn-custom" id="pdfImportButton" disabled>
                            <i class="fas fa-database"></i>
                            <span>&nbsp;Import to Database</span>
                        </button>
                    </form>
                </div>
                <div class="file-info" id="pdfFileInfo"></div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const navbar = document.getElementById('navbar');
            sidebar.classList.toggle('active');
            content.classList.toggle('with-shadow');
            navbar.style.display = sidebar.classList.contains('active') ? 'none' : 'flex';
        }

        function logout() {
            Swal.fire({
                title: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ออกจากระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../login.php';
                }
            });
        }

        const pdfFileInput = document.getElementById('pdfFileInput');
        const pdfFileInfo = document.getElementById('pdfFileInfo');
        const pdfImportButton = document.getElementById('pdfImportButton');

        pdfFileInput.addEventListener('change', function () {
            const file = pdfFileInput.files[0];
            if (file) {
                pdfFileInfo.innerHTML = `<i class="fas fa-file-pdf"></i> ${file.name}`;
                pdfImportButton.disabled = false;
            } else {
                pdfFileInfo.innerHTML = '';
                pdfImportButton.disabled = true;
            }
        });

        // แสดงการแจ้งเตือนด้วย SweetAlert2 ตามสถานะ
        const status = "<?php echo $status; ?>";
        const message = "<?php echo $message; ?>";

        if (status) {
            Swal.fire({
                icon: status === 'success' ? 'success' : 'error',
                title: status === 'success' ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                text: message
            });
        }
    </script>
</body>
</html>