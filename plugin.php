<?php
/*
 Plugin Name: WW Application Passwords
 Description: Application Passwords
 Author: Volodymyr Kolesnykov
 Version: 1.0.1
 Author URI: https://wildwolf.name/
*/
defined('ABSPATH') || die();

if (defined('VENDOR_PATH')) {
	require VENDOR_PATH . '/vendor/autoload.php';
}
elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require __DIR__ . '/vendor/autoload.php';
}
elseif (file_exists(ABSPATH . 'vendor/autoload.php')) {
	require ABSPATH . 'vendor/autoload.php';
}

WildWolf\WordPress\Autoloader::register();
WildWolf\ApplicationPasswords\Plugin::instance();
