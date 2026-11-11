<?php

class ProductController
{
    public function index()
    {
        require_role(['Owner', 'Inventory Staff']);

        $edit = [];
        if (isset($_GET['edit'])) {
            $edit = Product::find((int) $_GET['edit']);
        }

        render('products/index', [
            'pageTitle' => 'Products',
            'edit' => $edit,
            'categories' => Category::allByName(),
            'products' => Product::all(),
            'activeProducts' => Product::forStockIn(),
            'history' => StockIn::history(),
        ]);
    }

    public function save()
    {
        require_role(['Owner', 'Inventory Staff']);

        $id = (int) $_POST['product_id'];
        $values = [
            $_POST['category_id'], $_POST['product_name'], $_POST['pet_type'],
            $_POST['retail_price'], $_POST['wholesale_price'],
            $_POST['low_stock_level'], $_POST['tag'], trim($_POST['sku']),
        ];

        try {
            if ($id > 0) {
                Product::update($id, $values);
                flash('Product updated.');
            } else {
                Product::create($values);
                flash('Product added. Use Stock In to add stock.');
            }
        } catch (mysqli_sql_exception $e) {
            flash('Could not save. The code may already be used, or the wholesale price is higher than the retail price.');
        }

        redirect('products');
    }

    public function toggle()
    {
        require_role(['Owner', 'Inventory Staff']);
        Product::toggle($_POST['toggle']);
        redirect('products');
    }

    public function receive()
    {
        $user = require_role(['Owner', 'Inventory Staff']);

        $quantity = (int) $_POST['quantity'];
        $date = $_POST['date_received'] ?: date('Y-m-d');

        if ($quantity > 0) {
            StockIn::receive($_POST['product_id'], $user['user_id'], $quantity, $date);
            flash('Stock received.');
        } else {
            flash('Quantity must be more than zero.');
        }

        redirect('products', [], '#stock-in');
    }
}
