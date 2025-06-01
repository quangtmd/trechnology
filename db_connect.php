<?php
$servername = "localhost";
$username = "root";
$password = ""; // Mật khẩu MySQL mặc định trên XAMPP thường để trống
$dbname = "it_service_db";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
