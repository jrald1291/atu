<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists('WEPN_Admin_Menu') ) {
    class WEPN_Admin_Menu {
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
            //add_action( 'admin_menu', array( $this, 'my_add_profession_admin_page' ), 10 );
        }

        public function admin_menu() {

            add_menu_page( __( 'WEPN', WEPN_TEXT_DOMAIN ), __( 'WEPN', WEPN_TEXT_DOMAIN ), 'administrator', 'wepn-settings', null, 'dashicons-id-alt', null);
            add_submenu_page( 'wepn-settings', __( 'Account', WEPN_TEXT_DOMAIN ), __( 'Account', WEPN_TEXT_DOMAIN ), 'administrator', 'wepn-settings', array( $this, 'settings_page' ) );
            add_submenu_page( 'wepn-settings', __( 'Vendors', WEPN_TEXT_DOMAIN ), __( 'Vendors', WEPN_TEXT_DOMAIN ), 'administrator', 'users.php?role=vendor' );


//            if ( have_rows( 'cities', 'option' ) ) {
//                while ( have_rows( 'cities', 'option' ) ) {
//                    the_row();
//                    $name = sanitize_title(get_sub_field('city_name'));
//                    $label = esc_html(get_sub_field('city_label'));
//
//                    add_submenu_page( 'wepn-settings', __( $label, 'wepn' ), __( $label, 'wepn' ), 'administrator', 'edit-tags.php?taxonomy=' . $name );
//                }
//            }


        }




        public function settings_menu() {

        }

        public function settings_page() {
            WEPN_Admin_Settings::output();
        }
    }



}

return new WEPN_Admin_Menu();