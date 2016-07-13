<?php
/**
 * Template tag for outputting the SA Leader Report.
 *
 * Community Commons Salud America
 *
 * @package   Community_Commons_Salud_America
 * @author    Yan Barnett
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 */

/**
 * Generate Leader Report within the group.
 *
 * Output is accomplished via a template tag, for easy insertion in group pages.
 *
 * @since   1.8.0
 *
 * @return  string The html for the leader report
 */
function sa_leader_report() {
    /*
     * Is there a geoid set? We determine whether to show the report or the county
     * selector based on this variable.
     */
    $geoid = isset( $_GET['geoid'] ) ? $_GET['geoid'] : '';
    ?>
    <div class="content-row clear">
        <?php
        if ( ! $geoid ) :
        ?>
        Our county selector should appear here.
        <?php
        else :
        ?>
        The report output for a geoid will appear here. The geoid selected is <?php echo $geoid ?>.
        <?php
        endif;
        ?>
    </div><!-- end .content-row -->
    <?php
}
