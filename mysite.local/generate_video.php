<?php
// generate_video.php (СНЕГУРОЧКА КРУПНЫМ ПЛАНОМ)

error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(300);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ВАШ КЛЮЧ
$apiKey = "cDY2NTU2NjNAZ21haWwuY29t:ENtXxUJc7zLVd-ZO6u6Cm";

$input = json_decode(file_get_contents('php://input'), true);

// 1. ВАШ ТЕКСТ (Защита от пустоты)
$textToSpeak = $input['text'] ?? "С Новым Годом! Пусть сбудутся все мечты!";
// Обрезаем текст, если он слишком длинный (чтобы не перегрузить сервер)
if (mb_strlen($textToSpeak) > 300) {
    $textToSpeak = mb_substr($textToSpeak, 0, 300);
}

// 2. ВЫБИРАЕМ АВАТАРА
$hero = $input['hero'] ?? 'snegurochka';
$avatarUrl = "";

if ($hero === 'snegurochka') {
    // --- ИДЕАЛЬНАЯ ССЫЛКА ---
    // Это фото с Pixabay: Девушка в белой шапке, лицо КРУПНО, смотрит ПРЯМО.
    // На этом фото D-ID не должен падать.
    $avatarUrl = "https://cdn.pixabay.com/photo/2017/11/06/13/45/cap-2923682_640.jpg";
} elseif ($hero === 'robot') {
    $avatarUrl = "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Humanoid_Robot_Jia_Jia.jpg/320px-Humanoid_Robot_Jia_Jia.jpg";
} else {
    // Если всё сломается - Элис
    $avatarUrl = "https://d-id-public-bucket.s3.us-west-2.amazonaws.com/alice.jpg";
}

// 3. ОТПРАВЛЯЕМ ЗАПРОС
$chTalk = curl_init();

$postData = [
    "script" => [
        "type" => "text",
        "input" => $textToSpeak,
        "provider" => [
            "type" => "microsoft",
            "voice_id" => "ru-RU-SvetlanaNeural" // Русский женский голос
        ]
    ],
    "source_url" => $avatarUrl,
    "config" => [
        "stitch" => false, // Выключаем сшивание (это критично для своих картинок)
        "result_format" => "mp4"
    ]
];

curl_setopt_array($chTalk, [
    CURLOPT_URL => "https://api.d-id.com/talks",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    CURLOPT_USERPWD => $apiKey,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0
]);

$talkResponse = curl_exec($chTalk);
$httpCodeTalk = curl_getinfo($chTalk, CURLINFO_HTTP_CODE);

if ($httpCodeTalk != 201) {
    echo json_encode(['success' => false, 'error' => "D-ID Error ($httpCodeTalk): " . $talkResponse]);
    exit;
}

$talkData = json_decode($talkResponse, true);
$talkId = $talkData['id'];
curl_close($chTalk);

// 4. ОЖИДАНИЕ
$attempts = 0;
$videoUrl = "";

while ($attempts < 40) {
    sleep(5); // Ждем чуть дольше (5 сек)
    $attempts++;

    $chCheck = curl_init();
    curl_setopt_array($chCheck, [
        CURLOPT_URL => "https://api.d-id.com/talks/" . $talkId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $apiKey,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);

    $resp = curl_exec($chCheck);
    $checkData = json_decode($resp, true);
    curl_close($chCheck);

    if (isset($checkData['status']) && $checkData['status'] == 'done') {
        $videoUrl = $checkData['result_url'];
        break;
    }
    if (isset($checkData['status']) && $checkData['status'] == 'error') {
        // Если снова ошибка - показываем детали
        $err = isset($checkData['error']) ? json_encode($checkData['error']) : 'Unknown';
        echo json_encode(['success' => false, 'error' => "Render Failed: $err"]);
        exit;
    }
}

if ($videoUrl) {
    echo json_encode(['success' => true, 'video_url' => $videoUrl]);
} else {
    echo json_encode(['success' => false, 'error' => 'Таймаут']);
}
