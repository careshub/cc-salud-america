<?php
$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    global $post;
    setup_postdata( $post );
    // Show the whole post, not just the excerpt.
    global $more;
    $more = 1;

    $video_url = get_post_meta( get_the_ID(), 'sa_success_story_video_url', true );
    $video_embed_code = '';
    if ( ! empty( $video_url ) ) {
        $video_embed_code = wp_oembed_get( $video_url );
    }

    $terms = get_the_terms( get_the_ID(), 'sa_advocacy_targets' );
    $advocacy_targets = '';
    if ( ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
        $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $term->slug ) .'">'.$term->name.'</a>';
        }
        $advocacy_targets = join( ', ', $advocacy_targets );
        $first_advo_target = current( $terms )->slug;
    }
    // @TODO: Check that the more tag doesn't shorten these
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'main-article' ); ?>>
        <div class="entry-content">
            <header class="entry-header clear">
                <span class="<?php echo $first_advo_target; ?>x90"></span><h1 class="entry-title icon-friendly"><?php the_title(); ?></h1>
                <?php //if (function_exists('salud_the_target_icons')) {
                //      salud_the_target_icons();
                //      }
                ?>
            </header>

            <?php if ( ! empty( $video_embed_code ) ) { ?>
            <div class="video-container-group video-right">
                <figure class="video-container">
                    <?php echo $video_embed_code; ?>
                </figure>
            </div>
            <?php } ?>

            <?php the_content(); ?>

            <?php if ( ! empty( $advocacy_targets ) ) { ?>
            <p class="sa-policy-meta">Advocacy targets:
                <?php echo $advocacy_targets; ?>
            </a></p>
            <?php } ?>

            <?php
                // Comments and share buttons are added along with the PDF button
            ?>

            <div class="clear"></div>
            <!-- Finding and listing related resources. -->

        </div><!-- .entry-content -->
        <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
    </article><!-- #post -->
<?php endwhile; // end of the loop. ?>