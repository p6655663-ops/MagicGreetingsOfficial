<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Тест Gemini + Почта</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 50px;
            background: #e0e0e0;
        }

        .box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            margin: 0 auto;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: orange;
            border: none;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2 style="text-align:center;">Тестовый пульт</h2>
        <p style="text-align:center; font-size: 12px; color: #666;">Эта форма отправит данные в submit.php</p>

        <form action="submit.php" method="post">

            <label>Имя (для теста ИИ):</label>
            <input type="text" name="name" value="Тестер Василий">

            <label>Почта (куда придет письмо):</label>
            <input type="email" name="email" placeholder="test@test.com">

            <button type="submit">ПУСК (Отправить)</button>
        </form>
    </div>

</body>

</html>