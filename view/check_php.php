<?php
// ฟังก์ชันที่ต้องการเช็ค
$functions_to_check = [
    'curl_init',
    'file_get_contents',
    'shell_exec',
    'exec',
    'mail',
    'mysqli_connect',
    'json_encode',
    'xml_parser_create'
];

// แสดงฟังก์ชันที่ถูกเปิดหรือปิดอยู่
echo "<h2>Function Availability Check</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Function</th><th>Status</th></tr>";

foreach ($functions_to_check as $function) {
    echo "<tr>";
    echo "<td>" . $function . "</td>";
    if (function_exists($function)) {
        echo "<td style='color: green;'>Enabled</td>";
    } else {
        echo "<td style='color: red;'>Disabled</td>";
    }
    echo "</tr>";
}

echo "</table>";

// เช็คการตั้งค่า disable_functions ใน php.ini
$disabled_functions = ini_get('disable_functions');
echo "<h2>Disabled Functions (จาก php.ini)</h2>";
if (!empty($disabled_functions)) {
    echo "<p style='color: red;'>ฟังก์ชันที่ถูกปิด: " . $disabled_functions . "</p>";
} else {
    echo "<p style='color: green;'>ไม่มีฟังก์ชันที่ถูกปิดผ่าน php.ini</p>";
}
?>
