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
class CC_SA_Resources_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_resource_meta_box_nonce';
	private $nonce_name = 'sa_resource_meta_box';
	private $post_type = 'saresources';

	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register Policy custom post type
		add_action( 'init', array( $this, 'register_resources_cpt' ) );

		// Register related taxonomies
		add_action( 'init', array( $this, 'register_resource_types_taxonomy' ) );
		add_action( 'init', array( $this, 'register_resource_cats_taxonomy' ) );

		// Add submenus to handle the edit screens for our custom taxonomies
		add_action( 'admin_menu', array( $this, 'create_taxonomy_management_menu_items' ) );
		add_action( 'parent_file', array( $this, 'sa_tax_menu_highlighting' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// Add our templates to BuddyPress' template stack.
		// add_filter( 'manage_edit-sapolicies_columns', array( $this, 'edit_admin_columns') );
		// add_filter( 'manage_sapolicies_posts_custom_column', array( $this, 'manage_admin_columns') );
		// add_filter( 'manage_edit-sapolicies_sortable_columns', array( $this, 'register_sortable_columns' ) );
		// add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );

	}

	/**
	 * Define the "sa_policies" custom post type and related taxonomies:
	 * "sa_advocacy_targets", "sa_policy_tags" and "sa_geographies".
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function register_resources_cpt() {

		$resource_labels = array(
			'name' => __('Resources', $this->plugin_slug ),
			'singular_name' => __('Resource', $this->plugin_slug ),
			// 'all_items' => __('All Resources', $this->plugin_slug ),
			'add_new' => __('Add Resource', $this->plugin_slug ),
			'add_new_item' => __('Add New Resource', $this->plugin_slug ),
			'edit_item' => __('Edit Resource', $this->plugin_slug ),
			'new_item' => __('New Resource', $this->plugin_slug ),
			'view_item' => __('View Resource', $this->plugin_slug ),
			'search_items' => __('Search in Resources', $this->plugin_slug ),
			'not_found' =>  __('No Resources found', $this->plugin_slug ),
			'not_found_in_trash' => __('No resources found in trash', $this->plugin_slug ),
			'parent_item_colon' => __( 'Parent Resource:', $this->plugin_slug ),
	        'menu_name' => __( 'Salud Resources', $this->plugin_slug ),

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
			'menu_position' => 56,
			'taxonomies' => array('sa_advocacy_targets', 'sa_resource_cat', 'sa_resource_type'),
			// 'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes',),
			'supports' => array('title','editor','comments', 'thumbnail'),
			'has_archive' => true,
			'capability_type' => $this->post_type,
			'map_meta_cap' => true
		);

		register_post_type( $this->post_type, $args );
	}

	public function register_resource_types_taxonomy() {
		// Add new "Resource Type" taxonomy to Salud America Resources

		$labels = array(
				'name' => __( 'Resource Type', $this->plugin_slug  ),
				'singular_name' => __( 'Resource Type', $this->plugin_slug  ),
				'search_items' =>  __( 'Search Resource Types', $this->plugin_slug ),
				'all_items' => __( 'All Resource Types', $this->plugin_slug ),
				'parent_item' => __( 'Parent Resource Types', $this->plugin_slug ),
				'parent_item_colon' => __( 'Parent Resource Type:', $this->plugin_slug ),
				'edit_item' => __( 'Edit Resource Type', $this->plugin_slug ),
				'update_item' => __( 'Update Resource Type', $this->plugin_slug ),
				'add_new_item' => __( 'Add New Resource Type', $this->plugin_slug ),
				'new_item_name' => __( 'New Resource Type Name', $this->plugin_slug ),
				'menu_name' => __( 'Resource Types', $this->plugin_slug )
			);


		$args = array(
			'labels' => $labels,
			'query_var' => true,
			'rewrite' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'capabilities' => array(
							'manage_terms' => 'edit_saresources',
							'delete_terms' => 'edit_saresources',
							'edit_terms' => 'edit_saresources',
							'assign_terms' => 'edit_saresources'
						)
		);

		register_taxonomy( 'sa_resource_type', array( $this->post_type ), $args );
	}

	public function register_resource_cats_taxonomy() {
		// Add new "Resource Category" taxonomy to Salud America Resources
		$labels = array(
			'name' => __( 'Resource Category', $this->plugin_slug  ),
			'singular_name' => __( 'Resource Category', $this->plugin_slug  ),
			'search_items' =>  __( 'Search Resource Categories', $this->plugin_slug  ),
			'all_items' => __( 'All Resource Categories', $this->plugin_slug  ),
			'parent_item' => __( 'Parent Resource Categories', $this->plugin_slug  ),
			'parent_item_colon' => __( 'Parent Resource Category:', $this->plugin_slug  ),
			'edit_item' => __( 'Edit Resource Category', $this->plugin_slug  ),
			'update_item' => __( 'Update Resource Category', $this->plugin_slug  ),
			'add_new_item' => __( 'Add New Resource Category', $this->plugin_slug  ),
			'new_item_name' => __( 'New Resource Category Name', $this->plugin_slug  ),
			'menu_name' => __( 'Resource Categories', $this->plugin_slug  )
		);

		$args = array(
			'labels' => $labels,
			'query_var' => true,
			'rewrite' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'capabilities' => array(
							'manage_terms' => 'edit_saresources',
							'delete_terms' => 'edit_saresources',
							'edit_terms' => 'edit_saresources',
							'assign_terms' => 'edit_saresources'
						)
		);

		register_taxonomy( 'sa_resource_cat', array( $this->post_type ), $args );
	}
	/**
	 * Manage wp-admin behavior/appearance of CPT and taxonomy menus.
	 * - Add submenus to handle the edit screens for our custom taxonomies.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function create_taxonomy_management_menu_items() {
		add_submenu_page( 'salud_america', 'Resource Categories', 'Resource Categories', 'edit_others_posts', 'edit-tags.php?taxonomy=sa_resource_cat' );
		add_submenu_page( 'salud_america', 'Resource Types', 'Resource Types', 'edit_others_posts', 'edit-tags.php?taxonomy=sa_resource_type' );
	}

	/**
	 * Manage wp-admin behavior/appearance of CPT and taxonomy menus.
	 * - Make sure that the "Salud America" menu item is highlighted when viewing a taxonomy.
	 * - Default behavior highlights the "Posts" menu item.
	 *
	 * @since    1.0.0
	 *
	 * @return   string   id of menu item that should be highlighted
	 */
	public function sa_tax_menu_highlighting( $parent_file ) {
		global $current_screen;
		$sa_taxes = array( 'sa_resource_type', 'sa_resource_cat' );
		if ( in_array( $current_screen->taxonomy, $sa_taxes ) ) {
			$parent_file = 'salud_america';
		}
		return $parent_file;
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
	 * @since    1.0.0
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
	 * @since    1.0.0
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
	 * - Add meta boxes for policy meta and geography.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	//Building the input form in the WordPress admin area
	function add_meta_box() {
		add_meta_box( 'sa_resource_meta_box', 'Resource Information (optional)', array( $this, 'sa_resource_meta_box' ), $this->post_type, 'normal', 'high' );   ;
	}
		function sa_resource_meta_box( $post ) {
			$custom = get_post_custom( $post->ID );
			$saresource_date = isset( $custom["saresource_date"][0] ) ? $custom["saresource_date"][0] : '';
			$saresource_policy = isset( $custom["saresource_policy"][0] ) ? $custom["saresource_policy"][0] : '';
			$saresource_promote = isset( $custom["saresource_promote"][0] ) ? $custom["saresource_promote"][0] : '';
			$featured_video_url = isset( $custom["featured_video_url"][0] ) ? $custom["featured_video_url"][0] : '';

			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_name, $this->nonce_value );
			?>
			<?php // This no longer makes sense because the Resources Archive now displays all resources in order of recency.
			/* ?>
			<p><input type="checkbox" id="saresource_promote" name="saresource_promote" <?php checked( $saresource_promote, 'on' ); ?> > <label for="saresource_promote">Promote to Resources <em>(visible independent of related policies)</em></label></input></p>
			<?php */ ?>

			<label for="saresource_date"><h4>Source Date</h4></label>
				<input type='text' name='saresource_date' id='saresource_date' value='<?php
					if ( $saresource_date ) {
						echo $saresource_date;
					}
				 ?>'/>

			<label for="sa_featured_video_url" class="description"><h4>Featured video URL</h4>
			</label>
			<input type="text" id="sa_featured_video_url" name="sa_featured_video_url" value="<?php echo esc_attr( $featured_video_url ); ?>" size="75" /><br />
			<em>e.g.: http://www.youtube.com/watch?v=UueU0-EFido</em>
			<?php
			//@TODO: This is broken and not being used. Disabling for now.
			/*
			$seltext = "";
			$selval = "";
			if ( is_null( $saresource_policy ) ){
				$seltext = "---Select a Policy---";
				$selval = "";
			} else {
				$seltext = $saresource_policy;
				$selval = $saresource_policy;
			}

			$args = array(
				'post_type' => 'sapolicies',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'caller_get_posts'=> 1,
				'orderby' => 'title',
				'order' => 'ASC'
			);
			$related_policies = new WP_Query( $args );
			?>

			<strong>Policy</strong><br>
				<select name='saresource_policy' id='saresource_policy'>
					<option selected="true" value="<?php echo $selval; ?>"><?php echo $seltext; ?></option>
				<?php
				if ( $related_policies->have_posts() ) {
					while ( $related_policies->have_posts() ) : $related_policies->the_post(); ?>
						<option value='<?php the_title(); ?>'><?php the_title(); ?></option>
					<?php
					endwhile;
				}
				wp_reset_query();
				?>

				</select>

				<script type="text/javascript">
					jQuery(document).ready(function(){
							jQuery("#saresource_date").datepicker();
					});
				</script>

				<?php
				*/
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

		// Save meta
		$meta_fields = array( "saresource_date", "saresource_policy", "saresource_promote", 'sa_featured_video_url' );
		$meta_success = $this->save_meta_fields( $post_id, $meta_fields );

	}

} //End class CC_SA_Resources_CPT_Tax
$sa_resources_cpt_tax = new CC_SA_Resources_CPT_Tax();

