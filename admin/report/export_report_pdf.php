<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../tfpdf.php';

/* =========================
   SELECTED YEAR
========================= */
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/* =========================
   KPI – THEO NĂM
========================= */
$yearKPI = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total_orders,
        SUM(total_amount) AS total_revenue,
        AVG(total_amount) AS aov
    FROM orders
    WHERE status='Hoàn thành'
      AND YEAR(order_date) = $year
")) ?? ['total_orders'=>0,'total_revenue'=>0,'aov'=>0];

/* =========================
   DATA THEO THÁNG
========================= */
$resMonth = mysqli_query($conn, "
    SELECT 
        MONTH(order_date) AS m,
        SUM(total_amount) AS revenue,
        COUNT(*) AS orders
    FROM orders
    WHERE status='Hoàn thành'
      AND YEAR(order_date) = $year
    GROUP BY m
    ORDER BY m
");

/* =========================
   DOANH THU THEO NĂM
========================= */
$resYear = mysqli_query($conn, "
    SELECT YEAR(order_date) AS y, SUM(total_amount) AS revenue
    FROM orders
    WHERE status='Hoàn thành'
    GROUP BY y
    ORDER BY y
");

/* =========================
   TOP 5 THƯƠNG HIỆU
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
  AND YEAR(o.order_date) = $year
GROUP BY b.idbrand
ORDER BY revenue DESC
LIMIT 5

");

/* =========================
   COLORS
========================= */
$gold = [212,175,55];
$black = [20,20,20];
$gray  = [245,245,245];

/* =========================
   PDF CLASS
========================= */
class PDF extends tFPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('DejaVu','',8);
        $this->Cell(0,6,'Trang '.$this->PageNo(),0,0,'C');
    }
}

/* =========================
   INIT PDF
========================= */
$pdf = new PDF('P','mm','A4');
$pdf->SetMargins(10,10,10);
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
$pdf->SetFont('DejaVu','',11);
$pdf->SetAutoPageBreak(true,40);

/* =========================
   HEADER
========================= */
$logo = $_SERVER['DOCUMENT_ROOT'].'/AureliusWatch/assets/images/logo.png';
if(file_exists($logo)){
    $pdf->Image($logo,10,10,25);
}

$pdf->SetFont('DejaVu','B',15);
$pdf->Cell(0,15,'BÁO CÁO DOANH THU NĂM '.$year,0,1,'C');

$pdf->SetFont('DejaVu','',10);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$pdf->Cell(0,8,'Thời gian xuất: '.date('d/m/Y H:i'),0,1,'C');

$pdf->Ln(5);

/* =========================
   KPI BOX
========================= */
function kpiBox($pdf,$title,$value){
    global $gray;
    $pdf->SetFillColor(...$gray);
    $pdf->Cell(95,12,$title,1,0,'L',true);
    $pdf->SetFont('DejaVu','B',11);
    $pdf->Cell(95,12,$value,1,1,'C',true);
    $pdf->SetFont('DejaVu','',11);
}

$pdf->SetFont('DejaVu','B',12);
$pdf->Cell(0,8,'TỔNG QUAN NĂM '.$year,0,1);
$pdf->Ln(2);

kpiBox($pdf,'Tổng doanh thu',number_format($yearKPI['total_revenue']).' ₫');
kpiBox($pdf,'Tổng đơn hoàn thành',number_format($yearKPI['total_orders']));
kpiBox($pdf,'AOV trung bình',number_format($yearKPI['aov']).' ₫');
$pdf->Ln(5);

/* =========================
   TABLE FUNCTIONS
========================= */
function tableHeader($pdf,$headers,$widths){
    global $black,$gold;
    $pdf->SetFillColor(...$black);
    $pdf->SetTextColor(...$gold);
    $pdf->SetFont('DejaVu','B',11);
    foreach($headers as $i=>$h){
        $pdf->Cell($widths[$i],8,$h,1,0,'C',true);
    }
    $pdf->Ln();
    $pdf->SetTextColor(0);
    $pdf->SetFont('DejaVu','',11);
}

function tableRow($pdf,$data,$widths,$fill){
    global $gray;
    $pdf->SetFillColor(...$gray);
    foreach($data as $i=>$d){
        $pdf->Cell($widths[$i],7,$d,1,0,'C',$fill);
    }
    $pdf->Ln();
}

/* =========================
   TABLE THEO THÁNG
========================= */
$pdf->SetFont('DejaVu','B',12);
$pdf->Cell(0,8,'DOANH THU & ĐƠN HÀNG THEO THÁNG',0,1);
$pdf->Ln(2);

tableHeader($pdf,['Tháng','Doanh thu','Số đơn'],[60,65,65]);
$fill=false;
while($r=mysqli_fetch_assoc($resMonth)){
    tableRow(
        $pdf,
        ['Tháng '.$r['m'], number_format($r['revenue']).' ₫', number_format($r['orders'])],
        [60,65,65],
        $fill
    );
    $fill=!$fill;
}
$pdf->Ln(5);

/* =========================
   TOP BRAND
========================= */
$pdf->SetFont('DejaVu','B',12);
$pdf->Cell(0,8,'TOP 5 THƯƠNG HIỆU THEO NĂM '.$year,0,1);
$pdf->Ln(2);

tableHeader($pdf,['Thương hiệu','Doanh thu'],[80,110]);
$fill=false;
while ($b = mysqli_fetch_assoc($resBrand)) {
    tableRow(
        $pdf,
        [
            $b['brand'],
            number_format($b['revenue']) . ' ₫'
        ],
        [80,110],
        $fill
    );
    $fill = !$fill;
}

/* =========================
   SIGNATURE
========================= */
$pdf->Ln(10);
$pdf->SetX(120);
$pdf->SetFont('DejaVu','B',11);
$pdf->Cell(60,7,'Người lập báo cáo',0,1,'C');
$pdf->SetFont('DejaVu','',10);
$pdf->SetX(120);
$pdf->Cell(60,6,'(Ký & Họ tên)',0,1,'C');

/* =========================
   OUTPUT
========================= */
$pdf->Output('',"bao_cao_doanh_thu_nam_$year.pdf");
exit;
