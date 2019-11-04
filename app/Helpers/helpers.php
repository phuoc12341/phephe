<?php

use Illuminate\Support\Carbon;

/**
 * Filter special characters
 *
 * @codeCoverageIgnore
 * @param  array $input
 * @param  array $keys
 * @return array
 */
if (!function_exists('filter')) {
    function filter($input, $keys)
    {
        if (!empty($keys)) {
            foreach ($keys as $key) {
                if (isset($input[$key]) && !is_null($input[$key])) {
                    $input[$key] = str_replace(
                        ['\\', '%', '\'', '\n', '\r', '\\0', '\\x1a', '_'],
                        ['\\\\', '\%', '\\\'', '\\\n', '\\\r', '\\\0', '\\\x1a', '\_'],
                        $input[$key]
                    );
                }
            }
        }
        return $input;
    }
}

/**
 * Filter special characters
 *
 * @codeCoverageIgnore
 * @param  array $input
 * @param  array $keys
 * @return array
 */
if (!function_exists('tokenExpired')) {
    function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds(config('auth.passwords.users.expire'))->isPast();
    }
}


