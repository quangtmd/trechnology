<?php
$host = 'localhost';
$dbname = 'it_service_db';
$username = 'root';
$password = ''; // Để trống nếu bạn dùng XAMPP và chưa đặt pass

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Kết nối thành công!";
} catch (PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}
?>
