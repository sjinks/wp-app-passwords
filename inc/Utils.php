<?php
namespace WildWolf\ApplicationPasswords;

abstract class Utils
{
    public static function isGuest() : bool
    {
        global $current_user;
        return $current_user instanceof \WP_User && 0 === $current_user->ID;
    }

    public static function isRestRequest() : bool
    {
        return \defined('\\REST_REQUEST') && \REST_REQUEST;
    }

    public static function isApiRequest() : bool
    {
        return
               (\defined('\\XMLRPC_REQUEST') && \XMLRPC_REQUEST)
            || self::isRestRequest()
        ;
    }

    public static function isCLI()
    {
        return \defined('\\WP_CLI') && \WP_CLI;
    }
}
