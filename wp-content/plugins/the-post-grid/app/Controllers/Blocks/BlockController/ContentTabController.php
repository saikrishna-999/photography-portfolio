<?php

namespace RT\ThePostGrid\Controllers\Blocks\BlockController;

use RT\ThePostGrid\Helpers\Fns;

//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude

class ContentTabController {
	/**
	 * @param $attribute_args
	 *
	 * @return mixed|void
	 */
	public static function get_controller( $attribute_args, $prefix = '' ) {
		$prefix         = $attribute_args['prefix'];
		$default_layout = $attribute_args['default_layout'];

		$content_attribute = [
			'uniqueId'                         => [
				'type'    => 'string',
				'default' => '',
			],

			'preview'                          => [
				'type'    => 'boolean',
				'default' => false,
			],

			'prefix'                           => [
				'type'    => 'string',
				'default' => $prefix,
			],

			'offset_img_position'              => [
				'type'    => 'string',
				'default' => 'offset-image-left',
			],

			'is_builder'                       => [
				'type'    => 'string',
				'default' => '',
			],

			// Layouts

			'query_change'                     => [
				'type'    => 'boolean',
				'default' => false,
			],

			$prefix . '_layout'                => [
				'type'    => 'string',
				'default' => $default_layout,
			],

			'middle_border'                    => [
				'type'    => 'string',
				'default' => 'no',
			],

			'grid_column'                      => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
			],

			'grid_layout_style'                => [
				'type'    => 'string',
				'default' => 'tpg-even',
			],

			'list_layout_alignment'            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .list-behaviour .rt-holder .rt-el-content-wrapper {align-items: {{list_layout_alignment}}; }',
					],
				],
			],

			'list_flex_direction'              => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .list-behaviour .rt-holder .rt-el-content-wrapper {flex-direction: {{list_flex_direction}}; }',
					],
				],
			],

			'main_wrapper_hover_tab'           => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'grid_offset_col_width'            => [
				'type'    => 'string',
				'default' => '',
			],

			'full_wrapper_align'               => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-post-holder div {text-align: {{full_wrapper_align}}; }
						{{RTTPG}} .rt-tpg-container .rt-el-post-meta {justify-content: {{full_wrapper_align}}; }',
					],
				],
			],

			// Query Build.

			'show_title'                       => [
				'type'    => 'string',
				'default' => '',
			],

			'multiple_post_type'               => [
				'type'    => 'string',
				'default' => '',
			],

			'post_type'                        => [
				'type'    => 'string',
				'default' => 'post',
			],

			'post_types'                       => [
				'type'    => 'array',
				'default' => [],
			],

			'post_id'                          => [
				'type'    => 'string',
				'default' => '',
			],

			'exclude'                          => [
				'type'    => 'string',
				'default' => '',
			],

			'post_limit'                       => [
				'type'    => 'string',
				'default' => '',
			],

			'offset'                           => [
				'type'    => 'string',
				'default' => '',
			],

			'instant_query'                    => [
				'type'    => 'string',
				'default' => '',
			],

			// Todo: Query Advance Filter give below

			'taxonomy_lists'                   => [
				'type'    => 'object',
				'default' => [],
			],

			'author'                           => [
				'type'    => 'string',
				'default' => '',
			],

			'post_keyword'                     => [
				'type'    => 'string',
				'default' => '',
			],

			'relation'                         => [
				'type'    => 'string',
				'default' => 'OR',
			],

			'start_date'                       => [
				'type'    => 'string',
				'default' => '',
			],
			'end_date'                         => [
				'type'    => 'string',
				'default' => '',
			],

			'orderby'                          => [
				'type'    => 'string',
				'default' => 'date',
			],

			'order'                            => [
				'type'    => 'string',
				'default' => 'desc',
			],

			'ignore_sticky_posts'              => [
				'type'    => 'string',
				'default' => '',
			],

			'no_posts_found_text'              => [
				'type'    => 'string',
				'default' => 'No posts found.',
			],

			// Front-end Filter Settings.

			'multiple_taxonomy'                => [
				'type'    => 'string',
				'default' => '',
			],

			'show_taxonomy_filter'             => [
				'type'    => 'string',
				'default' => '',
			],

			'show_author_filter'               => [
				'type'    => 'string',
				'default' => '',
			],

			'show_order_by'                    => [
				'type'    => 'string',
				'default' => '',
			],

			'show_sort_order'                  => [
				'type'    => 'string',
				'default' => '',
			],

			'show_search'                      => [
				'type'    => 'string',
				'default' => '',
			],

			'search_by'                        => [
				'type'    => 'string',
				'default' => 'all_content',
			],

			'filter_type'                      => [
				'type'    => 'string',
				'default' => 'dropdown',
			],

			'filter_taxonomy'                  => [
				'type'    => 'string',
				'default' => 'category',
			],

			'filter_taxonomies'                => [
				'type'    => 'array',
				'default' => [],
			],

			'filter_btn_style'                 => [
				'type'    => 'string',
				'default' => 'default',
			],

			'filter_btn_item_per_page'         => [
				'type'    => 'string',
				'default' => 'auto',
			],

			'filter_post_count'                => [
				'type'    => 'string',
				'default' => 'no',
			],

			'tgp_filter_taxonomy_hierarchical' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'tpg_hide_all_button'              => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'tax_filter_all_text'              => [
				'type'    => 'string',
				'default' => '',
			],

			'custom_taxonomy_order'            => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'author_filter_all_text'           => [
				'type'    => 'string',
				'default' => '',
			],

			// Pagination

			'show_pagination'                  => [
				'type'    => 'string',
				'default' => '',
			],

			'page'                             => [
				'type'    => 'number',
				'default' => 1,
			],

			'display_per_page'                 => [
				'type'    => 'string',
				'default' => '',
			],

			'pagination_type'                  => [
				'type'    => 'string',
				'default' => 'pagination',
			],

			'ajax_pagination_type'             => [
				'type'    => 'string',
				'default' => '',
			],

			'load_more_button_text'            => [
				'type'    => 'string',
				'default' => 'Load More',
			],

			// Links

			'post_link_type'                   => [
				'type'    => 'string',
				'default' => 'default',
			],

			'link_target'                      => [
				'type'    => 'string',
				'default' => '_self',
			],

			'is_thumb_linked'                  => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'pagination_btn_position'          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination-wrap {position: {{pagination_btn_position}};margin:0;}',
					],
				],
			],

			'pagination_pos_val'               => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination-wrap{top:{{pagination_pos_val}};}',
					],
				],
			],

			'pagination_pos_val_left'          => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination-wrap{left:{{pagination_pos_val_left}};}',
					],
				],
			],

			'pagination_btn_space_btween'      => [
				'type'    => 'string',
				'default' => '',
			],
		];

		$post_types = Fns::get_post_types();

		// Field Selections

		$cf = Fns::is_acf();
		if ( $cf && rtTPG()->hasPro() ) {
			$content_attribute['show_acf'] = [
				'type'    => 'string',
				'default' => '',
			];

			foreach ( $post_types as $post_type => $post_type_title ) {
				$get_acf_field   = Fns::get_groups_by_post_type( $post_type );
				$selected_acf_id = '';
				if ( ! empty( $get_acf_field ) && is_array( $get_acf_field ) ) {
					$selected_acf_id = array_key_first( $get_acf_field );
				}

				$content_attribute[ $post_type . '_cf_group' ] = [
					'type'    => 'string',
					'default' => '',
				];
			}

			$content_attribute['cf_hide_empty_value'] = [
				'type'    => 'string',
				'default' => 'yes',
			];
			$content_attribute['cf_hide_group_title'] = [
				'type'    => 'string',
				'default' => 'yes',
			];
			$content_attribute['cf_show_only_value']  = [
				'type'    => 'string',
				'default' => 'yes',
			];

		}

		return apply_filters( 'rttpg_guten_content_attribute', $content_attribute );
	}
}
