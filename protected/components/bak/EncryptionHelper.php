<?php
/**
 * @param type $length
 * @return type 32 chars.
 */
function genRandomString($length = 32) {
    return(strtoupper(substr(str_shuffle(MD5(microtime())), 0, $length)));
}

/**
 *
 * @param type $length
 * @return type variable length.
 */
function genRandomStringLong($length = 10) {
    $randomstring = '';
    // Length of md5 hash.
    $len_per_loop = 32;
    if ($length > $len_per_loop) {
        $multiplier = floor($length / $len_per_loop);
        $remainder = $length % $len_per_loop;
        for ($i = 0; $i < $multiplier; $i++) {
            $randomstring .= substr(str_shuffle(md5(rand())), 0, $len_per_loop);
        }
        $randomstring .= substr(str_shuffle(md5(rand())), 0, $remainder);
    } else {
        $randomstring = substr(str_shuffle(md5(rand())), 0, $length);
    }
    return strtoupper($randomstring);
}

function encrypt($value) {
    return hash('sha256', $value);
}
