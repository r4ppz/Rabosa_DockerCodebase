<?php

declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/api/client.php';

$pageTitle = 'Update Reservation';
$employees = fetch_employees();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM reservations WHERE id = ?');
$stmt->execute([$id]);
$reservation = $stmt->fetch();

if (!$reservation) {
  $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Reservation not found.'];
  header('Location: index.php');
  exit;
}

$errors = [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $guestName = trim($_POST['guest_name'] ?? '');
  $roomType  = trim($_POST['room_type'] ?? '');
  $checkIn   = trim($_POST['check_in'] ?? '');
  $checkOut  = trim($_POST['check_out'] ?? '');
  $employeeId = (int) ($_POST['employee_id'] ?? 0);
  $status    = trim($_POST['status'] ?? 'pending');

  if ($guestName === '') {
    $errors[] = 'Guest name is required.';
  }
  if (!in_array($roomType, ROOM_TYPES, true)) {
    $errors[] = 'Please choose a valid room type.';
  }
  if (!strtotime($checkIn) || !strtotime($checkOut)) {
    $errors[] = 'Valid check-in and check-out dates are required.';
  } elseif (strtotime($checkOut) <= strtotime($checkIn)) {
    $errors[] = 'Check-out must be after check-in.';
  }
  if ($employeeId <= 0) {
    $errors[] = 'Please select an employee from the dropdown.';
  }

  if ($errors) {
    $_SESSION['old'] = $_POST;
    $_SESSION['flash'] = ['type' => 'danger', 'message' => implode(' ', $errors)];
    header('Location: update.php?id=' . $id);
    exit;
  }

  $stmt = db()->prepare(
    'UPDATE reservations
            SET guest_name = ?, room_type = ?, check_in = ?, check_out = ?, employee_id = ?, status = ?
          WHERE id = ?'
  );
  $stmt->execute([$guestName, $roomType, $checkIn, $checkOut, $employeeId, $status, $id]);

  $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reservation #' . $id . ' updated successfully.'];
  header('Location: index.php');
  exit;
}

$values = array_merge($reservation, $old);
$currentEmployeeId = (int) $values['employee_id'];
?>
<?php require __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0 fw-semibold">Update Reservation #<?= (int) $reservation['id'] ?></h5>
      </div>
      <div class="card-body">
        <form method="post" action="update.php?id=<?= (int) $id ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="guest_name" class="form-label">Guest Name</label>
              <input type="text" class="form-control" id="guest_name" name="guest_name"
                value="<?= htmlspecialchars($values['guest_name']) ?>" required>
            </div>
            <div class="col-md-6">
              <label for="room_type" class="form-label">Room Type</label>
              <select class="form-select" id="room_type" name="room_type" required>
                <?php foreach (ROOM_TYPES as $type): ?>
                  <option value="<?= htmlspecialchars($type) ?>" <?= ($values['room_type'] === $type) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="check_in" class="form-label">Check-In Date</label>
              <input type="date" class="form-control" id="check_in" name="check_in"
                value="<?= htmlspecialchars($values['check_in']) ?>" required>
            </div>
            <div class="col-md-6">
              <label for="check_out" class="form-label">Check-Out Date</label>
              <input type="date" class="form-control" id="check_out" name="check_out"
                value="<?= htmlspecialchars($values['check_out']) ?>" required>
            </div>
            <div class="col-md-6">
              <label for="employee_id" class="form-label">
                Handled By
              </label>
              <select class="form-select" id="employee_id" name="employee_id" required>
                <option value="">-- Select employee --</option>
                <?php foreach ($employees as $employee): ?>
                  <option value="<?= (int) $employee['id'] ?>" <?= ((int) $employee['id'] === $currentEmployeeId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($employee['name']) ?> &middot; <?= htmlspecialchars($employee['position']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (empty($employees)): ?>
                <div class="form-text text-danger">
                  Could not load employees from the microservice. Is the container on port 81 running?
                </div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <?php foreach (RESERVATION_STATUS as $status): ?>
                  <option value="<?= $status ?>" <?= ($values['status'] === $status) ? 'selected' : '' ?>>
                    <?= ucfirst($status) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-dark">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
