<section class="section-heading">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Dashboard</h1>
  </div>
  <div class="button-row">
    <a class="button primary" href="<?= e(url('admin/restaurant/form')) ?>">Add restaurant</a>
    <a class="button ghost" href="<?= e(url('admin/moderation')) ?>">Moderation</a>
  </div>
</section>

<section class="stats-grid">
  <article class="stat"><strong><?= (int) $stats['restaurants'] ?></strong><span>Restaurants</span></article>
  <article class="stat"><strong><?= (int) $stats['menuItems'] ?></strong><span>Menu items</span></article>
  <article class="stat"><strong><?= (int) $stats['reviews'] ?></strong><span>Food reviews</span></article>
  <article class="stat"><strong><?= (int) $stats['posts'] ?></strong><span>Experience posts</span></article>
  <article class="stat"><strong><?= (int) $stats['members'] ?></strong><span>Members</span></article>
</section>

<section class="admin-actions">
  <a class="admin-tile" href="<?= e(url('admin/restaurants')) ?>">
    <strong>Restaurant and Menu CRUD</strong>
    <span>Create, edit, delete restaurants and menu items.</span>
  </a>
  <a class="admin-tile" href="<?= e(url('admin/members')) ?>">
    <strong>Member Removal</strong>
    <span>Remove member profiles and cascade their content.</span>
  </a>
  <a class="admin-tile" href="<?= e(url('admin/moderation')) ?>">
    <strong>Review Moderation</strong>
    <span>Delete food reviews, restaurant reviews, posts, and comments.</span>
  </a>
</section>

