<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

class ImageSizeV1 {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_image_size_route' ) );
	}

	public function register_image_size_route() {
		register_rest_route(
			'rttpg/v1',
			'image-size',
			[
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_image_sizes' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	public function get_image_sizes() {
		$data = Fns::get_all_image_sizes_guten();

		return rest_ensure_response( $data );
	}
}
