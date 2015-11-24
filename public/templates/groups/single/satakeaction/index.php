<?php
/**
* Template used for displaying the take action tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$requested_page = bp_action_variable();
// Add a subnav for this section.
?>
        <div id="subnav" class="item-list-tabs no-ajax" role="navigation">
            <ul>
               <li<?php if ( $requested_page == 'current-actions' || empty( $requested_page ) ) {
                        echo ' class="current selected"';
                    } ?>>
                    <a href="<?php echo trailingslashit( sa_get_section_permalink( 'take_action' ) ) . 'current-actions' ; ?>">Current Actions</a>
                </li>
               <li<?php if ( $requested_page == 'past-actions' ) {
                        echo ' class="current selected"';
                    } ?>>
                    <a href="<?php echo trailingslashit( sa_get_section_permalink( 'take_action' ) ) . 'past-actions' ; ?>">Past Actions</a>
                </li>
            </ul>
        </div>
<?php

if ( sa_is_single_post() ){
    // BuddyPress forces comments closed on BP pages. Override that.
    remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );

    $item = new WP_Query( sa_get_query() );

    while ( $item->have_posts() ) : $item->the_post();
        // The post could be a petition or a video contest.
        // Show the right template.
        if ( 'sa_video_contest' == get_post_type() ) {
            bp_get_template_part( 'groups/single/savideocontests/single' );
        } else {
            bp_get_template_part( 'groups/single/satakeaction/single' );
        }
        comments_template();
    endwhile;

    // BuddyPress forces comments closed on BP pages. Put the filter back.
    add_filter( 'comments_open', 'bp_comments_open', 10, 2 );

} else {
    // Anything else is basically the same: Show any active contests, then show links to past contests.
    $items = new WP_Query( sa_get_query() );
    $total_pages = $items->max_num_pages;

    while ( $items->have_posts() ) : $items->the_post();
        // The post could be a petition or a video contest.
        // Show the right template.
        if ( 'sa_video_contest' == get_post_type() ) {
            bp_get_template_part( 'groups/single/savideocontests/single-short' );
        } else {
            bp_get_template_part( 'groups/single/satakeaction/single-short' );
        }

        // A hook after the first post so that content can be injected from other components
        if ( $items->current_post == 0 ) {
            do_action( 'sa_after_first_take_action' );
        }
    endwhile;

    sa_section_content_nav( 'nav-below', $total_pages );

}