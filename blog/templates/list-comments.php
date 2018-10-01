<?php
# Коментарі
?>
<form
    action="view-post.php?action=delete-comment&amp;post_id=<?php echo $postId?>&amp;"
    method="post"
    class="comment-list"
>
    <h4><?php echo $commentCount ?> коментарів</h4>

    <?php foreach (getCommentsForPost($pdo, $postId) as $comment): ?>
        <div class="comment">
            <div class="comment-meta">
                Прокоментував(-ла)
                <?php echo htmlEscapeFull($comment['name']) ?>
                від
                <?php echo convertSqlDate($comment['created_at']) ?>
                <?php if (isLoggedIn()): ?>
                <button type="submit"  name="delete-comment[<?php echo $comment['id'] ?>]" class="btn btn-danger">Видалити коментар</button>
                <?php endif ?>
            </div>
            <div class="comment-body">
                <?php // This is already escaped ?>
                <?php echo convertNewlinesToParagraphs($comment['text']) ?>
            </div>
        </div>
    <?php endforeach ?>
</form>
