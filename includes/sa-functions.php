<?php
/**
 * Utility functions for the plugin.
 *
 *
 * Community Commons Salud America
 *
 * @package   Community_Commons_Salud_America
 * @author    David Cavins
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 */

/**
 * Fetch the group id of the SA group for the current environment.
 *
 * @since   1.0.0
 *
 * @return  int The SA group ID
 */
function sa_get_group_id(){
    $location = get_site_url();
    switch ( $location ) {
        case 'http://commonsdev.local':
            $group_id = 42;
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 42;
            break;
        case 'http://staging.communitycommons.org':
            $group_id = 583;
            break;
        case 'http://www.communitycommons.org':
            $group_id = 678;
            break;
        default:
            $group_id = 42;
            break;
    }
    return apply_filters( 'sa_get_group_id', $group_id );
}

/**
 * Get base url for the Salud America group
 *
 * @since   1.0.0
 *
 * @return  string url
 */
function sa_get_group_permalink() {
    $group_id = sa_get_group_id();
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) );

    return apply_filters( "sa_get_group_permalink", $permalink, $group_id);
}

/**
 * Get url for a tab within the Salud America group.
 *
 * @since   1.0.0
 *
 * @param   string $section The shorthand name of the section
 *
 * @return  string url
 */
function sa_get_section_permalink( $section = 'policies' ) {
    $permalink = sa_get_group_permalink() .  trailingslashit( sa_get_tab_slug( $section ) );

    return apply_filters( "sa_get_section_permalink", $permalink );
}

/**
 * Fetch the slug for a specified tab.
 *
 * @since   1.0.0
 *
 * @param   string $section The shorthand name of the section
 * @return  string The slug for the target tab
 */
function sa_get_tab_slug( $section = 'policies' ) {
    switch ( $section ) {
        case 'resources':
        case 'saresources':
            $slug = "resources"; //old value = saresources
            break;
        case 'heroes':
        case 'sa_success_story':
            $slug = 'heroes'; //old value = sa_success_story
            break;
        case 'take_action':
            $slug = 'take-action'; //old value = take-action-list
            break;
        case 'video-contest':
            // $slug = 'video-contest';
            $slug = 'take-action'; // video contests were moved under the take-action tab.
            break;
        case 'tweetchats':
            $slug = 'tweetchats';
            break;
        case 'big_bets':
        case 'big-bets':
            $slug = 'big-bets';
            break;
        case 'search':
            $slug = 'search';
            break;
        case 'changes':
        case 'policies':
        case 'sapolicies':
        default:
            $slug = 'changes'; //old value = sapolicies
            break;
    }

    return apply_filters( 'sa_get_tab_slug', $slug );
}

/**
 * Fetch the label for a specified tab.
 *
 * @since   1.0.0
 *
 * @param   string $section The shorthand name of the section
 * @return  string The label for the target tab
 */
function sa_get_tab_label( $section = 'policies' ){
    switch ( $section ) {
        case 'resources':
            $label = "Resources";
            break;
        case 'heroes':
        case 'success-stories':
            $label = "Salud Heroes";
            break;
        case 'take_action':
            $label = "Take Action!";
            break;
        case 'video-contest':
            $label = "Video Contest";
            break;
        case 'tweetchats':
            $label = "Tweetchats";
            break;
        case 'big_bets':
            $label = 'Topics';
            break;
        case 'policies':
        case 'changes':
        default:
            $label = "Changes";
            break;
    }

    return apply_filters( 'sa_get_tab_label', $label );
}

/**
 * Fetch the name of the CPT that's associated with a section.
 *
 * @since   1.0.0
 *
 * @param   string $section The shorthand name of the section
 * @return  string The CPT name
 */
