<?php
/**
 * Generate the public-facing pieces of the plugin.
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
 * Generate the Salud America footer text.
 *
 * @since   1.0.0
 *
 * @return  string The html for the text block
 */
// add_action( 'bp_after_group_body', 'salud_america_footer' );
function salud_america_footer() {
    if ( sa_is_sa_group() ) :
    ?>
    <div class="salud-footer">
        <p>Salud America!  is a national online network of researchers, community group leaders, decision-makers, and members of the public working together to support healthy policy and environmental changes that can help reverse obesity among Latino children.</p>
        <p><a href="http://http://www.rwjf.org/"><img class="alignright" src="/wp-content/themes/CommonsRetheme/img/salud_america/logo-rwjf_small.png" ></a>The network, funded by the Robert Wood Johnson Foundation, is a project of <a href="http://ihpr.uthscsa.edu/"> the Institute for Health Promotion Research (IHPR)</a> at <a href="http://uthscsa.edu/">The UT Health Science Center at San Antonio</a>.</p>
        <p>Policies, comments, external links, and contributed stories and images are not affiliated with Salud America!, RWJF, or The UT Health Science Center at San Antonio, nor do they necessarily reflect the views of or endorsement by these organizations.</p>
    </div>
    <?php
    endif;
}

add_filter( 'bp_get_group_description', 'sa_append_rwjf_logo_to_group_description' );
function sa_append_rwjf_logo_to_group_description( $description ) {
    if ( sa_is_sa_group() ) {
        $replacement = '<a href="http://http://www.rwjf.org/"><img class="alignright" src="/wp-content/themes/CommonsRetheme/img/salud_america/logo-rwjf_small.png" ></a>';
        $description = str_replace('<!--rwjf-logo-->', $replacement, $description);
    }
    return $description;
}
/**
 * Generate archive navigation within Salud America.
 *
 * @since   1.0.0
 *
 * @param   string $html_id The id to apply to the nav
 * @param   int $paged The current page number
 * @param   int $paged The total number of pages of results
 * @return  string The html for the nav block
 */
function sa_section_content_nav( $html_id, $paged = 1, $total_pages = 1 ) {
    $html_id = esc_attr( $html_id );

    if ( $total_pages > 1 ) : ?>
        <nav id="<?php echo $html_id; ?>" class="sa-archive-navigation" role="navigation">
            <h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
            <div class="nav-previous"><a href="?paged=<?php echo $paged + 1; ?>">Older Posts</a></div>
            <?php if ( $paged > 1 ) : ?>
                <div class="nav-next"><a href="?paged=<?php echo $paged - 1; ?>">Newer Posts</a></div>
            <?php endif; ?>
        </nav><!-- #<?php echo $html_id; ?> .navigation -->
    <?php endif;
}

/**
 * Generate list of top resources in each topic.
 *
 * @since   1.0.0
 *
 * @param   array of slugs of requested resource cats
 * @return  string The html for the block
 */
