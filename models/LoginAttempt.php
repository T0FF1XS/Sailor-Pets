<?php

class LoginAttempt
{
    public static function tooMany($ip)
    {
        $row = get_row(
            'SELECT COUNT(*) AS total FROM login_attempts WHERE ip = ? AND attempted_at > NOW() - INTERVAL 15 MINUTE',
            [$ip]
        );
        return $row['total'] >= 5;
    }

    public static function add($ip)
    {
        run('DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 1 DAY');
        run('INSERT INTO login_attempts (ip) VALUES (?)', [$ip]);
    }

    public static function clear($ip)
    {
        run('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
    }
}
