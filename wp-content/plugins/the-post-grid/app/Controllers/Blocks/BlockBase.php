<?php

namespace RT\ThePostGrid\Controllers\Blocks;

use RT\ThePostGrid\Helpers\Fns;

abstract class BlockBase {
	public $last_post_id;

	abstract public function get_attributes();

	abstract public function render_block( $data );

	/**
	 * Script controller
	 *
	 * @param $data
	 *
	 * @return void
	 */
	public function get_script_depends( $data ) {

		$settings           = get_option( rtTPG()->options['settings'] );
		$prefix             = $data['prefix'];
		$this->last_post_id = Fns::get_last_post_id();

		if ( rtTPG()->hasPro() && ( $data['is_thumb_lightbox'] === 'show' || 'popup' == $data['post_link_type'] || 'multi_popup' == $data['post_link_type'] || $data[ $prefix . '_layout' ] == 'grid-layout7' || $data[ $prefix . '_layout' ] == 'slider-layout4' ) ) {
			wp_enqueue_style( 'rt-magnific-popup' );
			wp_enqueue_script( 'rt-magnific-popup' );
		}

		if ( rtTPG()->hasPro() && ( 'popup' == $data['post_link_type'] || 'multi_popup' == $data['post_link_type'] ) ) {
			wp_enqueue_script( 'rt-scrollbar' );
			add_action( 'wp_footer', [ Fns::class, 'get_modal_markup' ] );
		}

		if ( rtTPG()->hasPro() && 'button' == $data['filter_type'] && 'carousel' == $data['filter_btn_style'] ) {
			wp_enqueue_script( 'swiper' );
		}

		if ( isset( $data['grid_layout_style'] ) && 'masonry' === $data['grid_layout_style'] ) {
			wp_enqueue_script( 'rt-isotope-js' );
		}

		if ( 'show' == $data['show_pagination'] && 'pagination_ajax' == $data['pagination_type'] ) {
			wp_enqueue_script( 'rt-pagination' );
		}

		if ( isset( $settings['tpg_load_script'] ) ) {
			wp_enqueue_style( 'rt-fontawsome' );
			wp_enqueue_style( 'rt-flaticon' );
			wp_enqueue_style( 'rt-tpg-block' );
			if ( $data['prefix'] === 'slider' ) {
				wp_enqueue_style( 'swiper' );
			}
		}

		wp_enqueue_script( 'imagesloaded' );
		if ( $data['prefix'] === 'slider' ) {
			wp_enqueue_script( 'swiper' );
		}
		wp_enqueue_script( 'rt-tpg' );
		wp_enqueue_script( 'rttpg-block-pro' );
	}

	/**
	 * Post Query for gutenberg
	 *
	 * @param $data
	 * @param $prefix
	 *
	 * @return array
	 */
	public function post_query_guten( $data, $prefix = '' ) {
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

		if ( $data['post_id'] ) {
			$post_ids         = explode( ',', esc_html( $data['post_id'] ) );
			$post_ids         = array_map( 'trim', $post_ids );
			$args['post__in'] = $post_ids;
		}

		if ( $prefix !== 'slider' && 'show' === $data['show_pagination'] ) {
			$_paged        = is_front_page() ? 'page' : 'paged';
			$args['paged'] = get_query_var( $_paged ) ? intval( get_query_var( $_paged ) ) : 1;
		}

		if ( rtTPG()->hasPro() && 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}

		if ( $orderby = $data['orderby'] ) {
			if ( ! rtTPG()->hasPro() && 'rand' == $orderby ) {
				$orderby = 'date';
			}
			$args['orderby'] = esc_html( $orderby );
		}

		if ( $data['order'] ) {
			$args['order'] = esc_html( $data['order'] );
		}

		if ( $data['instant_query'] ) {
			$args = Fns::get_instant_query( $data['instant_query'], $args );
		}

		if ( $data['author'] ) {
			$args['author__in'] = esc_html( $data['author'] );
		}

		if ( rtTPG()->hasPro() && ( $data['start_date'] || $data['end_date'] ) ) {
			$args['date_query'] = [
				[
					'after'     => trim( esc_html( $data['start_date'] ) ),
					'before'    => trim( esc_html( $data['end_date'] ) ),
					'inclusive' => true,
				],
			];
		}

		$_taxonomies             = get_object_taxonomies( $post_type, 'objects' );
		$_taxonomy_list          = $data['taxonomy_lists'];
		$filtered_taxonomy_lists = [];

		if ( isset( $_taxonomy_list ) && ! empty( $_taxonomy_list ) ) {
			foreach ( $_taxonomies as $index => $object ) {
				if ( in_array( $object->name, Fns::get_excluded_taxonomy() ) ) {
					continue;
				}

				if ( ! isset( $_taxonomy_list[ $object->name ]['options'] ) ) {
					continue;
				}

				$_term_list = wp_list_pluck( $_taxonomy_list[ $object->name ]['options'], 'value' );
				if ( ! empty( $_term_list ) ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query'][] = [
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $_term_list,
					];
				}
			}
		}

