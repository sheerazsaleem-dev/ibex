<?php

namespace The7CLI\Commands\The7;

use \The7_Install;
use \The7PT_Install;
use \The7PT_Core;
use \WP_CLI;

defined( 'ABSPATH' ) || exit;

/**
 * Manage The7 db.
 *
 * @package The7CLI\Commands\The7
 */
class DB {

	const COMMAND = 'the7 db';
	const DB_UPDATING = 1;
	const DB_UPDATE_NEEDED = 2;
	const DB_UP_TO_DATE = 3;

	/**
	 * Start the7 db update.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 db update
	 *
	 * @when after_admin_init
	 */
	public function update() {
		$this->status();

		if ( self::DB_UPDATE_NEEDED === $this->get_the7_db_status() ) {
			The7_Install::update();
			the7_admin_notices()->reset( 'the7_updated' );
			WP_CLI::success( 'Database update has begun.' );
		}
	}

	/**
	 * Get db status.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 db status
	 *
	 * @when after_admin_init
	 */
	public function status() {
		$the7_db_status      = $this->get_the7_db_status();
		$the7_core_db_status = $this->get_the7_core_db_status();

		if ( $the7_db_status === self::DB_UPDATING ) {
			WP_CLI::log( 'DB is updating...' );
			return;
		}

		if ( in_array( self::DB_UPDATE_NEEDED, [ $the7_db_status, $the7_core_db_status ], true ) ) {
			WP_CLI::log( 'DB update is needed.' );
			return;
		}

		WP_CLI::success( 'DB is up to date.' );
	}

	/**
	 * Get update tasks.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 db tasks
	 *
	 * @when after_admin_init
	 */
	public function tasks() {
		$batches = [];
		foreach (The7_Install::get_updater()->get_batches() as $batch) {
			$batches[] = (array) $batch;
		}

		if ( !empty($batches) ) {
			\WP_CLI\Utils\format_items( 'table', $batches, [ 'key', 'data' ] );
			return;
		}

		WP_CLI::success('There is no background tasks yet.');
	}

	/**
	 * Cancel db update.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 db cancel_update
	 *
	 * @when after_admin_init
	 */
	public function cancel_update() {
		if ( ! class_exists( 'The7_Background_Updater' ) ) {
			include_once PRESSCORE_MODS_DIR . '/theme-update/class-the7-background-updater.php';
		}

		$background_updater = new \The7_Background_Updater();
		$background_updater->cancel_process();

		WP_CLI::success( 'Update is canceled.' );
	}

	/**
	 * Get db version.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 db version
	 *
	 * @when after_admin_init
	 */
	public function version() {
		$the7_version = The7_Install::get_db_version();
		$msg = 'The7 db version: ' . $the7_version;
		if ( version_compare( PRESSCORE_DB_VERSION, $the7_version, '>') ) {
			$msg .= ', can be updated up to ' . PRESSCORE_DB_VERSION;
		}
		WP_CLI::log( $msg );

		if ( $this->the7_core_installed() ) {
			$the7_core_version = The7PT_Install::get_db_version();
			$msg = 'The7 Elements db version: ' . $the7_core_version;
			if ( version_compare( The7PT_Core::PLUGIN_DB_VERSION, $the7_core_version, '>') ) {
				$msg .= ', can be updated up to ' . The7PT_Core::PLUGIN_DB_VERSION;
			}
			WP_CLI::log( $msg );
		}
	}

	protected function get_the7_db_status() {
		if ( The7_Install::db_is_updating() ) {
			return self::DB_UPDATING;
		}

		if ( The7_Install::db_update_is_needed() ) {
			return self::DB_UPDATE_NEEDED;
		}

		return self::DB_UP_TO_DATE;
	}

	protected function get_the7_core_db_status() {
		if ( ! $this->the7_core_installed() ) {
			return false;
		}

		if ( The7PT_Install::db_is_updating() ) {
			return self::DB_UPDATING;
		}

		if ( The7PT_Install::db_update_is_needed() ) {
			return self::DB_UPDATE_NEEDED;
		}

		return self::DB_UP_TO_DATE;
	}

	protected function the7_core_installed() {
		return class_exists( 'The7PT_Install' ) && class_exists( 'The7PT_Core' );
	}
}
