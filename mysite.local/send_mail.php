<?php
// send_mail.php — ВЕРСИЯ "НАЙТИ ФАЙЛЫ ГДЕ УГОДНО"

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// --- БЛОК ПОДКЛЮЧЕНИЯ БИБЛИОТЕК ---

// Вариант 1: Файлы лежат прямо рядом с send_mail.php
if (file_exists('PHPMailer.php')) {
    require 'Exception.php';
    require 'PHPMailer.php';
    require 'SMTP.php';
}
// Вариант 2: Файлы лежат в папке PHPMailer (без src)
elseif (file_exists('PHPMailer/PHPMailer.php')) {
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
}
// Вариант 3: Файлы лежат в папке PHPMailer/src (стандарт)
elseif (file_exists('PHPMailer/src/PHPMailer.php')) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
} else {
    // Если ничего не нашли — останавливаемся и говорим об этом
    echo json_encode(['success' => false, 'error' => 'Файлы PHPMailer не найдены! Положите PHPMailer.php, SMTP.php и Exception.php рядом с этим файлом.']);
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- ОСНОВНАЯ ЛОГИКА ---

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Нет данных']);
    exit;
}

$name = $input['name'];
$hero = $input['hero'];
$email = $input['email'];

$params = http_build_query(['n' => $name, 'h' => $hero, 't' => time()]);
$videoLink = "http://" . $_SERVER['HTTP_HOST'] . "/view_video.php?" . $params;

$mail = new PHPMailer(true);

try {
    // =========================================================
    // НАСТРОЙКИ ВАШЕЙ ПОЧТЫ (ЗАПОЛНИТЕ ЗАНОВО!)
    // =========================================================
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->SMTPAuth   = true;

    // --- ПРИМЕР ДЛЯ YANDEX ---
    $mail->Host       = 'smtp.yandex.ru';
    $mail->Username   = 'codpodtverghdeniya@yandex.ru';
    $mail->Password   = 'jbgxybofkbkgfoqs'; // <--- Вставьте пароль сюда
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // --- ПРИМЕР ДЛЯ MAIL.RU ---
    // $mail->Host       = 'ssl://smtp.mail.ru';
    // $mail->Username   = 'ВАША_ПОЧТА@mail.ru';
    // $mail->Password   = 'ВАШ_ПАРОЛЬ_ПРИЛОЖЕНИЯ';
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    // $mail->Port       = 465;

    $mail->setFrom($mail->Username, 'MagicGreetings');
    $mail->addAddress($email);

    // Письмо
    $mail->isHTML(true);
    $mail->Subject = "Видео для $name";
    $mail->Body    = "
        <h2>Поздравление готово!</h2>
        <p>Нажмите на кнопку, чтобы посмотреть:</p>
        <a href='$videoLink' style='background:blue; color:white; padding:10px 20px; text-decoration:none;'>Смотреть</a>
    ";

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => "Ошибка отправки: " . $mail->ErrorInfo]);
}
