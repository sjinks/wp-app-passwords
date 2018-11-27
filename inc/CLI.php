<?php
namespace WildWolf\ApplicationPasswords;

class CLI
{
    public static function instance()
    {
        static $self = null;

        if (!$self) {
            $self = new self();
        }

        return $self;
    }

    private function __construct()
    {
        \load_plugin_textdomain('wwapppass-cli', /** @scrutinizer ignore-type */ false, \plugin_basename(\dirname(__DIR__)) . '/lang/');

        \WP_CLI::add_command('app-pass list', [$this, 'listPasswords'], [
            'shortdesc' => \__('List user\'s application passwords', 'wwapppass-cli'),
            'synopsis'  => [
                [
                    'type'        => 'positional',
                    'name'        => 'id',
                    'description' => \__('User ID, user email, or user login.', 'wwapppass-cli'),
                    'optional'    => false,
                    'multiple'    => false,
                ],
                [
                    'type'        => 'assoc',
                    'name'        => 'format',
                    'description' => \__('Render output in a particular format.', 'wwapppass-cli'),
                    'optional'    => true,
                    'default'     => 'table',
                    'options'     => ['table', 'json', 'csv', 'yaml', 'count'],
                ]
            ]
        ]);

        \WP_CLI::add_command('app-pass create', [$this, 'create'], [
            'shortdesc' => \__('Create a new application password', 'wwapppass-cli'),
            'synopsis'  => [
                [
                    'type'        => 'positional',
                    'name'        => 'id',
                    'description' => \__('User ID, user email, or user login.', 'wwapppass-cli'),
                    'optional'    => false,
                    'multiple'    => false,
                ],
                [
                    'type'        => 'assoc',
                    'name'        => 'name',
                    'description' => \__('Application password name', 'wwapppass-cli'),
                    'optional'    => true,
                    'default'     => \sprintf(\__('Automatically generated password (%s)', 'wwapppass-cli'), \date('c')),
                ]
            ]
        ]);

        \WP_CLI::add_command('app-pass revoke', [$this, 'revoke'], [
            'shortdesc' => \__('Revoke a password', 'wwapppass-cli'),
            'synopsis'  => [
                [
                    'type'        => 'positional',
                    'name'        => 'id',
                    'description' => \__('User ID, user email, or user login.', 'wwapppass-cli'),
                    'optional'    => false,
                    'multiple'    => false,
                ],
                [
                    'type'        => 'positional',
                    'name'        => 'slug',
                    'description' => \__('Application password slug.', 'wwapppass-cli'),
                    'optional'    => false,
                    'multiple'    => false,
                ]
            ]
        ]);

        \WP_CLI::add_command('app-pass revoke-all', [$this, 'revokeAll'], [
            'shortdesc' => \__('Revoke all passwords', 'wwapppass-cli'),
            'synopsis'  => [
                [
                    'type'        => 'positional',
                    'name'        => 'id',
                    'description' => \__('User ID, user email, or user login.', 'wwapppass-cli'),
                    'optional'    => false,
                    'multiple'    => false,
                ]
            ]
        ]);
    }

    public function listPasswords($args, $assoc)
    {
        $user = self::getUser($args[0]);
        if (!$user) {
            \WP_CLI::error(\sprintf(\__('Invalid user ID, email or login: "%s"', 'wwapppass-cli'), $user));
        }

        $hashed = AppPasswords::get($user->ID);
        foreach ($hashed as &$item) {
            $item['slug']      = AppPasswords::slug($item);
            $item['created']   = \date('Y-m-d H:i:s', $item['created']);
            $item['last_used'] = $item['last_used'] ? \date('Y-m-d H:i:s', $item['last_used']) : '0000-00-00 00:00:00';
        }

        unset($item);

        \WP_CLI\Utils\format_items($assoc['format'], $hashed, ['slug', 'name', 'created', 'last_used', 'last_ip']);
    }

    public function create($args, $assoc)
    {
        $user = self::getUser($args[0]);
        if (!$user) {
            \WP_CLI::error(\sprintf(\__('Invalid user ID, email or login: "%s"', 'wwapppass-cli'), $user));
        }

        $name = $assoc['name'];
        list($pass) = AppPasswords::create($user->ID, $name);
        \WP_CLI::success($pass);
    }

    public function revoke($args, $assoc)
    {
        $user = self::getUser($args[0]);
        if (!$user) {
            \WP_CLI::error(\sprintf(\__('Invalid user ID, email or login: "%s"', 'wwapppass-cli'), $user));
        }

        $slug = $args[1];

        if (AppPasswords::delete($user->ID, $slug)) {
            \WP_CLI::success(\__('Password has been revoked.', 'wwapppass-cli'));
        }
        else {
            \WP_CLI::error(\__('Password not found.', 'wwapppass-cli'));
        }
    }

    public function revokeAll($args, $assoc)
    {
        $user = self::getUser($args[0]);
        if (!$user) {
            \WP_CLI::error(\sprintf(\__('Invalid user ID, email or login: "%s"', 'wwapppass-cli'), $user));
        }

        AppPasswords::deleteAll($user->ID);
        \WP_CLI::success(\__('All password have been revoked.', 'wwapppass-cli'));
    }

    private static function getUser($u)
    {
        if (\is_numeric($u)) {
            $user = \get_user_by('id', $u);
        }
        elseif (\is_email($u)) {
            $user = \get_user_by('email', $u);
        }
        else {
            $user = null;
        }

        if (!$user) {
            $user = \get_user_by('login', $u);
        }

        return $user;
    }
}
