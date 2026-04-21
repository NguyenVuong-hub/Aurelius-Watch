<?php
include '../../includes/header.php';

/* DANH SÁCH BÀI VIẾT (GIẢ LẬP DATA) */
$blogs = [
    1 => [
        'title' => 'Patek Philippe – Di sản chế tác hơn 180 năm',
        'image' => '/AureliusWatch/assets/images/blogdetail1.jpg',
        'date'  => '20/12/2025',
        'brand' => 'Patek Philippe',
        'content' => '
            <p>
                Trong thế giới đồng hồ xa xỉ, <strong>Patek Philippe</strong>
                không chỉ là một thương hiệu mà là một biểu tượng.
            </p>

            <p>
                Với hơn 180 năm lịch sử, Patek Philippe nổi tiếng với
                những cỗ máy cơ học tinh xảo và giá trị truyền đời.
            </p>

            <blockquote>
                “You never actually own a Patek Philippe.
                You merely look after it for the next generation.”
            </blockquote>

            <p>
                Những mẫu như Nautilus hay Grand Complications
                luôn là ước mơ của các nhà sưu tầm.
            </p>
        '
    ],

    2 => [
        'title' => 'Rolex – Biểu tượng của thành công',
        'image' => '/AureliusWatch/assets/images/blogdetail2.jpg',
        'date'  => '18/12/2025',
        'brand' => 'Rolex',
        'content' => '
            <p>
                Rolex không chỉ là một chiếc đồng hồ,
                mà là tuyên ngôn của sự thành đạt.
            </p>

            <p>
                Được biết đến với độ bền và độ chính xác,
                Rolex chinh phục những đỉnh cao khắc nghiệt nhất.
            </p>

            <blockquote>
                “A crown for every achievement.”
            </blockquote>

            <p>
                Từ Submariner đến Datejust,
                Rolex luôn giữ vững giá trị biểu tượng.
            </p>
        '
    ],

    3 => [
        'title' => 'Cách chọn đồng hồ phù hợp với doanh nhân',
        'image' => '/AureliusWatch/assets/images/blogdetail3.jpg',
        'date'  => '15/12/2025',
        'brand' => 'Luxury Guide',
        'content' => '
            <p>
                Một chiếc đồng hồ phù hợp giúp doanh nhân
                thể hiện phong cách và bản lĩnh.
            </p>

            <p>
                Thiết kế, thương hiệu và hoàn cảnh sử dụng
                là những yếu tố cần cân nhắc.
            </p>

            <blockquote>
                “A watch speaks before you do.”
            </blockquote>

            <p>
                Đồng hồ không chỉ để xem giờ,
                mà còn là tuyên ngôn cá nhân.
            </p>
        '
    ]
];

/* LẤY ID */
$id = $_GET['id'] ?? 1;

/* NẾU ID KHÔNG TỒN TẠI */
if (!isset($blogs[$id])) {
    echo "<h2 style='text-align:center'>Bài viết không tồn tại</h2>";
    include '../../includes/footer.php';
    exit;
}

$blog = $blogs[$id];
?>

<!-- ===== BLOG DETAIL ===== -->
<div class="blog-detail">

    <div class="blog-banner">
        <img src="<?= $blog['image'] ?>" alt="">
    </div>

    <div class="blog-detail-container">
        <h1 class="blog-detail-title"><?= $blog['title'] ?></h1>

        <div class="blog-meta">
            <span><?= $blog['date'] ?></span>
            <span><?= $blog['brand'] ?></span>
        </div>

        <div class="blog-content">
            <?= $blog['content'] ?>
        </div>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>
