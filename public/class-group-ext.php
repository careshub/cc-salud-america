<?php 
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :

class CC_Salud_America_Policies extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'policies' ),
            'name' => sa_get_tab_label( $section = 'policies' ),
            'nav_item_position' => 11,
            'enable_nav_item' => $this->enable_cc_sa_policies_tab( bp_get_current_group_id() ),
            'access' => 'anyone', // Make this a publicly accessible tab
            'show_tab' => 'anyone', // Anyone can see the nav tab
            // We might want to add a setting screen to handle the page content?
            // Don't need create or edit screens for this plugin
            // 'screens' => array(
            //     // 'edit' => array(
            //     //     'name' => 'GE Example 2',
            //     //     // Changes the text of the Submit button
            //     //     // on the Edit page
            //     //     'submit_text' => 'Submit, suckaz',
            //     // ),
            //     // 'create' => array(
            //     //     'position' => 100,
            //     // ),
            // ),
        );
        parent::init( $args );
    }
 
    function display() {
        bp_get_template_part( 'groups/single/sapolicies/index' );
    }
 	
 	/****
    function settings_screen( $group_id ) {
        $setting = groups_get_groupmeta( $group_id, 'group_extension_example_2_setting' );
 
        ?>
        Save your plugin setting here: <input type="text" name="group_extension_example_2_setting" value="<?php echo esc_attr( $setting ) ?>" />
        <?php
    }
 
    function settings_screen_save( $group_id ) {
        $setting = isset( $_POST['group_extension_example_2_setting'] ) ? $_POST['group_extension_example_2_setting'] : '';
        groups_update_groupmeta( $group_id, 'group_extension_example_2_setting', $setting );
    }
    */

    function enable_cc_sa_policies_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_policies_tab', $setting );
    }
 
}
bp_register_group_extension( 'CC_Salud_America_Policies' );
 
endif;  // if ( class_exists( 'BP_Group_Extension' ) )