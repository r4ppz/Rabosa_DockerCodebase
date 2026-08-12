<?php

declare(strict_types=1);

const MS_API_URL = 'http://nginx_ms:81/api/employees.php';

function fetch_employees(): array
{
  $json = @file_get_contents(MS_API_URL);

  if ($json === false) {
    return [];
  }

  $data = json_decode($json, true);

  return isset($data['data']) && is_array($data['data']) ? $data['data'] : [];
}

function employee_name(array $employees, ?int $id): string
{
  foreach ($employees as $employee) {
    if ((int) $employee['id'] === (int) $id) {
      return $employee['name'];
    }
  }

  return '—';
}
