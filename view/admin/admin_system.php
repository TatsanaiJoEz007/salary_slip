    <!DOCTYPE html>
    <html lang="en">
    <?php
        require_once('../config/connect.php');

        // Start session only if it hasn't started yet
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $admins = [];

        $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_status FROM tb_user WHERE user_type = 999";
        if ($stmt = $conn->prepare($sql)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $admins[] = $row;
                    }
                }
            } else {
                echo "เกิดข้อผิดพลาดในการดำเนินการคำสั่ง SQL: " . $stmt->error;
                exit;
            }
            $stmt->close();
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
            exit;
        }
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin System</title>
        <!-- Add SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Add Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <!-- Add Bootstrap CSS for responsiveness -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Add custom styles -->
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="../assets/img/logo/logo.png" type="image/x-icon">
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
                max-width: 5000px;
                width: 1000px;
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
        <img src="../assets/img/logo/logo.png" alt="logo" style="padding-left:8px; padding-right:10px;" />
            <h4>Salary Slip System</h4>
            <a href="upload_page.php">อัปโหลดไฟล์ PDF</a>
            <a href="file_page.php">ไฟล์ PDF</a>
            <a href="admin_system.php">ตารางข้อมูลผู้ดูแลระบบ</a>
            <div class="user-info">
                <p>ชื่อผู้ใช้: admin admin</p>
                <a class="logout-btn" onclick="logout()">ออกจากระบบ <i class="fas fa-sign-out-alt"></i></a> <!-- updated logout link -->
            </div>
        </div>
        <div class="content" id="content">
            <div class="content-card">
                <div class="container">
                    <h1 class="heading">ตารางข้อมูลผู้ดูแลระบบ</h1>
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            เพิ่มผู้ดูแลระบบ
                        </button>
                        <button type="button" class="btn btn-danger" id="deleteSelected">
                            ลบผู้ดูแลระบบที่เลือก
                        </button>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-striped" id="Tableall">
                            <thead>
                                <tr>
                                    <th scope="col" style="text-align: center;"><input type="checkbox" id="selectAll"></th>
                                    <th scope="col" style="text-align: center;">#</th>
                                    <th scope="col" style="text-align: center;">ชื่อ</th>
                                    <th scope="col" style="text-align: center;">นามสกุล</th>
                                    <th scope="col" style="text-align: center;">อีเมล์</th>    
                                    <th scope="col" style="text-align: center;">สถานะ</th> 
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                if (!empty($admins)) {
                                    $counter = 1;
                                    foreach ($admins as $admin) {
                                        $status_text = ($admin['user_status'] == 1) ? 'อยู่ในระบบ' : 'ไม่อยู่ในระบบ';
                                        $firstname = htmlspecialchars($admin['user_firstname']);
                                        $lastname = htmlspecialchars($admin['user_lastname']);
                                        $email = htmlspecialchars($admin['user_email']);
                                        
                                        echo "<tr>";
                                        echo "<td><input type='checkbox' class='userCheckbox' data-user-id='" . htmlspecialchars($admin['user_id']) . "'></td>";
                                        echo "<td>" . $counter++ . "</td>";
                                        echo "<td class='align-middle'>" . $firstname . "</td>";
                                        echo "<td class='align-middle'>" . $lastname . "</td>";
                                        echo "<td class='align-middle'>" . $email . "</td>";
                                        echo "<td class='align-middle'>" . $status_text . "</td>";

                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>ไม่มีข้อมูลผู้ดูแลระบบ</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Adding Admin -->
        <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModalLabel">เพิ่มผู้ดูแลระบบ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addAdminForm">
                            <div class="mb-3">
                                <label for="admin_firstname" class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" id="admin_firstname" name="admin_firstname" required>
                            </div>
                            <div class="mb-3">
                                <label for="admin_lastname" class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" id="admin_lastname" name="admin_lastname" required>
                            </div>
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">อีเมล</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="admin_pass" class="form-label">รหัสผ่าน</label>
                                <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-success" id="saveAdminBtn">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Delete Selected Admin
                const deleteSelected = document.getElementById('deleteSelected');
                if (deleteSelected) {
                    deleteSelected.addEventListener('click', function () {
                        let selectedUsers = [];
                        document.querySelectorAll('.userCheckbox:checked').forEach(function (checkbox) {
                            selectedUsers.push(checkbox.dataset.userId);
                        });

                        if (selectedUsers.length === 0) {
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: 'กรุณาเลือกผู้ใช้ที่ต้องการลบ',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'คุณแน่ใจหรือไม่?',
                            text: "คุณต้องการลบผู้ใช้ที่เลือกทั้งหมดใช่หรือไม่!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'ใช่, ลบเลย!',
                            cancelButtonText: 'ยกเลิก'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'function/delete_admin.php',
                                    type: 'post',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        user_ids: selectedUsers
                                    }),
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire({
                                                position: 'center',
                                                icon: 'success',
                                                title: response.message,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            setTimeout(function () {
                                                location.reload();
                                            }, 1500);
                                        } else {
                                            Swal.fire({
                                                position: 'center',
                                                icon: 'error',
                                                title: response.message,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                        }
                                    },
                                    error: function () {
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                });
                            }
                        });
                    });
                }

                // Add Admin
                const saveAdminBtn = document.getElementById('saveAdminBtn');
                if (saveAdminBtn) {
                    saveAdminBtn.addEventListener('click', function () {
                        const formData = new FormData(document.getElementById('addAdminForm'));

                        $.ajax({
                            url: 'function/add_admin.php',
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            data: formData,
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'เพิ่มผู้ดูแลระบบเรียบร้อยแล้ว',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        });
                    });
                }
            });

            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('active');
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
                        window.location.href = '../login';
                    }
                });
            }


        </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
