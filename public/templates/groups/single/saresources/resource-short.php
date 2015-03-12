<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

//Fetch and human-readize the advocacy targets
$terms = get_the_terms( get_the_ID(), 'sa_advocacy_targets' );
$advocacy_targets = array();
if ( ! empty( $terms ) ) {
    foreach ( $terms as $term ) {
        $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'resources', 'sa_advocacy_targets', $term->slug ) . '">' . $term->name . '</a>';
    }
    $advocacy_targets = join( ', ', $advocacy_targets );
}

//Fetch and human-readize the resource cats
$resource_cats = get_the_terms( get_the_ID(), 'sa_resource_cat' );
$resource_categories = array();
if ( ! empty( $resource_cats ) ) {
    foreach ( $resource_cats as $cat ) {
        $resource_categories[] = '<a href="' . get_term_link( $cat->slug, 'sa_resource_cat' ) . '">' . $cat->name . '</a>';
    }
    $resource_categories = join( ', ', $resource_categories );
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'change-short-form' ); ?>>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
            <?php //echo "<br />"; ?>
            <?php salud_the_target_icons() ?>
        </header>
        <p><?php
        $excerpt = get_the_excerpt();
        if ( ! empty( $excerpt ) ) {
            echo $excerpt;
        } else {
            the_content();
        }
        ?></p>

        <div class="clear"></div>
        <!-- Finding and listing related resources. -->
    </div><!-- .entry-content -->
</article><!-- #post -->