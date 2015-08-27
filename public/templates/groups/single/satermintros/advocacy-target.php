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
                // $video_embed_english = wp_oembed_get( $custom[ 'sa_term_intro_video_english_url' ][0] );
            }
            if ( ! empty( $custom[ 'sa_term_intro_video_spanish_url' ][0] ) ) {
                // $video_embed_spanish = wp_oembed_get( $custom[ 'sa_term_intro_video_spanish_url' ][0] );
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

            <div class="clear clear-both">
                <?php // What a pain in the ass.
                // Output a "coming soon block", which of course is different for every term. Eff me.
                $links = array();
                switch ( $tax_term->slug ) {
                    case 'sa-healthier-schools':
                        // Includes both Active Play and Healthier School Snacks
                        $links[] = '<strong>Active Play</strong> <a href="' . site_url( '/wp-content/uploads/2013/08/Active-Play-Research-Review.pdf' ) . '">Research Review</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Active-Play-Issue-Brief.pdf') . '">Issue Brief in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/SpanishActive-Play-Issue-Brief.pdf') . '">Issue Brief in Spanish</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2015/03/Active-Play-Infographic-875-1.jpg') . '">Infographic in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/ActivePlay_Infographic_SPN_sml.jpg') . '">Infographic in Spanish</a>';
                        $links[] = '<br /><strong>Healthier School Snacks</strong> <a href="' . site_url( '/wp-content/uploads/2013/08/Healthier-School-Snacks-Research-Review.pdf' ) . '">Research Review</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Healthier-School-Snacks-Issue-Brief.pdf') . '">Issue Brief in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/SpanishHealthier-School-Snacks-Issue-Brief.pdf') . '">Issue Brief in Spanish</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Healthier-School-Snacks-Infographic-875.jpg') . '">Infographic in English</a>';
                        break;
                    case 'sa-active-spaces':
                        $links[] = '<strong>Active Spaces</strong> <a href="' . site_url( '/wp-content/uploads/2013/08/Active-Spaces-Research-Review.pdf' ) . '">Research Review</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Active-Spaces-Issue-Brief.pdf') . '">Issue Brief in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/SpanishActive-Spaces-Issue-Brief.pdf') . '">Issue Brief in Spanish</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Active-Spaces-Infographic-875.jpg') . '">Infographic in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/Salud_ActiveSpaces_Infographic_SPN_sml.jpg') . '">Infographic in Spanish</a>';
                        break;
                    case 'sa-better-food-in-neighborhoods':
                        $links[] = '<strong>Better Food in Neighborhoods </strong><a href="' . site_url( '/wp-content/uploads/2013/08/BetterFoodintheNeighborhood-ResearchReview.pdf' ) . '">Research Review</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Better-Food-in-the-Neighborhood-Issue-Brief.pdf') . '">Issue Brief in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/SpanishBetter-Food-in-Neighborhoods-Issue-Brief.pdf') . '">Issue Brief in Spanish</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2013/08/Better-Food-in-the-Neighborhood-Infographic-875.jpg') . '">Infographic in English</a>';
                        $links[] = '<a href="' . site_url('/wp-content/uploads/2014/02/Salud_BetterFoods_Infographic_SPN_sml_0.jpg') . '">Infographic in Spanish</a>';
                        break;
                    case 'sa-health-equity':
                    case 'sa-healthy-weight':
                    default:
                        break;
                }

                ?>

                <div id="message" class="updated">
                    <h5>New Research Coming!</h5>
                    <p><em>Salud America!</em> will be releasing a brand-new research review on this topic, as well as a two-page research summary, infographics, and an animated video.</p>

                    <?php if ( ! empty( $links ) ) {
                        ?>
                        <p>Until then, refer to our 2013 <em>Salud America!</em> research.</p>
                        <p><?php echo implode( ', ', $links ); ?></p>
                        <?php
                    } else {
                        ?>
                        <p>Until then, visit the <a href="http://www.rwjf.org/en/culture-of-health.html" target="_blank">RWJF blog</a> on creating a culture of health.</p>
                        <?php
                    }
                ?>
                <div>
                <?php
                // We're temporarily changing the display of this stuff.
                if ( false ) {
                ?>
                    <?php if ( ! empty( $custom[ 'sa_term_intro_research_review' ][0] ) ) : ?>
                        <div class="column1of3 aligncenter">
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_research_review_icon' ][0] ) ) {
                                echo wp_get_attachment_image( $custom[ 'sa_term_intro_research_review_icon' ][0], 'detail', false, $small_icon_attr );
                            }
                            ?><strong>Research Review</strong>
                            <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_research_review' ][0] ); ?>" class="button aligncenter">Download</a></p></div>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $custom[ 'sa_term_intro_issue_brief_english' ][0] ) || ! empty( $custom[ 'sa_term_intro_issue_brief_spanish' ][0] ) ) : ?>
                        <div class="column1of3 aligncenter">
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
                    <?php if ( ! empty( $custom[ 'sa_term_intro_infographic_english' ][0] ) || ! empty( $custom[ 'sa_term_intro_infographic_spanish' ][0] ) ) :  ?>
                        <div class="column1of3 aligncenter">
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_infographics_icon' ][0] ) ) {
                                echo wp_get_attachment_image( $custom[ 'sa_term_intro_infographics_icon' ][0], 'detail', false, $small_icon_attr );
                            }
                            ?><strong>Infographic</strong>
                            <?php
                            if ( ! empty( $custom[ 'sa_term_intro_infographic_english' ][0] ) ) {
                            ?>
                                <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_infographic_english' ][0] ); ?>" class="button aligncenter">Download in English</a></div>
                            <?php
                            }
                            if ( ! empty( $custom[ 'sa_term_intro_infographic_spanish' ][0] ) ) {
                            ?>
                                <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_infographic_spanish' ][0] ); ?>" class="button aligncenter">Download in Spanish</a></div>
                            <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                <?php } // temporary highjacking the old stuff.?>
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