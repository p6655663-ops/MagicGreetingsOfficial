<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MagicGreetings — Волшебные поздравления</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6C5DD3;
            --accent: #FF75C3;
            --bg-gradient: linear-gradient(135deg, #2E0249 0%, #570A57 50%, #A91079 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --radius: 24px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "Varela Round", sans-serif;
            background: var(--bg-gradient);
            margin: 0;
            padding: 0;
            color: #2d3436;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.10) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: -10;
        }

        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: -9;
            pointer-events: none;
            overflow: hidden;
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
            z-index: -8;
            pointer-events: none;
            opacity: 0.9;
            background: radial-gradient(2px 2px at 10% 20%, rgba(255, 255, 255, .55), transparent 60%);
            animation: twinkle 6s ease-in-out infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.75;
                filter: blur(0px);
            }

            50% {
                opacity: 1;
                filter: blur(0.2px);
            }
        }

        #snowCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        header,
        main,
        footer,
        .modal {
            position: relative;
            z-index: 10;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding-right: 30px;
        }

        .logo {
            font-family: "Fredoka One", cursive;
            font-size: 28px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .auth-btn {
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: 0.3s;
            backdrop-filter: blur(5px);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .auth-btn:hover {
            background: #fff;
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        main {
            max-width: 980px;
            margin: 0 auto;
            text-align: center;
            padding: 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: pageIn 0.55s cubic-bezier(.2, .9, .25, 1) both;
        }

        @keyframes pageIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-family: "Fredoka One", cursive;
            color: #fff;
            font-size: 2.4rem;
            margin: 0 0 26px;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .heroes-grid {
            display: flex;
            justify-content: center;
            gap: 26px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(6px);
            border-radius: var(--radius);
            padding: 18px 18px 16px;
            width: 210px;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.45);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: transform .35s, box-shadow .35s, border-color .25s;
            animation: cardIn .6s cubic-bezier(.2, .9, .25, 1) both;
        }

        .hero-card:nth-child(1) {
            animation-delay: .05s;
        }

        .hero-card:nth-child(2) {
            animation-delay: .10s;
        }

        .hero-card:nth-child(3) {
            animation-delay: .15s;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .hero-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 22px 45px rgba(0, 0, 0, 0.35);
        }

        .hero-card.selected {
            border-color: var(--accent);
            transform: scale(1.05);
            box-shadow: 0 0 0 6px rgba(255, 117, 195, .18), var(--shadow);
        }

        .hero-card.selected::after {
            content: "✓";
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--accent);
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            animation: popIn 0.25s ease;
        }

        @keyframes popIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .hero-card img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 6px 0 12px;
            filter: drop-shadow(0 6px 10px rgba(0, 0, 0, 0.12));
            transition: 0.25s;
        }

        .hero-card:hover img {
            transform: translateY(-2px) scale(1.03);
        }

        .hero-card h3 {
            margin: 0;
            color: var(--primary);
            font-weight: 900;
            font-size: 18px;
        }

        .btn-row {
            display: flex;
            justify-content: center;
            margin-top: 22px;
        }

        button {
            font-family: "Fredoka One", cursive;
            border: none;
            border-radius: 999px;
            padding: 16px 42px;
            cursor: pointer;
            transition: 0.22s;
        }

        #openModalBtn {
            background: linear-gradient(45deg, #FF75C3, #FFD93D);
            color: #4a0033;
            font-size: 20px;
            opacity: 0.6;
            filter: grayscale(0.8);
            pointer-events: none;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
            text-transform: uppercase;
        }

        #openModalBtn.active {
            opacity: 1;
            filter: none;
            pointer-events: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.04);
            }

            100% {
                transform: scale(1);
            }
        }

        #openModalBtn:hover {
            transform: scale(1.03);
            box-shadow: 0 16px 30px rgba(255, 117, 195, 0.35);
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            background-color: rgba(18, 1, 36, 0.65);
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity .22s ease;
            align-items: center;
            justify-content: center;
        }

        .modal.open {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px 30px;
            border-radius: 28px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            transform: scale(0.95);
            transition: transform .28s cubic-bezier(.2, .9, .25, 1);
        }

        .modal.open .modal-content {
            transform: scale(1);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 32px;
            color: #aaa;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: var(--accent);
            transform: rotate(90deg);
            transition: 0.2s;
        }

        h2 {
            font-family: "Fredoka One", cursive;
            color: var(--primary);
            text-align: center;
            margin: 0 0 20px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 800;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #eee;
            border-radius: 14px;
            font-size: 16px;
            font-family: "Varela Round", sans-serif;
            background: #f9f9f9;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(108, 93, 211, 0.10);
        }

        .submit-btn {
            width: 100%;
            background: var(--primary);
            color: #fff;
            padding: 16px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 900;
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(108, 93, 211, 0.28);
        }

        .submit-btn:hover {
            background: #5a4bcf;
            transform: translateY(-2px);
        }

        footer {
            margin-top: auto;
            padding: 20px 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #fff;
            backdrop-filter: blur(5px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mpit-block {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #fff;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .mpit-block:hover {
            transform: scale(1.05);
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
        }

        .mpit-title {
            font-family: "Fredoka One", cursive;
            font-size: 18px;
            margin: 0;
            letter-spacing: 1px;
            background: linear-gradient(to right, #fff, #FFD93D);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mpit-sub {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.5);
            background: transparent;
            transition: 0.3s;
            color: #fff;
        }

        .social-link svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .social-link[title="Telegram"] svg {
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .social-link:hover {
            border-color: #fff;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-3px) scale(1.1);
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                gap: 10px;
            }

            .hero-card {
                width: 150px;
            }

            #openModalBtn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="bg-orbs" aria-hidden="true">
        <div class="bg-orb one"></div>
        <div class="bg-orb two"></div>
        <div class="bg-orb three"></div>
    </div>
    <div class="stars" aria-hidden="true"></div>

    <canvas id="snowCanvas"></canvas>

    <header>
        <div class="logo">MagicGreetings</div>
        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="admin1.php" class="auth-btn" id="btn_cab">👤 Кабинет</a>
            <?php else: ?>
                <a href="index.php" class="auth-btn" id="btn_login">Войти / Регистрация</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h1 id="txt_main_title">Кто будет поздравлять?</h1>

        <div class="heroes-grid">
            <div class="hero-card" data-hero="snegurochka">
                <img src="https://cdn-icons-png.flaticon.com/512/9049/9049550.png" alt="Снегурочка">
                <h3 id="txt_maid">Снегурочка</h3>
            </div>
            <div class="hero-card" data-hero="santa">
                <img src="https://cdn-icons-png.flaticon.com/512/14425/14425883.png" alt="Дед Мороз">
                <h3 id="txt_santa">Дед Мороз</h3>
            </div>
            <div class="hero-card" data-hero="snowman">
                <img src="https://cdn-icons-png.flaticon.com/512/3831/3831729.png" alt="Снеговик">
                <h3 id="txt_snow">Снеговик</h3>
            </div>
        </div>

        <div class="btn-row">
            <button id="openModalBtn" type="button">Заполнить анкету 📝</button>
        </div>
    </main>

    <footer>
        <a href="https://mpit.pro" target="_blank" class="mpit-block">
            <h3 class="mpit-title" id="txt_mpit">МОЯ ПРОФЕССИЯ — ИТ</h3>
            <span class="mpit-sub" id="txt_contest">Всероссийский конкурс</span>
        </a>
        <div class="social-links">
            <a href="https://t.me/mpitpro" target="_blank" class="social-link" title="Telegram"><svg viewBox="0 0 24 24">
                    <path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4"></path>
                </svg></a>
            <a href="https://vk.com/mpitpro" target="_blank" class="social-link" title="VK"><svg viewBox="0 0 24 24">
                    <path d="M13.162 18.994c.609 0 .858-.406.851-.584-.072-2.717 1.163-4.643 3.223-4.643.582 0 1.542.42 1.597 2.162.067 2.112.564 3.065 1.707 3.065h1.707s1.782-.109.932-2.385c-.394-1.056-2.527-2.903-2.617-3.085-.246-.5.145-1.121.267-1.332 2.308-3.923 3.65-6.505 3.65-6.505s.273-.667-.534-.667h-2.973c-.563 0-.825.263-.984.66 0 0-1.728 4.084-2.88 6.004-.377.629-.824.793-1.066.793-.166 0-.411-.19-.411-1.309V6.021c0-1.16-.339-1.681-1.307-1.681h-2.146c-.443 0-.71.328-.71.636 0 .668 1.002.822 1.104 2.701v4.116c0 .903-.162 1.067-.516 1.067-.946 0-3.245-3.435-4.608-7.362-.27-1.464-1.285-1.464-1.285-1.464H2.974c-.878 0-1.054.41-1.054.862 0 0 .178 4.295 4.981 9.006 3.204 3.143 6.911 2.937 6.911 2.937h-0.65z" />
                </svg></a>
        </div>
    </footer>

    <div id="modalForm" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true">
            <span class="close" id="closeModalBtn" aria-label="Закрыть">&times;</span>
            <h2 id="txt_modal_title">🔮 Магические настройки</h2>
            <form id="greetingForm" action="oplata.php" method="POST">
                <input type="hidden" id="heroInput" name="hero" value="snegurochka" />
                <div class="form-group">
                    <label id="lbl_name">Как зовут счастливчика?</label>
                    <input type="text" name="name" required id="ph_name" placeholder="Например: Маша" />
                </div>
                <div class="form-group">
                    <label id="lbl_age">Сколько лет исполняется?</label>
                    <input type="number" name="age" required placeholder="5" min="0" />
                </div>
                <div class="form-group">
                    <label id="lbl_occ">Какой повод?</label>
                    <select name="occasion" required>
                        <option value="С Новым Годом" id="opt_ny">С Новым Годом</option>
                        <option value="С Днем Рождения" id="opt_bd">С Днем Рождения</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn" id="btn_submit">Перейти к оплате 💳</button>
            </form>
        </div>
    </div>

    <script>
        let currentLang = localStorage.getItem('siteLang') || 'ru';
        const translations = {
            ru: {
                main_title: "Кто будет поздравлять?",
                btn_fill: "Заполнить анкету 📝",
                maid: "Снегурочка",
                santa: "Дед Мороз",
                snow: "Снеговик",
                settings: "Настройки",
                close: "Закрыть",
                mpit: "МОЯ ПРОФЕССИЯ — ИТ",
                contest: "Всероссийский конкурс",
                cab: "👤 Кабинет",
                login: "Войти / Регистрация",
                modal_title: "🔮 Магические настройки",
                lbl_name: "Как зовут счастливчика?",
                ph_name: "Например: Маша",
                lbl_age: "Сколько лет исполняется?",
                lbl_occ: "Какой повод?",
                opt_ny: "С Новым Годом",
                opt_bd: "С Днем Рождения",
                btn_submit: "Перейти к оплате 💳"
            },
            en: {
                main_title: "Who will congratulate?",
                btn_fill: "Fill the form 📝",
                maid: "Snow Maiden",
                santa: "Santa Claus",
                snow: "Snowman",
                settings: "Settings",
                close: "Close",
                mpit: "MY PROFESSION IS IT",
                contest: "All-Russian Contest",
                cab: "👤 Cabinet",
                login: "Login / Sign Up",
                modal_title: "🔮 Magic Settings",
                lbl_name: "What is the lucky one's name?",
                ph_name: "Ex: Mary",
                lbl_age: "How old are they?",
                lbl_occ: "What's the occasion?",
                opt_ny: "Happy New Year",
                opt_bd: "Happy Birthday",
                btn_submit: "Proceed to Payment 💳"
            }
        };

        function selectLanguage(lang) {
            currentLang = lang;

            const t = translations[lang];
            document.getElementById('txt_main_title').innerText = t.main_title;
            document.getElementById('openModalBtn').innerText = t.btn_fill;
            document.getElementById('txt_maid').innerText = t.maid;
            document.getElementById('txt_santa').innerText = t.santa;
            document.getElementById('txt_snow').innerText = t.snow;
            document.getElementById('txt_mpit').innerText = t.mpit;
            document.getElementById('txt_contest').innerText = t.contest;

            if (document.getElementById('btn_cab')) document.getElementById('btn_cab').innerText = t.cab;
            if (document.getElementById('btn_login')) document.getElementById('btn_login').innerText = t.login;

            document.getElementById('txt_modal_title').innerText = t.modal_title;
            document.getElementById('lbl_name').innerText = t.lbl_name;
            document.getElementById('ph_name').placeholder = t.ph_name;
            document.getElementById('lbl_age').innerText = t.lbl_age;
            document.getElementById('lbl_occ').innerText = t.lbl_occ;
            document.getElementById('opt_ny').innerText = t.opt_ny;
            document.getElementById('opt_bd').innerText = t.opt_bd;
            document.getElementById('btn_submit').innerText = t.btn_submit;
        }

        document.addEventListener('DOMContentLoaded', () => {
            selectLanguage(currentLang);
            initSnow();
        });

        const openBtn = document.getElementById("openModalBtn");
        const modal = document.getElementById("modalForm");
        const closeBtn = document.getElementById("closeModalBtn");
        const heroInput = document.getElementById("heroInput");

        function openModal() {
            modal.classList.add("open");
            modal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
        }

        function closeModal() {
            modal.classList.remove("open");
            modal.setAttribute("aria-hidden", "true");
            document.body.style.overflow = "";
        }

        document.querySelectorAll(".hero-card").forEach(card => {
            card.addEventListener("click", (event) => {
                document.querySelectorAll(".hero-card").forEach(el => el.classList.remove("selected"));
                event.currentTarget.classList.add("selected");
                heroInput.value = event.currentTarget.dataset.hero;
                openBtn.classList.add("active");
            });
        });

        openBtn.addEventListener("click", openModal);
        closeBtn.addEventListener("click", closeModal);
        window.addEventListener("click", (event) => {
            if (event.target === modal) closeModal();
        });
        window.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && modal.classList.contains("open")) closeModal();
        });

        {
            function initSnow() {
                const canvas = document.getElementById('snowCanvas');
                const ctx = canvas.getContext('2d');
                let w = window.innerWidth;
                let h = window.innerHeight;
                canvas.width = w;
                canvas.height = h;

                const flakes = [];
                const count = 70;

                for (let i = 0; i < count; i++) {
                    flakes.push({
                        x: Math.random() * w,
                        y: Math.random() * h,
                        r: Math.random() * 2 + 1,
                        d: Math.random() * count,
                        s: Math.random() * 0.3 + 0.2
                    });
                }

                function draw() {
                    ctx.clearRect(0, 0, w, h);
                    ctx.fillStyle = "rgba(255, 255, 255, 0.6)";
                    ctx.beginPath();
                    for (let i = 0; i < count; i++) {
                        const f = flakes[i];
                        ctx.moveTo(f.x, f.y);
                        ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2, true);
                    }
                    ctx.fill();
                    update();
                    requestAnimationFrame(draw);
                }

                let angle = 0;

                function update() {
                    angle += 0.005;
                    for (let i = 0; i < count; i++) {
                        const f = flakes[i];

                        f.y += f.s;

                        f.x += f.s * 0.8 + Math.sin(angle + f.d) * 0.5;

                        if (f.x > w + 5 || f.y > h) {
                            if (i % 3 > 0) {
                                flakes[i] = {
                                    x: Math.random() * w,
                                    y: -10,
                                    r: f.r,
                                    d: f.d,
                                    s: f.s
                                };
                            } else
                                flakes[i] = {
                                    x: -5,
                                    y: Math.random() * h,
                                    r: f.r,
                                    d: f.d,
                                    s: f.s
                                };
                        }
                    }
                }
            }
            draw();
            window.addEventListener('resize', () => {
                w = window.innerWidth;
                h = window.innerHeight;
                canvas.width = w;
                canvas.height = h;
            });
        }
    </script>
</body>

</html>