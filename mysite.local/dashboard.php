<?php
// Запускаем сессию. Если пользователь не вошел — выкидываем на регистрацию (index.php)
session_start();
if (!isset($_SESSION['user_id'])) {
    // Если сессии нет, раскомментируй строку ниже, когда доделаешь вход
    // header("Location: index.php"); 
    // exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MagicGreetings — Создать поздравление</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
        }

        /* Шапка */
        header {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #6a11cb;
        }

        .logout-btn {
            text-decoration: none;
            color: #555;
            border: 1px solid #ccc;
            padding: 5px 15px;
            border-radius: 20px;
        }

        /* Основной блок */
        main {
            max-width: 900px;
            margin: 40px auto;
            text-align: center;
            padding: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        p.subtitle {
            color: #666;
            margin-bottom: 40px;
        }

        /* Сетка героев */
        .heroes-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .hero-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            width: 180px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
            border: 3px solid transparent;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .hero-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-card img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .hero-card h3 {
            margin: 0;
            font-size: 18px;
        }

        /* Стиль выбранного героя */
        .hero-card.selected {
            border-color: #6a11cb;
            background-color: #f3e5f5;
            transform: scale(1.05);
        }

        /* Кнопка генерации */
        .generate-container {
            margin-top: 40px;
        }

        #openModalBtn {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 20px;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(37, 117, 252, 0.4);
            transition: 0.3s;
            opacity: 0.5;
            /* Неактивна пока не выбран герой */
            pointer-events: none;
        }

        #openModalBtn.active {
            opacity: 1;
            pointer-events: auto;
        }

        #openModalBtn:hover {
            transform: scale(1.05);
        }

        /* МОДАЛЬНОЕ ОКНО */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
            animation: slideDown 0.4s;
        }

        @keyframes slideDown {
            from {
                top: -100px;
                opacity: 0;
            }

            to {
                top: 0;
                opacity: 1;
            }
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .submit-btn {
            width: 100%;
            background: #2575fc;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Блок результата */
        #resultArea {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            display: none;
            /* Скрыт по умолчанию */
            border-left: 5px solid #6a11cb;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">✨ MagicGreetings</div>
        <a href="logout.php" class="logout-btn">Выйти</a>
    </header>

    <main>
        <h1>Кто будет поздравлять?</h1>
        <p class="subtitle">Выберите персонажа, чтобы начать магию</p>

        <div class="heroes-grid">
            <div class="hero-card" onclick="selectHero('santa')">
                <img src="https://cdn-icons-png.flaticon.com/512/3656/3656894.png" alt="Дед Мороз">
                <h3>Дед Мороз</h3>
            </div>
            <div class="hero-card" onclick="selectHero('robot')">
                <img src="https://cdn-icons-png.flaticon.com/512/4230/4230718.png" alt="Робот">
                <h3>Добробот</h3>
            </div>
            <div class="hero-card" onclick="selectHero('dino')">
                <img src="https://cdn-icons-png.flaticon.com/512/2316/2316823.png" alt="Динозавр">
                <h3>Динозавр</h3>
            </div>
        </div>

        <div class="generate-container">
            <p id="hero-name-display">Герой не выбран</p>
            <button id="openModalBtn">Заполнить анкету 📝</button>
        </div>
    </main>

    <div id="modalForm" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Настройки поздравления</h2>

            <form id="greetingForm">
                <input type="hidden" id="heroInput" name="hero">

                <div class="form-group">
                    <label>Имя ребенка:</label>
                    <input type="text" name="name" placeholder="Например: Ваня" required>
                </div>

                <div class="form-group">
                    <label>Возраст:</label>
                    <input type="number" name="age" placeholder="5" required>
                </div>

                <div class="form-group">
                    <label>Повод:</label>
                    <select name="occasion">
                        <option value="С Днем Рождения">🎂 С Днем Рождения</option>
                        <option value="С Новым Годом">🎄 С Новым Годом</option>
                        <option value="Первый раз в школу">🎒 Первый раз в школу</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Дополнительно (хобби, за что похвалить):</label>
                    <textarea name="details" placeholder="Любит лего, помогает маме..." rows="3"></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">Сгенерировать текст (Google AI)</button>
            </form>

            <div id="resultArea"></div>
        </div>
    </div>

    <script>
        // 1. Логика выбора героя
        function selectHero(heroName) {
            // Убираем выделение со всех
            document.querySelectorAll('.hero-card').forEach(el => el.classList.remove('selected'));
            // Выделяем текущего (используем event.currentTarget, так надежнее)
            event.currentTarget.classList.add('selected');

            // Записываем в скрытое поле
            document.getElementById('heroInput').value = heroName;

            // Меняем текст и включаем кнопку
            document.getElementById('hero-name-display').innerText = "Выбран: " + heroName;
            document.getElementById('openModalBtn').classList.add('active');
        }

        // 2. Управление модальным окном
        const modal = document.getElementById('modalForm');
        const openBtn = document.getElementById('openModalBtn');

        openBtn.onclick = () => modal.style.display = "block";

        function closeModal() {
            modal.style.display = "none";
        }
        window.onclick = (e) => {
            if (e.target == modal) closeModal();
        }

        // 3. Отправка формы на сервер (AJAX)
        const form = document.getElementById('greetingForm');
        const resultArea = document.getElementById('resultArea');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault(); // Не перезагружать страницу

            // UI эффекты
            submitBtn.disabled = true;
            submitBtn.innerText = "Думаю... 🧠";
            resultArea.style.display = 'block';
            resultArea.innerHTML = "Связываюсь с Google AI Studio...";

            const formData = new FormData(form);

            try {
                // Отправляем данные в соседний PHP файл
                const response = await fetch('generate_google.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json(); // Ждем JSON ответ

                if (data.success) {
                    resultArea.innerHTML = `
                        <h3 style="color:green">Готово!</h3>
                        <p><strong>Текст от AI:</strong></p>
                        <p style="background:#eee; padding:10px; border-radius:5px;">${data.text}</p>
                        <br>
                        <button style="width:100%; background:#ff9800; border:none; color:white; padding:10px; border-radius:5px;">Далее: Озвучить и Анимировать (в разработке)</button>
                    `;
                } else {
                    resultArea.innerHTML = `<p style="color:red">Ошибка: ${data.error}</p>`;
                }

            } catch (error) {
                console.error(error);
                resultArea.innerHTML = `<p style="color:red">Ошибка соединения с сервером.</p>`;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = "Сгенерировать текст (Google AI)";
            }
        });
    </script>
</body>

</html>