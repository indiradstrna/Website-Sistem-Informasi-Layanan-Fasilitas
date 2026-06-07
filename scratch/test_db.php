<?php
require_once __DIR__ . '/../config.php';

$res = $conn->query("SELECT id, room_id, status, date_start, time_start, date_end, time_end FROM room_requests ORDER BY id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
