<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

class ACFV1 {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_acf_data_route' ] );
	}

	public function register_acf_data_route() {
		register_rest_route(
			'rttpg/v1',
			'acf',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_acf_data' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	public function get_acf_data() {

		$post_types = Fns::get_post_types();

		$acf_data = [];
		foreach ( $post_types as $post_type => $post_type_title ) {
			$get_acf_field   = Fns::get_groups_by_post_type( $post_type );
			$selected_acf_id = '';
			if ( ! empty( $get_acf_field ) && is_array( $get_acf_field ) ) {
				$selected_acf_id = array_key_first( $get_acf_field );
			}

			$options       = Fns::get_groups_by_post_type( $post_type );
			$options_field = [];
			foreach ( $options as $value => $label ) {
				$options_field[] = [
					'value' => $value,
					'label' => $label,
				];
			}
			if ( ! empty( $options ) ) {
				$acf_data[ $post_type . '_cf_group' ] = [
					'post_type' => $post_type,
					'options'   => $options_field,
					'default'   => $selected_acf_id,
				];
			}
		}

		return rest_ensure_response( $acf_data );
	}
}
