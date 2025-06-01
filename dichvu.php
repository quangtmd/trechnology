<?php
// Bắt đầu session nếu cần
// session_start();

// KẾT NỐI DATABASE (giống như index.php)
$host = 'localhost';
$db = 'it_service_db';
$user = 'root';    // Thường mặc định XAMPP là root
$pass = '';        // Mặc định XAMPP password rỗng

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Lấy tất cả dữ liệu dịch vụ từ bảng 'services'
// Đã sửa: Chỉ truy vấn các cột hiện có: id, name, description, icon
$sql_all_services = "SELECT id, name AS title, description, icon FROM services ORDER BY name ASC";
$result_all_services = $conn->query($sql_all_services);

// Lấy dữ liệu gói dịch vụ (nếu bạn muốn hiển thị lại phần này trên trang dịch vụ)
// Giả định dữ liệu gói dịch vụ nếu không có bảng 'packages' trong DB
$packages_data = [
    [
        'id' => 1,
        'name' => 'Gói Tiêu Chuẩn',
        'type' => 'basic', // Loại gói: basic, pro, enterprise
        'price_text' => 'Liên hệ',
        'duration' => '/Tháng',
        'description' => [
            ['text' => 'Hỗ trợ tỉ mỉ 6x5', 'included' => true],
            ['text' => 'Kiểm tra định kì hệ thống', 'included' => true],
            ['text' => 'Tư vấn nâng cấp phần cứng', 'included' => true],
            ['text' => 'Quản lý máy chủ 3 Endpoint', 'included' => false],
            ['text' => 'Không giới hạn số lượng', 'included' => false],
            ['text' => 'Dịch vụ ưu tiên', 'included' => false],
            ['text' => 'Tư vấn chiến lược IT', 'included' => false],
        ],
        'button_text' => 'Yêu cầu báo giá',
    ],
    [
        'id' => 2,
        'name' => 'Gói Chuyên Nghiệp',
        'type' => 'popular', // Đánh dấu gói phổ biến
        'price_text' => 'Liên hệ',
        'duration' => '/Tháng',
        'description' => [
            ['text' => 'Hỗ trợ 24/7 (SLA)', 'included' => true],
            ['text' => 'Bảo trì định kỳ toàn bộ hệ thống', 'included' => true],
            ['text' => 'Quản lý máy chủ 5 Endpoint', 'included' => true],
            ['text' => 'Không giới hạn số lượng thiết bị', 'included' => true],
            ['text' => 'Dịch vụ ưu tiên', 'included' => false],
            ['text' => 'Tư vấn chiến lược IT', 'included' => false],
        ],
        'button_text' => 'Yêu cầu báo giá',
    ],
    [
        'id' => 3,
        'name' => 'Gói Doanh Nghiệp',
        'type' => 'enterprise',
        'price_text' => 'Liên hệ',
        'duration' => '/Tháng',
        'description' => [
            ['text' => 'Toàn bộ gói Chuyên Nghiệp', 'included' => true],
            ['text' => 'Dịch vụ ưu tiên nâng cao', 'included' => true],
            ['text' => 'Tư vấn chiến lược IT & chuyển đổi số', 'included' => true],
            ['text' => 'Không giới hạn số lượng', 'included' => true],
            ['text' => 'Hỗ trợ 24/7 (SLA)', 'included' => true],
            ['text' => 'Bảo trì định kỳ toàn bộ hệ thống', 'included' => true],
            ['text' => 'Quản lý máy chủ không giới hạn Endpoint', 'included' => true],
        ],
        'button_text' => 'Đăng ký tư vấn',
    ],
];
// ... (Nếu bạn có bảng 'packages' trong DB, bạn sẽ truy vấn ở đây) ...

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dịch vụ của IQ Technology</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Bao gồm toàn bộ CSS từ file index.php của bạn */
        /* Hoặc tốt hơn là tạo một file styles.css riêng và nhúng vào cả 2 file */

        /* Reset & cơ bản */
        * {margin:0; padding:0; box-sizing:border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f9f9f9; color:#333; line-height:1.6;}
        a {text-decoration:none; color:#e63946;}
        a:hover {text-decoration:underline;}
        img {max-width:100%; height:auto; display:block;}

        /* Header */
        header {background:#1d3557; color:#fff; padding:15px 30px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; position:sticky; top:0; z-index:1000;}
        .logo {font-size:1.5rem; font-weight:bold; cursor:pointer;}
        nav ul {list-style:none; display:flex; gap:20px; flex-wrap:wrap; align-items: center;}
        nav ul li {line-height:1;}
        nav ul li a {color:#fff; font-weight:600; padding: 5px 0;}
        .hotline {font-weight:600; margin-left:auto; margin-right: 15px; white-space: nowrap;}
        .btn-consult {background:#e63946; color:#fff; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; font-weight:700; white-space: nowrap;}
        .btn-consult:hover {background:#d62828;}

        /* Responsive Menu toggle */
        #menu-toggle {display:none;}
        .menu-icon {display:none; cursor:pointer; font-size:1.8rem; color:#fff; padding:5px;}
        @media (max-width:992px) { /* Adjusted breakpoint for better behavior */
            nav {order:3; width:100%;}
            nav ul {display:none; flex-direction:column; background:#1d3557; width:100%; padding:15px 0; margin-top:10px;}
            nav ul.show {display:flex;}
            nav ul li {width:100%; text-align:center; margin-bottom:10px;}
            .menu-icon {display:block; margin-left:15px;}
            .hotline {margin-left:0; margin-right: 0; margin-top:10px; width:100%; text-align:center;}
            .btn-consult { margin-top:10px; width:100%;}
            header {justify-content:space-between;}
        }
        @media (max-width:480px) {
            .logo {font-size: 1.3rem;}
            .hotline {font-size:0.9rem;}
        }

        /* Hero / Banner trên trang dịch vụ (tùy chọn) */
        .page-hero {
            position:relative;
            background:url('https://images.unsplash.com/photo-1549692520-cb9a36746401?auto=format&fit=crop&w=1470&q=80') center/cover no-repeat;
            min-height:250px;
            color:#fff;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:20px;
        }
        .page-hero h1 {font-size:clamp(2rem, 5vw, 2.8rem); margin-bottom:15px; text-shadow: 2px 2px 8px rgba(0,0,0,0.7);}
        .page-hero p {font-size:clamp(1rem, 3vw, 1.2rem); margin-bottom:25px; max-width:600px; text-shadow: 1px 1px 6px rgba(0,0,0,0.6);}


        /* Sections */
        section {padding:50px 20px; max-width:1200px; margin: auto; overflow:hidden;}
        h2.section-title {
            color:#1d3557; margin-bottom:30px; font-size:clamp(1.8rem, 4vw, 2.2rem);
            border-bottom: 3px solid #e63946; display:inline-block; padding-bottom:10px;
        }

        /* Dịch vụ chi tiết (nội dung chính của dichvu.php) */
        .service-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        .service-detail-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-detail-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .service-detail-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .service-detail-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .service-detail-content h3 {
            font-size: 1.6rem;
            color: #1d3557;
            margin-bottom: 15px;
        }
        .service-detail-content .icon {
            font-size: 2.2rem;
            color: #e63946;
            margin-bottom: 15px;
        }
        .service-detail-content p {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .service-detail-content .btn-learn-more {
            background: #e63946;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            margin-top: auto; /* Đẩy nút xuống dưới cùng */
            transition: background 0.3s;
            display: inline-block; /* Để text-align center hoạt động */
            text-align: center;
        }
        .service-detail-content .btn-learn-more:hover {
            background: #d62828;
        }
        .empty-message {
            padding: 20px; text-align: center; color: #777; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            grid-column: 1 / -1; /* Make message span all columns if in a grid */
        }
        /* PHẦN CSS MỚI CHO GÓI DỊCH VỤ DẠNG CARD (lặp lại từ index.php để đồng bộ) */
        .packages-section {
            background-color: #f1faee; /* Màu nền nhẹ cho phần gói dịch vụ */
            padding: 50px 20px;
            text-align: center;
        }

        .packages-section .section-title {
            margin-bottom: 40px;
        }

        .package-cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: auto;
        }

        .package-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 30px;
            text-align: center;
            flex: 1 1 300px; /* Cho phép card co giãn, không quá nhỏ hơn 300px */
            max-width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .package-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .package-card.popular {
            border: 3px solid #e63946; /* Border nổi bật cho gói phổ biến */
            box-shadow: 0 0 0 5px rgba(230, 57, 70, 0.2), 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: scale(1.03); /* Phóng to nhẹ gói phổ biến */
        }

        .package-card.popular:hover {
            transform: translateY(-8px) scale(1.03); /* Giữ hiệu ứng scale khi hover */
        }

        .package-card.popular .popular-label {
            position: absolute;
            top: 15px;
            right: -25px;
            background-color: #e63946;
            color: white;
            padding: 5px 30px;
            font-size: 0.85rem;
            font-weight: bold;
            transform: rotate(45deg);
            transform-origin: 100% 0%; /* Đặt tâm xoay ở góc trên bên phải */
            width: 150px;
            text-align: center;
        }


        .package-card h3 {
            font-size: 1.8rem;
            color: #1d3557;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .package-card .price {
            font-size: 1.2rem;
            color: #e63946;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .package-card .price span {
            font-size: 2.5rem;
            font-weight: 800;
            margin-right: 5px;
            line-height: 1; /* Đảm bảo dòng giá không bị cắt */
        }

        .package-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
            flex-grow: 1; /* Cho phép danh sách tính năng chiếm hết không gian trống */
            text-align: left;
        }

        .package-card ul li {
            margin-bottom: 15px;
            font-size: 1rem;
            color: #555;
            display: flex;
            align-items: center;
        }

        .package-card ul li i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: #2a9d8f; /* Màu xanh lá cho icon check */
        }

        .package-card ul li i.fa-times-circle {
            color: #e76f51; /* Màu đỏ cam cho icon X */
        }

        .package-card .btn-choose-package {
            background: #e63946;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            transition: background 0.3s, transform 0.3s;
        }

        .package-card .btn-choose-package:hover {
            background: #d62828;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .package-cards-container {
                flex-direction: column;
                align-items: center;
            }
            .package-card {
                max-width: 90%; /* Giới hạn chiều rộng trên màn hình nhỏ */
            }
            .package-card.popular {
                transform: scale(1.0); /* Không phóng to gói phổ biến trên mobile */
            }
            .package-card.popular:hover {
                transform: translateY(-8px) scale(1.0); /* Giữ hiệu ứng scale khi hover */
            }
             .package-card .popular-label {
                font-size: 0.75rem;
                padding: 4px 25px;
                width: 120px;
                right: -20px;
             }
        }
        /* Footer */
        footer {
            background:#1d3557; color:#fff; padding:50px 20px;
        }
        .footer-grid {
            display:grid;
            grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
            gap:30px;
            max-width:1200px; margin:auto;
        }
        footer h3 {
            font-size:1.3rem;
            margin-bottom:20px;
            border-bottom: 2px solid #e63946; /* Thinner border */
            display:inline-block;
            padding-bottom:8px; /* Adjusted padding */
            color:#fff; /* Ensure title color is white */
        }
        footer p, footer li, footer a {
            font-size:0.95rem;
            color:#ddd;
            margin-bottom:10px;
            line-height:1.7;
        }
        footer ul {list-style:none; padding-left:0;}
        footer ul li a:hover {color:#e63946; text-decoration:none;}
        .newsletter-form {display:flex; margin-top:10px;}
        footer input[type="email"] {
            padding:10px 12px; width:100%; flex-grow:1; border:none; border-radius:5px 0 0 5px;
            font-size:1rem; background-color: #f1faee; color:#1d3557;
        }
        footer input[type="email"]::placeholder {color: #6c757d;}
        footer button.subscribe-btn {
            padding:10px 15px; border:none; border-radius:0 5px 5px 0;
            background:#e63946; color:#fff; cursor:pointer;
            font-weight:600;
            font-size:1rem;
            transition: background 0.3s;
            white-space:nowrap;
        }
        footer button.subscribe-btn:hover {background:#d62828;}
        .footer-bottom {
            text-align:center; padding-top:30px; margin-top:30px; border-top:1px solid #457b9d;
            font-size:0.9rem; color:#a8dadc;
        }
        .footer-bottom p {margin-bottom: 5px;}

    </style>
    <script>
        // Responsive menu toggle (giữ nguyên từ index.php)
        function toggleMenu() {
            const menu = document.getElementById('menu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('menu');
            const menuIcon = document.querySelector('.menu-icon');
            if (menu && menuIcon && menu.classList.contains('show')) {
                if (!menu.contains(event.target) && !menuIcon.contains(event.target)) {
                    menu.classList.remove('show');
                }
            }
        });
    </script>
</head>
<body>

    <header>
        <div class="logo" onclick="window.location='index.php'">IQ Technology</div>
        <div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
        <nav>
            <ul id="menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="gioithieu.php">Giới thiệu</a></li> <li><a href="dichvu.php">Dịch vụ</a></li> <li><a href="dichvu.php#packages">Gói dịch vụ</a></li> <li><a href="sanpham.php">Sản phẩm</a></li> <li><a href="duan.php">Dự án</a></li> <li><a href="tintuc.php">Tin tức</a></li> <li><a href="lienhe.php">Liên hệ</a></li> </ul>
        </nav>
        <div class="hotline">Hotline: 0911.855.055</div>
        <button class="btn-consult" onclick="window.location='lienhe.php'">Đăng ký tư vấn</button>
    </header>

    <section class="page-hero">
        <h1>Các Dịch Vụ Chuyên Nghiệp của IQ Technology</h1>
        <p>Chúng tôi cung cấp đa dạng giải pháp IT toàn diện, phù hợp mọi nhu cầu doanh nghiệp.</p>
    </section>

    <section id="all-services">
        <h2 class="section-title">Khám phá các Dịch vụ của chúng tôi</h2>
        <div class="service-detail-grid">
            <?php
            if ($result_all_services && $result_all_services->num_rows > 0) {
                while ($row = $result_all_services->fetch_assoc()) {
                    echo '<div class="service-detail-card">';
                    // Đã sửa: Sử dụng ảnh placeholder cố định vì cột 'image' không có trong DB
                    echo '<img src="https://placehold.co/400x220/cccccc/333333?text=Service+Image" alt="' . htmlspecialchars($row['title']) . '">';
                    echo '<div class="service-detail-content">';
                    echo '<div class="icon">' . $row['icon'] . '</div>'; // Hiển thị icon
                    echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                    // Đã sửa: Chỉ sử dụng cột 'description' vì 'full_content' không có trong DB
                    echo '<p>' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                    // Bạn có thể tạo một trang chi tiết riêng cho từng dịch vụ nếu muốn
                    echo '<a href="#" class="btn-learn-more">Tìm hiểu thêm</a>';
                    echo '</div>'; // close service-detail-content
                    echo '</div>'; // close service-detail-card
                }
            } else {
                echo '<p class="empty-message">Hiện tại chưa có dịch vụ nào được cập nhật chi tiết. Vui lòng quay lại sau!</p>';
            }
            ?>
        </div>
    </section>

    <section id="packages" class="packages-section">
        <h2 class="section-title">Gói dịch vụ - Chọn gói phù hợp với doanh nghiệp bạn</h2>
        <div class="package-cards-container">
            <?php foreach ($packages_data as $package): ?>
                <div class="package-card <?php echo ($package['type'] == 'popular') ? 'popular' : ''; ?>">
                    <?php if ($package['type'] == 'popular'): ?>
                        <div class="popular-label">Phổ biến</div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                    <p class="price">
                        <?php if ($package['price_text'] === 'Liên hệ'): ?>
                            <?php echo htmlspecialchars($package['price_text']); ?>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($package['price_text']); ?></span><?php echo htmlspecialchars($package['duration']); ?>
                        <?php endif; ?>
                    </p>
                    <ul>
                        <?php foreach ($package['description'] as $feature): ?>
                            <li>
                                <i class="fas <?php echo $feature['included'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo htmlspecialchars($feature['text']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="btn-choose-package" onclick="window.location='lienhe.php'">Đăng ký tư vấn</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>


    <footer>
        <div class="footer-grid">
            <div>
                <h3>Về IQ Technology</h3>
                <p>Giải pháp IT toàn diện, hỗ trợ doanh nghiệp tối ưu hóa hiệu suất và bảo mật hệ thống. Uy tín - Chất lượng - Tận tâm.</p>
                <p>MST: 03xxxxxxx</p>
            </div>
            <div>
                <h3>Liên kết nhanh</h3>
                <ul>
                    <li><a href="index.php#about-us">Giới thiệu</a></li>
                    <li><a href="dichvu.php">Dịch vụ IT</a></li>
                    <li><a href="dichvu.php#packages">Gói bảo trì</a></li>
                    <li><a href="sanpham.php">Sản phẩm công nghệ</a></li>
                    <li><a href="tintuc.php">Tin tức & Thủ thuật</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div>
                <h3>Thông tin liên hệ</h3>
                <p><strong>Địa chỉ:</strong> Số 10 Huỳnh Thúc Kháng, Quận Hải Châu, TP. Đà Nẵng</p>
                <p><strong>Điện thoại:</strong> <a href="tel:0911855055">0911.855.055</a></p>
                <p><strong>Email:</strong> <a href="mailto:quangtmdit@gmail.com">quangtmdit@gmail.com</a></p>
                <p><strong>Giờ làm việc:</strong> T2 - T7: 8:00 - 18:00. Chủ Nhật: 9:00 - 17:00</p>
            </div>
            <div>
                <h3>Đăng ký nhận tin</h3>
                <p>Nhận thông tin mới nhất về giải pháp IT và các ưu đãi từ chúng tôi.</p>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" name="email_newsletter" placeholder="Nhập email của bạn" required>
                    <button type="submit" class="subscribe-btn">Đăng ký</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> IQ Technology. All Rights Reserved.</p>
            <p>Website được thiết kế và phát triển bởi IQ Technology Team.</p>
        </div>
    </footer>

    <?php
    // Đóng kết nối database
    if ($conn) {
        $conn->close();
    }
    ?>
</body>
</html>