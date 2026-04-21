<?php
require_once __DIR__ . '/../config/db.php';

/* =========================
   HELPER: FIND BY DB NAME (DYNAMIC)
========================= */
function findByName($table, $idCol, $nameCol, $msg, $extraWhere = '')
{
    global $conn;
    if (!$conn) return null;

    $sql = "
        SELECT $idCol, $nameCol
        FROM $table
        WHERE ? LIKE CONCAT('%', LOWER($nameCol), '%')
        $extraWhere
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $msg);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}

/* =========================
   HELPER: FIND BRAND
========================= */
function findBrand($msg)
{
    global $conn;
    if (!$conn) return null;

    $aliases = [
        'catier' => 'cartier',
        'carter' => 'cartier',
        'rolexx' => 'rolex'
    ];

    foreach ($aliases as $wrong => $correct) {
        if (mb_stripos($msg, $wrong) !== false) {
            $msg = str_replace($wrong, $correct, $msg);
        }
    }

    $stmt = $conn->prepare("
        SELECT idbrand, namebrand
        FROM brands
        WHERE ? LIKE CONCAT('%', LOWER(namebrand), '%')
        LIMIT 1
    ");
    $stmt->bind_param("s", $msg);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}

/* =========================
   HELPER: FIND GENDER
========================= */
function findGender($msg)
{
    $msg = mb_strtolower($msg, 'UTF-8');

    if (preg_match('/\b(unisex|đôi|nam\s*nữ|couple)\b/u', $msg)) {
        return ['idgender' => 3, 'namegender' => 'Unisex'];
    }
    if (preg_match('/\b(nam|men|male)\b/u', $msg)) {
        return ['idgender' => 1, 'namegender' => 'Nam'];
    }
    if (preg_match('/\b(nữ|nu|women|female)\b/u', $msg)) {
        return ['idgender' => 2, 'namegender' => 'Nữ'];
    }

    return null;
}

/* =========================
   HELPER: FIND PRICE
========================= */
function findPrice($msg)
{
    if (preg_match('/dưới\s*500|<\s*500/u', $msg)) {
        return ['key' => 'price', 'value' => 'under500', 'label' => 'Dưới 500 triệu'];
    }
    if (preg_match('/dưới\s*1\s*tỷ|<\s*1/u', $msg)) {
        return ['key' => 'price', 'value' => 'under1000', 'label' => 'Dưới 1 tỷ'];
    }
    if (preg_match('/trên\s*1\s*tỷ|>\s*1/u', $msg)) {
        return ['key' => 'price', 'value' => 'over1000', 'label' => 'Trên 1 tỷ'];
    }
    return null;
}

/* =========================
   MAIN CHATBOT
========================= */
function chatbotAnswer($message)
{
    try {
        global $conn;
        if (!$conn) throw new Exception("DB connection failed");

        $msg = mb_strtolower(trim($message), 'UTF-8');

        $brand  = findBrand($msg);
        $gender = findGender($msg);
        $price  = findPrice($msg);

        $color = findByName(
            'case_colors',
            'idcolor',
            'namecolor',
            $msg,
            "AND status = 1"
        );

        $strap = findByName(
            'materials',
            'idmaterial',
            'namematerial',
            $msg,
            "AND material_type = 'strap'"
        );

        /* ================= CHECK PRODUCTS ================= */
        $checkWhere  = [];
        $checkParams = [];
        $checkTypes  = "";

        if ($brand) {
            $checkWhere[]  = "idbrand = ?";
            $checkParams[] = $brand['idbrand'];
            $checkTypes   .= "i";
        }

        if ($gender) {
            if (in_array($gender['idgender'], [1,2])) {
                $checkWhere[] = "idgender IN (?,3)";
                $checkParams[] = $gender['idgender'];
                $checkTypes   .= "i";
            } else {
                $checkWhere[]  = "idgender = ?";
                $checkParams[] = $gender['idgender'];
                $checkTypes   .= "i";
            }
        }

        if ($price) {
            if ($price['value'] === 'under500')  $checkWhere[] = "price < 500000000";
            if ($price['value'] === 'under1000') $checkWhere[] = "price < 1000000000";
            if ($price['value'] === 'over1000')  $checkWhere[] = "price >= 1000000000";
        }

        if ($color) {
            $checkWhere[]  = "case_color_id = ?";
            $checkParams[] = $color['idcolor'];
            $checkTypes   .= "i";
        }

        if ($strap) {
            $checkWhere[]  = "strap_material_id = ?";
            $checkParams[] = $strap['idmaterial'];
            $checkTypes   .= "i";
        }

        $sql = "SELECT COUNT(*) AS total FROM watches "
             . ($checkWhere ? "WHERE ".implode(" AND ", $checkWhere) : "");

        $stmt = $conn->prepare($sql);
        if ($checkParams) {
            $stmt->bind_param($checkTypes, ...$checkParams);
        }
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];

        if ($total === 0) {
            return ["reply" => "😢 Hiện chưa có mẫu phù hợp với yêu cầu của bạn."];
        }

        /* ================= BUILD URL ================= */
        $params = [];
        if ($brand)  $params[] = "brand=".$brand['idbrand'];
        if ($gender) $params[] = "gender=".$gender['idgender'];
        if ($price)  $params[] = "{$price['key']}={$price['value']}";
        if ($color)  $params[] = "color=".$color['idcolor'];
        if ($strap)  $params[] = "strap=".$strap['idmaterial'];

        $params[] = "from=chatbot";
        $url = "/AureliusWatch/pages/product/list.php?".implode("&",$params);

        /* ================= TEXT ================= */
        $titleParts = array_filter([
            $brand['namebrand'] ?? 'Đồng hồ',
            $gender['namegender'] ?? null,
            $strap['namematerial'] ?? null,
            $color['namecolor'] ?? null
        ]);

        $title = implode(' ', $titleParts);

        return [
            "reply" => "
                <div class='chatbot-card'>
                    <div class='chatbot-card-title'>{$title}</div>
                    <div class='chatbot-card-desc'>
                        Mình đã chọn lọc các mẫu phù hợp với yêu cầu của bạn.
                    </div>
                    <a href='{$url}' class='chatbot-btn'>Xem bộ sưu tập</a>
                </div>
            "
        ];

    } catch (Throwable $e) {
        return ["reply" => "⚠️ Hệ thống đang bận, vui lòng thử lại."];
    }
}
