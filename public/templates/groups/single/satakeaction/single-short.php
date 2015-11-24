<?php
$custom_fields = get_post_meta( get_the_ID() );
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