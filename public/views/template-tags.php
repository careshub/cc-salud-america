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
 * Generate archive navigation within Salud America.
 *
 * @since   1.0.0
 *
 * @param   string $html_id The id to apply to the nav
 * @param   int $paged The current page number
 * @param   int $paged The total number of pages of results
 * @return  string The html for the nav block
 */
function sa_section_content_nav( $html_id, $total_pages = 1 ) {
    $html_id = esc_attr( $html_id );

    if ( $total_pages > 1 ) : ?>
        <nav id="<?php echo $html_id; ?>" class="sa-archive-navigation" role="navigation">
            <h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
            <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'cc-salud-america' ), $total_pages ); ?></div>
            <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'cc-salud-america' ), $total_pages ); ?></div>
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
    // Let's store the old global post, because wp_reset_postdata() goes way back, to the page that theme compat is using.
    global $post;
    $stored_post = $post;

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
    // Put the post global back.
    $post = $stored_post;
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
function salud_the_location( $cpt = 'sapolicies' ) {
  echo salud_get_the_location( $cpt );
}
    function salud_get_the_location( $cpt = 'sapolicies' ) {
        $location = 'United States';

        if ( 'sapolicies' == $cpt ) {
            // Policies use a very fine-grained location setup.
            $geo_tax_type = cc_get_the_geo_tax_type();

            switch ($geo_tax_type) {
                case 'State':
                    $location =  cc_get_the_geo_tax_state();
                break;
                case 'County':
                case 'City':
                case 'School District':
                case 'US Congressional District':
                case 'State House District':
                case 'State Senate District':
                    $location = cc_get_the_geo_tax_name() . ', ' . cc_get_the_geo_tax_state();
                break;
                default:
                    // Leave the default value of united states.
                break;
            }
        } elseif ( 'sa_success_story' == $cpt ) {
            // Heroes use a simple "Location" entry box.
            $location_string = get_post_meta( get_the_ID(), 'sa_success_story_location', true );
            if ( ! empty( $location_string ) ) {
                $location = $location_string;
            }
        } elseif ( 'saresources' == $cpt ) {
            // Resources don't have locations at all. @TODO: Should they?
            // Leave the default value of united states.
        }

         return $location;
    }

/**
 * Create icons from the advocacy targets of a salud policy or resource
 *
 * @since   1.0.0
 *
 * @return  html used to show icon
 */
function salud_the_target_icons( $post_id = 0, $size = '30', $include_name = false  ) {
  echo salud_get_the_target_icons( $post_id, $size, $include_name );
}
    function salud_get_the_target_icons( $post_id = 0, $size = '30', $include_name = false ) {
        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }

        $terms = get_the_terms( $post_id, 'sa_advocacy_targets' );
        $output = '';
        if ( ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                $output .= '<span class="target-icon"><span class="' . $term->slug . 'x' . $size . '" title="' . $term->name . '"></span>';
                if ( $include_name ) {
                    $output .= $term->name;
                }
                $output .= '</span>';
            }
        }
        return $output;
    }

/**
 * Create icons from the advocacy targets for a salud policy single view
 *
 * @since   1.2.0
 *
 * @return  html used to show icon(s)
 */
