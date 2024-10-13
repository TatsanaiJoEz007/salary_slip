<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../config/connect.php');

global $conn;

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = isset($_POST['trackingId']) ? trim($_POST['trackingId']) : '';

    if (empty($emp_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Empty Employee ID']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM tb_payslips WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'match', 'emp_id' => $emp_id]);
    } else {
        echo json_encode(['status' => 'no_match']);
    }

    $stmt->close();
}

$conn->close();
?>
