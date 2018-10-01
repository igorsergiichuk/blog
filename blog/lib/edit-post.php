<?php

/**
   Функція редагує вже існуючий запис (bool)
 */
function editPost(PDO $pdo, $title, $body, $postId)
{
    $sql = "
        UPDATE
            post
        SET
            title = :title,
            body = :body
        WHERE
            id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        throw new Exception('Не вдалось підготувати запит до БД');
    }
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'post_id' => $postId,
        )
    );
    if ($result === false)
    {
        throw new Exception('Не вдалося відредагувати запит у БД');
    }
    redirectAndExit('list-posts.php');
    return true;
}

/**
Функція додає пост в БД (string)
 */

function newPost(PDO $pdo, $title, $body, $userId){
    echo $userId;
    $sql = "INSERT INTO
            post
            (title, body, user_id, created_at)
            VALUES
            (:title,:body, :user_id, :created_at)
            ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        throw new Exception('Не вдалося підготувати запит до БД');
    }

    // Now run the query, with these parameters
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'user_id' => $userId,
            'created_at' => getSqlDateForNow(),
        )
    );
    if ($result === false)
    {
        throw new Exception('Не вдалося відредагувати запит у БД');
    }

    return $pdo->lastInsertId();
}