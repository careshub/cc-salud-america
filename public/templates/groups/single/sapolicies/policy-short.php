<?php
$custom_fields = get_post_custom($post->ID);
$terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
if ( ! empty ($terms) ) :
    foreach ( $terms as $term ) {
        $advocacy_targets[] = '<a href="' .get_term_link($term->slug, 'sa_advocacy_targets') .'">'.$term->name.'</a>';
    }
    $advocacy_targets = join( ', ', $advocacy_targets );
endif; //check for empty terms

//Progress meter
$progress = $custom_fields['sa_policystage'][0];
    switch ($progress) {
        case "emergence":
            $percentage = 25;
            $progress_label = "in emergence";
            break;
        case "development":
            $percentage = 50;
            $progress_label = 'in development';
            break;
        case "enactment":
            $percentage = 75;
            $progress_label = 'enacted';
            break;
        case "implementation":
            $percentage = 75;
            $progress_label = 'in implementation';
            break;
        default:
            $percentage = 0;
            $progress_label = 'in emergence';
            break;
    }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="entry-title">
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
            </h3>
            <?php salud_the_target_icons(); ?>
            <p class="location"><?php salud_the_location(); ?></p>
            <div class="meter-box clear">
                <p>This change is <?php echo $progress_label; ?>.</p>
                <!-- <div class="meter">
                    <span style="width: <?php echo $percentage; ?>%"><span></span></span>
                </div> -->
            </div> <!-- end .meter-box -->
            <p class="datestamp">Posted <?php echo get_the_date(); ?>.</p>

        </header>
        <p><?php
        $excerpt = get_the_excerpt();
        if ( ! empty( $excerpt ) ) {
            echo $excerpt;
        } else {
            the_content();
        }
        ?></p>
    </div><!-- .entry-content -->
</article><!-- #post -->