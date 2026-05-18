<section class="section-heading">
  <div>
    <p class="eyebrow">Food Experience</p>
    <h1>Descriptive reviews and comments</h1>
  </div>
  <?php if (is_logged_in()): ?>
    <a class="button primary" href="<?= e(url('food/create')) ?>">Share experience</a>
  <?php endif; ?>
</section>

<?php if (!is_logged_in()): ?>
  <section class="panel compact">
    <p class="muted">Visitors can read every food experience post and comment. Login as a member to post or comment.</p>
  </section>
<?php endif; ?>

<section class="feed">
  <?php foreach ($posts as $post): ?>
    <article class="post" id="post-<?= (int) $post['id'] ?>">
      <img class="post-image" src="<?= e(food_story_image($post)) ?>" alt="<?= e($post['title']) ?>">
      <header class="post-header">
        <div>
          <span class="pill"><?= e($post['post_type']) ?></span>
          <h2><?= e($post['title']) ?></h2>
          <small>
            By <?= e($post['author']) ?> &middot; <?= e($post['created_at']) ?>
            <?php if (!empty($post['restaurant_name'])): ?>
              &middot; <?= e($post['restaurant_name']) ?>
            <?php endif; ?>
            <?php if (!empty($post['menu_item_name'])): ?>
              &middot; <?= e($post['menu_item_name']) ?>
            <?php endif; ?>
          </small>
        </div>
        <?php if (can_edit_owner((int) $post['user_id'])): ?>
          <div class="button-row">
            <a class="button ghost" href="<?= e(url('food/edit', ['id' => $post['id']])) ?>">Edit</a>
            <button class="button danger" type="button" data-delete-url="<?= e(url('api/food-posts/delete', ['id' => $post['id']])) ?>" data-remove-target="#post-<?= (int) $post['id'] ?>">Delete</button>
          </div>
        <?php endif; ?>
      </header>

      <p class="post-content"><?= nl2br(e($post['content'])) ?></p>

      <div class="comments">
        <h3>Comments</h3>
        <?php foreach ($post['comments'] as $comment): ?>
          <div class="comment" id="comment-<?= (int) $comment['id'] ?>">
            <div>
              <strong><?= e($comment['author']) ?></strong>
              <small><?= e($comment['created_at']) ?></small>
            </div>
            <p><?= e($comment['comment']) ?></p>
            <?php if (can_edit_owner((int) $comment['user_id'])): ?>
              <button class="text-danger" type="button" data-delete-url="<?= e(url('api/food-comments/delete', ['id' => $comment['id']])) ?>" data-remove-target="#comment-<?= (int) $comment['id'] ?>">Delete</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <?php if (!$post['comments']): ?>
          <p class="muted">No comments yet.</p>
        <?php endif; ?>
      </div>

      <?php if (is_logged_in()): ?>
        <form class="inline-form" action="<?= e(url('api/food-comments/add')) ?>" method="post" data-ajax-form data-reload>
          <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
          <input name="comment" maxlength="1000" placeholder="Add a comment" required>
          <button class="button primary" type="submit">Comment</button>
        </form>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>

  <?php if (!$posts): ?>
    <section class="panel">
      <h2>No food experience posts yet</h2>
      <p class="muted">The first descriptive review will appear here.</p>
    </section>
  <?php endif; ?>
</section>
