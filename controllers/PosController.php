<?php

class PosController
{
    public function index()
    {
        require_role(['Owner', 'Sales Staff']);

        render('pos/index', [
            'pageTitle' => 'POS',
            'products' => Product::forPos(),
        ]);
    }

    public function checkout()
    {
        $user = require_role(['Owner', 'Sales Staff']);

        $priceType = $_POST['price_type'];
        $payment = $_POST['payment_method'];
        $customer = trim($_POST['customer_name']);
        $cart = json_decode($_POST['cart'], true);

        if ($priceType == 'Wholesale') {
            $column = 'wholesale_price';
        } else {
            $priceType = 'Retail';
            $column = 'retail_price';
        }

        if ($payment != 'GCash') {
            $payment = 'Cash';
        }

        if ($customer == '') {
            $customer = 'Walk-in';
        }

        $error = '';
        $total = 0;
        $lines = [];

        if (!$cart) {
            $error = 'Cart is empty.';
        } else {
            foreach ($cart as $item) {
                $id = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);

                $product = Product::priceFor($id, $column);

                if (!$product || $qty < 1) {
                    $error = 'Invalid item in cart.';
                    break;
                }

                if ($product['stock'] < $qty) {
                    $error = 'Not enough stock for ' . $product['product_name'] . '.';
                    break;
                }

                $subtotal = round($qty * $product['price'], 2);
                $total = $total + $subtotal;
                $lines[] = ['id' => $id, 'qty' => $qty, 'price' => $product['price'], 'subtotal' => $subtotal];
            }
        }

        if ($error == '') {
            $saleId = Sale::create($user['user_id'], $customer, $priceType, $payment, $total, $lines);

            if ($saleId) {
                redirect('sales', ['id' => $saleId]);
            }

            $error = 'Checkout failed. Please try again.';
        }

        flash($error);
        redirect('pos');
    }
}
