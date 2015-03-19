<?php
/**
* Template used for displaying the video contests tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

// First, we'll need to provide an info box to help the visitor register/log in and join the group.
$user_id = get_current_user_id();
$is_sa_member = false;
if ( $user_id && groups_is_user_member( $user_id, sa_get_group_id() ) ) {
    $is_sa_member = true;
}
if ( ! $user_id || ! $is_sa_member ) {
?>
    <div class="info" id="message">
<?php
    if ( ! $user_id ) {
        // User isn't logged in.
        ?>
        <p>To vote, you must <a class="login-link" href="<?php echo wp_login_url( ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ); ?>" title="Log in">log in</a>. If you don't have a Community Commons account and would like to join us, please <a href="<?php echo site_url( bp_get_signup_slug() . '?salud-america=1' ); ?>">register</a>.</p>
        <?php
    } else {
        // User doesn't belong to the SA group.
        ?>
        <p style="margin-bottom:0.6em;">You must be registered with Salud America before you can vote.</p>
        <form action="" method="POST" enctype="multipart/form-data" name="salud-america-video-contest-signup">
            <p style="margin-bottom:0.6em;"><strong>Join the hub <em>Salud America!</em> Growing Healthy Change</strong></p>
            <label style="margin-bottom:0.6em;"><input type="checkbox" name="join_salud_america_hub" id="join_salud_america_hub" value="agreed" checked="checked" /> Yes, I&rsquo;m interested in work by Salud America! to reduce Latino childhood obesity.</label><br />
            <label><input type="checkbox" name="salud_newsletter_acceptance" id="salud_newsletter_acceptance" value="agreed" checked="checked" /> I would like to receive email updates on this topic.</label>
            <p class="info" style="margin-bottom:0.6em;"><em>Periodically, Salud America! sends out news updates and brief surveys.</em></p>
            <?php
            $nonce_value = 'sa_video_contest_join_submit_' . get_current_user_id();
            wp_nonce_field( 'sa_video_contest_join_submit', $nonce_value );
            ?>
            <input type="submit"  id="sa_video_contest_join_submit" name="sa_video_contest_join_submit" alt="Join Salud America!" value="Join Salud America!" />
        </form>
        <?php
    }
?>
    </div> <!-- .info -->
<?php
}


if ( sa_is_single_post() ){
    $videos = new WP_Query( sa_get_query() );

    while ( $videos->have_posts() ) : $videos->the_post();
        bp_get_template_part( 'groups/single/savideocontests/single' );
    endwhile;

} else {
    // Anything else is basically the same: Show any active contests, then show links to past contests.
    $videos = new WP_Query( sa_get_query() );
    $total_pages = $policies->max_num_pages;

    while ( $videos->have_posts() ) : $videos->the_post();
        // Show the entire first post.
        if ( $videos->current_post == 0 ) {
            bp_get_template_part( 'groups/single/savideocontests/single' );
        } else {
            // Open a ul for the second through nth posts
            if ( $videos->current_post == 1 ) { ?>
                <h4>Previous Contests</h4>
                <ul class="previous-video-contests no-bullets">
                <?php
            }
            ?>
                    <li><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></li>
            <?php
            // Close the ul after the last post
            if ( $videos->current_post == ( $videos->post_count - 1 ) ) { ?>
                </ul>
                <?php
            }

        }


    endwhile;

    sa_section_content_nav( 'nav-below', $paged, $total_pages );

}