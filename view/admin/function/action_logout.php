<?php
session_start();
session_unset();
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้าเข้าสู่ระบบหรือหน้าแรก
header('Location: ../../login');
exit();
?>
