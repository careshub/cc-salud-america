<?php
/**
* Template used for displaying the take action tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

if ( sa_is_single_post() ){
    $petition = new WP_Query( sa_get_query() );

    while ( $petition->have_posts() ) : $petition->the_post();
        bp_get_template_part( 'groups/single/satakeaction/single' );
    endwhile;

} else {
    // Anything else is basically the same: Show any active contests, then show links to past contests.
    $petitions = new WP_Query( sa_get_query() );
    $total_pages = $petitions->max_num_pages;

    while ( $petitions->have_posts() ) : $petitions->the_post();
        bp_get_template_part( 'groups/single/satakeaction/single' );

        // A hook after the first post so that content can be injected from other components
        if ( $petitions->current_post == 0 ) {
            do_action( 'sa_after_first_take_action' );
        }
    endwhile;

    sa_section_content_nav( 'nav-below', $paged, $total_pages );

}