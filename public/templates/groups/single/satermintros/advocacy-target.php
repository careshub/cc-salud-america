<?php
//Get the page intro content, which is stored as a page with the same slug as the target area.
$tax_term = sa_get_requested_tax_term();

if ( ! empty( $tax_term ) ) {
    $args = array(
        'post_type' => 'sa_term_introduction',
        'posts_per_page' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => $tax_term->taxonomy,
                'field'    => 'term_id',
                'terms'    => $tax_term->term_id,
            ),
        ),
    );
} else {
    // if no term is selected, show all of the intros, such as on the big bets tab landing page.
    $args = array(
        'post_type' => 'sa_term_introduction',
        'order'     => 'ASC',
        'orderby'   => 'title'
    );
}
$small_icon_attr = array(
    'class' => "attachment-detail aligncenter",
);
// print_r($wp_query->query_vars);
$page_intro = new WP_Query( $args );
// print_r($page_intro);
if ( $page_intro->have_posts() ) :
    while ( $page_intro->have_posts() ) : $page_intro->the_post();
    $post_id = get_the_ID();
    $custom = get_post_meta( $post_id );
    $video_embed_english = '';
    $video_embed_spanish = '';
    // If this isn't a term-specific loop page, we need to get the term.
    if ( empty( $tax_term ) ) {
        $terms = get_the_terms( $post_id, 'sa_advocacy_targets' );
        $tax_term = current( $terms );
    }

    // echo "<pre>"; var_dump( $custom ); echo "</pre>";
    ?>
        <article  id="post-<?php the_ID(); ?>" <?php post_class('advocacy_target_introduction'); ?>>
			<div class="clear">
				<?php
				// Output the wide banner image, which is stored as a featured image.
				if ( has_post_thumbnail() ) {
					echo '<div class="content-row wrapper">';
					the_post_thumbnail( 'full' );
					echo '</div>';
				} else {
					?>
					<div class="screamer spacious wrapper <?php sa_the_topic_color( $tax_term->slug ); ?>">
						<strong style="font-size:1.4em;"><?php echo $tax_term->name; ?></strong>
					</div>
					<?php
				}

				// Output the introductory videos
				if ( ! empty( $custom[ 'sa_term_intro_video_english_url' ][0] ) ) {
					$video_embed_english = wp_oembed_get( $custom[ 'sa_term_intro_video_english_url' ][0] );
				}
				if ( ! empty( $custom[ 'sa_term_intro_video_spanish_url' ][0] ) ) {
					$video_embed_spanish = wp_oembed_get( $custom[ 'sa_term_intro_video_spanish_url' ][0] );
				}
				// If we have at least one video, output the video block
				if ( ! empty( $video_embed_english ) || ! empty( $video_embed_spanish ) ) {
				?>
					<div class="video-container-group video-right">
						<?php if ( ! empty( $video_embed_english ) ) { ?>
							<div class="video-container" id="englishVid">
								<?php echo $video_embed_english; ?>
							</div>
						<?php }
						if ( ! empty( $video_embed_spanish ) ) { ?>
							<div class="video-container" id="spanishVid">
								<?php echo $video_embed_spanish; ?>
							</div>
						<?php } ?>
						<figcaption>
							<?php if ( ! empty( $video_embed_english ) ) { ?>
								<input type="button" value="English Version (video)" id="englishButton" />
							<?php }
							if ( ! empty( $video_embed_spanish ) ) { ?>
								<input type="button" value="Spanish Version (video)" id="spanishButton" />
							<?php } ?>
						</figcaption>
					</div>
				<?php
				} elseif ( ! empty( $custom[ 'sa_term_intro_fallback_image' ][0] ) ) {
					// If there's no video, we use the fullsize fallback image for the term.
					echo wp_get_attachment_image( $custom[ 'sa_term_intro_fallback_image' ][0], 'full', false, array( 'class' => 'attachment-full alignright' ) );
				}

				the_content(); ?>
			</div>

            <div class="Grid Grid--full large-Grid--fit">

                    <?php if ( ! empty( $custom[ 'sa_term_intro_research_review' ][0] ) ) : ?>
                        <div class="Grid-cell aligncenter">
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_research_review_icon' ][0] ) ) {
                                echo wp_get_attachment_image( $custom[ 'sa_term_intro_research_review_icon' ][0], 'detail', false, $small_icon_attr );
                            }
                            ?><strong>Research Review</strong>
                            <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_research_review' ][0] ); ?>" class="button aligncenter">Download</a></p></div>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $custom[ 'sa_term_intro_issue_brief_english' ][0] ) || ! empty( $custom[ 'sa_term_intro_issue_brief_spanish' ][0] ) ) : ?>
                        <div class="Grid-cell aligncenter">
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_issue_brief_icon' ][0] ) ) {
                                echo wp_get_attachment_image( $custom[ 'sa_term_intro_issue_brief_icon' ][0], 'detail',false, $small_icon_attr );
                            }
                            ?><strong>Issue Brief</strong>
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_issue_brief_english' ][0] ) ) {
                            ?>
                                <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_issue_brief_english' ][0] ); ?>" class="button aligncenter">Download in English</a></div>
                            <?php
                            }
                            if ( ! empty( $custom[ 'sa_term_intro_issue_brief_spanish' ][0] ) ) {
                            ?>
                                <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_issue_brief_spanish' ][0] ); ?>" class="button aligncenter">Download in Spanish</a></div>
                            <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    $infographics = array(
                        'infographic_1',
                        'infographic_2',
                        'infographic_3'
                    );
                    foreach ( $infographics as $meta_key ) {
                        if ( ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_english' ][0] ) || ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_spanish' ][0] ) ) :  ?>
                            <div class="Grid-cell aligncenter">
                                <?php
                                if ( ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_icon' ][0] ) ) {
                                    echo wp_get_attachment_image( $custom[ 'sa_term_intro_' . $meta_key . '_icon' ][0], 'detail', false, $small_icon_attr );
                                }
                                ?><strong><?php
                                if ( ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_label' ][0] ) ) {
                                    echo $custom[ 'sa_term_intro_' . $meta_key . '_label' ][0];
                                } else {
                                    echo 'Infographic';
                                }
                                ?></strong>
                                <?php
                                if ( ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_english' ][0] ) ) {
                                ?>
                                    <div class="pad" style="margin:4px;"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_' . $meta_key . '_english' ][0] ); ?>" class="button aligncenter">Download in English</a></div>
                                <?php
                                }
                                if ( ! empty( $custom[ 'sa_term_intro_' . $meta_key . '_spanish' ][0] ) ) {
                                ?>
                                    <div class="pad" style="margin:4px;"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_' . $meta_key . '_spanish' ][0] ); ?>" class="button aligncenter">Download in Spanish</a></div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php endif;
                    } // end foreach;
                    ?>
           </div>
       </article>
    <?php
    // Clear $tax_term if the loop has to run again.
    unset( $tax_term );
    endwhile; // end of the loop.
endif;
wp_reset_query();
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#spanishVid').hide();
        $( "#spanishButton" ).click(function() {
            $('#englishVid').hide();
            $('#spanishVid').show();
        });
        $( "#englishButton" ).click(function() {
            $('#englishVid').show();
            $('#spanishVid').hide();
        });
    });
</script>
