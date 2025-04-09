<?php
namespace The7CLI\Commands\The7;

use \WP_CLI;

defined( 'ABSPATH' ) || exit;

/**
 * Deal with The7 auto resized images.
 *
 * @package The7CLI\Commands\The7
 */
class Images {

	const COMMAND = 'the7 images';

	/**
	 * Delete potentially auto resized images.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 images delete_resized
	 *
	 * @when after_admin_init
	 */
	public function delete_resized() {
		/**
		 * @param WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$wp_upload = wp_get_upload_dir();
		if ( ! $wp_filesystem && ! WP_Filesystem( false, $wp_upload['basedir'] ) ) {
			return WP_CLI::error( 'Cannot access file system.' );
		}

		$removed_files_count = 0;
		foreach ( $this->get_resized_images_files() as $file_to_delete ) {
			$wp_filesystem->delete( $wp_upload['basedir'] . '/' . $file_to_delete, false, 'f' ) && $removed_files_count++;
		}

		WP_CLI::success( "Deleted $removed_files_count files." );
	}

	/**
	 * Get the list of potentially auto resized images.
	 *
	 * ## OPTIONS
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
	 *  wp the7 images list_resized
	 *
	 * @when after_admin_init
	 */
	public function list_resized( $args, $assoc_args ) {
		WP_CLI::print_value( $this->get_resized_images_files(), $assoc_args );
	}

	/**
	 * @return array
	 */
	protected function get_resized_images_files() {
		/**
		 * @param WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$wp_upload = wp_get_upload_dir();
		if ( ! $wp_filesystem && ! WP_Filesystem( false, $wp_upload['basedir'] ) ) {
			return WP_CLI::error( 'Cannot access file system.' );
		}

		$file_names = array();
		$base_dirs  = $wp_filesystem->dirlist( $wp_upload['basedir'], false, false );
		foreach ( $base_dirs as $base_dir ) {
			if ( ! (int) $base_dir['name'] ) {
				continue;
			}

			$dirs = $wp_filesystem->dirlist( $wp_upload['basedir'] . '/' . $base_dir['name'], false, true );
			foreach ( $dirs as $dir ) {
				$file_names = array_merge( $file_names, $this->get_files_in_dir( $dir, $base_dir['name'] ) );
			}
		}

		$query = new \WP_Query( [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'paginate'       => false,
			'post_status'    => 'published',
		] );

		$files = array();
		foreach ( $query->posts as $attachment ) {
			if ( ! wp_attachment_is_image( $attachment ) ) {
				continue;
			}
			$attachment_meta = wp_get_attachment_metadata( $attachment->ID );
			if ( ! $attachment_meta ) {
				echo "Attachment mata is empty\n";
				var_dump( $attachment );
				continue;
			}

			$base_dir = dirname( $attachment_meta['file'] );
			$images   = wp_list_pluck( $attachment_meta['sizes'], 'file' );
			$files[]  = $attachment_meta['file'];
			foreach ( $images as $i => $image ) {
				$files[] = $base_dir . '/' . $image;
			}
		}

		return array_values( array_diff( $file_names, $files ) );
	}

	/**
	 * @param array  $dir
	 * @param string $base
	 *
	 * @return array
	 */
	protected function get_files_in_dir( $dir, $base = '' ) {
		$files = array();

		if ( empty( $dir['files'] ) ) {
			return $files;
		}

		$basedir    = $base . '/' . $dir['name'];
		$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
		foreach ( $dir['files'] as $file ) {
			if ( $file['type'] === 'd' ) {
				$files = array_merge( $files, $this->get_files_in_dir( $file, $basedir ) );
				continue;
			}

			$filetype = wp_check_filetype( $file['name'] );
			if ( ! in_array( $filetype['ext'], $image_exts ) ) {
				continue;
			}

			$files[] = $basedir . '/' . $file['name'];
		}

		return $files;
	}

}
