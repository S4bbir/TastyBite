<section class="auth-panel wide">
  <div>
    <p class="eyebrow">Account</p>
    <h1>Profile</h1>
    <p class="lead">Update your name, email, profile picture, or password.</p>
    <?php if (!empty($user['profile_picture'])): ?>
      <img class="avatar-large" src="<?= e(asset($user['profile_picture'])) ?>" alt="<?= e($user['name']) ?>">
    <?php endif; ?>
  </div>

  <form class="form-card" action="<?= e(url('auth/profile')) ?>" method="post" enctype="multipart/form-data" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <label>Name
      <input name="name" value="<?= e($user['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>

    <label>Email
      <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required>
      <?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
    </label>

    <label>Profile picture
      <input type="file" name="profile_picture" accept="image/jpeg,image/png">
      <?php if (!empty($errors['profile_picture'])): ?><small class="field-error"><?= e($errors['profile_picture']) ?></small><?php endif; ?>
    </label>

    <div class="form-divider"></div>

    <label>Current password
      <span class="password-field">
        <input type="password" name="current_password">
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
      <?php if (!empty($errors['current_password'])): ?><small class="field-error"><?= e($errors['current_password']) ?></small><?php endif; ?>
    </label>

    <label>New password
      <span class="password-field">
        <input type="password" name="new_password" minlength="8">
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
      <?php if (!empty($errors['new_password'])): ?><small class="field-error"><?= e($errors['new_password']) ?></small><?php endif; ?>
    </label>

    <label>Confirm new password
      <span class="password-field">
        <input type="password" name="new_password_confirm" minlength="8" data-match="new_password">
        <button type="button" class="password-toggle" data-toggle-password aria-label="Show password">Show</button>
      </span>
      <?php if (!empty($errors['new_password_confirm'])): ?><small class="field-error"><?= e($errors['new_password_confirm']) ?></small><?php endif; ?>
    </label>

    <button class="button primary" type="submit">Save profile</button>
  </form>
</section>