function sa_get_cpt_by_section( $section = 'policies' ){
    switch ( $section ) {
        case 'resources':
            $cpt = "saresources";
            break;
        case 'heroes':
        case 'success-stories':
            $cpt = "sa_success_story";
            break;
        case 'take_action':
        case 'take-action':
            $cpt = array( 'sa_take_action', 'sa_video_contest' );
            break;
        case 'video-contest':
            $cpt = 'sa_video_contest';
            break;
        case 'tweetchats':
            $cpt = 'sa_tweetchats';
            break;
        case 'big_bets':
        case 'big-bets':
            $cpt = array( 'saresources', 'sa_success_story', 'sapolicies' );
            break;
        case 'policies':
        case 'changes':
        default:
            $cpt = "sapolicies";
            break;
    }

    return apply_filters( 'sa_get_cpt_by_section', $cpt );
}

/**
 * Fetch the name of the section where a custom post type is displayed.
 *
 * @since   1.0.0
 *
 * @param   string $cpt The custom post type we want to find
 * @return  string $section The shorthand name of the section
 */
function sa_get_section_by_cpt( $cpt = 'sapolicies' ) {
    switch ( $cpt ) {
        case 'saresources':
            $section = "resources";
            break;
        case 'sa_success_story':
            $section = "heroes";
            break;
        case 'sa_take_action':
            $section = 'take_action';
            break;
        case 'sa_video_contest':
            // $section = 'video-contest';
            $section = 'take_action';
            break;
        case 'sa_tweetchats':
            $section = 'tweetchats';
            break;
        case 'sa_term_introduction':
            $section = 'big-bets';
            break;
        default:
            $section = "policies";
            break;
    }

    return apply_filters( 'sa_get_section_by_cpt', $section );
}

/**
 * Get the natural language label of a post type.
 *
 * @since   1.2.0
 *
 * @param   string $cpt The custom post type we want to find
 * @return  string $section The shorthand name of the section
 */
function sa_get_label_by_cpt( $cpt = 'sapolicies' ) {
    switch ( $cpt ) {
        case 'saresources':
            $section = "resources";
            break;
        case 'sa_success_story':
            $section = "salud heroes";
            break;
        case 'sa_take_action':
            $section = 'take_action';
            break;
        case 'sa_video_contest':
            $section = 'video contests';
            break;
        case 'sa_tweetchats':
            $section = 'tweetchats';
            break;
        default:
            $section = "changes";
            break;
    }

    return apply_filters( 'sa_get_label_by_cpt', $section );
}

/**
 * Get short slug-type string to represent a post type.
 * Useful for query arguments.
 *
 * @since   1.4.0
 * @param   string $post_type
 *
 * @return  string
 */
function salud_get_post_type_short_slug( $post_type ) {
    switch ( $post_type ) {
        case 'resources':
        case 'saresources':
            $slug = "resources";
            break;
        case 'heroes':
        case 'sa_success_story':
            $slug = 'heroes'; //old value = sa_success_story
            break;
        case 'sa_take_action':
            $slug = 'take-action'; //old value = take-action-list
            break;
        case 'sa_video_contest':
            // $slug = 'video-contest';
            $slug = 'video-contests'; // video contests were moved under the take-action tab.
            break;
        case 'tweetchats':
            $slug = 'tweetchats';
            break;
        case 'changes':
        case 'policies':
        case 'sapolicies':
        default:
            $slug = 'changes'; //old value = sapolicies
            break;
    }

    return $slug;
}

/**
 * Get short string to represent a post type.
 *
 * @since   1.4.0
 * @param   string $post_type
 *
 * @return  string
 */
function salud_get_post_type_short_name( $post_type ) {
    switch ( $post_type ) {
        case 'resources':
        case 'saresources':
            $slug = "Resources";
            break;
        case 'heroes':
        case 'sa_success_story':
            $slug = 'Salud Heroes'; //old value = sa_success_story
            break;
        case 'sa_take_action':
            $slug = 'Take Action!'; //old value = take-action-list
            break;
        case 'sa_video_contest':
            // $slug = 'video-contest';
            $slug = 'Video Contests'; // video contests were moved under the take-action tab.
            break;
        case 'tweetchats':
            $slug = 'Tweetchats';
            break;
        case 'changes':
        case 'policies':
        case 'sapolicies':
        default:
            $slug = 'Changes'; //old value = sapolicies
            break;
    }

    return $slug;
}

