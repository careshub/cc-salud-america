<?php
/**
 * Generate for the public-facing pieces of the plugin.
 *
 * Community Commons Salud America
 *
 * @package   Community_Commons_Salud_America
 * @author    David Cavins
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 */

/**
 * Generate the Salud America footer text.
 *
 * @since   1.0.0
 *
 * @return  string The html for the text block
 */
add_action( 'bp_after_group_body', 'salud_america_footer' );
function salud_america_footer() {
    if ( sa_is_sa_group() ) :
    ?>
    <div class="salud-footer">  
        <p>Salud America!  is a national online network of researchers, community group leaders, decision-makers, and members of the public working together to support healthy policy and environmental changes that can help reverse obesity among Latino children.</p>
        <p>The network, funded by the Robert Wood Johnson Foundation, is a project of <a href="http://ihpr.uthscsa.edu/"> the Institute for Health Promotion Research (IHPR)</a> at <a href="http://uthscsa.edu/">The UT Health Science Center at San Antonio</a>.</p>
        <p>Policies, comments, external links, and contributed stories and images are not affiliated with Salud America!, RWJF, or The UT Health Science Center at San Antonio, nor do they necessarily reflect the views of or endorsement by these organizations.</p>
        <a href="http://http://www.rwjf.org/"><img class="alignright" src="/wp-content/themes/CommonsRetheme/img/salud_america/logo-rwjf_small.png" ></a>
    </div>
    <?php
    endif; 
}
/**
 * Generate archive navigation within Salud America.
 *
 * @since   1.0.0
 *
 * @param   string $html_id The id to apply to the nav
 * @param   int $paged The current page number
 * @param   int $paged The total number of pages of results
 * @return  string The html for the nav block
 */
function sa_section_content_nav( $html_id, $paged = 1, $total_pages = 1 ) {
    $html_id = esc_attr( $html_id );

    if ( $total_pages > 1 ) : ?>
        <nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
            <h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
            <div class="nav-previous"><a href="?paged=<?php echo $paged + 1; ?>">Older Posts</a></div>
            <?php if ( $paged > 1 ) : ?>
                <div class="nav-next"><a href="?paged=<?php echo $paged - 1; ?>">Newer Posts</a></div>
            <?php endif; ?>
        </nav><!-- #<?php echo $html_id; ?> .navigation -->
    <?php endif;
}