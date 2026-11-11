<?php

// Dito ang mga storage room na may sensor
// Owner lang ang pwedeng pumasok dito
// Gawin mo ang pag add edit at delete ng room pati listahan na may bilang ng readings ng bawat isa
// Importante na kitang kita ang room id kasi yan ang ipapadala ng ESP32
// Bawal burahin ang room na may readings na

class RoomController
{
    // Ipakita ang form at listahan ng rooms
    public function index()
    {
        require_role(['Owner']);

        render('rooms/index', ['pageTitle' => 'Storage Rooms']);
    }

    // I save ang bago o pinalitang room
    public function save()
    {
    }

    // Burahin ang room kung wala pa siyang readings
    public function delete()
    {
    }
}
