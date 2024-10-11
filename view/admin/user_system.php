<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <style>
        /* ปรับแต่ง modal ให้อยู่ตรงกลางจอ */
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0 auto !important;
        }

        .modal {
            position: fixed;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: auto !important;
        }

        .modal-content {
            margin: auto !important;
        }

        .modal-backdrop.show {
            position: fixed;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        /* Container Styling */
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


    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
            <h1 class="app-page-title">ตารางข้อมูลผู้ดูแลระบบ - </h1>
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- ปุ่มเพิ่มผู้ดูแลระบบ -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            เพิ่มผู้ดูแลระบบ
                        </button>

                        <!-- ปุ่มลบผู้ใช้ที่เลือก -->
                        <button type="button" class="btn btn-danger">
                            ลบผู้ใช้ที่เลือก
                        </button>

                        <!-- Modal เพิ่มข้อมูลผู้ใช้ -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูล</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="register" method="post" enctype="multipart/form-data">
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
                                            <div class="mb-3">
                                                <label for="admin_img" class="form-label">รูปภาพ</label>
                                                <input type="file" class="form-control" id="admin_img" name="admin_img">
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_status" class="form-label">สถานะ</label>
                                                <select class="form-select" id="admin_status" name="admin_status" required>
                                                    <option value="1">อยู่ในระบบ</option>
                                                    <option value="0">ไม่อยู่ในระบบ</option>
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" form="register" class="btn btn-primary">บันทึกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ตารางผู้ดูแลระบบ -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;"></th>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">ชื่อ</th>
                                        <th scope="col" style="text-align: center;">นามสกุล</th>
                                        <th scope="col" style="text-align: center;">อีเมล</th>
                                        <th scope="col" style="text-align: center;">รหัสผ่าน</th>
                                        <th scope="col" style="text-align: center;">สถานะ</th>
                                        <th scope="col" style="text-align: center;">เมนู</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td><input type="checkbox" class="userCheckbox"></td>
                                        <td>1</td>
                                        <td class="align-middle">เอก</td>
                                        <td class="align-middle">ใจดี</td>
                                        <td class="align-middle">ake@example.com</td>
                                        <td class="align-middle">****</td>
                                        <td class="align-middle">อยู่ในระบบ</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="userCheckbox"></td>
                                        <td>2</td>
                                        <td class="align-middle">บอย</td>
                                        <td class="align-middle">รักดี</td>
                                        <td class="align-middle">boy@example.com</td>
                                        <td class="align-middle">****</td>
                                        <td class="align-middle">อยู่ในระบบ</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="userCheckbox"></td>
                                        <td>3</td>
                                        <td class="align-middle">แดง</td>
                                        <td class="align-middle">มณี</td>
                                        <td class="align-middle">dang@example.com</td>
                                        <td class="align-middle">****</td>
                                        <td class="align-middle">ไม่อยู่ในระบบ</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="userCheckbox"></td>
                                        <td>4</td>
                                        <td class="align-middle">หนึ่ง</td>
                                        <td class="align-middle">เจริญ</td>
                                        <td class="align-middle">nueng@example.com</td>
                                        <td class="align-middle">****</td>
                                        <td class="align-middle">อยู่ในระบบ</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="userCheckbox"></td>
                                        <td>5</td>
                                        <td class="align-middle">ต้น</td>
                                        <td class="align-middle">สมบูรณ์</td>
                                        <td class="align-middle">ton@example.com</td>
                                        <td class="align-middle">****</td>
                                        <td class="align-middle">อยู่ในระบบ</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <!-- เพิ่มข้อมูลอื่น ๆ ได้ตามต้องการ -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
