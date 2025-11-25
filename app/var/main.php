<?php

interface IDatabase
{
    static function verify_promo_code($code): int;
    static function add_promo_code($code, $expir, $discount): void;
    static function remove_promo_code($code): void;
}

class Database implements IDatabase
{
    static function verify_promo_code($code): bool
    {
        return false;
    }
}

function verify_promo_code() {}
