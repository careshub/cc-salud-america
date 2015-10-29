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
class CC_SA_Video_Contests_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_video_contest_meta_box_nonce';
	private $nonce_name = 'sa_video_contest_meta_box';
	public $post_type = 'sa_video_contest';

	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register Policy custom post type
		add_action( 'init', array( $this, 'register_cpt_sa_video_contest' ), 17 );

		// Register related taxonomies
		// add_action( 'init', array( $this, 'register_resource_types_taxonomy' ) );
		// add_action( 'init', array( $this, 'register_resource_cats_taxonomy' ) );

		// Add submenus to handle the edit screens for our custom taxonomies
		// add_action( 'admin_menu', array( $this, 'create_taxonomy_management_menu_items' ) );
		// add_action( 'parent_file', array( $this, 'sa_tax_menu_highlighting' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

		// Add our templates to BuddyPress' template stack.
		// add_filter( 'manage_edit-sapolicies_columns', array( $this, 'edit_admin_columns') );
		// add_filter( 'manage_sapolicies_posts_custom_column', array( $this, 'manage_admin_columns') );
		// add_filter( 'manage_edit-sapolicies_sortable_columns', array( $this, 'register_sortable_columns' ) );
		// add_action( 'pre_get_posts', array( $this, 'sortable_columns_orderby' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );

		add_action( 'bp_init', array( $this, 'capture_vote_submission'), 78 );
		add_action( 'bp_init', array( $this, 'capture_join_group_submission'), 78 );

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
	public function register_cpt_sa_video_contest() {

	    $labels = array(
	        'name' => __( 'Video Contests', $this->plugin_slug ),
	        'singular_name' => __( 'Video Contest', $this->plugin_slug ),
	        'add_new' => __( 'Add New', $this->plugin_slug ),
	        'add_new_item' => __( 'Add New Video Contest', $this->plugin_slug ),
	        'edit_item' => __( 'Edit Video Contest', $this->plugin_slug ),
	        'new_item' => __( 'New Video Contest', $this->plugin_slug ),
	        'view_item' => __( 'View Video Contest', $this->plugin_slug ),
	        'search_items' => __( 'Search Video Contests', $this->plugin_slug ),
	        'not_found' => __( 'No video contests found', $this->plugin_slug ),
	        'not_found_in_trash' => __( 'No video contests found in Trash', $this->plugin_slug ),
	        'parent_item_colon' => __( 'Parent Video Contest:', $this->plugin_slug ),
	        'menu_name' => __( 'SA Video Contests', $this->plugin_slug ),
	    );

	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Current and past video contests held in the Salud America group.',
	        'supports' => array( 'title', 'editor', 'author', 'comments', 'thumbnail' ),
	        'taxonomies' => array( 'sa_advocacy_targets', 'sa_policy_tags' ),
	        'public' => true,
	        'show_ui' => true,
	        // 'show_in_nav_menus' => true,
	        'show_in_menu' => true, //'salud_america',
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
	//Building the input form in the WordPress admin area
	function add_meta_box() {
		add_meta_box( 'sa_video_contest_meta_box', 'Video Contest Details', array( $this, 'sa_video_contest_meta_box' ), $this->post_type, 'normal', 'high' );   ;
	}
		function sa_video_contest_meta_box( $post ) {
			$custom = get_post_custom( $post->ID );
			$end_date = maybe_unserialize( $custom[ 'sa_video_contest_end_date' ][0] );
			$stem_sentence = $custom[ 'sa_notice_box_stem' ][0];
			$votes = sa_video_contest_count_votes( $post->ID );

			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_name, $this->nonce_value );
			?>
			<div>
				<h4>Contest End Date</h4>
				<p>
					<input type='text' name='sa_video_contest_end_date' id='sa_video_contest_end_date' value='<?php
						if ( ! empty( $end_date ) ) {
							echo sa_convert_to_human_date( $end_date );
						}
					 ?>'/>
				</p>
				<p class="info">Set the start date by scheduling the publication date in the "Publish" box.</p>
				<h4>Hub Home Page Notice Box Title</h4>
				<p>
					<input type='text' name='sa_notice_box_stem' id='sa_notice_box_stem' value='<?php
						if ( ! empty( $stem_sentence) ) {
							echo $stem_sentence;
						}
					 ?>'/>
				</p>
				<p class="info">This will be output in the notices box on the hub home page:<br />
					CONTEST ALERT <br />
					<em>&laquo;notice box title&raquo;</em>
				</p>

			</div>
			<div>
				<h4>Candidate Videos</h4>
				<p class="info">Note: Video URLs should take the form <em>http://www.youtube.com/watch?v=NMBEbVf965k</em></p>
				<?php
				// Collect the details of 6 videos
				for ( $i = 1; $i < 7; $i++ ) {
					?>
					<fieldset>
						<h5>Video <?php echo $i; ?> Information</h5>
						<label for='sa_video_contest_title_<?php echo $i; ?>'>Video Title</label>
						<input type='text' name='sa_video_contest_title_<?php echo $i; ?>' id='sa_video_contest_title_<?php echo $i; ?>' value='<?php
						if ( ! empty( $custom[ 'sa_video_contest_title_' . $i ][0] ) ) {
							echo $custom[ 'sa_video_contest_title_' . $i ][0];
						}
						?>' size="50"/><br />
						<label for='sa_video_contest_url_<?php echo $i; ?>'>Video URL</label>
						<input type='text' name='sa_video_contest_url_<?php echo $i; ?>' id='sa_video_contest_url_<?php echo $i; ?>' value='<?php
						if ( ! empty( $custom[ 'sa_video_contest_url_' . $i ][0] ) ) {
							echo $custom[ 'sa_video_contest_url_' . $i ][0];
						}
						?>' size="90"/>
						<p>Current votes: <?php
						if ( empty( $votes[ $i ] ) ) {
							echo '0';
						} else {
							echo $votes[ $i ];
						}
						?></p>
					</fieldset>
					<?php
				}


				?>
			</div>
			<div>
				<h4>Users who voted</h4>
				<ul>
				<?php
					$votes = $custom[ 'sa_video_contest_votes' ][0];
					if ( ! empty( $votes ) ) {
						$votes = maybe_unserialize( $votes );
						foreach ( $votes as $user_id => $video_id ) {
							?>
							<li>
							<?php echo bp_core_get_userlink( $user_id );
							?> &bull; <?php
								$email_addy = get_userdata( $user_id )->user_email;
							?><a href="mailto:<?php echo $email_addy; ?>"><?php echo $email_addy; ?></a>
							</li>
							<?php
						}
					} else {
						echo '<li>No votes yet!</li>';
					}
				?>
				</ul>
			</div>

		 	<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#sa_video_contest_end_date").datepicker( {
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
		$meta_fields_to_save = array( 'sa_video_contest_end_date', 'sa_notice_box_stem' );
		// Convert the end date for storage.
		if ( ! empty( $_POST[ 'sa_video_contest_end_date' ] ) ) {
			$_POST[ 'sa_video_contest_end_date' ] = sa_convert_to_computer_date( $_POST[ 'sa_video_contest_end_date' ] );
		}

		for ( $i = 1; $i < 7; $i++ ) {
			$meta_fields_to_save[] = 'sa_video_contest_title_' . $i;
			$meta_fields_to_save[] = 'sa_video_contest_url_' . $i;
		}

		// Save meta
		$meta_success = $this->save_meta_fields( $post_id, $meta_fields_to_save );

	}

	public function capture_vote_submission(){
		// $_POST['sa_video_contest_selection'] is an int
		// $_POST['video_contest_id'] is the post_id
		// $_POST['sa_video_contest_submit_vote'] is the submit button
		if ( ! isset( $_POST['sa_video_contest_submit_vote'] ) ) {
			return;
		}

		if ( ! sa_is_section( 'video-contest') ) {
			return;
		}

        $nonce_value = 'sa_video_contest_vote_' . $_POST['video_contest_id'] . '_' . get_current_user_id();
		if( ! wp_verify_nonce( $_POST[ $nonce_value ], 'sa_video_contest_vote' ) ) {
			bp_core_add_message( __( 'Sorry, we couldn\'t count your vote right now.', $this->plugin_slug ), 'error' );
			// Redirect and exit
			bp_core_redirect( sa_get_section_permalink( 'video-contest' ) );
			return;
		}

		$success = $this->count_vote( $_POST['video_contest_id'], get_current_user_ID(), $_POST['sa_video_contest_selection'] );

		if ( 1 == $success ) {
				bp_core_add_message( __( 'Thanks for your vote!', $this->plugin_slug ) );
	    } elseif ( -1 == $success ) {
			bp_core_add_message( __( 'You\'ve already voted. No ballot stuffing! :)' , $this->plugin_slug ), 'error' );
	    } elseif ( -2 == $success ) {
			bp_core_add_message( __( 'You\'ve got to choose a video for your vote to count! Try again.' , $this->plugin_slug ), 'error' );
	    } else {
			bp_core_add_message( __( 'Sorry, we couldn\'t count your vote right now.', $this->plugin_slug ), 'error' );
	    }
		// Redirect and exit
		bp_core_redirect( sa_get_section_permalink( 'video-contest' ) );
		return;
	}

	public function capture_join_group_submission(){
		// $_POST['sa_video_contest_selection'] is an int
		// $_POST['video_contest_id'] is the post_id
		// $_POST['sa_video_contest_submit_vote'] is the submit button
		if ( ! isset( $_POST['sa_video_contest_join_submit'] ) ) {
			return;
		}

		if ( ! sa_is_section( 'video-contest') ) {
			return;
		}
		$user_id = get_current_user_id();

		$nonce_value = 'sa_video_contest_join_submit_' . $user_id;
		if( ! wp_verify_nonce( $_POST[ $nonce_value ], 'sa_video_contest_join_submit' ) ) {
			bp_core_add_message( __( 'Sorry, we couldn\'t add you to the hub Salud America!', $this->plugin_slug ), 'error' );
			// Redirect and exit
			bp_core_redirect( sa_get_section_permalink( 'video-contest' ) );
			return;
		}

		$message = '';
		$error = false;

		// Add the user to the group if necessary.
		if ( isset( $_POST[ 'join_salud_america_hub' ] ) && $_POST[ 'join_salud_america_hub' ] == 'agreed' ) {
			if ( groups_join_group( sa_get_group_id(), $user_id ) ) {
				$message .= 'You have successfully joined the hub Salud America! ';
		    } else {
				$message .= 'Sorry, we couldn\'t add you to the hub Salud America! ';
				$error = true;
		    }
		}

		// Add the user to the group if necessary.
		if ( isset( $_POST[ 'salud_newsletter_acceptance' ] ) && $_POST[ 'salud_newsletter_acceptance' ] == 'agreed' ) {
			if ( add_user_meta( $user_id, 'salud_newsletter', 'agreed' ) ) {
				$message .= 'You have been added to the Salud America mailing list.';
		    } else {
				$message .= 'Sorry, we couldn\'t add you to the Salud America mailing list ';
				$error = true;
		    }
		}

		if ( ! empty( $message ) ) {
			if ( $error ) {
				bp_core_add_message( $message, 'error' );
			} else {
				bp_core_add_message( $message );
			}
		}
		// Redirect and exit
		bp_core_redirect( sa_get_section_permalink( 'video-contest' ) );
		return;
	}

	/**
	 * Is the current user eligible to vote?
	 *
	 * @since    1.0.0
	 * @param 	int $post_id of the contest.
	 * @param 	int $user_id to check. Defaults to current user.
	 * @param 	int $video_id the user selected.
	 * @return   int. -1 means "already voted", -2 means "no video selected", 0 "there was a problem", 1 "success"
	 */
	public function count_vote( $post_id, $user_id, $video_id ) {
		$votes = get_post_meta( $post_id, 'sa_video_contest_votes', true );
		$success = 0;

		// Has the user already voted?
		if ( array_key_exists( $user_id, $votes ) ) {
			return -1;
		}
		// Is the user eligible to vote?
		if ( ! sa_video_contest_current_user_can_vote( $post_id, $user_id ) ) {
			return $success;
		}
		// Did the user choose a video?
		if ( empty( $video_id ) ) {
			return -2;
		}

		// Add the user's vote to the array
		$votes[ $user_id ] = $video_id;

		if ( update_post_meta( $post_id, 'sa_video_contest_votes', $votes ) ) {
			$success = 1;
			/**
			 * Fires on successful tally of user vote.
			 *
			 * @since 1.3.1
			 *
			 * @param int   $post_id   ID of the contest the user voted in.
			 * @param int   $user_id   ID of the voter.
			 */
			do_action( 'sa_count_video_contest_vote', $post_id, $user_id );
		}

		return $success;
	}

	/**
	 * Add current contest to group home page notices box.
	 *
	 * @since    1.0.0
	 *
	 * @return   html
	 */
	public function add_notices( $notices ) {
		$args = array(
		'post_type' => $this->post_type,
		'posts_per_page' => 1,
		'meta_query' => array(
				            array(
				                'key' => 'sa_video_contest_end_date', // Check the start date field
				                'value' => date("Ymd"), // Set today's date (note the similar format)
				                'compare' => '>=', // Return the ones greater than today's date
				                'type' => 'NUMERIC,' // Let WordPress know we're working with numbers
				                )
				            ),
		);
		$contest = new WP_Query( $args );
		if ( $contest->have_posts() ) {
        	while ( $contest->have_posts() ):
        		$contest->the_post();
        		$post_id = get_the_ID();
        		// $post_meta = get_post_meta( $post_id );
        		// $end_date = $post_meta['sa_video_contest_end_date'][0];
        		// $teaser = $post_meta['sa_video_contest_notice_stem'][0];
		 		// $notices .= PHP_EOL . '<div class="sa-notice-item"><h4 class="sa-notice-title"><a href="' . get_the_permalink() . '"><span class="sa-action-phrase">Vote &amp; Win:</span>&ensp;';
		 		// $notices .= apply_filters( 'the_title', $teaser );
		 		// $notices .= '</a></h4>';
		 		// $notices .= '<a class="button" href="' . get_the_permalink() . '">Vote Now</a></div>';
        		$message = get_post_meta( $post_id, 'sa_notice_box_stem', true );
        		if ( empty( $message ) ) {
        			$message = get_the_title();
        		}

        		$notices[ $post_id ] = array(
        			'action-phrase' => 'Contest Alert',
        			'permalink'		=> get_the_permalink(),
        			'title'			=> $message,
        			'fallback_image' => sa_get_plugin_base_uri() . 'public/images/fallbacks/contest-icon.png'

    			);
			endwhile;
			wp_reset_query();
		}

		return $notices;
	}

} //End class CC_SA_Resources_CPT_Tax
$sa_video_contest_cpt_tax = new CC_SA_Video_Contests_CPT_Tax();

