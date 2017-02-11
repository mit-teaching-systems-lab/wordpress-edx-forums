<?php

/**
 * BuddyPress Member Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>


			<?php do_action( 'bp_before_member_settings_template' ); ?>

				<h3><?php _e( 'General Settings', 'buddypress' ); ?></h3>

				<?php do_action( 'bp_template_content' ); ?>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

					<label for="email"><?php _e( 'Account Email', 'buddypress' ); ?></label>
					<input type="text" name="email" id="email" readonly value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" />

					<?php wp_nonce_field( 'bp_settings_general' ); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>
