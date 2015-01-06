<?php 
$custom_fields = get_post_custom($post->ID);
$terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
if ( !empty ($terms) ) :
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
//echo $progress_label . " " . $percentage;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="entry-title">
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
            </h3>
            <?php //echo "<br />"; ?>
            <?php if (function_exists('salud_the_target_icons')) {
                    salud_the_target_icons();
                    }
            ?>
            <p class="location"><?php //echo $location; 
                    if (function_exists('salud_the_location')) {
                        salud_the_location();
                    }
                ?></p>
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

        if ( isset($excerpt) ) {
            echo $excerpt;
        } else {
            the_content();
        }
        ?></p>
        <!-- <p class="policy-type">This policy is of the type: <a href="#"><?php echo $custom_fields['policytype'][0];?></a></p> -->

        <div class="clear"></div>           

        <?php //wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
    </div><!-- .entry-content -->
    <!-- <footer class="entry-meta">
                
        <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
    </footer> --><!-- .entry-meta -->
</article><!-- #post -->