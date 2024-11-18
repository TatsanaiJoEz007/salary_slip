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

function fetchUserProfile($conn, $userId)
{
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

// ดึงข้อมูลจากตาราง tb_payslips และกรองข้อมูลที่ไม่มีคำว่า 'unknown'
$payslips = [];
$sql = "SELECT emp_id, emp_name, emp_dep, salary_income, salary_deduction, salary_net, slip_divide FROM tb_payslips WHERE 
        emp_id NOT LIKE '%unknown%' AND
        emp_name NOT LIKE '%unknown%' AND
        emp_dep NOT LIKE '%unknown%' AND
        salary_income NOT LIKE '%unknown%' AND
        salary_deduction NOT LIKE '%unknown%' AND
        salary_net NOT LIKE '%unknown%' AND
        slip_divide NOT LIKE '%unknown%'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payslips[] = $row;
    }
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Slip Page</title>
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

        .sidebar h2,
        .sidebar h4 {
            margin-top: 0;
        }

        .sidebar a,
        .logout-btn {
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

        .sidebar a:hover,
        .logout-btn:hover {
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

            .content {
                margin-left: 0;
            }
        }

        @media (min-width: 769px) {
            .navbar {
                display: none;
            }
        }

        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
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

        /* ปรับปรุงตารางให้ responsive */
        table {
            width: 100%;
            table-layout: auto;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
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
        <a href="personalslip_page">ข้อมูลใบจ่ายเงินเดือน</a>
        <a href="admin_system">ตารางข้อมูลผู้ดูแลระบบ</a>
        <div class="user-info">
            <p>ชื่อผู้ใช้: <?php echo $myprofile['user_firstname'] ?>&nbsp; <?php echo $myprofile['user_lastname'] ?></p>
            <a class="logout-btn" onclick="logout()">ออกจากระบบ <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="content" id="content">
        <div class="container-fluid">
            <div class="row g-4 settings-section">
                <div class="col-12">
                    <h5 class="app-page-title">รายการข้อมูลใบจ่ายเงินเดือน</h5>
                    <div class="app-card app-card-settings shadow-sm p-4">
                        <div class="app-card-body">
                            <form id="payslipsForm" method="post" action="">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>รหัสพนักงาน</th>
                                                <th>ชื่อพนักงาน</th>
                                                <th>แผนก</th>
                                                <th>รายได้</th>
                                                <th>หัก</th>
                                                <th>สุทธิ</th>
                                                <th>ดาวน์โหลดสลิป</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($payslips)): ?>
                                                <?php foreach ($payslips as $payslip): ?>
                                                    <tr>
                                                        <td><input type="checkbox" name="selected_ids[]" value="<?= htmlspecialchars($payslip['emp_id']) ?>"></td>
                                                        <td><?= htmlspecialchars($payslip['emp_id']) ?></td>
                                                        <td><?= htmlspecialchars($payslip['emp_name']) ?></td>
                                                        <td><?= htmlspecialchars($payslip['emp_dep']) ?></td>
                                                        <td><?= htmlspecialchars($payslip['salary_income']) ?></td>
                                                        <td><?= htmlspecialchars($payslip['salary_deduction']) ?></td>
                                                        <td><?= htmlspecialchars($payslip['salary_net']) ?></td>
                                                        <td>
                                                            <a href="function/download_payslip.php?emp_id=<?= urlencode($payslip['emp_id']) ?>" class="btn btn-primary btn-sm">ดาวน์โหลด</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="8">ไม่มีข้อมูล</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-danger" onclick="deleteSelected()">ลบข้อมูลที่เลือก</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- รวมสคริปต์ต่างๆ -->
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

        // Select/Deselect all checkboxes
        document.getElementById('selectAll').onclick = function () {
            var checkboxes = document.getElementsByName('selected_ids[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        function deleteSelected() {
            var form = document.getElementById('payslipsForm');
            var selected = document.querySelectorAll('input[name="selected_ids[]"]:checked');
            if (selected.length > 0) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "คุณต้องการลบข้อมูลที่เลือกใช่หรือไม่!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่งคำร้องขอไปยัง delete_payslips.php
                        var formData = new FormData(form);
                        fetch('function/delete_payslips.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('ลบข้อมูลสำเร็จ', data.message, 'success').then(() => {
                                    location.reload(); // รีเฟรชหน้าหลังจากลบข้อมูลสำเร็จ
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
            } else {
                Swal.fire('กรุณาเลือกข้อมูล', 'กรุณาเลือกข้อมูลที่ต้องการลบ', 'warning');
            }
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
