<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

function respond(array $payload, int $status = 200): void
{
  http_response_code($status);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function request_data(): array
{
  $raw = file_get_contents('php://input');
  $json = json_decode((string) $raw, true);

  return is_array($json) ? $json : $_POST;
}

function validate_employee(array $d): array
{
  $errors = [];

  foreach (['name', 'position', 'department', 'email', 'hire_date'] as $field) {
    if (empty($d[$field])) {
      $errors[] = "Missing required field: {$field}";
    }
  }

  if (!empty($d['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
  }

  if (isset($d['status']) && !in_array($d['status'], ['active', 'inactive'], true)) {
    $errors[] = 'Status must be active or inactive';
  }

  return $errors;
}

try {
  $pdo = db();
  $method = $_SERVER['REQUEST_METHOD'];
  $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

  switch ($method) {
    case 'GET':
      if ($id !== null) {
        $stmt = $pdo->prepare('SELECT * FROM employees WHERE id = ?');
        $stmt->execute([$id]);
        $employee = $stmt->fetch();

        if (!$employee) {
          respond(['error' => 'Employee not found'], 404);
        }

        respond(['data' => $employee]);
      }

      $stmt = $pdo->query('SELECT * FROM employees ORDER BY name ASC');
      respond(['data' => $stmt->fetchAll()]);

    case 'POST':
      $data = request_data();
      $errors = validate_employee($data);

      if ($errors) {
        respond(['error' => 'Validation failed', 'details' => $errors], 422);
      }

      $stmt = $pdo->prepare(
        'INSERT INTO employees (name, position, department, email, hire_date, status)
                 VALUES (?, ?, ?, ?, ?, ?)'
      );
      $stmt->execute([
        $data['name'],
        $data['position'],
        $data['department'],
        $data['email'],
        $data['hire_date'],
        $data['status'] ?? 'active',
      ]);

      respond(['message' => 'Employee created successfully', 'id' => (int) $pdo->lastInsertId()], 201);

    case 'PUT':
      if ($id === null) {
        respond(['error' => 'Employee id is required for update'], 400);
      }

      $stmt = $pdo->prepare('SELECT * FROM employees WHERE id = ?');
      $stmt->execute([$id]);
      if (!$stmt->fetch()) {
        respond(['error' => 'Employee not found'], 404);
      }

      $data = request_data();
      $errors = validate_employee($data);

      if ($errors) {
        respond(['error' => 'Validation failed', 'details' => $errors], 422);
      }

      $stmt = $pdo->prepare(
        'UPDATE employees
                    SET name = ?, position = ?, department = ?, email = ?, hire_date = ?, status = ?
                  WHERE id = ?'
      );
      $stmt->execute([
        $data['name'],
        $data['position'],
        $data['department'],
        $data['email'],
        $data['hire_date'],
        $data['status'] ?? 'active',
        $id,
      ]);

      respond(['message' => 'Employee updated successfully', 'id' => $id]);

    case 'DELETE':
      if ($id === null) {
        respond(['error' => 'Employee id is required for delete'], 400);
      }

      $stmt = $pdo->prepare('DELETE FROM employees WHERE id = ?');
      $stmt->execute([$id]);

      if ($stmt->rowCount() === 0) {
        respond(['error' => 'Employee not found'], 404);
      }

      respond(['message' => 'Employee deleted successfully', 'id' => $id]);

    default:
      respond(['error' => 'Method not allowed'], 405);
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
  respond(['error' => 'Database error occurred'], 500);
}