function saresources_get_featured_blocks( $resource_cats = array() ) {
    //We'll loop through the entries of the array to build the queries and display the content
    //Count the dimension of the resource_cats array to determine proper class to apply to top blocks.
    $count = count( $resource_cats );
    switch ( $count ) {
        case ( $count % 4 == 0 ) :
            $block_class = 'quarter-block';
            break;
        case ( $count % 3 == 0 ) :
            $block_class = 'third-block';
            break;
        default:
            $block_class = 'half-block';
            break;
    }
    $do_not_duplicate = array();

    foreach ( $resource_cats as $resource_cat ) {
        $args = array(
            'post_type' => 'saresources',
            'sa_resource_cat' => $resource_cat,
            'showposts' => '3',
            'post__not_in' => $do_not_duplicate,
        );
        $resources_results = new WP_Query( $args );
        // echo "<pre>";
        // var_dump($resources_results);
        // echo "</pre>";

        // The Loop
        if ( $resources_results->have_posts() ) : ?>
                <div class="<?php echo $block_class; ?>">
                    <?php
                    while ( $resources_results->have_posts() ) : $resources_results->the_post();
                        if ( $resources_results->current_post == 0 ) :
                        ?>
                            <header class="entry-header">
                                <?php
                                $topic_link = sa_get_the_cpt_tax_intersection_link( 'resources', 'sa_resource_cat', $resource_cat );
                                echo '<a href="' . $topic_link . '">' . salud_get_taxonomy_images( $resource_cat, 'sa_resource_cat' ) . '</a>';
                                ?>
                                <h4 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
                            </header>
                            <div class="entry-content">
                                <?php the_excerpt();?>
                            </div> <!-- End .entry-content -->
                            <?php
                        else:
                            // Open a ul for the second through nth posts
                            if ( $resources_results->current_post == 1 ) { ?>
                                <h4>Other Resources</h4>
                                <ul class="related-posts no-bullets">
                            <?php
                            }
                            ?>
                                <li>
                                  <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                </li>
                            <?php
                            // Close the ul after the last post
                            if ( $resources_results->current_post == ( $resources_results->post_count - 1 ) ) { ?>
                                </ul>
                                <?php
                            }
                        endif;
                            //Add each displayed post to the do_not_duplicate array
                            $do_not_duplicate[] = get_the_ID();
                        endwhile; ?>
                </div> <!-- End <?php echo $block_class; ?> '-->
            <?php
        endif;
    } // Ends foreach for four top blocks
    wp_reset_query();
}
/**
 * Generate list of top resources in each topic.
 *
 * @since   1.0.0
 *
 * @param   string slug of requested resource cat
 * @return  string The html for the block
 */
// @TODO: is this used?
function saresources_by_cat( $resource_cat ) {
    $args = array(
      'post_type' => 'saresources',
      'sa_resource_cat' => $resource_cat,
      );
    $resources_results = new WP_Query( $args );
    if ( $resources_results->have_posts() ) : ?>
        <div class="<?php echo $block_class; ?>">
        <?php
        while ( $resources_results->have_posts() ) : $resources_results->the_post();
            bp_get_template_part( 'groups/single/saresources/resource-short' );
        endwhile;
    endif;
}
// @TODO: probably not used?
function saresources_get_related_resources($resource_cats) {
    wp_reset_postdata();
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
    'post_type' => 'saresources',
    'showposts' => '4',
    'paged' => $paged,
    'tax_query' => array(
                    array(
                     'taxonomy' => 'sa_resource_cat',
                     'field' => 'slug',
                     'terms' => $resource_cats
                    )
                 )
    );
    $list_of_policies = new WP_Query( $args );

    while ( $list_of_policies->have_posts() ): $list_of_policies->the_post();
        bp_get_template_part( 'groups/single/saresources/resource-short' );
    endwhile; // end of the loop.
}

/**
 * Parses the location of a salud policy or resource to a human-readable output
 *
 * @since   1.0.0
 *
 * @return  string human-readable name of geography
 */
function salud_the_location() {
  echo salud_get_the_location();
}
    function salud_get_the_location() {
        $geo_tax_type = cc_get_the_geo_tax_type();

        switch ($geo_tax_type) {
            case 'State':
                $geo_tax_location =  cc_get_the_geo_tax_state();
            break;
            case 'County':
            case 'City':
            case 'School District':
            case 'US Congressional District':
            case 'State House District':
            case 'State Senate District':
                $geo_tax_location = cc_get_the_geo_tax_name() . ', ' . cc_get_the_geo_tax_state();
            break;
            default:
                $geo_tax_location = 'United States';
            break;
        }

         return $geo_tax_location;
    }

/**
 * Create icons from the advocacy targets of a salud policy or resource
 *
 * @since   1.0.0
 *
 * @return  html used to show icon
 */
