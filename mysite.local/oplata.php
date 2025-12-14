<?php
session_start();

$servername = "127.0.1.27";
$port = 3306;
$username = "root";
$password = "";
$dbname = "registerUser";
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Ошибка подключения к БД: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hero = $_POST['hero'] ?? 'snegurochka';
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $occasion = $_POST['occasion'] ?? '';
} else {
    header("Location: magic.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary: #6C5DD3;
            --accent: #FF75C3;
            --bg-gradient: linear-gradient(135deg, #2E0249 0%, #570A57 50%, #A91079 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, .22);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Varela Round", sans-serif;
            background: var(--bg-gradient);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            overflow: auto;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.10) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: -3;
        }

        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-orb {
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(32px);
            opacity: .35;
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

        #snowCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .checkout-container {
            width: 100%;
            max-width: 520px;
            margin: 18px;
            padding: 28px 22px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(12px);
            box-shadow: 0 25px 55px rgba(0, 0, 0, .35);
            text-align: center;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        h2 {
            margin: 0 0 18px 0;
            font-family: "Fredoka One", cursive;
            font-weight: 400;
            color: #fff;
            letter-spacing: .5px;
        }

        .order-summary {
            background: rgba(255, 255, 255, 0.92);
            color: #2d3436;
            border-radius: 16px;
            padding: 14px;
            margin: 0 0 18px 0;
            text-align: left;
            box-shadow: var(--shadow);
            border: 2px solid rgba(255, 255, 255, 0.45);
        }

        .price-tag {
            float: right;
            font-weight: 900;
            color: var(--primary);
        }

        .card-visual {
            background: linear-gradient(135deg, rgba(255, 117, 195, .95) 0%, rgba(255, 217, 61, .92) 100%);
            border-radius: 18px;
            padding: 18px;
            color: #3b0030;
            margin: 0 0 18px 0;
            box-shadow: 0 18px 38px rgba(0, 0, 0, .25);
            position: relative;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, .45);
        }

        .card-visual .chip {
            width: 44px;
            height: 32px;
            background: #ffd700;
            border-radius: 6px;
            margin-bottom: 18px;
            opacity: .9;
        }

        #dispNumber {
            font-family: "Fredoka One", cursive;
            letter-spacing: 2px;
            font-size: 18px;
            margin-bottom: 14px;
            color: #3b0030;
        }

        .form-row {
            margin-bottom: 14px;
            text-align: left;
        }

        .form-row label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: rgba(255, 255, 255, .92);
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: 14px;
            font-size: 16px;
            outline: none;
            transition: .25s;
            font-family: "Varela Round", sans-serif;
            background: rgba(255, 255, 255, .92);
            color: #2d3436;
        }

        input:focus {
            border-color: rgba(255, 255, 255, 0.85);
            transform: translateY(-1px);
        }

        .flex-row {
            display: flex;
            gap: 12px;
        }

        .flex-row>div {
            flex: 1;
        }

        .pay-btn {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 16px 20px;
            cursor: pointer;
            font-family: "Fredoka One", cursive;
            font-size: 18px;
            text-transform: uppercase;
            color: #4a0033;
            background: linear-gradient(45deg, #FF75C3, #FFD93D);
            box-shadow: 0 14px 28px rgba(0, 0, 0, .20);
            transition: .22s;
        }

        .pay-btn:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 18px 36px rgba(255, 117, 195, .35);
        }

        .loader-screen {
            display: none;
            padding: 26px 0;
            flex-grow: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .spinner {
            border: 5px solid rgba(255, 255, 255, 0.25);
            border-top: 5px solid rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 14px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .status-text {
            font-size: 16px;
            color: rgba(255, 255, 255, .92);
            font-weight: 800;
        }
    </style>
</head>

<body>

    <div class="bg-orbs">
        <div class="bg-orb one"></div>
        <div class="bg-orb two"></div>
        <div class="bg-orb three"></div>
    </div>

    <canvas id="snowCanvas"></canvas>

    <div class="checkout-container">
        <div id="paymentBlock">
            <h2 id="txt_title">Оплата заказа</h2>

            <div class="order-summary">
                <span id="txt_product">Видео-поздравление</span> (<span><?= htmlspecialchars($name) ?></span>)
                <span class="price-tag">299 ₽</span>
            </div>

            <form id="paymentForm" onsubmit="startProcess(event)">
                <div class="card-visual">
                    <div class="chip"></div>
                    <div id="dispNumber" style="font-size:18px; margin-bottom:15px">#### #### #### ####</div>
                    <div style="display:flex; justify-content:space-between; font-size:12px;">
                        <span id="dispName">CARD HOLDER</span>
                        <span id="dispDate">MM/YY</span>
                    </div>
                </div>

                <div class="form-row">
                    <label id="lbl_card">Номер карты</label>
                    <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19" required />
                </div>

                <div class="flex-row">
                    <div class="form-row">
                        <label id="lbl_date">Срок действия</label>
                        <input type="text" id="cardDate" placeholder="MM/YY" maxlength="5" required />
                    </div>
                    <div class="form-row">
                        <label>CVC/CVV</label>
                        <input type="password" id="cardCvc" placeholder="123" maxlength="3" required />
                    </div>
                </div>

                <div class="form-row">
                    <label id="lbl_owner">Владелец карты</label>
                    <input type="text" id="cardName" placeholder="IVAN IVANOV" required />
                </div>

                <button type="submit" class="pay-btn" id="txt_btn">Оплатить 299 ₽</button>
            </form>
        </div>

        <div id="loaderBlock" class="loader-screen">
            <div class="spinner"></div>
            <div class="status-text" id="loaderText">Связь с банком...</div>
            <p style="color:rgba(255,255,255,.70); font-size:13px; margin-top:10px;" id="txt_wait">
                Не закрывайте страницу
            </p>
        </div>

    </div>

    <script>
        const hero = "<?= htmlspecialchars($hero) ?>";
        const name = "<?= htmlspecialchars($name) ?>";
        const age = "<?= htmlspecialchars($age) ?>";
        const occasion = "<?= htmlspecialchars($occasion) ?>";

        const paymentBlock = document.getElementById("paymentBlock");
        const loaderBlock = document.getElementById("loaderBlock");
        const loaderText = document.getElementById("loaderText");

        let currentLang = localStorage.getItem('siteLang') || 'ru';

        const translations = {
            ru: {
                title: "Оплата заказа",
                product: "Видео-поздравление",
                card: "Номер карты",
                date: "Срок действия",
                owner: "Владелец карты",
                btn: "Оплатить 299 ₽",
                wait: "Не закрывайте страницу",
                step1: "Проверка реквизитов...",
                step2: "Оплата одобрена!",
                step3: "Составляем поздравление...",
                step4: "Записываем голос...",
                step5: "Подготовка ссылки...",
                step6: "Переход..."
            },
            en: {
                title: "Order Payment",
                product: "Video Greeting",
                card: "Card Number",
                date: "Expiry Date",
                owner: "Card Holder",
                btn: "Pay 299 ₽",
                wait: "Do not close the page",
                step1: "Checking details...",
                step2: "Payment approved!",
                step3: "Creating greeting...",
                step4: "Recording voice...",
                step5: "Preparing link...",
                step6: "Redirecting..."
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const t = translations[currentLang];

            document.getElementById('txt_title').innerText = t.title;
            document.getElementById('txt_product').innerText = t.product;
            document.getElementById('lbl_card').innerText = t.card;
            document.getElementById('lbl_date').innerText = t.date;
            document.getElementById('lbl_owner').innerText = t.owner;
            document.getElementById('txt_btn').innerText = t.btn;
            document.getElementById('txt_wait').innerText = t.wait;

            initSnow(); 
        });

        const cardNumberEl = document.getElementById("cardNumber");
        const cardDateEl = document.getElementById("cardDate");
        const cardNameEl = document.getElementById("cardName");
        const dispNumberEl = document.getElementById("dispNumber");
        const dispDateEl = document.getElementById("dispDate");
        const dispNameEl = document.getElementById("dispName");

        cardNumberEl.addEventListener("input", (e) => {
            let v = e.target.value.replace(/\D/g, "").slice(0, 16);
            v = v.replace(/(\d{4})(?=\d)/g, "$1 ").trim();
            e.target.value = v;
            dispNumberEl.innerText = v || "#### #### #### ####";
        });

        cardDateEl.addEventListener("input", (e) => {
            let v = e.target.value.replace(/\D/g, "").slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + "/" + v.slice(2);
            e.target.value = v;
            dispDateEl.innerText = v || "MM/YY";
        });

        cardNameEl.addEventListener("input", (e) => {
            const v = (e.target.value || "").toUpperCase();
            dispNameEl.innerText = v || "CARD HOLDER";
        });

        async function startProcess(e) {
            e.preventDefault();
            const t = translations[currentLang];

            paymentBlock.style.display = "none";
            loaderBlock.style.display = "flex";

            try {
                loaderText.innerText = t.step1;
                await new Promise((r) => setTimeout(r, 1000));
                loaderText.innerText = t.step2;
                await new Promise((r) => setTimeout(r, 1000));

                loaderText.innerText = t.step3;
                const formData = new FormData();
                formData.append("hero", hero);
                formData.append("name", name);
                formData.append("age", age);
                formData.append("occasion", occasion);
                formData.append("lang", currentLang);

                let resText = await fetch("generate_google.php", {
                    method: "POST",
                    body: formData
                });
                let rawText = await resText.text();
                let dataText;
                try {
                    dataText = JSON.parse(rawText);
                } catch (err) {
                    throw new Error("Generate Text Error: " + rawText.substring(0, 50));
                }
                if (!dataText.success) throw new Error(dataText.error);
                const text = dataText.text;

                loaderText.innerText = t.step4;
                let resVoice = await fetch("generate_voice.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        text: text,
                        hero: hero,
                        lang: currentLang
                    })
                });

                let rawVoice = await resVoice.text();
                let dataVoice;
                try {
                    dataVoice = JSON.parse(rawVoice);
                } catch (err) {
                    throw new Error("Generate Voice Error: " + rawVoice.substring(0, 50));
                }
                if (!dataVoice.success) throw new Error(dataVoice.error);
                const audioUrl = dataVoice.audio_url;

                loaderText.innerText = t.step5;
                let resSave = await fetch("save_order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        hero: hero,
                        recipient_name: name,
                        recipient_age: age,
                        audio_url: audioUrl
                    })
                });

                let rawSave = await resSave.text();
                let dataSave;
                try {
                    dataSave = JSON.parse(rawSave);
                } catch (err) {
                    throw new Error("Save Error: " + rawSave.substring(0, 50));
                }
                if (!dataSave.success) throw new Error(dataSave.error);
                const shareCode = dataSave.share_code;

                loaderText.innerText = t.step6;
                await new Promise((r) => setTimeout(r, 500));
                window.location.replace(`share.php?code=${shareCode}`);

            } catch (err) {
                alert("ERROR: " + err.message);
                loaderBlock.style.display = "none";
                paymentBlock.style.display = "block";
            }
        }

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
                        } else {
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