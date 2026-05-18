<section class="section-heading">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Member profiles</h1>
  </div>
</section>

<section class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Joined</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $member): ?>
        <tr id="member-<?= (int) $member['id'] ?>">
          <td><?= e($member['name']) ?></td>
          <td><?= e($member['email']) ?></td>
          <td><?= e($member['created_at']) ?></td>
          <td>
            <button class="text-danger" type="button" data-delete-url="<?= e(url('api/members/delete', ['id' => $member['id']])) ?>" data-remove-target="#member-<?= (int) $member['id'] ?>">Delete member</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

