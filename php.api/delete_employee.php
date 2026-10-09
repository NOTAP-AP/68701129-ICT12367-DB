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
$employeeId = $data['emp_id'] ?? null;

if (!is_string($employeeId) && !is_int($employeeId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุรหัสพนักงาน"
    ]);
    exit;
}

if (!preg_match('/^\d+$/', (string) $employeeId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "รหัสพนักงานไม่ถูกต้อง"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM employees WHERE emp_id = :emp_id");
    $stmt->execute([':emp_id' => $employeeId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "ไม่พบข้อมูลพนักงาน"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "ลบข้อมูลพนักงานเรียบร้อย"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "ไม่สามารถลบข้อมูลพนักงานได้"
    ]);
}
?>
