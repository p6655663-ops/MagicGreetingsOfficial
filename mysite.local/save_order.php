<?php
ob_start();
ob_clean();

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "127.0.1.27";
$port = 3306;
$username = "root";
$password = "";
$dbname = "registerUser";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);

$audio_url = $input['audio_url'] ?? '';
$hero = $input['hero'] ?? 'snegurochka';
$name = $input['recipient_name'] ?? 'Неизвестно';
$age = $input['recipient_age'] ?? 0;

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';


$share_code = uniqid() . bin2hex(random_bytes(4));

$hero = $conn->real_escape_string($hero);
$name = $conn->real_escape_string($name);
$audio_url = $conn->real_escape_string($audio_url);
$share_code = $conn->real_escape_string($share_code);

$sql = "INSERT INTO orders (user_id, hero, recipient_name, recipient_age, audio_url, share_code) 
        VALUES ($user_id, '$hero', '$name', $age, '$audio_url', '$share_code')";

ob_clean(); 

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'share_code' => $share_code]);
} else {
    echo json_encode(['success' => false, 'error' => 'SQL Error: ' . $conn->error]);
}

$conn->close();
exit;
 