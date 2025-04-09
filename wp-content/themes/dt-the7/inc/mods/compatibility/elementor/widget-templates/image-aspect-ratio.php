<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Image_Aspect_Ratio.
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Image_Aspect_Ratio extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_style_controls() {
		$this->widget->add_basic_responsive_control(
			'item_ratio',
			[
				'label'       => esc_html__( 'Image Ratio', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to use original proportions', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => '',
				],
				'range'       => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'selectors'   => [
					'{{WRAPPER}}' => '--aspect-ratio: {{SIZE}};',
				],
			]
		);
		$object_fit_options            = [
			'fill'    => esc_html__( 'Fill', 'the7mk2' ),
			'cover'   => esc_html__( 'Cover', 'the7mk2' ),
			'contain' => esc_html__( 'Contain', 'the7mk2' ),
		];
		$object_fit_options_on_devices = [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $object_fit_options;
		$this->widget->add_basic_responsive_control(
			'object_fit',
			[
				'label'                => esc_html__( 'Object Fit', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'condition'            => [
					'item_ratio[size]!' => '',
				],
				'options'              => $object_fit_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $object_fit_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $object_fit_options_on_devices,
					],
				],
				'selectors_dictionary' => [
					'fill'    => $this->widget->combine_to_css_vars_definition_string(
						[
							'position'         => 'static',
							'object-fit'       => 'fill',
							'ratio-img-width'  => 'initial',
							'svg-width'        => '100%',
							'height'           => 'auto',
							'max-height'       => 'unset',
							'max-width'        => '100%',
							'box-width'        => 'var(--image-size, auto)',
						]
					),
					'cover'   => $this->widget->combine_to_css_vars_definition_string(
						[
							'position'         => 'absolute',
							'object-fit'       => 'cover',
							'ratio-img-width'  => '100%',
							'svg-width'        => '100%',
							'height'           => '100%',
							'max-height'       => '100%',
							'max-width'        => '100%',
							'box-width'        => 'var(--image-size, var(--ratio-img-width))',
						]
					),
					'contain' => $this->widget->combine_to_css_vars_definition_string(
						[
							'position'         => 'static',
							'object-fit'       => 'contain',
							'ratio-img-width'  => 'auto',
							'svg-width'        => '100%',
							'height'           => 'auto',
							'max-height'       => '100%',
							'max-width'        => '100%',
							'box-width'        => 'var(--image-size, auto)',
						]
					),
				],
				'default'              => 'cover',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'prefix_class'         => 'preserve-img-ratio-',
			]
		);
	}

	/**
	 * @return string
	 */
	public function get_wrapper_class() {
		return 'img-css-resize-wrapper';
	}

}
