<?php
/**
 * Used to display "Resources"--the saresources post type.
 */
$user_id = get_current_user_id();
$is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    $main_post_id = get_the_ID();
    $custom_fields = get_post_custom( $main_post_id );

    $geo_terms = get_the_terms( $post_id, 'geographies' );
    // Get the GeoID if possible, else use the whole US
    $geo_id = ( ! empty( $geo_terms ) ) ? current( $geo_terms )->description : '01000US';
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'clear main-article' ); ?>>
        <div class="entry-content clear">
            <header class="entry-header clear">
                <h1 class="entry-title screamer sayellow"><?php the_title(); ?></h1>
                <?php sa_single_post_header_meta( $main_post_id ); ?>
            </header>

            <?php // Featured Image and indicator dials ?>
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
                    echo sa_get_advo_target_fallback_image( current( $terms ), 'feature-front' );
                }
                ?>
            </div>

            <?php sa_post_date_author( $main_post_id, 'p' ); ?>

            <?php the_content(); ?>

            <?php
                if ( function_exists('cc_add_comment_button') ) {
                    cc_add_comment_button();
                }

                if ( function_exists('bp_share_post_button') ) {
                    bp_share_post_button();
                }
            ?>

        </div><!-- .entry-content -->

        <footer class="entry-meta clear">
            <?php
            sa_post_terms_meta( $main_post_id, 'saresources' );
            edit_post_link( 'Edit This Post', '<span class="edit-link">', '</span>', $main_post_id );
            ?>
        </footer>
    </article><!-- #post -->
    <?php comments_template(); ?>
<?php endwhile; // end of the loop. ?>