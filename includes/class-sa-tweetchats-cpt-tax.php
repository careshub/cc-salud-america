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
class CC_SA_Tweetchats_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_tweetchats_meta_box_nonce';
	private $nonce_name = 'sa_tweetchats_meta_box';
	public $post_type = 'sa_tweetchats';

	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register Policy custom post type
		add_action( 'init', array( $this, 'register_cpt' ), 17 );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// Add our templates to BuddyPress' template stack.
		add_filter( 'manage_edit-sa_tweetchats_columns', array( $this, 'edit_admin_columns') );
		add_filter( 'manage_sa_tweetchats_posts_custom_column', array( $this, 'manage_admin_columns'), 18, 2 );
		add_filter( 'manage_edit-sa_tweetchats_sortable_columns', array( $this, 'register_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );

		add_filter( 'sa_group_home_page_notices', array( $this, 'add_notices' ), 20 );

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
	        'name' => __( 'Tweetchats', $this->plugin_slug ),
	        'singular_name' => __( 'Tweetchat', $this->plugin_slug ),
	        'add_new' => __( 'Add New', $this->plugin_slug ),
	        'add_new_item' => __( 'Add New Tweetchat', $this->plugin_slug ),
	        'edit_item' => __( 'Edit Tweetchat', $this->plugin_slug ),
	        'new_item' => __( 'New Tweetchat', $this->plugin_slug ),
	        'view_item' => __( 'View Tweetchat', $this->plugin_slug ),
	        'search_items' => __( 'Search Tweetchats', $this->plugin_slug ),
	        'not_found' => __( 'No tweetchats found', $this->plugin_slug ),
	        'not_found_in_trash' => __( 'No tweetchats found in Trash', $this->plugin_slug ),
	        'parent_item_colon' => __( 'Parent Tweetchat:', $this->plugin_slug ),
	        'menu_name' => __( 'Tweetchats', $this->plugin_slug ),
	    );

	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Tweetchats sponsored by Salud America',
	        'supports' => array( 'title' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => 'salud_america',
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => true,
	        'capability_type' => $this->post_type,
	        'map_meta_cap' => true
	    );

	    register_post_type( $this->post_type, $args );

	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Add Type and Stage columns (populated from post meta).
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
			'event_date' => __( 'Chat Date' ),
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
			if ( $column == 'event_date' ) {
				$date = get_post_meta( $post_id, 'sa_tweetchat_date', true );
				echo sa_convert_to_human_date( $date );
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
					$columns['event_date'] = 'event_date';
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
				case 'event_date':
					$query->set( 'meta_key','sa_tweetchat_date' );
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
		add_meta_box( 'sa_tweetchat_meta_box', 'Tweetchat Details', array( $this, 'sa_tweetchat_meta_box' ), $this->post_type, 'normal', 'high' );   ;
	}
		function sa_tweetchat_meta_box( $post ) {
			$date = get_post_meta( $post->ID, 'sa_tweetchat_date', true );

			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_name, $this->nonce_value );
			?>
			<div>
				<h4>Tweetchat Date</h4>
				<p>
					<input type='text' name='sa_tweetchat_date' id='sa_tweetchat_date' value='<?php
						if ( ! empty( $date ) ) {
							echo sa_convert_to_human_date( $date );
						}
					 ?>'/>
				</p>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#sa_tweetchat_date").datepicker( {
						dateFormat: "MM d, yy",
					} );
				});
			</script>
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
		// Create array of fields to save
		$meta_fields_to_save = array( 'sa_tweetchat_date' );
		// Convert the end date for storage.
		if ( ! empty( $_POST[ 'sa_tweetchat_date' ] ) ) {
			$_POST[ 'sa_tweetchat_date' ] = sa_convert_to_computer_date( $_POST[ 'sa_tweetchat_date' ] );
		}

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
		// We'll only get one upcoming tweetchat for this
		$args = array(
		'post_type' => $this->post_type,
		'posts_per_page' => 1,
		'meta_key'  => 'sa_tweetchat_date',
		'orderby'   => 'meta_value_num',
		'order' => 'ASC',
		'meta_query' => array(
				            array(
				                'key' => 'sa_tweetchat_date',
				                'value' => date("Ymd"), // Set today's date
				                'compare' => '>=', // Return future events
				                'type' => 'NUMERIC,'
				                )
				            ),
		);
		$tweetchat = new WP_Query( $args );

        if ( $tweetchat->have_posts() ) {
        	while ( $tweetchat->have_posts() ):
        		$tweetchat->the_post();
        		$date = get_post_meta( get_the_ID(), 'sa_tweetchat_date', true );
        		$date = sa_convert_to_short_human_date( $date );
		 		$notices .= PHP_EOL . '<div class="sa-notice-item"><h4 class="sa-notice-title"><a href="' . sa_get_group_permalink() . 'pages/tweetchats"><span class="sa-action-phrase">Tweetchat ' . $date . ':</span>&ensp;';
		 		$notices .= get_the_title();
		 		$notices .= '</a></h4>';
		 		$notices .= '<a class="button" target="_blank" href="https://twitter.com/SaludToday">Follow the Conversation</a> <a class="button" href="' . sa_get_group_permalink() . 'pages/tweetchats">Learn More</a></div>';
			endwhile;
			wp_reset_query();
		}

		return $notices;
	}

} //End class CC_SA_Tweetchats_CPT_Tax
$sa_tweetchats_cpt_tax = new CC_SA_Tweetchats_CPT_Tax();

function sa_tweetchats_list( $numposts, $period = 'upcoming' ) {
	$numposts = is_numeric( $numposts ) ? intval( $numposts ): 4;
	$args = array(
		'post_type' => 'sa_tweetchats',
		'posts_per_page' => $numposts,
		'meta_key'  => 'sa_tweetchat_date',
		'orderby'   => 'meta_value_num',
		'order' => 'ASC',
		'meta_query' => array(
				            array(
				                'key' => 'sa_tweetchat_date',
				                'value' => date("Ymd"), // Set today's date
				                'compare' => '>=', // Return future events
				                'type' => 'NUMERIC,'
				                )
				            ),
		);
	// Not looking forward? We've got something for that.
	if ( $period == 'past' ) {
		$args[ 'order' ] = 'DESC';
		$args[ 'meta_query' ][0][ 'compare' ] = '<=';
	}
	$tweetchat = new WP_Query( $args );

    if ( $tweetchat->have_posts() ) {
    	echo '<ul class="tweetchats no-bullets">';
    	while ( $tweetchat->have_posts() ):
    		$tweetchat->the_post();
    		$date = get_post_meta( get_the_ID(), 'sa_tweetchat_date', true );
    		$date = sa_convert_to_short_human_date( $date );
    		?>
    		<li><strong><?php echo $date; ?></strong>&ensp;<?php the_title(); ?></li>
    		<?php
		endwhile;
    	echo '</ul>';
		wp_reset_query();
	}
}