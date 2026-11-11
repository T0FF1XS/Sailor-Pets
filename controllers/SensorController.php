<?php

// Dito ang sensors page para sa lahat ng staff kita dito ang init at halumigmig ng stockroom
// Tapos na ang record kasi yan ang tinatawag ng ESP32
// Ang index na lang ang gawin mo

class SensorController
{
    // Kunin ang rooms at logs sa Sensor model tapos ipasa sa view
    public function index()
    {
        require_role(['Owner', 'Sales Staff', 'Inventory Staff']);

        render('sensors/index', ['pageTitle' => 'Sensors']);
    }

    public function record()
    {
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
            Sensor::record($roomId, $temp, $hum);
            echo json_encode(['ok' => true]);
        } catch (mysqli_sql_exception $e) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Reading rejected']);
        }
    }
}
