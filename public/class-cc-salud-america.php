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

		// Modify the permalinks for SA-related CPTs. Point all traffic to the group.
		add_filter( 'post_type_link', array( $this, 'cpt_permalink_filter'), 12, 2);

		add_action( 'admin_menu', array( $this, 'register_admin_page_aggregator' ) );

		add_action( 'cc_group_home_page_before_content', array( $this, 'build_home_page_notices' ) );

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

}
$cc_salud_america = new CC_Salud_America();