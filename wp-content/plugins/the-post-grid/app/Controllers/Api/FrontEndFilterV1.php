<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

class FrontEndFilterV1 {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_front_end_filter' ] );
	}

	public function register_front_end_filter() {
		register_rest_route(
			'rttpg/v1',
			'filter',
			array(
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_frontend_filter' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	public function get_frontend_filter( $data ) {
		$data        = Fns::get_frontend_filter_markup( $data, true );
		$filter_html = [
			'markup' =>	$data,
		];
		return rest_ensure_response( $filter_html );
	}
}
