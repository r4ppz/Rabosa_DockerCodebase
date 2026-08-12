<?php

declare(strict_types=1);

define('DB_HOST', 'mysql');
define('DB_NAME', 'employee_db');
define('DB_USER', 'default');
define('DB_PASS', 'default');

function db(): PDO
{
  static $pdo = null;

  if ($pdo === null) {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }

  return $pdo;
}
