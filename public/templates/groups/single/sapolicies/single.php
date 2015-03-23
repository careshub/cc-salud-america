<?php
$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    $custom_fields = get_post_custom($post->ID);
    $terms = get_the_terms( get_the_ID(), 'sa_advocacy_targets' );
    $advocacy_targets = array();
    if ( ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            $advocacy_targets[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'policies', $term->taxonomy, $term->slug ) . '">' . $term->name . '</a>';
        }
        $advocacy_targets = join( ', ', $advocacy_targets );
    }

    $tags = get_the_terms( $post->ID, 'sa_policy_tags' );
    $policy_tags = array();
    if ( ! empty( $tags ) ) {
        foreach ( $tags as $tag ) {
            $policy_tags[] = '<a href="' . sa_get_the_cpt_tax_intersection_link( 'policies', $tag->taxonomy, $tag->slug ) . '">' . $tag->name . '</a>';
        }
        $policy_tags = join( ', ', $policy_tags );
    }
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'main-article' ); ?>>
        <div class="entry-content">
            <header class="entry-header clear">
                <h1 class="entry-title screamer sapurple"><?php the_title(); ?></h1>
                <?php //echo "<br />"; ?>
                <div class="header-meta clear">
                    <?php salud_the_target_icons() ?>
                    <p class="location"><?php salud_the_location(); ?> <span class="sa-policy-date">Posted <?php echo get_the_date(); ?>.</span></p>

                    <?php cc_the_policy_progress_tracker( $custom_fields['sa_policystage'][0] ); ?>
                </div>
            </header>

            <?php the_content(); ?>

            <?php if ( ! empty( $advocacy_targets ) ) { ?>
                <p class="sa-policy-meta">Advocacy targets:
                    <?php echo $advocacy_targets; ?>
                </p>
            <?php } ?>

            <?php if ( ! empty( $policy_tags ) ) { ?>
                <p class="sa-policy-meta">Tags :
                    <?php echo $policy_tags; ?>
                </p>
            <?php } ?>

            <?php if ( ! empty( $custom_fields['sa_policytype'][0] ) ) { ?>
                <p class="sa-policy-meta">This change is of the type:
                    <?php echo $custom_fields['sa_policytype'][0]; ?>
                </p>
            <?php } ?>

            <?php
                if ( function_exists('cc_add_comment_button') ) {
                    cc_add_comment_button();
                }

                if ( function_exists('bp_share_post_button') ) {
                    bp_share_post_button();
                }
            ?>

            <div class="clear"></div>
            <!-- Finding and listing related resources. -->
            <?php // args

                // $looky = '%"' . $post->ID . '"%';
                // $related_resource_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'sa_resource_policy' AND meta_value LIKE %s", $looky ) );

                // wp_reset_postdata();
                //@TODO This is broken, and unused. If we wish to enable this, we should do it better.
                $related_resource_results = 0;
                if ( $related_resource_results ) {
                    //     //Build a 1-dimensional array of associated post IDs
                    //     foreach ($related_resource_results as $relation) {
                    //         $associated_resources[] = $relation->post_id;
                    //     }
                    // print_r($associated_resources);

                    $args = array(
                        'post_type' => 'saresources',
                        'meta_query' => array(
                            array(
                                'key'     => 'saresource_policy',
                                'value'   => '\"%' . $post->ID . '%\"',
                                'compare' => 'LIKE',
                            ),
                        ),

                    );
                    $associated_docs = new WP_Query( $args );

                    // echo "<pre>";
                    // var_dump($associated_docs);
                    // echo "</pre>";

                    if ( $associated_docs->have_posts() ) { ?>

                    <h5>Associated Resources</h5>
                    <ul id="sa_associated_resources">

                    <?php while ( $associated_docs->have_posts() ) : $associated_docs->the_post();
                    $assoc_tags = get_the_terms( $post->ID, 'sa_resource_cat' );
                        if ($assoc_tags) {
                            foreach ( $assoc_tags as $assoc_tag ) {
                                $resource_tags[] = '<a href="' . get_term_link($assoc_tag->slug, 'sa_resource_cat') .'">'.$assoc_tag->name.'</a>';
                            }
                            $resource_tags = join( ', ', $resource_tags );
                        }
                ?>
                        <li>
                            <p class="sa_assoc_resource_title">
                                <em>
                                    <?php
                                    // @TODO: What is "get_field"?
                                    $resource_type = get_field( "sa_resource_type" ) ? get_field( "sa_resource_type" ) : '' ;
                                    if ( $resource_type )  { ?>
                                        <?php the_field( "sa_resource_type" ); ?>:
                                    <?php } ?>
                                </em>
                                <?php if ( $resource_type == 'Link' ) {
                                    $link_url = get_field( 'sa_resource_link' );
                                    ?>

                                    <a href="<?php echo $link_url ; ?>" title="<?php the_title(); ?>" ><?php the_title(); ?></a>

                                <?php } else { ?>

                                    <?php the_title(); ?>

                                <?php } ?>

                            </p>
                            <div class="sa_assoc_resource_title">
                                <?php the_content(); ?>
                            </div>
                        <?php if ( ! empty( $resource_tags ) ) { ?>
                            <p class="resource-tags">Tags :
                                <?php echo $resource_tags; ?>
                            </a></p>
                        <?php } ?>

                        </li>
                    <?php endwhile; ?>
                        </ul>
                <?php } //if ( $associated_docs ) : ?>

            <?php } // End if ($related_resource_results) check ?>

        </div><!-- .entry-content -->
        <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
    </article><!-- #post -->
