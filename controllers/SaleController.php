<?php

// Dito ang sales page para sa owner at sales staff
// Kita kada araw at mga latest na benta pati resibo ng isang benta
// Dito pumupunta ang POS pagkatapos ng checkout at may id na kasama sa link kaya pag may id ipakita ang resibo

class SaleController
{
    // Kunin ang kita kada araw at latest na sales at ang resibo kung may id tapos ipasa sa view
    public function index()
    {
        require_role(['Owner', 'Sales Staff']);

        render('sales/index', ['pageTitle' => 'Sales']);
    }
}
