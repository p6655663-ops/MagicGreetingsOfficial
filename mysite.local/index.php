<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

$servername = "127.0.1.27";
$port = 3306;
$username = "root";
$password = "";
$dbname = "registerUser";

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$message = "";
$showPopup = false;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signUp'])) {
    $login = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passConfirm = trim($_POST['password_confirm']);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if (empty($login) || empty($email) || empty($password)) {
        $message = "Заполните все поля!";
    } elseif ($password !== $passConfirm) {
        $message = "Пароли не совпадают!";
    } elseif ($check->num_rows > 0) {
        $message = "Этот Email уже занят!";
    } else {
        $verificationCode = rand(100000, 999999);
        $_SESSION['temp_user'] = ['login' => $login, 'email' => $email, 'pass' => $password, 'code' => $verificationCode];

        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.yandex.ru';
            $mail->SMTPAuth = true;
            $mail->Username = 'codpodtverghdeniya@yandex.ru';
            $mail->Password = 'jbgxybofkbkgfoqs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($mail->Username, 'MagicGreetings Auth');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Код подтверждения';
            $mail->Body = "<h2>Ваш код: <span style='color:#6C5DD3'>$verificationCode</span></h2>";
            $mail->send();
            $showPopup = true;
            $message = "Код отправлен на $email";
        } catch (Exception $e) {
            $message = "Ошибка отправки: {$mail->ErrorInfo}";
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verifyCodeAction'])) {
    $userCode = trim($_POST['digit_code']);
    if (isset($_SESSION['temp_user'])) {
        if ($userCode == $_SESSION['temp_user']['code']) {
            $login = $_SESSION['temp_user']['login'];
            $email = $_SESSION['temp_user']['email'];
            $passHash = password_hash($_SESSION['temp_user']['pass'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (login, email, pass) VALUES ('$login', '$email', '$passHash')";
            if ($conn->query($sql) === TRUE) {
                $message = "Аккаунт создан! Теперь войдите.";
                unset($_SESSION['temp_user']);
            } else {
                $message = "Ошибка базы: " . $conn->error;
            }
        } else {
            $message = "Неверный код!";
            $showPopup = true;
        }
    } else {
        $message = "Время сессии истекло.";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Введите данные!";
    } else {
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['pass'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                echo "<script>window.location.href = 'magic.php';</script>";
            } else {
                $message = "Неверный пароль!";
            }
        } else {
            $message = "Пользователь не найден";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в MagicGreetings</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        :root {
            --primary: #6C5DD3;
            --accent: #FF75C3;
            --bg-gradient: linear-gradient(135deg, #2E0249 0%, #570A57 50%, #A91079 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --radius: 24px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Varela Round", sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.10) 1px, transparent 1px), radial-gradient(rgba(255, 255, 255, 0.10) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            pointer-events: none;
            z-index: -3;
        }

        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
        }

        .bg-orb {
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(32px);
            opacity: 0.35;
            animation: floatOrb 14s ease-in-out infinite;
        }

        .bg-orb.one {
            left: -140px;
            top: 10vh;
            background: radial-gradient(circle at 30% 30%, #FFD93D, transparent 60%);
        }

        .bg-orb.two {
            right: -180px;
            top: 32vh;
            background: radial-gradient(circle at 30% 30%, #FF75C3, transparent 60%);
            animation-duration: 18s;
        }

        .bg-orb.three {
            left: 18vw;
            bottom: -240px;
            background: radial-gradient(circle at 30% 30%, #6C5DD3, transparent 60%);
            animation-duration: 20s;
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translate3d(0, 0, 0) scale(1);
            }

            50% {
                transform: translate3d(0, -22px, 0) scale(1.06);
            }
        }

        .stars {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            opacity: 0.9;
            background: radial-gradient(2px 2px at 10% 20%, rgba(255, 255, 255, .55), transparent 60%);
            animation: twinkle 6s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.75;
            }

            50% {
                opacity: 1;
            }
        }

       
        .php-msg {
            position: absolute;
            top: 20px;
            z-index: 2000;
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        
        .wrapper {
            position: relative;
            width: 900px;
            height: 550px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            z-index: 10;
        }

        .form-box {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 40px;
            transition: all 0.6s ease-in-out;
            z-index: 100;
        }

        .form-box.login {
            left: 0;
        }

        .form-box.register {
            right: 0;
            transform: translateX(100%);
            opacity: 0;
            pointer-events: none;
        }

        .wrapper.active .form-box.login {
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .wrapper.active .form-box.register {
            transform: translateX(0);
            opacity: 1;
            pointer-events: auto;
        }

        h2 {
            font-family: "Fredoka One", cursive;
            font-size: 36px;
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 20px 0;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 0 45px 0 15px;
            transition: 0.3s;
            font-family: "Varela Round", sans-serif;
        }

        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .input-box input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #fff;
        }

        .input-box i {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: #fff;
            font-size: 20px;
        }

        .btn {
            width: 100%;
            height: 50px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            margin-top: 10px;
            transition: 0.3s;
            font-family: "Fredoka One", cursive;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(108, 93, 211, 0.4);
        }

        .btn:hover {
            background: #5a4bcf;
            transform: translateY(-2px);
        }

        .linkTxt {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .linkTxt a {
            color: #FFD93D;
            font-weight: 700;
            text-decoration: none;
            margin-left: 5px;
        }

        .linkTxt a:hover {
            text-decoration: underline;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            z-index: 100;
            transition: transform 0.6s ease-in-out;
            pointer-events: none;
        }

        .overlay-glass {
            position: absolute;
            width: 200%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            backdrop-filter: blur(30px);
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .wrapper.active .overlay-container {
            transform: translateX(-100%);
        }

        .wrapper.active .overlay-glass {
            transform: translateX(50%);
        }

        .info-panel {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 40px;
            z-index: 110;
            transition: 0.6s ease-in-out;
        }

        .info-panel.left-panel {
            left: 0;
            pointer-events: none;
            transform: translateX(-20%);
            opacity: 0;
        }

        .info-panel.right-panel {
            right: 0;
        }

        .wrapper.active .info-panel.left-panel {
            pointer-events: auto;
            transform: translateX(0);
            opacity: 1;
        }

        .wrapper.active .info-panel.right-panel {
            pointer-events: none;
            transform: translateX(20%);
            opacity: 0;
        }

        .info-panel h3 {
            font-family: "Fredoka One", cursive;
            font-size: 32px;
            color: #fff;
            margin-bottom: 10px;
        }

        .info-panel p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        
        .overlay-black {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 1500;
            opacity: 0;
            pointer-events: none;
            transition: .3s;
        }

        .overlay-black.active {
            opacity: 1;
            pointer-events: auto;
        }

        .popup-box {
            position: fixed;
            top: -100%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            text-align: center;
            z-index: 2000;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            transition: top 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        }

        .popup-box.active {
            top: 50%;
        }

        .popup-box h2 {
            color: var(--primary);
            text-shadow: none;
            margin-bottom: 10px;
        }

        .code-input {
            width: 100%;
            height: 50px;
            background: #f0f2f5;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 24px;
            color: #333;
            text-align: center;
            letter-spacing: 5px;
            margin: 20px 0;
            outline: none;
            transition: .3s;
        }

        .code-input:focus {
            border-color: var(--primary);
        }
    </style>
</head>

<body>

    <?php if (!empty($message)): ?>
        <div class="php-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="bg-orbs">
        <div class="bg-orb one"></div>
        <div class="bg-orb two"></div>
        <div class="bg-orb three"></div>
    </div>
    <div class="stars"></div>

    <div class="overlay-black <?php if ($showPopup) echo 'active'; ?>"></div>
    <div class="popup-box <?php if ($showPopup) echo 'active'; ?>">
        <h2>Проверка</h2>
        <p style="color:#555;">Введите код из Email</p>
        <form method="POST">
            <input type="text" name="digit_code" class="code-input" placeholder="XXXXXX" maxlength="6" required>
            <button type="submit" name="verifyCodeAction" class="btn">Подтвердить</button>
        </form>
        <a href="index.php" style="display:block; margin-top:15px; color:#999; text-decoration:none;">Закрыть</a>
    </div>

    <div class="wrapper <?php if ($showPopup) echo 'active'; ?>">

        <div class="overlay-container">
            <div class="overlay-glass"></div>
        </div>

        <div class="info-panel left-panel">
            <h3>Привет, друг!</h3>
            <p>Введи свои данные, чтобы начать путешествие с нами.</p>
        </div>

        <div class="info-panel right-panel">
            <h3>С возвращением!</h3>
            <p>Чтобы оставаться на связи, войди под своим логином.</p>
        </div>

        <div class="form-box login">
            <h2>Вход</h2>
            <form action="" method="POST">
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Пароль" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="signIn" class="btn">Войти</button>
                <div class="linkTxt">
                    Нет аккаунта? <a href="#" class="register-link">Регистрация</a>
                </div>
            </form>
        </div>

        <div class="form-box register">
            <h2>Создать аккаунт</h2>
            <form action="" method="POST">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Имя" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Пароль" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirm" placeholder="Повтор пароля" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="signUp" class="btn">Зарегистрироваться</button>
                <div class="linkTxt">
                    Уже есть аккаунт? <a href="#" class="login-link">Войти</a>
                </div>
            </form>
        </div>

    </div>

    <script>
        const wrapper = document.querySelector('.wrapper');
        const registerLink = document.querySelector('.register-link');
        const loginLink = document.querySelector('.login-link');

        registerLink.onclick = (e) => {
            e.preventDefault();
            wrapper.classList.add('active');
        }

        loginLink.onclick = (e) => {
            e.preventDefault();
            wrapper.classList.remove('active');
        }
    </script>
</body>

</html>