<?php
/**
* Template used for displaying the heroes tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
// Should we display the video archive?
$archive_style = ( isset( $_GET['style'] ) && $_GET['style'] == 'videos'  ) ? 'videos' : '';


if ( sa_is_section_front() ) {

    if ( $archive_style == 'videos') {
        ?>
        <h3 class="screamer sablue">Salud Heroes Video Archive</h3>
        <?php
        bp_get_template_part( 'groups/single/saheroes/hero-loop-video' );

    } else {
        ?>

        <h3 class="screamer sablue">Salud Heroes</h3>
            <?php
            //Get the page intro content.
            $args = array (
                'pagename' => 'salud-america/success-stories-intro/',
                'post_type' => 'page'
                );
            $page_intro = new WP_Query( $args );

            while ( $page_intro->have_posts() ) : $page_intro->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('clear'); ?>>
                    <?php sa_get_random_hero_video() ?>
                    <?php the_content(); ?>
                    <!-- <header>
                         <img class="size-full wp-image-16768 no-box" alt="Topic header for <?php echo $tax_term->name ?>" src="<?php echo sa_get_plugin_base_uri(); ?>/img/salud_america/topic_headers/<?php echo $tax_term->slug ?>.jpg" />
                     </header> -->
                </article>

                <?php
            endwhile; // end of the loop.
            wp_reset_postdata();

            //Loop to display the most recent changemaker featured image for each target area.
            $advocacy_targets = get_terms('sa_advocacy_targets');

            $do_not_duplicate = array();
            foreach ( $advocacy_targets as $target ) {
                //Build the query
                $args = array (
                    'post_type' => 'sa_success_story',
                    'posts_per_page' => 1,
                    'post__not_in' => $do_not_duplicate,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'sa_advocacy_targets',
                            'field' => 'slug',
                            'terms' => array( $target->slug )
                        ),
                    )
                    );

                $ssquery = new WP_Query( $args );
                while ( $ssquery->have_posts() ) {
                    $ssquery->the_post();
                    global $post;
                    setup_postdata( $post );
                    ?>
                    <div class="half-block salud-topic <?php echo $target->slug; ?>">
                        <a href="<?php sa_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $target->slug ); ?>" class="topic-header-link" title="Link to taxonomy page.">
                            <span class="<?php echo $target->slug; ?>x90"></span><h3 class="icon-friendly" style="width:65%; margin-top:0; line-height:1.2;"><?php echo $target->name; ?></h3>
                        </a>
                        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail('feature-large');
                            }
                        ?>
                        <h5 class="entry-title"><?php the_title(); ?></h5></a>
                        <a href="<?php sa_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $target->slug ); ?>" title="Link to taxonomy page." class="button">More stories on this topic...</a>
                    </div> <!-- .half-block -->
                    <?php
                    $do_not_duplicate[] = get_the_ID();
                }
                wp_reset_postdata();

            } //End foreach advocacy target
     } // END non-advocacy target version
    // Show the top section on the front page only.
// Not the section front? OK, let's figure out what to display.
} elseif ( sa_is_archive_taxonomy() ) {

    $tax_term = sa_get_requested_tax_term();
    // Special case: we're looking at an advocacy target
    if ( $tax_term->taxonomy == 'sa_advocacy_targets' ) {
        bp_get_template_part( 'groups/single/saheroes/advocacy-targets' );
    } else {
        //Taxonomy term is set, but not an advocacy target
        ?>
        <div class="taxonomy-policies">
            <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Changes in the <?php
            echo $tax_term->name;
            echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' )
            ?></h3>
            <?php bp_get_template_part( 'groups/single/saheroes/hero-loop' ); ?>
        </div>
        <?php
    }

} elseif ( sa_is_single_post() ){

    bp_get_template_part( 'groups/single/saheroes/single' );

} // if ( sa_is_section_front() ) :