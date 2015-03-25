<?php
//Get the page intro content, which is stored as a page with the same slug as the target area.
$tax_term = sa_get_requested_tax_term();
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
$small_icon_attr = array(
    'class' => "attachment-detail aligncenter",
);
// print_r($wp_query->query_vars);
$page_intro = new WP_Query( $args );
// print_r($page_intro);
if ( $page_intro->have_posts() ) :
    while ( $page_intro->have_posts() ) : $page_intro->the_post();
    $custom = get_post_meta( get_the_ID() );
    ?>
        <article  id="post-<?php the_ID(); ?>" <?php post_class('advocacy_target_introduction'); ?>>
            <?php
            // Output the wide banner image, which is stored as a featured image.
            the_post_thumbnail( 'full' );
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
            <?php } // end check for existence of at least one video

            the_content(); ?>

            <div class="clear clear-both">
                <div class="column1of3 aligncenter">
                    <?php
                    if ( ! empty( $custom[ 'sa_term_intro_research_review_icon' ][0] ) ) {
                        echo wp_get_attachment_image( $custom[ 'sa_term_intro_research_review_icon' ][0], 'detail', false, $small_icon_attr );
                    }
                    ?><strong>Research Review</strong>
                    <?php
                    if ( ! empty( $custom[ 'sa_term_intro_research_review' ][0] ) ) {
                    ?>
                        <div class="pad"><a href="<?php echo wp_get_attachment_url( $custom[ 'sa_term_intro_research_review' ][0] ); ?>" class="button aligncenter">Download</a></p></div>
                    <?php
                    }
                    ?>
                </div>
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
           </div>
       </article>
    <?php
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