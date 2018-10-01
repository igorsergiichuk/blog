<?php // Виводимо помилки у вигляді списку
require_once 'lib/common.php';
require_once 'lib/view-post.php';
?>

<?php if ($errors): ?>
    <div class="error box comment-margin">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<h3>Додати коментар</h3>

<form
    action="view-post.php?action=add-comment&amp;post_id=<?php echo $postId?>"
    method="post"
    class="comment-form"
>
    <div>
        <label for="comment-name">
            Ім'я:
        </label>
        <input
            type="text"
            id="comment-name"
            name="comment-name"
            value="<?php echo htmlEscapeFull($commentData['name']) ?>"
        />
    </div>
    <div>
        <label for="comment-website">
            Website:
        </label>
        <input
            type="text"
            id="comment-website"
            name="comment-website"
            value="<?php echo htmlEscapeFull($commentData['website']) ?>"
        />
    </div>
    <div>
        <label for="comment-text">
            Коментар:
        </label>
        <textarea
            id="comment-text"
            name="comment-text"
            rows="8"
            cols="70"
        ><?php echo htmlEscapeFull($commentData['text']) ?></textarea>
    </div>

    <div>
        <button type="submit" value="Submit comment" class="btn btn-success">Додати коментар</button>
    </div>
</form>
