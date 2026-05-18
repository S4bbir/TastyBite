<section class="hero visual-hero" style="--hero-image: url('<?= e(site_image('hero-food-blog.jpg')) ?>')">
  <div class="hero-copy">
    <p class="eyebrow">Online Food Blog</p>
    <h1>Discover restaurants, menus, and real food experiences.</h1>
    <p class="lead">
      Browse restaurants as a visitor, sign in as a member to review dishes and share stories,
      or manage the full blog as an admin.
    </p>
    <div class="button-row">
      <a class="button primary" href="<?= e(url('browse/restaurants')) ?>">Browse restaurants</a>
      <?php if (!is_logged_in()): ?>
        <a class="button ghost" href="<?= e(url('auth/register')) ?>">Create account</a>
      <?php elseif (is_admin()): ?>
        <a class="button ghost" href="<?= e(url('admin/dashboard')) ?>">Open dashboard</a>
      <?php else: ?>
        <a class="button ghost" href="<?= e(url('food/create')) ?>">Share experience</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="panel">
  <div class="section-heading">
    <div>
      <p class="eyebrow">Search</p>
      <h2>Find food by name, location, area, or price</h2>
    </div>
  </div>
  <form class="search-grid" id="searchForm" data-search-form>
    <input type="search" name="q" placeholder="Restaurant, food item, or cuisine">
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

<section class="section-heading">
  <div>
    <p class="eyebrow">Restaurants</p>
    <h2>Latest restaurants</h2>
  </div>
  <a href="<?= e(url('browse/restaurants')) ?>">View all</a>
</section>

<section class="card-grid">
  <?php foreach ($restaurants as $restaurant): ?>
    <article class="card image-card">
      <img class="card-image" src="<?= e(restaurant_image($restaurant)) ?>" alt="<?= e($restaurant['name']) ?>">
      <p class="eyebrow"><?= e($restaurant['location']) ?> &middot; <?= e($restaurant['area']) ?></p>
      <h3><?= e($restaurant['name']) ?></h3>
      <p><?= e($restaurant['short_background']) ?></p>
      <a class="text-link" href="<?= e(url('browse/restaurant', ['id' => $restaurant['id']])) ?>">View menu</a>
    </article>
  <?php endforeach; ?>
</section>

<section class="section-heading">
  <div>
    <p class="eyebrow">Stories</p>
    <h2>Food Experience highlights</h2>
  </div>
  <a href="<?= e(url('food')) ?>">Read all</a>
</section>

<section class="story-list">
  <?php foreach ($posts as $post): ?>
    <article class="story">
      <img class="story-image" src="<?= e(food_story_image($post)) ?>" alt="<?= e($post['title']) ?>">
      <span class="pill"><?= e($post['post_type']) ?></span>
      <h3><?= e($post['title']) ?></h3>
      <p><?= e(excerpt($post['content'], 180)) ?></p>
      <small>By <?= e($post['author']) ?> &middot; <?= e($post['created_at']) ?></small>
    </article>
  <?php endforeach; ?>
</section>
