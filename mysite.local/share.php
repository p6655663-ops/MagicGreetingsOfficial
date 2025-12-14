<?php
session_start();

$servername = "127.0.1.27";
$port = 3306;
$username = "root";
$password = "";
$dbname = "registerUser";
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$share_code = $_GET['code'] ?? null;
$order = null;

$videos = [
    'snegurochka' => 'snegurochka.mp4',
    'santa'       => 'santa.mp4',
    'snowman'     => 'snegovik.mp4'
];

if ($share_code) {
    $safe_code = $conn->real_escape_string($share_code);
    $sql = "SELECT * FROM orders WHERE share_code = '$safe_code'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $video_src = $videos[$order['hero']] ?? $videos['snegurochka'];
        $recipient_name = htmlspecialchars($order['recipient_name']);

        $hero_display = ($order['hero'] == 'santa') ? 'Деда Мороза' : (($order['hero'] == 'snowman') ? 'Снеговика' : 'Снегурочки');
    } else {
        $error_message = "Поздравление не найдено или ссылка устарела.";
    }
} else {
    $error_message = "Отсутствует код поздравления.";
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✨ Поздравление для <?php echo $recipient_name ?? 'вас'; ?>!</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6C5DD3;
            --accent: #FF75C3;
            --bg-gradient: linear-gradient(135deg, #2E0249 0%, #570A57 50%, #A91079 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Varela Round", sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow-x: hidden;
            position: relative;
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

        #snowCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            z-index: 10;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h1 {
            font-family: "Fredoka One", cursive;
            font-size: 2rem;
            color: #FFD93D;
            margin-bottom: 10px;
        }

        p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            margin-bottom: 25px;
            background: #000;
        }

        video {
            width: 100%;
            display: block;
        }

        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 10;
        }

        .play-btn-icon {
            font-size: 60px;
            color: white;
            opacity: 0.9;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .play-overlay.hidden {
            display: none;
        }

        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .share-btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
            transition: 0.2s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: white;
            border: 1px solid;
        }

        .share-btn-vk {
            background: #4a76a8;
            border-color: #4a76a8;
        }

        .share-btn-vk:hover {
            background: #3b5a7a;
        }

        .share-btn-tg {
            background: #0088cc;
            border-color: #0088cc;
        }

        .share-btn-tg:hover {
            background: #006b99;
        }

        .download-btn {
            background: var(--accent);
            border-color: var(--accent);
        }

        .download-btn:hover {
            background: #d35a96;
        }

        .create-link {
            color: #999;
            font-size: 13px;
            text-decoration: none;
            transition: 0.2s;
        }

        .create-link:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <canvas id="snowCanvas"></canvas>

    <div class="container">
        <?php if ($order): ?>
            <h1>🎁 <span id="txt_congrats">Поздравляю</span>, <?php echo $recipient_name; ?>!</h1>

            <p id="txt_desc">Ваше персональное видео от <?php echo htmlspecialchars($hero_display); ?> готово.</p>

            <div class="video-wrapper" onclick="playVideo()">
                <div class="play-overlay" id="playBtn">
                    <div class="play-btn-icon">▶</div>
                </div>
                <video id="heroVideo" playsinline muted>
                    <source src="<?php echo $video_src; ?>" type="video/mp4">
                </video>
            </div>

            <audio id="heroAudio" src="<?php echo $order['audio_url']; ?>"></audio>

            <p style="font-size:12px; color:#aaa; margin-top:-10px;" id="txt_click_hint">Нажмите на видео, чтобы начать просмотр</p>

            <div class="share-buttons">
                <a href="#" id="downloadLink" download="MagicGreetings_<?php echo $recipient_name; ?>.mp3" class="share-btn download-btn">
                    <span id="txt_download">Скачать Поздравление</span>
                </a>

                <a href="#" id="vkShare" target="_blank" class="share-btn share-btn-vk">
                    <span id="txt_share_vk">Поделиться в VK</span>
                </a>

                <a href="#" id="tgShare" target="_blank" class="share-btn share-btn-tg">
                    <span id="txt_share_tg">Отправить в Telegram</span>
                </a>
            </div>

            <div style="margin-top:25px;">
                <a href="magic.php" class="create-link" id="txt_create_new">Создать свое поздравление</a>
            </div>

        <?php else: ?>
            <h1 id="txt_error_title">🚫 Ошибка</h1>
            <p id="txt_error_desc"><?php echo $error_message; ?></p>
            <a href="magic.php" class="share-btn share-btn-vk" style="margin-top: 20px;" id="txt_home">На главную</a>
        <?php endif; ?>
    </div>

    <script>
        (function() {
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
        })();

        <?php if ($order): ?>
            const vidEl = document.getElementById('heroVideo');
            const audEl = document.getElementById('heroAudio');
            const playBtn = document.getElementById('playBtn');
            const downloadLink = document.getElementById('downloadLink');

            document.addEventListener('DOMContentLoaded', () => {
                const currentLang = localStorage.getItem('siteLang') || 'ru';
                if (currentLang === 'en') {
                    document.getElementById('txt_congrats').innerText = "Congratulations";

                    const heroName = "<?php echo ($order['hero'] == 'santa' ? 'Santa Claus' : ($order['hero'] == 'snowman' ? 'Snowman' : 'Snow Maiden')); ?>";
                    document.getElementById('txt_desc').innerText = `Your personal video from ${heroName} is ready.`;

                    document.getElementById('txt_click_hint').innerText = "Click on video to start watching";
                    document.getElementById('txt_download').innerText = "Download MP3";
                    document.getElementById('txt_share_vk').innerText = "Share on VK";
                    document.getElementById('txt_share_tg').innerText = "Send via Telegram";
                    document.getElementById('txt_create_new').innerText = "Create your own greeting";
                }
            });

            const shareUrl = window.location.href;
            const shareText = encodeURIComponent(`✨ Вы получили волшебное поздравление от MagicGreetings! Поздравление для ${"<?php echo $recipient_name; ?>"}! @mpitpro #МПИТ #MagicGreetings`);

            const vkShareUrl = `http://vk.com/share.php?url=${encodeURIComponent(shareUrl)}&title=${shareText}&noparse=true`;
            document.getElementById('vkShare').href = vkShareUrl;

            const tgShareUrl = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${shareText}`;
            document.getElementById('tgShare').href = tgShareUrl;

            downloadLink.href = "<?php echo $order['audio_url']; ?>";

            window.playVideo = function() {
                if (vidEl.paused) {
                    vidEl.play();
                    audEl.play();
                    playBtn.classList.add('hidden');
                } else {
                    vidEl.pause();
                    audEl.pause();
                    playBtn.classList.remove('hidden');
                }

                audEl.onended = function() {
                    vidEl.pause();
                    playBtn.classList.remove('hidden');
                    vidEl.currentTime = 0;
                    audEl.currentTime = 0;
                };
            };
        <?php else: ?>
            document.addEventListener('DOMContentLoaded', () => {
                const currentLang = localStorage.getItem('siteLang') || 'ru';
                if (currentLang === 'en') {
                    document.getElementById('txt_error_title').innerText = "🚫 Error";
                    document.getElementById('txt_home').innerText = "Go Home";
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>