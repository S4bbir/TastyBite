<?php
  $post = $post ?? null;
  $route = $post ? 'food/edit' : 'food/create';
?>
<section class="auth-panel wide">
  <div>
    <p class="eyebrow">Food Experience</p>
    <h1><?= $post ? 'Edit your story' : 'Share a story' ?></h1>
    <p class="lead">Write a descriptive review and optionally connect it to a restaurant or food item.</p>
  </div>

  <form class="form-card" action="<?= e(url($route, $post ? ['id' => $post['id']] : [])) ?>" method="post" data-validate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <?php if ($post): ?>
      <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
    <?php endif; ?>

    <label>Title
      <input name="title" value="<?= e($post['title'] ?? '') ?>" required>
      <?php if (!empty($errors['title'])): ?><small class="field-error"><?= e($errors['title']) ?></small><?php endif; ?>
    </label>

    <label>Content
      <textarea name="content" rows="8" required><?= e($post['content'] ?? '') ?></textarea>
      <?php if (!empty($errors['content'])): ?><small class="field-error"><?= e($errors['content']) ?></small><?php endif; ?>
    </label>

    <label>Type
      <?php $type = $post['post_type'] ?? 'food'; ?>
      <select name="post_type" required>
        <option value="restaurant" <?= $type === 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
        <option value="food" <?= $type === 'food' ? 'selected' : '' ?>>Food</option>
        <option value="both" <?= $type === 'both' ? 'selected' : '' ?>>Both</option>
      </select>
      <?php if (!empty($errors['post_type'])): ?><small class="field-error"><?= e($errors['post_type']) ?></small><?php endif; ?>
    </label>

    <label>Restaurant
      <?php $restaurantId = (int) ($post['restaurant_id'] ?? 0); ?>
      <select name="restaurant_id">
        <option value="0">No restaurant link</option>
        <?php foreach ($restaurants as $restaurant): ?>
          <option value="<?= (int) $restaurant['id'] ?>" <?= $restaurantId === (int) $restaurant['id'] ? 'selected' : '' ?>>
            <?= e($restaurant['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['restaurant_id'])): ?><small class="field-error"><?= e($errors['restaurant_id']) ?></small><?php endif; ?>
    </label>

    <label>Food item
      <?php $menuItemId = (int) ($post['menu_item_id'] ?? 0); ?>
      <select name="menu_item_id">
        <option value="0">No food item link</option>
        <?php foreach ($items as $item): ?>
          <option value="<?= (int) $item['id'] ?>" <?= $menuItemId === (int) $item['id'] ? 'selected' : '' ?>>
            <?= e($item['name']) ?> &middot; <?= e($item['restaurant_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['menu_item_id'])): ?><small class="field-error"><?= e($errors['menu_item_id']) ?></small><?php endif; ?>
    </label>

    <div class="button-row">
      <button class="button primary" type="submit"><?= $post ? 'Save changes' : 'Publish post' ?></button>
      <a class="button ghost" href="<?= e(url('food')) ?>">Cancel</a>
    </div>
  </form>
</section>
