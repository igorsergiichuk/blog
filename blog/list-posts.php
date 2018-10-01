<?php
require_once 'lib/common.php';
require_once 'lib/list-posts.php';

session_start();

// Тільки для авторизованих користувачів
if (!isLoggedIn())
{
    redirectAndExit('login.php');
}

if ($_POST)
{
    $deleteResponse = $_POST['delete-post'];
    if ($deleteResponse)
    {
        $keys = array_keys($deleteResponse);
        $deletePostId = $keys[0];
        if ($deletePostId)
        {
            deletePost(getPDO(), $deletePostId);
            redirectAndExit('list-posts.php');
        }
    }
}

// Під'єднатися до БД та вивести список
$pdo = getPDO();
$posts = getAllPosts($pdo);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Code::in | Перелік записів</title>
    <?php require 'templates/head.php' ?>
    <?php require 'templates/title.php' ?>
</head>
<body>

<h2>Записи</h2>

<p id="list-posts">Кількість постів <?php echo count($posts) ?>.

<form method="post">
    <table id="post-list">
        <thead>
        <tr>
            <th>Назва</th>
            <th>Дата створення</th>
            <th>Кількість коментарів</th>
            <th />
            <th />
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td>
                    <a
                        href="view-post.php?post_id=<?php echo $post['id']?>"
                    ><?php echo htmlEscape($post['title']) ?></a>
                </td>
                <td>
                    <?php echo convertSqlDate($post['created_at']) ?>
                </td>
                <td>
                    <?php echo $post['comment_count'] ?>
                </td>
                <td>
                    <a href="edit-post.php?post_id=<?php echo $post['id']?>" class="btn btn-warning" role="button">Редагувати</a>
                </td>
                <td>
                    <button type="submit" name="delete-post[<?php echo $post['id']?>]" value="Видалити" class="btn btn-danger">Видалити</button>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</form>
</body>
</html>
