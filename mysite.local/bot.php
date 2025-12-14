<?php
// ... Настройки PDO подключения (как в моем первом ответе) ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $occasion = $_POST['occasion'];
    $email = $_POST['email'];
    $delay_hours = 1; // Задержка в 1 час для демонстрации

    // Устанавливаем время отправки: текущее время + заданная задержка
    $scheduled_time = date('Y-m-d H:i:s', strtotime("+$delay_hours hour"));

    $sql = "INSERT INTO greetings (child_name, child_age, occasion, recipient_email, scheduled_at) 
            VALUES (:name, :age, :occasion, :email, :scheduled_at)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $name,
        'age' => $age,
        'occasion' => $occasion,
        'email' => $email,
        'scheduled_at' => $scheduled_time
    ]);

    echo "Заявка принята! Поздравление будет отправлено на $email в $scheduled_time.";
}
