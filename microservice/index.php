<?php

declare(strict_types=1);

$apiBase = 'http://' . $_SERVER['HTTP_HOST'] . '/api/employees.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Microservice API</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f6f8fb;
    }

    code {
      background: #eef2f7;
      padding: 2px 6px;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
      <span class="navbar-brand fw-bold">Employee Microservice API</span>
      <span class="navbar-text text-white-50">Port 81</span>
    </div>
  </nav>

  <div class="container">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white fw-semibold">API Endpoints</div>
      <div class="card-body">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th>Method</th>
              <th>Endpoint</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge text-bg-success">GET</span></td>
              <td><code><?= htmlspecialchars($apiBase) ?></code></td>
              <td>List all employees</td>
            </tr>
            <tr>
              <td><span class="badge text-bg-success">GET</span></td>
              <td><code><?= htmlspecialchars($apiBase) ?>?id=1</code></td>
              <td>Get a single employee</td>
            </tr>
            <tr>
              <td><span class="badge text-bg-warning">POST</span></td>
              <td><code><?= htmlspecialchars($apiBase) ?></code></td>
              <td>Create an employee (JSON body)</td>
            </tr>
            <tr>
              <td><span class="badge text-bg-info">PUT</span></td>
              <td><code><?= htmlspecialchars($apiBase) ?>?id=1</code></td>
              <td>Update an employee (JSON body)</td>
            </tr>
            <tr>
              <td><span class="badge text-bg-danger">DELETE</span></td>
              <td><code><?= htmlspecialchars($apiBase) ?>?id=1</code></td>
              <td>Delete an employee</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Live Response</span>
        <button class="btn btn-sm btn-outline-light" onclick="loadEmployees()">Refresh</button>
      </div>
      <div class="card-body">
        <div id="output" class="text-muted">Loading...</div>
      </div>
    </div>
  </div>

  <script>
    async function loadEmployees() {
      const out = document.getElementById('output');
      out.textContent = 'Loading...';
      try {
        const res = await fetch('<?= htmlspecialchars($apiBase) ?>');
        const data = await res.json();
        out.textContent = JSON.stringify(data, null, 2);
        out.className = 'text-body';
        out.style.whiteSpace = 'pre-wrap';
      } catch (e) {
        out.textContent = 'Error fetching API: ' + e.message;
        out.className = 'text-danger';
      }
    }
    loadEmployees();
  </script>
</body>

</html>
