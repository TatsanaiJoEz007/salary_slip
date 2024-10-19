<!DOCTYPE html>
<html lang="th">
<?php
    require_once('../config/connect.php');
    session_start();
    

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // ตรวจสอบสิทธิ์การเข้าถึง
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 999) {
        header('Location: ../error404');
        exit();
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

    $userId = $_SESSION['user_id'];
    $myprofile = fetchUserProfile($conn, $userId);

    // ดึงข้อมูลไฟล์ PDF ทั้งหมดจากฐานข้อมูล
    $pdfFiles = [];
    $sql = "SELECT pdf_id, original_pdf_name, uploaded_at FROM tb_pdf_files";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdfFiles[] = $row;
        }
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Page</title>
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo/logo.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
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
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
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
            margin-left: 250px;
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

        .file-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .file-card-time {
            font-size: 14px;
            color: #666;
        }

        .file-card-actions {
            display: flex;
            gap: 10px;
        }

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
    <img src="../assets/img/logo/logo.png" alt="logo" style="padding-left:8px; padding-right:10px;" />
        <h4>Salary Slip System</h4>
        <a href="upload_page">อัปโหลดไฟล์ PDF</a>
        <a href="file_page">ไฟล์ PDF</a>
        <a href="admin_system">ตารางข้อมูลผู้ดูแลระบบ</a>
        <div class="user-info">
            <p>ชื่อผู้ใช้: <?php echo $myprofile['user_firstname'] ?>&nbsp; <?php echo $myprofile['user_lastname'] ?></p>
            <a class="logout-btn" onclick="logout()">ออกจากระบบ <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>


    <div class="content" id="content">
        <div class="container">
            <div class="row g-4 settings-section">
                <div class="col-12 col-md-12">
                    <h5 class="app-page-title">ไฟล์ PDF ในระบบ</h5>
                    <div class="app-card app-card-settings shadow-sm p-4">
                        <div class="app-card-body">
                            <?php if (!empty($pdfFiles)): ?>
                                <?php foreach ($pdfFiles as $file): ?>
                                    <div class="file-card">
                                        <div>
                                            <div class="file-card-title">ชื่อไฟล์: <?= htmlspecialchars($file['original_pdf_name']) ?></div>
                                            <div class="file-card-time">เวลาอัปโหลด: <?= $file['uploaded_at'] ?></div>
                                        </div>
                                        <div class="file-card-actions">
                                            <a href="function/download_pdf.php?id=<?= $file['pdf_id'] ?>" class="btn btn-primary btn-sm">ดาวน์โหลดไฟล์</a>
                                            <button class="btn btn-danger btn-sm" onclick="deletePDF(<?= $file['pdf_id'] ?>)">ลบไฟล์</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>ไม่มีไฟล์ PDF ที่อัปโหลด</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
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

        function deletePDF(pdf_id) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบไฟล์นี้ใช่หรือไม่!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งคำร้องขอไปยัง delete_pdf.php
                    fetch('function/delete_pdf.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `pdf_id=${pdf_id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('ลบไฟล์สำเร็จ', data.message, 'success').then(() => {
                                location.reload(); // รีเฟรชหน้าหลังจากลบไฟล์สำเร็จ
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    });
                }
            });
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
                        window.location.href = 'function/action_logout.php';
                    }
                });
            }
    </script>
</body>
</html>
