<?php

class Product
{
    public static function forStore()
    {
        return get_rows(
            'SELECT p.product_id, p.product_name, p.retail_price, p.wholesale_price, p.stock, p.tag, p.sku, p.pet_type, c.category_name
             FROM products p JOIN categories c ON p.category_id = c.category_id
             WHERE p.is_active = 1 ORDER BY p.product_id'
        );
    }

    public static function forPos()
    {
        return get_rows(
            'SELECT product_id, product_name, retail_price, wholesale_price, stock
             FROM products WHERE is_active = 1 AND stock > 0 ORDER BY product_name'
        );
    }

    public static function forStockIn()
    {
        return get_rows('SELECT product_id, product_name, stock FROM products WHERE is_active = 1 ORDER BY product_name');
    }

    public static function all()
    {
        return get_rows(
            'SELECT p.*, c.category_name FROM products p
             JOIN categories c ON p.category_id = c.category_id
             ORDER BY p.product_id DESC'
        );
    }

    public static function find($id)
    {
        return get_row('SELECT * FROM products WHERE product_id = ?', [$id]) ?: [];
    }

    public static function priceFor($id, $column)
    {
        return get_row("SELECT product_name, stock, $column AS price FROM products WHERE product_id = ? AND is_active = 1", [$id]);
    }

    public static function toggle($id)
    {
        run('UPDATE products SET is_active = 1 - is_active WHERE product_id = ?', [$id]);
    }

    public static function create($values)
    {
        run(
            'INSERT INTO products (category_id, product_name, pet_type, retail_price,
             wholesale_price, low_stock_level, tag, sku) VALUES (?,?,?,?,?,?,?,?)',
            $values
        );
    }

    public static function update($id, $values)
    {
        $values[] = $id;
        run(
            'UPDATE products SET category_id=?, product_name=?, pet_type=?, retail_price=?,
             wholesale_price=?, low_stock_level=?, tag=?, sku=? WHERE product_id=?',
            $values
        );
    }
}
