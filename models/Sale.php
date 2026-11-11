<?php

// Dito ang mga SQL ng sales at sale items table
// Tapos na ang create kasi gamit yan ng POS at ang today kasi gamit ng dashboard
// Ikaw na bahala sa mga method na may paliwanag sa taas nila

class Sale
{
    public static function create($cashierId, $customer, $priceType, $payment, $total, $lines)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            run(
                'INSERT INTO sales (cashier_id, customer_name, price_type, payment_method, total_amount) VALUES (?, ?, ?, ?, ?)',
                [$cashierId, $customer, $priceType, $payment, $total]
            );
            $saleId = $conn->insert_id;

            foreach ($lines as $line) {
                run(
                    'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)',
                    [$saleId, $line['id'], $line['qty'], $line['price'], $line['subtotal']]
                );
            }

            $conn->commit();
            return $saleId;
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return 0;
        }
    }

    public static function today()
    {
        return get_row('SELECT * FROM daily_sales WHERE sale_day = CURDATE()');
    }

    // Kunin ang isang sale pati pangalan ng cashier
    public static function find($id)
    {
    }

    // Kunin ang mga item ng isang sale
    public static function items($id)
    {
    }

    // Kunin ang kita kada araw sa last 14 days
    public static function daily()
    {
    }

    // Kunin ang latest 30 na sales
    public static function recent()
    {
    }
}
