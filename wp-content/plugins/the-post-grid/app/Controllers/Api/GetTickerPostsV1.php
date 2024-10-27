<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;
use WP_Query;

class GetTickerPostsV1 {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_post_route' ] );
	}

	public function register_post_route() {
		register_rest_route(
			'rttpg/v1',
			'tickerquery',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_all_posts' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	public function get_all_posts( $data ) {
		$_post_type = ! empty( $data['post_type'] ) ? esc_html( $data['post_type'] ) : 'post';
		$post_type = Fns::available_post_type( $_post_type );
		$args = [
			'post_type'   => $post_type,
			'post_status' => 'publish',
		];

		$excluded_ids = null;
		if ( $data['post_id'] ) {
			$post_ids = explode( ',', $data['post_id'] );
			$post_ids = array_map( 'trim', $post_ids );

			$args['post__in'] = $post_ids;

			if ( $excluded_ids != null && is_array( $excluded_ids ) ) {
				$args['post__in'] = array_diff( $post_ids, $excluded_ids );
			}
		}

		if ( 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}

		if ( $orderby = $data['orderby'] ) {
			$args['orderby'] = $orderby;
		}

		if ( $data['order'] ) {
			$args['order'] = $data['order'];
		}

		if ( $data['instant_query'] ) {
			$args = Fns::get_instant_query( $data['instant_query'], $args );
		}

		if ( $data['author'] ) {
			$args['author__in'] = $data['author'];
		}

		if ( $data['start_date'] || $data['end_date'] ) {
			$args['date_query'] = [
				[
					'after'     => trim( $data['start_date'] ),
					'before'    => trim( $data['end_date'] ),
					'inclusive' => true,
				],
			];
		}

		// TODO: Taxonomy should implement after
		$_taxonomies             = get_object_taxonomies( $data['post_type'], 'objects' );
		$_taxonomy_list          = $data['taxonomy_lists'];
		$filtered_taxonomy_lists = [];

		foreach ( $_taxonomies as $index => $object ) {
			if ( in_array( $object->name, Fns::get_excluded_taxonomy() ) ) {
				continue;
			}

			$filtered_taxonomy_lists[ $object->name ] = isset( $_taxonomy_list[ $object->name ] ) ? $_taxonomy_list[ $object->name ]['options'] : null;
			$_term_list                               = isset( $_taxonomy_list[ $object->name ] ) ? wp_list_pluck( $_taxonomy_list[ $object->name ]['options'], 'value' ) : null;
			if ( ! empty( $_term_list ) ) {
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				$args['tax_query'][] = [
					'taxonomy' => $object->name,
					'field'    => 'term_id',
					'terms'    => $_term_list,
				];
			}
			if ( ! empty( $args['tax_query'] ) && $data['relation'] ) {
				$args['tax_query']['relation'] = $data['relation']; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			}
		}

		if ( $data['post_keyword'] ) {
			$args['s'] = $data['post_keyword'];
		}

		$excluded_ids = [];
		$offset_posts = [];
		if ( $data['exclude'] || $data['offset'] ) {
			if ( $data['exclude'] ) {
				$excluded_ids = explode( ',', $data['exclude'] );
				$excluded_ids = array_map( 'trim', $excluded_ids );
			}

			if ( $data['offset'] ) {
				$_temp_args = $args;
				unset( $_temp_args['paged'] );
				$_temp_args['posts_per_page'] = $data['offset'];
				$_temp_args['fields']         = 'ids';

				$offset_posts = get_posts( $_temp_args );
			}

			$excluded_post_ids    = array_merge( $offset_posts, $excluded_ids );
			$args['post__not_in'] = array_unique( $excluded_post_ids ); //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		}

		$query = new WP_Query( $args );

		$send_data = [
			'posts'      => [],
			'total_post' => $query->found_posts,
			'query_args' => $args,
		];

		// $send_data['total_post'] = esc_html( $query->found_posts );
		if ( $query->have_posts() ) {
			$pCount = 1;
			while ( $query->have_posts() ) {
				$query->the_post();
				$id         = get_the_id();
				$post_count = $query->post_count;
				set_query_var( 'tpg_post_count', $pCount );
				set_query_var( 'tpg_total_posts', $post_count );
				$category_terms_list = get_the_terms( $id, $data['category_source'] ? $data['category_source'] : 'category' );
				$tags_terms_list     = get_the_terms( $id, $data['tag_source'] ? $data['tag_source'] : 'post_tag' );
				$category_terms = wp_list_pluck( $category_terms_list, 'name' );
				$tags_terms     = wp_list_pluck( $tags_terms_list, 'name' );

				$excerpt_args = [
					'excerpt_type'      => $data['excerpt_type'],
					'excerpt_limit'     => $data['excerpt_limit'],
					'excerpt_more_text' => $data['excerpt_more_text'],
				];

				$exerpt = Fns::get_the_excerpt( $id, $excerpt_args );
				$author_id = get_the_author_meta( 'ID' );
				$count_key      = Fns::get_post_view_count_meta_key();
				$get_view_count = get_post_meta( $id, $count_key, true );

				$send_data['posts'][] = [
					'author_name'     => esc_html( get_the_author_meta( 'display_name', $author_id ) ),
					'comment_count'   => esc_html( get_comments_number( $id ) ),
					'category'        => ! empty( $category_terms ) ? $category_terms : [],
					'tags'            => ! empty( $tags_terms ) ? $tags_terms : '',
					'excerpt'         => $exerpt,
					'id'              => $id,
					'thumb_url'       => get_the_post_thumbnail_url( $id, 'thumbnail' ),
					'post_date'       => esc_html( get_the_date() ),
					'post_link'       => get_the_permalink(),
					'post_type'       => $data['post_type'],
					'post_count'      => esc_html( $get_view_count ),
					'title'           => Fns::get_the_title( $id, $data ), // wp_kses( $post->post_title, Fns::allowedHtml() ),
					'tpg_post_count'  => $pCount,
					'tpg_total_posts' => $post_count,
				];

				$pCount ++;
			}
		} else {
			$send_data['message'] = $data['no_posts_found_text'] ?? __( 'No posts found', 'the-post-grid' );
			$send_data['args']    = $args;
		}

		wp_reset_postdata();

		return rest_ensure_response( $send_data );
	}
}