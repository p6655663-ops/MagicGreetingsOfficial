<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$servername = "127.0.1.27";
$username = "root";
$password = "";
$dbname = "registerUser";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$orders_sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC";
$orders_result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
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
            justify-content: flex-start;
            color: #fff;
            overflow-x: hidden;
            padding: 60px 20px;
            position: relative;
        }

        .settings-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: 0.3s;
            z-index: 9999;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .settings-btn:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(90deg) scale(1.1);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-window {
            background: #fff;
            color: #333;
            width: 90%;
            max-width: 350px;
            padding: 30px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .lang-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .lang-btn {
            padding: 15px;
            border-radius: 15px;
            border: 2px solid #eee;
            background: #f9f9f9;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            transition: 0.2s;
        }

        .lang-btn:hover {
            background: #eee;
        }

        .lang-btn.active {
            border-color: var(--primary);
            background: rgba(108, 93, 211, 0.1);
            color: var(--primary);
        }

        .close-modal {
            margin-top: 20px;
            background: #ff4757;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .cabinet-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 800px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        h1 {
            font-family: "Fredoka One", cursive;
            margin-bottom: 30px;
            font-size: 32px;
        }

        h2 {
            font-family: "Fredoka One", cursive;
            margin-bottom: 15px;
            margin-top: 30px;
            font-size: 24px;
            color: #FFD93D;
        }

        .user-info {
            text-align: left;
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .info-row {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .info-row span {
            color: #FFD93D;
            font-weight: bold;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .btn {
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }

        .btn-home {
            background: #fff;
            color: var(--primary);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .btn-admin {
            background: linear-gradient(45deg, #FFD93D, #FF75C3);
            color: #2d3436;
        }

        .btn-logout {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
            border: 1px solid #ff4757;
        }

        .btn-logout:hover {
            background: #ff4757;
            color: white;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 15px;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
        }

        .orders-table th {
            background: rgba(0, 0, 0, 0.3);
            font-family: "Fredoka One", cursive;
            color: #FF75C3;
            font-size: 16px;
        }

        .orders-table td a {
            color: #FFD93D;
            text-decoration: none;
            font-weight: bold;
        }

        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(32px);
            opacity: 0.4;
        }

        .one {
            width: 300px;
            height: 300px;
            background: #FFD93D;
            top: 10%;
            left: -50px;
            animation: float 10s infinite alternate;
        }

        .two {
            width: 400px;
            height: 400px;
            background: #6C5DD3;
            bottom: 10%;
            right: -50px;
            animation: float 12s infinite alternate-reverse;
        }

        @keyframes float {
            from {
                transform: translate(0, 0);
            }

            to {
                transform: translate(20px, -20px);
            }
        }
    </style>
</head>

<body>

    <div class="settings-btn" onclick="openSettings()">⚙️</div>

    <div id="settingsModal" class="modal-overlay">
        <div class="modal-window">
            <h2 style="color:#2E0249; margin-top:0;" id="txt_settings_title">Настройки</h2>
            <div class="lang-options">
                <div class="lang-btn active" id="btn_ru" onclick="selectLanguage('ru')">🇷🇺 Русский</div>
                <div class="lang-btn" id="btn_en" onclick="selectLanguage('en')">🇺🇸 English</div>
            </div>
            <button class="close-modal" onclick="closeSettings()" id="txt_close">Закрыть</button>
        </div>
    </div>

    <div class="bg-orbs">
        <div class="bg-orb one"></div>
        <div class="bg-orb two"></div>
    </div>

    <div class="cabinet-card">
        <h1 id="txt_cab_title">👤 Личный кабинет</h1>

        <div class="user-info">
            <div class="info-row"><span id="lbl_login">Логин:</span> <?php echo htmlspecialchars($user['login']); ?></div>
            <div class="info-row"><span id="lbl_email">Email:</span> <?php echo htmlspecialchars($user['email']); ?></div>
            <?php if ($user['role'] === 'admin'): ?>
                <div class="info-row"><span id="lbl_role" style="color:#ff7675">Роль:</span> <span style="color:#ff7675"><?php echo htmlspecialchars($user['role']); ?></span></div>
            <?php endif; ?>
        </div>

        <div class="btn-group">
            <a href="magic.php" class="btn btn-home" id="btn_create">✨ Создать</a>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-admin" id="btn_admin">👑 Админка</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-logout" id="btn_logout">Выйти</a>
        </div>

        <h2 id="txt_history">История заказов</h2>

        <?php if ($orders_result && $orders_result->num_rows > 0): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th id="th_recipient">Получатель</th>
                        <th id="th_hero">Герой</th>
                        <th id="th_link">Ссылка</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order_row = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order_row['recipient_name']); ?> (<?php echo $order_row['recipient_age']; ?>)</td>
                            <td><?php echo htmlspecialchars($order_row['hero']); ?></td>
                            <td>
                                <a href="share.php?code=<?php echo htmlspecialchars($order_row['share_code']); ?>" target="_blank">
                                    Link 🔗
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#ddd;" id="txt_no_orders">У вас пока нет заказов.</p>
        <?php endif; ?>
    </div>

    <script>
        let currentLang = localStorage.getItem('siteLang') || 'ru';

        const translations = {
            ru: {
                settings: "Настройки",
                close: "Закрыть",
                title: "👤 Личный кабинет",
                login: "Логин:",
                email: "Email:",
                role: "Роль:",
                create: "✨ Создать",
                admin: "👑 Админка",
                logout: "Выйти",
                history: "История заказов",
                recipient: "Получатель",
                hero: "Герой",
                link: "Ссылка",
                no_orders: "У вас пока нет заказов."
            },
            en: {
                settings: "Settings",
                close: "Close",
                title: "👤 Dashboard",
                login: "Login:",
                email: "Email:",
                role: "Role:",
                create: "✨ Create New",
                admin: "👑 Admin Panel",
                logout: "Logout",
                history: "Order History",
                recipient: "Recipient",
                hero: "Hero",
                link: "Link",
                no_orders: "You have no orders yet."
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            selectLanguage(currentLang, false);
        });

        function openSettings() {
            document.getElementById('settingsModal').classList.add('active');
        }

        function closeSettings() {
            document.getElementById('settingsModal').classList.remove('active');
        }

        function selectLanguage(lang, close = true) {
            currentLang = lang;
            localStorage.setItem('siteLang', lang);

            document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('btn_' + lang).classList.add('active');

            const t = translations[lang];
            document.getElementById('txt_settings_title').innerText = t.settings;
            document.getElementById('txt_close').innerText = t.close;
            document.getElementById('txt_cab_title').innerText = t.title;

            document.getElementById('lbl_login').innerText = t.login;
            document.getElementById('lbl_email').innerText = t.email;
            if (document.getElementById('lbl_role')) document.getElementById('lbl_role').innerText = t.role;

            document.getElementById('btn_create').innerText = t.create;
            if (document.getElementById('btn_admin')) document.getElementById('btn_admin').innerText = t.admin;
            document.getElementById('btn_logout').innerText = t.logout;

            document.getElementById('txt_history').innerText = t.history;
            if (document.getElementById('th_recipient')) document.getElementById('th_recipient').innerText = t.recipient;
            if (document.getElementById('th_hero')) document.getElementById('th_hero').innerText = t.hero;
            if (document.getElementById('th_link')) document.getElementById('th_link').innerText = t.link;
            if (document.getElementById('txt_no_orders')) document.getElementById('txt_no_orders').innerText = t.no_orders;

            if (close) setTimeout(closeSettings, 300);
        }
    </script>
</body>

</html>