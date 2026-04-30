<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../tfpdf.php';

$idorder = intval($_GET['id'] ?? 0);
if ($idorder <= 0) die('Đơn hàng không hợp lệ');

/* ===== LẤY THÔNG TIN ĐƠN HÀNG ===== */
$orderQuery = "
SELECT 
    o.*,
    COALESCE(u.hoten, o.guest_name, 'Khách vãng lai') AS customer_name,
    COALESCE(u.phone, o.guest_phone) AS phone,
    COALESCE(u.email, o.guest_email) AS email
FROM orders o
LEFT JOIN user u ON o.iduser = u.iduser
WHERE o.idorder = $idorder
";
$order = $conn->query($orderQuery)->fetch_assoc();
if (!$order) die('Không tìm thấy đơn hàng');

/* ===== LẤY CHI TIẾT SẢN PHẨM ===== */
$itemSql = "
SELECT 
    w.namewatch,
    oi.quantity,
    oi.price,
    (oi.quantity * oi.price) AS subtotal
FROM order_items oi
JOIN watches w ON oi.watch_id = w.idwatch
WHERE oi.order_id = $idorder
";
$items = $conn->query($itemSql);

/* =========================
   PDF CLASS CHUẨN HÓA ĐƠN
========================= */
class PDF extends tFPDF {
    function Header() {
        $logo = $_SERVER['DOCUMENT_ROOT'].'/AureliusWatch/assets/images/logo.png';
        if(file_exists($logo)) $this->Image($logo,20,20,35);

        $this->SetFont('DejaVu','B',16);
        $this->SetXY(40,15);
        $this->Cell(0,10,'HÓA ĐƠN MUA HÀNG',0,1,'C');

        $this->SetFont('DejaVu','',11);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->SetX(40);
        $this->Cell(0,8,'Ngày xuất: '.date('d/m/Y H:i'),0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15); // luôn ở đáy
        $this->SetFont('DejaVu','',8);
        $this->Cell(0,6,'Trang '.$this->PageNo(),0,0,'C');
    }


    function OrderDetails($order,$items){
        $this->SetFont('DejaVu','B',12);
        $this->Cell(0,8,'Thông tin khách hàng',0,1,'L');

        $this->SetFont('DejaVu','',11);
        $this->Cell(0,6,'Họ tên: '.$order['customer_name'],0,1,'L');
        $this->Cell(0,6,'Email: '.$order['email'],0,1,'L');
        $this->Cell(0,6,'Số điện thoại: '.$order['phone'],0,1,'L');
        $this->Cell(0,6,'Ngày đặt: '.date('d/m/Y H:i', strtotime($order['order_date'])),0,1,'L');
        $this->Ln(5);

        // Header bảng
        $widthProduct = 70;
        $widthQty     = 25;
        $widthPrice   = 45;
        $widthTotal   = 45;

        $this->SetFont('DejaVu','B',12);
        $this->SetFillColor(212,175,55);
        $this->Cell($widthProduct,8,'Sản phẩm',1,0,'C',true);
        $this->Cell($widthQty,8,'Số lượng',1,0,'C',true);
        $this->Cell($widthPrice,8,'Đơn giá',1,0,'C',true);
        $this->Cell($widthTotal,8,'Thành tiền',1,1,'C',true);

        $this->SetFont('DejaVu','',11);
        $fill = false;
        $total = 0;

        while($row = $items->fetch_assoc()){
            $subtotal = $row['subtotal'];
            $this->SetFillColor(245,245,245);

            $x = $this->GetX();
            $y = $this->GetY();

            // Sản phẩm xuống dòng nếu dài
            $this->MultiCell($widthProduct,7,$row['namewatch'],1,'L',$fill);
            $h = $this->GetY() - $y;

            $this->SetXY($x + $widthProduct, $y);
            $this->Cell($widthQty, $h, $row['quantity'],1,0,'C',$fill);
            $this->Cell($widthPrice, $h, number_format($row['price']).' VNĐ',1,0,'C',$fill);
            $this->Cell($widthTotal, $h, number_format($subtotal).' VNĐ',1,1,'C',$fill);

            $fill = !$fill;
            $total += $subtotal;
        }

        // ===== TỔNG CỘNG (2 Ô BẰNG NHAU) =====
        $this->Ln(3);
        $this->SetFont('DejaVu','B',12);

        $totalWidth = $widthProduct + $widthQty + $widthPrice + $widthTotal;
        $halfWidth  = $totalWidth / 2;

        // Ô trái
        $this->Cell(
            $halfWidth,
            8,
            'Tổng cộng',
            1,
            0,
            'C'
        );

        // Ô phải
        $this->Cell(
            $halfWidth,
            8,
            number_format($total) . ' VNĐ',
            1,
            1,
            'C'
        );
    }
}

/* =========================
   TẠO PDF
========================= */
$pdf = new PDF('P','mm','A4');
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
$pdf->SetAutoPageBreak(true,50);
$pdf->AddPage();
$pdf->OrderDetails($order,$items);

$pdf->Ln(10); // khoảng cách từ bảng cuối
$leftX = 30;   // vị trí chữ ký bên trái
$rightX = 120; // vị trí chữ ký bên phải

$pdf->SetFont('DejaVu','B',12);
$pdf->SetX($leftX);
$pdf->Cell(60,7,'Khách hàng',0,0,'C');
$pdf->SetX($rightX);
$pdf->Cell(60,7,'Người lập báo cáo',0,1,'C');

$pdf->SetFont('DejaVu','',10);
$pdf->SetX($leftX);
$pdf->Cell(60,6,'(Ký & Họ tên)',0,0,'C');
$pdf->SetX($rightX);
$pdf->Cell(60,6,'(Ký & Họ tên)',0,1,'C');

$pdf->Output('I','hoa_don_'.$idorder.'.pdf');
exit;
