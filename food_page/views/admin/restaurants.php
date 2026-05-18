<section class="section-heading">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Restaurants</h1>
  </div>
  <a class="button primary" href="<?= e(url('admin/restaurant/form')) ?>">Add restaurant</a>
</section>

<section class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Location</th>
        <th>Area</th>
        <th>Menu items</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($restaurants as $restaurant): ?>
        <tr>
          <td><?= e($restaurant['name']) ?></td>
          <td><?= e($restaurant['location']) ?></td>
          <td><?= e($restaurant['area']) ?></td>
          <td><?= (int) $restaurant['menu_count'] ?></td>
          <td class="table-actions">
            <a href="<?= e(url('browse/restaurant', ['id' => $restaurant['id']])) ?>">View</a>
            <a href="<?= e(url('admin/menu', ['restaurant_id' => $restaurant['id']])) ?>">Menu</a>
            <a href="<?= e(url('admin/restaurant/form', ['id' => $restaurant['id']])) ?>">Edit</a>
            <form action="<?= e(url('admin/restaurant/delete')) ?>" method="post" data-confirm="Delete this restaurant and its menu items?">
              <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $restaurant['id'] ?>">
              <button class="text-danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

