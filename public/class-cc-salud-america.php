<?php
/**
 * Community Commons Salud America
 *
 * @package   Community_Commons_Salud_America
 * @author    David Cavins
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `admin/class-cc-salud-america-admin.php`
 *
 *
 * @package Community_Commons_Salud_America
 * @author  David Cavins
 */
class CC_Salud_America {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '0.1.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'cc-salud-america';

	/**
	* An array of the post types that should generate an activity item when published.
	*
	*/
	public $activity_post_types = array( 'sapolicies', 'saresources', 'sa_success_story', 'sa_take_action', 'sa_video_contest' );

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		// add_action( '@TODO', array( $this, 'action_method_name' ) );
		// add_filter( '@TODO', array( $this, 'filter_method_name' ) );

		// Add our templates to BuddyPress' template stack.
		add_filter( 'bp_get_template_stack', array( $this, 'add_template_stack'), 10, 1 );
		add_filter( 'bp_get_template_part', array( $this, 'replace_group_header' ), 10, 3 );

		// Modify the permalinks for SA-related CPTs. Point all traffic to the group.
		add_filter( 'post_type_link', array( $this, 'cpt_permalink_filter'), 12, 2);

		add_action( 'admin_menu', array( $this, 'register_admin_page_aggregator' ) );

		add_action( 'cc_group_home_page_before_content', array( $this, 'build_home_page_notices' ) );

		// Add activity stream items when policies are published
		add_action( 'transition_post_status', array( $this, 'create_post_activity' ), 10, 3 );
		add_action( 'transition_post_status', array( $this, 'delete_post_activity' ), 10, 3 );

		// Add SA section to registration page
		add_action( 'bp_before_registration_submit_buttons', array( $this, 'salud_interest_section_registration' ), 71 );
		add_action( 'bp_core_signup_user', array( $this, 'sa_newsletter_opt_in_fields' ), 1, 71 );

