<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

function respond($success, $message = '', $data = null, $status = 200) {
    http_response_code($status);
    $response = ["success" => $success];
    if ($message !== '') {
        $response["message"] = $message;
    }
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

function readContactInput() {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!is_array($data)) {
        respond(false, "รูปแบบข้อมูลไม่ถูกต้อง", null, 400);
    }

    $fields = ["fullname", "subject", "detail", "email"];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
            respond(false, "กรุณากรอกข้อมูลให้ครบทุกช่อง", null, 400);
        }
        $data[$field] = trim($data[$field]);
    }

    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        respond(false, "รูปแบบอีเมลไม่ถูกต้อง", null, 400);
    }

    return $data;
}

try {
    $method = $_SERVER["REQUEST_METHOD"];

    if ($method === "GET") {
        $stmt = $conn->query("SELECT contact_id, fullname, subject, detail, email, created_at FROM contacts ORDER BY contact_id DESC");
        respond(true, "", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($method === "POST") {
        $data = readContactInput();
        $stmt = $conn->prepare(
            "INSERT INTO contacts (fullname, subject, detail, email)
             VALUES (:fullname, :subject, :detail, :email)"
        );
        $stmt->execute([
            ":fullname" => $data["fullname"],
            ":subject" => $data["subject"],
            ":detail" => $data["detail"],
            ":email" => $data["email"]
        ]);
        respond(true, "เพิ่มข้อมูลผู้ติดต่อเรียบร้อย");
    }

    if ($method === "PUT") {
        $data = readContactInput();
        $contactId = filter_var($data["contact_id"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($contactId === false || $contactId === null) {
            respond(false, "รหัสผู้ติดต่อไม่ถูกต้อง", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE contacts
             SET fullname = :fullname, subject = :subject, detail = :detail, email = :email
             WHERE contact_id = :contact_id"
        );
        $stmt->execute([
            ":fullname" => $data["fullname"],
            ":subject" => $data["subject"],
            ":detail" => $data["detail"],
            ":email" => $data["email"],
            ":contact_id" => $contactId
        ]);

        if ($stmt->rowCount() === 0) {
            $exists = $conn->prepare("SELECT 1 FROM contacts WHERE contact_id = :contact_id");
            $exists->execute([":contact_id" => $contactId]);
            if (!$exists->fetchColumn()) {
                respond(false, "ไม่พบข้อมูลผู้ติดต่อ", null, 404);
            }
        }
        respond(true, "แก้ไขข้อมูลผู้ติดต่อเรียบร้อย");
    }

    if ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);
        $contactId = filter_var($data["contact_id"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($contactId === false || $contactId === null) {
            respond(false, "รหัสผู้ติดต่อไม่ถูกต้อง", null, 400);
        }

        $stmt = $conn->prepare("DELETE FROM contacts WHERE contact_id = :contact_id");
        $stmt->execute([":contact_id" => $contactId]);
        if ($stmt->rowCount() === 0) {
            respond(false, "ไม่พบข้อมูลผู้ติดต่อ", null, 404);
        }
        respond(true, "ลบข้อมูลผู้ติดต่อเรียบร้อย");
    }

    header("Allow: GET, POST, PUT, DELETE, OPTIONS");
    respond(false, "Method ไม่ถูกต้อง", null, 405);
} catch (PDOException $e) {
    error_log("Contacts CRUD failed: " . $e->getMessage());
    respond(false, "เกิดข้อผิดพลาดในการจัดการข้อมูลผู้ติดต่อ", null, 500);
}
?>
