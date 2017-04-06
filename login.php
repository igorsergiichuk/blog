<?php
require_once 'lib/common.php';

session_start();

// Якщо користувач залогінений відправляємо його на стартову сторінку
if (isLoggedIn())
{
    redirectAndExit('index.php');
}

$username = '';
if ($_POST)
{
    $pdo = getPDO();

    // Перевіряємо пароль
    $username = $_POST['username'];
    $ok = tryLogin($pdo, $username, $_POST['password']);
    if ($ok)
    {
        login($username);
        redirectAndExit('index.php');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Code::in | Ввійти</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
<?php require 'templates/title.php' ?>

<?php if ($username): ?>
    <div class="error box">
        Пароль чи ім'я користувача не правильні. Будь ласка, спробуйте ще.
    </div>
<?php endif ?>

<h2>Вхід:</h2>

<form
    method="post"
    class="user-form"
>
    <div>
        <label for="username">
            Ім'я:
        </label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?php echo htmlEscape($username) ?>"
        />
    </div>
    <div>
        <label for="password">
            Пароль:
        </label>
        <input
            type="password"
            id="password"
            name="password"
        />
    </div>
    <button class="btn btn-primary" type="submit" id="login-button">Ввійти</button>
</form>
</body>
</html>