function salud_the_policy_target_icons( $post_id = 0 ) {
  echo salud_get_the_policy_target_icons( $post_id );
}
    function salud_get_the_policy_target_icons( $post_id = 0 ) {
        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }

        $terms = get_the_terms( $post_id, 'sa_advocacy_targets' );
        if ( ! is_array( $terms ) ) {
            return;
        }
        $num_terms = count( $terms );

        // What we return depends on how many terms there are.
        $output = '';
        if ( $num_terms > 1 ) {
            $items = array();
            foreach ( $terms as $term ) {
                $link = sa_get_section_permalink( 'big_bets' ) . $term->slug;
                $items[] = '<a href="' . $link . '" class="target-icon alignleft" style="width:99%;"><span class="' . $term->slug . 'x30" title="' . $term->name . '"></span>' . $term->name . '</a>';
                $output = implode( '<br />', $items );
            }
        } elseif ( $num_terms = 1 ) {
            $term = current( $terms );
            $link = sa_get_section_permalink( 'big_bets' ) . $term->slug;
            $output .= '<div class="advo-target-thumbnail-link size90"><a href="' . $link . '" class="' . $term->slug . 'x90" title="' . $term->name . '"></a></div><p><strong>' . $term->name;
            $output .= '</strong><br /><span class="policy-header-meta">See what else is happening in <a href="' . $link . '" title="Link to ' . $term->name . ' term archive">this topic!</a></span></p>';

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
    global $post;
    $stored_post = $post;

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
    $post = $stored_post;
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
    echo sa_get_the_advocacy_target_thumbnail( $post_id, $possible_targets, $width );
}
    function sa_get_the_advocacy_target_thumbnail( $post_id = null, &$possible_targets = array(), $width = 300 ) {
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

        $retval = '<img src="' . sa_get_plugin_base_uri() . 'public/images/advocacy_targets/' . $advo_target . 'x' . $width .'jpg" >';
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

/**
 * Create a human readable list of tags and advocacy targets for a post
 *
 * @since 1.2.0
 * @param int $post_id ID of post in question
 *
 * @return string HTML of term meta list
 */
function sa_post_terms_meta( $post_id, $post_type ) {
    echo sa_get_post_terms_meta( $post_id, $post_type );
}
    function sa_get_post_terms_meta( $post_id, $post_type = 'sapolicies' ) {
        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $terms = get_the_terms( $post_id, 'sa_advocacy_targets' );
        $advocacy_targets = array();
        if ( ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( $post_type, $term->taxonomy, $term->slug ) . '" class="big-bet-link ' . sa_get_topic_color ( $term->slug ). '">' . $term->name . '</a>';
            }
            $advocacy_targets = join( ' ', $advocacy_targets );
        }

        $tags = get_the_terms( $post_id, 'sa_policy_tags' );
        $policy_tags = array();
        if ( ! empty( $tags ) ) {
            foreach ( $tags as $tag ) {
                $policy_tags[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( $post_type, $tag->taxonomy, $tag->slug ) . '">' . $tag->name . '</a>';
            }
            $policy_tags = join( ' ', $policy_tags );
        }


        if ( ! empty( $advocacy_targets ) || ! empty( $policy_tags ) ) {
            $item_type = sa_get_label_by_cpt( $post_type );
            ?>
            <div class="taxonomy-links">
                <h5 style="margin:0;">Browse similar <?php echo ucfirst( $item_type ); ?></h5>
                <?php
                if ( ! empty( $advocacy_targets ) ) {
                    ?>
                    <p class="sa-policy-meta">By Big Bet:
                        <?php echo $advocacy_targets; ?>
                    </p>
                    <?php
                }

                if ( ! empty( $policy_tags ) ) {
                    ?>
                    <p class="sa-policy-meta">By Tag:
                        <span class="tag-links"><?php echo $policy_tags; ?></span>
                    </p>
                    <?php
                }
                ?>
            </div>
            <?php
        }

    }

/**
 * Output a list of links to post-type/advocacy-target intersection views.
 *
 * @since 1.6.1
 *
 * @param string $section Which tab the links should point to.
 *
 * @return string HTML Unordered list of links.
 */
function sa_post_type_archive_big_bet_filters( $section = 'changes' ) {
    ?>
    <div class="archive-filter-container background-light-gray">
        <h5 class="filter-label">Filter by topic:</h5>
        <ul class="topic-filter-links">
    <?php
        $advocacy_targets = get_terms( 'sa_advocacy_targets' );
        foreach ( $advocacy_targets as $advo_target ) {
            ?>
            <li>
                <a href="<?php sa_the_cpt_tax_intersection_link( $section, 'sa_advocacy_targets', $advo_target->slug ); ?>"><span class="target-icon"><span class="<?php echo $advo_target->slug; ?>x30" title="<?php echo $advo_target->name; ?>"></span><?php echo $advo_target->name; ?></a>
            </li>
            <?php
        }
    ?>
        </ul>
    </div>
    <?php
}

/**
 * Create a human readable "posted by" string
 *
 * @since 1.2.0
 * @param int $post_id ID of post in question
 * @param string $wrapper HTML element to wrap the output in.
 *
 * @return string HTML of when the article was posted and by whom.
 */
function sa_post_date_author( $post_id, $wrapper ) {
    echo sa_get_post_date_author( $post_id );
}
    function sa_get_post_date_author( $post_id, $wrapper = 'p' ) {
        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $output = '';

        $date = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>',
        esc_attr( get_the_date( 'c', $post_id ) ),
        esc_html( get_the_date( '', $post_id ) )
        );

        $author_id = get_the_author_meta( 'ID' );
        $author_name = bp_core_get_user_displayname( $author_id );
        $author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
        // esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        //Use the BuddyPress profile instead
        esc_url( bp_core_get_user_domain( $author_id ) ),
        esc_attr( sprintf( __( 'View all posts by %s', 'twentytwelve' ), $author_name ) ),
        $author_name
        );

        if ( $date && $author ) {
            $output = '<' . $wrapper . ' class="sa-policy-date">Posted on ' . $date . '<span class="by-author"> by ' . $author . '</span>.' . '</' . $wrapper . '>';
        }

        return $output;
    }

