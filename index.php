<?php
// Kết nối Database
$host = 'localhost';
$db = 'it_service_db';
$user = 'root';    // Thường mặc định XAMPP là root
$pass = '';        // Mặc định XAMPP password rỗng

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}
// Set charset to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");

// Lấy dữ liệu dịch vụ tiêu biểu (ví dụ)
// Sửa: 'title' -> 'name AS title' để khớp với DB và giữ tương thích PHP
$sql_services = "SELECT id, name AS title, description, icon FROM services LIMIT 4";
$result_services = $conn->query($sql_services);

// Lấy dữ liệu gói dịch vụ
// Bảng 'packages' không tồn tại trong schema đã cung cấp.
// $sql_packages = "SELECT id, name, price, description FROM packages LIMIT 4";
$result_packages = false; // Đặt là false để phần hiển thị báo không có dữ liệu

// Lấy dữ liệu sản phẩm mới
// Sửa: 'image' -> 'thumbnail AS image' để khớp với DB
$sql_products = "SELECT id, name, price, thumbnail AS image FROM products ORDER BY created_at DESC LIMIT 6";
$result_products = $conn->query($sql_products);

// Lấy dữ liệu dự án tiêu biểu
// Bảng 'projects' không tồn tại trong schema đã cung cấp.
// $sql_projects = "SELECT id, name, description, image FROM projects LIMIT 4";
$result_projects = false; // Đặt là false để phần hiển thị báo không có dữ liệu

