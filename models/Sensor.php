<?php

// Dito ang mga SQL ng sensor logs at storage rooms table
// Tapos na ang record kasi yan ang nagse save ng reading galing ESP32
// Isulat mo ang rooms at logs

class Sensor
{
    public static function record($roomId, $temperature, $humidity)
    {
        run('INSERT INTO sensor_logs (room_id, temperature, humidity) VALUES (?, ?, ?)', [$roomId, $temperature, $humidity]);
    }

    // Kunin ang bawat room pati ang pinakabago niyang reading
    public static function rooms()
    {
    }

    // Kunin ang latest 50 na readings
    public static function logs()
    {
    }
}
