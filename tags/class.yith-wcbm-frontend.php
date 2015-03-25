<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Badges Management
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
            add_filter('woocommerce_single_product_image_html',array( $this, 'show_badge_on_product' ));
            // Action to add custom badge in shop page
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
            add_action('woocommerce_before_shop_loop_item_title', array($this, 'show_badge_on_thumbnail'));

            // add frontend css
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

            // edit sale flash badge
            add_filter('woocommerce_sale_flash', array($this, 'sale_flash'));

       }

        /**
         * Hide or show default sale flash badge
         *
         * @access public
         * @return string
         * @param $val value of filter woocommerce_sale_flash
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function sale_flash($val){
            $hide_on_sale_default = get_option( 'yith-wcbm-hide-on-sale-default' ) == 'yes' ? true : false;
            
            global $post;
            $product_id = $post->ID;
            $bm_meta = get_post_meta( $product_id, '_yith_wcbm_product_meta', true);
            $id_badge = ( isset( $bm_meta[ 'id_badge' ] ) ) ? $bm_meta[ 'id_badge' ] : ''; 

            if( $hide_on_sale_default || $id_badge != ''){
                return '';
            }
            return $val;
        }

        /**
         * Edit thumbnails in shop page
         *
         * @access public
         * @return void
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_badge_on_thumbnail() {
            echo self::show_badge_on_product(woocommerce_get_product_thumbnail());
        }

        /**
         * Edit image in products
         *
         * @access public
         * @return void
         * @param $val product image
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_badge_on_product ( $val ) {
            $badge_container = "<div class='container-image-and-badge'>". $val;

            global $post;

            $product_id = $post->ID;
            $bm_meta = get_post_meta( $post->ID, '_yith_wcbm_product_meta', true);
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
            wp_enqueue_style( 'yith_wcbm_badge_style', YITH_WCBM_ASSETS_URL . '/css/yith_wcbm_frontend.css');
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
