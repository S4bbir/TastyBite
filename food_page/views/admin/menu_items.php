<section class="section-heading">
  <div>
    <p class="eyebrow"><?= $restaurant ? e($restaurant['name']) : 'Admin' ?></p>
    <h1><?= $restaurant ? 'Menu items' : 'All menu items' ?></h1>
  </div>
  <div class="button-row">
    <a class="button ghost" href="<?= e(url('admin/restaurants')) ?>">Restaurants</a>
    <?php if ($restaurant): ?>
      <a class="button primary" href="<?= e(url('admin/menu/form', ['restaurant_id' => $restaurant['id']])) ?>">Add menu item</a>
    <?php endif; ?>
  </div>
</section>

<section class="item-grid">
  <?php foreach ($items as $item): ?>
    <article class="item-card">
      <img src="<?= e(menu_item_image($item)) ?>" alt="<?= e($item['name']) ?>">
      <div>
        <h3><?= e($item['name']) ?></h3>
        <?php if (!$restaurant && !empty($item['restaurant_name'])): ?>
          <p class="eyebrow"><?= e($item['restaurant_name']) ?></p>
        <?php endif; ?>
        <p><?= e(excerpt($item['description'], 120)) ?></p>
        <strong><?= e(number_format((float) $item['price'], 2)) ?> BDT</strong>
        <div class="button-row">
          <a class="button ghost" href="<?= e(url('admin/menu/form', ['restaurant_id' => $item['restaurant_id'], 'id' => $item['id']])) ?>">Edit</a>
          <form action="<?= e(url('admin/menu/delete')) ?>" method="post" data-confirm="Delete this menu item?">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="restaurant_id" value="<?= (int) $item['restaurant_id'] ?>">
            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
            <button class="button danger" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
  <?php if (!$items): ?>
    <section class="panel">
      <h2>No menu items yet</h2>
      <p class="muted">Open a restaurant from the admin restaurant list to add its first menu item.</p>
    </section>
  <?php endif; ?>
</section>
