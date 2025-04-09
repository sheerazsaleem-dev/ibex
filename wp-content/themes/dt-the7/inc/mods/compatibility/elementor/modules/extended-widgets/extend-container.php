<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Extend_Container {

	public function __construct() {
		// inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	/**
	 * Before container end.
	 * Fires before Elementor container ends in the editor panel.
	 *
	 * @param Controls_Stack $widget     The control.
	 * @param string         $section_id Section ID.
	 * @param array          $args       Section arguments.
	 *
	 * @since 1.4.0
	 */
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'container' => [
				'section_name' => [ 'section_layout_container' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		$widget->update_control(
			'content_width',
			[
				'options' => [
					'boxed' => esc_html__( 'Boxed', 'the7mk2' ),
					'full' => esc_html__( 'Full Width', 'the7mk2' ),
					'fit' => esc_html__( 'Fit Content', 'the7mk2' ),
				],
			]
		);

		$widget->update_responsive_control(
			'width',
			[
				'description' => __( 'Select  <i class="eicon-edit"></i>  and enter "fit-content" to make container width fit its content', 'the7mk2' ),
			]
		);

		$widget->start_injection( [
			'of' => 'content_width',
			'at' => 'after',
		] );

		$widget->add_control( 'the7_width_fit', [
			'label'        => esc_html__( 'Fit Content', 'the7mk2' ),
			'type'         => Controls_Manager::HIDDEN,
			'selectors' => [
				'{{WRAPPER}}' => '--width: fit-content;',
			],
			'condition' => [
				'content_width' => 'fit',
			],
			'default'      => 'y',
			'return_value' => 'y',
		] );

		$widget->end_injection();
	}
}
