<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "รูปแบบข้อมูลไม่ถูกต้อง"
    ]);
    exit;
}

$fields = ['fullname', 'subject', 'detail', 'email'];
foreach ($fields as $field) {
    if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "กรุณากรอกข้อมูลให้ครบทุกช่อง"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $data[$field] = trim($data[$field]);
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "รูปแบบอีเมลไม่ถูกต้อง"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO contacts
            (subject, detail, fullname, email)
            VALUES
            (:subject, :detail, :fullname, :email)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':subject'    => $data['subject'],
        ':detail'     => $data['detail'],
        ':fullname'   => $data['fullname'],
        ':email'      => $data['email'],
    ]);

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "เพิ่มข้อมูลเรียบร้อย"
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('Adding contact failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบตาราง contacts ในฐานข้อมูล"
    ], JSON_UNESCAPED_UNICODE);
}