		// Add the Salud America interest query string to the register link on SA pages
		add_filter( 'registration_form_interest_query_string', array( $this, 'add_registration_interest_parameter' ), 12, 1 );

	}


	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( sa_is_sa_group() ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), self::VERSION );
			    global $wp_styles;
		    wp_enqueue_style( $this->plugin_slug . '-ie-plugin-styles', plugins_url( 'css/public-ie.css', __FILE__ ), array(), self::VERSION );
		    $wp_styles->add_data( $this->plugin_slug . '-ie-plugin-styles', 'conditional', 'lt IE 9' );
		}

	    if ( bp_is_register_page() && isset( $_GET['salud-america'] ) && $_GET['salud-america'] ) {
			wp_enqueue_style( 'salud-section-register-css', plugins_url( 'css/sa_registration.css', __FILE__ ), array(), '0.1', 'screen' );
		}

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( sa_is_sa_group() ) {
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	/**
	 * Add our templates to BuddyPress' template stack.
	 *
	 * @since    1.0.0
	 */
	public function add_template_stack( $templates ) {
	    // if we're on a page of our plugin and the theme is not BP Default, then we
	    // add our path to the template path array
	    if ( bp_is_current_component( 'groups' ) && sa_is_sa_group() ) {
	        $templates[] = trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' );
	    }
	   // $towrite = print_r($templates, TRUE);
	   // $fp = fopen('template_stack.txt', 'a');
	   // fwrite($fp, $towrite);
	   // fclose($fp);
	    return $templates;
	}

	public function replace_group_header( $templates, $slug, $name ) {
		if ( $slug == 'groups/single/group-header' && sa_is_sa_group() ) {
			$templates = array( 'groups/single/group-header-slug-salud-america.php' );
		}
		return $templates;
	}

	/**
	 * Modify the permalinks for SA-related CPTs. Point all traffic to the group.
	 *
	 * @since    1.0.0
	 */
	public function cpt_permalink_filter( $permalink, $post ) {
		$post_type = get_post_type( $post );

		if ( in_array( $post_type, array( 'saresources', 'sa_success_story', 'sa_take_action', 'sapolicies', 'sa_video_contest' ) ) ) {
			$section = sa_get_section_by_cpt( $post_type );
			$section_permalink = sa_get_section_permalink( $section );
			$permalink = $section_permalink . $post->post_name;
		}

	    return $permalink;
	}

	public function register_admin_page_aggregator() {
		add_menu_page( 'Salud America', 'Salud America', 'edit_others_posts', 'salud_america', function() { echo 'Salud America'; }, 'dashicons-arrow-right', 48 );
	}


	/*--------------------------------------------*
	 * Helper Functions
	 *--------------------------------------------*/

	/**
	 * Determines whether or not the current user has the ability to save meta data associated with this post.
	 *
	 * @param		int		$post_id	The ID of the post being save
	 * @param		bool				Whether or not the user has the ability to save this post.
	 */
	public function user_can_save( $post_id, $nonce_value, $nonce_name ) {

	    // Don't save if the user hasn't submitted the changes
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		} // end if

		// Verify that the input is coming from the proper form
		if( ! wp_verify_nonce( $_POST[ $nonce_value ], $nonce_name ) ) {
			return false;
		} // end if

		// @TODO: Add user permission checks
		// Make sure the user has permissions to post
		// if( 'post' == $_POST['post_type'] ) {
		// 	if( ! current_user_can( 'edit_post', $post_id ) ) {
		// 		return;
		// 	} // end if
		// } // end if/else

		return true;

	} // end user_can_save

	/**
	 * General handler for saving post meta.
	 *
	 * @since   1.0.0
	 *
	 * @param 	int $post_id
	 * @param 	array meta_key names to save
	 * @return  bool
	 */
	function save_meta_fields( $post_id, $fields = array() ) {
	    $successes = 0;

	    foreach( $fields as $field ) {
	      //groups_update_groupmeta returns false if the old value matches the new value, so we'll need to check for that case
	      $old_setting = get_post_meta( $post_id, $field, true );
	      $new_setting = ( isset( $_POST[$field] ) ) ? $_POST[$field] : '' ;
	      $success = false;

	      $towrite = PHP_EOL . 'field: ' . print_r( $field, TRUE );
	      $towrite .= PHP_EOL . 'old setting: ' . print_r($old_setting, TRUE);
	      $towrite .= PHP_EOL . 'new setting: ' . print_r($new_setting, TRUE);


	      if ( empty( $new_setting ) && ! empty( $old_setting ) ) {
	        $success = delete_post_meta( $post_id, $field );
	        $towrite .= PHP_EOL . 'did delete';

	      } elseif ( $new_setting == $old_setting ) {
	          // No need to resave settings if they're the same
	          $success = true;
	          $towrite .= PHP_EOL . 'did nothing';
	      } else {
	        $success = update_post_meta( $post_id, $field, $new_setting );
	        $towrite .= PHP_EOL . 'did update';
	      }

	      if ( $success ) {
	        $successes++;
	      }

	     $fp = fopen('saving_meta.txt', 'a');
	     fwrite($fp, $towrite);
	     fclose($fp);
	    }

	    if ( $successes == count( $fields ) ) {
	      return true;
	    } else {
	      return false;
	    }
	}

	/**
	 * General handler for saving post taxonomy (like geography terms).
	 *
	 * @since   1.0.0
	 *
	 * @param 	int $post_id
	 * @param 	string $tax_field $_POST key_value to check.
	 * @return  bool
	 */
	public function save_taxonomy_field( $post_id, $tax_field, $taxonomy ) {
		// Don't save empty metas
		// @TODO: This wouldn't allow for the removal of a term
		if ( ! empty( $_POST[$tax_field] ) ) {
			$term_ids = array( $_POST[$tax_field] );
			//Make sure the terms IDs are integers:
			$term_ids = array_map('intval', $term_ids);
			$term_ids = array_unique( $term_ids );
			return wp_set_object_terms( $post_id, $term_ids, $taxonomy );
		}
	}

	/**
	 * Build the notices box on the Salud America group homepage.
	 *
	 * @since   1.0.0
	 *
	 * @param 	int $group_id
	 * @return  html
	 */
	public function build_home_page_notices( $current_group_id ){
		if ( $current_group_id != sa_get_group_id() ) {
			return;
		}

		$notices = apply_filters( 'sa_group_home_page_notices', '' );

		if ( ! empty( $notices ) ){
			?>
			<div id="message" class="error">
				<?php echo $notices; ?>
			</div>
			<?php
		}
	}

	/**
	 * Post an activity item on publishing a new policy.
	 *
	 * @package BuddyPress Docs
	 * @since 1.0-beta
	 *
	 * @param obj $query The query object created in BP_Docs_Query and passed to the
	 *        bp_docs_doc_saved filter
	 * @return int $activity_id The id number of the activity created
	 */
	function create_post_activity( $new_status, $old_status, $post ) {
		// Only work on salud america-related post types
		if ( ! in_array( $post->post_type, $this->activity_post_types ) ) {
			return;
		}

		// Only when they change to publish. Don't show updates, drafts.
		if ( ! ( $new_status == 'publish' && $old_status != 'publish' ) ) {
			return;
		}

		$bp = buddypress();

		// The action hook we're using will only run when a post is changed to "publish" status
		$post_id = $post->ID;
		$author_id = (int) $post->post_author;
		$user_link = bp_core_get_userlink( $author_id );

		$post_type_object = get_post_type_object( $post->post_type );
		$post_type_label = strtolower( $post_type_object->labels->singular_name );

		$post_url = get_permalink( $post_id );
		$post_link = sprintf( '<a href="%s">%s</a>', $post_url, get_the_title( $post_id ) );

		$group_id = sa_get_group_id();
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		$group_url  = bp_get_group_permalink( $group );
		$group_link = '<a href="' . $group_url . '">' . $group->name . '</a>';

		$action = sprintf( __( '%1$s published the %2$s %3$s in the Hub %4$s', $this->plugin_slug ), $user_link, $post_type_label, $post_link, $group_link );

		$type = $post->post_type . '_created';

		$excerpt = cc_ellipsis( $post->post_content, $max=100, $append='&hellip;' );

		$args = array(
			'user_id'		=> $author_id,
			'action'		=> $action,
			'primary_link'	=> $post_link,
			'component'		=> $bp->groups->id,
			'type'			=> $type,
			'item_id'		=> $group_id, // Set to the group/user/etc id, for better consistency with other BP components
			'secondary_item_id'	=> $post_id, // The id of the doc itself
			'recorded_time'		=> bp_core_current_time(),
			'hide_sitewide'		=> false, // Filtered to allow plugins and integration pieces to dictate
			'content'			=> $excerpt
		);

		do_action( $post->post_type . '_before_activity_save', $args );

		$activity_id = bp_activity_add( apply_filters( $post->post_type . '_activity_args', $args, $post_id ) );

		return $activity_id;
	}

	/**
	 * Delete activity associated with a post
	 *
	 * Run on transition_post_status, to catch deletes from all locations
	 *
	 * @since 1.0.0
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param obj WP_Post object
	 */
	public function delete_post_activity( $new_status, $old_status, $post ) {
		// Only work on salud america-related post types
		if ( ! in_array( $post->post_type, $this->activity_post_types ) ) {
			return;
		}

		// Only when they change from publish. Fire on change to trash, draft.
		if ( ! ( $new_status != 'publish' && $old_status == 'publish' ) ) {
			return;
		}

		$activities = bp_activity_get(
			array(
				'filter' => array(
					'secondary_id' => $post->ID,
					'component' => $bp->groups->id,
				),
			)
		);

		foreach ( (array) $activities['activities'] as $activity ) {
			bp_activity_delete( array( 'id' => $activity->id ) );
		}
	}

	/**
	 * Add SA newsletter opt-in checkbox on register page
	 * @since 0.1
	 */
	function salud_interest_section_registration() {
	  if ( isset( $_GET['salud-america'] ) && $_GET['salud-america'] ) :
	  ?>
	    <div id="sa-interest-opt-in" class="alignright register-section checkbox">
		    <?php $avatar = bp_core_fetch_avatar( array(
				'item_id' => sa_get_group_id(),
				'object'  => 'group',
				'type'    => 'thumb',
				'class'   => 'registration-logo',

			) );
			echo $avatar; ?>
	      <h4 class="registration-headline">Join the Hub: <em>Salud America!</em> <br />Growing Healthy Change</h4>

	      <label><input type="checkbox" name="salud_interest_group" id="salud_interest_group" value="agreed" <?php $this->determine_checked_status_default_is_checked( 'salud_interest_group' ); ?> /> Yes, I’m interested in work by Salud America! to reduce Latino childhood obesity.</label>

	      <label><input type="checkbox" name="salud_newsletter" id="salud_newsletter" value="agreed" <?php $this->determine_checked_status_default_is_checked( 'salud_newsletter' ); ?> /> I would like to receive email updates on this topic.</label>

	      <p class="description">Periodically, Salud America! sends out news updates and brief surveys.</p>

	    </div>
	    <?php
	    endif;
	}

	/**
	* Update usermeta with custom registration data
	* @since 0.1
	*/
	function sa_newsletter_opt_in_fields( $user_id ) {

		if ( isset( $_POST['salud_interest_group'] ) ) {
			// Create the group request
			$request = groups_join_group( sa_get_group_id(), $user_id );
			// $request = groups_send_membership_request( $user_id, sa_get_group_id() );
		}

		if ( isset( $_POST['salud_newsletter'] ) ) {
		    update_usermeta( $user_id, 'salud_newsletter', $_POST['salud_newsletter'] );
		}

	  return $user_id;
	}

	public function add_registration_interest_parameter( $interests ) {

	    if ( bp_is_groups_component() && sa_is_sa_group() ) {
	    	$interests[] = 'salud-america';
		}

	    return $interests;
	}

	function determine_checked_status_default_is_checked( $field_name ){
	  // In its default state, no $_POST should exist. If this is a resubmit effort, $_POST['signup_submit'] will be set, then we can trust the value of the checkboxes.
	  if ( isset( $_POST['signup_submit'] ) && !isset( $_POST[ $field_name ] ) ) {
	    // If the user specifically unchecked the box, don't make them do it again.
	  } else {
	    // Default state, $_POST['signup_submit'] isn't set. Or, it is set and the checkbox is also set.
	    echo 'checked="checked"';
	  }
	}

}
$cc_salud_america = new CC_Salud_America();