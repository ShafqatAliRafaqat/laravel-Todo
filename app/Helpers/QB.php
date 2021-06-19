<?php

namespace App\Helpers;

class QB
{

    public static function where($input, $param, $qb) {

        if (self::applyFilter($input, $param)) {
            $qb->where($param, $input[$param]);
        }

        return $qb;
    }
    public static function whereLike($input, $param, $qb)
    {

        if (self::applyFilter($input, $param)) {
            $qb = $qb->where($param, "LIKE", "%{$input[$param]}%");
        }

        return $qb;
    }


    private static function applyFilter($input, $param)
    {
        if (isset($input[$param])) {
            if ($input[$param] != "all") {

                if ($input[$param] == '0') {
                    return true;
                }

                if (!empty($input[$param])) {
                    return true;
                }
            }
        }
        return false;
    }
    
}