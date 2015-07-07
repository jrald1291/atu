<?php
/**
 * Plugin Name: All Tied Up
 * Description: All Tied up
 * Plugin URI: http://www.alltiedup.com
 * Author: Sergio D. Casquejo
 * Author URI: http://casquejs.freevar.com
 * Version: 1.0
 * Text Domain: atu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
//error_reporting(0);
//add_rewrite_rule(
//    '^'.$user_slug.'/([^/]*)$',
//    'index.php?page_id='.$user_page_id.'&um_user=$matches[1]',
//    'top'
//);
class ATU {
    var $atu_db_version = '1.0';

    function __construct() {
        $this->define_constant();
        $this->init_hooks();
        $this->includes();
    }


    function atu_theme_setup() {
        add_image_size( 'gallery-thumb', 186, 186, true ); // (cropped)
        add_image_size( 'venue-listing', 221, 221, true ); // (cropped)
        add_image_size( 'venue-medium', 553, 372, true ); // (cropped)
        add_image_size( 'venue-small-thumb', 110, 75, true ); // (cropped)
        add_image_size( 'venue-xs-small-thumb', 60, 60, true ); // (cropped)
        add_image_size( 'vendor-small-thumb', 110, 105, true ); // (cropped)
    }


    function enqueue_scripts() {
        wp_enqueue_style( 'atu-css', ATU_ASSETS_URL . 'css/atu.css' );
        wp_enqueue_script( 'atu-js', ATU_ASSETS_URL . 'js/atu.js', array('jquery'), false, true );
        wp_localize_script( 'atu-js', 'ATU', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ) );

        if ( is_single() ) {
            wp_enqueue_script('acf-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array(), false, true);
            wp_enqueue_script('atu-map', ATU_ASSETS_URL . 'js/google-map.js', array('acf-map'), false, true);
        }
    }

    function add_role() {

        //remove_role( 'vendor' );
        add_role(
            'vendor',
            __( 'Vendor' ),
            array(
                'moderate_comments' => 1,
                'manage_categories' => 0,
                'manage_links' => 0,
                'upload_files' => 1,
                'unfiltered_html' => 1,
                'edit_posts' => 1,
                'edit_others_posts' => 0,
                'edit_published_posts' => 1,
                'publish_posts' => 1,
                'edit_pages' => 0,
                'read' => 1,
                'edit_others_pages' => 0,
                'edit_published_pages' => 0,
                'publish_pages' => 0,
                'delete_pages' => 0,
                'delete_others_pages' => 0,
                'delete_published_pages' => 0,
                'delete_posts' => 1,
                'delete_others_posts' => 0,
                'delete_published_posts' => 1,
                'delete_private_posts' => 1,
                'edit_private_posts' => 1,
                'read_private_posts' => 1,
                'delete_private_pages' => 0,
                'edit_private_pages' => 0,
                'read_private_pages' => 0,

            )
        );
    }


    function atu_install() {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();

        $reg_code_tbl_name = $wpdb->prefix . ATU_TBL_PREFIX . 'registration_code';

        $sql = "CREATE TABLE $reg_code_tbl_name (
            id mediumint(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            code varchar(120) NOT NULL,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            date_used datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (code)
        ) $charset_collate";



        dbDelta( $sql );

        $gallery_tbl_name = $wpdb->prefix . ATU_TBL_PREFIX . 'user_gallery';
        $sql = "CREATE TABLE $gallery_tbl_name (
            id mediumint(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            filename varchar(120) NOT NULL,
            file varchar(255) NOT NULL,
            path varchar(255) NOT NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            sort VARCHAR(255) DEFAULT 0 NOT NULL
        ) $charset_collate";

        dbDelta( $sql );


        add_option( 'atu_db_version', $this->atu_db_version );

    }

    function  atu_update_db_check() {

        if ( get_site_option( 'atu_db_version' ) != $this->atu_db_version ) {
            $this->atu_install();
        }
    }


    /**
     * Hook into actions and filters
     * @since  2.3
     */
    private function init_hooks() {
        // Install database tables
        register_activation_hook( __FILE__, array( $this, 'atu_install' ) );
        register_activation_hook( __FILE__, array( $this, 'add_role' ) );
        // Update database check
        add_action( 'plugins_loaded', array( $this, 'atu_update_db_check' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'after_setup_theme', array( $this, 'atu_theme_setup' ) );
        add_action( 'aut_post_thumnail', array( $this, 'atu_post_thumbnail' ), 1, 2 );
        add_action( 'pre_get_posts', array( $this, 'atu_advance_search' ) );
        add_action( 'atu_pagination', array( $this, 'atu_do_pagination' ) );

        add_action( 'atu_venue_search_form', array( $this, 'atu_venue_search_form' ) );
    }

    public function atu_venue_search_form() {
        ?>
        <form action="<?php echo home_url( '/' ); ?>" class="form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                    <input type="text" name="s" class="form-control input-block" placeholder="<?php _e( 'Keyword...', 'atu' ); ?>">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <div class="input-group-addon"><?php _e( 'Venue Category', 'atu' ); ?></div>
                            <?php wp_dropdown_categories( array(
                            'taxonomy'  => 'venue-category',
                            'name'               => 'venue-category',
                            'hide_empty'         => 0,
                            'class'              => 'form-control',
                            'show_option_none'   => '',
                            'option_none_value'  => '-1',
                            ) ); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="hidden" name="post_type" value="venue">
                    <button class="btn btn-secondary btn-block" ><span class="fa fa-search icon-l"></span><?php _e( 'Search Venue', 'atu' ); ?></button>
                </div>
            </div>
        </form>
    <?php
    }


    public function atu_do_pagination() {
        global $wp_query, $wp;
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

        // Get post type archive link
        //$post_type_archive_link = get_post_type_archive_link( 'venue' );
        // Get maximum number of page
        $total_row = $wp_query->max_num_pages;
        // Set row per page
        $per_page = 12;
        // Get total page
        $total_page = ceil( $total_row / $per_page );
        // Get current page
        $current_page = get_query_var('paged') ? get_query_var('paged') : 1;
        // Get next page
        $next_page = $total_page <= $current_page ? $current_page : $current_page + 1;

        echo '<div class="pagination">';
        echo '<label for="">' . __( 'Pagination', 'atu') . ' :</label>';
        echo '<div class="wp-pagenavi">';
        echo '<span class="pages">Page '. $current_page .' of '. $total_page .'</span>';

        for( $i = 1; $i <= $total_page; $i++ ):

            if ( $i == $current_page ):

                echo '<span class="current">'. $i .'</span>';

            else:

                echo '<a class="page larger" href="'. $current_url  .'page/'. $i .'">'. $i .'</a>';

            endif;

        endfor;

        echo '<a class="nextpostslink" rel="next" href="'. $current_url  .'page/'. $next_page .'">Next</a>';
        echo '</div>';
        echo '</div>';
    }

    public function atu_advance_search( $query ) {

        if ( !$query->is_search )
            return $query;

        if ( isset( $_REQUEST['venue-category'] ) ) {

            $query->set( 'post_type', array( 'venue' ) );
            $query->set( 'tax_query', array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'venue-category',
                    'field' => 'id',
                    'terms' => array( intval($_REQUEST['venue-category']) ),
                    'operator' => 'IN'
                )
            ) );


        }

        return $query;
    }




    public function atu_post_thumbnail( $size = 'venue-medium', $attr = array( 'alt' => 'Venue image' ) ) {
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( $size, $attr );
        } else {
            echo '<img src="'. get_template_directory_uri() .'/images/placeholders/slide-single.jpg" alt="">';
        }
    }

    /**
     * Define ATU Constants
     */

    private function define_constant() {
        $this->define( 'ATU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define( 'ATU_PLUGIN_URL', plugin_dir_url(__FILE__) );
        $this->define( 'ATU_INCLUDES_DIR', ATU_PLUGIN_DIR . 'includes' );
        $this->define( 'ATU_ASSSETS_DIR', ATU_PLUGIN_DIR . 'assets' );
        $this->define( 'ATU_ASSETS_URL', ATU_PLUGIN_URL . 'assets/' );
        $this->define( 'ATU_CLASSES_DIR', ATU_PLUGIN_DIR . 'classes' );
        $this->define( 'ATU_TBL_PREFIX', 'atu_' );
        $this->define( 'ATU_TEXT_DOMAIN', 'atu' );
    }


    /**
     * Define constant if not already set
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    private function includes() {
        include_once( 'includes/class-atu-install.php' );
    }
}

$GLOBALS['ATU'] = new ATU();
