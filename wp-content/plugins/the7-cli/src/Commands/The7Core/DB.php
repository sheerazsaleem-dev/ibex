<?php
namespace The7CLI\Commands\The7Core;

use \The7PT_Install;
use \WP_CLI;

defined( 'ABSPATH' ) || exit;

class DB {

	const COMMAND = 'the7-core db';
	const DB_UPDATING = 1;
	const DB_UPDATE_NEEDED = 2;
	const DB_UP_TO_DATE = 3;

	/**
	 * ## EXAMPLES
	 *
	 *  wp the7-core db update
	 *
	 * @when after_admin_init
	 */
	public function update() {
		$this->check_dependencies();

		if ( self::DB_UPDATE_NEEDED === $this->status() ) {
			The7PT_Install::update();
			WP_CLI::success( 'Database update has begun.' );
		}
	}

	/**
	 * ## EXAMPLES
	 *
	 *  wp the7-core db status
	 *
	 * @when after_admin_init
	 */
	public function status() {
		$this->check_dependencies();

		if ( The7PT_Install::db_is_updating() ) {
			WP_CLI::log( 'DB is updating...' );

			return self::DB_UPDATING;
		}

		if ( The7PT_Install::db_update_is_needed() ) {
			WP_CLI::log( 'DB update is needed.' );

			return self::DB_UPDATE_NEEDED;
		}

		WP_CLI::log( 'DB is up to date.' );

		return self::DB_UP_TO_DATE;
	}

	/**
	 * ## EXAMPLES
	 *
	 *  wp the7-core db cancel_update
	 *
	 * @when after_admin_init
	 */
	public function cancel_update() {
		if ( ! class_exists( 'The7_Background_Updater' ) ) {
			include_once PRESSCORE_MODS_DIR . '/theme-update/class-the7-background-updater.php';
		}

		$background_updater = new The7_Background_Updater();
		$background_updater->cancel_process();

		WP_CLI::success( 'Update is canceled.' );
	}

	/**
	 * ## EXAMPLES
	 *
	 *  wp the7-core db version
	 *
	 * @when after_admin_init
	 */
	public function version() {
		$this->check_dependencies();

		WP_CLI::log( 'Current db version: ' . The7PT_Install::get_db_version() );
	}

	/**
	 * Check commands dependencies. Error exit if dependencies are not met.
	 */
	protected function check_dependencies() {
		if ( ! class_exists( 'The7PT_Install' ) ) {
			WP_CLI::error( 'The7PT_Install is not exists.', true );
		}
	}
}
