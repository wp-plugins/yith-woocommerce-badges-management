<?php
/**
 * Functions
 *
 * @author Yithemes
 * @package YITH WooCommerce Badges Management
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
		<div class="half-left">
            <div class="yith-wcbm-container-button">
                <div id="yith-wcbm-custom-button">Custom</div>
                <div id="yith-wcbm-image-button">Image</div>
            </div>
            <input class="update-preview" type="hidden" value="<?php echo $type ?>" data-type="<?php echo $type ?>" name= "_badge_meta[type]" id="yith-wcbm-badge-type">
            <input class="update-preview" type="hidden" value="<?php echo $image_url ?>" name= "_badge_meta[image_url]" id="yith-wcbm-image-url">
            
            <div id="yith-wcbm-panel-custom">
                <p>
                    <label><?php echo __('Text', 'yith-wcbm') ?></label><br />
                    <input class="update-preview" type="text" value="<?php echo $text ?>" name= "_badge_meta[text]" id="yith-wcbm-text" >
                </p>
                <p>
                    <label><?php echo __('Text Color', 'yith-wcbm') ?></label><br />
                    <input type="text" class="yith-wcbm-color-picker" name= "_badge_meta[txt_color]" value="<?php echo $txt_color ?>"
                    data-default-color="<?php echo $txt_color_default; ?>" id="yith-wcbm-txt-color">
                </p>
                <p>
                    <label><?php echo __('Background Color', 'yith-wcbm') ?></label><br />
                    <input type="text" class="yith-wcbm-color-picker" name= "_badge_meta[bg_color]" value="<?php echo $bg_color ?>"
                    data-default-color="<?php echo $bg_color_default; ?>" id="yith-wcbm-bg-color">
                </p>
                <p>
                    <label><?php echo __('Size [Width x Height] (pixel)', 'yith-wcbm') ?></label><br />
                    <input class="update-preview" type="text" value="<?php echo $width ?>" name= "_badge_meta[width]" id="yith-wcbm-width"> x 
                    <input class="update-preview" type="text" value="<?php echo $height ?>" name= "_badge_meta[height]" id="yith-wcbm-height">
                </p>
                <?php echo apply_filters('yith_wcbm_metabox_options_custom_premium', ''); ?>
            </div><!-- yith_wcbm_panel_custom -->
            <div id="yith-wcbm-panel-image">
                <?php
                for( $i = 1; $i<5; $i++ ){
                    $img_url = YITH_WCBM_ASSETS_URL . '/images/' . $i . '.png';
                    echo '<div class="yith-wcbm-select-image-btn" badge_image_url="'. $img_url .'" style="background-image:url('. $img_url .')">';
                    echo '</div>';
                }
                echo apply_filters('yith_wcbm_metabox_images_premium', '');
                ?>
            </div><!-- yith-wcbm-panel-image -->

            <?php
            $position_select    = '<p><label>' . __('Position', 'yith-wcbm') . '</label><br />';
            $position_select   .= '<select class="update-preview" name= "_badge_meta[position]" id="yith-wcbm-position">';
            $position_select   .= '<option value="top-left" ' . selected($position, 'top-left',false) . '>' . __('top-left','yith-wcbm') . '</option>';
            $position_select   .= '<option value="top-right" ' . selected($position, 'top-right',false) . '>' . __('top-right','yith-wcbm') . '</option>';
            $position_select   .= '<option value="bottom-left" ' . selected($position, 'bottom-left',false) . '>' . __('bottom-left','yith-wcbm') . '</option>';
            $position_select   .= '<option value="bottom-right" ' . selected($position, 'bottom-right',false) . '>' . __('bottom-right','yith-wcbm') . '</option>';
            $position_select   .= '</select>';
            $position_select   .= '</p>';
            echo apply_filters('yith_wcbm_metabox_options_common', $position_select); ?>

        </div>
     <?php
     $position_css = "";
     if ($position == 'top-left'){
            $position_css = "top: 0; left: 0;";
        }else if ($position == 'top-right'){
            $position_css = "top: 0; right: 0;";
        }else if ($position == 'bottom-left'){
            $position_css = "bottom: 0; left: 0;";
        }else if ($position == 'bottom-right'){
            $position_css = "bottom: 0; right: 0;";
        }
     ?>
     
     <div class="half-right">
            <h3 id="preview-title"> <?php echo __('Preview', 'yith-wcbm') ?> </h3>
            <div id="preview-bg">
                <div id="preview-badge">
                    <?php echo $text ?>
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
            'type'                          => 'custom',
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



?>