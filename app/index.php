<?php

declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/api/client.php';

$employees = fetch_employees();
$rows = db()->query('SELECT * FROM reservations ORDER BY created_at DESC, id DESC')->fetchAll();
$pageTitle = 'Reservations';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<?php require __DIR__ . '/includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0 fw-bold">Reservation List</h4>
  <a href="create.php" class="btn btn-primary">+ New Reservation</a>
</div>

<?php if (empty($employees)): ?>
  <div class="alert alert-warning">
    <strong>Microservice unavailable.</strong> Could not reach the Employee API at
    <code>http://nginx_ms:81/api/employees.php</code>. Employee names are hidden until the microservice container is up.
  </div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Guest Name</th>
          <th>Room Type</th>
          <th>Check-In</th>
          <th>Check-Out</th>
          <th>Handled By (Microservice)</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">No reservations yet.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td>#<?= (int) $row['id'] ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['guest_name']) ?></td>
              <td><?= htmlspecialchars($row['room_type']) ?></td>
              <td><?= htmlspecialchars($row['check_in']) ?></td>
              <td><?= htmlspecialchars($row['check_out']) ?></td>
              <td>
                <span class="badge text-bg-light border">
                  <?= htmlspecialchars(employee_name($employees, (int) $row['employee_id'])) ?>
                </span>
              </td>
              <td>
                <?php
                $badge = match ($row['status']) {
                  'confirmed'   => 'text-bg-success',
                  'pending'     => 'text-bg-warning',
                  'checked_in'  => 'text-bg-info',
                  'checked_out' => 'text-bg-secondary',
                  default       => 'text-bg-danger',
                };
                ?>
                <span class="badge <?= $badge ?>"><?= htmlspecialchars($row['status']) ?></span>
              </td>
              <td class="text-end">
                <a href="update.php?id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete reservation #<?= (int) $row['id'] ?>?');">
                  <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
