<?php

class StockIn
{
    public static function receive($productId, $userId, $quantity, $date)
    {
        run(
            'INSERT INTO stock_in (product_id, user_id, quantity, date_received) VALUES (?, ?, ?, ?)',
            [$productId, $userId, $quantity, $date]
        );
    }

    public static function history()
    {
        return get_rows(
            'SELECT s.*, p.product_name, CONCAT(u.first_name, " ", u.last_name) AS received_by
             FROM stock_in s
             JOIN products p ON s.product_id = p.product_id
             JOIN users u ON s.user_id = u.user_id
             ORDER BY s.stock_in_id DESC LIMIT 30'
        );
    }
}
