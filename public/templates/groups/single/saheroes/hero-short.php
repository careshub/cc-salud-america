<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
global $post;
//print_r($post);
// echo 'META:';
$video_url = get_post_meta( $post->ID, 'sa_featured_video_url', true );
if ( !empty( $video_url ) ) {
    $video_embed_code = wp_oembed_get( $video_url );
}

$terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
if ( !empty($terms) ) {
    foreach ( $terms as $term ) {
    $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'sa_success_story', 'sa_advocacy_targets', $term->slug ) .'">'.$term->name.'</a>';
    }
    $advocacy_targets = join( ', ', $advocacy_targets );
    $plain_index = reset($terms);
    $first_advo_target = $plain_index->slug;

}

// $featured_image = get_the_post_thumbnail( $post->ID, 'feature-front-sub');

?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
        <div class="entry-content">
            <!-- <header class="entry-header clear">
                <h1 class="entry-title"><span class="<?php echo $first_advo_target; ?>x60"></span><?php the_title(); ?></h1>
                <?php //if (function_exists('salud_the_target_icons')) {
                //      salud_the_target_icons();
                //      }
                ?>
            </header> -->
            <header class="entry-header clear">
                <?php if ($featured_image) { ?>
                    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark" class="front<?php echo $layout_location ?>"><?php echo $featured_image; ?></a>
                <?php } ?>
                <h3 class="entry-title"><span class="<?php echo $first_advo_target; ?>x60"></span><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
            </header>
            <div class="entry-content">
                <?php // @TODO: Don't use the excerpt directly. ?>
                <?php the_excerpt(); ?>
            </div>

            <?php if ( isset($advocacy_targets) ) { ?>
            <p class="sa-policy-meta">Advocacy targets:
                <?php echo $advocacy_targets; ?>
            </a></p>
            <?php } ?>

            <div class="clear"></div>
            <!-- Finding and listing related resources. -->

            <?php //wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
        </div><!-- .entry-content -->
        <!-- <footer class="entry-meta">
            <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
        </footer> --><!-- .entry-meta -->
    </article><!-- #post -->
