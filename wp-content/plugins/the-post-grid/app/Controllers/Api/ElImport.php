<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

class ElImport {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_image_size_route' ] );
	}

	public function register_image_size_route() {
		register_rest_route(
			'rttpg/v1',
			'elimport',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'el_import' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	public function el_import( $data ) {

		$content = $data['content'];

		$status = false;

		if ( isset( $data['content'] ) ) {
			$content = json_decode( $data['content'] );

			$is_update = update_post_meta( '5696', '_elementor_data', $content );

			if ( $is_update ) {
				$status = true;
			}
		}

		return rest_ensure_response(
			[
				'success' => true,
			]
		);
	}
}
