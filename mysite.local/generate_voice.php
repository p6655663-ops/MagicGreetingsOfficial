<?php

ob_start();

error_reporting(0);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $text = $input['text'] ?? '';
    $hero = $input['hero'] ?? '';

    $lang = $input['lang'] ?? 'ru';

    if (!$text) {
        throw new Exception('Нет текста для озвучки!');
    }

    $text = mb_substr($text, 0, 200);
    $encodedText = urlencode($text);

    $googleUrl = "https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&q={$encodedText}&tl={$lang}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $audioData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode != 200 || !$audioData) {
        throw new Exception("Ошибка Google TTS: Код $httpCode. " . $curlError);
    }

    $filename = "voice_" . time() . "_" . rand(1000, 9999) . ".mp3";
    $savePath = __DIR__ . '/' . $filename;

    if (!file_put_contents($savePath, $audioData)) {
        throw new Exception("Не могу сохранить файл на сервер.");
    }

    ob_clean();

    echo json_encode([
        'success' => true,
        'audio_url' => $filename
    ]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
