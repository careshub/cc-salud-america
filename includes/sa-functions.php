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
            $group_id = 42;
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
    // If a group_id is supplied, it is probably because the post originated from another group (and editing should occur from the original group's space).
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
function sa_get_tab_slug( $section = 'policies' ){
    switch ( $section ) {
        case 'resources':
        case 'saresources':
            $slug = "resources"; //old value = saresources
            break;
        case 'heroes':
        case 'sa_success_story':
            $slug = "heroes"; //old value = sa_success_story
            break;
        case 'take_action':
            $slug = "take-action"; //old value = take-action-list
            break;
        case 'video-contest':
            $slug = "video-contest";
            break;
        case 'changes':
        case 'policies':
        case 'sapolicies':
        default:
            $slug = "changes"; //old value = sapolicies
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
            $label = "Resources for Change";
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
        case 'policies':
        case 'changes':
        default:
            $label = "Find Change";
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
            $cpt = 'sa_take_action';
            break;
        case 'video-contest':
            $cpt = 'sa_video_contest';
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
function sa_get_section_by_cpt( $cpt = 'sapolicies' ){
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
            $section = 'video-contest';
            break;
        default:
            $section = "policies";
            break;
    }

    return apply_filters( 'sa_get_section_by_cpt', $section );
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
    if ( sa_is_archive_taxonomy() ) {
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
        $query = array(
            'name' => bp_action_variable( 0 ),
            'post_type' => $cpt,
            // 'post_status' => array( 'publish', 'draft'),
        );
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
            $query['meta_key'] = 'sa_success_story_video_url';
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
        $cpt = sa_get_cpt_by_section( $section );
        // What taxonomies are associated with that cpt?
        $taxonomy_names = get_object_taxonomies( $cpt );
        $other_names = array( 'page', 'paged', 'search' );

        $action_variables = bp_action_variables();

        $reserved_names = array_merge( $taxonomy_names, $other_names );

        if ( ! empty( $action_variables ) && ! in_array( $action_variables[0], $reserved_names ) ) {
            $single = true;
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
        $cpt = sa_get_cpt_by_section( $section );
        // What taxonomies are associated with that cpt?
        $taxonomy_names = get_object_taxonomies( $cpt );

        $action_variables = bp_action_variables();

        // If $action_variables is populated, and the first entry is the slug of a related taxonomy, this must be a taxonomy term filter.
        if ( ! empty( $action_variables ) && in_array( $action_variables[0], $taxonomy_names ) ) {
            $is_tax = true;
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
    $filter_args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
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
    if ( ! empty( $_POST['keyword'] ) ) {
        $filter_args['s'] = $_POST['keyword'];
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