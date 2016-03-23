<?php
/**
* Template used for displaying the policies tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$requested_page = bp_action_variable();

// Add a subnav of terms in this taxonomy
$terms = get_terms( 'sa_advocacy_targets', array(
    'hide_empty' => 0,
 ) );
if ( ! empty( $terms ) ) :
    $url_base = sa_get_section_permalink( 'big_bets' );
    ?>
    <div id="subnav" class="item-list-tabs no-ajax" role="navigation">
        <ul>
            <?php foreach ( $terms as $term  ) {
                    ?>
                    <li<?php if ( $requested_page == $term->slug ) {
                        echo ' class="current selected"';
                    } ?>>
                    <a href="<?php echo trailingslashit( $url_base ) . $term->slug; ?>"><?php echo $term->name; ?></a>
                    </li>
            <?php } ?>
          </ul>
    </div>
    <?php
endif;

if ( sa_is_section_front() ) {
    // We provide a page that shows all of the term intros.
    bp_get_template_part( 'groups/single/satermintros/advocacy-target' );

// Not the section front? OK, let's figure out what to display.
} elseif ( sa_is_archive_taxonomy() ) {

    $tax_term = sa_get_requested_tax_term();
    // Special case: Advocacy targets get a special introductory block
    if ( $tax_term->taxonomy == 'sa_advocacy_targets' && $paged == 1 ) {
        bp_get_template_part( 'groups/single/satermintros/advocacy-target' );
    } ?>

    <div class="taxonomy-policies">
        <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>"><?php
        echo $tax_term->name;
        echo ': What&lsquo;s New?';
        ?></h3>
        <div class="archive-filter-container background-light-gray">
            See <a href="<?php sa_the_cpt_tax_intersection_link( 'changes', 'sa_advocacy_targets', $tax_term->slug ); ?>">Changes</a>, <a href="<?php sa_the_cpt_tax_intersection_link( 'resources', 'sa_advocacy_targets', $tax_term->slug ); ?>">Resources</a>, or <a href="<?php sa_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $tax_term->slug ); ?>">Heroes</a> in this topic.
        </div>
        <?php
        // print_r( sa_get_query() );
        $items = new WP_Query( sa_get_query() );
        $total_pages = $items->max_num_pages;
        if ( $items->have_posts() ) :
            while ( $items->have_posts() ) : $items->the_post();
                global $post;
                switch ( $post->post_type ) {
                    case 'saresources':
                        $template_part = 'groups/single/saresources/resource-short-general';
                        break;
                    case 'sa_success_story':
                        $template_part = 'groups/single/saheroes/hero-short-general';
                        break;
                    case 'sapolicies':
                    default:
                        $template_part = 'groups/single/sapolicies/policy-short-general';
                        break;
                }
                bp_get_template_part( $template_part );
            endwhile;
        endif;

        sa_section_content_nav( 'nav-below', $total_pages );

        ?>
    </div>
    <?php

}