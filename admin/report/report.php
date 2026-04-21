<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../config_admin/admin_auth.php';
include __DIR__ . '/../includes_admin/header.php';

/* =========================
   SELECTED YEAR
========================= */
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/* =========================
   AVAILABLE YEARS (HOÀN THÀNH)
========================= */
$resYears = mysqli_query($conn, "
    SELECT DISTINCT YEAR(order_date) AS year
    FROM orders
    WHERE status = 'Hoàn thành'
    ORDER BY year DESC
");
$availableYears = [];
while ($y = mysqli_fetch_assoc($resYears)) {
    $availableYears[] = $y['year'];
}

/* =========================
   KPI THEO NĂM
========================= */
$yearKPI = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total_orders,
        SUM(total_amount) AS total_revenue,
        AVG(total_amount) AS aov
    FROM orders
    WHERE status = 'Hoàn thành'
      AND YEAR(order_date) = $selectedYear
"));

$yearKPI['total_orders']  ??= 0;
$yearKPI['total_revenue'] ??= 0;
$yearKPI['aov']           ??= 0;

/* =========================
   YEAR OVER YEAR GROWTH
========================= */
$lastYearRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(total_amount) AS revenue
    FROM orders
    WHERE status = 'Hoàn thành'
      AND YEAR(order_date) = " . ($selectedYear - 1)
));

$growth = ($lastYearRevenue['revenue'] ?? 0) > 0
    ? (($yearKPI['total_revenue'] - $lastYearRevenue['revenue']) / $lastYearRevenue['revenue']) * 100
    : 0;

/* =========================
   MONTHLY DATA
========================= */
$months = [];
$revenues = [];
$orderCounts = [];

for ($m = 1; $m <= 12; $m++) {
    $months[] = "Tháng $m";

    $rev = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT SUM(total_amount) AS revenue
        FROM orders
        WHERE status = 'Hoàn thành'
          AND YEAR(order_date) = $selectedYear
          AND MONTH(order_date) = $m
    "));
    $revenues[] = (float)($rev['revenue'] ?? 0);

    $ord = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM orders
        WHERE status = 'Hoàn thành'
          AND YEAR(order_date) = $selectedYear
          AND MONTH(order_date) = $m
    "));
    $orderCounts[] = (int)($ord['total'] ?? 0);
}

