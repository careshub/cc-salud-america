<?php
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$heroes = new WP_Query( sa_get_query() );
$total_pages = $heroes->max_num_pages;

while ( $heroes->have_posts() ) : $heroes->the_post();
    bp_get_template_part( 'groups/single/saheroes/hero-short' );
endwhile;

sa_section_content_nav( 'nav-below', $paged, $total_pages );