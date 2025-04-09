<?php
/**
 * Plugin Name: The7 CLI
 * Description: This plugin contains wp-cli commands specific to The7 theme.
 * Version:     1.0.0
 * Author:      Dream-Theme
 * Author URI:  https://dream-theme.com/
 */

use The7CLI\Commands\The7\Cache;
use The7CLI\Commands\The7\DB;
use The7CLI\Commands\The7\Images;
use The7CLI\Commands\The7\Option;
use The7CLI\Commands\The7\PostMeta;
use The7CLI\Commands\The7Core\DB as CoreDB;

defined( 'ABSPATH' ) || exit;

if ( defined( 'WP_CLI' ) ) {
	require __DIR__ . '/vendor/autoload.php';

	WP_CLI::add_command( DB::COMMAND, DB::class );
	WP_CLI::add_command( Option::COMMAND, Option::class );
	WP_CLI::add_command( Cache::COMMAND, Cache::class );

	if ( defined( 'THE7_CLI_ENABLE_BETA_FEATURES' ) ) {
		WP_CLI::add_command( CoreDB::COMMAND, CoreDB::class );
		WP_CLI::add_command( PostMeta::COMMAND, PostMeta::class );
		WP_CLI::add_command( Images::COMMAND, Images::class );
	}
}
