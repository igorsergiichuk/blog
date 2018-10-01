<?php

/**
   Створення БД блога (array(count array, error string))
 */
function installBlog(PDO $pdo)
{
    // Шлях до БД та проекту
    $root = getRootPath();
    $database = getDatabasePath();

    $error = '';

    // Перевірка на випадок якщо БД вже існує аби її не видалили випадково
    if (is_readable($database) && filesize($database) > 0)
    {
        $error = 'Будь ласка, видаліть існуючу БД вручну перед встановленням'.'<p>'.
        '<a href="index.php">Переглянути блог</a> або <a href="install.php">Встановити знову</a>.';
    }

    // Створення пустого файлу БД
    if (!$error)
    {
        $createdOk = touch($database);
        if (!$createdOk)
        {
            $error = sprintf(
                'Неможливо створити БД, дозвольте серверу стрювати нові файли в теці \'%s\'',
                dirname($database)
            );
        }
    }

    if (!$error)
    {
        $sql = file_get_contents($root . '/data/init.sql');

        if ($sql === false)
        {
            $error = 'Неможливо створити БД';
        }
    }

    // З_єднання з БД відтворення команд sql
    if (!$error)
    {
        $result = $pdo->exec($sql);
        if ($result === false)
        {
            $error = 'Не вдалося запустит sql: ' . print_r($pdo->errorInfo(), true);
        }
    }

    // Підрахунок кількості створенних записів
    $count = array();

    foreach(array('post', 'comment') as $tableName)
    {
        if (!$error)
        {
            $sql = "SELECT COUNT(*) AS c FROM " . $tableName;
            $stmt = $pdo->query($sql);
            if ($stmt)
            {
                $count[$tableName] = $stmt->fetchColumn();
            }
        }
    }

    return array($count, $error);
}