/*
* Call sa_get_the_cpt_tax_intersection_link and echo the result
*
*/
function sa_the_cpt_tax_intersection_link( $section = false, $taxonomy = false, $term = false ){
    echo sa_get_the_cpt_tax_intersection_link( $section, $taxonomy, $term );
}
    /**
     * Create a url to a taxonomy term within a CPT, SA-specific
     *
     * @param string $section The shorthand name of the section
     * @param string $taxonomy
     * @param string $term
     *
     * @return string of the url || false
     */
    function sa_get_the_cpt_tax_intersection_link( $section = false, $taxonomy = false, $term = false ){

        // Bail if one of the args isn't specified
        if( !( $section ) || !( $taxonomy ) || !( $term ) ) {
            return false;
        }

        // $post_type = sa_get_cpt_by_section( $section );

        // // If that CPT doesn't exist, bail
        // if ( !$cpt_object = get_post_type_object( $post_type ) )
        //     return false;

        // $cpt_slug = $cpt_object->name;

        // //Make sure the taxonomy requested is actually related to the CPT
        // if ( !in_array( $taxonomy, $cpt_object->taxonomies ) ) {
        //     return false;
        // }

        return sa_get_section_permalink( $section ) . $taxonomy . '/' . $term;
    }

/**
 * Get the requested taxonomy term if applicable.
 *
 * @since   1.0.0
 *
 * @return  object taxonomy term object
 */
function sa_get_requested_tax_term(){
    $term = false;
    if ( sa_is_big_bets_tab() ) {
        $action_variables = bp_action_variables();
        //URLs take the form salud-america/big-bets/term-slug
        // So we get the advocacy_target terms:
        $term = get_term_by( 'slug', $action_variables[0], 'sa_advocacy_targets' );
    } else if ( sa_is_archive_taxonomy() ) {
        $action_variables = bp_action_variables();
        $term = get_term_by( 'slug', $action_variables[1], $action_variables[0] );
    }

    return apply_filters( "sa_get_requested_tax_term", $term );
}

/**
 * Build the required query, based on the action and action variables.
 *
 * @since   1.0.0
 *
 * @return  array query variables
 */
function sa_get_query(){
    $section = bp_current_action();
    $cpt = sa_get_cpt_by_section( $section );

    // For a single post, get the post by the slug
    if ( sa_is_single_post() ){
        $request = bp_action_variable( 0 );
        $query = array(
            'post_type' => $cpt,
        );
        if ( isset( $_GET['preview'] ) && ( $_GET['preview'] ) && is_numeric( $request ) ) {
            $query['p']  = $request;
        } else {
            $query['name'] = $request;
        }
    } else if ( sa_is_take_action_tab() ) {
        // The Take Action tab is a rule unto itself.
        // We sort by end date, interleaving video contests and petitions.
        // There are two sub-pages, "current-actions" and "past-actions"
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

        $query = array(
            'post_type' => array( 'sa_video_contest', 'sa_take_action' ),
            'paged' => $paged,
            'meta_query' => array(
                array(
                    'key' => 'sa_expiry_date',
                    'value' => date("Ymd"), // Set today's date
                    'type' => 'NUMERIC'
                    )
                ),
        );
        if ( 'past-actions' == bp_action_variable( 0 )) {
            // Find only "expired" posts
            $query['meta_query'][0]['compare'] = '<';
        } else {
            // Find only posts that haven't expired
            $query['meta_query'][0]['compare'] = '>=';
        }

    } else {
        // This is a taxonomy term view
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

        $query = array(
            'post_type' => $cpt,
            'paged' => $paged,
        );

        if ( sa_is_archive_taxonomy() && $term = sa_get_requested_tax_term() ) {
            $query['tax_query'] = array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                    // 'operator' => 'IN'
                )
            );
        }

        // On the video archive, only find stories with a set video url.
        if ( $section == 'heroes' && isset( $_GET['style'] ) && $_GET['style'] == 'videos' ) {
            $query['meta_key'] = 'sa_featured_video_url';
        }

    }

    return apply_filters( "sa_get_query", $query );
}

