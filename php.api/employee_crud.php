<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ดึงข้อมูลพนักงานทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT emp_id AS employee_id, firstName, lastName, phone, username FROM employees ORDER BY emp_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // เพิ่มข้อมูลพนักงาน
    elseif ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        if (empty($data["firstName"]) || empty($data["lastName"]) || empty($data["phone"]) || empty($data["username"]) || empty($data["password"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $password_hash = password_hash($data["password"], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO employees (firstName, lastName, phone, username, password)
                                VALUES (:firstName, :lastName, :phone, :username, :password)");
        $stmt->execute([
            ":firstName" => $data["firstName"],
            ":lastName" => $data["lastName"],
            ":phone" => $data["phone"],
            ":username" => $data["username"],
            ":password" => $password_hash
        ]);
        echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลพนักงานเรียบร้อย"]);
    }

    // แก้ไขข้อมูลพนักงาน
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        if (empty($data["employee_id"]) || empty($data["firstName"]) || empty($data["lastName"]) || empty($data["phone"]) || empty($data["username"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $employee_id = intval($data["employee_id"]);

        if (!empty($data["password"])) {
            $password_hash = password_hash($data["password"], PASSWORD_BCRYPT);
            $sql = "UPDATE employees 
                    SET firstName = :firstName, 
                        lastName = :lastName, 
                        phone = :phone, 
                        username = :username,
                        password = :password
                    WHERE emp_id = :id";
        } else {
            $sql = "UPDATE employees 
                    SET firstName = :firstName, 
                        lastName = :lastName, 
                        phone = :phone, 
                        username = :username
                    WHERE emp_id = :id";
        }

        $stmt = $conn->prepare($sql);
        $params = [
            ":firstName" => $data["firstName"],
            ":lastName" => $data["lastName"],
            ":phone" => $data["phone"],
            ":username" => $data["username"],
            ":id" => $employee_id
        ];
        if (!empty($data["password"])) {
            $params[":password"] = $password_hash;
        }
        $stmt->execute($params);
        echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
    }

    // ลบข้อมูลพนักงาน
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        if (!isset($data["employee_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า employee_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM employees WHERE emp_id = :id");
        $stmt->bindParam(":id", $data["employee_id"], PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลพนักงาน"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
