<?php
require_once 'lib/common.php';
require_once 'lib/edit-post.php';
require_once 'lib/view-post.php';

session_start();

// Редагувати дописи можуть тільки користувачі які залогінені
if (!isLoggedIn())
{
    redirectAndExit('index.php');
}

$title = $body = '';
$pdo = getPDO();

$postId = null;
if (isset($_GET['post_id']))
{
    $post = getPostRow($pdo, $_GET['post_id']);
    if ($post)
    {
        $postId = $_GET['post_id'];
        $title = $post['title'];
        $body = $post['body'];
    }
}

$errors = array();
if ($_POST)
{
    $title = $_POST['post-title'];
    if (!$title)
    {
        $errors[] = 'У запису має бути заголовок';
    }
    $body = $_POST['post-body'];
    if (!$body)
    {
        $errors[] = 'Додайте текст запису';
    }

    if (!$errors)
    {
        $pdo = getPDO();
        // Додаємо запис чи редагуємо
        if ($postId)
        {
            editPost($pdo, $title, $body, $postId);
        }
        else
        {
            $userId = getAuthUserId($pdo);
            redirectAndExit('new-post.php');
        }
    }

    if (!$errors)
    {
        redirectAndExit('edit-post.php?post_id=' . $postId);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Code::in | Редагувати запис</title>
    <?php require 'templates/head.php' ?>
    <?php require 'templates/title.php' ?>
</head>
<body>

<h2>Редагувати запис</h2>

<?php if ($errors): ?>
    <div class="error box">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<form method="post" class="new-post">
    <div>
        <label for="post-title">Назва:</label>
        <input
            id="post-title"
            name="post-title"
            type="text"
            value="<?php echo htmlEscape($title) ?>"
        />
    </div>
    <div>
        <label for="post-body">Текст:</label>
        <textarea
            id="post-body"
            name="post-body"
            rows="12"
            cols="70"
        ><?php echo htmlEscape($body) ?></textarea>
    </div>
    <div>
        <button type="submit" class="btn btn-success">Зберегти</button>
        <a href="index.php" class="btn btn-primary" role="button">Відмінити</a>
    </div>
</form>
</body>
</html>
