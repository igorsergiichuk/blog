<?php

/**
   Видалення певного запису. Напочатку видаляємо коментарі, потім сам запис
 */
function deletePost(PDO $pdo, $postId)
{
    $sqls = array(
        // Видаляємо кометарі, щоб не виникало помилок через наявність зовнішніх ключів
        "DELETE FROM
            comment
        WHERE
            post_id = :id",
        // Потім видаляємо сам запис
        "DELETE FROM
            post
        WHERE
            id = :id",
    );

    foreach ($sqls as $sql)
    {
        $stmt = $pdo->prepare($sql);
        if ($stmt === false)
        {
            throw new Exception('Не вдалось підготувати запит до БД');
        }

        $result = $stmt->execute(
            array('id' => $postId, )
        );

        // В разі виникнення помилку зупиняємо операцію
        if ($result === false)
        {
            break;
        }
    }

    return $result !== false;
}
