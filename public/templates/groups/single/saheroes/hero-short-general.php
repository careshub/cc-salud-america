<?php
$custom_fields = get_post_custom($post->ID);
$terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
if ( ! empty ($terms) ) :
    foreach ( $terms as $term ) {
        $advocacy_targets[] = '<a href="' .get_term_link($term->slug, 'sa_advocacy_targets') .'">'.$term->name.'</a>';
    }
    $advocacy_targets = join( ', ', $advocacy_targets );
endif; //check for empty terms
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'hero-short-general', 'sa-item-short-form', 'clear' ) ); ?>>
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
                echo sa_get_advo_target_fallback_image( current( $terms ), 'feature-front-sub', 'alignleft' );
            }
            ?>
            <?php salud_the_target_icons(); ?>
            <p class="location"><?php salud_the_location( 'sa_success_story' ); ?></p>
        </header>

        <p><?php
        $excerpt = get_the_excerpt();
        if ( ! empty( $excerpt ) ) {
            echo $excerpt;
        } else {
            the_content();
        }
        ?></p>
        <?php twentytwelve_entry_meta(); ?>
    </div><!-- .entry-content -->
</article><!-- #post -->