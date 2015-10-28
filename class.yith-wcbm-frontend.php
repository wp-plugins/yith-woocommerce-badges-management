<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Badge Management
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCBM' ) ) { exit; } // Exit if accessed directly

require_once('functions.yith-wcbm.php');

if( ! class_exists( 'YITH_WCBM_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCBM_Frontend {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCQV_Frontend
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCBM_VERSION;


        private $is_in_sidebar = false;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCQV_Frontend
         * @since 1.0.0
         */
        public static function get_instance(){
            if( is_null( self::$instance ) ){
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct() {
            
            // Action to add custom badge in single product page
            add_filter('woocommerce_single_product_image_html',array( $this, 'show_badge_on_product' ) , 10 , 2);
            
            // add frontend css
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

            // edit sale flash badge
            add_filter('woocommerce_sale_flash', array($this, 'sale_flash') , 10 , 2 );

            // POST Thumbnail [to add custom badge in shop page]
            add_filter('post_thumbnail_html', array($this, 'add_box_thumb') , 10 , 2);
            
            // action to set this->is_in_sidebar
            add_action('dynamic_sidebar_before', array($this, 'set_is_in_sidebar') );
            add_action('dynamic_sidebar_after', array($this, 'unset_is_in_sidebar') );
       }

        public function add_box_thumb( $thumb , $post_id ){
            if( ! $this->is_in_sidebar() && !is_cart() && !is_checkout() ){
                return self::show_badge_on_product($thumb , $post_id);
            }else{
                return $thumb;
            }
        }

        /**
         * Set this->is in sidebar to true
         *
         * @access public
         * @since  1.1.4
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function set_is_in_sidebar(){
            $this->is_in_sidebar = true;
        }

        /**
         * Set this->is in sidebar to false
         *
         * @access public
         * @since  1.1.4
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function unset_is_in_sidebar(){
            $this->is_in_sidebar = false;
        }

        /**
         * Return true if is in sidebar
         *
         * @access public
         * @return bool
         * @since  1.1.4
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function is_in_sidebar(){
            return $this->is_in_sidebar;
        }

        /**
         * Hide or show default sale flash badge
         *
         * @access public
         * @return string
         * @param $val string value of filter woocommerce_sale_flash
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function sale_flash( $val , $post ){
            $hide_on_sale_default = get_option( 'yith-wcbm-hide-on-sale-default' ) == 'yes' ? true : false;

            $product_id = $post->ID;

            $product_id = $this->get_wpml_parent_id( $product_id );

            $bm_meta = get_post_meta( $product_id, '_yith_wcbm_product_meta', true);
            $id_badge = ( isset( $bm_meta[ 'id_badge' ] ) ) ? $bm_meta[ 'id_badge' ] : ''; 

            if( $hide_on_sale_default || $id_badge != ''){
                return '';
            }
            return $val;
        }

        /**
         * Edit image in products
         *
         * @access public
         * @return string
         * @param $val string product image
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_badge_on_product ( $val , $product_id ) {
            // prevent multiple badge copies
            if( strpos($val, 'container-image-and-badge') > 0)
                return $val;

            $badge_container = "<div class='container-image-and-badge'>". $val;

            $product_id = $this->get_wpml_parent_id( $product_id );

            $bm_meta = get_post_meta( $product_id , '_yith_wcbm_product_meta', true);
            $id_badge = ( isset( $bm_meta[ 'id_badge' ] ) ) ? $bm_meta[ 'id_badge' ] : ''; 
            if( ! defined( 'YITH_WCBM_PREMIUM' )){
                $badge_container .= yith_wcbm_get_badge($id_badge, $product_id);
            }else{
                $badge_container .= yith_wcbm_get_badges_premium($id_badge, $product_id);
            }

            $badge_container .= "</div><!--container-image-and-badge-->";
            return $badge_container;

        }
        public function enqueue_scripts(){
            wp_enqueue_style( 'yith_wcbm_badge_style', YITH_WCBM_ASSETS_URL . '/css/frontend.css');
            wp_enqueue_style('googleFontsOpenSans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300');
        }

        public function get_wpml_parent_id( $product_id ) {

            global $sitepress;
            if ( isset( $sitepress ) ) {

                $default_language = $sitepress->get_default_language();

                if ( function_exists( 'icl_object_id' ) ) {
                    $product_id = icl_object_id( $product_id, 'product', true, $default_language );
                }
                else {
                    if ( function_exists( 'wpml_object_id_filter' ) ) {
                        $product_id = wpml_object_id_filter( $product_id, 'product', true, $default_language );
                    }
                }

            }

            return $product_id;
        }
    }
}
/**
 * Unique access to instance of YITH_WCBM_Frontend class
 *
 * @return \YITH_WCBM_Frontend
 * @since 1.0.0
 */
function YITH_WCBM_Frontend(){
    return YITH_WCBM_Frontend::get_instance();
}
?>
