<?php

class Inquiry
{
    public static function create($name, $phone, $email, $address, $type, $message, $column, $items)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            run(
                'INSERT INTO inquiries (customer_name, customer_phone, customer_email, customer_address, inquiry_type, message)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$name, $phone, $email ?: null, $address ?: null, $type, $message]
            );
            $inquiryId = $conn->insert_id;

            foreach ($items as $productId => $quantity) {
                $product = Product::priceFor($productId, $column);
                if ($product) {
                    run(
                        'INSERT INTO inquiry_items (inquiry_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)',
                        [$inquiryId, $productId, $quantity, $product['price']]
                    );
                }
            }

            $conn->commit();
            return $inquiryId;
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return 0;
        }
    }

    public static function findForTrack($id, $phone)
    {
        return get_row(
            'SELECT inquiry_id, inquiry_type, status, date_sent FROM inquiries WHERE inquiry_id = ? AND customer_phone = ?',
            [$id, $phone]
        );
    }

    public static function decide($id, $status, $userId)
    {
        run(
            "UPDATE inquiries SET status = ?, handled_by = ? WHERE inquiry_id = ? AND status = 'Pending'",
            [$status, $userId, $id]
        );
    }

    public static function allWithItems()
    {
        $inquiries = get_rows(
            'SELECT i.*, CONCAT(h.first_name, " ", h.last_name) AS handled_by_name
             FROM inquiries i LEFT JOIN users h ON i.handled_by = h.user_id
             ORDER BY FIELD(i.status, "Pending", "Approved", "Rejected"), i.date_sent DESC'
        );

        foreach ($inquiries as $n => $inq) {
            $inquiries[$n]['items'] = get_rows(
                'SELECT p.product_name, p.stock, i.quantity, i.unit_price
                 FROM inquiry_items i JOIN products p ON i.product_id = p.product_id
                 WHERE i.inquiry_id = ?',
                [$inq['inquiry_id']]
            );
        }

        return $inquiries;
    }

    public static function pendingCount()
    {
        $row = get_row("SELECT COUNT(*) AS total FROM inquiries WHERE status = 'Pending'");
        return (int) $row['total'];
    }

    public static function pendingList()
    {
        return get_rows(
            "SELECT inquiry_id, customer_name, customer_phone, inquiry_type, date_sent
             FROM inquiries WHERE status = 'Pending' ORDER BY date_sent DESC LIMIT 5"
        );
    }
}
