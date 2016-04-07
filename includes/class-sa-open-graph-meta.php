<?php
/**
 * The file that contains the functionality to work with CC Open Graph Meta.
 *
 *
 * @link       http://example.com
 * @since      1.7.0
 *
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 */

/**
 * Define the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @since      1.7.0
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 * @author     Your Name <email@example.com>
 */
class CC_SA_Open_Graph_Meta extends CC_Salud_America {

	/**
	 * Initialize the extension class
	 *
	 * @since     1.7.0
	 */
	public function __construct() {

		add_filter( 'cc_open_graph_is_single', array( $this, 'filter_og_is_single' ) );
		add_filter( 'cc_open_graph_post_id', array( $this, 'filter_og_post_id' ) );

	}

	/**
	 * Identify the page as an article when a change, resource, hero, or petition is viewed.
	 *
	 * @since    1.7.0
	 *
	 * @return   html
	 */
	public function filter_og_is_single( $is_single ) {
		if ( ! $is_single && sa_is_single_post() ) {
			$is_single = true;
		}

		return $is_single;
	}

	/**
	 * Pass the current post ID to the open graph plugin, which will use it to
	 * populate the title, content and image attributes.
	 *
	 * @since    1.7.0
	 *
	 * @return   html
	 */
	public function filter_og_post_id( $post_id ) {

		if ( ! $post_id && sa_is_single_post() ) {
	        $items = new WP_Query( sa_get_query() );
	        $total_pages = $items->max_num_pages;
	        if ( $items->have_posts() ) :
	            while ( $items->have_posts() ) : $items->the_post();
	                global $post;
	                $post_id = $post->ID;
	                break;
	            endwhile;
	        endif;
		}

		return $post_id;
	}

} //End class CC_SA_Tweetchats_CPT_Tax
$sa_open_graph_meta = new CC_SA_Open_Graph_Meta();