/* =========================
   REVENUE BY YEAR
========================= */
$resYearRevenue = mysqli_query($conn, "
    SELECT YEAR(order_date) AS y, SUM(total_amount) AS revenue
    FROM orders
    WHERE status = 'Hoàn thành'
    GROUP BY YEAR(order_date)
    ORDER BY y
");

$years = $yearRevenues = [];
while ($yr = mysqli_fetch_assoc($resYearRevenue)) {
    $years[] = $yr['y'];
    $yearRevenues[] = (float)$yr['revenue'];
}

/* =========================
   TOP BRAND THEO NĂM
========================= */
$resBrand = mysqli_query($conn, "
    SELECT 
        b.namebrand AS brand,
        SUM(oi.price * oi.quantity) AS revenue
    FROM orders o
    JOIN order_items oi ON o.idorder = oi.order_id
    JOIN watches w ON oi.watch_id = w.idwatch
    JOIN brands b ON w.idbrand = b.idbrand
    WHERE o.status = 'Hoàn thành'
      AND YEAR(o.order_date) = $selectedYear
    GROUP BY b.idbrand
    ORDER BY revenue DESC
    LIMIT 5
");

$brands = [];
$brandRevenue = [];
while ($b = mysqli_fetch_assoc($resBrand)) {
    $brands[] = $b['brand'];
    $brandRevenue[] = (float)$b['revenue'];
}
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="report-container">
<h1>Báo cáo & Phân tích hệ thống</h1>

<div class="report-actions">
    <form method="get">
        <select name="year" class="year-select" onchange="this.form.submit()">
            <?php foreach ($availableYears as $y): ?>
                <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>>
                    Năm <?= $y ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <form method="get" action="/AureliusWatch/admin/report/export_report_pdf.php" target="_blank">
        <input type="hidden" name="year" value="<?= $selectedYear ?>">
        <button type="submit" class="btn-export-pdf">
            <i class="fa fa-file-pdf"></i> Xuất PDF năm <?= $selectedYear ?>
        </button>
    </form>
</div>

<!-- KPI -->
<div class="summary-box">
    <div class="summary-item">
        <h3><?= number_format($yearKPI['total_revenue']) ?> ₫</h3>
        <p>Doanh thu năm <?= $selectedYear ?></p>
    </div>
    <div class="summary-item">
        <h3><?= number_format($yearKPI['total_orders']) ?></h3>
        <p>Đơn hoàn thành</p>
    </div>
    <div class="summary-item">
        <h3><?= number_format($yearKPI['aov']) ?> ₫</h3>
        <p>AOV</p>
    </div>
    <div class="summary-item">
        <h3 style="color:<?= $growth >= 0 ? '#2ecc71' : '#e74c3c' ?>">
            <?= $growth >= 0 ? '▲' : '▼' ?> <?= number_format(abs($growth),1) ?>%
        </h3>
        <p>So với <?= $selectedYear - 1 ?></p>
    </div>
</div>

<!-- CHARTS -->
<div class="chart-row">
    <div class="chart-box">
        <h2>Doanh thu theo tháng</h2>
        <canvas id="revenueChart"></canvas>
    </div>

    <div class="chart-box">
        <h2>So sánh Doanh thu & Đơn hàng</h2>
        <canvas id="compareChart"></canvas>
    </div>
</div>

<div class="chart-row">
    <div class="chart-box pie-chart">
        <h2>Top 5 thương hiệu năm <?= $selectedYear ?></h2>
        <canvas id="brandChart"></canvas>
    </div>

    <div class="chart-box">
        <h2>Doanh thu theo năm</h2>
        <canvas id="yearChart"></canvas>
    </div>
</div>
</div>

<script>
const revenueCtx = document.getElementById('revenueChart');
const compareCtx = document.getElementById('compareChart');
const yearCtx    = document.getElementById('yearChart');
const brandCtx   = document.getElementById('brandChart');

new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{ data: <?= json_encode($revenues) ?>, backgroundColor:'#d4af37' }]
    },
    options:{plugins:{legend:{display:false}}}
});

new Chart(compareCtx, {
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
            {
                type: 'bar',
                label: 'Doanh thu',
                data: <?= json_encode($revenues) ?>,
                backgroundColor: '#d4af37',
                yAxisID: 'y'
            },
            {
                type: 'line',
                label: 'Số đơn',
                data: <?= json_encode($orderCounts) ?>,
                borderColor: '#ffffff',
                backgroundColor: 'rgba(255,255,255,0.1)',
                tension: 0.3,
                pointRadius: 4,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        scales: {
            y: {
                position: 'left',
                ticks: {
                    callback: v => v.toLocaleString('vi-VN') + ' ₫'
                },
                grid: {
                    color: 'rgba(255,255,255,0.08)'
                }
            },
            y1: {
                position: 'right',
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label(ctx) {
                        if (ctx.dataset.label === 'Doanh thu') {
                            return `Doanh thu: ${ctx.raw.toLocaleString('vi-VN')} ₫`;
                        }
                        return `Số đơn: ${ctx.raw}`;
                    }
                }
            }
        }
    }
});

new Chart(yearCtx, {
    type:'bar',
    data:{
        labels:<?= json_encode($years) ?>,
        datasets:[{ data:<?= json_encode($yearRevenues) ?>, backgroundColor:'#d4af37' }]
    },
    options:{plugins:{legend:{display:false}}}
});

const brandData = <?= json_encode($brandRevenue) ?>;

new Chart(brandCtx,{
    type:'pie',
    data:{
        labels:<?= json_encode($brands) ?>,
        datasets:[{
            data:brandData,
            backgroundColor:['#d4af37','#111','#7f8c8d','#bdc3c7','#ecf0f1']
        }]
    },
    options:{
        plugins:{
            tooltip:{
                displayColors:false,
                callbacks:{
                    label(ctx){
                        const total = brandData.reduce((a,b)=>a+b,0);
                        const value = ctx.raw || 0;
                        const percent = total ? ((value/total)*100).toFixed(1) : 0;
                        return [
                            `Doanh thu: ${value.toLocaleString('vi-VN')} ₫`,
                            `Tỷ trọng: ${percent}%`
                        ];
                    }
                }
            },
            legend:{
                position:'bottom',
                labels:{
                    color:'#d4af37',
                    usePointStyle:false
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
