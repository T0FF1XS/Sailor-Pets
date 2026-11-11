<?php

// Dito ang mga SQL ng categories table
// Tapos na ang allByName at allById kasi gamit sila ng store at ng product form

class Category
{
    public static function allByName()
    {
        return get_rows('SELECT * FROM categories ORDER BY category_name');
    }

    public static function allById()
    {
        return get_rows('SELECT category_name FROM categories ORDER BY category_id');
    }

    // Kunin lahat ng category pati kung ilang products meron sila
    public static function allWithCount()
    {
    }

    // Kunin ang isang category gamit ang id
    public static function find($id)
    {
    }

    // Magdagdag ng category
    public static function create($name)
    {
    }

    // Palitan ang pangalan ng category
    public static function rename($id, $name)
    {
    }

    // Burahin ang category
    public static function delete($id)
    {
    }
}
