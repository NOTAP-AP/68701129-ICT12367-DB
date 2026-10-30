<?php
include 'condb.php';

header('Content-Type: application/json; charset=UTF-8');

function sendJson($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function uploadAssetImage()
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('อัปโหลดรูปภาพไม่สำเร็จ');
    }

    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'], true)) {
        throw new RuntimeException('ไฟล์รูปภาพต้องเป็น JPG, PNG, GIF, WEBP หรือ JFIF');
    }

    $directory = __DIR__ . '/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('ไม่สามารถสร้างโฟลเดอร์เก็บรูปภาพได้');
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $extension;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {
        throw new RuntimeException('บันทึกรูปภาพไม่สำเร็จ');
    }
    return $filename;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $conn->query('SELECT * FROM assets ORDER BY asset_id DESC');
        sendJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(['success' => false, 'error' => 'ไม่รองรับคำขอนี้'], 405);
    }

    $action = $_POST['action'] ?? '';
    $assetCode = trim($_POST['asset_code'] ?? '');
    $assetName = trim($_POST['asset_name'] ?? '');
    $purchaseDate = trim($_POST['purchase_date'] ?? '');
    $price = $_POST['price'] ?? '';

    if ($action === 'add') {
        if ($assetCode === '' || $assetName === '' || !is_numeric($price)) {
            sendJson(['success' => false, 'error' => 'กรุณากรอกรหัส ชื่อ และราคา สินค้า ให้ถูกต้อง'], 400);
        }

        $image = uploadAssetImage();
        $stmt = $conn->prepare(
            'INSERT INTO assets (asset_code, asset_name, purchase_date, price, image)
             VALUES (:asset_code, :asset_name, :purchase_date, :price, :image)'
        );
        $stmt->execute([
            ':asset_code' => $assetCode,
            ':asset_name' => $assetName,
            ':purchase_date' => $purchaseDate !== '' ? $purchaseDate : null,
            ':price' => $price,
            ':image' => $image,
        ]);
        sendJson(['success' => true, 'message' => 'เพิ่ม Asset สำเร็จ']);
    }

    if ($action === 'update') {
        $id = filter_var($_POST['asset_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $assetCode === '' || $assetName === '' || !is_numeric($price)) {
            sendJson(['success' => false, 'error' => 'กรุณากรอกข้อมูล Asset ให้ถูกต้อง'], 400);
        }

        $image = uploadAssetImage();
        $sql = 'UPDATE assets SET asset_code = :asset_code, asset_name = :asset_name,
                purchase_date = :purchase_date, price = :price';
        $params = [
            ':asset_code' => $assetCode,
            ':asset_name' => $assetName,
            ':purchase_date' => $purchaseDate !== '' ? $purchaseDate : null,
            ':price' => $price,
            ':id' => $id,
        ];
        if ($image !== null) {
            $sql .= ', image = :image';
            $params[':image'] = $image;
        }
        $sql .= ' WHERE asset_id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        sendJson(['success' => true, 'message' => 'แก้ไข Asset สำเร็จ']);
    }

    if ($action === 'delete') {
        $id = filter_var($_POST['asset_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            sendJson(['success' => false, 'error' => 'ไม่พบรหัส Asset'], 400);
        }
        $stmt = $conn->prepare('DELETE FROM assets WHERE asset_id = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) {
            sendJson(['success' => false, 'error' => 'ไม่พบ Asset ที่ต้องการลบ'], 404);
        }
        sendJson(['success' => true, 'message' => 'ลบ Asset สำเร็จ']);
    }

    sendJson(['success' => false, 'error' => 'Action ไม่ถูกต้อง'], 400);
} catch (Throwable $error) {
    error_log('Asset API error: ' . $error->getMessage());
    sendJson(['success' => false, 'error' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่'], 500);
}