/** Conditional checks ********************************************************/

/**
 * Is this the Salud America group?.
 *
 * @since   1.0.0
 *
 * @param   int $group_id Optional. Group ID to check.
 *          Defaults to current group.
 * @return  bool
 */
function sa_is_sa_group( $group_id = 0 ) {
    if ( empty( $group_id ) ){
        $group_id = bp_get_current_group_id();
    }
    $setting = ( sa_get_group_id() == $group_id ) ? true : false;

    return apply_filters( 'sa_is_sa_group', $setting );
}

/**
 * Are we looking at a single post?
 *
 * @since   1.0.0
 *
 * @return  bool
 */
function sa_is_single_post(){
    $single = false;

    if ( sa_is_sa_group() ) {
        // If $action_variables is populated, and the first entry isn't a
        // * slug of a related taxonomy
        // * the word "page"
        // * other things?
        // this must be a single post.

        // Which section are we looking at?
        $section = bp_current_action();

        if ( sa_is_big_bets_tab() ) {
            $single = false;
        } else {
            $cpt = sa_get_cpt_by_section( $section );
            // What taxonomies are associated with that cpt?
            $taxonomy_names = get_object_taxonomies( $cpt );
            $other_names = array( 'page', 'paged', 'search' );

            // The "take action" tab has two subsections, we'll need to account for those.
            if ( sa_is_take_action_tab() ) {
                $other_names[] = 'current-actions';
                $other_names[] = 'past-actions';
            }

            $action_variables = bp_action_variables();

            $reserved_names = array_merge( $taxonomy_names, $other_names );

            if ( ! empty( $action_variables ) && ! in_array( $action_variables[0], $reserved_names ) ) {
                $single = true;
            }
        }
    }

    return apply_filters( 'sa_is_single_post', $single );
}

/**
 * Are we looking at a taxonomy-filtered view?
 *
 * @since   1.0.0
 *
 * @return  bool
 */
function sa_is_archive_taxonomy(){
    $is_tax = false;

    if ( sa_is_sa_group() ) {
        // Which section are we looking at?
        $section = bp_current_action();
        $action_variables = bp_action_variables();

        if ( sa_is_big_bets_tab() ) {
            //URLs take the form salud-america/big-bets/term-slug
            // So we get the advocacy_target terms:
            $terms = get_terms( 'sa_advocacy_targets', array(
                'hide_empty' => 0,
                'fields' => 'id=>slug'
             ) );
            // The advocacy target slug will be the action variable if we're looking at a term archive.
            if ( ! empty( $action_variables ) && in_array( $action_variables[0], $terms ) ) {
                $is_tax = true;
            }
        } else {
            $cpt = sa_get_cpt_by_section( $section );
            // What taxonomies are associated with that cpt?
            $taxonomy_names = get_object_taxonomies( $cpt );

            // If $action_variables is populated, and the first entry is the slug of a related taxonomy, this must be a taxonomy term filter.
            if ( ! empty( $action_variables ) && in_array( $action_variables[0], $taxonomy_names ) ) {
                $is_tax = true;
            }
        }
    }

    return apply_filters( 'sa_is_archive_taxonomy', $is_tax );
}

/**
 * Is this the front page of a section?
 *
 * @since   1.0.0
 *
 * @param   string  $section name to check for. Optional. Otherwise just checks
 *                  that this is a section front generally.
 * @return  bool
 */
