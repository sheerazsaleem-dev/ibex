<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bullets.
 */
class Bullets extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_content_controls($condition = null) {
		$this->widget->start_controls_section(
			'bullets_section',
			[
				'label' => esc_html__( 'Bullets', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);

		$layouts            = [
			'show' => esc_html__( 'Always', 'the7mk2' ),
			'hide' => esc_html__( 'Never', 'the7mk2' ),
			'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
		];
		$responsive_layouts = [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $layouts;

		$this->widget->add_basic_responsive_control(
			'show_bullets',
			[
				'label'                => esc_html__( 'Show Bullets', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'show',
				'options'              => $layouts,
				'device_args'          => [
					'tablet' => [
						'options' => $responsive_layouts,
					],
					'mobile' => [
						'options' => $responsive_layouts,
					],
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'show' => '--bullet-display: inline-flex; --bullet-opacity:1;',
					'hide' => '--bullet-display: none',
					'hover'  => '--bullet-display: inline-flex;--bullet-opacity:0;',
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_controls($condition = null) {

		$this->widget->start_controls_section(
			'bullets_style_section',
			[
				'label'      => esc_html__( 'Bullets', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'show_bullets',
									'operator' => '!=',
									'value'    => 'hide',
								],
								
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'show_bullets_tablet',
									'operator' => '!=',
									'value'    => 'hide',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'show_bullets_mobile',
									'operator' => '!=',
									'value'    => 'hide',
								],
							],
						],
					],
				],
				
				'condition' =>$condition
			]
		);

		$this->widget->add_control(
			'bullets_style_heading',
			[
				'label'     => esc_html__( 'Bullets Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'bullets_style',
			[
				'label'        => esc_html__( 'Choose Bullets Style', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'small-dot-stroke',
				'options'      => [
					'small-dot-stroke' => 'Small dot stroke',
					'scale-up'         => 'Scale up',
					'stroke'           => 'Stroke',
					'fill-in'          => 'Fill in',
					'ubax'             => 'Square',
					'etefu'            => 'Rectangular',
				],
				'prefix_class' => 'bullets-',
				'render_type'  => 'template',
			]
		);

		$selector = '{{WRAPPER}} .owl-dots';

		$this->widget->add_control(
			'bullet_size',
			[
				'label'      => esc_html__( 'Bullets Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'bullet_border_size',
			[
				'label'      => esc_html__( 'Border Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition'  => [
					'bullets_style!' => 'scale-up',
				],
				'selectors'  => [
					$selector => '--bullet-border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'bullet_gap',
			[
				'label'      => esc_html__( 'Gap Between Bullets', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->start_controls_tabs( 'bullet_style_tabs' );

		$this->widget->start_controls_tab(
			'bullet_colors',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_hover_colors',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_hover',
			[
				'label'     => esc_html__( 'Hover Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-hover-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_active_colors',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_active',
			[
				'label'     => esc_html__( 'Active Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-active-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'bullets_position_heading',
			[
				'label'     => esc_html__( 'Bullets Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'bullets_v_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'top: var(--bullet-v-offset, 10px); --bullet-translate-y:0;',
					'center' => 'top: calc(50% + var(--bullet-v-offset, 10px)); --bullet-translate-y:-50%;',
					'bottom' => 'top: calc(100% + var(--bullet-v-offset, 10px)); --bullet-translate-y:0;',
				],
				'toggle'               => false,
				'selectors'            => [
					$selector => '{{VALUE}};',
				],
				'default'              => 'bottom',
			]
		);

		$this->widget->add_control(
			'bullets_h_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'               => false,
				'default'              => 'center',
				'selectors_dictionary' => [
					'left'   => 'left: var(--bullet-h-offset); --bullet-translate-x:0;',
					'center' => 'left: calc(50% + var(--bullet-h-offset)); --bullet-translate-x:-50%;',
					'right'  => 'left: calc(100% - var(--bullet-h-offset)); --bullet-translate-x:-100%;',
				],
				'selectors'            => [
					$selector => '{{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			'bullets_v_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-v-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_control(
			'bullets_h_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-h-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->end_controls_section();
	}

}
