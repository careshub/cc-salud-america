<?php
/**
 * The file that defines the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 */

/**
 * Define the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @since      1.0.0
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 * @author     Your Name <email@example.com>
 */
class CC_SA_Take_Action_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_take_action_meta_box_nonce';
	private $nonce_name = 'sa_take_action_meta_box';
	public $post_type = 'sa_take_action';

	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register Policy custom post type
		add_action( 'init', array( $this, 'register_cpt' ), 17 );

		// Register related taxonomies
		// add_action( 'init', array( $this, 'register_resource_types_taxonomy' ) );
		// add_action( 'init', array( $this, 'register_resource_cats_taxonomy' ) );

		// Add submenus to handle the edit screens for our custom taxonomies
		// add_action( 'admin_menu', array( $this, 'create_taxonomy_management_menu_items' ) );
		// add_action( 'parent_file', array( $this, 'sa_tax_menu_highlighting' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// Add our templates to BuddyPress' template stack.
		add_filter( 'manage_edit-sa_take_action_columns', array( $this, 'edit_admin_columns') );
		add_filter( 'manage_sa_take_action_posts_custom_column', array( $this, 'manage_admin_columns'), 18, 2 );
		add_filter( 'manage_edit-sa_take_action_sortable_columns', array( $this, 'register_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );

		add_filter( 'sa_group_home_page_notices', array( $this, 'add_notices' ), 10 );

	}

	/**
	 * Define the "sa_policies" custom post type and related taxonomies:
	 * "sa_advocacy_targets", "sa_policy_tags" and "sa_geographies".
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function register_cpt() {

	    $labels = array(
	        'name' => __( 'Petitions', $this->plugin_slug ),
	        'singular_name' => __( 'Petition', $this->plugin_slug ),
	        'add_new' => __( 'Add New', $this->plugin_slug ),
	        'add_new_item' => __( 'Add New Petition', $this->plugin_slug ),
	        'edit_item' => __( 'Edit Petition', $this->plugin_slug ),
	        'new_item' => __( 'New Petitions', $this->plugin_slug ),
	        'view_item' => __( 'View Petitions', $this->plugin_slug ),
	        'search_items' => __( 'Search Petitions', $this->plugin_slug ),
	        'not_found' => __( 'No petitions found', $this->plugin_slug ),
	        'not_found_in_trash' => __( 'No petitions found in Trash', $this->plugin_slug ),
	        'parent_item_colon' => __( 'Parent Petition:', $this->plugin_slug ),
	        'menu_name' => __( 'SA Petitions', $this->plugin_slug ),
	    );

	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Petition campaigns run by Salud America',
	        'supports' => array( 'title', 'editor', 'thumbnail' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true, //'salud_america',
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => true,
	        'capability_type' => $this->post_type,
	        'map_meta_cap' => true
	    );

	    register_post_type( $this->post_type, $args );

	}

	/**
	 * Change behavior of the video contests overview table.
	 * - Add expiration date.
	 *
	 * @since    1.0.0
	 *
	 * @return   array of columns to display
	 */
	public function edit_admin_columns( $columns ) {
		// Last column is Date.
		// We want to insert our new columns just before that.
		$entries = count( $columns );
		$opening_set = array_slice( $columns, 0, $entries - 1 );
		$closing_set = array_slice( $columns, - 1 );

		$insert_set = array(
			'expires_date' => __( 'Closing Date' ),
			);

		$columns = array_merge( $opening_set, $insert_set, $closing_set );

		return $columns;
	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Handle Output for Type and Stage columns (populated from post meta).
	 *
	 * @since    1.0.0
	 *
	 * @return   string content of custom columns
	 */
	public function manage_admin_columns( $column, $post_id ) {
			if ( $column == 'expires_date' ) {
				$date = get_post_meta( $post_id, 'sa_expiry_date', true );
				echo sa_convert_to_short_complete_human_date( $date );
			}
	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Add sortability to Type and Stage columns.
	 *
	 * @since    1.0.0
	 *
	 * @return   array of columns to display
	 */
	public function register_sortable_columns( $columns ) {
					$columns['expires_date'] = 'expires_date';
					//Note: Advo targets can't be sortable, because the value is a string.
					return $columns;
	}
	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Define sorting query for Type and Stage columns.
	 *
	 * @since    1.0.0
	 *
	 * @return   alters $query variable by reference
	 */
	function sortable_columns_orderby( $query ) {
			if ( ! is_admin() ) {
				return;
			}

			$orderby = $query->get( 'orderby');

			switch ( $orderby ) {
				case 'expires_date':
					$query->set( 'meta_key','sa_expiry_date' );
					$query->set( 'orderby','meta_value_num' );
					break;
			}
	}

	/**
	 * Modify the SA Policies edit screen.
	 * - Add meta boxes for policy meta and geography.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	//Building the input form in the WordPress admin area
	function add_meta_box() {
		add_meta_box( 'sa_take_action_meta_box', 'Petition Details', array( $this, 'sa_take_action_meta_box' ), 'sa_take_action', 'normal', 'high' );   ;
	}
		function sa_take_action_meta_box( $post ) {
			$custom = get_post_custom( $post->ID );
			$end_date = maybe_unserialize( $custom[ 'sa_expiry_date' ][0] );
			$stem_sentence = $custom[ 'sa_notice_box_stem' ][0];

			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_name, $this->nonce_value );
			?>
			<div>
				<p>
					<label for='sa_expiry_date'>Petition End Date</label><br />
					<input type='text' name='sa_expiry_date' id='sa_expiry_date' value='<?php
						if ( ! empty( $end_date ) ) {
							echo sa_convert_to_human_date( $end_date );
						}
					 ?>'/>
				</p>
				<p class="info">After this date, this petition will move from the "current actions" screen to the "past actions" screen. Set the start date by scheduling the publication date in the "Publish" box.</p>
				<p>
					<label for='sa_take_action_url'>Petition URL</label><br />
					<input type='text' name='sa_take_action_url' value='<?php
						if ( ! empty( $custom[ 'sa_take_action_url' ][0] ) ) {
							echo $custom[ 'sa_take_action_url' ][0];
						}
						?>' style="width:98%"/><br />
					<span class="info">Note: Petition URLs should take the form <em>http://www.thepetitionsite.com/takeaction/702/787/135/?z00m=21258369</em></span>
				</p>

				<p>
					<label for='sa_take_action_button_text'>Button Text</label>
					<input type='text' name='sa_take_action_button_text' value='<?php
						if ( ! empty( $custom[ 'sa_take_action_button_text' ][0] ) ) {
							echo $custom[ 'sa_take_action_button_text' ][0];
						}
						?>' style="width:98%"/><br />
					<span class="info">Note: If left empty, the button will read "Take Action Now!"</span>
				</p>
				<h4>Hub Home Page Notice</h4>
				<p>
					<input type="checkbox" id="sa_take_action_highlight" name="sa_take_action_highlight" value='1' <?php checked( $custom[ 'sa_take_action_highlight' ][0] ); ?> > <label for="sa_take_action_highlight">Highlight this campaign at the top of the hub home page.</label>
				</p>
				<p>
					<input type='text' name='sa_notice_box_stem' id='sa_notice_box_stem' value='<?php
						if ( ! empty( $stem_sentence) ) {
							echo $stem_sentence;
						}
					 ?>'/>
				</p>
				<p class="info">This text will be output in the notices box on the hub home page:<br />
					TAKE ACTION ALERT<br />
					<em>&laquo;notice box title&raquo;</em>
				</p>
			</div>
			<div>

		 	<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#sa_expiry_date").datepicker( {
						dateFormat: "MM d, yy",
					} );
				});
			</script>
			</div>
			<?php

			}

	/**
	 * Save resources extra meta.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function save( $post_id ) {

 		if ( get_post_type( $post_id ) != $this->post_type ) {
			return;
		}

		if ( ! $this->user_can_save( $post_id, $this->nonce_value, $this->nonce_name  ) ) {
			return false;
		}

		// Convert the end date for storage.
		if ( ! empty( $_POST[ 'sa_expiry_date' ] ) ) {
			$_POST[ 'sa_expiry_date' ] = sa_convert_to_computer_date( $_POST[ 'sa_expiry_date' ] );
		}
		// Create array of fields to save
		$meta_fields_to_save = array( 'sa_take_action_highlight', 'sa_take_action_url', 'sa_take_action_button_text', 'sa_notice_box_stem', 'sa_expiry_date' );

		// Save meta
		$meta_success = $this->save_meta_fields( $post_id, $meta_fields_to_save );

	}

	/**
	 * Add current petition to group home page notices box.
	 *
	 * @since    1.0.0
	 *
	 * @return   html
	 */
	public function add_notices( $notices ) {
		$args = array(
            'post_type' 		=> $this->post_type,
			'meta_query' => array(
	            'relation' => 'AND',
	            // Must have the highlighted box checked.
	            array(
	                'key' => 'sa_take_action_highlight',
	                'value' => 1,
	                'compare' => '=',
	                ),
	            // Must not be expired.
	            array(
	                'key' => 'sa_expiry_date', // Check the start date field.
	                'value' => date("Ymd"), // Set today's date (note the format)
	                'compare' => '>=', // Return the ones greater than today's date
	                'type' => 'NUMERIC,' // Let WordPress know we're working with numbers
	                ),
            ),
        );
        $petition = new WP_Query( $args );

        if ( $petition->have_posts() ) {
        	while ( $petition->have_posts() ):
        		$petition->the_post();
        		$post_id = get_the_ID();
        		// $post_meta = get_post_meta( $post_id );
        		// $petition_url = $post_meta['sa_take_action_url'][0];
        		// if ( isset( $post_meta['sa_take_action_button_text'][0] ) ) {
        		// 	$button_text = wptexturize( $post_meta['sa_take_action_button_text'][0] );
        		// } else {
        		// 	$button_text = 'Take Action Now!';
        		// }
        		$message = get_post_meta( $post_id, 'sa_notice_box_stem', true );
        		if ( empty( $message ) ) {
        			$message = get_the_title();
        		}
        		$notices[ $post_id ] = array(
        			'action-phrase' => 'Take Action Alert',
        			'permalink'		=> get_the_permalink(),
        			'title'			=> $message,
        			'fallback_image' => sa_get_plugin_base_uri() . 'public/images/fallbacks/take-action-icon.png'
        			);

			endwhile;
		}
		wp_reset_query();

		return $notices;
	}

} //End class CC_SA_Resources_CPT_Tax
$sa_take_action_cpt_tax = new CC_SA_Take_Action_CPT_Tax();