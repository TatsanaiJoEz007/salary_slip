<?php
header('Content-Type: application/json');
require_once('../../config/connect.php'); // ตรวจสอบเส้นทางให้ถูกต้อง

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $missing_fields = [];

    // Check required fields from the form
    $required_fields = ['admin_firstname', 'admin_lastname', 'admin_email', 'admin_pass'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }

    // Set default value for admin_status if not provided
    $status = isset($_POST['admin_status']) ? intval($_POST['admin_status']) : 1;

    // Check if any fields are missing
    if (!empty($missing_fields)) {
        $response['success'] = false;
        $response['message'] = 'Required fields are missing: ' . implode(', ', $missing_fields);
    } else {
        // Sanitize and assign the input values
        $firstname = trim($_POST['admin_firstname']);
        $lastname = trim($_POST['admin_lastname']);
        $email = trim($_POST['admin_email']);
        $password = $_POST['admin_pass'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['success'] = false;
            $response['message'] = 'Invalid email format.';
        } else {
            // Check if email is already in use
            $email_check_stmt = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
            if ($email_check_stmt) {
                $email_check_stmt->bind_param("s", $email);
                $email_check_stmt->execute();
                $email_check_stmt->store_result();

                if ($email_check_stmt->num_rows > 0) {
                    $response['success'] = false;
                    $response['message'] = 'Email is already in use.';
                } else {
                    // แฮชรหัสผ่านด้วย password_hash
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Set user_type as 999
                    $user_type = 999;

                    // Prepare the SQL statement to insert the new admin
                    $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_type, user_create_at, user_status) VALUES (?, ?, ?, ?, ?, NOW(), ?)");

                    if ($stmt) {
                        $stmt->bind_param("ssssii", $firstname, $lastname, $email, $hashed_password, $user_type, $status);

                        if ($stmt->execute()) {
                            // ลบการเก็บล็อกกิจกรรมออก
                            /*
                            // Log the activity
                            $admin_user_id = $_SESSION['user_id']; // Assuming admin user_id is stored in session
                            $action = 'add user';
                            $entity = 'user';
                            $entity_id = $conn->insert_id; // Get the last inserted ID
                            $additional_info = "Added user with email: " . $email;
                            logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);
                            */

                            $response['success'] = true;
                            $response['message'] = 'Registration successful.';
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Error: ' . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Prepare failed: ' . $conn->error;
                    }
                }
                $email_check_stmt->close();
            } else {
                $response['success'] = false;
                $response['message'] = 'Prepare failed: ' . $conn->error;
            }
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>
