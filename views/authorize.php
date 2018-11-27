<?php defined('ABSPATH') || die(); ?>
<?php global $current_user; ?>
<div class="wrap">
    <h2><?=__('Authorize Application', 'wwapppass'); ?></h2>

    <div class="card">
        <h2 class="title"><?=__('Account Connection Request', 'wwapppass'); ?></h2>

        <form action="<?=esc_attr(admin_url('admin-post.php')); ?>" method="post" id="authform">
            <p><?=__('An application would like to connect to your account.', 'wwapppass'); ?></p>

            <label for="appname"><?=__('Application name:', 'wwapppass'); ?></label>
            <input type="text" id="appname" name="appname" value="<?=esc_html($params['name']); ?>" required="required"/>

            <p><?=__('Would you like to grant this application access to your account? You should only do this if you trust this application.', 'wwapppass'); ?></p>

            <input type="hidden" name="action" value="wwapppass_authorize_application"/>
            <input type="hidden" name="redirect_uri" id="redirect_uri" value="<?=esc_attr($params['redirect_uri']); ?>"/>
            <?php wp_nonce_field('authorize_application'); ?>

            <p>
                <?php submit_button(__('Yes, I authorize this application', 'wwapppass'), 'primary', 'authorize', false); ?>
                <?php submit_button(__('No, I reject this application', 'wwapppass'), 'secondary', 'reject', false); ?>
            </p>
        </form>

        <form id="redirect_form" action="#" method="post" hidden="hidden">
            <input
        </form>
    </div>
</div>

<script type="text/template" id="tmpl-ww-app-pass-msg">
    <div id="result" hidden="true">
        <p><?=sprintf(__('<strong>Your login:</strong> <code>%s</code>', 'wwapppass'), esc_html($current_user->user_login)); ?></p>
        <p><?=sprintf(__('<strong>Application password:</strong> %s', 'wwapppass'), '<code id="apppass">{{! it.pass }}</code>'); ?></p>
    </div>
</script>
