<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/about.css">

<div class="about-wrapper">

<!-- HERO -->
<section class="about-hero">
    <div class="hero-inner">
        <span class="hero-brand">AURELIUS WATCH</span>
        <h1>A Legacy of Time</h1>
        <p>Di sản của thời gian</p>
        <p>Đồng hồ không chỉ đo thời gian – mà đo đẳng cấp</p>
    </div>
</section>

<!-- INTRO -->
<section class="about-block fade">
    <div class="about-grid">
        <div class="about-text">
            <span class="label">Về AureliusWatch</span>
            <h2 class="about-title">Biểu tượng của phong cách & vị thế</h2>
            <p class="about-desc luxury">
                <strong>AureliusWatch</strong> không đơn thuần là nơi mua sắm
                đồng hồ cao cấp. Chúng tôi mang đến những cỗ máy thời gian
                được tuyển chọn kỹ lưỡng từ các thương hiệu danh tiếng,
                nơi mỗi chi tiết đều đại diện cho
                <em>đẳng cấp, thành công và bản sắc cá nhân</em>.
            </p>
        </div>

        <div class="about-image">
            <img src="../assets/images/about.jpg" alt="Luxury Watch">
        </div>
    </div>
</section>

<!-- CORE VALUES -->
<section class="about-dark fade">
    <span class="label center">Giá trị cốt lõi</span>
    <h2 class="center">Chúng tôi mang đến điều gì?</h2>

    <div class="value-grid">
        <div class="value-card">
            <h3>Luxury</h3>
            <p>Thiết kế tinh xảo, đẳng cấp quốc tế, tôn vinh phong cách cá nhân.</p>
        </div>

        <div class="value-card highlight">
            <h3>Authentic</h3>
            <p>Cam kết 100% chính hãng, đầy đủ giấy tờ, bảo hành minh bạch.</p>
        </div>

        <div class="value-card">
            <h3>Service</h3>
            <p>Dịch vụ tư vấn tận tâm, hỗ trợ khách hàng 24/7.</p>
        </div>
    </div>
</section>

</div> <!-- END about-wrapper -->

<?php include '../includes/footer.php'; ?>

<script>
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.classList.add('show');
        }
    });
},{ threshold: 0.15 });

document.querySelectorAll('.fade').forEach(el => observer.observe(el));
</script>
