<?php

// Dito ang mga account ng staff tulad ng owner sales staff at inventory staff
// Owner lang ang pwedeng pumasok dito
// Gawin mo ang pag add pag edit pag reset ng password at pag delete ng staff
// Dapat walo pataas ang letra ng password at tama at hindi pa gamit ang email
// Bawal burahin o palitan ng role ang sarili mong account para hindi ma lock out ang owner

class StaffController
{
    // Ipakita ang form at listahan ng staff
    public function index()
    {
        require_role(['Owner']);

        render('staff/index', ['pageTitle' => 'Staff']);
    }

    // I save ang bago o pinalitang staff at i hash ang password
    public function save()
    {
    }

    // Burahin ang staff pero hindi ang sarili mong account
    public function delete()
    {
    }
}
