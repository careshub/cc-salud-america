<?php
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$heroes = new WP_Query( sa_get_query() );
$total_pages = $heroes->max_num_pages;

while ( $heroes->have_posts() ) : $heroes->the_post();

    $video_url = get_post_meta( get_the_ID(), 'sa_success_story_video_url', 'true' );
    $video_embed_code = '';
    if ( ! empty( $video_url ) ) {
        $video_embed_code = wp_oembed_get( $video_url );
    }

    // Do not display if no video was returned.
    if ( empty( $video_embed_code ) ) {
        continue;
    }

    $terms = get_the_terms( get_the_ID(), 'sa_advocacy_targets' );
    $advocacy_targets = '';
    if ( ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
        $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'sa_success_story', 'sa_advocacy_targets', $term->slug ) .'">'.$term->name.'</a>';
        }
        $advocacy_targets = join( ', ', $advocacy_targets );
        $first_advo_target = current( $terms )->slug;
    }

    $video_meta = sa_get_youtube_video_metadata( $video_url );
    $description = apply_filters( 'the_content', $video_meta['description'] );
    $video_title = apply_filters( 'the_title', $video_meta['title'] );

    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'salud-hero-video-summary' ); ?>>
        <div class="entry-content">
            <header class="entry-header clear">
                <span class="<?php echo $first_advo_target; ?>x60"></span><h3 class="entry-title icon-friendly"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
            </header>

            <h5 class="video-title">
                <?php echo $video_title; ?>
            </h5>

            <figure>
                <?php echo $video_embed_code; ?>
            </figure>

            <?php echo $description; ?>

            <?php if ( ! empty( $advocacy_targets ) ) { ?>
            <p class="sa-policy-meta">Advocacy targets:
                <?php echo $advocacy_targets; ?>
            </a></p>
            <?php } ?>

            <div class="clear"></div>

        </div><!-- .entry-content -->
    </article><!-- #post -->
<?php
endwhile;
sa_section_content_nav( 'nav-below', $paged, $total_pages );