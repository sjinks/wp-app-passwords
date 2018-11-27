<?php
namespace WildWolf\ApplicationPasswords;

final class Admin
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
        \add_action('init',       [$this, 'init']);
    }

    public function init()
    {
        \load_plugin_textdomain('wwapppass', /** @scrutinizer ignore-type */ false, \plugin_basename(\dirname(__DIR__)) . '/lang/');
        \add_action('admin_menu', [$this, 'admin_menu']);
        \add_action('admin_init', [$this, 'admin_init']);
    }

    public function admin_menu()
    {
        \add_submenu_page(null, \__('Authorize Application', 'wwapppass'), null, 'read', 'wwapppass-auth', [$this, 'auth_app']);
    }

    public function admin_init()
    {
        \add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        \add_action('show_user_profile',     [$this, 'show_user_profile']);
        \add_action('edit_user_profile',     [$this, 'show_user_profile']);
    }

    /**
     * @param string $hook
     */
    public function admin_enqueue_scripts($hook)
    {
        if ('user-edit.php' === $hook || 'profile.php' === $hook) {
            \wp_enqueue_style('wwapppass-profile-css', \plugins_url('/assets/profile.min.css', \dirname(__DIR__) . '/plugin.php'), [], '1');
            \wp_enqueue_script('doT', 'https://cdnjs.cloudflare.com/ajax/libs/dot/1.1.2/doT.min.js', [], '1.1.2', true);
            \wp_enqueue_script('wwapppass-profile-js', \plugins_url('/assets/profile.min.js', \dirname(__DIR__) . '/plugin.php'), ['jquery', 'doT'], \filemtime(__DIR__ . '/../assets/profile.min..js'), true);
            \wp_localize_script('wwapppass-profile-js', 'wwAppPass', [
                'rest_base'      => \rest_url() . Plugin::REST_NS . '/application-passwords/',
                'nonce'          => \wp_create_nonce('wp_rest'),
                'revoke'         => \__('Are you sure you want to revoke this password? This action cannot be undone!', 'wwapppass'),
                'revokeAll'      => \__('Are you sure you want to revoke ALL passwords? This action cannot be undone!', 'wwapppass'),
                'ajaxError'      => \__('There was an error communicating with server.', 'wwapppass'),
                'ajaxError2'     => \__('There was an error communicating with server. Server replied: ', 'wwapppass'),
                'revokeError'    => \__('Failed to revoke the password.', 'wwapppass'),
                'revokeAllError' => \__('There was an error revoking passwords. Please reload the page.', 'wwapppass'),
                'revokeOK'       => \__('Password has been revoked.', 'wwapppass'),
                'revokeAllOK'    => \__('All passwords have been revoked.', 'wwapppass'),
            ]);
        }
    }

    public function show_user_profile(\WP_User $user)
    {
        $params = ['user_id' => $user->ID];
        require __DIR__ . '/../views/passwords.php';
    }

    public function auth_app()
    {
        $params = [
            'name'         => 'Sample Application',
            'redirect_uri' => ''
        ];

        require __DIR__ . '/../views/authorize.php';
    }
}
