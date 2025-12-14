<?php

// ВАШ КЛЮЧ УЖЕ ВСТАВЛЕН СЮДА:
define('GEMINI_API_KEY', 'AIzaSyAj3JGEx3dLF-b3nftp76vobIlSSxiQyQI');

function generate_greeting($prompt)
{
    // Используем константу с ключом
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;

    $data = json_encode([
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'config' => [
            'temperature' => 0.9,
            'maxOutputTokens' => 800,
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return "Ошибка cURL: " . curl_error($ch);
    }
    curl_close($ch);

    $json = json_decode($response, true);

    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        return $json['candidates'][0]['content']['parts'][0]['text'];
    } else {
        return "Ошибка API: " . ($json['error']['message'] ?? 'Неизвестная ошибка');
    }
}
