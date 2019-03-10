<?php
defined('WP_UNINSTALL_PLUGIN') || die();

delete_metadata('user', 0, WildWolf\ApplicationPasswords\AppPasswords::USERMETA_KEY, '', true);