function sa_is_section_front( $section = false ) {
    $section_front = false;
    // if the first action variable is empty, this is the basic view.
    if ( sa_is_sa_group() ) {
        $av = bp_action_variable();
        if ( false == $av || in_array( $av, array( 'page', 'paged' ) )  ) {
        $section_front = true;
        }
    }

    // If we're checking that we're on a specific section, make that check.
    if ( ! empty( $section ) && $section_front ) {
        $section_front = false;
        if ( $section == bp_current_action() ) {
            $section_front = true;
        }
    }

    return apply_filters( 'sa_is_section_front', $section_front );
}

/**
 * Is this part of a section?
 *
 * @since   1.0.0
 *
 * @param   string $section name to check for.
 * @return  bool
 */
function sa_is_section( $section = false ) {
    $is_section = false;
    if ( sa_is_sa_group() && $section == bp_current_action() ) {
        $is_section = true;
    }

    return apply_filters( 'sa_is_section', $is_section );
}

/**
 * Is this a policy/resources/etc search page?
 *
 * @since   1.0.0
 *
 * @return  bool
 */
function sa_is_archive_search(){
    $is_search = false;

    // if the first action variable is empty, this is the basic view.
    if ( sa_is_sa_group() ) {
        if ( 'search' == bp_action_variable() ) {
            $is_search = true;
        }
    }

    return apply_filters( 'sa_is_archive_search', $is_search );
}

/**
 * Convert $_POST search args to WP_Query-ready argument array.
 *
 * @since   1.0.0
 *
 * @param   string $post_type saresources or similar
 * @param   array  $taxonomies list of taxonomy names to include
 * @param   array  $metas list of meta_keys to check for
 * @return  array WP_Query-ready query arguments
 */
function sa_build_search_query( $post_type, $taxonomies = array(), $metas = array() ){
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

    $filter_args = array(
        'post_type' => $post_type,
        'posts_per_page' => 20,
        'suppress_filters' => true,
        'paged' => $paged,
    );
    // Parse the search query
    // Begin by handling any combination of advanced search checkboxes selected.
    $tax_query = array();
    $meta_query = array();

    foreach ( $taxonomies as $tax) {
        if ( ! empty( $_POST[$tax] ) ) {
            $tax_query[] = array(
                 'taxonomy' => $tax,
                 'field' => 'term_id',
                 'terms' => $_POST[$tax]
            );
        }
    }
    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 ) {
            $tax_query[ 'relation' ] = 'AND';
        }
        $filter_args['tax_query'] = $tax_query;
    }

    foreach ( $metas as $meta ) {
        if ( ! empty( $_POST[$meta] ) ) {
            $meta_query[] = array(
                'key' => $meta,
                'value' => $_POST[$meta],
                'compare' => 'IN',
            );
        }
    }
    if ( ! empty( $meta_query ) ) {
        if ( count( $meta_query ) > 1 ) {
            $meta_query[ 'relation' ] = 'AND';
        }
        $filter_args['meta_query'] = $meta_query;
    }

    // Add the text search term if necessary
    if ( ! empty( $_REQUEST['keyword'] ) ) {
        $filter_args['s'] = esc_attr( $_REQUEST['keyword'] );
    }

    return $filter_args;
}

/**
 * Convert number of columns to appropriate class for blocks.
 *
 * @since   1.0.0
 *
 * @param   int $columns number of columns
 * @return  string class name
 */
function sa_get_classname_from_columns( $columns ) {
        switch ( (int) $columns ) {
        case 2:
            $class = 'half-block';
            break;
        case 4:
            $class = 'quarter-block';
            break;
        case 6:
            $class = 'sixth-block';
            break;
        case 3:
        default:
            $class = 'third-block';
            break;
    }
    return apply_filters( 'sa_get_classname_from_columns', $class );
}

/**
 * Are we viewing the new "big bets" tab?
 * URLs are structured differently here.
 *
 * @since   1.2.0
 *
 * @return  bool
 */
