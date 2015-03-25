<?php
/**
 * Admin class
 *
 * @author Yithemes
 * @package YITH WooCommerce Badges Management
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBM' ) ) { exit; } // Exit if accessed directly

require_once('functions.yith-wcbm.php');

if( !class_exists( 'YITH_WCBM_Admin' ) ) {
    /**
     * Admin class.
	 * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCBM_Admin {
		
        /**
         * Single instance of the class
         *
         * @var \YITH_WCQV_Admin
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Plugin options
         *
         * @var array
         * @access public
         * @since 1.0.0
         */
        public $options = array();

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCBM_VERSION;

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = '#';

        /**
         * @var string Quick View panel page
         */
        protected $_panel_page = 'yith_wcbm_panel';

        /**
         * Various links
         *
         * @var string
         * @access public
         * @since 1.0.0
         */
        public $doc_url = 'http://yithemes.com/docs-plugins/yith-woocommerce-badge-management/';

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCBM
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

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBM_DIR . '/' . basename( YITH_WCBM_FILE ) ), array( $this, 'action_links') );
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            /* Registro il custom post_id type */
            add_action('init', array( $this, 'post_type_register'));

            // Action per le metabox
            add_action('save_post', array( $this, 'metabox_save'));
            add_action('save_post', array($this, 'badge_settings_save'));

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'badge_settings_tabs' ) );
		 }

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @return mixed
         * @use plugin_action_links_{$plugin_file_name}
         */
        public function action_links( $links ) {

            $links[] = '<a href="' . admin_url( "admin.php?page={$this->_panel_page}" ) . '">' . __( 'Settings', 'yith-wcbm' ) . '</a>';

            return $links;
        }

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @use plugin_row_meta
         */
        public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

            if ( defined( 'YITH_WCBM_FREE_INIT' ) && YITH_WCBM_FREE_INIT == $plugin_file ) {
                //$plugin_meta[] = '<a href="' . $this->doc_url . '" target="_blank">' . __( 'Plugin Documentation', 'yith-wcbm' ) . '</a>';
            }
            return $plugin_meta;
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'settings'      => __( 'Settings', 'yith-wcbm' )
                );


            if ( defined( 'YITH_WCBM_FREE_INIT' ) ) {
                //$admin_tabs['premium'] = __( 'Premium Version', 'yith-wcbm' );
            }

            if ( defined( 'YITH_WCBM_PREMIUM' ) ) {
                $admin_tabs['advanced'] = __( 'Advanced Options', 'yith-wcbm' );
                $admin_tabs['category'] = __( 'Category Badges', 'yith-wcbm' );
            }

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => __( 'Badges Management', 'yith-wcbm' ),
                'menu_title'       => __( 'Badges Management', 'yith-wcbm' ),
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCBM_DIR . '/plugin-options'
            );


            /* === Fixed: not updated theme  === */
            if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
        }

        public function admin_enqueue_scripts() {
            wp_enqueue_style( 'admin_init', YITH_WCBM_ASSETS_URL . '/css/yith_wcbm_admin.css');
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');

            $screen = get_current_screen();

            if( 'yith-wcbm-badge' == $screen->id  ) {
                if ( defined( 'YITH_WCBM_PREMIUM' ) ) {
                    wp_enqueue_script( 'yith_wcbm_metabox_options', YITH_WCBM_ASSETS_URL .'/js/metabox_options_premium.js', array('jquery', 'wp-color-picker'), '1.0.0', true );
                }else{
                    wp_enqueue_script( 'yith_wcbm_metabox_options', YITH_WCBM_ASSETS_URL .'/js/metabox_options.js', array('jquery', 'wp-color-picker'), '1.0.0', true );
                }
            }
        }

        /**
         * Register Badge custom post type with options metabox
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function post_type_register() {
            $labels = array(
                'name'              => __('Badges', 'yith-wcbm'),
                'singular_name'     => __('Badge', 'yith-wcbm'),
                'add_new'           => __('Add Badge', 'yith-wcbm'),
                'add_new_item'      => __('Add new Badge', 'yith-wcbm'),
                'edit_item'         => __('Edit Badge', 'yith-wcbm'),
                'view_item'         => __('View Badge', 'yith-wcbm'),
                'not_found'         => __('Badge not found', 'yith-wcbm'),
                'not_found_in_trash'=> __('Badge not found in trash', 'yith-wcbm')
            );

            $args = array(
                'labels'                    => $labels,
                'public'                    => true,
                'show_ui'                   => true,
                'menu_position'             => 10,
                'exclude_from_search'     => true,
                'capability_type'           => 'post',
                'map_meta_cap'              => true,
                'rewrite'                   => true,
                'has_archive'               => true,
                'hierarchical'              => false,
                'show_in_nav_menus'         => false,
                'menu_icon'                 => 'dashicons-visibility',
                'supports'                  => array('title'),
                'register_meta_box_cb'      => array($this, 'register_metabox')
            );

            register_post_type('yith-wcbm-badge', $args);
        }

        public function register_metabox(){
            add_meta_box('yith-wcbm-metabox', __('Badge Options', 'yith-wcbm'), array( $this, 'metabox_render'), 'yith-wcbm-badge', 'normal', 'high');
        }

        public function metabox_render( $post ){
            $bm_meta = get_post_meta( $post->ID, '_badge_meta', true);

            $default = array(
                'type'                          => 'custom',
                'text'                          => '', 
                'txt_color_default'             => '#000000', 
                'txt_color'                     => '#000000', 
                'bg_color_default'              => '#2470FF', 
                'bg_color'                      => '#2470FF', 
                'width'                         => '100', 
                'height'                        => '50',
                'position'                      => 'top-left',
                'image_url'                     => ''
            );

            $args = wp_parse_args( $bm_meta , $default );

            $args = apply_filters('yith_wcbm_metabox_options_content_args' , $args);
            
            yith_wcbm_metabox_options_content($args);
        }

        public function metabox_save( $post_id ) {
            if ( !empty( $_POST[ '_badge_meta' ] ) ){
                $badge_meta['type'] = ( !empty( $_POST[ '_badge_meta' ]['type'] ) ) ? $_POST[ '_badge_meta' ]['type'] : '';
                $badge_meta['text'] = ( !empty( $_POST[ '_badge_meta' ]['text'] ) ) ? $_POST[ '_badge_meta' ]['text'] : '';
                $badge_meta['txt_color'] = ( !empty( $_POST[ '_badge_meta' ]['txt_color'] ) ) ? $_POST[ '_badge_meta' ]['txt_color'] : '';
                $badge_meta['bg_color'] = ( !empty( $_POST[ '_badge_meta' ]['bg_color'] ) ) ? esc_url($_POST[ '_badge_meta' ]['bg_color']) : '';
                $badge_meta['width'] = ( !empty( $_POST[ '_badge_meta' ]['width'] ) ) ? $_POST[ '_badge_meta' ]['width'] : '';
                $badge_meta['height'] = ( !empty( $_POST[ '_badge_meta' ]['height'] ) ) ? $_POST[ '_badge_meta' ]['height'] : '';
                $badge_meta['position'] = ( !empty( $_POST[ '_badge_meta' ]['position'] ) ) ? $_POST[ '_badge_meta' ]['position'] : 'top-left';
                $badge_meta['image_url'] = ( !empty( $_POST[ '_badge_meta' ]['image_url'] ) ) ? $_POST[ '_badge_meta' ]['image_url'] : '';
                update_post_meta( $post_id, '_badge_meta', $badge_meta );
            }
        }


        /**
         * Add badge select In WC General Tab options metabox
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */

        function badge_settings_tabs(){
            global $post;
            $bm_meta = get_post_meta( $post->ID, '_yith_wcbm_product_meta', true);
            $id_badge = ( isset( $bm_meta[ 'id_badge' ] ) ) ? $bm_meta[ 'id_badge' ] : ''; 
            ?>

            <div class="options_group">
                <p class="form-field">
                    <label><?php echo __('Select Badge', 'yith-wcbm') ?></label>
                    <select name= "_yith_wcbm_product_meta[id_badge]" class="select short">
                        <option value="" selected="selected"><?php echo __('none', 'yith-wcbm') ?></option>
                        <?php
                        
                            $args = ( array('posts_per_page' => -1, 
                                            'post_type' => 'yith-wcbm-badge', 
                                            'orderby' => 'title', 
                                            'order' => 'ASC', 
                                            'post_status'=> 'publish') 
                            );
                            $badges = get_posts( $args );

                            foreach ($badges as $badge) {
                                ?><option value="<?php echo $badge->ID ?>" <?php selected($id_badge, $badge->ID ) ?>><?php echo get_the_title($badge->ID) ?></option><?php 
                            }

                        ?>
                    </select>
                </p><!-- form-field -->
            </div><!-- options_group -->

            <?php
        }
        public function badge_settings_save( $post_id ){
           if ( !empty( $_POST[ '_yith_wcbm_product_meta' ] ) ){
                $product_meta['id_badge'] = ( !empty( $_POST[ '_yith_wcbm_product_meta' ]['id_badge'] ) ) ? $_POST[ '_yith_wcbm_product_meta' ]['id_badge'] : '';
                update_post_meta( $post_id, '_yith_wcbm_product_meta', $product_meta );
            }
        }



    }
}

/**
 * Unique access to instance of YITH_WCBM_Admin class
 *
 * @return \YITH_WCBM_Admin
 * @since 1.0.0
 */
function YITH_WCBM_Admin(){
    return YITH_WCBM_Admin::get_instance();
}
?>
