<?php

$settings = array(

	'settings'  => array(

		'general-options' => array(
			'title' => __( 'General Options', 'yith-wcbm' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wcbm-general-options'
		),

		'hide-on-sale-default-badge' => array(
			'id'        => 'yith-wcbm-hide-on-sale-default',
			'name'      => __( 'Hide on sale badge', 'yith-wcbm' ),
			'type'      => 'checkbox',
			'default'   => 'no'
		),

		'general-options-end' => array(
			'type'      => 'sectionend',
			'id'        => 'yith-wcqv-general-options'
		)
	)
);

return apply_filters( 'yith_wcbm_panel_settings_options', $settings );