		if ( ! empty( $args['tax_query'] ) && $data['relation'] ) {
			$args['tax_query']['relation'] = esc_html( $data['relation'] ); //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		if ( $data['post_keyword'] ) {
			$args['s'] = esc_html( $data['post_keyword'] );
		}

		$offset_posts = $excluded_ids = [];
		if ( $data['exclude'] || $data['offset'] ) {
			if ( $data['exclude'] ) {
				$excluded_ids = explode( ',', esc_html( $data['exclude'] ) );
				$excluded_ids = array_map( 'trim', $excluded_ids );
			}

			if ( $data['offset'] ) {
				$_temp_args = $args;
				unset( $_temp_args['paged'] );
				$_temp_args['posts_per_page'] = esc_html( $data['offset'] );
				$_temp_args['fields']         = 'ids';

				$offset_posts = get_posts( $_temp_args );
			}

			$excluded_post_ids    = array_merge( $offset_posts, $excluded_ids );
			$args['post__not_in'] = array_unique( $excluded_post_ids ); //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		}

		if ( $prefix !== 'slider' ) {
			if ( $data['post_limit'] ) {
				$tempArgs                   = $args;
				$tempArgs['posts_per_page'] = esc_html( $data['post_limit'] );
				$tempArgs['paged']          = 1;
				$tempArgs['fields']         = 'ids';
				if ( ! empty( $offset_posts ) ) {
					$tempArgs['post__not_in'] = $offset_posts; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				}
				$tempQ = new \WP_Query( $tempArgs );
				if ( ! empty( $tempQ->posts ) ) {
					$args['post__in']       = $tempQ->posts;
					$args['posts_per_page'] = ( 'show' == $data['show_pagination'] && $data['display_per_page'] ) ? esc_html( $data['display_per_page'] ) : esc_html( $data['post_limit'] );
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
					)
					) {
						$_posts_per_page = 4;
					}
				}

				$args['posts_per_page'] = intval( $data['display_per_page'] ?: $_posts_per_page );
			}
		} else {
			$slider_per_page = $data['post_limit'];
			if ( $data['slider_layout'] == 'slider-layout10' ) {
				$slider_reminder = ( intval( $data['post_limit'], 10 ) % 5 );
				if ( $slider_reminder ) {
					$slider_per_page = ( $data['post_limit'] - $slider_reminder + 5 );
				}
			}
			$args['posts_per_page'] = intval( $slider_per_page );
		}

		// Builder query
		if ( ! empty( $data['is_builder'] ) && $data['is_builder'] === 'yes' ) {
			$args['posts_per_page'] = get_option( 'posts_per_page' );
			if ( is_tag() ) {
				$args['tag'] = get_query_var( 'tag' );
			}

			if ( is_category() ) {
				$args['cat'] = get_query_var( 'cat' );
			}

			if ( is_author() ) {
				$args['author'] = get_query_var( 'author' );
			}

			if ( is_date() ) {
				$year     = get_query_var( 'year' );
				$monthnum = get_query_var( 'monthnum' );
				$day      = get_query_var( 'day' );

				$args = [
					'date_query' => [
						[
							'year'  => $year,
							'month' => $monthnum,
							'day'   => $day,
						],
					],
				];
			}

			if ( is_search() ) {
				$search    = get_query_var( 's' );
				$args['s'] = esc_html( $search );
			}
		}

		return $args;
	}
}
