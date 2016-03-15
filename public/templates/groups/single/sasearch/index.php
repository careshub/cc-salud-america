<?php
/**
* Template used for displaying the search results page in the Salud America hub.
*/

// The complete list of post types that our query could pull from.
$post_types = array( 'sapolicies',  'saresources', 'sa_success_story', 'sa_take_action', 'sa_video_contest' );
// The complete list of Big Bets (Advocacy Targets).
$bets = get_terms( 'sa_advocacy_targets', array( 'hide_empty' => 0 ) );

// If the user has chosen topic or type filters, set them up.
$search_terms = get_query_var('s') ? get_query_var('s') : '';

$selected_bets = $_REQUEST['topic'] ? $_REQUEST['topic'] : array();
if ( ! empty( $selected_bets ) ) {
    $selected_bets =  explode( ',', $selected_bets );
}

$selected_post_types = $_REQUEST['type'] ? $_REQUEST['type'] : array();
if ( ! empty( $selected_post_types ) ) {
    $selected_post_types =  explode( ',', $selected_post_types );
}

// Empty containers we'll fill as we parse the user input, to build the WP_Query.
$query_post_types = array();
$query_topic_ids = array();
?>
<div class="taxonomy-policies">
    <h3 class="screamer sagreen">Search results for: <strong><?php echo $search_terms; ?></strong></h3>

    <div class="salud-search-page-search-form clear">
        <div class="salud-search-box">
            <form id="salud-hub-advanced-search" method="get" action="<?php echo sa_get_section_permalink( 'search' ); ?>">
                <input id="salud-hub-search-text" class="salud-search-input searchx18" type="search" maxlength="150" value="<?php echo $search_terms; ?>" name="s" placeholder="Search">
                <input id="salud-advanced-hub-search-submit" class="salud-hub-search-button" type="submit" value="Search"> <br/>
                <?php
                // These fields will be submitted as part of the form,
                // after being concatenated from the checkboxes below by JS.
                // The resulting query vars look like:
                // type=resources,policies instead of type[]=resources&type[]=policies
                ?>
                <input type="hidden" id="topic" name="topic">
                <input type="hidden" id="type" name="type">
            </form>
            <a href="#" id="toggle-advanced-search">More Search Options&hellip;</a>
            <div id="salud-search-advanced">
                <div class="half-block compact">
                    <h5 class="no-bottom-margin">Refine Search by Topic</h5>
                    <ul class="no-bullets compact">
                        <?php // Show advocacy targets.
                            foreach ( $bets as $bet ) {
                                $short_name = salud_get_bet_short_name( $bet->slug );
                                ?>
                                <li><input type="checkbox" name="topic[]" id="<?php echo $bet->slug; ?>" value="<?php echo $short_name; ?>" <?php
                                    if ( in_array( $short_name, $selected_bets ) ) {
                                        echo 'checked="checked"';
                                        // Also add checked terms to the array we'll use to build the WP_Query.
                                        $query_topic_ids[] = $bet->term_id;
                                    }
                                    ?>/> <label for="<?php echo $bet->slug; ?>"><?php echo $bet->name; ?></label></li>
                                <?php
                            }
                        ?>
                    </ul>
                </div>
                <div class="half-block compact">
                    <h5 class="no-bottom-margin">Refine Search by Type</h5>
                    <ul class="no-bullets compact">
                        <?php // Show post type options.
                            foreach ( $post_types as $type ) {
                                $short_type_name = salud_get_post_type_short_name( $type );
                                $short_type_slug = salud_get_post_type_short_slug( $type );
                                ?>
                                <li><input type="checkbox" name="type[]" id="post-type-<?php echo $short_type_slug; ?>" value="<?php echo $short_type_slug; ?>" <?php
                                    if ( in_array( $short_type_slug, $selected_post_types ) ) {
                                        echo 'checked="checked"';
                                        $query_post_types[] = $type;
                                    }
                                    ?>/> <label for="post-type-<?php echo $short_type_slug; ?>"><?php echo $short_type_name; ?></label></li>
                                <?php
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <?php
    // Build the query, based on our query args.
    $args = array( 'orderby' => 'date');

    // Search terms
    if ( ! empty( $search_terms ) ) {
        $args['s'] = $search_terms;
    }

    // Advanced search: Post type
    if ( ! empty( $query_post_types ) ) {
        // If the user has selected one or more post types, limit the query to those only.
        $args['post_type'] = $query_post_types;
    } else {
        // None selected, include them all.
        $args['post_type'] = $post_types;
    }

    // Advanced search: Big bet
    if ( ! empty( $query_topic_ids ) ) {
        // If the user has selected one or more topics, limit the query to those only.
        $args['tax_query'] = array(
                                array(
                                    'taxonomy' => 'sa_advocacy_targets',
                                    'field'    => 'term_id',
                                    'terms'    => $query_topic_ids,
                                ),
                            );
    }

    // Pagination
    $args['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;

    $items = new WP_Query( $args );
    $total_pages = $items->max_num_pages;

    if ( $items->have_posts() ) :
        while ( $items->have_posts() ) : $items->the_post();
            global $post;
            switch ( $post->post_type ) {
                case 'saresources':
                    $template_part = 'groups/single/saresources/resource-short-general';
                    break;
                case 'sa_success_story':
                    $template_part = 'groups/single/saheroes/hero-short-general';
                    break;
                case 'sa_take_action':
                    $template_part = 'groups/single/satakeaction/single-short';
                    break;
                case 'sa_video_contest':
                    $template_part = 'groups/single/savideocontests/single-short';
                    break;
                case 'sapolicies':
                default:
                    $template_part = 'groups/single/sapolicies/policy-short-general';
                    break;
            }
            bp_get_template_part( $template_part );
        endwhile;
    else :
        ?>
        <p>We didn't find anything matching your search terms. Try different search terms above.</p>
        <?php
    endif;

    sa_section_content_nav( 'nav-below', $total_pages );

    ?>
</div>
