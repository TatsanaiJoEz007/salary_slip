<?php
header('Content-Type: application/json');
require_once('../config/connect.php'); // ตรวจสอบเส้นทางให้ถูกต้อง

session_start();

// รับข้อมูลจาก JSON ที่ส่งมา
$input = json_decode(file_get_contents('php://input'), true);

$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

// ตรวจสอบว่ามีการส่งค่าที่จำเป็นหรือไม่
if (!isset($input['login']) || $input['login'] != 1) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

if (!isset($input['user_email']) || !isset($input['user_pass'])) {
    $response['message'] = 'Please provide both email and password.';
    echo json_encode($response);
    exit;
}

$user_email = trim($input['user_email']);
$user_pass = $input['user_pass'];
$remember = isset($input['remember']) ? $input['remember'] : false;

// Validate email format
if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format.';
    echo json_encode($response);
    exit;
}

// Prepare SQL to fetch user by email
$stmt = $conn->prepare("SELECT user_id, user_firstname, user_lastname, user_email, user_pass, user_status, user_type FROM tb_user WHERE user_email = ? LIMIT 1");
if (!$stmt) {
    $response['message'] = 'Database error: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $user_firstname, $user_lastname, $user_email_db, $hashed_password, $user_status, $user_type);
    $stmt->fetch();

    // ตรวจสอบรหัสผ่าน
    if (password_verify($user_pass, $hashed_password)) {
        if ($user_status != 1) {
            $response['message'] = 'บัญชีนี้ถูกระงับการใช้งาน.';
            echo json_encode($response);
            exit;
        }

        // ตั้งค่าตัวแปรเซสชัน
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_firstname'] = $user_firstname;
        $_SESSION['user_lastname'] = $user_lastname;
        $_SESSION['user_email'] = $user_email_db;
        $_SESSION['user_type'] = $user_type;

        // ถ้า Remember Me ถูกเลือก ให้ตั้งค่า Cookie (ตัวอย่าง: 30 วัน)
        if ($remember) {
            setcookie('username', $user_email, time() + (86400 * 30), "/"); // 86400 = 1 day
        } else {
            setcookie('username', '', time() - 3600, "/"); // ลบ cookie
        }

        // กำหนดเส้นทางการเปลี่ยนเส้นทางตามประเภทผู้ใช้
        if ($user_type == 999) { // admin
            $response['redirect'] = 'admin/upload_page';
            $response['success'] = true;
            $response['message'] = 'เข้าสู่ระบบสำเร็จ!!';
        } else {
            // ถ้าคุณมีประเภทผู้ใช้อื่น ๆ ให้กำหนดเส้นทางที่เหมาะสม
            $response['message'] = 'สิทธิ์การใช้งานไม่ถูกต้อง';
        }
    } else {
        // รหัสผ่านไม่ถูกต้อง
        $response['message'] = 'รหัสผ่านไม่ถูกต้อง!!';
    }
} else {
    // ไม่พบผู้ใช้
    $response['message'] = 'ไม่มีบัญชีนี้ในระบบ!!';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
