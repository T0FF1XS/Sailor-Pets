<?php

class Alert
{
    public static function unread()
    {
        return get_rows('SELECT * FROM low_stock_alerts WHERE is_read = 0 ORDER BY created_at DESC');
    }

    public static function markRead($id)
    {
        run('UPDATE low_stock_alerts SET is_read = 1 WHERE alert_id = ?', [$id]);
    }

    public static function lowStock()
    {
        return get_rows('SELECT * FROM low_stock_products ORDER BY stock');
    }
}
