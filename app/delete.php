<?php

declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('DELETE FROM reservations WHERE id = ?');
$stmt->execute([$id]);

$_SESSION['flash'] = $stmt->rowCount() > 0
  ? ['type' => 'success', 'message' => 'Reservation #' . $id . ' deleted successfully.']
  : ['type' => 'warning', 'message' => 'Reservation not found.'];

header('Location: index.php');
exit;
