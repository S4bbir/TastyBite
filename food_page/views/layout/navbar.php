<header class="site-header">
  <a class="brand" href="<?= e(url('home')) ?>">
    <span class="brand-mark"><img src="<?= e(asset('favicon.svg')) ?>" alt="" aria-hidden="true"></span>
    <span>
      <strong><?= e(APP_NAME) ?></strong>
      <small>Restaurants, menus, and stories</small>
    </span>
  </a>

  <nav class="nav-links" aria-label="Primary navigation">
    <a href="<?= e(url('home')) ?>">Home</a>
    <a href="<?= e(url('browse/restaurants')) ?>">Restaurants</a>
    <a href="<?= e(url('food')) ?>">Food Experience</a>
    <?php if (is_admin()): ?>
      <a href="<?= e(url('admin/dashboard')) ?>">Admin</a>
      <a href="<?= e(url('admin/restaurants')) ?>">Manage Content</a>
      <a href="<?= e(url('admin/members')) ?>">Members</a>
    <?php endif; ?>
  </nav>

  <div class="nav-actions">
    <?php if (is_logged_in()): ?>
      <a class="button ghost" href="<?= e(url('auth/profile')) ?>"><?= e(current_user_name()) ?></a>
      <form action="<?= e(url('auth/logout')) ?>" method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <button class="button subtle" type="submit">Logout</button>
      </form>
    <?php else: ?>
      <a class="button ghost" href="<?= e(url('auth/login')) ?>">Login</a>
      <a class="button primary" href="<?= e(url('auth/register')) ?>">Register</a>
    <?php endif; ?>
  </div>
</header>
