<?php
namespace The7CLI\Commands\The7;

use \WP_CLI;

defined( 'ABSPATH' ) || exit;

/**
 * Manage The7 theme options.
 *
 * @package The7CLI\Commands\The7
 */
class Option {

	const COMMAND = 'the7 option';

	/**
	 * Update The7 theme option.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : Option to update.
	 *
	 * [<value>]
	 * : The new value. If omitted, the value is read from STDIN.
	 *
	 * [--format=<format>]
	 * : The serialization format for the value.
	 * ---
	 * default: plaintext
	 * options:
	 *   - plaintext
	 *   - json
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 option update header-layout classic
	 *
	 * @when after_admin_init
	 */
	public function update( $args, $assoc_args ) {
		if ( ! function_exists( 'optionsframework_get_options_id' ) ) {
			WP_CLI::error( "Seems that you don't use The7 theme." );
		}

		$key   = $args[0];
		$value = WP_CLI::get_value_from_arg_or_stdin( $args, 1 );
		$value = WP_CLI::read_value( $value, $assoc_args );

		$the7_options_id = optionsframework_get_options_id();
		$theme_options   = get_option( $the7_options_id );
		$old_value       = $theme_options[ $key ];

		if ( $value === $old_value ) {
			WP_CLI::success( "Value passed for '$key' option is unchanged." );
		} else {
			$theme_options[ $key ] = $value;
			$theme_options         = sanitize_option( $the7_options_id, $theme_options );
			if ( update_option( $the7_options_id, $theme_options ) ) {
				presscore_refresh_dynamic_css();
				WP_CLI::success( "Updated '$key' option." );
			} else {
				WP_CLI::error( "Could not update option '$key'." );
			}
		}
	}

	/**
	 * Get The7 theme option.
	 * ## OPTIONS
	 *
	 * <key>
	 * : Option to get.
	 *
	 * [--format=<format>]
	 * : Get value in a particular format.
	 * ---
	 * default: var_export
	 * options:
	 *   - var_export
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 option get header-layout
	 *
	 * @when after_admin_init
	 */
	public function get( $args, $assoc_args ) {
		if ( ! function_exists( 'optionsframework_get_options_id' ) ) {
			WP_CLI::error( "Seems that you don't use The7 theme." );
		}

		$the7_options_id = optionsframework_get_options_id();
		$theme_options   = get_option( $the7_options_id );
		list( $key ) = $args;
		if ( ! array_key_exists( $key, $theme_options ) ) {
			return;
		}
		$value = $theme_options[ $key ];
		WP_CLI::print_value( $value, $assoc_args );
	}

	/**
	 * Delete The7 theme option.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : Option to delete.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 option delete header-layout
	 *
	 * @when after_admin_init
	 */
	public function delete( $args ) {
		if ( ! function_exists( 'optionsframework_get_options_id' ) ) {
			WP_CLI::error( "Seems that you don't use The7 theme." );
		}

		$key = $args[0];

		$the7_options_id = optionsframework_get_options_id();
		$theme_options   = get_option( $the7_options_id );
		unset( $theme_options[ $key ] );
		$theme_options = sanitize_option( $the7_options_id, $theme_options );
		if ( update_option( $the7_options_id, $theme_options ) ) {
			WP_CLI::success( "Option '$key' has been deleted." );
		} else {
			WP_CLI::error( "Could not delete option '$key'." );
		}
	}
}
