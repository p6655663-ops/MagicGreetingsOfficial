<?php
ob_start();
ob_clean();

header('Content-Type: application/json');

$name = $_POST['name'] ?? 'Friend';
$hero = $_POST['hero'] ?? 'snegurochka';
$occasion = $_POST['occasion'] ?? 'С Новым Годом';
$age = $_POST['age'] ?? 0;
$lang = $_POST['lang'] ?? 'ru';

if ($lang === 'ru') {
    if ($hero === 'santa') $heroName = 'Дедушка Мороз';
    elseif ($hero === 'snowman') $heroName = 'Снеговик';
    else $heroName = 'Снегурочка';

    function getYearWord($n)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return 'лет';
        if ($n1 > 1 && $n1 < 5) return 'года';
        if ($n1 == 1) return 'год';
        return 'лет';
    }
    $fullAge = "$age " . getYearWord($age);

    if ($occasion === 'С Новым Годом') {
        if ($hero === 'snowman') {
            $texts = [
                "Привет, $name! Я веселый Снеговик! Поздравляю с Новым Годом! Моя морковка подсказывает, что тебе уже $fullAge. Желаю тебе горы пушистого снега и отличного настроения! а этот танец для тебя! ту ,ту ,ту ,ту, ту ",
                "Здравствуй, $name! Я Снеговик-почтовик! Принес тебе поздравление. В $fullAge нужно много играть в снежки и кататься с горки! С Новым Годом! желаю удачи! а этот танец для тебя!  ту , ту, ту ,ту, ту ,ту, ту, ту ",
                "Ого! Привет, $name! Я Снеговик! У меня ведро на голове, а у тебя праздник! Тебе уже $fullAge? Круто! Желаю в Новом Году быть таким же классным, как я! а этот танец для тебя!  ту, ту ,ту ,ту, ту ,ту ,ту, ту, ту "
            ];
        } else {
            $texts = [
                "Здравствуй, $name! Это я, $heroName! Поздравляю тебя с Новым Годом! Тебе уже $fullAge, ты такой большой! Желаю найти под ёлочкой самые лучшие подарки. этот танец для тебя ту, ту ,ту ,ту, ту ",
                "Привет, $name! С Новым Годом! Я, $heroName, спешу поздравить тебя. Знаю, что тебе исполнилось $fullAge. Слушайся родителей! С новым годом! этот танец для тебя ту, ту ,ту ,ту, ту ,ту ",
                "Здравствуй, $name! Это $heroName! С Новым Годом! Тебе уже $fullAge? Ого! Желаю тебе много сладостей, гору игрушек и веселья! Счастья тебе! этот танец для тебя ту, ту ,ту ,ту, ту ,ту "
            ];
        }
    } else {
        if ($hero === 'snowman') {
            $texts = [
                "Привет, $name! Я Снеговик! Услышал, что у тебя День Рождения! Тебе исполнилось $fullAge! Желаю, чтобы твой праздничный торт был больше моего сугроба!",
                "С Днем Рождения, $name! Я Снеговик! Даже зимой мне становится жарко от твоего праздника! Тебе уже $fullAge! Расти большим и не болей!",
                "Привет-привет, $name! Я Снеговик! Поздравляю с Днем Рождения. $fullAge — это серьезный возраст. Желаю тебе много друзей и веселых игр!"
            ];
        } else {
            $texts = [
                "Привет, $name! Это я, $heroName! Узнал, что тебе сегодня исполнилось $fullAge! Поздравляю! Расти большим, сильным и самым счастливым.",
                "Здравствуй, $name! $heroName на связи! Поздравляю с Днем Рождения! Тебе уже $fullAge — это так здорово! Желаю огромный торт и кучу подарков!",
                "Ого, $name! Тебе уже $fullAge! Какой ты взрослый! $heroName шлет тебе привет. С Днем Рождения! Желаю веселиться, играть и никогда не грустить."
            ];
        }
    }
}


else {
    if ($hero === 'santa') $heroName = 'Santa Claus';
    elseif ($hero === 'snowman') $heroName = 'Snowman';
    else $heroName = 'Snow Maiden';

    $fullAge = "$age years old";
    $isNewYear = ($occasion === 'С Новым Годом' || $occasion === 'New Year');

    if ($isNewYear) {
        if ($hero === 'snowman') {
            $texts = [
                "Hello, $name! I am a funny Snowman! Happy New Year! You are already $fullAge. I wish you lots of snow! And this dance is for you! too, too, too, too",
                "Hi, $name! Snowman here! At $fullAge you need to play snowballs! Happy New Year! And this dance is for you! too, too, too, too",
                "Wow! Hi, $name! I am Snowman! Happy New Year! You are $fullAge? Cool! Be awesome this year! And this dance is for you! too, too, too"
            ];
        } else {
            $texts = [
                "Hello, $name! It's me, $heroName! Happy New Year! You are $fullAge now! I wish you the best gifts. This dance is for you too, too, too",
                "Hi, $name! Happy New Year! I, $heroName, want to congratulate you. I know you are $fullAge. Be happy! This dance is for you too, too, too",
                "Hello, $name! It is $heroName! Happy New Year! You are $fullAge? Wow! I wish you lots of sweets and toys! This dance is for you too, too, too"
            ];
        }
    } else {
        if ($hero === 'snowman') {
            $texts = [
                "Hello, $name! I am Snowman! Happy Birthday! You are $fullAge! I wish your cake was bigger than my snowbank!",
                "Happy Birthday, $name! I am Snowman! Even in winter I feel warm from your party! You are $fullAge! Grow big!",
                "Hi, $name! Snowman here! Happy Birthday! $fullAge is a serious age. Have fun!"
            ];
        } else {
            $texts = [
                "Hello, $name! It is me, $heroName! Happy Birthday to you! You are $fullAge! Be strong and happy!",
                "Hi, $name! $heroName calling! Happy Birthday! You are $fullAge — that is great! I wish you a huge cake!",
                "Wow, $name! You are $fullAge! So big! $heroName sends greetings. Happy Birthday! Have fun and play!"
            ];
        }
    }
}

$randomText = $texts[array_rand($texts)];

echo json_encode(['success' => true, 'text' => $randomText]);
exit;
