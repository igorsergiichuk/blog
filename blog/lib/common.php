<?php

/**
   функція вертає директорію проекту (string)
 */
function getRootPath()
{
    return realpath(__DIR__ . '/..');
}

/**
   функція вертає повний шлях до директорії БД (string)
 */
function getDatabasePath()
{
    return getRootPath() . '/data/data.sqlite';
}

/**
   Функція вертає DSN для з'єднання з SQLite (string)
 */
function getDsn()
{
    return 'sqlite:' . getDatabasePath();
}

/**
   Створення об'єкту PDO для доступу до ДБ (PDO)
 */
function getPDO()
{
    $pdo = new PDO(getDsn());
    $result = $pdo->query('PRAGMA foreign_keys = ON');
    if ($result === false)
    {
        throw new Exception('Не вдалося під_єднати зовнішні ключі');
    }

    return $pdo;
}

/**
   Escapes HTML (string $html)
 */
function htmlEscape($html)
{
    return strip_tags($html, '<b><br><img><a>');
}

/**
   Повний HTML escape для коментарів
 */

function htmlEscapeFull($html)
{
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

/**
   Конвертує змінну з формату sql в більш звичний формат (var $date DateTime)
 */
function convertSqlDate($sqlDate)
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $sqlDate);
    return $date->format('d M Y, H:i');
}

function getSqlDateForNow()
{
    return date('Y-m-d H:i:s');
}

/**
   Повертає пости в зворотньому  (PDO $pdo)
 */
function getAllPosts(PDO $pdo)
{
    $stmt = $pdo->query(
        'SELECT
            id, title, created_at, body,
            (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) comment_count
        FROM
            post
        ORDER BY
            created_at DESC'
    );
    if ($stmt === false)
    {
        throw new Exception('Не вдалось підготувати запит до БД');
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
   Адаптуємо перехід на нові строку під HTML та убезпечуємо себе від ін_єкцій (string)
 */
function convertNewlinesToParagraphs($text)
{
    $escaped = htmlEscape($text);
    return '<p>' . str_replace("\n", "</p><p>", $escaped) . '</p>';
}



/**
   Перенаправлення на повну адресу URL (string)
 */

function redirectAndExit($script)
{
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);
    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}

/**
   Вертає всі коментарі підв_язані до посту (array)
 */
function getCommentsForPost(PDO $pdo, $postId)
{
    $sql = "
        SELECT
            id, name, text, created_at, website
        FROM
            comment
        WHERE
            post_id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array('post_id' => $postId, )
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
   Функція за порівнює логін і пароль в базі данних та логін, пароль введений користувачем (bool)
 */
function tryLogin(PDO $pdo, $username, $password)
{
    $sql = "SELECT * FROM user";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($username === $row['username']){
        if ($password === $row['password']){
            $success = true;
        }
    }
    return $success;
}

/**
   Починає сесію з користувачем
 */
function login($username)
{
    session_regenerate_id();

    $_SESSION['logged_in_username'] = $username;
}

/**
   Функція розлогінює користувача
 */
function logout()
{
    unset($_SESSION['logged_in_username']);
}

/**
    Функція вертає логін корустувача
 */
function getAuthUser()
{
    return isLoggedIn() ? $_SESSION['logged_in_username'] : null;
}

/**
    Перевіряємо чи користувач залогінився
 */
function isLoggedIn()
{
    return isset($_SESSION['logged_in_username']);
}

/**
   Вертає user_id для конкретного користувача, що залогінився
 */
function getAuthUserId(PDO $pdo)
{
    if (!isLoggedIn())
    {
        return null;
    }
    $sql = "
        SELECT
            id
        FROM
            user
        WHERE
            username = :username
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array('username' => getAuthUser()));
    return $stmt->fetchColumn();
}

function viewLimit($html)
{

    // strip tags to avoid breaking any html
    $string = htmlEscape($html);

    if (strlen($string) > 500) {

        // truncate string
        $stringCut = substr($string, 0, 400);

        // make sure it ends in a word so assassinate doesn't become ass...
        $string = substr($stringCut, 0, strrpos($stringCut, ' '));
    }
    return $string;

}