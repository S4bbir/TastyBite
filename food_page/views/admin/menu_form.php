<?php $item = $item ?? null; ?>
<section class="auth-panel wide">
  <div>
    <p class="eyebrow"><?= e($restaurant['name']) ?></p>
    <h1><?= $item ? 'Edit menu item' : 'Add menu item' ?></h1>
    <p class="lead">Upload JPEG or PNG images up to 2MB.</p>
  </div>

  <form class="form-card" action="<?= e(url('admin/menu/save')) ?>" method="post" enctype="multipart/form-data" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['id'] ?>">
    <?php if ($item && !empty($item['id'])): ?>
      <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
    <?php endif; ?>

    <label>Name
      <input name="name" value="<?= e($item['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>
    <label>Description
      <textarea name="description" rows="5" required><?= e($item['description'] ?? '') ?></textarea>
      <?php if (!empty($errors['description'])): ?><small class="field-error"><?= e($errors['description']) ?></small><?php endif; ?>
    </label>
    <label>Price
      <input type="number" min="0.01" step="0.01" name="price" value="<?= e((string) ($item['price'] ?? '')) ?>" required>
      <?php if (!empty($errors['price'])): ?><small class="field-error"><?= e($errors['price']) ?></small><?php endif; ?>
    </label>
    <label>Image
      <input type="file" name="image" accept="image/jpeg,image/png">
      <?php if (!empty($errors['image'])): ?><small class="field-error"><?= e($errors['image']) ?></small><?php endif; ?>
    </label>
    <?php if (!empty($item['image_path'])): ?>
      <img class="preview-image" src="<?= e(asset($item['image_path'])) ?>" alt="<?= e($item['name']) ?>">
    <?php endif; ?>

    <div class="button-row">
      <button class="button primary" type="submit">Save menu item</button>
      <a class="button ghost" href="<?= e(url('admin/menu', ['restaurant_id' => $restaurant['id']])) ?>">Cancel</a>
    </div>
  </form>
</section>

