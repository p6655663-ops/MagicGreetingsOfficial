<?php
session_start();

$servername = "127.0.1.27";
$username = "root";
$password = "";
$dbname = "registerUser";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
if ($conn->connect_error) {
    die("Ошибка: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<body style='background:#1e1e2e; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2>❌ Доступ запрещен!</h2>
            <p>Эта страница только для Шефа.</p>
            <a href='admin1.php' style='color:#a29bfe;'>Вернуться в кабинет</a>
         </body>");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['pass']);
    $role  = $_POST['role'];

    if ($login && $email && $pass) {
        $passHash = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (login, email, pass, role) VALUES ('$login', '$email', '$passHash', '$role')";

        if ($conn->query($sql) === TRUE) {
            $msg = "<div class='success-msg'>✅ Пользователь <b>$login</b> добавлен!</div>";
        } else {
            $msg = "<div class='error-msg'>❌ Ошибка: " . $conn->error . "</div>";
        }
    } else {
        $msg = "<div class='error-msg'>Заполните все поля!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet">
    <style>
       
        :root {
            --primary: #6C5DD3;
            --accent: #FF75C3;
            --bg-gradient: linear-gradient(135deg, #2E0249 0%, #570A57 50%, #A91079 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Varela Round", sans-serif;
            background: var(--bg-gradient);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
        }

       
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: -3;
        }

        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(32px);
            opacity: 0.4;
            animation: float 10s infinite alternate;
            z-index: -2;
        }

        .one {
            width: 400px;
            height: 400px;
            background: #FFD93D;
            top: -100px;
            left: -100px;
        }

        .two {
            width: 500px;
            height: 500px;
            background: #6C5DD3;
            bottom: -100px;
            right: -100px;
        }

        @keyframes float {
            from {
                transform: translate(0, 0);
            }

            to {
                transform: translate(20px, -20px);
            }
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 40px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        h2 {
            font-family: "Fredoka One", cursive;
            margin-bottom: 10px;
            font-size: 32px;
            color: #FFD93D;
        }

        p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
        }

        input,
        select {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            outline: none;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        option {
            background: #2E0249;
        }

    

        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            font-family: "Fredoka One", cursive;
        }

        button:hover {
            background: #5a4bcf;
            transform: translateY(-3px);
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .success-msg {
            color: #55efc4;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .error-msg {
            color: #ff7675;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="bg-orb one"></div>
    <div class="bg-orb two"></div>

    <div class="admin-card">
        <h2>👑 Админ-панель</h2>
        <p>Добавление нового сотрудника</p>

        <?= $msg ?>

        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="pass" placeholder="Пароль" required>

            <select name="role">
                <option value="user">Обычный юзер (user)</option>
                <option value="admin">Администратор (admin)</option>
            </select>

            <button type="submit">Создать</button>
        </form>

        <a href="admin1.php" class="back-link">⬅️ Вернуться в кабинет</a>
    </div>
</body>

</html>