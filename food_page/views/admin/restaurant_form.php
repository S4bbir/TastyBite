<?php $restaurant = $restaurant ?? null; ?>
<section class="auth-panel wide">
  <div>
    <p class="eyebrow">Admin</p>
    <h1><?= $restaurant ? 'Edit restaurant' : 'Add restaurant' ?></h1>
    <p class="lead">Restaurant pages are generated from these database records.</p>
  </div>

  <form class="form-card" action="<?= e(url('admin/restaurant/save')) ?>" method="post" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <?php if ($restaurant && !empty($restaurant['id'])): ?>
      <input type="hidden" name="id" value="<?= (int) $restaurant['id'] ?>">
    <?php endif; ?>

    <label>Name
      <input name="name" value="<?= e($restaurant['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>
    <label>Location
      <input name="location" value="<?= e($restaurant['location'] ?? '') ?>" required>
      <?php if (!empty($errors['location'])): ?><small class="field-error"><?= e($errors['location']) ?></small><?php endif; ?>
    </label>
    <label>Area
      <input name="area" value="<?= e($restaurant['area'] ?? '') ?>" required>
      <?php if (!empty($errors['area'])): ?><small class="field-error"><?= e($errors['area']) ?></small><?php endif; ?>
    </label>
    <label>Short background
      <textarea name="short_background" rows="4" required><?= e($restaurant['short_background'] ?? '') ?></textarea>
      <?php if (!empty($errors['short_background'])): ?><small class="field-error"><?= e($errors['short_background']) ?></small><?php endif; ?>
    </label>
    <label>Goals
      <textarea name="goals" rows="4" required><?= e($restaurant['goals'] ?? '') ?></textarea>
      <?php if (!empty($errors['goals'])): ?><small class="field-error"><?= e($errors['goals']) ?></small><?php endif; ?>
    </label>

    <div class="button-row">
      <button class="button primary" type="submit">Save restaurant</button>
      <a class="button ghost" href="<?= e(url('admin/restaurants')) ?>">Cancel</a>
    </div>
  </form>
</section>

