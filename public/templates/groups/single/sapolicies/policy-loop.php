<?php
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$policies = new WP_Query( sa_get_query() );
$total_pages = $policies->max_num_pages;

while ( $policies->have_posts() ) : $policies->the_post();
    bp_get_template_part( 'groups/single/sapolicies/policy-short-general' );
endwhile;

sa_section_content_nav( 'nav-below', $total_pages );