function sa_is_big_bets_tab(){
    $retval = false;
    if ( bp_current_action() == sa_get_tab_slug( 'big_bets' ) ) {
        $retval = true;
    }
    return $retval;
}

/**
 * Are we viewing the reimagined "take action" tab?
 * URLs are structured differently here.
 *
 * @since   1.3.5
 *
 * @return  bool
 */
function sa_is_take_action_tab(){
    $retval = false;
    if ( bp_current_action() == sa_get_tab_slug( 'take_action' ) ) {
        $retval = true;
    }
    return $retval;
}


/**
* Date format converters
* For comparison purposes, dates are stored in the db like "20150417"
* These helper functions act as converters to produce human readable dates.
*
* @param $date string date in human- or computer-readable format
*
*/
function sa_convert_to_computer_date( $date ){
    // Goal format is "Ymd"
    $shuffle = date_create_from_format( 'F j, Y', $date );
    return date_format( $shuffle, 'Ymd' );
}
function sa_convert_to_human_date( $date ){
    // Goal format is "F j, Y"
    $shuffle = date_create_from_format( 'Ymd', $date );
    return date_format( $shuffle, 'F j, Y' );
}
function sa_convert_to_short_human_date( $date ){
    // Goal format is 3/25
    $shuffle = date_create_from_format( 'Ymd', $date );
    return date_format( $shuffle, 'n/j' );
}
function sa_convert_to_short_complete_human_date( $date ){
    // Goal format is 3/25/2015
    $shuffle = date_create_from_format( 'Ymd', $date );
    return date_format( $shuffle, 'n/j/Y' );
}

/**
 * Build the URL to the policy map.
 *
 * @since   1.2.0
 *
 * @return  string
 */
function sa_policy_map_base_url( $append ) {
    echo sa_get_policy_map_base_url( $append );
}
    function sa_get_policy_map_base_url( $append = '' ) {
        $retval = 'http://maps.communitycommons.org/policymap/';
        if ( ! empty( $append ) ) {
            $retval = $retval . $append;
        }

        return $retval;
    }

function sa_get_most_recent_items_by_big_bet( $term_slug = '', $exclude_ids = array() ) {
    if ( empty( $term_slug ) ) {
        return;
    }

    // Get the term
    $term = get_term_by( 'slug', $term_slug, 'sa_advocacy_targets' );

    // Following is an attempt to minimize queries.
    // If this doesn't work because one post type is published much less frequently than others, I may have to do separate queries.
    $args = array(
        'post_status' => array( 'publish' ),
        'post_type' => array( 'sapolicies', 'saresources', 'sa_success_story' ),
        'tax_query' => array(
                            array(
                                'taxonomy' => 'sa_advocacy_targets',
                                'field'    => 'slug',
                                'terms'    => $term_slug,
                            ),
                        ),
        // Optimizations to minimize what WP_Query does.
        'nopaging' => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'no_found_rows' => 1,
    );

    // Don't send an empty array for the post__not_in argument.
    if ( ! empty( $exclude_ids ) ) {
        $args['post__not_in'] = $exclude_ids;
    }

    $recent_items = new WP_Query( $args );
    // Set up the return array.
    $results = array(
        'term_slug' => $term_slug,
        'posts' => array(
            'sapolicies'        => array(
                'post_type' => 'sapolicies', // This is duplicated here for easier use in the JS template loop.
                'term_slug' => $term_slug, // This is duplicated here for easier use in the JS template loop.
                'post_id'   => 0,
                'title'     => '',
                'permalink' => '',
                'thumbnail' => '',
                'excerpt'   => ''
                ),
            'saresources'       => array(
                'post_type' => 'saresources',
                'term_slug' => $term_slug,
                'post_id'   => 0,
                'title'     => '',
                'permalink' => '',
                'thumbnail' => '',
                'excerpt'   => ''
                ),
            'sa_success_story'  => array(
                'post_type' => 'sa_success_story',
                'term_slug' => $term_slug,
                'post_id'   => 0,
                'title'     => '',
                'permalink' => '',
                'thumbnail' => '',
                'excerpt'   => ''
                ),
            ),
        );

    if ( $recent_items->have_posts() ) {
        $i = 1;

        // Pull out the post_id, thumbnail image, create an excerpt from the content, permalink
        foreach ( $recent_items->posts as $item ) {
            $i++;

            // Do we still need one of these?
            if ( ! empty( $results['posts'][ $item->post_type ]['post_id'] ) ) {
                continue;
            }

            $results['posts'][ $item->post_type ]['post_id'] = $item->ID;
            $results['posts'][ $item->post_type ]['title'] = wptexturize( $item->post_title );
            $results['posts'][ $item->post_type ]['permalink'] = get_permalink( $item->ID );

            // Prepare the thumbnail code.
            if ( has_post_thumbnail( $item->ID ) ) {
                // Use the post thumbnail if it exists
                $results['posts'][ $item->post_type ]['thumbnail'] = get_the_post_thumbnail( $item->ID, 'feature-front-sub' );
            } else {
                // Otherwise, use some stand-in images by advocacy target
                $results['posts'][ $item->post_type ]['thumbnail'] = sa_get_advo_target_fallback_image( $term, 'feature-front-sub', '' );
            }

            $results['posts'][ $item->post_type ]['excerpt'] = wptexturize( cc_ellipsis( $item->post_content, 125 ) );

            // Do we have a complete results set? Can we stop?
            if ( ! empty( $results['posts'][ 'sapolicies' ]['post_id'] )
                && ! empty( $results['posts'][ 'saresources' ]['post_id'] )
                && ! empty( $results['posts'][ 'sa_success_story' ]['post_id'] ) ) {
                break;
            }
        }

    }

    return $results;
}

