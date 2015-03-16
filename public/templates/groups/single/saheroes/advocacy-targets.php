<?php
$tax_term = sa_get_requested_tax_term();
//Get the page intro content, which is stored as a page with the same slug as the target area.
$args = array (
    'pagename' => 'salud-america/sa-advocacy-targets-intros/' . $tax_term->slug,
    'post_type' => 'page'
    );
// print_r($wp_query->query_vars);
$page_intro = new WP_Query( $args );
// print_r($page_intro);
while ( $page_intro->have_posts() ) : $page_intro->the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('advocacy_target_introduction'); ?>>
        <?php
        //Get the page header image ?>
        <header>
             <img class="size-full no-box" alt="Topic header for <?php echo $tax_term->name ?>" src="<?php echo sa_get_plugin_base_uri() . 'public/images/topic_headers/' . $tax_term->slug ?>.jpg" />
         </header>
     <?php
        the_content();
        ?>
    </article>
<?php
endwhile; // end of the loop.
?>

<div class="taxonomy-policies">
    <h3 class="screamer saorange">Salud Heroes in the Topic <?php echo $tax_term->name ?></h3>
    <?php bp_get_template_part( 'groups/single/saheroes/hero-loop' ); ?>
</div>