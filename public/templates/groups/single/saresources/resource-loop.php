<?php
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$resources = new WP_Query( sa_get_query() );
$total_pages = $resources->max_num_pages;

while ( $resources->have_posts() ) : $resources->the_post();
    bp_get_template_part( 'groups/single/saresources/resource-short' );
endwhile; // end of the loop.

sa_section_content_nav( 'nav-below', $paged, $total_pages );