/**
 * Output resources search form and build search results.
 *
 * @since   1.0.0
 *
 * @return  html
 */
function sa_searchresources() {
?>
	<div id="cc-adv-search" class="clear">
		<form action="<?php echo sa_get_section_permalink( $section = 'resources' ) . 'search'; ?>" method="POST" enctype="multipart/form-data" name="sa_ps">
			<div class="row">
		        <input type="text" id="saps" name="keyword" Placeholder="Enter search terms here" value="<?php
	    			if ( isset( $_REQUEST['keyword'] ) ) {
						echo $_REQUEST['keyword'];
					} elseif ( isset($_GET['qs'] ) ) {
						echo $_GET['qs'];
					}
	    		?>" />
		    	<!-- Hidden input to set post type for search-->
			    <input type="hidden" name="requested_content" value="saresources" />
	  			<input id="searchsubmit" type="submit" alt="Search" value="Search" />
			</div>

			<a role="button" id="cc_advanced_search_toggle" class="clear" >+ Advanced Search</a>
			<div id="cc-adv-search-pane-container" class="row clear">
		        <div class="cc-adv-search-option-pane third-block">
					<h4>Topic Area</h4>
					<ul style="list-style-type: none;">
						<?php
						$ATterms = get_terms('sa_advocacy_targets');
						foreach ($ATterms as $ATterm) {
						  echo '<li><input type="checkbox" name="sa_advocacy_targets[]" id="sa_advocacy_targets_' . $ATterm->term_id . '" value="' . $ATterm->term_id . '" /> <label for="sa_advocacy_targets_' . $ATterm->term_id . '">' . $ATterm->name . '</label></li>';
						}
						?>
					</ul>
		        </div> <!-- End option pane -->

				<div class="cc-adv-search-option-pane third-block">
					<h4>Type of Resource</h4>
					<div class="cc-adv-search-scroll-container">
						<ul style="list-style-type: none;">
							<?php
							$CATterms = get_terms('sa_resource_cat');
							foreach ($CATterms as $CATterm) {
							  echo '<li><input type="checkbox" name="sa_resource_cat[]" id="sa_resource_cat_' . $CATterm->term_id . '" value="' . $CATterm->term_id . '" /> <label for="sa_resource_cat_' . $CATterm->term_id . '">' . $CATterm->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div> <!-- End option pane -->

				<div class="cc-adv-search-option-pane third-block">
					<h4>Tags</h4>
					<?php $sat_args = array('orderby' => count, 'order' => DESC);
					$sapolicytags = get_terms('sa_policy_tags', $sat_args);
					?>
					<div class="cc-adv-search-scroll-container">
						<ul style="list-style-type: none;">
							<?php
							foreach ($sapolicytags as $sapolicytag) {
							  echo '<li><input type="checkbox" name="sa_policy_tags[]" id="sa_policy_tags_' .  $sapolicytag->term_id . '" value="' . $sapolicytag->term_id . '" /> <label for="sa_policy_tags_' . $sapolicytag->term_id . '">' . $sapolicytag->name . ' (' . $sapolicytag->count . ')</label></li>';
							}
							?>
						</ul>
					</div> <!-- End scroll container -->
		        </div> <!-- End option pane -->
		    </div>
		</form>
	</div>
	<script type="text/javascript">
		var $j = jQuery.noConflict();

		$j(document).ready(function(){
		   $j('#cc-adv-search-pane-container').hide();
		   $j('#cc_advanced_search_toggle').click(function(){
				$j('#cc-adv-search-pane-container').slideToggle('fast');
				if ($j("#cc_advanced_search_toggle").text() == "+ Advanced Search") {
					$j("#cc_advanced_search_toggle").text("- Advanced Search");
				}
				else {
					$j("#cc_advanced_search_toggle").text("+ Advanced Search");
				}
		   });
		});
	</script>

	<?php
	if ( sa_is_archive_search() ) {
		$post_type = 'saresources';
		$taxonomies = array( 'sa_advocacy_targets', 'sa_policy_tags', 'sa_resource_cat' );
		$metas = array();
		$filter_args = sa_build_search_query( $post_type, $taxonomies, $metas );

	    //Make the query, do the loop
		$resource_search = new WP_Query( $filter_args );
		if ( $resource_search->have_posts() ) {
			echo '<div class="row">';
			echo '<h3 class="screamer sapurple">Search Results</h3>';
			while ( $resource_search->have_posts()) : $resource_search->the_post();
				bp_get_template_part( 'groups/single/saresources/resource-short' );
			endwhile;
			echo '</div>';
			sa_section_content_nav( 'nav-below', $resource_search->max_num_pages );
		} else {
			echo "No Results - Search criteria too specific";
		}
	}

}