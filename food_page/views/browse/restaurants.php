<section class="section-heading">
  <div>
    <p class="eyebrow">Browse</p>
    <h1>Restaurants and menu items</h1>
  </div>
  <?php if (is_admin()): ?>
    <a class="button primary" href="<?= e(url('admin/restaurant/form')) ?>">Add restaurant</a>
  <?php endif; ?>
</section>

<section class="panel">
  <form class="search-grid" data-search-form>
    <input type="search" name="q" placeholder="Search restaurant, dish, or cuisine">
    <select name="location">
      <option value="">All locations</option>
      <?php foreach ($locations as $location): ?>
        <option value="<?= e($location) ?>"><?= e($location) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="area">
      <option value="">All areas</option>
      <?php foreach ($areas as $area): ?>
        <option value="<?= e($area) ?>"><?= e($area) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" min="0" step="0.01" name="min_price" placeholder="Min price">
    <input type="number" min="0" step="0.01" name="max_price" placeholder="Max price">
  </form>
  <div class="search-results" data-search-results></div>
</section>

<section class="card-grid">
  <?php foreach ($restaurants as $restaurant): ?>
    <article class="card image-card">
      <img class="card-image" src="<?= e(restaurant_image($restaurant)) ?>" alt="<?= e($restaurant['name']) ?>">
      <p class="eyebrow"><?= e($restaurant['location']) ?> &middot; <?= e($restaurant['area']) ?></p>
      <h2><?= e($restaurant['name']) ?></h2>
      <p><?= e($restaurant['short_background']) ?></p>
      <p><strong><?= (int) $restaurant['menu_count'] ?></strong> menu items</p>
      <a class="button ghost" href="<?= e(url('browse/restaurant', ['id' => $restaurant['id']])) ?>">Open restaurant</a>
    </article>
  <?php endforeach; ?>
</section>
