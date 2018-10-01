<?php
require_once 'lib/common.php';
require_once 'lib/install.php';

session_start();

if ($_POST)
{
    $pdo = getPDO();
    list($rowCounts, $error) = installBlog($pdo);

    $password = '';

    $_SESSION['count'] = $rowCounts;
    $_SESSION['error'] = $error;
    $_SESSION['try-install'] = true;

    redirectAndExit('install.php');
}

// Перевіряємо чи відбулося встановлення
$attempted = false;
if (isset($_SESSION['try-install']))
{
    $attempted = true;
    $count = $_SESSION['count'];
    $error = $_SESSION['error'];

    // Зкидаємо сесію
    unset($_SESSION['count']);
    unset($_SESSION['error']);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['try-install']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Встановити блог</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
<?php if ($attempted): ?>

    <?php if ($error): ?>
        <div class="error box">
            <?php echo $error ?>
        </div>
    <?php else: ?>
        <div class="success box">
            БД та демо записи були створені успішно.

            <?php // Report the counts for each table ?>
            <?php foreach (array('post', 'comment') as $tableName): ?>
                <?php if (isset($count[$tableName])): ?>
                    <?php // Prints the count ?>
                    Створено
                    <?php echo $count[$tableName] ?>
                    <?php // Prints the name of the thing ?>
                    <?php echo $tableName ?>
                    .
                <?php endif ?>
            <?php endforeach ?>
        </div>

        <p>
            <a href="index.php">Переглянути блог</a>
            або <a href="install.php">Встановити знову</a>.
        </p>
    <?php endif ?>

<?php else: ?>

    <p>Натисніть на кнопку "Встановити" для встановлення блогу.</p>

    <form method="post">
        <input
            name="install"
            type="submit"
            value="Встановити"
        />
    </form>

<?php endif ?>
</body>
</html>
