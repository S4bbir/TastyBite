<section class="detail-hero">
  <div>
    <p class="eyebrow"><?= e($restaurant['location']) ?> &middot; <?= e($restaurant['area']) ?></p>
    <h1><?= e($restaurant['name']) ?></h1>
    <p class="lead"><?= e($restaurant['short_background']) ?></p>
    <p><?= e($restaurant['goals']) ?></p>
  </div>
  <div class="detail-media-panel">
    <img class="detail-image" src="<?= e(restaurant_image($restaurant)) ?>" alt="<?= e($restaurant['name']) ?>">
    <?php if (is_admin()): ?>
      <div class="button-row">
        <a class="button ghost" href="<?= e(url('admin/restaurant/form', ['id' => $restaurant['id']])) ?>">Edit restaurant</a>
        <a class="button primary" href="<?= e(url('admin/menu', ['restaurant_id' => $restaurant['id']])) ?>">Manage menu</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section-heading">
  <div>
    <p class="eyebrow">Menu</p>
    <h2>Food items</h2>
  </div>
</section>

<section class="item-grid">
  <?php foreach ($items as $item): ?>
    <article class="item-card">
      <img src="<?= e(menu_item_image($item)) ?>" alt="<?= e($item['name']) ?>">
      <div>
        <h3><?= e($item['name']) ?></h3>
        <p><?= e(excerpt($item['description'], 120)) ?></p>
        <strong><?= e(number_format((float) $item['price'], 2)) ?> BDT</strong>
        <a class="text-link" href="<?= e(url('browse/item', ['id' => $item['id']])) ?>">Details and reviews</a>
      </div>
    </article>
  <?php endforeach; ?>
  <?php if (!$items): ?>
    <p class="muted">No menu items have been added yet.</p>
  <?php endif; ?>
</section>

<section class="panel" id="restaurant-reviews">
  <div class="section-heading">
    <div>
      <p class="eyebrow">Restaurant Reviews</p>
      <h2>Member comments</h2>
    </div>
  </div>

  <?php if (is_member()): ?>
    <form class="inline-form" action="<?= e(url('api/restaurant-reviews/add')) ?>" method="post" data-ajax-form data-reload>
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['id'] ?>">
      <select name="rating" required>
        <option value="">Rating</option>
        <option value="5">5 - Excellent</option>
        <option value="4">4 - Good</option>
        <option value="3">3 - Average</option>
        <option value="2">2 - Poor</option>
        <option value="1">1 - Bad</option>
      </select>
      <input name="comment" maxlength="1000" placeholder="Write a restaurant review" required>
      <button class="button primary" type="submit">Post</button>
    </form>
  <?php elseif (!is_logged_in()): ?>
    <p class="muted"><a href="<?= e(url('auth/login')) ?>">Login</a> as a member to review this restaurant.</p>
  <?php endif; ?>

  <div class="review-list">
    <?php foreach ($restaurantReviews as $review): ?>
      <article class="review" id="restaurant-review-<?= (int) $review['id'] ?>">
        <div>
          <strong><?= e($review['author']) ?></strong>
          <span class="pill"><?= (int) $review['rating'] ?>/5</span>
          <small><?= e($review['created_at']) ?></small>
        </div>
        <p><?= e($review['comment']) ?></p>
        <?php if (can_edit_owner((int) $review['user_id'])): ?>
          <button class="text-danger" type="button" data-delete-url="<?= e(url('api/restaurant-reviews/delete', ['id' => $review['id']])) ?>" data-remove-target="#restaurant-review-<?= (int) $review['id'] ?>">Delete</button>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
</section>
