<?php
namespace WildWolf\ApplicationPasswords;

class RESTController
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
		$this->registerRoutes();
	}

	private function registerRoutes()
	{
		\register_rest_route(Plugin::REST_NS, '/application-passwords/(?P<user_id>[\d]+)', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [$this, 'listPasswords'],
			'permission_callback' => [$this, 'canEditUser'],
		]);

		\register_rest_route(Plugin::REST_NS, '/application-passwords/(?P<user_id>[\d]+)', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'newPassword'],
			'permission_callback' => [$this, 'canEditUser'],
			'args'                => [
				'name' => ['required' => true],
			],
		]);

		\register_rest_route(Plugin::REST_NS, '/application-passwords/(?P<user_id>[\d]+)/(?P<slug>[0-9A-Fa-f]{12})', [
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [$this, 'deletePassword'],
			'permission_callback' => [$this, 'canEditUser'],
		]);

		\register_rest_route(Plugin::REST_NS, '/application-passwords/(?P<user_id>[\d]+)', [
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [$this, 'deleteAllPasswords'],
			'permission_callback' => [$this, 'canEditUser'],
		]);
	}

	public function listPasswords(\WP_REST_Request $data) : array
	{
		$hashed = AppPasswords::get((int)$data['user_id']);
		$result = [];

		foreach ($hashed as $item) {
			$slug = AppPasswords::slug($item);
			unset($item['password']);
			$result[$slug] = $item;
		}

		return $result;
	}

	public function newPassword(\WP_REST_Request $data) : array
	{
		list($password, $entry) = AppPasswords::create((int)$data['user_id'], $data['name']);

		$entry['slug']      = AppPasswords::slug($entry);
		$entry['created']   = \date(\get_option('date_format', 'r'), $entry['created']);
		$entry['last_used'] = \__('&mdash;', 'wwapppass');
		$entry['last_ip']   = \__('&mdash;', 'wwapppass');
		unset($entry['password']);

		return ['row' => $entry, 'password' => $password];
	}

	public function deletePassword(\WP_REST_Request $data) : bool
	{
		return AppPasswords::delete($data['user_id'], $data['slug']);
	}

	public function deleteAllPasswords(\WP_REST_Request $data) : int
	{
		$all = AppPasswords::get($data['user_id']);
		AppPasswords::deleteAll($data['user_id']);
		return \count($all);
	}

	public function canEditUser(\WP_REST_Request $data) : bool
	{
		return \current_user_can('edit_user', $data['user_id']);
	}
}
