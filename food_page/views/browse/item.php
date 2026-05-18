<section class="detail-hero item-detail">
  <div>
    <p class="eyebrow"><?= e($item['restaurant_name']) ?> &middot; <?= e($item['location']) ?> &middot; <?= e($item['area']) ?></p>
    <h1><?= e($item['name']) ?></h1>
    <p class="lead"><?= e($item['description']) ?></p>
    <strong class="price"><?= e(number_format((float) $item['price'], 2)) ?> BDT</strong>
    <div class="button-row">
      <a class="button ghost" href="<?= e(url('browse/restaurant', ['id' => $item['restaurant_id']])) ?>">Back to restaurant</a>
      <?php if (is_admin()): ?>
        <a class="button primary" href="<?= e(url('admin/menu/form', ['restaurant_id' => $item['restaurant_id'], 'id' => $item['id']])) ?>">Edit item</a>
      <?php endif; ?>
    </div>
  </div>
  <img class="detail-image" src="<?= e(menu_item_image($item)) ?>" alt="<?= e($item['name']) ?>">
</section>

<section class="panel" id="item-reviews">
  <div class="section-heading">
    <div>
      <p class="eyebrow">Food Item Reviews</p>
      <h2>What members say</h2>
    </div>
  </div>

  <?php if (is_member()): ?>
    <form class="inline-form" action="<?= e(url('api/reviews/add')) ?>" method="post" data-ajax-form data-reload>
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="menu_item_id" value="<?= (int) $item['id'] ?>">
      <input name="comment" maxlength="1000" placeholder="Write a review for this dish" required>
      <button class="button primary" type="submit">Post review</button>
    </form>
  <?php elseif (!is_logged_in()): ?>
    <p class="muted"><a href="<?= e(url('auth/login')) ?>">Login</a> as a member to post a review.</p>
  <?php endif; ?>

  <div class="review-list">
    <?php foreach ($reviews as $review): ?>
      <article class="review" id="review-<?= (int) $review['id'] ?>">
        <div>
          <strong><?= e($review['author']) ?></strong>
          <small><?= e($review['created_at']) ?></small>
        </div>
        <p><?= e($review['comment']) ?></p>
        <?php if (can_edit_owner((int) $review['user_id'])): ?>
          <button class="text-danger" type="button" data-delete-url="<?= e(url('api/reviews/delete', ['id' => $review['id']])) ?>" data-remove-target="#review-<?= (int) $review['id'] ?>">Delete</button>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if (!$reviews): ?>
      <p class="muted">No reviews yet.</p>
    <?php endif; ?>
  </div>
</section>
