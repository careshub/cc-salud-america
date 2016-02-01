<?php
$custom_fields = get_post_meta( get_the_ID() );
if ( sa_is_single_post() ) {
    $size = 'feature-front';
    // Show the whole post, not just the excerpt.
    global $more;
    $more = 1;
} else {
    $size = 'feature-front-sub';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(  ); ?>>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="entry-title screamer sapink"><?php the_title(); ?></h3>
        </header>
        <div class="sa-featured-image-container">
            <?php
            //First, show the thumbnail or the fallback image.
            if ( has_post_thumbnail() ) {
                $thumbnail_id = get_post_thumbnail_id();
                ?>
                <div id="attachment_<?php echo $thumbnail_id; ?>" class="wp-caption">
                    <?php the_post_thumbnail( $size ); ?>
                    <p class="wp-caption-text"><?php echo get_post( $thumbnail_id )->post_excerpt; ?></p>
                </div>
                <?php
            } else {
                echo sa_get_advo_target_fallback_image_for_post( $main_post_id, $size );
            }
            ?>
        </div>
        <?php
        the_content();
        if ( ! empty( $custom_fields['sa_take_action_url'][0] ) ) {
            if ( isset( $custom_fields['sa_take_action_button_text'][0] ) ) {
                $button_text = wptexturize( $custom_fields['sa_take_action_button_text'][0] );
            } else {
                $button_text = 'Take Action Now!';
            }
            ?>
            <a href="<?php echo $custom_fields['sa_take_action_url'][0]; ?>" class="sa-take-action-link button" data-petition-title="<?php the_title(); ?>" target="_blank"><?php echo $button_text; ?></a>
            <?php
        }
        ?>
    </div><!-- .entry-content -->
    <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
</article><!-- #post -->
