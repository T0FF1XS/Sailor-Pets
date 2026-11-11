<?php

// Dito ang mga SQL ng users at roles table
// Tapos na ang findByEmail kasi gamit yan ng login
// Ikaw na bahala sa mga method na may paliwanag sa taas nila
// Lagi gamitin ang password_hash sa pag save ng password

class User
{
    public static function findByEmail($email)
    {
        return get_row(
            'SELECT u.user_id, u.first_name, u.last_name, u.password, r.role_name
             FROM users u JOIN roles r ON u.role_id = r.role_id
             WHERE u.email = ?',
            [$email]
        );
    }

    // Kunin lahat ng staff pati role nila
    public static function all()
    {
    }

    // Kunin ang isang user gamit ang id
    public static function find($id)
    {
    }

    // Kunin ang mga role na pwedeng ibigay pero wag isama ang customer
    public static function roles()
    {
    }

    // Magdagdag ng staff at i hash ang password
    public static function create($roleId, $first, $last, $email, $phone, $password)
    {
    }

    // I update ang staff at palitan lang ang password kung may bago
    public static function update($id, $roleId, $first, $last, $email, $phone, $password)
    {
    }

    // Burahin ang staff
    public static function delete($id)
    {
    }
}
