<?php
/**
 * Used to display "Salud Heroes"--the sa_success_story post type.
 */
$user_id = get_current_user_id();
$is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    $main_post_id = get_the_ID();

    // Show the whole post, not just the excerpt.
    global $more;
    $more = 1;

    $post_meta = get_post_custom( $main_post_id );

    // Set up the featured video.
    $video_url = '';
    if ( ! empty( $post_meta['sa_featured_video_url'] ) ) {
        $video_url = current( $post_meta['sa_featured_video_url'] );
    }
    $video_embed_code = '';
    if ( ! empty( $video_url ) ) {
        $video_embed_code = wp_oembed_get( $video_url );
    }
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'main-article' ); ?>>
        <div class="entry-content clear">
            <header class="entry-header clear">
                <h1 class="entry-title screamer sapurple"><?php the_title(); ?></h1>
                <?php sa_single_post_header_meta( $main_post_id ); ?>
            </header>

            <?php
            // Show the featured video if one exists.
            if ( ! empty( $video_embed_code ) ) { ?>
                <div class="video-container-group video-right">
                    <figure class="video-container">
                        <?php echo $video_embed_code; ?>
                    </figure>
                </div>
                <?php
            } else {
                // Else show the post's featured image or a fallback.
                ?>
                <div class="sa-featured-image-container">
                    <?php
                    //First, show the thumbnail or the fallback image.
                    if ( has_post_thumbnail() ) {
                        $thumbnail_id = get_post_thumbnail_id();
                        ?>
                        <div id="attachment_<?php echo $thumbnail_id; ?>" class="wp-caption">
                            <?php the_post_thumbnail( 'feature-front' ); ?>
                            <p class="wp-caption-text"><?php echo get_post( $thumbnail_id )->post_excerpt; ?></p>
                        </div>
                        <?php
                    } else {
                        echo sa_get_advo_target_fallback_image_for_post( $main_post_id, 'feature-front' );
                    }
                    ?>
                </div>
                <?php
            }
            ?>

            <?php the_content(); ?>

            <?php if ( ! empty( $advocacy_targets ) ) { ?>
            <p class="sa-policy-meta">Advocacy targets:
                <?php echo $advocacy_targets; ?>
            </a></p>
            <?php } ?>

            <?php
                // Comments and share buttons are added along with the PDF button.
            ?>

        </div><!-- .entry-content -->

        <footer class="entry-meta clear">
            <?php
            sa_post_terms_meta( $main_post_id, 'sa_success_story' );
            edit_post_link( 'Edit This Post', '<span class="edit-link">', '</span>', $main_post_id );
            ?>
        </footer>
    </article><!-- #post -->
<?php endwhile; // end of the loop. ?>