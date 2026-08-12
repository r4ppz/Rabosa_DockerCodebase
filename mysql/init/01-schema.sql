-- ==================================================================
-- MySQL bootstrap (runs automatically the first time the data volume
-- is empty). Creates the microservice database, tables and seed data.
-- ==================================================================

CREATE DATABASE IF NOT EXISTS hotel_db;
CREATE DATABASE IF NOT EXISTS employee_db;

-- Grant the application user access to BOTH databases
GRANT ALL PRIVILEGES ON hotel_db.*    TO 'default'@'%';
GRANT ALL PRIVILEGES ON employee_db.* TO 'default'@'%';
FLUSH PRIVILEGES;

-- ==================================================================
-- SYSTEM 1: hotel_db
-- ==================================================================
USE hotel_db;

CREATE TABLE IF NOT EXISTS reservations (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100)  NOT NULL,
    room_type  VARCHAR(50)   NOT NULL,
    check_in   DATE          NOT NULL,
    check_out  DATE          NOT NULL,
    employee_id INT          NOT NULL,
    status     ENUM('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reservation_employee (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample reservations (employee_id references employee_db.employees)
INSERT INTO reservations (guest_name, room_type, check_in, check_out, employee_id, status) VALUES
    ('Alice Johnson',     'Deluxe Room',    '2026-08-15', '2026-08-18', 1, 'confirmed'),
    ('Robert Tan',        'Suite',          '2026-08-20', '2026-08-22', 2, 'pending'),
    ('Sofia Dela Paz',    'Standard Room',  '2026-08-10', '2026-08-12', 3, 'checked_in');

-- ==================================================================
-- SYSTEM 2: employee_db
-- ==================================================================
USE employee_db;

CREATE TABLE IF NOT EXISTS employees (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    position   VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    hire_date  DATE         NOT NULL,
    status     ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed employees served by the microservice API
INSERT INTO employees (name, position, department, email, hire_date, status) VALUES
    ('Maria Santos',       'Front Desk Agent',       'Front Office',        'maria.santos@hotel.local',       '2022-03-01', 'active'),
    ('Juan Dela Cruz',     'Housekeeping Supervisor','Housekeeping',        'juan.delacruz@hotel.local',     '2021-07-15', 'active'),
    ('Ana Reyes',          'Reservation Clerk',      'Reservations',        'ana.reyes@hotel.local',         '2023-01-10', 'active'),
    ('Pedro Mendoza',      'Concierge',              'Front Office',        'pedro.mendoza@hotel.local',     '2020-11-20', 'active'),
    ('Elena Garcia',       'Restaurant Manager',     'Food & Beverage',     'elena.garcia@hotel.local',      '2019-05-06', 'active'),
    ('Jose Ramirez',       'Bell Captain',           'Front Office',        'jose.ramirez@hotel.local',      '2022-09-12', 'active'),
    ('Lucia Torres',       'Revenue Analyst',        'Sales & Marketing',   'lucia.torres@hotel.local',      '2021-02-25', 'active'),
    ('Carlo Villanueva',   'Maintenance Engineer',   'Engineering',         'carlo.villanueva@hotel.local',  '2020-08-03', 'inactive');
