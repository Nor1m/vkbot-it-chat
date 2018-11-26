<?php
/**
 * Created by PhpStorm.
 * User: ROMAN
 * Date: 18.11.2018
 * Time: 20:53
 */

namespace App\models;


class UserProposed
{
    public $user_id;
    public $proposed_id;

    public static function get($userId, $proposedId): ?self
    {
        return null;
    }

    public static function create($userId, $proposedId): bool
    {
        return false;
    }
}