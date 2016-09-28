<?php
/**
 * BuddyPress - Members Register
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div id="buddypress">
	<?php

	/**
	 * Fires at the top of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_register_page' ); ?>

	<div class="page" id="register-page">

		<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

		<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
			do_action( 'template_notices' ); ?>
			<?php

			/**
			 * Fires before the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_before_registration_disabled' ); ?>

				<p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

			<?php

			/**
			 * Fires after the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_after_registration_disabled' ); ?>
		<?php endif; // registration-disabled signup step ?>

		<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
			do_action( 'template_notices' ); ?>
			<h2 class="registration-headline screamer sablue"><?php $avatar = bp_core_fetch_avatar( array(
					'item_id' => sa_get_group_id(),
					'object'  => 'group',
					'type'    => 'thumb',
					'class'   => 'registration-logo',

				) );
			echo $avatar; ?><span class="sa-join-header-text">Join the <em>Salud America!</em> Hub and the Community Commons.</span></h2>
			<p>Register with <em>Salud America!</em> and we&rsquo;ll make you a Salud Leader, a member of our national movement of parents, teachers, academics, and activists who want to reduce Latino childhood obesity.</p>
			<p>Then you can (for free):</p>
			<ol>
				<li>See yourself on our map of the movement!</li>
				<li>Use our map to connect with others interested in creating healthy communities!</li>
				<li>Get an email &ldquo;state of obesity&rdquo; report for your area!</li>
				<li>Get great action campaigns, content, and data from <em>Salud America!</em> (a Robert Wood Johnson Foundation program based at the UT Health Science Center at San Antonio) and our online home, the Community Commons network of network for healthy communities!</li>
			</ol>

			<?php

			/**
			 * Fires before the display of member registration account details fields.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_before_account_details_fields' ); ?>

			<div class="register-section" id="basic-details-section">

				<?php /***** Basic Account Details ******/ ?>

				<h4><?php _e( 'Account Details', 'buddypress' ); ?></h4>

				<div class="editfield">
					<label for="signup_username"><?php _e( 'Username', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
					<?php

					/**
					 * Fires and displays any member registration username errors.
					 *
					 * @since 1.1.0
					 */
					 do_action( 'bp_signup_username_errors' ); ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>"  <?php bp_form_field_attributes( 'username' ); ?>/>
					<p class="description">Please use lowercase letters and numbers only in your username. <br />Spaces are not allowed.</p>
				</div>

				<div class="editfield">
					<label for="signup_email"><?php _e( 'Email Address', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
					<?php

					/**
					 * Fires and displays any member registration email errors.
					 *
					 * @since 1.1.0
					 */
					 do_action( 'bp_signup_email_errors' ); ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?> />
				</div>

				<?php do_action( 'bp_signup_after_email' ); ?>

				<div class="editfield">
					<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
					<?php

					/**
					 * Fires and displays any member registration password errors.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_signup_password_errors' ); ?>
					<input type="password" name="signup_password" id="signup_password" value="" class="password-entry" <?php bp_form_field_attributes( 'password' ); ?>/>
					<div id="pass-strength-result"></div>
				</div>

				<div class="editfield">
					<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
					<?php

					/**
					 * Fires and displays any member registration password confirmation errors.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_signup_password_confirm_errors' ); ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" class="password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>/>
				</div>

				<?php

				/**
				 * Fires and displays any extra member registration details fields.
				 *
				 * @since 1.9.0
				 */
				do_action( 'bp_account_details_fields' ); ?>

			<?php

			/**
			 * Fires after the display of member registration account details fields.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_account_details_fields' ); ?>

			<?php /***** Extra Profile Details ******/ ?>

			<?php if ( bp_is_active( 'xprofile' ) ) : ?>

				<?php

				/**
				 * Fires before the display of member registration xprofile fields.
				 *
				 * @since 1.2.4
				 */
				do_action( 'bp_before_signup_profile_fields' ); ?>

					<h4 class="cc-profile-details-header" style="margin-top:3.4em">Community Commons Profile Details</h4>
<!-- <pre> -->
					<?php
					// This logic is tortured, but we really want to use bp_has_profile().
					// Since on this registratoin form, we're grabbing fields from different groups
					// and smooshing them together, we're working outside of how bp_has_profile()
					// is built to work. There is an "exclude fields arg" that we can use,
					// but we'll need to exclude every field (in every group) we don't want to use.
					// Start by finding all the field ids.
					$all_groups = bp_xprofile_get_groups( array( 'fetch_fields' => true ) );
					$all_field_ids = array();
					foreach ( $all_groups as $field_group ) {
						$field_ids = wp_list_pluck( $field_group->fields, 'id' );
						// echo PHP_EOL . "field ids";
						// var_dump($field_ids);
						$all_field_ids = array_merge( $all_field_ids, $field_ids );
					}

					// Now we remove the fields we want to include from the exclude list.
					    $location = get_site_url( null, '', 'http' );
					    switch ( $location ) {
					        case 'http://commonsdev.local':
					            $include_fields = array(
					            	1, // Display Name (all members)
					            	103, // Affiliations (all members)
					            	96, // Leaders Map Opt-in (SA)
					            	98, // Location (SA)
					            	99, // Organization/Occupation (SA)
					            	100, // Website (SA)
					            	101, // About Me (SA)
					            	);
					            break;
					        case 'http://dev.communitycommons.org':
					            $include_fields = array();
					            break;
					        case 'http://staging.communitycommons.org':
					            $include_fields = array(
					            	1, // Display Name (all members)
					            	990, // Affiliations (all members)
					            	1013, // Leaders Map Opt-in (SA)
					            	949, // Location (SA)
					            	951, // Organization/Occupation (SA)
					            	956, // Website (SA)
					            	950, // About Me (SA)
					            	1100, // Self-efficacy (SA)
					            	1101, // Collective Efficacy
					            	1173, // Join the SA e-mail list
					            	);
					            break;
					        case 'http://www.communitycommons.org':
   					        default:
					            $include_fields = array(
					            	1, // Display Name (all members)
					            	1218, // Affiliations (all members)
					            	1312, // Leaders Map Opt-in (SA)
					            	1314, // Location (SA)
					            	1315, // Organization/Occupation (SA)
					            	1316, // Website (SA)
					            	1317, // About Me (SA)
					            	1318, // Self-efficacy (SA)
					            	1329, // Collective Efficacy
					            	1382, // Join the SA e-mail list
					            	);
					            break;
					    }

					    $exclude_fields = array_diff( $all_field_ids, $include_fields );

						// echo PHP_EOL . "exclude_fields";
						// var_dump($exclude_fields);

					?>
<!-- </pre> -->
					<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
					<?php
					if ( bp_is_active( 'xprofile' ) ) :
						if ( bp_has_profile( array( 'exclude_fields' => $exclude_fields, 'fetch_field_data' => false ) ) ) :
							while ( bp_profile_groups() ) : bp_the_profile_group();
						// We end the div after the BP base group and start a new one before the the SA fields.
						if ( 1 != bp_get_the_profile_group_id() ) {
							?>
							</div>
							<div class="register-section alignright">
								<h4><em>Salud America!</em> Profile Details</h4>
							<?php
						}

						?>

					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						<div<?php bp_field_css_class( 'editfield' ); ?>>

							<?php
							$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
							$field_type->edit_field_html();

							/**
							 * Fires before the display of the visibility options for xprofile fields.
							 *
							 * @since 1.7.0
							 */
							do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

							if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
								<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
									<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'buddypress' ); ?></a>
								</p>

								<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
									<fieldset>
										<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

										<?php bp_profile_visibility_radio_buttons() ?>

									</fieldset>
									<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>

								</div>
							<?php else : ?>
								<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
									<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?>
								</p>
							<?php endif ?>

							<?php

							/**
							 * Fires after the display of the visibility options for xprofile fields.
							 *
							 * @since 1.1.0
							 */
							do_action( 'bp_custom_profile_edit_fields' ); ?>

							<p class="description"><?php bp_the_profile_field_description(); ?></p>

						</div>

					<?php endwhile; ?>

					<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

					<?php
					endwhile;
				endif;
			endif; ?>

					<?php

					/**
					 * Fires and displays any extra member registration xprofile fields.
					 *
					 * @since 1.9.0
					 */
					do_action( 'bp_signup_profile_fields' ); ?>

				</div><!-- #profile-details-section -->

				<?php

				/**
				 * Fires after the display of member registration xprofile fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_signup_profile_fields' ); ?>

			<?php endif; ?>

			<?php if ( bp_get_blog_signup_allowed() ) : ?>

				<?php

				/**
				 * Fires before the display of member registration blog details fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_before_blog_details_fields' ); ?>

				<?php /***** Blog Creation Details ******/ ?>

				<div class="register-section" id="blog-details-section">

					<h4><?php _e( 'Blog Details', 'buddypress' ); ?></h4>

					<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ); ?></label></p>

					<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

						<label for="signup_blog_url"><?php _e( 'Blog URL', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
						<?php

						/**
						 * Fires and displays any member registration blog URL errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_url_errors' ); ?>

						<?php if ( is_subdomain_install() ) : ?>
							http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_signup_subdomain_base(); ?>
						<?php else : ?>
							<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
						<?php endif; ?>

						<label for="signup_blog_title"><?php _e( 'Site Title', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
						<?php

						/**
						 * Fires and displays any member registration blog title errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_title_errors' ); ?>
						<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

						<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ); ?></span>
						<?php

						/**
						 * Fires and displays any member registration blog privacy errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_privacy_errors' ); ?>

						<label for="signup_blog_privacy_public"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ); ?></label>
						<label for="signup_blog_privacy_private"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ); ?></label>

						<?php

						/**
						 * Fires and displays any extra member registration blog details fields.
						 *
						 * @since 1.9.0
						 */
						do_action( 'bp_blog_details_fields' ); ?>

					</div>

				</div><!-- #blog-details-section -->

				<?php

				/**
				 * Fires after the display of member registration blog details fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_blog_details_fields' ); ?>

			<?php endif; ?>

			<input type="hidden" name="salud_interest_group" id="salud_interest_group" value="1" />

			<?php

			/**
			 * Fires before the display of the registration submit buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_before_registration_submit_buttons' ); ?>

			<div class="submit">
				<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Complete Sign Up', 'buddypress' ); ?>" />
			</div>

			<?php

			/**
			 * Fires after the display of the registration submit buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_registration_submit_buttons' ); ?>

			<?php wp_nonce_field( 'bp_new_signup' ); ?>

		<?php endif; // request-details signup step ?>

		<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
			do_action( 'template_notices' ); ?>
			<?php

			/**
			 * Fires before the display of the registration confirmed messages.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_before_registration_confirmed' ); ?>

			<?php if ( bp_registration_needs_activation() ) : ?>
				<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
			<?php else : ?>
				<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
			<?php endif; ?>

			<?php

			/**
			 * Fires after the display of the registration confirmed messages.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_after_registration_confirmed' ); ?>

		<?php endif; // completed-confirmation signup step ?>

		<?php

		/**
		 * Fires and displays any custom signup steps.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php

	/**
	 * Fires at the bottom of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_register_page' ); ?>

</div><!-- #buddypress -->
