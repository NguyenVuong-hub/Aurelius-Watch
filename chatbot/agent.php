<?php
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode([
        "reply" => "Bạn vui lòng nhập câu hỏi nhé."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . "/../includes/chatbot_rag.php";

$result = chatbotAnswer($message);

echo json_encode([
    "reply" => $result['reply'],
    "quickReplies" => $result['quickReplies'] ?? []
], JSON_UNESCAPED_UNICODE);
