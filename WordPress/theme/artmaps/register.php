<?php
require_once("../../../wp-config.php");
get_header();
$errors = new WP_Error();
wp_openid_selector_login_head();
?>
<div>
<h1>
    From here you can create a new account using either your OpenID
    if you have one or by registering a new username and your email address.
</h2>
<hr />
<form id="setupform" method="post" action="<?= network_site_url() ?>wp-signup.php">
    <input type="hidden" name="stage" value="validate-user-signup" />
    <?php do_action("signup_hidden_fields"); ?>
    <label for="user_name"><?= __('Username:') ?></label>
    <input name="user_name" type="text" id="user_name" value="<?= esc_attr($user_name) ?>" maxlength="60" /><br />
    <?php _e("(Must be at least 4 characters, letters and numbers only.)") ?><br />
    <label for="user_email"><?php _e("Email&nbsp;Address:") ?></label>
    <input name="user_email" type="text" id="user_email" value="<?= esc_attr($user_email) ?>" maxlength="200" /><br />
    <?php _e("We send your registration email to this address. (Double-check your email address before continuing.)") ?>
    <?php do_action("signup_extra_fields", $errors); ?>
    <input id="signupblog" type="hidden" name="signup_for" value="user" />
    <br />
	<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Next') ?>" /></p>
</form>
</div>
<hr />
<div>
<form id="registerform" method="post" action="<?= site_url("wp-login.php") ?>" >
    <hr id="openid_split" style="clear: both; margin-bottom: 1.0em; border: 0; border-top: 1px solid #999; height: 1px;" />
    <div id="openid_choice">
    	<p><label><?php _e('Or click your account provider:', 'wpois') ?></label></p>
    	<div id="openid_btns"></div>
    </div>
    <div id="openid_input_area"></div>

    <p style="margin-bottom: 8px;">
        <label style="display: block; margin-bottom: 5px;">
            Register with an OpenID<br />
            <input type="text" name="openid_identifier" id="openid_identifier" class="input openid_identifier" value="" size="20" tabindex="25" />
        </label>
	</p>
	<p style="font-size: 0.9em; margin: 8px 0 24px 0;" id="what_is_openid">
		<a href="http://openid.net/what/" target="_blank"><?= __('Learn about OpenID', 'openid') ?></a>
	</p>
	<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In" tabindex="100" />
	</form>
</div>
<?php
get_footer();
?>
