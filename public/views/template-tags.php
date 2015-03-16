<?php
/**
 * Generate for the public-facing pieces of the plugin.
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