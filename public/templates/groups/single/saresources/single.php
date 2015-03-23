<?php
$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    //Fetch and human-readize the advocacy targets
    $terms = get_the_terms( get_the_ID(), 'sa_advocacy_targets' );
    $advocacy_targets = array();
    if ( ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'resources', $term->taxonomy, $term->slug ) . '">' . $term->name . '</a>';
        }
        $advocacy_targets = join( ', ', $advocacy_targets );
    }

    //Fetch and human-readize the resource cats
    $resource_cats = get_the_terms( get_the_ID(), 'sa_resource_cat' );
    $resource_categories = array();
    if ( ! empty( $resource_cats ) ) {
        foreach ( $resource_cats as $cat ) {
            $resource_categories[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'resources', $cat->taxonomy, $cat->slug ) . '">' . $cat->name . '</a>';
        }
        $resource_categories = join( ', ', $resource_categories );
    }
?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <header class="entry-header clear">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php salud_the_target_icons(); ?>
            </header>

            <?php the_content(); ?>

            <?php if ( ! empty( $advocacy_targets ) ) { ?>
                <p class="sa-policy-meta">Advocacy targets:
                    <?php echo $advocacy_targets; ?>
                </p>
            <?php } ?>

            <?php if ( ! empty( $resource_categories ) ) { ?>
                <p class="sa-policy-meta">Categories :
                    <?php echo $resource_categories; ?>
                </p>
            <?php } ?>

            <?php
                if ( function_exists('cc_add_comment_button') ) {
                    cc_add_comment_button();
                }

                if ( function_exists('bp_share_post_button') ) {
                    bp_share_post_button();
                }
            ?>

            <div class="clear"></div>
            <!-- Finding and listing related resources. -->

        </div><!-- .entry-content -->
        <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
    </article><!-- #post -->
<?php endwhile; // end of the loop. ?>