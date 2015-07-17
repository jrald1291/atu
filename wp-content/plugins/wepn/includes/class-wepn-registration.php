<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

session_start();
if ( !class_exists('WEPN_Registration') ) {
    class WEPN_Registration {
        var $errors = null;

        function __construct() {
            $this->init_hooks();
        }

        private function init_hooks() {
            add_shortcode( 'wepn_registration', array( $this, 'render' ) );
            add_action( 'init', array( $this, 'validate' ) );
        }



        public function validate() {
            if ( ! isset( $_POST['wepn_registration_nonce_field'] )
                || ! wp_verify_nonce( $_POST['wepn_registration_nonce_field'], 'wepn_registration' )
                || is_admin() ) {
                return false;
            }

            global $wpdb;

            $input = $_POST;
            $rules = array(
                'username' => 'required|unique:'. $wpdb->prefix .'users,user_login|min:4',//,ID,1
                'password' => 'required|min:5',
                'password_confirmation' => 'same:password',
                'email' => 'required|email',
                'phone' => 'required',
                'website' => 'url',
                'first_name' => 'required',
                'last_name' => 'required',

            );

            $validate_registration_code = get_option('wepn_validate_registration_code', 'no');

            if ( $validate_registration_code == 'yes' ) {
                $rules['registration_code'] = 'exists:' . $wpdb->prefix . WEPN_TBL_PREFIX . 'registration_code,code';
            }




            $validator = new WEPN_Validator();
            $validator = $validator->make($input, $rules);



            if ( $validator->success() ) {

                $this->add_new_user( $input );
            }

        }

        private function add_new_user($post) {

            $new_user_id = wp_insert_user(array(
                    'user_login'		=> $post['username'],
                    'user_pass'	 		=> $post['password'],
                    'user_email'		=> $post['email'],
                    'first_name'		=> $post['first_name'],
                    'last_name'			=> $post['last_name'],
                    'user_url'          => $post['website'],
                    'description'       => $post['description'],
                    'user_registered'	=> date('Y-m-d H:i:s'),
                    'role'				=> get_option('wepn_default_user_role', get_option('default_role'))
                )
            );


            if ( ! is_wp_error( $new_user_id ) ) {


                global $wpdb;

                add_user_meta( $new_user_id, 'company_name', $post['company_name'] )
                or update_user_meta( $new_user_id, 'company_name', $post['company_name']  );

                add_user_meta( $new_user_id, 'mobile', $post['mobile'] )
                or update_user_meta( $new_user_id, 'mobile', $post['mobile']  );

                add_user_meta( $new_user_id, 'phone', $post['phone'] )
                or update_user_meta( $new_user_id, 'phone', $post['phone']  );



                $city = $post['city'];
                $group = explode( '::', $post['group'] );
                $category = $post['category'];
                $company_name = esc_attr( $post['company_name'] );

                $group_title = $group[1];
                $group_slug = sanitize_title( $group[0] );

                $category_slug = sanitize_title( $category );




                /* Sets the terms (we're just using a single term) for the user. */

                if ( ! $parent = term_exists( $group_title, $city ) ) {
                    $parent = wp_insert_term( $group_title, $city, array(
                        'slug' => $group_slug,
                    ));
                }


                if ( ! term_exists( $category, $city ) ) {

                    wp_insert_term($category, $city, array(
                        'slug' => $category_slug,
                        'parent' => $parent['term_id']
                    ));

                }


                $company_id = $wpdb->get_var("SELECT ID FROM wp_posts WHERE post_title = '" . $company_name . "'");

                if ( ! empty( $company_name ) && ! $company_id ) {
                    $company_id = wp_insert_post( array(
                        'post_title' => $company_name,
                        'post_type' => 'vendor',
                        'post_status' => 'publish'
                    ));

                    update_user_meta( $new_user_id, 'company', $company_id );

                }



                wp_delete_object_term_relationships( $company_id, $city );


                wp_set_object_terms($company_id, $category_slug, $city, false);
                update_post_meta( $company_id, 'vendor', $new_user_id );
                update_post_meta( $company_id, 'region', $group_slug );
                update_post_meta( $company_id, 'city', $city );

                // Update custom permalink
                update_post_meta( $company_id, 'custom_permalink', $city.'/'.$group_slug.'/'. $category_slug .'/'. sanitize_title($company_name) );


                update_user_meta( $new_user_id, 'city', $city );
                update_user_meta( $new_user_id, 'group', $group_slug );
                update_user_meta( $new_user_id, 'category', $category_slug );



                clean_object_term_cache( $new_user_id, $city );



                // Update registration code to inactive
                WEPN_Admin_Settings::set_used_reg_code( $post['registration_code'] );

                $page_id = get_option( 'wepn_registration_success_page' );




                exit( wp_redirect( get_page_link( $page_id ) ) );


            } else {

                WEPN_Notify::add( $new_user_id->errors, 'error' );
            }
        }

        private static function add_current_code( $code ) {

            $_SESSION['wepn_registration']['code'] = $code;
        }

        private static function get_current_code() {
            return  isset( $_SESSION['wepn_registration']['code'] ) ? $_SESSION['wepn_registration']['code'] : '';
        }


        private function get_post_value( $key, $default = '' ) {
            if ( ! $_POST ) return $default;
            return ! isset( $_POST[$key] ) ? $default : $_POST[$key];
        }

        private function get_fields() {





            $fields = array(
                array(
                    'type'      => 'form',
                    'action'    => '#',
                    'method'    => 'post',
                    'attributes'    => array(
                        'class' => 'form form-labeled',
                    ),
                    'id'        => 'registrationForm'
                ),

                array(
                    'title'     => __( 'Username', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'username',
                    'placeholder'   => '',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'required'  => true,
                    'value'     => $this->get_post_value( 'username' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Password', 'atu' ),
                    'type'      => 'password',
                    'id'        => 'password',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'password' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Confirm Password' ),
                    'type'      => 'password',
                    'id'        => 'password_confirmation',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'password_confirmation' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Email', 'atu' ),
                    'type'      => 'email',
                    'id'        => 'email',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'email' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Phone', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'phone',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'value'     => $this->get_post_value( 'phone' ),
                    'default'   => '',
                ),


                array(
                    'title'     => __( 'Mobile', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'mobile',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'value'     => $this->get_post_value( 'mobile' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Website', 'atu' ),
                    'type'      => 'url',
                    'id'        => 'website',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'value'     => $this->get_post_value( 'website' ),
                    'default'   => '',
                ),




                array(
                    'title'     => __( 'Company Name', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'company_name',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'company_name' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Description', 'atu' ),
                    'type'      => 'textarea',
                    'id'        => 'description',
                    'attributes'    => array(
                        'class' => 'form-control',
                        'rows'  => 3,
                        'cols'  => 50
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'description' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'First Name', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'first_name',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'first_name' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'Last Name', 'atu' ),
                    'type'      => 'text',
                    'id'        => 'last_name',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'placeholder'   => '',
                    'required'  => true,
                    'value'     => $this->get_post_value( 'last_name' ),
                    'default'   => '',
                ),

                array(
                    'title'     => __( 'City', 'atu' ),
                    'type'      => 'select',
                    'id'        => 'city',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'required'  => true,
                    'value'     => '',
                    'default'   => '0',
                    'options'   => WEPN_Helper::city_lists()
                ),

                array(
                    'title'     => __( 'Region', 'atu' ),
                    'type'      => 'select',
                    'id'        => 'group',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'required'  => true,
                    'value'     => '',
                    'default'   => '0',
                    'options'   => WEPN_Helper::region_lists()
                ),

                array(
                    'title'     => __( 'Category', 'atu' ),
                    'type'      => 'select',
                    'id'        => 'category',
                    'attributes'    => array(
                        'class' => 'form-control'
                    ),
                    'required'  => true,
                    'value'     => '',
                    'default'   => '0',
                    'options'   => WEPN_Helper::category_list()
                ),




                array(
                    'title'     => __( 'Register', 'atu' ),
                    'type'      => 'button',
                    'id'        => 'register',
                    'attributes'    => array(
                        'class' => 'btn btn-primary btn-block btn-lg'
                    ),
                    'button_type'   => 'submit',
                    'value'     => 'register'
                ),

                array(
                    'type'  => 'nonce_field',
                    'id'    => 'wepn_registration_nonce_field',
                    'action'    => 'wepn_registration'
                ),

                array(
                    'type'      => 'hidden',
                    'id'        => 'registration_code',
                    'value'     => self::get_current_code(),
                    'default'   => '',
                ),

                array(
                    'type'      => 'form-end',
                    'id'        => 'registrationForm'
                ),

            );

            return $fields;
        }

        public function render() {
            global $wpdb;

            ob_start();


            //Check if user is already loggedin
            if ( is_user_logged_in() ) {
                return __('You are already logged in.', WEPN_TEXT_DOMAIN);
            }

            // check to make sure user registration is enabled
            $registration_enabled = get_option('users_can_register');
            if ( !$registration_enabled ) {

                return __('User registration is not enabled', WEPN_TEXT_DOMAIN);
            }

            //Check if registration code confirmation is enabled
            $validate_registration_code = get_option('wepn_validate_registration_code', 'no');

            if ( $validate_registration_code == 'yes' ) {

                $code = ! isset( $_GET['reg_code'] ) ?  self::get_current_code() : $_GET['reg_code'];
                //  check registration code is exists and active
                if ( WEPN_Admin_Settings::validate_code( esc_attr( $code ) ) == 0 ) {
                    return __( 'Registration code does not exists', 'atu' );
                } else {
                    self::add_current_code( $code );
                }

            }

            $fields = $this->get_fields();



            WEPN_Notify::display();

            WEPN_Form_Builder::create( $fields );

            return ob_get_clean();
        }
    }


    $registration = new WEPN_Registration();
}

