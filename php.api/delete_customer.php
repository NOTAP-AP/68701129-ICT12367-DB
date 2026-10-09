<?php
include 'condb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "อนุญาตเฉพาะคำขอ DELETE"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$customerId = $data['customer_id'] ?? null;

if (!is_string($customerId) && !is_int($customerId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุรหัสลูกค้า"
    ]);
    exit;
}

if (!preg_match('/^\d+$/', (string) $customerId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "รหัสลูกค้าไม่ถูกต้อง"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = :customer_id");
    $stmt->execute([':customer_id' => $customerId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "ไม่พบข้อมูลลูกค้า"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "ลบข้อมูลลูกค้าเรียบร้อย"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "ไม่สามารถลบข้อมูลลูกค้าได้"
    ]);
}
?>