function salud_the_target_icons() {
  echo salud_get_the_target_icons();
}
    function salud_get_the_target_icons() {
        $terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
        $output = '';
        if ( ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                $output .= '<span class="' . $term->slug . 'x30" title="' . $term->name . '"></span>';
            }
        }
        return $output;
    }

/**
 * Create all six advocacy target icons with links to the taxonomy archive
 *
 * @since   1.0.0
 * @param   string $section used to incorporate correct section in link
 * @param   int $columns  number of columns to arrange icons in
 * @param   int $icon_size Size of icons to use, in px. Will be converted to 30, 60 or 90.
 * @return  html used to show icon
 */
function sa_advocacy_target_icon_links( $section = 'changes', $columns = 3, $icon_size = 90 ) {
    $class = sa_get_classname_from_columns( $columns );
    $cpt = sa_get_cpt_by_section( $section );
    // Convert all requests to 30, 60 or 90.
    switch ( $icon_size ) {
        case ( $icon_size < 46 ) :
            $icon_size = 30;
            break;
        case ( $icon_size < 76 ) :
            $icon_size = 60;
            break;
        default:
            $icon_size = 90;
            break;
    }

    $advocacy_targets = get_terms('sa_advocacy_targets');
    foreach ($advocacy_targets as $target) {
        ?>
        <div class="<?php echo $class; ?> mini-text"><a href="<?php sa_the_cpt_tax_intersection_link( $cpt, 'sa_advocacy_targets', $target->slug ) ?>" title="<?php echo $target->description; ?>"><span class="<?php echo $target->slug . 'x' . $icon_size; ?>"></span><br /><?php echo $target->name; ?></a></div>
    <?php } //end foreach
}

/**
 * Output html for taxonomy-related images
 *
 * @since   1.0.0
 * @uses    cc_get_taxonomy_images()
 *
 * @param   string $category slug of taxonomy term
 * @param   string $taxonomy name of taxonomy to search
 * @return  html used to show image
 */
function salud_get_taxonomy_images( $category, $taxonomy ){
    $cat_object = get_term_by( 'slug', $category, $taxonomy );
    $section_title = $cat_object->name;

    $output .= '<div class="sa-resource-header-icon"><span>' . $section_title . '</span>';
    $output .= cc_get_taxonomy_images( $category, $taxonomy );
    $output .= '</div>';

    return $output;
}

/**
 * Output html for a small recent posts loop, like on the group home page.
 *
 * @since   1.0.0
 *
 * @param   int $columns Number of columns the posts should be displayed in.
 * @param   int $numposts How many posts to fetch.
 * @return  html The blocks and their contents.
 */
function sa_recent_posts_loop( $section = 'policies', $columns = 3, $numposts = 3 ){
    //Grab the possible advocacy targets
    $advocacy_targets = get_terms( 'sa_advocacy_targets' );
    $possible_targets = array();
    foreach ( $advocacy_targets as $target ) {
        $possible_targets[] = $target->slug;
    }
    $class = sa_get_classname_from_columns( $columns );
    $cpt = sa_get_cpt_by_section( $section );

    // Grab the N most recent policies
    $args = array (
            'post_type' => $cpt,
            'posts_per_page' => $numposts,
        );
    $recent_posts = new WP_Query( $args );

    if ( $recent_posts->have_posts() ) {
        while ( $recent_posts->have_posts() ) {
            $recent_posts->the_post();
            ?>
            <div class="<?php echo $class; ?>">
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                <?php
                if ( has_post_thumbnail()) {
                    // Use the post thumbnail if it exists
                    the_post_thumbnail('feature-front-sub');
                } else {
                    // Otherwise, use some stand-in images by advocacy target
                    sa_the_advocacy_target_thumbnail( get_the_ID(), $possible_targets, 300 );
                }
                ?>
                <h5 class="entry-title"><?php the_title(); ?></h5></a>
                <?php
                if ( $cpt == 'sapolicies' ){
                    the_excerpt();
                    }
                ?>
            </div>
            <?php
        } // endwhile $recent_policies->have_posts()
    } // endif $recent_policies->have_posts()
    wp_reset_postdata();
}

