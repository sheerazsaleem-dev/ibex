<?php

namespace The7CLI\Commands\The7;

use \WP_CLI;

defined( 'ABSPATH' ) || exit;

/**
 * Manage The7 cache.
 *
 * @package The7CLI\Commands\The7
 */
class Cache {

	const COMMAND = 'the7 cache';

	/**
	 * Flush The7 inner cache, dynamic css.
	 *
	 * ## EXAMPLES
	 *  wp the7 cache flush
	 *
	 * @when after_admin_init
	 */
	public function flush() {
		WP_CLI::log( 'Flush options cache ...' );
		if ( function_exists( '_optionsframework_delete_defaults_cache' ) ) {
			_optionsframework_delete_defaults_cache();
		}
		WP_CLI::success( 'Done.' );

		WP_CLI::log( 'Flush css cache ...' );
		if ( function_exists( 'presscore_refresh_dynamic_css' ) ) {
			presscore_refresh_dynamic_css();
		}
		WP_CLI::success( 'Done.' );

		WP_CLI::log( 'Flush shortcodes cache ...' );

		if ( ! function_exists( 'the7_mass_regenerate_short_codes_inline_css' ) ) {
			require PRESSCORE_MODS_DIR . '/theme-update/the7-update-functions.php';
		}

		the7_mass_regenerate_short_codes_inline_css();

		WP_CLI::success( 'Done.' );

		WP_CLI::success( 'Cache was flushed.' );
	}

	/**
	 * Flush The7 dynamic css.
	 *
	 * ## EXAMPLES
	 *  wp the7 cache flush_css
	 *
	 * @when after_admin_init
	 */
	public function flush_css() {
		WP_CLI::log( 'Flush css cache ...' );
		if ( function_exists( 'presscore_refresh_dynamic_css' ) ) {
			presscore_refresh_dynamic_css();
		}
		WP_CLI::success( 'Cache was flushed.' );
	}
}