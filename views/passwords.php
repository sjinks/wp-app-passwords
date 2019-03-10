<?php defined('ABSPATH') || die(); ?>
<div id="ww-app-passwords">
	<h2><?=__('Application Passwords', 'wwapppass'); ?></h2>

	<p>
		<?=__('Application passwords allow for non-interactive authentication, such as XML RPC or the REST API, without providing your account password.', 'wwapppass');?>
	</p>
	<p>
		<?=__('<strong>WARNING:</strong> you <em>cannot</em> use application passwords to log into your website.', 'wwapppass'); ?>
	</p>

	<div id="create-new-app-pass" class="hide-if-no-js">
		<input type="text" id="ww-app-pass-name" class="regular-text" placeholder="<?php _e('Application Name', 'wwapppass'); ?>"/>
		<button type="button" id="ww-add-new-pass-btn" class="button button-secondary"><?=__('Add', 'wwapppass'); ?></button>
	</div>

	<div id="ww-app-passwords-table">
		<?php
		$table = new WildWolf\ApplicationPasswords\AppPasswordsTable(['user_id' => $params['user_id']]);
		$table->prepare_items();
		$table->display();
		?>
	</div>
</div>

<script type="text/template" id="tmpl-ww-app-pass-msg">
	<div class="notice notice-{{! it.type }}"><p>{{! it.message }}</p></div>
</script>

<script type="text/template" id="tmpl-ww-new-app-pass">
	<div class="new-application-password notification-dialog-wrap">
		<div class="notification-dialog-background">
			<div class="notification-dialog">
				<?php
				printf(
					__('Your new password for %1$s is: %2$s', 'wwapppass'),
					'<strong>{{! it.name }}</strong>',
					'<code>{{! it.password }}</code>'
				);
				?>
				<p>
					<?=__('Just like your normal password, this application password grants complete access to your account. You will not need to remember it, so please do not write it down or share it with anyone.', 'wwapppass');?>
					<?=__('For security reasons this password will not be displayed again.', 'wwapppass'); ?>
				</p>
				<button type="button" class="button button-primary ww-app-pass-modal-dismiss"><?=__('Dismiss', 'wwapppass'); ?></button>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-ww-app-pass-row">
	<tr>
		<td class="name column-name has-row-actions column-primary" data-colname="<?=__('Name', 'wwapppass'); ?>">
			{{! it.name }}
			<div class="row-actions">
				<span class="revoke"><button class="button-link" data-slug="{{! it.slug }}"><?=__('Revoke', 'wwapppass'); ?></button></span>
			</div>
		</td>
		<td class="created column-created" data-colname="<?=__('Created On', 'wwapppass'); ?>">{{= it.created }}</td>
		<td class="last_used column-last_used" data-colname="<?=__('Last Used', 'wwapppass'); ?>">{{= it.last_used }}</td>
		<td class="last_ip column-last_ip" data-colname="<?=__('Last IP', 'wwapppass'); ?>">{{= it.last_ip }}</td>
	</tr>
</script>
