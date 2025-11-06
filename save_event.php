<?php
header('Content-Type: application/json; charset=utf-8');
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$db = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(["error" => "Ошибка подключения к бд: " . $e->getMessage()]);
    exit;
}

$fio = $_POST['fio'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$eventType = $_POST['eventType'] ?? '';
$eventDate = $_POST['eventDate'] ?? '';
$eventTime = $_POST['eventTime'] ?? '';
$address = $_POST['address'] ?? '';
$createdAt = $_POST['createdAt'] ?? date('Y-m-d H:i:s');


if (!$fio || !$email || !$phone || !$eventType || !$eventDate || !$eventTime || !$address) {
    echo json_encode(["error" => "Заполните все поля формы"]);
    exit;
}


$currentDate = date('Y-m-d');
if ($eventDate < $currentDate) {
    echo json_encode(["error" => "Нельзя выбрать дату в прошлом"]);
    exit;
}


// $sqlCheckSlot = "SELECT COUNT(*) as count FROM orders WHERE event_date = :event_date AND event_time = :event_time";
// $stmtCheckSlot = $pdo->prepare($sqlCheckSlot);
// $stmtCheckSlot->execute([
//     ':event_date' => $eventDate,
//     ':event_time' => $eventTime
// ]);
// $result = $stmtCheckSlot->fetch(PDO::FETCH_ASSOC);

// if ($result['count'] > 0) {
//     echo json_encode(["error" => "Выбранный вами слот доставки уже занят. Пожалуйста, выберите другой."]);
//     exit;
// }

$sql = "INSERT INTO orders (fio, email, phone, event_type, event_date, event_time, address, created_at) VALUES (:fio, :email, :phone, :event_type, :event_date, :event_time, :address, :created_at)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':fio' => $fio,
        ':email' => $email,
        ':phone' => $phone,
        ':event_type' => $eventType,
        ':event_date' => $eventDate,
        ':event_time' => $eventTime,
        ':address' => $address,
        ':created_at' => $createdAt
    ]);
    echo json_encode(["success" => "Благодарим за заказ, мы с вами свяжемся в ближайшее время!"]);
} catch (\PDOException $e) {
    echo json_encode(["error" => "Ошибка запроса (Проверь серверный код): " . $e->getMessage()]);
}
?>
