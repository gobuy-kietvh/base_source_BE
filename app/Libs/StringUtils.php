<?php

namespace App\Libs;

class StringUtils
{
    /**
     * Generate a random string of the given length.
     *
     * @param int $length
     * @return string
     */
    public static function randomString(int $length): string
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / 62))), 1, $length);
    }
}
