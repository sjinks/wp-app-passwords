<?php
namespace WildWolf\ApplicationPasswords;

abstract class AppPasswords
{
	const USERMETA_KEY = '_ww_app_passwords';

	public static function slug(array $item): string
	{
		$hash = \sha1($item['name'] . "\x00" . $item['created'] . "\x00" . $item['password']);
		return \substr($hash, 6, 12);
	}

	public static function get(int $id) : array
	{
		$list = \get_user_meta($id, self::USERMETA_KEY, true);
		return \is_array($list) ? $list : [];
	}

	public static function set(int $id, array $items)
	{
		\update_user_meta($id, self::USERMETA_KEY, $items);
	}

	public static function create(int $id, string $name) : array
	{
		$password = \wp_generate_password(24, false);
		$hashed   = \wp_hash_password($password);
		$all      = self::get($id);
		$entry    = [
			'name'      => $name,
			'password'  => $hashed,
			'created'   => \time(),
			'last_used' => null,
			'last_ip'   => null,
		];

		$all[] = $entry;
		self::set($id, $all);

		return [$password, $entry];
	}

	public static function delete(int $id, string $slug) : bool
	{
		$all = self::get($id);
		foreach ($all as $key => $item) {
			if ($slug === AppPasswords::slug($item)) {
				unset($all[$key]);
				self::set($id, $all);
				return true;
			}
		}

		return false;
	}

	public static function deleteAll(int $id)
	{
		\delete_user_meta($id, self::USERMETA_KEY);
	}

	public static function validate(int $id, string $password) : bool
	{
		$hashed = self::get($id);
		foreach ($hashed as &$v){
			if (\wp_check_password($password, $v['password'], $id)) {
				$v['last_used'] = \time();
				$v['last_ip']   = $_SERVER['REMOTE_ADDR'] ?? '';
				AppPasswords::set($id, $hashed);
				return true;
			}
		}

		unset($v);
		return false;
	}
}