// Lấy dữ liệu bài viết mới nhất
// Sửa: 'image' -> 'thumbnail AS image', thêm 'SUBSTRING' cho 'excerpt'
$sql_posts = "SELECT id, title, SUBSTRING(content, 1, 150) AS excerpt, created_at, thumbnail AS image FROM posts ORDER BY created_at DESC LIMIT 4";
$result_posts = $conn->query($sql_posts);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>IQ Technology - Giải pháp IT toàn diện cho doanh nghiệp</title>
<style>
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


    /* Banner */
    .hero {
        position:relative;
        /* Updated banner image URL */
        background:url('https://images.unsplash.com/photo-1517030386927-463897b0a852?auto=format&fit=crop&w=1470&q=80') center/cover no-repeat;
        min-height:400px; /* Use min-height for flexibility */
        color:#fff;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        text-align:center;
        padding:20px; /* Consistent padding */
    }
    .hero h1 {font-size:clamp(2rem, 5vw, 2.8rem); margin-bottom:15px; text-shadow: 2px 2px 8px rgba(0,0,0,0.7);}
    .hero p {font-size:clamp(1rem, 3vw, 1.2rem); margin-bottom:25px; max-width:600px; text-shadow: 1px 1px 6px rgba(0,0,0,0.6);}
    .hero .hero-buttons {display:flex; flex-wrap:wrap; justify-content:center; gap:15px;}
    .hero button {
        background:#e63946; border:none; color:#fff; padding:12px 25px; /* margin:0 10px; remove fixed margin, use gap */
        border-radius:30px;
        font-weight:600; cursor:pointer;
        box-shadow: 0 5px 15px rgba(230,57,70,0.4);
        transition: background 0.3s, transform 0.3s;
    }
    .hero button:hover {background:#d62828; transform: translateY(-2px);}

    /* Sections */
    section {padding:50px 20px; max-width:1200px; margin: auto; overflow:hidden;} /* Added overflow:hidden for safety */
    h2.section-title { /* Made it a class for flexibility */
        color:#1d3557; margin-bottom:30px; font-size:clamp(1.8rem, 4vw, 2.2rem);
        border-bottom: 3px solid #e63946; display:inline-block; padding-bottom:10px;
    }

    /* Giới thiệu */
    .about {display:flex; gap:30px; align-items:center; flex-wrap:wrap;}
    .about-image-container {flex-basis:350px; flex-shrink:0; /* Prevent shrinking too much */}
    .about img {width:100%; border-radius:10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
    .about-text {flex:1; min-width: 280px; /* Ensure text has enough space */}
    .about-text p {font-size:1.1rem; line-height:1.7;}

    /* Grid 4 dịch vụ */
    .grid-4 {display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:25px;}
    .service-card {
        background:#fff; padding:25px; border-radius:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-align:center; transition: box-shadow 0.3s, transform 0.3s;
        display:flex; flex-direction:column;
    }
    .service-card:hover {box-shadow: 0 8px 20px rgba(0,0,0,0.12); transform: translateY(-5px);}
    .service-icon {font-size:2.8rem; margin-bottom:15px; color:#e63946; line-height:1;} /* Added line-height */
    .service-card h3 {margin-bottom:12px; color:#1d3557; font-size:1.3rem;}
    .service-card p {color:#555; font-size:0.95rem; line-height:1.5; flex-grow:1; margin-bottom:15px;}
    .service-card a.details-link {display:inline-block; margin-top:auto; font-weight:600; color:#e63946; padding:8px 15px; border:1px solid #e63946; border-radius:20px; transition: background-color 0.3s, color 0.3s;}
    .service-card a.details-link:hover {background-color:#e63946; color:#fff;}

    /* Bảng gói dịch vụ */
    .packages-table-container {overflow-x:auto; /* For responsive tables */}
    .packages-table {
        width:100%; border-collapse: collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 600px; /* Ensure table readability on small screens before scroll */
    }
    .packages-table th, .packages-table td {
        padding:15px 20px; text-align:left; border-bottom: 1px solid #eee;
    }
    .packages-table th {
        background:#1d3557; color:#fff; text-align:center;
    }
    .packages-table tr:last-child td {border-bottom:none;}
    .packages-table td:nth-child(2) {text-align:right; font-weight:bold; color: #e63946;} /* Price column */
    .packages-table td:last-child {text-align:center;} /* Button column */
    .packages-table td .btn-register {
        background:#e63946; color:#fff; padding:8px 20px; border:none; border-radius:30px; cursor:pointer;
        font-weight:600; white-space:nowrap;
        transition: background-color 0.3s;
    }
    .packages-table td .btn-register:hover {background:#d62828;}

    /* Sản phẩm, dự án, tin tức dạng card */
    .card-list {
        display:grid;
        grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); /* Slightly larger minmax */
        gap:25px;
    }
    .card {
        background:#fff; border-radius:10px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display:flex; flex-direction:column;
        transition: box-shadow 0.3s, transform 0.3s;
    }
    .card:hover {box-shadow: 0 8px 20px rgba(0,0,0,0.12); transform: translateY(-5px);}
    .card img.card-image { /* Added class for specificity */
        width:100%; height:200px; /* Increased height */ object-fit:cover;
    }
    .card-content {
        padding:20px; flex:1; display:flex; flex-direction:column;
    }
    .card-content h4 {
        margin-bottom:10px; color:#1d3557; font-size:1.2rem; line-height:1.3;
    }
    .card-content p.description { /* Class for description */
        color:#555; font-size:0.9rem; line-height:1.5;
        flex-grow:1; margin-bottom:15px;
    }
    .card-content .price {
        margin:10px 0;
        font-weight:700;
        color:#e63946;
        font-size:1.15rem;
    }
    .card-content .post-date {
        font-size: 0.85rem; color: #777; margin-bottom: 10px;
    }
    .card-content a.details-link { /* Consistent link styling */
        font-weight:600; color:#e63946; margin-top:auto; display:inline-block;
        padding:8px 15px; border:1px solid #e63946; border-radius:20px; text-align:center;
        transition: background-color 0.3s, color 0.3s;
    }
    .card-content a.details-link:hover {background-color:#e63946; color:#fff;}
    .empty-message {
        padding: 20px; text-align: center; color: #777; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        grid-column: 1 / -1; /* Make message span all columns if in a grid */
    }


    /* Form đăng ký tư vấn */
    .contact-form-section {background-color: #eef2f7; padding: 50px 20px;} /* Section background */
    .contact-form {
        background:#fff; padding:30px; border-radius:10px; max-width:600px; margin: auto;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .contact-form h2 {margin-bottom:25px; text-align:center; color:#1d3557;}
    .contact-form label {display:block; margin-bottom:6px; font-weight:600; color:#1d3557;}
    .contact-form input, .contact-form select, .contact-form textarea {
        width:100%; padding:12px 15px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;
        font-size:1rem; transition: border-color 0.3s, box-shadow 0.3s;
    }
    .contact-form input:focus, .contact-form select:focus, .contact-form textarea:focus {
        border-color: #e63946; box-shadow: 0 0 0 2px rgba(230,57,70,0.2); outline:none;
    }
    .contact-form textarea {min-height: 120px; resize:vertical;}
    .contact-form button {
        background:#e63946; color:#fff; border:none; padding:12px 20px; font-weight:700; cursor:pointer; border-radius:30px;
        width:100%;
        font-size:1.1rem;
        transition: background 0.3s, transform 0.3s;
    }
    .contact-form button:hover {background:#d62828; transform: translateY(-2px);}

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
    // Responsive menu toggle
    function toggleMenu() {
        const menu = document.getElementById('menu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    // Close menu if clicking outside on mobile
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
            <li><a href="#about-us">Giới thiệu</a></li>
            <li><a href="dichvu.php">Dịch vụ</a></li>
            <li><a href="#packages">Gói dịch vụ</a></li>
            <li><a href="#products">Sản phẩm</a></li>
            <li><a href="#projects">Dự án</a></li>
            <li><a href="#news">Tin tức</a></li>
            <li><a href="#contact">Liên hệ</a></li>
        </ul>
    </nav>
    <div class="hotline">Hotline: 0911.855.055</div>
    <button class="btn-consult" onclick="document.getElementById('contact-form-anchor').scrollIntoView({ behavior: 'smooth' });">Đăng ký tư vấn</button>
</header>

<section class="hero">
    <h1>Giải pháp IT toàn diện cho doanh nghiệp</h1>
    <p>Chuyên bảo trì - sửa chữa - tư vấn - thiết kế hệ thống CNTT trọn gói, đồng hành cùng sự phát triển của bạn.</p>
    <div class="hero-buttons">
        <button onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">Khám phá dịch vụ</button>
        <button onclick="document.getElementById('contact-form-anchor').scrollIntoView({ behavior: 'smooth' });">Liên hệ ngay</button>
    </div>
</section>

<section id="about-us">
    <h2 class="section-title">Giới thiệu về IQ Technology</h2>
    <div class="about">
        <div class="about-image-container">
            <img src="https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&w=800&q=80" alt="Đội ngũ kỹ thuật viên IQ Technology" 
                onerror="this.onerror=null;this.src='https://placehold.co/350x233/cccccc/333333?text=Our+Team';">
        </div>
        <div class="about-text">
            <p><strong>IQ Technology</strong> tự hào với hơn 10 năm kinh nghiệm trong lĩnh vực cung cấp giải pháp và dịch vụ IT chuyên nghiệp cho các doanh nghiệp vừa và nhỏ (SMEs). Chúng tôi chuyên sâu về bảo trì hệ thống máy tính, sửa chữa thiết bị văn phòng, thiết kế website hiện đại, quản trị hệ thống mạng an toàn và tư vấn các giải pháp công nghệ thông tin toàn diện, tối ưu chi phí và hiệu quả hoạt động cho khách hàng.</p>
            <p>Với đội ngũ kỹ thuật viên giàu kinh nghiệm, tận tâm và liên tục cập nhật công nghệ mới, IQ Technology cam kết mang đến sự ổn định và phát triển bền vững cho hạ tầng IT của bạn.</p>
        </div>
    </div>
</section>

<section id="services">
    <h2 class="section-title">Dịch vụ tiêu biểu</h2>
    <div class="grid-4">
        <?php
        if ($result_services && $result_services->num_rows > 0) {
            while ($row = $result_services->fetch_assoc()) {
                echo '<div class="service-card">';
                // Assuming $row['icon'] contains HTML like <i class="fas fa-laptop-code"></i> or SVG
                echo '<div class="service-icon">' . $row['icon'] . '</div>'; // Output icon HTML directly
                echo '<h3>' . htmlspecialchars($row['title']) . '</h3>'; // 'title' is an alias for 'name'
                echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                echo '<a href="#" class="details-link">Xem chi tiết</a>';
                echo '</div>';
            }
        } else {
            echo '<p class="empty-message">Hiện tại chưa có dịch vụ nào được cập nhật. Vui lòng quay lại sau!</p>';
        }
        ?>
    </div>
</section>

<section id="packages" style="background-color: #eef2f7;">
    <h2 class="section-title">Gói dịch vụ - Chọn gói phù hợp</h2>
    <div class="packages-table-container">
        <table class="packages-table">
            <thead>
                <tr><th>Tên Gói Dịch Vụ</th><th>Chi Phí Ước Tính</th><th>Mô Tả Chi Tiết</th><th>Hành Động</th></tr>
            </thead>
            <tbody>
                <?php
                // $result_packages is false because table 'packages' does not exist in schema
                if ($result_packages && $result_packages->num_rows > 0) {
                    // This block will not be executed
                    while ($row = $result_packages->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                        echo '<td>' . nl2br(htmlspecialchars($row['description'])) . '</td>';
                        echo '<td><button class="btn-register" onclick="alert(\'Chức năng đăng ký tư vấn cho gói ' . htmlspecialchars(addslashes($row['name'])) . ' hiện chưa sẵn sàng. Vui lòng liên hệ trực tiếp!\')">Đăng ký tư vấn</button></td>';
                        echo '</tr>';
                    }
                } else {
                    // This message will be displayed as $result_packages is false
                    echo '<tr><td colspan="4"><p class="empty-message" style="text-align:left; padding: 20px 0;">Thông tin các gói dịch vụ hiện chưa được cập nhật. Chúng tôi sẽ sớm bổ sung!</p></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<section id="products">
    <h2 class="section-title">Sản phẩm mới / bán chạy</h2>
    <div class="card-list">
        <?php
        if ($result_products && $result_products->num_rows > 0) {
            while ($row = $result_products->fetch_assoc()) {
                echo '<div class="card">';
                // 'image' is an alias for 'thumbnail'
                echo '<img class="card-image" src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.onerror=null;this.src=\'https://placehold.co/280x200/cccccc/333333?text=Image+Unavailable\';">';
                echo '<div class="card-content">';
                echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
                $price = is_numeric($row['price']) ? number_format((float)$row['price'], 0, ',', '.') . ' VNĐ' : htmlspecialchars($row['price']);
                echo '<p class="price">' . $price . '</p>';
                echo '<a href="#" class="details-link">Chi tiết sản phẩm</a>';
                echo '</div>'; // close card-content
                echo '</div>'; // close card
            }
        } else {
            echo '<p class="empty-message">Hiện chưa có sản phẩm nào. Chúng tôi đang cập nhật!</p>';
        }
        ?>
    </div>
</section>

<section id="projects" style="background-color: #eef2f7;">
    <h2 class="section-title">Dự án tiêu biểu</h2>
    <div class="card-list">
        <?php
        // $result_projects is false because table 'projects' does not exist in schema
        if ($result_projects && $result_projects->num_rows > 0) {
            // This block will not be executed
            while ($row = $result_projects->fetch_assoc()) {
                echo '<div class="card">';
                echo '<img class="card-image" src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.onerror=null;this.src=\'https://placehold.co/280x200/cccccc/333333?text=Project+Image\';">';
                echo '<div class="card-content">';
                echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
                echo '<p class="description">' . htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : '') . '</p>';
                echo '<a href="#" class="details-link">Xem dự án</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            // This message will be displayed as $result_projects is false
            echo '<p class="empty-message">Chưa có dự án tiêu biểu nào được cập nhật. Vui lòng kiểm tra lại sau.</p>';
        }
        ?>
    </div>
</section>

<section id="news">
    <h2 class="section-title">Tin tức & Bài viết mới nhất</h2>
    <div class="card-list">
        <?php
        if ($result_posts && $result_posts->num_rows > 0) {
            while ($row = $result_posts->fetch_assoc()) {
                echo '<div class="card">';
                   // 'image' is an alias for 'thumbnail', 'excerpt' is an alias for SUBSTRING(content,...)
                echo '<img class="card-image" src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" onerror="this.onerror=null;this.src=\'https://placehold.co/280x200/cccccc/333333?text=News+Image\';">';
                echo '<div class="card-content">';
                echo '<h4>' . htmlspecialchars($row['title']) . '</h4>';
                try {
                    $date = new DateTime($row['created_at']);
                    echo '<p class="post-date"><small>Ngày đăng: ' . $date->format('d/m/Y') . '</small></p>';
                } catch (Exception $e) {
                    echo '<p class="post-date"><small>Ngày đăng: N/A</small></p>';
                }
                echo '<p class="description">' . htmlspecialchars($row['excerpt']) . '</p>';
                echo '<a href="#" class="details-link">Đọc thêm</a>';
                echo '</div>'; // close card-content
                echo '</div>'; // close card
            }
        } else {
            echo '<p class="empty-message">Chưa có bài viết mới. Hãy ghé thăm chúng tôi thường xuyên để cập nhật!</p>';
        }
        ?>
    </div>
</section>

<div id="contact-form-anchor" style="scroll-margin-top: 80px;"></div> {/* Anchor for smooth scroll */}
<section id="contact" class="contact-form-section">
    <div class="contact-form">
        <h2 class="section-title" style="text-align:center; display:block;">Đăng ký nhận tư vấn miễn phí</h2>
        <form action="submit_form.php" method="POST"> {/* Replace submit_form.php with your actual handler */}
            <label for="name_contact">Họ và Tên:</label> {/* Changed id to name_contact to avoid conflict if any */}
            <input type="text" id="name_contact" name="name" required placeholder="Nguyễn Văn A">

            <label for="phone_contact">Số điện thoại:</label> {/* Changed id */}
            <input type="tel" id="phone_contact" name="phone" required placeholder="09xxxxxxxx">

            <label for="email_contact">Email:</label> {/* Changed id */}
            <input type="email" id="email_contact" name="email" placeholder="example@email.com">

            <label for="service_needed">Dịch vụ quan tâm:</label>
            <select id="service_needed" name="service_needed">
                <option value="">-- Chọn dịch vụ --</option>
                <?php
                // Populate services from DB if available, otherwise provide static options
                if ($result_services_for_dropdown = $conn->query("SELECT id, name FROM services ORDER BY name ASC")) {
                    if ($result_services_for_dropdown->num_rows > 0) {
                        while($service_row = $result_services_for_dropdown->fetch_assoc()){
                            echo '<option value="' . htmlspecialchars($service_row['id']) . '">' . htmlspecialchars($service_row['name']) . '</option>';
                        }
                    } else {
                            echo '<option value="khac">Chưa có dịch vụ cụ thể</option>';
                    }
                    $result_services_for_dropdown->free();
                } else {
                    // Static fallback if query fails or no services
                    echo '<option value="bao_tri_he_thong">Bảo trì hệ thống</option>';
                    echo '<option value="sua_chua_thiet_bi">Sửa chữa thiết bị</option>';
                    echo '<option value="thiet_ke_website">Thiết kế Website</option>';
                    echo '<option value="khac">Khác</option>';
                }
                ?>
                <option value="khac_input">Khác (ghi rõ ở dưới)</option>
            </select>

            <label for="message">Nội dung yêu cầu:</label>
            <textarea id="message" name="message" rows="4" required placeholder="Nêu rõ yêu cầu của bạn..."></textarea>

            <button type="submit">Gửi Yêu Cầu</button>
        </form>
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
                <li><a href="#about-us">Giới thiệu</a></li>
                <li><a href="#services">Dịch vụ IT</a></li>
                <li><a href="#packages">Gói bảo trì</a></li>
                <li><a href="#products">Sản phẩm công nghệ</a></li>
                <li><a href="#news">Tin tức & Thủ thuật</a></li>
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
<script>
// Tạo chat bubble
const chatIcon = document.createElement('div');
chatIcon.innerHTML = "💬";
chatIcon.style.cssText = `
    position: fixed; bottom: 20px; right: 20px;
    width: 60px; height: 60px;
    background: red; color: white;
    border-radius: 50%; font-size: 30px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; z-index: 9999;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
`;
document.body.appendChild(chatIcon);

const iframe = document.createElement('iframe');
iframe.style.cssText = `
    position: fixed; bottom: 90px; right: 20px;
    width: 300px; height: 400px;
    display: none; border: 1px solid #ccc;
    border-radius: 10px; z-index: 9999;
`;
iframe.src = "https://chat.openai.com"; // sau này bạn có thể thay bằng chatbot nội bộ
document.body.appendChild(iframe);

chatIcon.onclick = () => {
    iframe.style.display = iframe.style.display === "none" ? "block" : "none";
};
</script>

</body>
</html>