function sa_video_contest_count_votes( $post_id = 0 ){
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$meta = get_post_meta( $post_id );
	$votes = maybe_unserialize( current( $meta['sa_video_contest_votes'] ) );

	// Produces an array with members 'value' => 'count'...
	$counts = array_count_values( $votes );

	// Add array members for videos that didn't get any votes.
	// First, we need to know the keys of the candidate videos.
	$candidate_videos = array();
	for ( $i = 1; $i < 7 ; $i++ ) {
		if ( ! empty( $meta[ 'sa_video_contest_url_' . $i ][0] ) ) {
			$candidate_videos[] = $i;
		}
	}
	$videos_with_no_votes = array_diff( $candidate_videos, array_keys( $counts ) );
	foreach ( $videos_with_no_votes as $id ) {
		$counts[ $id ] = 0;
	}

	// Finally, we order the array by values lowest to highest, so our winner is always first.
	arsort( $counts );

    return $counts;
}

/**
 * Is the current user eligible to vote?
 *
 * @since    1.0.0
 * @param 	int $post_id of the contest.
 * @param 	int $user_id to check. Defaults to current user.
 * @return   boolean
 */

function sa_video_contest_current_user_can_vote( $post_id = 0, $user_id = 0 ){
	$can_vote = false;
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	// If the user isn't logged in, she can't vote.
	if ( empty( $user_id ) ) {
		return $can_vote;
	}
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	// The contest must still be open
	if ( ! sa_video_contest_is_active( $post_id ) ) {
		return $can_vote;
	}

	// You must be a SA group member to vote.
	if ( ! groups_is_user_member( $user_id, sa_get_group_id() ) ) {
		return $can_vote;
	}

	// Has the user already voted?
	if ( ! sa_video_contest_get_current_user_vote( $post_id, $user_id ) ) {
		$can_vote = true;
	}

    return $can_vote;
}

