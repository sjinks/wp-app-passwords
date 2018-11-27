<?php
namespace WildWolf\ApplicationPasswords;

abstract class HttpAuthorization
{
    public static $keys = [
        'HTTP_AUTHORIZATION',
        'REDIRECT_HTTP_AUTHORIZATION',
        'REMOTE_USER',
        'REDIRECT_REMOTE_USER',
    ];

    public static function fixup()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW'])) {
            return;
        }

        foreach (self::$keys as $idx) {
            if (isset($_SERVER[$idx])) {
                return self::parseAuthorizationHeader($_SERVER[$idx]);
            }
        }

        return self::parseAuthorizationHeader(self::getAuthFromApache());
    }

    private static function getAuthFromApache()
    {
        $headers = null;
        if (\function_exists('apache_get_headers')) {
            $headers = \array_change_key_case((array)\apache_request_headers(), \CASE_UPPER);
        }

        return $headers['AUTHORIZATION'] ?? null;
    }

    private static function parseAuthorizationHeader(string $auth = null)
    {
        $m = [];
        if (preg_match('/^Basic\\s+([A-Za-z0-9\\/+]*(?:={0,2}))/', $auth, $m)) {
            $auth = explode(':', (string)base64_decode($m[1]), 2);
            if (count($auth) === 2) {
                $_SERVER['PHP_AUTH_USER'] = $m[1];
                $_SERVER['PHP_AUTH_PW']   = $m[2];
            }
        }
    }
}
