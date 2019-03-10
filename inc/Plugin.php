<?php
namespace WildWolf\ApplicationPasswords;

final class Plugin
{
	const REST_NS = 'wildwolf/apppass/v1';

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
		\add_action('plugins_loaded', [HttpAuthorization::class, 'fixup']);
		\add_action('init',           [$this, 'init']);

		if (\is_admin()) {
			Admin::instance();
		}
		elseif (Utils::isCLI()) {
			CLI::instance();
		}
	}

	public function init()
	{
		\add_filter('authenticate',           [$this, 'authenticate'], 10, 3);
		\add_filter('determine_current_user', [$this, 'determine_current_user'], 15);
		\add_action('rest_api_init',          [$this, 'rest_api_init'], 10, 1);
	}

	public function authenticate($user, $username, $password)
	{
		if (!Utils::isApiRequest()) {
			return $user;
		}

		$u = \get_user_by('login', $username);
		if (!$u) {
			return $user;
		}

		return AppPasswords::validate($u->ID, str_replace(' ', '', $password)) ? $u : $user;
	}

	public function determine_current_user($user)
	{
		if (!empty($user) || !isset($_SERVER['PHP_AUTH_USER'])) {
			return $user;
		}

		$u = $this->authenticate($user, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		return ($u instanceof \WP_User) ? $u->ID : $user;
	}

	public function rest_api_init(\WP_Rest_Server $server)
	{
		if (Utils::isRestRequest() && Utils::isGuest()) {
			global $current_user;
			$current_user = null;
		}

		RESTController::instance();
	}
}
