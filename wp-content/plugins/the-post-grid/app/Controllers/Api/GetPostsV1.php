<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;
use WP_Query;

class GetPostsV1 {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_post_route' ] );
	}

	public function register_post_route() {
		register_rest_route(
			'rttpg/v1',
			'query',
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

		$prefix = isset( $data['prefix'] ) ? $data['prefix'] : 'grid';

		$_post_type  = ! empty( $data['post_type'] ) ? esc_html( $data['post_type'] ) : 'post';
		$_post_types = ! empty( $data['post_types'] ) ? Fns::escape_array( $data['post_types'] ) : [ 'post' ];

		if ( rtTPG()->hasPro() && 'yes' === $data['multiple_post_type'] ) {
			$post_type = Fns::available_post_types( $_post_types, true );
		} else {
			$post_type = Fns::available_post_type( $_post_type );
		}

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

		if ( $prefix !== 'slider' && 'show' === $data['show_pagination'] ) {
			$_paged        = is_front_page() ? 'page' : 'paged';
			$args['paged'] = get_query_var( $_paged ) ? absint( get_query_var( $_paged ) ) : absint( $data['page'] );
		}

		if ( rtTPG()->hasPro() && 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}

		if ( $orderby = $data['orderby'] ) {
			if ( ! rtTPG()->hasPro() && 'rand' == $orderby ) {
				$orderby = 'date';
			}
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

		if ( rtTPG()->hasPro() && ( $data['start_date'] || $data['end_date'] ) ) {
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

		if ( $prefix !== 'slider' ) {
			if ( $data['post_limit'] ) {
				if ( 'show' !== $data['show_pagination'] ) {
					$args['posts_per_page'] = $data['post_limit'];
				} else {
					$tempArgs                   = $args;
					$tempArgs['posts_per_page'] = $data['post_limit'];
					$tempArgs['paged']          = 1;
					$tempArgs['fields']         = 'ids';
					if ( ! empty( $offset_posts ) ) {
						$tempArgs['post__not_in'] = $offset_posts; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
					}
					$tempQ = new WP_Query( $tempArgs );
					if ( ! empty( $tempQ->posts ) ) {
						$args['post__in']       = $tempQ->posts;
						$args['posts_per_page'] = $data['post_limit'];
					}
				}
			} else {
				$_posts_per_page = 9;
				if ( 'grid' === $prefix ) {
					if ( $data['grid_layout'] == 'grid-layout5' ) {
						$_posts_per_page = 5;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout6', 'grid-layout6-2' ] ) ) {
						$_posts_per_page = 3;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout5', 'grid-layout5-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'list' === $prefix ) {
					if ( in_array( $data['list_layout'], [ 'list-layout2', 'list-layout2-2' ] ) ) {
						$_posts_per_page = 7;
					} elseif ( in_array( $data['list_layout'], [ 'list-layout3', 'list-layout3-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'grid_hover' === $prefix ) {
					if ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout4', 'grid_hover-layout4-2' ] ) ) {
						$_posts_per_page = 7;
					} elseif ( in_array(
						$data['grid_hover_layout'],
						[
							'grid_hover-layout5',
							'grid_hover-layout5-2',
						]
					) ) {
						$_posts_per_page = 3;
					} elseif ( in_array(
						$data['grid_hover_layout'],
						[
							'grid_hover-layout6',
							'grid_hover-layout6-2',
							'grid_hover-layout9',
							'grid_hover-layout9-2',
							'grid_hover-layout10',
							'grid_hover-layout11',
						]
					)
					) {
						$_posts_per_page = 4;
					} elseif ( in_array(
						$data['grid_hover_layout'],
						[
							'grid_hover-layout7',
							'grid_hover-layout7-2',
							'grid_hover-layout8',
						]
					) ) {
						$_posts_per_page = 5;
					} elseif ( in_array(
						$data['grid_hover_layout'],
						[
							'grid_hover-layout6',
							'grid_hover-layout6-2',
						]
					) ) {
						$_posts_per_page = 4;
					}
				}

				$args['posts_per_page'] = $_posts_per_page;
			}

			if ( isset( $data['display_per_page'] ) && $data['display_per_page'] > 0 ) {
				$args['posts_per_page'] = $data['display_per_page'];
			}
		} else {
			$slider_per_page = $data['post_limit'];
			if ( $data['slider_layout'] == 'slider-layout10' ) {
				$slider_reminder = ( intval( $data['post_limit'], 10 ) % 5 );
				if ( $slider_reminder ) {
					$slider_per_page = ( $data['post_limit'] - $slider_reminder + 5 );
				}
			}
			$args['posts_per_page'] = $slider_per_page;
		}

		$_all_taxs = wp_list_pluck( $_taxonomies, 'label', 'name' );

		$post_tax_list = array_filter(
			$_all_taxs,
			function ( $item ) {
				return ( ! in_array(
					$item,
					[
						'post_format',
						'elementor_library_type',
						'product_visibility',
						'product_shipping_class',
					],
					true
				) );
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( ! empty( $data['is_builder'] ) && $data['is_builder'] === 'yes' ) {
			$args['posts_per_page'] = get_option( 'posts_per_page' );
		}

		$query = new WP_Query( $args );

		$post_layout = $data[ $prefix . '_layout' ];

		$send_data = [
			'posts'      => [],
			'total_post' => $query->found_posts,
			'query_args' => $args,
			'query_info' => [
				'prefix'   => $prefix,
				'layout'   => $post_layout,
				'taxonomy' => $post_tax_list,
			],
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
				global $post;

				$cf_group            = isset( $data['acf_data_lists'][ $data['post_type'] . '_cf_group' ] ) ? $data['acf_data_lists'][ $data['post_type'] . '_cf_group' ]['options'] : [];
				$cf_group_collection = wp_list_pluck( $cf_group, 'value' );

				$acfArgs = [
					'is_guten'            => 'yes',
					'show_acf'            => $data['show_acf'],
					'cf_hide_empty_value' => $data['cf_hide_empty_value'],
					'cf_show_only_value'  => $data['cf_show_only_value'],
					'cf_hide_group_title' => $data['cf_hide_group_title'],
					'cf_group'            => $cf_group_collection,
				];

				$acf_data = Fns::tpg_get_acf_data_elementor( $acfArgs, $id, false );

				// First image from the post
				preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content(), $matches );
				if ( empty( $first_img ) ) { // Defines a default image
					$first_img = RT_THE_POST_GRID_PLUGIN_PATH . '/images/default.png';
				} else {
					$first_img = $matches [1] [0];
				}

				$category_terms_list = get_the_terms( $id, $data['category_source'] ? $data['category_source'] : 'category' );
				$tags_terms_list     = get_the_terms( $id, $data['tag_source'] ? $data['tag_source'] : 'post_tag' );

				$category_terms = wp_list_pluck( $category_terms_list, 'name' );
				$tags_terms     = wp_list_pluck( $tags_terms_list, 'name' );

				// TODO: Working Here 13-12-22

				$_cat_bg_meta = [];
				if ( $data['post_type'] == 'post' ) {
					$_category_list_term = wp_list_pluck( $category_terms_list, 'term_id' );
					foreach ( $_category_list_term as $item_id ) {
						$meta_color     = get_term_meta( $item_id, 'rttpg_category_color', true );
						$_cat_bg_meta[] = $meta_color ? "#{$meta_color}" : '';
					}
				}

				$img_url        = esc_url_raw( get_the_post_thumbnail_url( $id, $data['image_size'] ) );
				$img_offset_url = '';
				if ( isset( $data['image_offset_size'] ) && $data['image_offset_size'] ) {
					$img_offset_url = esc_url_raw( get_the_post_thumbnail_url( $id, $data['image_offset_size'] ) );
				}

				if ( 'first_image' === $data['media_source'] ) {
					$img_url = Fns::get_content_first_image( $id, 'url' );
				}

				if ( isset( $data['default_image']['id'] ) && ! $img_url ) {
					$img_size_key    = 'image_size';
					$default_img_url = wp_get_attachment_image_src( $data['default_image']['id'], $data[ $img_size_key ] );
					$img_url         = $default_img_url[0];
				}

				$excerpt_args = [
					'excerpt_type'      => $data['excerpt_type'],
					'excerpt_limit'     => $data['excerpt_limit'],
					'excerpt_more_text' => $data['excerpt_more_text'],
				];

				$exerpt = Fns::get_the_excerpt( $id, $excerpt_args );

				$author_id         = get_the_author_meta( 'ID' );
				$author_avatar_url = get_avatar_url( $author_id );

				$count_key      = Fns::get_post_view_count_meta_key();
				$get_view_count = get_post_meta( $id, $count_key, true );

				$send_data['posts'][] = [
					'author_name'      => esc_html( get_the_author_meta( 'display_name', $author_id ) ),
					'avatar_url'       => esc_url( $author_avatar_url ),
					'comment_count'    => esc_html( get_comments_number( $id ) ),
					'content'          => get_the_content(),
					'category'         => ! empty( $category_terms ) ? $category_terms : [],
					'category_bg'      => ! empty( $_cat_bg_meta ) ? $_cat_bg_meta : [],
					'tags'             => ! empty( $tags_terms ) ? $tags_terms : '',
					'excerpt'          => $exerpt,
					'id'               => $id,
					'image_url'        => $img_url,
					'thumb_url'        => get_the_post_thumbnail_url( $id, 'thumbnail' ),
					'offset_image_url' => $img_offset_url,
					'post_date'        => esc_html( get_the_date() ),
					'post_link'        => get_the_permalink(),
					'post_type'        => $data['post_type'],
					'prefix'           => $prefix,
					'post_count'       => esc_html( $get_view_count ),
					'title'            => Fns::get_the_title( $id, $data ), // wp_kses( $post->post_title, Fns::allowedHtml() ),
					'taxonomy_lists'   => $filtered_taxonomy_lists,
					'post_class'       => join( ' ', get_post_class( null, $id ) ),
					'layout_style'     => $data['layout_style'],
					'hover_animation'  => $data['hover_animation'],
					'acf_data'         => $acf_data,
					'tpg_post_count'   => $pCount,
					'tpg_total_posts'  => $post_count,
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
