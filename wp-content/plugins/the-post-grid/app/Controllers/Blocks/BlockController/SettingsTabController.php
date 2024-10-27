<?php

namespace RT\ThePostGrid\Controllers\Blocks\BlockController;

use RT\ThePostGrid\Helpers\Fns;

class SettingsTabController {

	/**
	 * @return mixed|void
	 */
	public static function get_controller( $prefix = '' ) {

		$settings_attribute = [
			'show_section_title'      => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_title'              => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_thumb'              => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_excerpt'            => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_meta'               => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_date'               => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_category'           => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_author'             => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_tags'               => [
				'type'    => 'string',
				'default' => '',
			],

			'show_comment_count'      => [
				'type'    => 'string',
				'default' => '',
			],

			'show_post_count'         => [
				'type'    => 'string',
				'default' => '',
			],

			'show_read_more'          => [
				'type'    => 'string',
				'default' => 'show',
			],

			'show_social_share'       => [
				'type'    => 'string',
				'default' => '',
			],

			'show_woocommerce_rating' => [
				'type'    => 'string',
				'default' => '',
			],

			// Section Title Settings

			'section_title_style'     => [
				'type'    => 'string',
				'default' => 'style1',
			],

			'section_title_source'    => [
				'type'    => 'string',
				'default' => 'custom_title',
			],

			'section_title_text'      => [
				'type'    => 'string',
				'default' => 'Section Title',
			],

			'title_prefix'            => [
				'type'    => 'string',
				'default' => '',
			],

			'title_suffix'            => [
				'type'    => 'string',
				'default' => '',
			],

			'section_title_tag'       => [
				'type'    => 'string',
				'default' => 'h2',
			],

			'enable_external_link'    => [
				'type'    => 'string',
				'default' => false,
			],

			'external_icon_size'      => [
				'type'    => 'string',
				'default' => '15',
			],

			'section_external_text'   => [
				'type'    => 'string',
				'default' => 'See More',
			],

			'section_external_link'   => [
				'type'    => 'string',
				'default' => '#',
			],

			// Title Settings

			'title_tag'               => [
				'type'    => 'string',
				'default' => 'h3',
			],

			'title_visibility_style'  => [
				'type'    => 'string',
				'default' => 'default',
			],

			'title_limit'             => [
				'type'    => 'string',
				'default' => '',
			],

			'title_limit_type'        => [
				'type'    => 'string',
				'default' => 'word',
			],

			'title_position'          => [
				'type'    => 'string',
				'default' => 'default',
			],

			'title_position_hidden'   => [
				'type'    => 'string',
				'default' => 'default',
			],

			'title_hover_underline'   => [
				'type'    => 'string',
				'default' => 'default',
			],

			// Thumbnail Settings

			'media_source'            => [
				'type'    => 'string',
				'default' => 'feature_image',
			],

			'image_size'              => [
				'type'    => 'string',
				'default' => 'medium_large',
			],

			'image_offset_size'       => [
				'type'    => 'string',
				'default' => 'medium_large',
			],

			'img_crop_style'          => [
				'type'    => 'string',
				'default' => 'hard',
			],

			'image_offset'            => [
				'type'    => 'string',
				'default' => 'medium_large',
			],
			'list_image_side_width'   => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .list-layout-wrapper [class*="rt-col"]:not(.offset-left) .rt-holder .tpg-el-image-wrap {flex: 0 0 {{list_image_side_width}};max-width:{{list_image_side_width}}; }',
					],
				],
			],

			'image_height'            => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap,
						 {{RTTPG}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap img,
						 {{RTTPG}} .tpg-el-main-wrapper.slider-layout11-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper,
						 {{RTTPG}} .tpg-el-main-wrapper.slider-layout12-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper
						{height: {{image_height}}; }',
					],
				],
			],

			'offset_image_height'     => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap,
						 {{RTTPG}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap img {height: {{offset_image_height}}; }',
					],
				],
			],

			'c_image_width'           => [
				'type'    => 'string',
				'default' => '',
			],

			'c_image_height'          => [
				'type'    => 'string',
				'default' => '',
			],

			'hover_animation'         => [
				'type'    => 'string',
				'default' => 'default',
			],

			'is_thumb_lightbox'       => [
				'type'    => 'string',
				'default' => 'default',
			],

			'is_default_img'          => [
				'type'    => 'string',
				'default' => '',
			],

			'default_image'           => [
				'type'    => 'object',
				'default' => [],
			],

			// Post Excerpt Settings

			'excerpt_type'            => [
				'type'    => 'string',
				'default' => 'character',
			],

			'excerpt_limit'           => [
				'type'    => 'string',
				'default' => '100',
			],

			'excerpt_more_text'       => [
				'type'    => 'string',
				'default' => '...',
			],

			// Post Meta Settings

			'meta_position'           => [
				'type'    => 'string',
				'default' => 'default',
			],

			'show_meta_icon'          => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'meta_separator'          => [
				'type'    => 'string',
				'default' => 'default',
			],

			'meta_ordering'           => [
				'type'    => 'array',
				'default' => [],
			],

			// user meta
			'author_prefix'           => [
				'type'    => 'string',
				'default' => 'By',
			],
			'author_icon_visibility'  => [
				'type'    => 'string',
				'default' => 'default',
			],
			'show_author_image'       => [
				'type'    => 'string',
				'default' => 'icon',
			],

			// ACF Settings given below

			'acf_data_lists'          => [
				'type'    => 'object',
				'default' => [],
			],

			// Read More Settings

			'readmore_btn_style'      => [
				'type'    => 'string',
				'default' => 'default-style',
			],

			'read_more_label'         => [
				'type'    => 'string',
				'default' => 'Read More',
			],

			'show_btn_icon'           => [
				'type'    => 'string',
				'default' => '',
			],

			'readmore_style_tabs'     => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'readmore_btn_icon'       => [
				'type'    => 'string',
				'default' => Fns::change_icon( 'fas fa-angle-right', 'right-arrow' ),
			],
		];

		return apply_filters( 'rttpg_guten_settings_attribute', $settings_attribute );
	}
}
