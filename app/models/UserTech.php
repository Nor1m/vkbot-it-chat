<?php

namespace App\models;


class UserTech
{
    public $user_id;
    public $tech_id;

    public static function get($userId, $techId): ?self
    {
        return null;
    }

    public static function create($userId, $techId): bool
    {
        return false;
    }
}