<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e(($pageTitle ?? 'Home') . ' | ' . APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Text:ital,wght@0,100..900;1,100..900&family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
  </head>
  <body>
    <?php require BASE_PATH . '/food_page/views/layout/navbar.php'; ?>

    <main class="page">
      <?php foreach (flashes() as $flash): ?>
        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endforeach; ?>

      <?= $content ?>
    </main>

    <footer class="site-footer">
      <div class="footer-brand">
        <span class="footer-mark"><img src="<?= e(asset('favicon.svg')) ?>" alt="" aria-hidden="true"></span>
        <div>
          <strong><?= e(APP_NAME) ?></strong>
          <p class="footer-contact">Dhaka, Bangladesh</p>
          <p class="footer-contact">info@tastybite.test</p>
          <p class="footer-contact">+880 1234-567890</p>
          <div class="footer-socials" aria-label="Social links">
            <a href="#" aria-label="X">X</a>
            <a href="#" aria-label="LinkedIn">in</a>
            <a href="#" aria-label="Instagram">ig</a>
            <a href="#" aria-label="Facebook">f</a>
          </div>
        </div>
      </div>

      <div class="footer-column">
        <span>Product</span>
        <a href="<?= e(url('browse/restaurants')) ?>">Restaurants</a>
        <a href="<?= e(url('food')) ?>">Food Stories</a>
        <a href="<?= e(url('api/search')) ?>">Search</a>
        <a href="<?= e(url('auth/register')) ?>">Join</a>
      </div>

      <div class="footer-column">
        <span>Company</span>
        <a href="<?= e(url('home')) ?>">Home</a>
        <a href="<?= e(url('browse/restaurants')) ?>">Features</a>
        <a href="<?= e(url('food')) ?>">Food Experience</a>
        <?php if (is_admin()): ?>
          <a href="<?= e(url('admin/dashboard')) ?>">Admin</a>
        <?php endif; ?>
      </div>

      <div class="footer-column">
        <span>Support</span>
        <?php if (is_logged_in()): ?>
          <a href="<?= e(url('auth/profile')) ?>">Profile</a>
        <?php else: ?>
          <a href="<?= e(url('auth/login')) ?>">Login</a>
          <a href="<?= e(url('auth/register')) ?>">Register</a>
        <?php endif; ?>
        <a href="<?= e(url('browse/restaurants')) ?>">Browse Help</a>
        <a href="<?= e(url('food')) ?>">Community</a>
      </div>
    </footer>

    <div class="toast-zone" id="toastZone" aria-live="polite"></div>

    <script>
      window.FOOD_BLOG = {
        csrf: "<?= e(csrf_token()) ?>",
        baseUrl: "<?= e(base_url()) ?>",
        loginUrl: "<?= e(url('auth/login')) ?>"
      };
    </script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
  </body>
</html>
