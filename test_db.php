<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    echo "<b>[1] Kiểm tra thư viện MySQLi:</b> ";
    if (!function_exists('mysqli_init')) {
        throw new Exception("THẤT BẠI! Railway chưa cài được thư viện mysqli.");
    }
    echo "Thành công!<br>";

    echo "<b>[2] Đọc biến môi trường Railway:</b> ";
    $host = getenv('DB_HOST');
    if (!$host) {
        throw new Exception("THẤT BẠI! Không đọc được DB_HOST. Hãy kiểm tra lại tab Variables.");
    }
    echo "Thành công! (Host: $host)<br>";

    echo "<b>[3] Gọi kết nối Aiven:</b> ";
    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    
    // Dùng @ để tắt thông báo sập mặc định, tự xử lý bằng code
    $success = @mysqli_real_connect($conn, $host, getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'), getenv('DB_PORT'), NULL, MYSQLI_CLIENT_SSL);
    
    if (!$success) {
        throw new Exception("Từ chối kết nối - Lỗi chi tiết: " . mysqli_connect_error());
    }
    
    echo "🎉🎉🎉 THÀNH CÔNG RỰC RỠ! ĐÃ THÔNG VỚI AIVEN!";

} catch (Throwable $e) {
    echo "<br><br><div style='background:#ffebee; padding:15px; border: 1px solid #f44336;'>";
    echo "<h3 style='color:#d32f2f; margin-top:0;'>🛑 BẮT ĐƯỢC THỦ PHẠM GÂY SẬP WEB (502):</h3>";
    echo "<b>Lý do:</b> " . $e->getMessage();
    echo "</div>";
}
?>
