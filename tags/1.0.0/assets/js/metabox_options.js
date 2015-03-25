jQuery(function($){

    var type = $( '#yith-wcbm-badge-type').data( 'type' ),
        preview_render = function() {
        var preview_badge = $('#preview-badge');
        if ( type == 'custom' ){
           preview_badge.html($("#yith-wcbm-text").val());
           preview_badge.css({
                "color":                        $("#yith-wcbm-txt-color").val(),
                "background-color":             $("#yith-wcbm-bg-color").val(),
                "width":                        $("#yith-wcbm-width").val() + "px",
                "height":                       $("#yith-wcbm-height").val() + "px",
                "line-height":                  $("#yith-wcbm-height").val() + "px"
           });

        }else{
           preview_badge.removeAttr("style");
           var image_badge = $("#yith-wcbm-image-url").val();
           preview_badge.html('<img src="' + image_badge + '" />');
        }
   
        var position = $("#yith-wcbm-position").val();
        switch(position){
            case 'top-right':
                preview_badge.css({'top': '0', 'bottom': 'auto', 'left': 'auto', 'right': '0'});
                break;
            case 'bottom-left':
                preview_badge.css({'top': 'auto', 'bottom': '0', 'left': '0', 'right': 'auto'});
                break;
            case 'bottom-right':
                preview_badge.css({'top': 'auto', 'bottom': '0', 'left': 'auto', 'right': '0'});
                break;
            default:
                preview_badge.css({'top': '0', 'bottom': 'auto', 'left': '0', 'right': 'auto'});
        }
    }

    preview_render();
    $("input.update-preview").on("change paste keyup input focus", function() {
        preview_render();
    });
    $("select.update-preview").on("change focus", function() {
        preview_render();
    });
    $('.yith-wcbm-color-picker').wpColorPicker({
        change: preview_render
    });

    /*** Button Control ***/
    var custom_btn = $('#yith-wcbm-custom-button'),
        image_btn = $('#yith-wcbm-image-button'),
        selected_class = 'yith-wcbm-button-selected',
        panel_custom = $('#yith-wcbm-panel-custom'),
        panel_image = $('#yith-wcbm-panel-image'),
        input_type = $("#yith-wcbm-badge-type"),
        input_image_url = $("#yith-wcbm-image-url"),
        button_select_image = $(".yith-wcbm-select-image-btn"),
        button_reset = function(){
            custom_btn.removeClass( selected_class );
            image_btn.removeClass( selected_class );
        };

    if(type == 'custom'){
        custom_btn.addClass( selected_class );
        panel_image.hide();
    }else{
        image_btn.addClass( selected_class );
        panel_custom.hide();
    }

    custom_btn.on( 'click', function(){
        button_reset;
        custom_btn.addClass( selected_class );
        panel_image.fadeOut(0);
        panel_custom.fadeIn();
        input_type.val('custom');
        type = 'custom';
        preview_render();
    });

    image_btn.on( 'click', function(){
        button_reset;
        image_btn.addClass( selected_class );
        panel_custom.fadeOut(0);
        panel_image.fadeIn();
        input_type.val('image');
        type = 'image';
        preview_render();
    });

    button_select_image.on( 'click', function(e){
        var badge_image_url = $(this).attr('badge_image_url');
        input_image_url.val(badge_image_url);
        preview_render();
        button_select_image.removeClass("yith-wcbm-select-image-btn-selected");
        $(this).addClass("yith-wcbm-select-image-btn-selected");
    } );

    //add selected css class to the selected image button
    button_select_image.each(function(){
        if ($(this).attr('badge_image_url') == input_image_url.val()){
            $(this).addClass("yith-wcbm-select-image-btn-selected");
        }
    });

});