<?php
endwhile; // end of the loop. ?>

<div class="policy-meta">
<?php
$geogterm = wp_get_object_terms( $post->ID, 'geographies' );
if( ! empty( $geogterm ) && ! is_wp_error( $geogterm ) ){
    $geoid = current( $geogterm )->description;
    $geoidstate = '04000' . substr($geoid, 5, 4);
}
?>
    <h3 class="screamer sapink">Related Data for this Region</h3>

    <div class="half-block">
        <!-- @TODO: Use responsive version. -->
        <h6 style="margin-top:0;">Percent Adults Age 18+ Obese (BMI >= 30)  by County</h6>
        <script src='http://maps.communitycommons.org/jscripts/mapWidget.js?ids=348&vm=348&w=190&h=190&geoid=<?php echo $geoidstate; ?>&l=1'></script>
    </div>

    <div class="half-block">
        <div id="dial">
            <script src='http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=<?php echo $geoid; ?>&id=779'></script>
        </div>
        <input type="button" id="btnSubmit1" value="Poverty rate" onclick="changeDial('779')" />
        <input type="button" id="btnSubmit2" value="Children in Poverty " onclick="changeDial('781')" />
        <input type="button" id="btnSubmit3" value="Pop. With No HS Diploma" onclick="changeDial('760')" />
    </div>
</div>

<div class="related-policies">
    <?php
    // Get related policies by advocacy target =====================================
    $source_post_id = $post->ID;
    $exclude_posts = array( $source_post_id );
    $advocacy_targets_id_array = array();
    $terms = get_the_terms( $source_post_id, 'sa_advocacy_targets' );
    if ( ! empty ( $terms ) ) {
        foreach ( $terms as $term ) {
            $advocacy_targets_id_array[] = $term->term_id;
        }
    }

    $related_policies_args = array(
        'post_type' => 'sapolicies',
        'post__not_in' => $exclude_posts,
        'posts_per_page' => 3,
        'tax_query' => array(
            array(
                'taxonomy' => 'sa_advocacy_targets',
                'field' => 'id',
                'terms' => $advocacy_targets_id_array,
            )
        )
    );

    $related_policies = new WP_Query( $related_policies_args );
    if ( $related_policies->have_posts() ) {
        ?>
        <div class="related-by-topic">
            <h3 class="screamer sagreen">Related Changes by Topic</h3>
            <?php
            while ( $related_policies->have_posts() ) : $related_policies->the_post();
                // $body = apply_filters( 'the_content', get_the_content() );
                // $body = ellipsis( $body );
                ?>
                <div class="third-block">
                    <div id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
                        <div class="entry-content">
                            <header class="entry-header clear">
                                <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h4>
                                <?php the_excerpt(); ?>
                            </header>

                        </div> <!-- entry-content -->
                    </div>
                </div>
                <?php
                $exclude_posts[] = $post->ID;
            endwhile; // end of the loop.
        ?>
        </div> <!-- #related-by-topic -->
    <?php
    } // end if ( $related_policies->have_posts() )
    ?>

      <!-- @TODO: Fix these hardcoded links -->
        <div class="related-what-can-you-do clear">
            <h3 class="screamer sablue">What Can You Do?</h3>

            <a href="/salud-america/share-your-own-stories/" class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/BeaStar_icon.png" width="100px"/><br />Start your own change!
            </a>

    <!-- @TODO: This block is pointless -->
                <span class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/AddChange_icon.png" width="100px"/><br />Connect with members in your area!
            </span>
      <!-- @TODO: Fix these hardcoded links -->
            <a href="/salud-america/what-is-change/" class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/WhatsChange_icon.png" width="100px"/><br />See how a change is made
            </a>
        </div>

<?php
// Get related posts by tag ====================================================
$tags = get_the_terms( $source_post_id, 'sa_policy_tags' );
$policy_tags_array = array();
if ( ! empty ( $tags ) ) {
    foreach ( $tags as $tag ) {
        $policy_tags_array[] = $tag->term_id;
    }
}

$related_policies_args = array(
    'post_type' => 'sapolicies',
    'post__not_in' => $exclude_posts,
    'posts_per_page' => 3,
    'tax_query' => array(
        array(
            'taxonomy' => 'sa_policy_tags',
            'field' => 'id',
            'terms' => $policy_tags_array,
            'operator' => 'IN'
        )
    )
);

$related_policies = new WP_Query( $related_policies_args );
if ( $related_policies->have_posts() ) {
?>
    <div class="related-by-tag">
        <h3 class="screamer saorange">Related Changes by Tag</h3>
            <?php
            while ( $related_policies->have_posts() ): $related_policies->the_post();
            ?>
                <div class="third-block">
                    <div id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
                        <div class="entry-content">
                            <header class="entry-header clear">
                                <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h4>
                                <?php the_excerpt(); ?>
                            </header>

                        </div> <!-- entry-content -->
                    </div>
                </div>
            <?php
            endwhile; // end of the loop.
            ?>
    </div>
<?php
} // if ( $related_policies->have_posts() )
?>
</div> <!-- .related-policies -->

<script type="text/javascript">
    function changeDial(id) {
    var geoid = '<?php echo $geoid; ?>';
    var s = document.createElement("script");
    s.type = "text/javascript";
    s.src = "http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + id;

    var dial = document.getElementById('dial');
    if (!document._write) document._write = document.write;
    document.write = function (str) {
        dial.innerHTML += str;
    };

    while (dial.firstChild) {  dial.removeChild(dial.firstChild); }
    dial.appendChild(s);
    }
</script>