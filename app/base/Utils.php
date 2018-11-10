<?php
/**
 * Created by PhpStorm.
 * User: ROMAN
 * Date: 10.11.2018
 * Time: 13:55
 */

namespace App\base;


class Utils
{
    public static function getTypeOfId($id): string
    {
        if ($id < 0) {
            return 'group';
        } elseif ($id > 2000000000) {
            return 'chat';
        } else {
            return 'user';
        }
    }

    public static function clearId($id): int
    {
        switch (self::getTypeOfId($id)) {
            case 'group':
                return -$id;
            case 'chat':
                return $id - 2000000000;
            default:
                return $id;
        }
    }
}