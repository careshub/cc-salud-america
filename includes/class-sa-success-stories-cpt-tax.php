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
class CC_SA_Success_Stories_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_success_story_meta_box_nonce';
	private $nonce_name = 'sa_success_story_meta_box';
	private $post_type = 'sa_success_story';


	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register Salud Heroes custom post type
		add_action( 'init', array( $this, 'register_cpt_sa_success_story' ) );

		// Add submenus to handle the edit screens for our custom taxonomies
		// add_action( 'admin_menu', array( $this, 'create_taxonomy_management_menu_items' ) );
		// add_action( 'parent_file', array( $this, 'sa_tax_menu_highlighting' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// Add our templates to BuddyPress' template stack.
		// add_filter( 'manage_edit-sapolicies_columns', array( $this, 'edit_admin_columns') );
		// add_filter( 'manage_sapolicies_posts_custom_column', array( $this, 'manage_admin_columns'), 12, 2 );
		// add_filter( 'manage_edit-sapolicies_sortable_columns', array( $this, 'register_sortable_columns' ) );
		// add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		// Handle autosuggest input from this post_type's edit screen.
		add_action( 'save_post', array( $this, 'sa_related_leaders_meta_box_save' ) );

		// Add an AJAX listener for removing the related PDF via the admin meta box.
		add_action( 'wp_ajax_delete_success_story_pdf', array( $this, 'ajax_delete_success_story_pdf' ) );

		// Add action buttons, including "download PDF" button, after first paragraph
		add_filter( 'the_content', array( $this, 'insert_actions_in_success_stories' ) );

	}

	/**
	 * Define the "sa_success_story" custom post type and related taxonomies:
	 * "sa_advocacy_targets", "sa_policy_tags" and "sa_geographies".
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function register_cpt_sa_success_story() {

	    $labels = array(
	        'name' => __( 'Salud Heroes', 'sa_success_story' ),
	        'singular_name' => __( 'Salud Hero', 'sa_success_story' ),
	        'add_new' => __( 'Add New', 'sa_success_story' ),
	        'add_new_item' => __( 'Add New Salud Hero', 'sa_success_story' ),
	        'edit_item' => __( 'Edit Salud Hero', 'sa_success_story' ),
	        'new_item' => __( 'New Salud Hero', 'sa_success_story' ),
	        'view_item' => __( 'View Salud Hero', 'sa_success_story' ),
	        'search_items' => __( 'Search Salud Heros', 'sa_success_story' ),
	        'not_found' => __( 'No salud heroes found', 'sa_success_story' ),
	        'not_found_in_trash' => __( 'No salud heroes found in Trash', 'sa_success_story' ),
	        'parent_item_colon' => __( 'Parent Salud Hero:', 'sa_success_story' ),
	        'menu_name' => __( 'Salud Heroes', 'sa_success_story' ),
	    );

	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Used to highlight policies that went well and can serve as a model for change in other places.',
	        'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt' ),
	        'taxonomies' => array( 'sa_advocacy_targets' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true, //'salud_america',
	        'menu_position' => 57,
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
	public function add_meta_box() {

		add_meta_box(
			'sa_success_stories_meta_box',
			'More Details',
			array( $this, 'render_meta_box_content' ),
			$this->post_type,
			'normal',
			'high'
			);
		 add_meta_box(
		 	'sa_related_leaders_meta_box',
		 	'Associated Leaders',
		 	array( $this, 'sa_related_leaders_meta_box' ),
		 	$this->post_type,
		 	'side',
		 	'default'
		 	);
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
		// @TODO: drop the file-input-type pdf uploader for the WP media uploader.

		// Add an nonce field so we can check for it later.
		wp_nonce_field( $this->nonce_name, $this->nonce_value );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, 'sa_featured_video_url', true );

		//****ADDED BY MIKE B.*********
		$locvalue = get_post_meta( $post->ID, 'sa_success_story_location', true );
		$latvalue = get_post_meta( $post->ID, 'sa_success_story_latitude', true );
		$longvalue = get_post_meta( $post->ID, 'sa_success_story_longitude', true );

		// Display the form, using the current value.
		?>
		<label for="sa_featured_video_url" class="description"><h4>Featured video URL</h4></label>
		<input type="text" id="sa_featured_video_url" name="sa_featured_video_url" value="<?php echo esc_attr( $value); ?>" size="75" /><br />
		<em>e.g.: http://www.youtube.com/watch?v=UueU0-EFido</em>

		<!--****ADDED BY MIKE B.*********-->
		<label for="sa_success_story_location" class="description"><h4>Location</h4>
			<em>e.g.: Houston, Texas</em></label><br />
		<input type="text" id="sa_success_story_location" name="sa_success_story_location" value="<?php echo esc_attr( $locvalue); ?>" size="75" />	<input type="button" id="sa_success_story_save_location" value="Verify Location" /> <img id="sa_success_story_save_location_check" src="/wp-content/uploads/2013/12/greencheck.png" style="vertical-align:middle;" />
		<input type="hidden" id="sa_success_story_latitude" name="sa_success_story_latitude" value="<?php echo esc_attr( $latvalue); ?>" /><input type="hidden" id="sa_success_story_longitude" name="sa_success_story_longitude" value="<?php echo esc_attr( $longvalue); ?>" />

		<label for="sa_success_story_pdf" class="description"><h4>Attach the PDF version of this story</h4></label>
		<input id="sa_success_story_pdf" type="file" name="sa_success_story_pdf" value="" size="25" />
		<p class="description">
			<?php
			if( '' == get_post_meta( $post->ID, 'sa_success_story_pdf', true ) ) {
				echo 'No PDF is attached to this post.';
			} else {
				echo '<span id="attached_pdf_info">Currently attached: ' . get_post_meta( $post->ID, 'sa_success_story_pdf', true ) . ' (<a id="delete_attached_pdf">Detach this PDF</a>)</span>';
			} // end if
			?>

		</p><!-- /.description -->

		<script type="text/javascript">
			jQuery(document).ready( function( $ ) {

				"use strict";

				$(function() {

					if( 0 < $('#sa_success_story_pdf').length ) {
					  $('form').attr('enctype', 'multipart/form-data');
					} // end if

				});

				//For deleting attachments
				var data = {
					action: 'delete_success_story_pdf',
					post_attachment_to_delete: <?php echo $post->ID; ?>,
					security: '<?php echo wp_create_nonce( 'delete_attached_pdf' ); ?>'
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$("#delete_attached_pdf").click(function(evt) {
					$.post(
							ajaxurl,
							data,
							function(response) {
							// alert('Got this from the server: ' + response);
								if ( response == 1 ) {
									$("#attached_pdf_info").text("No PDF is attached to this post.");
								}
							}
						);
				});


				//*******ADDED BY MIKE B.****************
				$("#sa_success_story_save_location_check").hide();
				$("#sa_success_story_save_location").click(function() {
					var geogterm = jQuery("#sa_success_story_location").val();
					var dataString = 'geogstr=' + geogterm;

					 $.ajax
						 ({
						   type: "POST",
						   url: "http://www.communitycommons.org/wp-content/themes/CommonsRetheme/ajax/getlatlong.php",
						   data: dataString,
						   cache: false,
						   error: function() {
							 alert("Could not compute a latitude/longitude for this location. Please modify your location.");
						   },
						   success: function(k)
						   {
							 //alert(k);
							 var coord = $.parseJSON(k);
							 $("#sa_success_story_latitude").val(coord.latitude);
							 $("#sa_success_story_longitude").val(coord.longitude);
							 $("#sa_success_story_save_location_check").show();
						   }
						 });
				});


			});
		</script>
<?php
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		if ( get_post_type( $post_id ) != $this->post_type ) {
			return;
		}

		// First, make sure the user can save the post
		if ( ! $this->user_can_save( $post_id, $this->nonce_value, $this->nonce_name  ) ) {
			return false;
		}

		// Sanitize the user input.
		$video_url = sanitize_text_field( $_POST['sa_featured_video_url'] );

		// Update the meta field.
		if ( ! empty( $video_url ) ) {
			update_post_meta( $post_id, 'sa_featured_video_url', $video_url );
		} else {
			delete_post_meta( $post_id, 'sa_featured_video_url' );
		}
		update_post_meta( $post_id, 'sa_success_story_location', $_POST['sa_success_story_location'] );
		update_post_meta( $post_id, 'sa_success_story_latitude', $_POST['sa_success_story_latitude'] );
		update_post_meta( $post_id, 'sa_success_story_longitude', $_POST['sa_success_story_longitude'] );

		// Saving the uploaded PDF
		// If the user uploaded an image, let's upload it to the server
		// $_FILES isn't enough, we need to see if there's something specific set, like the name
		// @TODO: Use the WP Media Uploader for this..
		if ( ! empty( $_FILES['sa_success_story_pdf']['name']) ) {

			// Use the WordPress API to upload the file
            $upload = wp_upload_bits( $_FILES['sa_success_story_pdf']['name'], null, file_get_contents( $_FILES['sa_success_story_pdf']['tmp_name'] ) );

            if( isset( $upload['error'] ) && $upload['error'] != 0 ) {
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
            } else if ( ! empty( $upload['url'] ) ) {
            	//Only record the meta if it isn't empty
                update_post_meta( $post_id, 'sa_success_story_pdf', $upload['url'] );
            } // end if/else

		} // end if
	}

	public function ajax_delete_success_story_pdf() {
		global $wpdb; // this is how you get access to the database

		if( wp_verify_nonce( $_REQUEST['security'], 'delete_attached_pdf' ) ) {

		$post_attachment_to_delete = intval( $_POST['post_attachment_to_delete'] );

		$success = delete_post_meta( $post_attachment_to_delete, 'umb_file' );

		die( $success ); // this is required to return a proper result

		} else {
			die('-1');
		}
	}
	//Insert ads after lead in paragraph of single success story.
	function insert_actions_in_success_stories( $content ) {

		if ( ! is_admin() && sa_is_single_post() ) {
			global $post;
			if ( $post->post_type == $this->post_type ) {
				$insertion = '<p>';
				$pdf_url = get_post_meta( $post->ID, 'sa_success_story_pdf', true );
				if ( $pdf_url ) {
					$insertion .= '<a href="' . $pdf_url . '" class="button">Download the PDF</a> <a class="button add-comment-link" href="#respond"><span class="comment-icon"></span>Comment</a> ';
				}
				if ( function_exists( 'bp_get_share_post_button' ) ) {
					$insertion .= bp_get_share_post_button();
				}
				$insertion .= '</p>';
				$content = sa_insert_random_content_after_paragraph( $insertion, 1, $content );
			}
		}

		return $content;
	}

} //End class CC_SA_Policies_CPT_Tax
$sa_success_stories_cpt_tax = new CC_SA_Success_Stories_CPT_Tax();

// Parent Function that makes the magic happen
function sa_insert_random_content_after_paragraph( $insertion, $paragraph_id, $content ) {
	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );
	foreach ($paragraphs as $index => $paragraph) {

		if ( trim( $paragraph ) ) {
			$paragraphs[$index] .= $closing_p;
		}

		if ( $paragraph_id == $index + 1 ) {
			$paragraphs[$index] .= $insertion;
		}
	}

	return implode( '', $paragraphs );
}

function sa_get_youtube_video_metadata( $url ) {

	//Get the important part of the $video_url
	// URLs can take the form 'http://youtu.be/WZE-VHRtau8', 'https://www.youtube.com/watch?v=e5yNGuE9qzY' OR 'https://www.youtube.com/watch?v=e5yNGuE9qzY&feature=embedded', so we've got to handle some cases
	if ( stripos( $url, 'www.youtube.com' )  ) {

		$parsed = parse_url($url);
		$args = explode( '&', $parsed['query'] );
		foreach ( $args as $piece ) {
			if ( stripos( $piece, 'v=') !== false ) {
				//Remove the leading 'v='
				$guts = substr( $piece, 2 );
			}
		}

	} else if ( stripos( $url, 'youtu.be' ) ) {

		$parsed = parse_url($url);
		// Remove the leading slash
		$guts = substr( $parsed['path'], 1);
	} else {
		return false;
	}

	$json_output = file_get_contents( "http://gdata.youtube.com/feeds/api/videos/{$guts}?v=2&alt=json" );
	$json = json_decode($json_output, true);
	// print_r($json);

	//This gives you the video description
	$video_description = $json['entry']['media$group']['media$description']['$t'];

	//This gives you the video views count
	$view_count = $json['entry']['yt$statistics']['viewCount'];

	//This gives you the video title
	$video_title = $json['entry']['title']['$t'];

	return array( 	'title' => $video_title,
					'description' => $video_description,
					'count' => $view_count
					);
}

function sa_get_random_hero_video() {
	$args = array(
		'post_type' 			=> 'sa_success_story',
		'orderby'               => 'rand',
		'posts_per_page'         => 1,

		//Custom Field Parameters
		'meta_query'     => array(
			array(
				'key' => 'sa_featured_video_url',
				'compare' => 'EXISTS'
			),
		),

	);

	$video_story = new WP_Query( $args );
	// print_r($video_story);

	// Use alternate syntax (using the_post() object messes up the outer WP_Query loop because wp_reset_postdata in this case resets the postdata to the archive page's real job, not the page intro secondary loop.
	foreach ( $video_story->posts as $video ) {

		$video_url = get_post_meta( $video->ID, 'sa_featured_video_url', 'true' );

		if ( ! empty( $video_url ) ) {
			$video_embed_code = wp_oembed_get( $video_url );
		}

		if ( $video_embed_code ) { ?>
			<div class="video-container-group video-right">
				<figure class="video-container">
					<?php echo $video_embed_code; ?>
				</figure>
				<?php if ( sa_is_section_front( 'heroes' ) ) { ?>
					<a href="<?php sa_get_section_permalink( 'heroes' ); ?>?style=videos" title="link to the Salud Heroes video archive" class="button">Watch all videos</a>
				<?php } else { ?>
					<figcaption>See how these <a href="/sa_success_story/">Salud Heroes</a> are fighting Latino obesity…and learn how easy it is to be a Salud Hero, too!</figcaption>
				<?php } ?>
			</div>
			<?php } // End if $video_embed_code
	} // End foreach
}
