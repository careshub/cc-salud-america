<?php
$activity_post_types = array( 'sapolicies', 'saresources', 'sa_success_story', 'sa_take_action', 'sa_video_contest' );
$search = array(
    'post_type' => $activity_post_types,
    'posts_per_page' => 50,
    'paged' => 2
);

$history = new WP_Query( $search );
echo '<ul>';
echo 'found' . $history->found_posts;
while( $history->have_posts() ) {
    $history->the_post();
    global $post;
    echo PHP_EOL . '<li>';
    echo get_post_type() . ': ' . get_the_title();
    echo ' | created activity id: ' . add_historical_activity_item( $post );
    echo '</li>';
}
echo '</ul>';


function add_historical_activity_item( $post ) {
    $bp = buddypress();

    // The action hook we're using will only run when a post is changed to "publish" status
    $post_id = $post->ID;
    $author_id = (int) $post->post_author;
    $user_link = bp_core_get_userlink( $author_id );

    $post_type_object = get_post_type_object( $post->post_type );
    $post_type_label = strtolower( $post_type_object->labels->singular_name );

    $post_url = get_permalink( $post_id );
    $post_link = sprintf( '<a href="%s">%s</a>', $post_url, get_the_title( $post_id ) );

    $group_id = sa_get_group_id();
    $group = groups_get_group( array( 'group_id' => $group_id ) );
    $group_url  = bp_get_group_permalink( $group );
    $group_link = '<a href="' . $group_url . '">' . $group->name . '</a>';

    $action = sprintf( __( '%1$s published the %2$s %3$s in the Hub %4$s', 'cc-salud-america' ), $user_link, $post_type_label, $post_link, $group_link );

    $type = $post->post_type . '_created';

    $excerpt = cc_ellipsis( $post->post_content, $max=100, $append='&hellip;' );

    $args = array(
        'user_id'       => $author_id,
        'action'        => $action,
        'primary_link'  => $post_link,
        'component'     => $bp->groups->id,
        'type'          => $type,
        'item_id'       => $group_id, // Set to the group/user/etc id, for better consistency with other BP components
        'secondary_item_id' => $post_id, // The id of the doc itself
        'recorded_time'     => $post->post_date,
        'hide_sitewide'     => false, // Filtered to allow plugins and integration pieces to dictate
        'content'           => $excerpt
    );

    $activity_id = bp_activity_add( apply_filters( $post->post_type . '_activity_args', $args, $post_id ) );

    return $activity_id;
}