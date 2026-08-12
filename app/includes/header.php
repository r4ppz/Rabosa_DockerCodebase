<?php

declare(strict_types=1);

function active_nav(string $page): string
{
  $current = basename($_SERVER['SCRIPT_NAME']);

  return $current === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Hotel System' ?> | Hotel System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f6f8fb;
    }

    .navbar-brand {
      font-weight: 700;
      letter-spacing: .5px;
    }

    .card {
      border: none;
    }

    .table thead th {
      background: #f1f5f9;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">🏨 Hotel System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link <?= active_nav('index.php') ?>" href="index.php">Reservations</a></li>
          <li class="nav-item"><a class="nav-link <?= active_nav('create.php') ?>" href="create.php">New Reservation</a></li>
        </ul>
        <span class="navbar-text text-white-50 small">
          Port 80 &middot; Employee data from Microservice API &middot; Port 81
        </span>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
