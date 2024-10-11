<?php
header('Content-Type: application/json');

// เริ่มต้น session หากยังไม่ได้เริ่ม
if (!isset($_SESSION)) {
    session_start();
}

require_once('../config/connect.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// รับข้อมูล JSON ที่ถูกส่งมา
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามีการส่งข้อมูล login มาหรือไม่
if (isset($data['login'])) {
    $user_email = $data['user_email'];
    $user_pass = $data['user_pass']; // รับรหัสผ่านที่ยังไม่ถูกเข้ารหัส
    $remember = isset($data['remember']) ? $data['remember'] : false;

    // ตรวจสอบข้อมูลที่ได้รับ
    if (empty($user_email) || empty($user_pass)) {
        echo json_encode('invalid_input');
        exit;
    }

    // ค้นหาผู้ใช้ในฐานข้อมูล
    $check = "SELECT * FROM tb_user WHERE user_email = ?";
    $check_user = $conn->prepare($check);
    $check_user->bind_param("s", $user_email);
    $check_user->execute();
    $result = $check_user->get_result();

    // ตรวจสอบว่าพบผู้ใช้หรือไม่
    if ($result->num_rows >= 1) {
        $user = $result->fetch_array();

        // ตรวจสอบรหัสผ่านด้วย password_verify
        if (password_verify($user_pass, $user['user_pass'])) {
            if ($user['user_status'] != 0) {
                // ตั้งค่า session
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['employee_id'] = $user['employee_id'];  // Update for employee_id
                $_SESSION['user_firstname'] = $user['user_firstname'];
                $_SESSION['user_lastname'] = $user['user_lastname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_create_at'] = $user['user_create_at'];

                $user_type = $user['user_type'];

                // จัดการ "Remember Me" ด้วยการใช้โทเค็นแทนการเก็บรหัสผ่าน
                if ($remember) {
                    // สร้างโทเค็นแบบสุ่ม
                    $token = bin2hex(random_bytes(16));
                    $expires_at = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 วัน

                    // เก็บโทเค็นในฐานข้อมูล
                    $insert_token = "INSERT INTO tb_remember_me (user_id, token, expires_at) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($insert_token);
                    $stmt->bind_param("iss", $user['user_id'], $token, $expires_at);
                    $stmt->execute();

                    // ตั้งค่าโคเคชสำหรับโทเค็น
                    setcookie('remember_me', $token, time() + (86400 * 30), "/"); // 30 วัน
                } else {
                    setcookie('remember_me', '', time() - 3600, "/");
                }

                // ส่งคืนประเภทผู้ใช้ (เฉพาะ admin)
                if ($user_type == 999) {
                    $_SESSION['user_type'] = 'admin';
                    echo json_encode('admin');
                } else {
                    echo json_encode('invalid_user_type');
                }
            } else {
                echo json_encode('close');
            }
        } else {
            echo json_encode('failpass');
        }
    } else {
        echo json_encode('failuser');
    }
} else {
    error_log("Login key not detected");
    echo json_encode('no_post');
}
?>
