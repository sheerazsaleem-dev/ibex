<?php
/**
 * WPML Elementor Widgets compatibility.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\WPML;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Elementor_Translate_Widgets.
 */
class Elementor_Translate_Widgets {

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		add_filter( 'the7_elementor_widget_init_settings', [ $this, 'translate_settings' ], 10, 2 );
	}

	/**
	 * Translate widget settings.
	 *
	 * @param array       $settings The widget settings.
	 * @param Widget_Base $widget The widget instance.
	 *
	 * @return array
	 */
	public function translate_settings( $settings, $widget ) {
		if ( ! is_array( $settings ) ) {
			return $settings;
		}

		$controls = $widget->get_controls();

		foreach ( $settings as $setting_name => &$setting_value ) {
			if ( ! isset( $controls[ $setting_name ] ) || strpos( $setting_name, '_' ) === 0 ) {
				continue;
			}

			$control = $controls[ $setting_name ];

			if ( $control['type'] === Controls_Manager::MEDIA ) {
				if ( ! empty( $setting_value['id'] ) ) {
					$setting_value['id'] = apply_filters( 'wpml_object_id', $setting_value['id'], 'attachment', true );
				}
				if ( ! empty( $setting_value['url'] ) ) {
					$image_id = attachment_url_to_postid( $setting_value['url'] );
					if ( $image_id ) {
						$image_id             = apply_filters( 'wpml_object_id', $image_id, 'attachment', true );
						$setting_value['url'] = wp_get_attachment_url( $image_id );
					}
				}
			}
		}
		unset( $setting_value );

		return $settings;
	}
}
