<section class="auth-panel">
  <div>
    <p class="eyebrow">Welcome back</p>
    <h1>Login</h1>
    <p class="lead">Members can review food, and admins can manage restaurants, users, and moderation.</p>
  </div>

  <form class="form-card" action="<?= e(url('auth/login')) ?>" method="post" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <label>Email
      <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
      <?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
    </label>

    <label>Password
      <span class="password-field">
        <input type="password" name="password" required>
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
    </label>

    <label class="check-line">
      <input type="checkbox" name="remember_me" value="1">
      <span>Remember me for 30 days</span>
    </label>

    <button class="button primary" type="submit">Login</button>
    <p class="form-note">Need an account? <a href="<?= e(url('auth/register')) ?>">Register</a></p>
  </form>
</section>
