<?php
/**
 * The file that defines the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @link       http://example.com
 * @since      1.6.0
 *
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 */

/**
 * Define the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @since      1.6.0
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 * @author     Your Name <email@example.com>
 */
class CC_SA_Ticker_Items_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_ticker_items_meta_box_nonce';
	private $nonce_name = 'sa_ticker_items_meta_box';
	private $post_type = 'sa_ticker_items';
	private $tax_name = 'sa_ticker_item_types';

	/**
	 * Initialize the extension class
	 *
	 * @since     1.6.0
	 */
	public function __construct() {

		// Register Policy custom post type
		add_action( 'init', array( $this, 'register_ticker_items_cpt' ) );

		// Register related taxonomies
		add_action( 'init', array( $this, 'register_ticker_item_types_tax' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// add_filter( 'manage_edit-sapolicies_columns', array( $this, 'edit_admin_columns') );
		// add_filter( 'manage_sapolicies_posts_custom_column', array( $this, 'manage_admin_columns') );
		// add_filter( 'manage_edit-sapolicies_sortable_columns', array( $this, 'register_sortable_columns' ) );
		// add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );


		// Change the REST API response so that it includes important meta for ticker items.
		add_action( 'rest_api_init', array( $this, 'rest_read_meta' ) );
	}

	/**
	 * Define the "sa_policies" custom post type and related taxonomies:
	 * "sa_advocacy_targets", "sa_policy_tags" and "sa_geographies".
	 *
	 * @since    1.6.0
	 *
	 * @return   void
	 */
	public function register_ticker_items_cpt() {

		$resource_labels = array(
			'name' => __('Ticker Items', $this->plugin_slug ),
			'singular_name' => __('Ticker Item', $this->plugin_slug ),
			// 'all_items' => __('All Resources', $this->plugin_slug ),
			'add_new' => __('Add Ticker Item', $this->plugin_slug ),
			'add_new_item' => __('Add Ticker Item', $this->plugin_slug ),
			'edit_item' => __('Edit Ticker Item', $this->plugin_slug ),
			'new_item' => __('New Ticker Item', $this->plugin_slug ),
			'view_item' => __('View Ticker Item', $this->plugin_slug ),
			'search_items' => __('Search in Ticker Items', $this->plugin_slug ),
			'not_found' =>  __('No Ticker Items found', $this->plugin_slug ),
			'not_found_in_trash' => __('No ticker items found in trash', $this->plugin_slug ),
			'parent_item_colon' => __( 'Parent Ticker Item:', $this->plugin_slug ),
	        'menu_name' => __( 'Salud Ticker Items', $this->plugin_slug ),

		);
		$args = array(
			'labels' => $resource_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'hierarchical' => false,
			'show_in_menu' => true,//'salud_america',
			'menu_position' => 58,
			'taxonomies' => array( 'sa_ticker_item_type' ),
			// 'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes',),
			'supports' => array('title'),
			'show_in_rest' => true,
			'has_archive' => true,
			'capability_type' => $this->post_type,
			'map_meta_cap' => true
		);

		register_post_type( $this->post_type, $args );
	}

	public function register_ticker_item_types_tax() {

		$labels = array(
				'name' => __( 'Ticker Item Types', $this->plugin_slug ),
				'singular_name' => __( 'Ticker Item Type', $this->plugin_slug  ),
				'search_items' => __( 'Search Ticker Item Types', $this->plugin_slug ),
				'popular_items' => __( 'Popular Ticker Item Types', $this->plugin_slug ),
				'all_items' => __( 'All Ticker Item Types', $this->plugin_slug ),
				'parent_item' => __( 'Parent Ticker Item Type', $this->plugin_slug ),
				'parent_item_colon' => __( 'Parent Ticker Item Type:', $this->plugin_slug ),
				'edit_item' => __( 'Edit Ticker Item Type', $this->plugin_slug ),
				'update_item' => __( 'Update Ticker Item Type', $this->plugin_slug ),
				'add_new_item' => __( 'Add New Ticker Item Type', $this->plugin_slug ),
				'new_item_name' => __( 'New Ticker Item Type', $this->plugin_slug ),
				'separate_items_with_commas' => __( 'Separate types with commas', $this->plugin_slug ),
				'add_or_remove_items' => __( 'Add or remove Ticker Item Types', $this->plugin_slug ),
				'choose_from_most_used' => __( 'Choose from most used Ticker Item Types', $this->plugin_slug ),
				'menu_name' => __( 'Ticker Item Types', $this->plugin_slug ),
		);

		$args = array(
				'labels' => $labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'capabilities' => array(
					'manage_terms' => 'edit_sapoliciess',
					'delete_terms' => 'edit_sapoliciess',
					'edit_terms' => 'edit_sapoliciess',
					'assign_terms' => 'edit_sapoliciess'
				),
				'show_tagcloud' => true,
				'show_admin_column' => true,
				'hierarchical' => true,

				'rewrite' => true,
				// 'rewrite' => array( 'slug' => 'salud/sa_advocacy_targets', 'with_front' => false),
				'query_var' => true
		);

		register_taxonomy( $this->tax_name, array( $this->post_type ), $args );
	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Add Type and Stage columns (populated from post meta).
	 *
	 * @since    1.6.0
	 *
	 * @return   array of columns to display
	 */
	public function edit_admin_columns( $columns ) {
		// Last two columns are always Comments and Date.
		// We want to insert our new columns just before those.
		$entries = count( $columns );
		$opening_set = array_slice( $columns, 0, $entries - 2 );
		$closing_set = array_slice( $columns, - 2 );

		$insert_set = array(
			'type' => __( 'Type' ),
			'stage' => __( 'Stage' )
			);

		$columns = array_merge( $opening_set, $insert_set, $closing_set );

		return $columns;
	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Handle Output for Type and Stage columns (populated from post meta).
	 *
	 * @since    1.6.0
	 *
	 * @return   string content of custom columns
	 */
	public function manage_admin_columns( $column, $post_id ) {
			switch( $column ) {
				case 'type' :
					// These are all title case.
					$type = get_post_meta( $post_id, 'sa_policytype', true );
					echo $type;
				break;
				case 'stage' :
					// These are all lowercase.
					$stage = get_post_meta( $post_id, 'sa_policystage', true );
					echo ucfirst( $stage );
				break;
			}
	}

	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Add sortability to Type and Stage columns.
	 *
	 * @since    1.6.0
	 *
	 * @return   array of columns to display
	 */
	public function register_sortable_columns( $columns ) {
					$columns["type"] = "type";
					$columns["stage"] = "stage";
					//Note: Advo targets can't be sortable, because the value is a string.
					return $columns;
	}
	/**
	 * Change behavior of the SA Policies overview table by adding taxonomies and custom columns.
	 * - Define sorting query for Type and Stage columns.
	 *
	 * @since    1.6.0
	 *
	 * @return   alters $query variable by reference
	 */
	function sortable_columns_orderby( $query ) {
			if ( ! is_admin() ) {
				return;
			}

			$orderby = $query->get( 'orderby');

			switch ( $orderby ) {
				case 'stage':
						$query->set( 'meta_key','sa_policystage' );
						$query->set( 'orderby','meta_value' );
					break;
				case 'type':
						$query->set( 'meta_key','sa_policytype' );
						$query->set( 'orderby','meta_value' );
					break;
			}
	}

	/**
	 * Modify the SA Policies edit screen.
	 * - Add meta box for item hyperlink.
	 *
	 * @since    1.6.0
	 *
	 * @return   void
	 */
	//Building the input form in the WordPress admin area
	function add_meta_box() {
		add_meta_box( 'sa_ticker_item_meta_box', 'Ticker Item Information', array( $this, 'meta_box_html' ), $this->post_type, 'normal', 'high' );   ;
	}
		function meta_box_html( $post ) {
			$custom = get_post_custom( $post->ID );
			$sa_ticker_item_link = ( ! empty( $custom["sa_ticker_item_link"][0] ) ) ? $custom["sa_ticker_item_link"][0] : '';
			// $saresource_policy = $custom["saresource_policy"][0];
			// $saresource_promote = $custom["saresource_promote"][0];

			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_name, $this->nonce_value );
			?>
			<label for="sa_ticker_item_link"><h4>Link (optional)</h4></label>
				<input type='text' name='sa_ticker_item_link' id='sa_ticker_item_link' value='<?php	echo $sa_ticker_item_link; ?>'/>
			<?php
			}

	/**
	 * Save resources extra meta.
	 *
	 * @since    1.6.0
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

		// Save meta
		$meta_fields = array( 'sa_ticker_item_link' );
		$meta_success = $this->save_meta_fields( $post_id, $meta_fields );

	}

	/**
	 * Change the REST API response so that it includes important meta for ticker items.
	 *
	 * @since    1.6.0
	 *
	 * @return   void
	 */
	public function rest_read_meta() {
	    register_rest_field( $this->post_type,
	        'sa_ticker_item_link',
	        array(
	            'get_callback'    => array( $this, 'get_ticker_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( $this->post_type,
	        'item_type_term',
	        array(
	            'get_callback'    => array( $this, 'get_ticker_item_type_term' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( $this->post_type,
	        'nice_date',
	        array(
	            'get_callback'    => array( $this, 'get_short_human_date' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	}

	/**
	 * Get the value of the requested meta field.
	 *
	 * @param array $object Details of current post.
	 * @param string $field_name Name of field.
	 * @param WP_REST_Request $request Current request
	 *
	 * @return mixed
	 */
	public function get_ticker_meta( $object, $field_name, $request ) {
	    return get_post_meta( $object[ 'id' ], $field_name, true );
	}

	/**
	 * Get the value of the requested meta field.
	 *
	 * @param array $object Details of current post.
	 * @param string $field_name Name of field.
	 * @param WP_REST_Request $request Current request
	 *
	 * @return mixed
	 */
	public function get_ticker_item_type_term( $object, $field_name, $request ) {
		// Set a default value.
		$value = array(
				'term_id' => 0,
	            'name' => 'The Latest',
	            'slug' => '',
	            'color' => '#0088CF',
	        );
		$terms = get_the_terms( $object[ 'id' ], $this->tax_name );
		if ( is_array( $terms ) ) {
			$term = current( $terms );
			$value = array(
				'term_id' => $term->term_id,
	            'name' => $term->name,
	            'slug' => $term->slug,
	        );
	        // Add the term color to the response.
			if ( $color = get_term_meta( $term->term_id, 'color', true ) ) {
				$value['color'] = $color;
			}
		}
	    return $value;
	}

	/**
	 * Get the value of the requested meta field for a post's term.
	 *
	 * @param array $object Details of current post.
	 * @param string $field_name Name of field.
	 * @param WP_REST_Request $request Current request
	 *
	 * @return mixed
	 */
	public function get_ticker_term_meta( $object, $field_name, $request ) {
		// Set a default value.
		$value = '';
		$terms = get_the_terms( $object[ 'id' ], $this->tax_name );
		if ( is_array( $terms ) ) {
			$term_id = current( $terms )->term_id;
			$value = get_term_meta( $term_id, $field_name, true );
		}
	    return $value;
	}

	/**
	 * Get the value of the requested meta field for a post's term.
	 *
	 * @param array $object Details of current post.
	 * @param string $field_name Name of field.
	 * @param WP_REST_Request $request Current request
	 *
	 * @return mixed
	 */
	public function get_short_human_date( $object, $field_name, $request ) {
	    return get_the_date( 'M j', $object[ 'id' ] );
	}

} //End class CC_SA_Resources_CPT_Tax
$sa_ticker_items_cpt_tax = new CC_SA_Ticker_Items_CPT_Tax();