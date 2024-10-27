<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;
use WP_Query;

class GetBuilderData {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_post_route' ] );
	}

	public function register_post_route() {
		register_rest_route(
			'rttpg/v1',
			'builder',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_all_posts' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}


	/**
	 * Get all posts
	 *
	 * @param $data
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_all_posts( $data ) {

		$last_post_id = Fns::get_last_post_id();
		$post_args    = get_post( $last_post_id );

		if ( ! empty( $data['fetch'] ) ) {
			switch ( $data['fetch'] ) {
				case 'title':
					return rest_ensure_response( $post_args->post_title );
				case 'thumbnail':
					return rest_ensure_response( get_the_post_thumbnail( $post_args, $data['image_size'] ) );
				case 'content':
					return rest_ensure_response( $post_args->post_content );
				case 'meta':
					$post_meta = Fns::get_post_meta_html( $last_post_id, $data, false );

					return rest_ensure_response( $post_meta );
				case 'share':
					$share_html = Functions::rtShare( $last_post_id );

					return rest_ensure_response( $share_html );
				case 'comment':
					$comment_form = Functions::get_dummy_comment_box();

					return rest_ensure_response( $comment_form );
			}
		}

		$send_data = [
			'post_title' => $post_args->post_title,
		];

		return rest_ensure_response( $send_data );
	}
}
