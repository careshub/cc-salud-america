<?php
$custom_fields = get_post_meta( get_the_ID() );
// echo '<pre>'; print_r( $custom_fields ); echo '</pre>';
$is_active = sa_video_contest_is_active( get_the_ID() );
$user_can_vote = sa_video_contest_current_user_can_vote( get_the_ID() );
// Has the user already voted?
$user_vote = sa_video_contest_get_current_user_vote( get_the_ID() );

$end_date = sa_convert_to_human_date( $custom_fields['sa_expiry_date'][0] );

// echo '<pre>'; var_dump( get_post_meta( get_the_ID(), 'sa_video_contest_votes', true ) ); echo '</pre>';

$post_class = 'sa-video-contest';
if ( $is_active ) {
    $post_class .= ' active-contest';
}

// If it is still fresh and the current user can vote, make the contest actionable, otherwise, make it a good show. All others display as links.
// If it is not active, show the winner first, then the others.
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'clear', 'clear', 'sa-item-short-form' ) ); ?>>
    <div class="entry-content">
        <header class="entry-header">
            <h3 class="entry-title">
            <?php cc_post_type_flag(); ?>
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
            </h3>
            <?php
            // Featured image or fallback.
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'feature-front-sub', array( 'class' => 'attachment-feature-front-sub alignleft' ) );
            } else {
                echo sa_get_advo_target_fallback_image_for_post( $post_id, 'feature-front-sub', 'alignleft' );
            }
            ?>
            <?php salud_the_target_icons(); ?>
        </header>
        <?php // The content is the description of the contest in this case.
        the_content();
        ?>
        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark" class="button"><?php
        if ( $is_active ) {
            echo "Vote Now!";
        } else {
            echo "See the Results!";
        } ?></a>
    </div><!-- .entry-content -->
    <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
</article><!-- #post -->