/**
 * Fetch the field ids that we use to determine location.
 *
 * @since   1.4
 *
 * @return  int The field ID
 */
function sa_get_location_xprofile_field_ids(){
    $location = get_site_url();
    switch ( $location ) {
        case 'http://commonsdev.local':
            $ids = array( 'optin' => 96, 'location' => 98 );
            break;
        case 'http://dev.communitycommons.org':
            $ids = array( 'optin' => 96, 'location' => 98 );
            break;
        case 'http://staging.communitycommons.org':
            $ids = array( 'optin' => 1013, 'location' => 949 );
            break;
        case 'http://www.communitycommons.org':
            $ids = array( 'optin' => 1312, 'location' => 1314 );
            break;
        default:
            $ids = array( 'optin' => 1013, 'location' => 949 );
            break;
    }
    return apply_filters( 'sa_get_location_xprofile_field_ids', $ids );
}

/**
 * Get very short slug to stand in for the full slug of a big bet.
 * Useful as a query argument.
 *
 * @since   1.4.0
 * @param   string $term_slug
 *
 * @return  string url
 */
// Sometimes the advocacy target term slug is too long, like for query arguments.
function salud_get_bet_short_name( $term_slug ) {
    // Substrings are good enough--I can imagine these slugs changing,
    // so we'll just pick out the key, unique word.
    $shorties = array( 'spaces', 'food', 'equity', 'schools', 'weight', 'sugar' );
    foreach( $shorties as $shorty ) {
        if ( strpos( $term_slug, $shorty ) ) {
            return $shorty;
        }
    }
}

/**
 * Is the current or specified user a member of the Salud America hub?
 *
 * @since   1.5.0
 * @param   int $user_id
 *
 * @return  bool
 */
function sa_is_current_user_a_member( $user_id = 0 ) {
    $is_member = false;

    if ( empty( $user_id )  ) {
        $user_id = get_current_user_id();
    }

    if ( $user_id ) {
        $is_member = (bool) groups_is_user_member( $user_id, sa_get_group_id() );
    }

    return $is_member;
}