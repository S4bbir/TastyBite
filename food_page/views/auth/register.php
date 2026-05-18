<section class="auth-panel">
  <div>
    <p class="eyebrow">Join</p>
    <h1>Create an account</h1>
    <p class="lead">Register with your email and password. Your account is saved securely so you can log in later.</p>
  </div>

  <form class="form-card" action="<?= e(url('auth/register')) ?>" method="post" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <label>Name
      <input name="name" value="<?= e($old['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>

    <label>Email
      <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
      <?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
    </label>

    <label>Password
      <span class="password-field">
        <input type="password" name="password" minlength="8" required>
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
      <?php if (!empty($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?>
    </label>

    <label>Confirm password
      <span class="password-field">
        <input type="password" name="password_confirm" minlength="8" required data-match="password">
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
      <?php if (!empty($errors['password_confirm'])): ?><small class="field-error"><?= e($errors['password_confirm']) ?></small><?php endif; ?>
    </label>

    <button class="button primary" type="submit">Create account</button>
    <p class="form-note">Already registered? <a href="<?= e(url('auth/login')) ?>">Login</a></p>
  </form>
</section>
