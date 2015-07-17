<?php
/**
 * Plugin Name: All Tied Up
 * Description: All Tied up
 * Plugin URI: http://www.alltiedup.com
 * Author: Sergio D. Casquejo
 * Author URI: http://casquejs.freevar.com
 * Version: 1.0
 * Text Domain: wepn
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
//error_reporting(0);


class WEPN {
    var $wepn_db_version = '1.0';

    function __construct() {
        $this->define_constant();
        $this->init_hooks();
        $this->includes();

    }


    function wepn_theme_setup() {
        add_image_size( 'gallery-thumb', 186, 186, true ); // (cropped)
        add_image_size( 'venue-medium', 553, 372, true ); // (cropped)
        add_image_size( 'venue-small-thumb', 110, 75, true ); // (cropped)
        add_image_size( 'venue-xs-small-thumb', 60, 60, true ); // (cropped)
        add_image_size( 'vendor-small-thumb', 110, 105, true ); // (cropped)
    }

    function enqueue_scripts() {
        wp_enqueue_style( 'wepn-css', WEPN_ASSETS_URL . 'css/wepn.css' );
        wp_enqueue_script( 'wepn-js', WEPN_ASSETS_URL . 'js/wepn.js', array('jquery'), false, true );
        wp_localize_script( 'wepn-js', 'ATU', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ) );

        if ( is_single() ) {
            wp_enqueue_script('acf-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array(), false, true);
            wp_enqueue_script('wepn-map', WEPN_ASSETS_URL . 'js/google-map.js', array('acf-map'), false, true);
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


    function wepn_install() {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();

        $reg_code_tbl_name = $wpdb->prefix . WEPN_TBL_PREFIX . 'registration_code';

        $sql = "CREATE TABLE $reg_code_tbl_name (
            id mediumint(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            code varchar(120) NOT NULL,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            date_used datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (code)
        ) $charset_collate";



        dbDelta( $sql );

        $gallery_tbl_name = $wpdb->prefix . WEPN_TBL_PREFIX . 'user_gallery';
        $sql = "CREATE TABLE $gallery_tbl_name (
            id mediumint(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            filename varchar(120) NOT NULL,
            file varchar(255) NOT NULL,
            path varchar(255) NOT NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            sort VARCHAR(255) DEFAULT 0 NOT NULL
        ) $charset_collate";

        dbDelta( $sql );


        add_option( 'wepn_db_version', $this->wepn_db_version );

    }

    function  wepn_update_db_check() {

        if ( get_site_option( 'wepn_db_version' ) != $this->wepn_db_version ) {
            $this->wepn_install();
        }
    }


    /**
     * Hook into actions and filters
     * @since  2.3
     */
    private function init_hooks() {
        // Install database tables
        register_activation_hook( __FILE__, array( $this, 'wepn_install' ) );
        register_activation_hook( __FILE__, array( $this, 'add_role' ) );
        // Update database check
        add_action( 'plugins_loaded', array( $this, 'wepn_update_db_check' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'after_setup_theme', array( $this, 'wepn_theme_setup' ) );
        add_action( 'aut_post_thumnail', array( $this, 'wepn_post_thumbnail' ), 1, 2 );




        add_action( 'wepn_pagination', array( $this, 'wepn_do_pagination' ) );

        add_action( 'wepn_venue_search_form', array( $this, 'wepn_venue_search_form' ) );
        add_action( 'wepn_vendor_search_form', array( $this, 'wepn_vendor_search_form' ) );



        add_filter('posts_where', array( $this, 'websmart_search_where' ) );
        add_filter('posts_join', array( $this, 'websmart_search_join' ) );
        add_action( 'pre_get_posts', array( $this, 'wepn_advance_search' ) );


        add_action( 'wepn_venue_region_list', array( $this, 'wepn_region_list' ) );

        add_filter('template_include', array( $this, 'template_chooser' ) );


        /**
         * remove the register link from the wp-login.php script
         */
        add_filter('option_users_can_register', function($value) {
            $script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));

            if ($script == 'wp-login.php') {
                $value = false;
            }

            return $value;
        });


    }



    public function template_chooser($template)
    {
        global $wp_query;
        $post_type = $_GET['post_type'];
        if( $wp_query->is_search && $post_type == 'venue' )
        {
            return locate_template('archive-venue.php');
        } elseif( $wp_query->is_search && $post_type == 'vendor' )
        {
            return locate_template('archive-vendor.php');
        }
        return $template;
    }





    public function wepn_advance_search( $query ) {

        if ( ! $query->is_search || is_admin() ) return $query;

        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'venue' && is_post_type_archive( 'venue' ) ) {

            $query->set('post_type', array('venue'));
            if ( isset( $_GET['venue-category'] ) && $_GET['venue-category'] != -1 ) {


                $query->set('tax_query', array(
//                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'venue-category',
                        'field' => 'id',
                        'terms' => array(intval($_GET['venue-category'])),
//                        'operator' => 'IN'
                    )
                ));


            }

        } elseif ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'vendor' && is_post_type_archive( 'vendor' ) ) {
            $query->set('post_type', array('vendor'));

            if ( isset( $_GET['category'] ) && ! empty( $_GET['category'] )  ) {
                $query->set('tax_query', array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => isset($_GET['city']) && !empty($_GET['city']) ? esc_attr( $_GET['city'] ) : 'sydney',
                        'field' => 'slug',
                        'terms' => array(esc_attr($_GET['category'])),
                        'operator' => 'IN'
                    )
                ));
            }
        }


        return $query;
    }




    public function websmart_search_join( $join ) {
        global $wpdb;

        if ( ! is_search() && is_admin() ) return $join;


        if( ( isset( $_GET['post_code'] ) && ! empty( $_GET['post_code'] ) )
            || ( isset( $_GET['capacity'] ) && ! empty( $_GET['capacity'] ) )
            || ( isset( $_GET['city'] ) && ! empty( $_GET['city'] ) )
            || ( isset( $_GET['region'] ) && ! empty( $_GET['region'] ) ) ) {
            $join .= " LEFT JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id) ";
        }



        return $join;
    }



    public function websmart_search_where( $where ) {

        if ( ! is_search() && is_admin() ) return $where;

        $where = str_replace('0 = 1', '1 = 1', $where);

        if( isset( $_GET['post_code'] ) && ! empty( $_GET['post_code'] ) ) {

            $where .= " AND ( m.meta_key = 'post_code' AND m.meta_value='". esc_attr( $_GET['post_code'] ) ."' ) ";
        }

        if( isset( $_GET['city'] ) && ! empty( $_GET['city'] ) ) {

            $where .= " AND ( m.meta_key = 'city' AND m.meta_value='". esc_attr( $_GET['city'] ) ."' ) ";
        }

        if( isset( $_GET['capacity'] ) && ! empty( $_GET['capacity'] ) ) {

            $where .= " AND ( m.meta_key = 'capacity' AND m.meta_value='". esc_attr( $_GET['capacity'] ) ."' ) ";
        }

        if ( isset( $_GET['region'] ) && ! empty( $_GET['region'] ) ) {
            $where .= " AND ( m.meta_key = 'region' AND m.meta_value='". esc_attr( $_GET['region'] ) ."' ) ";
        }



        return $where;
    }


    public function wepn_vendor_search_form() {
        ?>

        <form id="vendorSearchForm" action="<?php echo home_url( '/' ); ?>" class="form">
            <div class="row row-sm">
                <div class="col-md-3">
                    <div class="form-group">
                    <input type="text" name="s" value="<?php echo isset( $_GET['s'] ) ? $_GET['s'] : ''; ?>" class="form-control input-block" placeholder="<?php _e( 'Keyword...', 'atu' ); ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <?php WEPN_Helper::dropwdown_cities(); ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <?php WEPN_Helper::dropwdown_regions(); ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                            <?php WEPN_Helper::dropwdown_vendor_category(array(
                                'selected' => isset( $_REQUEST['category'] ) ? $_REQUEST['category'] : ''
                            )); ?>
                    </div>
                </div>
                <div class="col-md-2">

                    <input type="hidden" name="post_type" value="vendor">
                    <button class="btn btn-secondary btn-block" ><?php _e( 'Search', 'atu' ); ?></button>
                </div>
            </div>
        </form>
    <?php
    }



    public function wepn_venue_search_form() {

        ?>
        <form id="venueSearchForm" action="<?php echo home_url( '/' ); ?>" method="get" class="form">
            <div class="row row-sm">

                <div class="col-md-2">
                    <div class="form-group">
                        <?php
                        $selected_post_code = isset( $_REQUEST['post_code'] ) ? $_REQUEST['post_code'] : '';
                        $post_codes = get_field( 'post_codes', 'option' );

                        $post_codes_array = explode( "\r\n", $post_codes );



                        if ( count( $post_codes_array ) != 0 ) {
                            echo '<select name="post_code" class="form-control">';
                            echo '<option value=""'. selected('', $selected_post_code) .'>-- Post Code --</option>';
                            foreach ( $post_codes_array as $post_code ) {
                                $post_code = wp_strip_all_tags( $post_code );
                                echo '<option value="'. $post_code .'" '. selected( $post_code, $selected_post_code, false ) .'>'. $post_code .'</option>';
                            }
                            echo '<select>';
                        }?>
                    </div>
                </div>



                <div class="col-md-3">
                    <div class="form-group">
                        <?php WEPN_Helper::dropwdown_cities(); ?>
                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">

                        <?php
                        $selected_capacity = isset( $_REQUEST['capacity'] ) ? $_REQUEST['capacity'] : '';
                        $capacities = get_field( 'capacity', 'option' );

                        $capacities_array = explode( "\r\n", $capacities );



                        if ( count( $capacities_array ) != 0 ) {
                            echo '<select name="capacity" class="form-control">';
                            echo '<option value=""'. selected('', $selected_capacity) .'>-- Capacity --</option>';
                            foreach ( $capacities_array as $capacity ) {
                                $capacity = wp_strip_all_tags( $capacity );
                                echo '<option value="'. $capacity .'" '. selected( $capacity, $selected_capacity, false ) .'>'. $capacity .'</option>';
                            }
                            echo '<select>';
                        }?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <?php wp_dropdown_categories( array(
                            'taxonomy'  => 'venue-category',
                            'name'               => 'venue-category',
                            'selected'              => isset( $_GET['venue-category'] ) ? $_GET['venue-category'] : '-1',
                            'hide_empty'         => 0,
                            'class'              => 'form-control',
                            'show_option_none'   => '-- Select Category --',
                            'option_none_value'  => '-1',
                        ) ); ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <input type="hidden" name="s" value="">
                    <input type="hidden" name="post_type" value="venue">
                    <button class="btn btn-secondary btn-block" ><?php _e( 'Search', 'atu' ); ?></button>
                </div>
            </div>
        </form>
    <?php
    }


    public function wepn_do_pagination() {
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
        if ($total_page!=1 and $page!=$total_page) {
            echo '<a class="nextpostslink" rel="next" href="'. $current_url  .'page/'. $next_page .'">»</a>';
        }        

        echo '</div>';
        echo '</div>';
    }






    public function wepn_post_thumbnail( $size = 'venue-medium', $attr = array( 'alt' => 'Venue image' ) ) {
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
        $this->define( 'WEPN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define( 'WEPN_PLUGIN_URL', plugin_dir_url(__FILE__) );
        $this->define( 'WEPN_INCLUDES_DIR', WEPN_PLUGIN_DIR . 'includes' );
        $this->define( 'WEPN_ASSSETS_DIR', WEPN_PLUGIN_DIR . 'assets' );
        $this->define( 'WEPN_ASSETS_URL', WEPN_PLUGIN_URL . 'assets/' );
        $this->define( 'WEPN_CLASSES_DIR', WEPN_PLUGIN_DIR . 'classes' );
        $this->define( 'WEPN_TBL_PREFIX', 'wepn_' );
        $this->define( 'WEPN_TEXT_DOMAIN', 'wepn' );
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
        include_once('includes/class-wepn-install.php');
    }
}

$GLOBALS['WEPN'] = new WEPN();