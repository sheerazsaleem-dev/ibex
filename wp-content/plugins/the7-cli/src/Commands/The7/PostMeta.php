<?php
namespace The7CLI\Commands\The7;

use \WP_CLI;

defined( 'ABSPATH' ) || exit;

class PostMeta {

	const COMMAND = 'the7 post-meta';

	/**
	 * Delete unused post meta.
	 *
	 * ## EXAMPLES
	 *
	 *  wp the7 post-meta cleanup
	 *
	 */
	public function cleanup() {
		global $wpdb;

		$posts = $wpdb->get_results( "SELECT ID as id, post_type FROM $wpdb->posts" );
		if ( ! $posts ) {
			WP_CLI::success( 'There is nothing to do - no posts found.' );
		}

		include_once PRESSCORE_ADMIN_DIR . '/load-meta-boxes.php';

		do_action( 'admin_init' );

		$posts = wp_list_pluck( $posts, 'post_type', 'id' );

		$total_meta_data_removed = 0;
		foreach ( $posts as $post_id => $post_type ) {
			$meta_removed_per_post = $this->delete_unused_post_meta( $post_id, $post_type );
			$total_meta_data_removed += $meta_removed_per_post;
		}

		WP_CLI::success( 'All done. Processed ' . count( $posts ) . ' posts, ' . $total_meta_data_removed . ' meta removed.' );
	}

	protected function delete_unused_post_meta( $post_id, $post_type ) {
		global $DT_META_BOXES;

		$meta_data         = get_metadata( 'post', $post_id );
		$registered_fields = array();

		$affected_fields_count = 0;
		foreach ( $DT_META_BOXES as $meta_box ) {
			foreach ( $meta_box['fields'] as $field ) {
				$registered_fields[] = $field['id'];
			}

			if ( ! in_array( $post_type, $meta_box['pages'], true ) ) {
				continue;
			}

			$fields = $meta_box['fields'];

			if ( isset( $meta_box['only_on']['template'] ) ) {
				$post_template = get_post_meta( $post_id, '_wp_page_template', true );
				if ( ! in_array( $post_template, (array) $meta_box['only_on']['template'], true ) ) {
					foreach ( $fields as $field ) {
						if ( array_key_exists( $field['id'], $meta_data ) ) {
							delete_post_meta( $post_id, $field['id'] );
							$affected_fields_count++;
						}
					}

					continue;
				}
			}

			if ( isset( $meta_box['only_on']['meta_value'] ) ) {
				foreach ( (array) $meta_box['only_on']['meta_value'] as $meta_key => $meta_value ) {
					if ( get_post_meta( $post_id, $meta_key, true ) !== $meta_value ) {
						foreach ( $fields as $field ) {
							if ( array_key_exists( $field['id'], $meta_data ) ) {
								delete_post_meta( $post_id, $field['id'] );
								$affected_fields_count++;
							}
						}

						continue;
					}
				}
			}
		}

		$meta_data_to_delete = array_diff( array_keys( $meta_data ), $registered_fields );
		foreach ( $meta_data_to_delete as $meta_key_to_delete ) {
			if ( strpos( $meta_key_to_delete, '_dt_' ) === 0 ) {
				delete_post_meta( $post_id, $meta_key_to_delete );
				$affected_fields_count++;
			}
		}

		return $affected_fields_count;
	}
}
