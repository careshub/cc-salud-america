<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Community_Commons_Salud_America
 * @author    David Cavins
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 *
 * @wordpress-plugin
 * Plugin Name:       CC Salud America
 * Plugin URI:        @TODO
 * Description:       Adds SA functionality to CC site
 * Version:           0.1.0
 * Author:            David Cavins
 * Author URI:        @TODO
 * Text Domain:       plugin-name-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function sa_class_init() {

    // Helper functions
    require_once( plugin_dir_path( __FILE__ ) . 'includes/sa-functions.php' );
    // Template output functions
    require_once( plugin_dir_path( __FILE__ ) . 'public/views/template-tags.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'public/views/shortcodes.php' );

    // The main class
    require_once( plugin_dir_path( __FILE__ ) . 'public/class-cc-salud-america.php' );

    // Extension classes
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sapolicies-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-saresources-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sa-success-stories-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sa-video-contests-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sa-take-action-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sa-tweetchats-cpt-tax.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-sa-term-intros-cpt-tax.php' );


    // Admin and dashboard functionality
    // if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        // require_once( plugin_dir_path( __FILE__ ) . 'admin/class-cc-group-narratives-admin.php' );
        // add_action( 'bp_include', array( 'CC_Group_Narratives_Admin', 'get_instance' ), 21 );
    // }

}
add_action( 'bp_include', 'sa_class_init' );


/* Only load the component if BuddyPress is loaded and initialized. */
function bp_startup_cc_salud_america_extension() {
	require_once( plugin_dir_path( __FILE__ ) . 'public/class-group-ext.php' );
}
add_action( 'bp_include', 'bp_startup_cc_salud_america_extension', 32 );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'CC_Salud_America', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CC_Salud_America', 'deactivate' ) );

/*
 * Helper function .
 * @return Fully-qualified URI to the root of the plugin.
 */
function sa_get_plugin_base_uri(){
    return plugin_dir_url( __FILE__ );
}