/**
 * Is the current user eligible to vote?
 *
 * @since    1.0.0
 * @param 	int $post_id of the contest.
 * @param 	int $user_id to check. Defaults to current user.
 * @return   int 0 for no vote, else integer for number of video voted for.
 */

function sa_video_contest_get_current_user_vote( $post_id = 0, $user_id = 0 ){
	$vote = 0;

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	// If the user isn't logged in, she can't vote.
	if ( empty( $user_id ) ) {
		return $vote;
	}
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}
	// Has the user already voted?
	$votes = get_post_meta( $post_id, 'sa_video_contest_votes', true );
	if ( array_key_exists( $user_id, $votes ) ) {
		$vote = $votes[ $user_id ];
	}

    return $vote;
}

function sa_video_contest_is_active( $post_id ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}
	$end_date = get_post_meta( $post_id, 'sa_video_contest_end_date', true );
	return ( date( 'Ymd' ) <= $end_date ) ? true : false ;
}

function sa_has_current_video_contest() {
	// Check for published contests that have not yet expired.
	$sa_video_contest_class = new CC_SA_Video_Contests_CPT_Tax();
	$args = array(
		'post_type' => $sa_video_contest_class->post_type,
		'meta_query' => array(
				            array(
				                'key' => 'sa_video_contest_end_date', // Check the start date field
				                'value' => date("Ymd"), // Set today's date (note the similar format)
				                'compare' => '>=', // Return the ones greater than today's date
				                'type' => 'NUMERIC,' // Let WordPress know we're working with numbers
				                )
				            ),
		);
	$contest = new WP_Query( $args );
	if ( $contest->have_posts() ) {
		$retval = true;
	} else {
		$retval = false;
	}
	return $retval;
}