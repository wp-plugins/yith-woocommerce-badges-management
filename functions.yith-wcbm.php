<?php
/**
 * Functions
 *
 * @author Yithemes
 * @package YITH WooCommerce Badge Management
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBM' ) ) { exit; } // Exit if accessed directly


/**
 * Print the content of metabox options [Free Version]
 *
 * @return   void
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
if ( ! function_exists( 'yith_wcbm_metabox_options_content' ) ) {
	function yith_wcbm_metabox_options_content( $args ){
		extract( $args );
?>

        <div class="tab-container">
            <ul>
                <li><a id="btn-text" href="#tab-text"><?php echo __('Text Badge', 'yith-wcbm') ?></a></li>
                <li><a id="btn-image" href="#tab-image"><?php echo __('Image Badge', 'yith-wcbm') ?></a></li>
            </ul>

            <input class="update-preview" type="hidden" value="<?php echo $type ?>" data-type="<?php echo $type ?>" name= "_badge_meta[type]" id="yith-wcbm-badge-type">
            <input class="update-preview" type="hidden" value="<?php echo $image_url ?>" name= "_badge_meta[image_url]" id="yith-wcbm-image-url">
            <input id="yith-wcbm-url-for-images" type="hidden" value="<?php echo YITH_WCBM_ASSETS_URL . '/images/'; ?>">
            
            <div class="half-left">
                <div id="tab-text">
                    <div class="section-container">
                        <div class="section-title"> <?php echo __('Text Options', 'yith-wcbm') ?></div>
                        <table class="section-table">
                            <tr>
                                <td class="table-title">
                                    <label><?php echo __('Text', 'yith-wcbm') ?></label>
                                </td>
                                <td class="table-content">
                                    <input class="update-preview" type="text" value="<?php echo $text ?>" name= "_badge_meta[text]" id="yith-wcbm-text">
                                </td>
                            </tr>
                            <tr>
                                <td class="table-title">
                                    <label><?php echo __('Text Color', 'yith-wcbm') ?></label>
                                </td>
                                <td class="table-content">
                                    <input type="text" class="yith-wcbm-color-picker" name= "_badge_meta[txt_color]" value="<?php echo $txt_color ?>"
                                    data-default-color="<?php echo $txt_color_default; ?>" id="yith-wcbm-txt-color">
                                </td>
                            </tr>
                        </table>        
                    </div><!-- section-container -->

                    <div class="section-container">
                        <div class="section-title"> <?php echo __('Style Options', 'yith-wcbm') ?></div>
                        <table class="section-table">
                            <tr>
                                <td class="table-title">
                                    <label><?php echo __('Background Color', 'yith-wcbm') ?></label>
                                </td>
                                <td class="table-content">
                                    <input type="text" class="yith-wcbm-color-picker" name= "_badge_meta[bg_color]" value="<?php echo $bg_color ?>"
                                    data-default-color="<?php echo $bg_color_default; ?>" id="yith-wcbm-bg-color">
                                </td>
                            </tr>
                            <tr>
                                <td class="table-title table-align-top">
                                    <label><?php echo __('Size (pixel)', 'yith-wcbm') ?></label><br />
                                </td>
                                <td class="table-content">
                                    <table class="table-mini-title">
                                        <tr>
                                            <td>
                                                <input class="update-preview" type="text" size="4" value="<?php echo $width ?>" name= "_badge_meta[width]" id="yith-wcbm-width">
                                            </td>
                                            <td>
                                                <input class="update-preview" type="text" size="4" value="<?php echo $height ?>" name= "_badge_meta[height]" id="yith-wcbm-height">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo __('Width', 'yith-wcbm') ?>
                                            </th>
                                            <th>
                                                <?php echo __('Height', 'yith-wcbm') ?>
                                            </th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>        
                    </div><!-- section-container -->
                </div><!-- tab-text -->
                
                <div id="tab-image">
                    <div class="section-container">
                        <div class="section-title"> <?php echo __('Select the Image Badge', 'yith-wcbm') ?></div>
                        <div class="section-content-container">
                            <?php
                            for( $i = 1; $i<5; $i++ ){
                                $img_url = YITH_WCBM_ASSETS_URL . '/images/' . $i . '.png';
                                echo '<div class="yith-wcbm-select-image-btn button-select-image" badge_image_url="'. $i . '.png' .'" style="background-image:url('. $img_url .')">';
                                echo '</div>';
                            }
            
                            // Custom Image Badge Uploaded
                            echo "<div id='custom-image-badges'>"; 
                            echo "</div>";
                            ?>
                        </div> <!-- section-content-container -->
                    </div> <!-- section-container -->
                </div>

                <div class="section-container">
                        <div class="section-title"> <?php echo __('Position', 'yith-wcbm') ?></div>
                        <table class="section-table">
                            <tr>
                                <td class="table-title">
                                    <label><?php echo __('Position', 'yith-wcbm') ?></label>
                                </td>
                                <td class="table-content">
                                    <select class="update-preview" name= "_badge_meta[position]" id="yith-wcbm-position">
                                        <option value="top-left" <?php echo selected($position, 'top-left',false) ?>><?php echo __('top-left','yith-wcbm') ?></option>;
                                        <option value="top-right" <?php echo selected($position, 'top-right',false) ?>><?php echo __('top-right','yith-wcbm') ?></option>;
                                        <option value="bottom-left" <?php echo selected($position, 'bottom-left',false) ?>><?php echo __('bottom-left','yith-wcbm') ?></option>;
                                        <option value="bottom-right" <?php echo selected($position, 'bottom-right',false) ?>><?php echo __('bottom-right','yith-wcbm') ?></option>;
                                    </select>
                                </td>
                            </tr>
                        </table>        
                    </div><!-- section-container -->
            </div>

            <div class="half-right">
                    <h3 id="preview-title"> <?php echo __('Preview', 'yith-wcbm') ?> </h3>
                    <div id="preview-bg">
                        <div id="preview-badge">
                        </div>
                    </div>
            </div>
        </div>
        
        <?php
	}
}

/**
 * Print the content of badge in frontend [Free Version]
 *
 * @return   string
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */

if ( ! function_exists( 'yith_wcbm_get_badge' ) ){
    function yith_wcbm_get_badge($id_badge, $product_id) {

        if ( $id_badge == '' || $product_id == ''){
            return '';
        }

        $badge_container = '';

        $bm_meta = get_post_meta( $id_badge, '_badge_meta', true);
        $default = array(
            'type'                          => 'text',
            'text'                          => '', 
            'txt_color_default'             => '#000000', 
            'txt_color'                     => '#000000', 
            'bg_color_default'              => '#2470FF', 
            'bg_color'                      => '#2470FF', 
            'width'                         => '100', 
            'height'                        => '50',
            'position'                      => 'top-left',
            'image_url'                     => '',
            'product_id'                    => $product_id,
            'id_badge'                      => $id_badge
        );
        
        $args = wp_parse_args( $bm_meta , $default );
        $args = apply_filters('yith_wcbm_badge_content_args' , $args);

        ob_start();
        yith_wcbm_get_template('badge_content.php', $args);
        $badge_container .= ob_get_clean();

        return $badge_container;
        
    }
}

if ( ! function_exists( 'yith_wcbm_get_template' ) ) {
    function yith_wcbm_get_template( $template , $args ){
        extract( $args );
        include(YITH_WCBM_TEMPLATE_PATH . '/' . $template);
    }
}


if ( ! function_exists( 'yith_wcbm_wpml_register_string' ) ) {
    /**
     * Register a string in wpml trnslation
     *
     * @param string
     * @param string
     * @param string
     *
     * @since  2.0.0
     * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
     */
    function yith_wcbm_wpml_register_string( $context , $name , $value  ) {
        // wpml string translation
        do_action( 'wpml_register_single_string', $context, $name, $value );
    }
}

if ( ! function_exists( 'yith_wcbm_wpml_string_translate' ) ) {
    /**
     * Get a string translation
     *
     * @param string
     * @param string
     * @param string
     *
     * @return string the string translated
     * @since  2.0.0
     * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
     */
    function yith_wcbm_wpml_string_translate( $context, $name, $default_value ) {
        return apply_filters( 'wpml_translate_single_string', $default_value, $context, $name );
    }
}



?>