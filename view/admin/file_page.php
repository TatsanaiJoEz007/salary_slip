<!DOCTYPE html>
<html lang="th">

<head>
    <title>File Exal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <style>
        /* ปรับแต่ง card */
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
            gap: 10px; /* ระยะห่างระหว่างปุ่ม */
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

    <hr class="container">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <h5 class="app-page-title">ตารางไฟล์ Excel</h5>
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">

                        <!-- ส่วนแสดงไฟล์ในรูปแบบ card -->
                        <div class="file-card">
                            <div>
                                <div class="file-card-title">ชื่อไฟล์: example.xlsx</div>
                                <div class="file-card-time">เวลาอัปโหลด: 2024-10-4 14:30</div>
                            </div>
                            <div class="file-card-actions">
                                <button class="btn btn-danger btn-sm">ลบไฟล์</button>
                                <button class="btn btn-primary btn-sm">ดาวน์โหลดไฟล์</button>
                            </div>
                        </div>

                        <!-- ส่วนแสดงไฟล์ที่เพิ่มเข้ามาอีก (สามารถเพิ่มจากฐานข้อมูลได้ในอนาคต) -->
                        <div class="file-card">
                            <div>
                                <div class="file-card-title">ชื่อไฟล์: data.xlsx</div>
                                <div class="file-card-time">เวลาอัปโหลด: 2024-10-03 10:45</div>
                            </div>
                            <div class="file-card-actions">
                                <button class="btn btn-danger btn-sm">ลบไฟล์</button>
                                <button class="btn btn-primary btn-sm">ดาวน์โหลดไฟล์</button>
                            </div>
                        </div>

                        <!-- เพิ่ม card ตามต้องการในนี้ -->
                        <div class="file-card">
                            <div>
                                <div class="file-card-title">ชื่อไฟล์: data.xlsx</div>
                                <div class="file-card-time">เวลาอัปโหลด: 2024-10-02 16:45</div>
                            </div>
                            <div class="file-card-actions">
                                <button class="btn btn-danger btn-sm">ลบไฟล์</button>
                                <button class="btn btn-primary btn-sm">ดาวน์โหลดไฟล์</button>
                            </div>
                        </div>

                        <!-- เพิ่ม card ตามต้องการในนี้ -->
                        <div class="file-card">
                            <div>
                                <div class="file-card-title">ชื่อไฟล์: data.xlsx</div>
                                <div class="file-card-time">เวลาอัปโหลด: 2024-10-01 11:25</div>
                            </div>
                            <div class="file-card-actions">
                                <button class="btn btn-danger btn-sm">ลบไฟล์</button>
                                <button class="btn btn-primary btn-sm">ดาวน์โหลดไฟล์</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
