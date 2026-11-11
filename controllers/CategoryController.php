<?php

// Dito ang mga category ng products tulad ng dog food at toys
// Para lang to sa owner at inventory staff
// pag add at pag rename ng category tapos lagyan ng listahan na kita kung ilang products ang nasa bawat isa
// Pwede lang burahin ang category kung wala pa siyang products kasi ang database na mismo ang aayaw
// look at the ProductController for reference
class CategoryController
{
    // Ipakita ang form at listahan ng categories
    public function index()
    {
        require_role(['Owner', 'Inventory Staff']);

        render('categories/index', ['pageTitle' => 'Categories']);
    }

    // I save ang bago o pinalitang category
    public function save()
    {
    }

    // Burahin ang category kung wala pa siyang products
    public function delete()
    {
    }
}
