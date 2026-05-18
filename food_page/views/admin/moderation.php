<section class="section-heading">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Moderation</h1>
  </div>
</section>

<section class="moderation-grid">
  <article class="panel">
    <h2>Food item reviews</h2>
    <?php foreach ($itemReviews as $review): ?>
      <div class="mod-row" id="item-review-<?= (int) $review['id'] ?>">
        <div>
          <strong><?= e($review['item_name']) ?></strong>
          <p><?= e($review['comment']) ?></p>
          <small><?= e($review['author']) ?> &middot; <?= e($review['created_at']) ?></small>
        </div>
        <button class="text-danger" type="button" data-delete-url="<?= e(url('api/reviews/delete', ['id' => $review['id']])) ?>" data-remove-target="#item-review-<?= (int) $review['id'] ?>">Delete</button>
      </div>
    <?php endforeach; ?>
    <?php if (!$itemReviews): ?><p class="muted">No food item reviews.</p><?php endif; ?>
  </article>

  <article class="panel">
    <h2>Restaurant reviews</h2>
    <?php foreach ($restaurantReviews as $review): ?>
      <div class="mod-row" id="restaurant-review-mod-<?= (int) $review['id'] ?>">
        <div>
          <strong><?= e($review['restaurant_name']) ?> &middot; <?= (int) $review['rating'] ?>/5</strong>
          <p><?= e($review['comment']) ?></p>
          <small><?= e($review['author']) ?> &middot; <?= e($review['created_at']) ?></small>
        </div>
        <button class="text-danger" type="button" data-delete-url="<?= e(url('api/restaurant-reviews/delete', ['id' => $review['id']])) ?>" data-remove-target="#restaurant-review-mod-<?= (int) $review['id'] ?>">Delete</button>
      </div>
    <?php endforeach; ?>
    <?php if (!$restaurantReviews): ?><p class="muted">No restaurant reviews.</p><?php endif; ?>
  </article>

  <article class="panel">
    <h2>Food Experience posts</h2>
    <?php foreach ($posts as $post): ?>
      <div class="mod-row" id="post-mod-<?= (int) $post['id'] ?>">
        <div>
          <strong><?= e($post['title']) ?></strong>
          <p><?= e(excerpt($post['content'], 120)) ?></p>
          <small><?= e($post['author']) ?> &middot; <?= e($post['created_at']) ?></small>
        </div>
        <button class="text-danger" type="button" data-delete-url="<?= e(url('api/food-posts/delete', ['id' => $post['id']])) ?>" data-remove-target="#post-mod-<?= (int) $post['id'] ?>">Delete</button>
      </div>
    <?php endforeach; ?>
    <?php if (!$posts): ?><p class="muted">No posts.</p><?php endif; ?>
  </article>

  <article class="panel">
    <h2>Food Experience comments</h2>
    <?php foreach ($comments as $comment): ?>
      <div class="mod-row" id="comment-mod-<?= (int) $comment['id'] ?>">
        <div>
          <strong><?= e($comment['post_title']) ?></strong>
          <p><?= e($comment['comment']) ?></p>
          <small><?= e($comment['author']) ?> &middot; <?= e($comment['created_at']) ?></small>
        </div>
        <button class="text-danger" type="button" data-delete-url="<?= e(url('api/food-comments/delete', ['id' => $comment['id']])) ?>" data-remove-target="#comment-mod-<?= (int) $comment['id'] ?>">Delete</button>
      </div>
    <?php endforeach; ?>
    <?php if (!$comments): ?><p class="muted">No comments.</p><?php endif; ?>
  </article>
</section>
