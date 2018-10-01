<?php

/**
   Додаємо коментар до певного запису
 */
function addCommentToPost(PDO $pdo, $postId, array $commentData)
{
    $errors = array();

    if (empty($commentData['name']))
    {
        $errors['name'] = 'Введіть своє ім_я';
    }
    if (empty($commentData['text']))
    {
        $errors['text'] = 'Не забудьте про коментар';
    }

    // Якшо помилки відсутні додаємо коментар
    if (!$errors)
    {
        $sql = "
            INSERT INTO
                comment
            (name, website, text, created_at, post_id)
            VALUES(:name, :website, :text, :created_at, :post_id)
        ";
        $stmt = $pdo->prepare($sql);
        if ($stmt === false)
        {
            throw new Exception('Не вдалось підготувати запит до БД');
        }

        $name = htmlEscapeFull($commentData['name']);
        $website = htmlEscapeFull($commentData['website']);
        $text = htmlEscapeFull($commentData['text']);
        $commentDataEscaped = Array($name, $website, $text);
        $result = $stmt->execute(
            array_merge(
                $commentDataEscaped,
                array('post_id' => $postId, 'created_at' => getSqlDateForNow(), )
            )
        );

        if ($result === false)
        {
            $errorInfo = $pdo->errorInfo();
            if ($errorInfo)
            {
                $errors[] = $errorInfo[2];
            }
        }
    }

    return $errors;
}

/**
   Фунція перенаправляє на сторінку перегляду запису, якщо коментар додано успішно
 */
function handleAddComment(PDO $pdo, $postId, array $commentData)
{
    $errors = addCommentToPost(
        $pdo,
        $postId,
        $commentData
    );

    if (!$errors)
    {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }

    return $errors;
}

/**
   Видалення певного коментаря з запису
 */
function deleteComment(PDO $pdo, $postId, $commentId)
{
    // Додатково перевіряємо post_id
    $sql = "
        DELETE FROM
            comment
        WHERE
            post_id = :post_id
            AND id = :comment_id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        throw new Exception('Не вдалось підготувати запит до БД');
    }

    $result = $stmt->execute(
        array(
            'post_id' => $postId,
            'comment_id' => $commentId,
        )
    );

    return $result !== false;
}


/**
   Видалення коментарів та перенаправлення на сторінку перегляду запису
 */
function handleDeleteComment(PDO $pdo, $postId, array $deleteResponse)
{
    if (isLoggedIn())
    {
        $keys = array_keys($deleteResponse);
        $deleteCommentId = $keys[0];
        if ($deleteCommentId)
        {
            deleteComment($pdo, $postId, $deleteCommentId);
        }

        redirectAndExit('view-post.php?post_id=' . $postId);
    }
}

/**
   Перегляд окремого допису
 */
function getPostRow(PDO $pdo, $postId)
{

    $stmt = $pdo->prepare(
        'SELECT
            title, created_at, body,
            (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) comment_count
        FROM
            post
        WHERE
            id = :id'
    );
    if ($stmt === false)
    {
        throw new Exception('Не вдалось підготувати запит до БД');
    }
    $result = $stmt->execute(
        array('id' => $postId, )
    );
    if ($result === false)
    {
        throw new Exception('Проблеми з запитом до БД');
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}