/**
 * Output html for the content-by-advocacy-target area on the group's home page.
 *
 * @since   1.0.0
 *
 * @return  html
 */
function sa_tabbed_content_by_adv_target() {
    $advocacy_targets = get_terms('sa_advocacy_targets');
    if ( empty( $advocacy_targets ) ) {
        return;
    }
    $advocacy_target_slugs = wp_list_pluck( $advocacy_targets, 'slug' );

    // We use the GET value 'recent_items_topic' to choose which term to show.
    // If that's not set, we prime these blocks with content from a randomly selected term.
    if ( isset( $_GET['recent_items_topic'] ) && in_array( $_GET['recent_items_topic'], $advocacy_target_slugs ) ) {
        //Find the term with the matching slug.
        foreach ( $advocacy_targets as $target ) {
           if (  $_GET['recent_items_topic'] == $target->slug ) {
                $primer_term = $target;
                break;
           }
        }
    } else {
        $primer_term = $advocacy_targets[ array_rand( $advocacy_targets ) ];
    }
    $icon_size = 60;

    // Get the most recent of each type of item in this taxonomy term:
    $recent_items = sa_get_most_recent_items_by_big_bet( $primer_term->slug );

    ?>
    <div class="screamer sablue">Find changes, resources, and Salud Heroes&hellip;then START YOUR OWN CHANGE!</div>
    <!-- <div class="recent-posts-section content-row clear"> -->
    <div class="recent-posts-section Grid Grid--guttersLg Grid--full large-Grid--1of4">

        <!-- <div class="quarter-block compact topic-selector"> -->
        <div class="Grid-cell topic-selector">
            <div class="cell-liner">
                <div class="entry-header">
                    <!-- <span class="icon sa-change"></span> --><h4 class="block-header">Topics</h4>
                    <span class="header-description">Our Focal Areas</span>
                </div>
                <ul class="no-bullets toggle-list" id="topic-toggle">
                <?php
                foreach ( $advocacy_targets as $target ) {
                    ?>
                    <li class="toggle-list-item clear">
                        <a href="?recent_items_topic=<?php echo $target->slug; ?>" title="<?php echo $target->description; ?>" class="Grid toggle<?php
                            if ( $target->term_id == $primer_term->term_id ) {
                                echo ' active';
                            } else {
                                echo ' inactive';
                            }
                            ?>" id="<?php echo $target->slug; ?>">
                            <div class="advo-target-icon-cell Grid-cell Grid-cell--autoSize"><span class="advo-target-icon <?php echo $target->slug . 'x' . $icon_size; ?>"></span></div>
                            <div class="advo-target-name-cell Grid-cell Grid-cell--center"><span class="advo-target-name"><?php echo $target->name; ?></span></div>
                            <div class="working-indicator Grid-cell Grid-cell--autoSize Grid-cell--center"><img src="<?php echo sa_get_plugin_base_uri() . 'public/images/ajax-loader.gif' ?>" style="padding-right:.3em;"/></div>
                        </a>
                    </li>
                <?php } //end foreach
                ?>
                </ul>
            </div>
        </div>
        <?php
        $blocks = array( 'sapolicies', 'saresources', 'sa_success_story' );
        $exclude_ids = array();

        foreach ( $blocks as $block ) {
            $exclude_ids[] = $recent_items['posts'][$block]['post_id'];
            ?>
            <!--<div class="quarter-block compact" id="most-recent-<?php echo str_replace( ' ', '-', sa_get_label_by_cpt( $block ) ) ; ?>"> -->
            <div class="Grid-cell recent-item-cell <?php echo $block; ?>" id="most-recent-<?php echo str_replace( ' ', '-', sa_get_label_by_cpt( $block ) ) ; ?>">
                <div class="cell-liner">
                    <div class="entry-header">
                            <!-- <span class="icon sa-change"></span> --><h4 class="block-header post-type-flag <?php echo $block; ?>"><a href="<?php echo sa_get_section_permalink( $block ); ?>"><?php echo ucwords( sa_get_label_by_cpt( $block ) ); ?></a></h4>
                            <span class="header-description"><?php
                                switch ( $block ) {
                                    case 'saresources':
                                        echo 'To Help You Make a Change';
                                        break;
                                    case 'sa_success_story':
                                        echo 'Follow the Steps of Change-makers';
                                        break;
                                    default:
                                        echo 'New Healthy Policies';
                                        break;
                                }
                            ?></span>
                    </div>
                    <?php // The following div is what the JS will be building from the front end. ?>
                    <div class="entry-content <?php echo $block . ' ' . $target->slug . ' ' . $recent_items['posts'][$block]['post_id']; ?>">
                        <?php
                        // Thumbnail
                        echo '<a href="' . $recent_items['posts'][$block]['permalink'] . '">' . $recent_items['posts'][$block]['thumbnail'] . '</a>';
                        // Title
                        echo '<h5 class="post-title"><a href="' . $recent_items['posts'][$block]['permalink'] . '">' . $recent_items['posts'][$block]['title'] . '</a></h5>';
                        // Excerpt
                        echo '<p class="excerpt">' . $recent_items['posts'][$block]['excerpt'] . ' <a href="' . $recent_items['posts'][$block]['permalink'] . '">Read More</a></p>';
                        ?>
                    </div>
                    <div class="entry-footer">
                        <a href="<?php echo sa_get_section_permalink( $block ); ?>" class="button">See All <?php echo ucwords( sa_get_label_by_cpt( $block ) ); ?></a>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div> <!-- End .content-row -->
    <input type="hidden" id="sa-recent-items-exclude-ids" value="<?php echo implode( ',', $exclude_ids ); ?>">
    <!-- We include the following template file for use by JS -->
    <script type="text/html" id="tmpl-salud-recent-items-block">
        <div class="entry-content {{data.post_type}} {{data.term_slug}} {{data.post_id}}" style="display:none;">
            <a href="{{{data.permalink}}}">{{{data.thumbnail}}}</a>
            <h5 class="post-title"><a href="{{{data.permalink}}}">{{{data.title}}}</a></h5>
            <p class="excerpt">{{{data.excerpt}}} <a href="{{{data.permalink}}}">Read More</a></p>
        </div>
    </script>
    <?php
}

/**
 * Output html for the single post header meta.
 * Includes taxonomy, location and the "sign up now" blocks.
 *
 * @since   1.2.0
 * @param   int $post_id ID of post to process meta for.
 *
 * @return  html
 */
function sa_single_post_header_meta( $post_id = 0 ) {
    if ( empty( $post_id ) ) {
        $post_id = get_the_ID();
    }
    // Get the post_type
    $post_type = get_post_type( $post_id );
    $user_id = get_current_user_id();
    $is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

    // Default map buffer setting
    $buffer = 10;

    // Set up the location
    switch ( $post_type ) {
        case 'sa_success_story':
            // Set up the location.
            $location_meta = get_post_meta( $post_id, 'sa_success_story_location', true );
            $location = ( ! empty( $location_meta ) ) ? $location_meta : 'United States';
            $map_link = sa_get_policy_map_base_url( '?address=' . urlencode( $location ) );
            $geo_id = '01000US';
        break;
        case 'sapolicies':
        case 'saresources':
        default:
            // Note that saresources don't have locations yet, so this may change.
            $geo_terms = get_the_terms( $post_id, 'geographies' );
            // Get the GeoID if possible, else use the whole US
            // var_dump($geo_terms);
            $geo_id = ( ! empty( $geo_terms ) ) ? current( $geo_terms )->description : '01000US';
            $map_link = sa_get_policy_map_base_url( '?geoid=' . $geo_id );
            if ( 'City' == cc_get_the_geo_tax_type( $post_id ) ) {
                $buffer = 85;
            }
            break;
    }

    ?>
    <div class="Grid Grid--full large-Grid--fit">
        <div class="Grid-cell background-light-gray">
            <div class="inset-contents">
                <?php salud_the_policy_target_icons( $post_id ); ?>
            </div>
        </div>
        <div class="Grid-cell background-light-gray">
            <div class="inset-contents">
                <a href="<?php echo $map_link; ?>" title="See recent changes, resources and Salud Hero stories on a map." class="tiny-policymap-container">
                    <script src="//maps.communitycommons.org/jscripts/mapWidget.js?vr=natgeo&w=90&h=90&maplink=0&buffer=<?php echo $buffer; ?>&geoid=<?php echo $geo_id; ?>" ></script>
                </a>
                <p><strong class="meta-action"><?php salud_the_location( $post_type ); ?></strong><br />
                <span class="policy-header-meta">See all changes, resources, and Salud Heroes in <a href="<?php echo $map_link; ?>" title="See recent changes, resources and Salud Hero stories on a map.">this area</a>!</span></p>
            </div>
        </div>
        <?php
        if ( ! $user_id || ! $is_sa_member ) {
            ?>
            <div class="Grid-cell background-light-gray">
                <div class="inset-contents">
                    <img src="<?php echo sa_get_plugin_base_uri() . 'public/images/how-you-can-get-involved-90x90.jpg' ; ?>" class="alignleft" style="margin-top:0;"></a>
                    <p><strong>Get involved!</strong><br />
                    <span class="policy-header-meta">Become a Salud Leader and connect with others!</span><br />
                    <?php if ( ! $user_id ) : ?>
                        <a href="/register/?salud-america=1" title="Register Now" class="button" style="margin-top:.6em;text-shadow:none;">Register Now</a></div>
                    <?php elseif ( ! $is_sa_member ) : ?>
                        <div class="aligncenter" style="text-shadow:none;"><?php bp_group_join_button(); ?></div>
                    <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

/**
 * Output html for the policy map and pitch box on the group home page.
 * This is used via a shortcode: [sa_policy_map_widget_and_pitchbox].
 *
 * @since   1.2.0
 *
 * @return  html
 */
function sa_the_home_page_map_and_pitchbox() {
    $user_id = get_current_user_id();
    $is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );
    ?>
    <div class="content-row clear">
        <?php if ( ! $user_id || ! $is_sa_member ) :
            // We show two columns if we're pitching the user.
        ?>
        <div class="third-block compact spans-2">
            <?php sa_the_policy_map_widget(); ?>
        </div>
        <div id="sa-join-group-action-call" class="third-block compact fill-height">
            <?php sa_get_the_home_page_join_pitch_box( $user_id, $is_sa_member ); ?>
        </div>
        <?php else:
            // If the user's already a group member, we can skip the pitch.
        ?>
            <?php sa_the_policy_map_widget(); ?>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Output html for the policy map on the group home page.
 * This can be used via a shortcode [sa_policy_map_widget], but is
 * also called internally in sa_the_home_page_map_and_pitchbox().
 *
 * @since   1.2.0
 *
 * @return  html
 */
function sa_the_policy_map_widget( $hero_story_id = 0 ) {
    $story_id_arg = '';
    if ( ! empty( $hero_story_id ) ) {
        $story_id_arg = '&story_id=' . $hero_story_id;
    }
    // base_map_widget_src
    echo '<div id="map-widget-container" class="sa-policy-map-widget"></div>';
    echo '<script type="text/javascript">
            var base_map_widget_src = "//maps.communitycommons.org/jscripts/mapWidget.js?interface=policymap' . $story_id_arg . '";
        </script>';
}

/**
 * Output html for the policy map on the group home page.
 * This called internally in sa_the_home_page_map_and_pitchbox().
 *
 * @since   1.2.0
 *
 * @return  html
 */
function sa_get_the_home_page_join_pitch_box( $user_id = null, $is_sa_member = null ) {
    if ( is_null( $user_id ) ) {
        $user_id = get_current_user_id();
    }
    if ( $user_id && is_null( $is_sa_member ) ) {
        $is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );
    }

?>
    <div class="sa-join-group-prop background-sablue" style="">
        <?php if ( ! $user_id ) : ?>
            <span class="sa-login-register-cta"><a href="/register/?salud-america=1" title="Register Now">Register</a> or login</a> now&hellip;</span>
            <form name="login-form" id="salud-front-page-login-form" class="salud-front-page-login-form standard-form" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
                <label><?php _e( 'Username or email', 'buddypress' ) ?><br />
                <input type="text" name="log" id="front-page-user-login" class="full-width-input input" value="" tabindex="" /></label>

                <label><?php _e( 'Password', 'buddypress' ) ?><br />
                <input type="password" name="pwd" id="front-page-user-pass" class="full-width-input input" value="" tabindex="" /></label>

                <input type="submit" name="wp-submit" id="front-page-wp-submit" value="<?php _e( 'Log In', 'buddypress' ); ?>" tabindex="100" />
                <input type="hidden" name="redirect_to" value="<?php echo ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ?>" />
            </form>
        <?php elseif ( ! $is_sa_member ) : ?>
            <div class="aligncenter" style="text-shadow:none;"><?php bp_group_join_button(); ?></div>
            <hr />
        <?php endif; ?>
        <span class="sa-login-register-cta">&hellip;get tools for healthy change.</span>
        <ul class="sa-join-group-reasons no-bullets">
            <li><span class="li-map-connect"></span>&emsp;<a href="http://maps.communitycommons.org/policymap/">Hop on our map and connect</a>!</li>
            <li><span class="li-changes-heroes"></span>&emsp;See new <a href="<?php echo sa_get_section_permalink( 'policies' ); ?>">changes</a> and <a href="<?php echo sa_get_section_permalink( 'heroes' ); ?>">heroes</a>!</li>
            <li><span class="li-customize-data"></span>&emsp;<a href="<?php echo sa_get_group_permalink(); ?>report-card/">Customized data for you</a>!</li>
            <?php /* ?><li><span class="li-share-story"></span>&emsp;<a href="/groups/salud-america/share-your-story/">Share your change</a>!</li><?php */ ?>
        </ul>
    </div>
<?php
}

/**
 * Output the concatenated notices.
 * This can be used via a shortcode [sa_homepage_notices]
 *
 * @since   1.2.0
 *
 * @return  html
 */
function sa_the_homepage_notices() {
    // This is like standing on a hillside shouting, "To me, to me!"
    do_action( 'sa_build_home_page_notices' );
}

/**
 * Add an introduction before the group members list.
 *
 * @since   1.3.0
 *
 * @return  html
 */
add_action( 'bp_before_group_members_content', 'sa_before_member_list_intro' );
function sa_before_member_list_intro() {
    if ( ! sa_is_sa_group() ) {
        return;
    }
    ?>
    <h5><em>Salud America!</em> Salud Leaders are parents, teachers, researchers, and leaders like you who are interested in starting and supporting healthy change for Latino kids across the country.</h5>

    <p>Salud Leaders can <a href="/register/?salud-america=1" title="Register Now">register</a> or <a href="<?php echo wp_login_url( sa_get_group_permalink() ); ?>" title="Log in">log in</a> to our website to:</p>
    <ol>
        <li>Get a place on our Salud Leader national map.</li>
        <li>Contact other Salud Leaders for support and advice.</li>
        <li>Get a customized Salud Leader Report to make a case for a change.</li>
        <li>Get technical assistance.</li>
        <li>Share your own change&hellip;and move from being a Salud Leader to a Salud Hero!</li>
    </ol>
    <?php
}

/**
 * Add an introduction before the group activity stream.
 *
 * @since   1.3.0
 *
 * @return  html
 */
add_action( 'bp_before_group_activity_content', 'sa_before_activity_list_intro' );
function sa_before_activity_list_intro() {
    if ( ! sa_is_sa_group() ) {
        return;
    }
    ?>
    <h5>Here’s the latest activity on the <em>Salud America!</em> Hub to drive healthy changes:</h5>
    <?php
}

/**
 * Template tag to output a search box that sends queries to the SA search tab.
 *
 * @since   1.4.0
 * @param   string $placeholder  Text to use as form input placeholder.
 *
 * @return  html
 */
function salud_hub_search_form( $placeholder = "Search" ) {
    // Grab user-entered search terms.
    $search_terms = get_query_var('s') ? get_query_var('s') : '';
    ?>
    <div class="salud-search-box">
        <form id="salud-hub-search" method="get" action="<?php echo sa_get_section_permalink( 'search' ); ?>">
            <input id="salud-hub-search-text" class="salud-search-input searchx18" type="search" maxlength="150" value="<?php echo $search_terms; ?>" name="s" placeholder="<?php echo $placeholder; ?>">
            <input class="salud-hub-search-button" type="submit" value="Search">
        </form>
    </div>
    <?php
}

/**
 * Output html for the ticker marquee on the group's home page.
 *
 * @since   1.6.0
 *
 * @return  html
 */
function sa_ticker() {
    ?>
    <div class="content-row" style="margin-top:2em;">
        <ul id="sa-ticker-marquee" class="sa-ticker-list">
            <li class="sa-ticker-placeholder sa-ticker-item"><span class="sa-ticker-item-content">&hellip;</span></li>
        </ul>
    </div>
    <?php /* We include the following template file for use by JS */ ?>
    <script type="text/html" id="tmpl-salud-ticker-items-block">
        <li id="sa-ticker-item-{{data.id}}" class="sa-ticker-item">
            <span class="sa-ticker-item-type-label" style="background-color:{{data.sa_ticker_item_leader_color}}">{{data.sa_ticker_item_leader_text}}</span>
            <span class="sa-ticker-item-content">
                <span class="date">{{{data.nice_date}}}</span> {{{data.title.rendered}}}
                <# if ( data.sa_ticker_item_link ) { #>
                    <a href="{{{data.sa_ticker_item_link}}}">Read More</a>
                <# } #>
            </span>
        </li>
    </script>
    <?php
}

/**
 * Output html for the welcome email template.
 *
 * @since   1.6.0
 *
 * @return  string
 */
function sa_welcome_email_template() {
ob_start();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
      a:link, a, a:visited{color:#008EAA !important;}
      a:hover { text-decoration: none !important; color:#008EAA;}
      h1, h2, h3, h4, h5, h6{padding: 0; margin: 0;}
    </style>
  </head>
  <body style="margin: 0; padding: 0; background: #f5f5f5; font-family:Helvetica, Arial, Sans-serif;line-height:22px;font-size:12px;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
    <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; background: #ffffff; margin-top: 2em; margin-bottom: 2em;" border="0" width="700" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
      <tbody>
        <tr>
          <td><a style="text-decoration: none;" href="%blog_url%" target="_blank"><img style="width: 65%; max-width: 720px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/cc_logo_horizontal_org.png" alt="" align="middle" /></a></td>
        </tr>
        <tr>
          <td style="padding: 12px 30px; border-bottom: 4px solid #f5f5f5; color: #444444;">
            <h4 style="font-size: 14px; font-weight: normal; line-height: 24px;">%blog_description%</h4>
          </td>
        </tr>
        <tr>
          <td style="padding: 12px 30px; border-bottom: 4px solid #f5f5f5; color: #444444;">
            <h4 style="font-size: 14px; font-weight: normal; line-height: 50px;"><img src="<?php echo sa_get_plugin_base_uri(); ?>/public/images/SA-logox50.png" style="margin-right:1em;display:inline-block;vertical-align:middle;">Welcome to the <em>Salud America</em> hub!</h4>
          </td>
        </tr>
        <tr>
          <td style="line-height: 19px; color: #111111; padding: 12px 30px; border-bottom: 4px solid #f5f5f5;">%content%</td>
        </tr>
        <tr>
          <td style="color: #bbbbbb; padding: 12px 30px;"><img style="padding-top: 0px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/cc_logomark-30x30.png" alt="Supporting collaboration through data, maps and stories." width="30" height="30" align="right" />
            <div style="padding-top: 8px; padding-bottom: 1px;">Email sent %date% @ %time%</div>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html><?php
return ob_get_clean();
}

/*
 * We accept signups from a few places. This adds a general-purpose quick sign up form.
 * The input is captured in CC_Salud_America::capture_join_group_submission()
 *
 * @since 1.8.0
 */
function sa_get_auxiliary_signup_form() {
    ?>
    <form action="" method="POST" enctype="multipart/form-data" name="salud-america-auxiliary-signup">
        <p style="margin-bottom:0.6em;"><strong>Join the hub <em>Salud America!</em></strong></p>
        <label style="margin-bottom:0.6em;"><input type="checkbox" name="join_salud_america_hub" id="join_salud_america_hub" value="agreed" checked="checked" /> Yes, I&rsquo;m interested in work by Salud America! to reduce Latino childhood obesity.</label><br />
        <label><input type="checkbox" name="salud_newsletter_acceptance" id="salud_newsletter_acceptance" value="agreed" checked="checked" /> I would like to receive email updates on this topic.</label>
        <p class="info" style="margin-bottom:0.6em;"><em>Periodically, Salud America! sends out news updates and brief surveys.</em></p>
        <?php wp_nonce_field( 'sa_auxiliary_group_join_submit_' . get_current_user_id() ); ?>
        <input type="submit" id="sa_auxiliary_group_join_submit" name="sa_auxiliary_group_join_submit" class="sa-cta-button" alt="Join Salud America!" value="Join Salud America!" />
    </form>
    <?php
}

/*
 * Use this function to protect actions behind SA hub membership.
 * Most likely used via the `sa_membership_conditional_box` shortcode.
 *
 * @since 1.8.2
 */
function sa_membership_conditional_box( $header_text, $login_button_text, $carrot_text, $content ) {
    ?>
    <div class="sa-membership-conditional-box clear background-light-gray">
        <h3 class="screamer sa-membership-conditional-box-header sagreen"><?php echo $header_text; ?></h3>
        <div class="inset-contents">
            <?php
            // User isn't logged in.
            if ( ! bp_loggedin_user_id() ) :
                ?>
                <p><a class="login-link sa-cta-button" href="<?php echo wp_login_url( ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ); ?>" title="Log in"><?php echo $login_button_text; ?></a><br /> If you don't have a free Community Commons account and would like to join us, please <a href="<?php echo site_url( bp_get_signup_slug() . '?salud-america=1' ); ?>"><strong>register</strong></a>.</p>
            <?php
            elseif ( ! sa_is_current_user_a_member() ) :
            ?>
                <p style="margin-bottom:0.6em;"><?php echo $carrot_text; ?></p>
                <?php echo sa_get_auxiliary_signup_form(); ?>
            <?php
            else :
                echo $content;
            ?>
            <script type="text/javascript">
                jQuery(document).ready( function( $ ) {
                    $( '.sa-track-kiss' ).on( 'click', function() {
                        var action = $( this ).data( 'track-action' );
                        // console.log( action );
                        // KISS tracking
                        _kmq.push(['record', 'sa-' + action]);
                    });
                });
            </script>
            <?php
            endif;
            ?>
        </div>
    </div>
    <?php
}
