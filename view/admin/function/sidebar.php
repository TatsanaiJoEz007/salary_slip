<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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



    </div>

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
    </script>
</body> 
</html>