/**
 * Output html for a generic advocacy target thumbnail.
 *
 * @since   1.0.0
 *
 * @param   int $columns Number of columns the posts should be displayed in.
 * @param   array $possible_targets Array of unused advocacy targets
 * @param   int $numposts How many posts to fetch.
 * @return  html The blocks and their contents.
 */
function sa_the_advocacy_target_thumbnail( $post_id = null, &$possible_targets = array(), $width = 300 ) {
    if ( is_null( $post_id ) ) {
        $post_id = get_the_ID();
    }
    $advo_target = '';

    // We're trying to find a good stand-in image based on the advocacy targets of the post.
    $terms = get_the_terms( $post_id, 'sa_advocacy_targets' );
    if ( ! empty( $terms ) ) {
        // Loop through the post terms to find a usable (unique) image
        foreach ( $terms as $term ) {
            if ( in_array( $term->slug, $possible_targets ) ) {
                $advo_target = $term->slug;
                break;
            }
        }
    } // end check for empty terms

    // If an advo_target didn't get set, we'll set one at random
    if ( empty( $advo_target ) ) {
        $advo_target = current( $possible_targets );
    }
    // Delete the term we used from the possible values for future use.
    $key_to_delete = array_search( $advo_target, $possible_targets );
    if ( false !== $key_to_delete ) {
        unset( $possible_targets[$key_to_delete] );
    }

    ?>
    <img src="<?php echo sa_get_plugin_base_uri() . 'public/images/advocacy_targets/' . $advo_target . 'x' . $width; ?>.jpg" >
    <?php
}

/**
 * Output html for a tag list by advocacy target for changes.
 *
 * @since   1.0.0
 *
 * @return  html The blocks and their contents.
 */
function sa_what_is_change_tag_list() {
    $tag_list = array(
        'Active Play' => array( 'Recess', 'PE', 'After School Programs', 'Safe Routes to School', 'Brain Breaks'  ),
        'Active Spaces' => array( 'Parks','Shared Use','Playgrounds', 'Complete Streets', 'Sidewalks' ),
        'Better Food in Neighborhoods' => array( 'Corner Stores', 'Farmers\' Markets', 'Community Gardens' ),
        'Healthier Marketing' => array( 'Healthy Ad Campaigns', 'Unhealthy Ad Campaigns', 'Digital Advertising', 'TV Advertising', 'Neighborhood Advertising' ),
        'Healthier School Snacks' => array( 'Healthy Lunches', 'Fundraising', 'School Wellness Policies' ),
        'Sugary Drinks' => array( 'Sugar-Sweetened Beverages', 'Soda Tax', 'Water' )
        );

    $i = 1;

    foreach ($tag_list as $advo_target => $tags) {

        //Start the row on i=1 and i=4
        if ( $i%3 == 1 )
            echo '<div class="row clear">';

        $advo_clean = sanitize_title( $advo_target );
        ?>

        <div class="third-block">
          <h4 class="clear"><span class="sa-<?php echo $advo_clean; ?>x60"></span><?php echo $advo_target; ?></h4>
          <ul class="no-bullets clear">
            <?php //Loop through the tags.
            foreach ($tags as $tag_candidate) {
                //Need to search for the correct term
                $tag = get_term_by( 'name', $tag_candidate, 'sa_policy_tags' );
                if ( $tag ) {
                ?>
                    <li><a href="<?php sa_the_cpt_tax_intersection_link( 'sapolicies', 'sa_policy_tags', $tag->slug ); ?>" title="Link to <?php echo $tag->name; ?> topic archive"><?php echo $tag->name; ?></a></li>
                <?php
                } // End check for $tag match
            }
            ?>
          </ul>
        </div>

        <?php
        //End the row on i=3 and i=6
        if ( $i%3 == 0 )
            echo '</div> <!-- end .row -->';

        $i++;

    } // END foreach ($tag_list as $advo_target => $tags)
}