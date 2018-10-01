<?php
require_once 'lib/common.php';

session_start();

$pdo = getPDO();
$posts = getAllPosts($pdo);

$notFound = isset($_GET['not-found']);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Code::in | Блог</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
<?php require 'templates/title.php' ?>

<?php if ($notFound): ?>
    <div class="error box">
        Error: запис не знайдено
    </div>
<?php endif ?>

<div class="post-list">
    <?php foreach ($posts as $post): ?>
        <div class="post-synopsis">
            <h2>
                <?php echo htmlEscape($post['title']) ?>
            </h2>
            <div class="meta">
                <?php echo convertSqlDate($post['created_at']) ?>

                (коментарів: <?php echo $post['comment_count'] ?>)
            </div>
            <p>
                <?php echo viewLimit($post['body']) ?>
            </p>
            <div class="post-controls">
                <a href="view-post.php?post_id=<?php echo $post['id'] ?>" class="btn btn-success active" role="button"> Більше... </a>
                <?php if (isLoggedIn()): ?>

                <a href="edit-post.php?post_id=<?php echo $post['id'] ?>" class="btn btn-primary active" role="button">Редагувати</a>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
</div>

</body>
</html>
