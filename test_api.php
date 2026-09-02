<?php
require 'config.php';
$driverName = 'Test Driver';
$sql = "SELECT id, user_id, vehicle_id, applicant_name, applicant_unit, destination, passenger_name, departure, cost_bearer, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, status, note, driver_name, created_at FROM vehicle_requests WHERE driver_name LIKE '%$driverName%' AND status NOT IN ('canceled', 'rejected') ORDER BY created_at DESC";
$res = $conn->query($sql);
if (!$res) echo "Error: " . $conn->error;
else echo "OK, rows: " . $res->num_rows;
