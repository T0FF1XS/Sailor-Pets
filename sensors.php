<?php

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    if (($_POST['key'] ?? '') != SENSOR_KEY) {
        http_response_code(401);
        exit(json_encode(['ok' => false, 'error' => 'Wrong key']));
    }

    $roomId = (int) ($_POST['room_id'] ?? 0);
    $temp = $_POST['temperature'] ?? '';
    $hum = $_POST['humidity'] ?? '';

    if ($roomId < 1 || !is_numeric($temp) || !is_numeric($hum)) {
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Missing or wrong fields']));
    }

    try {
        run('INSERT INTO sensor_logs (room_id, temperature, humidity) VALUES (?, ?, ?)', [$roomId, $temp, $hum]);
        echo json_encode(['ok' => true]);
    } catch (mysqli_sql_exception $e) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Reading rejected']);
    }
    exit;
}

require_role(['Owner', 'Sales Staff', 'Inventory Staff']);

$rooms = get_rows(
    'SELECT r.room_name, r.location, l.temperature, l.humidity, l.logged_at
     FROM storage_rooms r
     LEFT JOIN sensor_logs l ON l.log_id = (
         SELECT MAX(log_id) FROM sensor_logs WHERE room_id = r.room_id
     )'
);
$logs = get_rows(
    'SELECT l.*, r.room_name FROM sensor_logs l
     JOIN storage_rooms r ON l.room_id = r.room_id
     ORDER BY l.log_id DESC LIMIT 50'
);

$staffPage = true;
$pageTitle = 'Sensors';
include 'header.php';
?>

<meta http-equiv="refresh" content="30">
<h1 class="h3 mb-3">Stockroom Monitoring</h1>

<div class="card shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Room</th><th>Location</th><th>Temp (C)</th><th>Humidity (%)</th><th>Last reading</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php foreach ($rooms as $r): ?>
                <?php
                $hot = $r['temperature'] !== null && $r['temperature'] > TEMP_MAX;
                $wet = $r['humidity'] !== null && $r['humidity'] > HUMIDITY_MAX;
                ?>
                <tr class="<?= ($hot || $wet) ? 'table-danger' : '' ?>">
                    <td><?= e($r['room_name']) ?></td>
                    <td><?= e($r['location']) ?></td>
                    <td><?= e($r['temperature'] ?? '-') ?></td>
                    <td><?= e($r['humidity'] ?? '-') ?></td>
                    <td><?= e($r['logged_at'] ?? 'No data') ?></td>
                    <td>
                        <?php if ($r['temperature'] === null): ?>
                            <span class="badge bg-secondary">No data</span>
                        <?php elseif ($hot || $wet): ?>
                            <span class="badge bg-danger">WARNING<?= $hot ? ' - too hot' : '' ?><?= $wet ? ' - too humid' : '' ?></span>
                        <?php else: ?>
                            <span class="badge bg-success">OK</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">Recent Readings</div>
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Time</th><th>Room</th><th>Temp (C)</th><th>Humidity (%)</th></tr></thead>
            <tbody>
            <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= e($l['logged_at']) ?></td>
                    <td><?= e($l['room_name']) ?></td>
                    <td><?= e($l['temperature']) ?></td>
                    <td><?= e($l['humidity']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
