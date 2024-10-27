<?php

namespace RT\ThePostGrid\Controllers\Blocks\BlockController;

class StyleTabController {
	/**
	 * Get controller
	 *
	 * @param $prefix
	 *
	 * @return mixed|null
	 */
	public static function get_controller( $prefix = '' ) {
		$style_attribute = [
			// Section Title Style.
			'section_title_alignment'                => [
				'type'    => 'string',
				'default' => '',
			],

			'section_title_margin'                   => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper{{section_title_margin}}',
					],
				],
			],

			'section_title_radius'                   => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .tpg-widget-heading{{section_title_radius}}',
					],
				],
			],

			'section_title_typography'               => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .tpg-widget-heading' ],
				],
			],

			'section_title_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .tpg-widget-heading {color: {{section_title_color}}; }',
					],
				],
			],

			'section_title_bg_color'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, {{RTTPG}} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading {background-color: {{section_title_bg_color}}; }
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after, {{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::before {border-color: {{section_title_bg_color}} transparent; }',
					],
				],
			],

			'section_title_dot_color'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading::before {background-color: {{section_title_dot_color}}}
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::before {background-color: {{section_title_dot_color}}}',
					],
				],
			],

			'section_title_line_color'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						    {{RTTPG}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line {border-color: {{section_title_line_color}} }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before {border-bottom-color: {{section_title_line_color}} }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{RTTPG}}.section-title-style-style3 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel {border-bottom-color: {{section_title_line_color}} }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected {color: {{section_title_line_color}} }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover, {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover {color: {{section_title_line_color}} }
						    {{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::after {background-color: {{section_title_line_color}} }
						',
					],
				],
			],

			'section_title_line_width'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::before {width: {{section_title_line_width}}px }
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::after {width: calc(100% - calc({{section_title_line_width}}px + 10px)) }
						',
					],
				],
			],

			'section_title_line_spacing'             => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::before {bottom: {{section_title_line_spacing}}px; }
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::after {bottom: calc({{section_title_line_spacing}}px + 2px) }
						',
					],
				],
			],

			'external_icon_size'                     => [
				'type'  => 'number',
				'style' => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link { font-size: {{external_icon_size}}px;}',
					],
				],
			],

			'external_icon_position'                 => [
				'type'  => 'number',
				'style' => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link { top: {{external_icon_position}}px;}',
					],
				],
			],

			'external_icon_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link {color: {{external_icon_color}}; }',
					],
				],
			],

			'external_icon_color_hover'              => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link:hover {color: {{external_icon_color_hover}}; }',
					],
				],
			],

			'cat_tag_description_heading'            => [
				'type'    => 'string',
				'default' => '',
			],

			'taxonomy_des_typography'                => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .tpg-widget-heading' ],
				],
			],

			// Title Style

			'title_spacing'                          => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .entry-title-wrapper{{title_spacing}}',
					],
				],
			],

			'title_padding'                          => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title{{title_padding}}',
					],
				],
			],

			'title_typography'                       => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-el-main-wrapper .entry-title-wrapper .entry-title' ],
				],
			],

			'title_offset_typography'                => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-el-main-wrapper .offset-left .entry-title-wrapper .entry-title' ],
				],
			],

			'title_border_visibility'                => [
				'type'    => 'string',
				'default' => 'default',
			],

			'title_box_hover_tab'                    => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'title_alignment'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .entry-title {text-align: {{title_alignment}}; }',
					],
				],
			],

			'title_color'                            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .entry-title {color: {{title_color}}; }',
					],
				],
			],

			'title_bg_color'                         => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .entry-title {background-color: {{title_bg_color}}; }',
					],
				],
			],

			'title_border_color'                     => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title::before {background-color: {{title_border_color}}; }',
					],
				],
			],

			'title_hover_border_color'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .entry-title {--tpg-primary-color: {{title_hover_border_color}}; }',
					],
				],
			],

			'title_hover_color'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder .entry-title:hover, {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder .entry-title a:hover {color: {{title_hover_color}} !important; }',
					],
				],
			],

			'title_bg_color_hover'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .entry-title:hover {background-color: {{title_bg_color_hover}} !important; }',
					],
				],
			],

			'title_color_box_hover'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title, {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title a {color: {{title_color_box_hover}}; }',
					],
				],
			],

			'title_bg_color_box_hover'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title {background-color: {{title_bg_color_box_hover}}; }',
					],
				],
			],

			'title_border_color_hover'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder:hover .entry-title-wrapper .entry-title::before {background-color: {{title_border_color_hover}}; }',
					],
				],
			],

			// Thumbnail Style

			'img_border_radius'                      => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap, 
						{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap img, 
						{{RTTPG}} .tpg-el-main-wrapper .rt-grid-hover-item .rt-holder .rt-el-content-wrapper,
						{{RTTPG}} .rt-grid-hover-item .grid-hover-content{{img_border_radius}}',
					],
				],
			],

			'image_width'                            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap img {width:{{image_width}}}',
					],
				],
			],

			'thumbnail_position'                     => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder .tpg-el-image-wrap img {object-position:{{thumbnail_position}};}',
					],
				],
			],

			'thumbnail_position_hover'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap img {object-position:{{thumbnail_position_hover}};}',
					],
				],
			],

			'thumbnail_transition_duration'          => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap img {transition-duration:{{thumbnail_transition_duration}}s;}',
					],
				],
			],

			'thumbnail_content_width'                => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .slider-layout7 .rt-grid-hover-item .rt-holder .grid-hover-content {width:{{thumbnail_content_width}}px;margin-left:auto;margin-right:auto;margin-bottom:0}',
					],
				],
			],

			'thumbnail_opacity'                      => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap img {opacity:{{thumbnail_opacity}};}',
					],
				],
			],

			'thumbnail_opacity_hover'                => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap img {opacity:{{thumbnail_opacity_hover}};}',
					],
				],
			],

			'author_image_width'                     => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags span img {width:{{author_image_width}}px;height:{{author_image_width}}px;}',
					],
				],
			],

			'thumbnail_spacing'                      => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap{{thumbnail_spacing}}',
					],
				],
			],

			'thumbnail_overlay_padding'              => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content{{thumbnail_overlay_padding}}',
					],
				],
			],

			'grid_hover_style_tabs'                  => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'grid_hover_overlay_color'               => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:before, {{RTTPG}} .tpg-el-main-wrapper .tpg-el-image-wrap .overlay',
					],
				],
			],

			'thumb_lightbox_bg'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa {background-color: {{thumb_lightbox_bg}}; }',
					],
				],
			],
			'thumb_lightbox_color'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa {color: {{thumb_lightbox_color}}; }',
					],
				],
			],

			'grid_hover_overlay_color_hover'         => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:after, {{RTTPG}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap .overlay',
					],
				],
			],

			'thumb_lightbox_bg_hover'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa {background-color: {{thumb_lightbox_bg_hover}}; }',
					],
				],
			],
			'thumb_lightbox_color_hover'             => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa {color: {{thumb_lightbox_color_hover}}; }',
					],
				],
			],
			'grid_hover_overlay_type'                => [
				'type'    => 'string',
				'default' => 'always',
			],
			'grid_hover_overlay_height'              => [
				'type'    => 'string',
				'default' => 'default',
			],
			'on_hover_overlay'                       => [
				'type'    => 'string',
				'default' => 'default',
			],

			// Content Style

			'excerpt_style_tabs'                     => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'content_typography'                     => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner' ],
				],
			],

			'excerpt_spacing'                        => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-excerpt{{excerpt_spacing}}',
					],
				],
			],

			'content_alignment'                      => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner, {{RTTPG}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner * {text-align: {{content_alignment}}; }',
					],
				],
			],

			'excerpt_color'                          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner {color: {{excerpt_color}}; }',
					],
				],
			],

			'excerpt_border'                         => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}}.meta_position_default .tpg-el-main-wrapper .grid-layout3 .rt-holder .rt-el-post-meta::before {background: {{excerpt_border}}; }',
					],
				],
			],

			'excerpt_hover_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-el-excerpt .tpg-excerpt-inner {color: {{excerpt_hover_color}}; }',
					],
				],
			],

			'excerpt_border_hover'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}}.meta_position_default .tpg-el-main-wrapper .grid-layout3 .rt-holder:hover .rt-el-post-meta::before {color: {{excerpt_border_hover}}; }',
					],
				],
			],

			// Meta Info Style

			'post_meta_typography'                   => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-el-post-meta, {{RTTPG}} .tpg-post-holder .tpg-separate-category .categories-links a' ],
				],
			],

			'postmeta_alignment'                     => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-el-post-meta {text-align: {{postmeta_alignment}};}
						{{RTTPG}} .rt-tpg-container .rt-el-post-meta {justify-content: {{postmeta_alignment}}; }',
					],
				],
			],

			'meta_wrap_spacing'                      => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder .rt-el-post-meta{{meta_wrap_spacing}}',
					],
				],
			],

			'meta_spacing'                           => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .post-meta-user span, {{RTTPG}} .post-meta-tags span{{meta_spacing}}',
					],
				],
			],
			'separator_cat_heading'                  => [
				'type'    => 'string',
				'default' => '',
			],

			'separator_cat_typography'               => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .tpg-separate-category .categories-links a' ],
				],
			],

			'category_margin_bottom'                 => [
				'type'  => 'number',
				// 'default' => 0.5,
				'style' => [
					(object) [
						'selector' => '{{RTTPG}}  .tpg-el-main-wrapper .tpg-separate-category.above_title { margin-bottom: {{category_margin_bottom}}px !important;}',
					],
				],
			],

			'category_radius'                        => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links a{{category_radius}}
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a{{category_radius}}
						',
					],
				],
			],

			'category_padding'                       => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder .categories-links a{{category_padding}}
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a{{category_padding}}',
					],
				],
			],

			'meta_info_style_tabs'                   => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'meta_info_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags span {color: {{meta_info_color}}; }',
					],
				],
			],

			'meta_link_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags a {color: {{meta_link_color}}; }',
					],
				],
			],

			'meta_separator_color'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .separator {color: {{meta_separator_color}}; }',
					],
				],
			],

			'meta_icon_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags i {color: {{meta_icon_color}}; }',
					],
				],
			],
			'category_position'                      => [
				'type'    => 'string',
				'default' => 'default',
			],

			'category_source'                        => [
				'type'    => 'string',
				'default' => 'category',
			],

			'tag_source'                             => [
				'type'    => 'string',
				'default' => 'post_tag',
			],

			'category_style'                         => [
				'type'    => 'string',
				'default' => 'style1',
			],

			'category_bg_style'                      => [
				'type'    => 'string',
				'default' => 'default',
			],

			'show_cat_icon'                          => [
				'type'    => 'string',
				'default' => '',
			],

			'separate_category_color'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links a {color: {{separate_category_color}}; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a {color: {{separate_category_color}}; }
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links a {color: {{separate_category_color}}; }
						',
					],
				],
			],

			'separate_category_bg'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links a {background-color: {{separate_category_bg}}; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links a::after {border-top-color: {{separate_category_bg}}; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a {background-color: {{separate_category_bg}}; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a:after {border-top-color: {{separate_category_bg}}; }
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links a {background-color: {{separate_category_bg}}; }',
					],
				],
			],

			'separate_category_icon_color'           => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links i {color: {{separate_category_icon_color}}; }
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links i {color: {{separate_category_icon_color}}; }',
					],
				],
			],

			'post_footer_border_color'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .grid-layout3 .rt-holder .rt-el-post-meta::before {background: {{post_footer_border_color}} ; }',
					],
				],
			],

			'meta_link_colo_hover'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder .post-meta-tags a:hover {color: {{meta_link_colo_hover}}; }',
					],
				],
			],

			'separate_category_color_hover'          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a:hover {color: {{separate_category_color_hover}} !important; }
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover {color: {{separate_category_color_hover}} !important; }',
					],
				],
			],

			'separate_category_bg_hover'             => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links a:hover {background-color: {{separate_category_bg_hover}} !important; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links:not(.style1) a:hover {background-color: {{separate_category_bg_hover}} !important; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a:hover::after {border-top-color: {{separate_category_bg_hover}} !important; }
						{{RTTPG}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover {background-color: {{separate_category_bg_hover}} !important; }
						',
					],
				],
			],

			'meta_link_colo_box_hover'               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags * {color: {{meta_link_colo_box_hover}}; }',
					],
				],
			],

			'separate_category_color_box_hover'      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links a,
						{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a {color: {{separate_category_color_box_hover}}; }',
					],
				],
			],

			'separate_category_bg_box_hover'         => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category.style1 .categories-links a {background-color: {{separate_category_bg_box_hover}}; }
						 {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links a::after {border-top-color: {{separate_category_bg_box_hover}}; }
						{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category:not(.style1) .categories-links a,
						 {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a {background-color: {{separate_category_bg_box_hover}}; }',
					],
				],
			],

			'separate_category_icon_color_box_hover' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links i,
						 {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links i {color: {{separate_category_icon_color_box_hover}}; }',
					],
				],
			],

			// Social Icon Style

			'social_icon_margin'                     => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a{{social_icon_margin}}',
					],
				],
			],

			'social_wrapper_margin'                  => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share{{social_wrapper_margin}}',
					],
				],
			],

			'social_icon_radius'                     => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share i{{social_icon_radius}}',
					],
				],
			],
			'social_icon_width'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i{width:{{social_icon_width}}px;text-align:center;}',
					],
				],
			],
			'social_icon_height'                     => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i{height:{{social_icon_height}}px; line-height: {{social_icon_height}}px}',
					],
				],
			],

			'icon_font_size'                         => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i{font-size:{{icon_font_size}}}',
					],
				],
			],

			'social_share_style_tabs'                => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'social_icon_color'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i {color: {{social_icon_color}}; }',
					],
				],
			],

			'social_icon_bg_color'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i {background-color: {{social_icon_bg_color}}; }',
					],
				],
			],

			'social_icon_border'                     => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a i',
					],
				],
			],

			'social_icon_color_hover'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a:hover i {color: {{social_icon_color_hover}} !important; }',
					],
				],
			],

			'social_icon_bg_color_hover'             => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a:hover i {background-color: {{social_icon_bg_color_hover}} !important; }',
					],
				],
			],

			'social_icon_color_box_hover'            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder:hover .rt-tpg-social-share a i {color: {{social_icon_color_box_hover}}; }',
					],
				],
			],

			'social_icon_bg_color_box_hover'         => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-holder:hover .rt-tpg-social-share a i {background-color: {{social_icon_bg_color_box_hover}}; }',
					],
				],
			],

			'social_icon_border_hover'               => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-social-share a:hover i',
					],
				],
			],

			// ACF Style

			'acf_group_title_typography'             => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-tpg-container .tpg-cf-group-title' ],
				],
			],

			'acf_typography'                         => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-tpg-container .tpg-cf-fields' ],
				],
			],

			'acf_label_style'                        => [
				'type'    => 'string',
				'default' => 'inline',
			],

			'acf_label_width'                        => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tgp-cf-field-label {min-width:{{acf_label_width}}}',
					],
				],
			],
			'acf_alignment'                          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {text-align: {{acf_alignment}}; }',
					],
				],
			],
			'acf_style_tabs'                         => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'acf_group_title_color'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .acf-custom-field-wrap .tpg-cf-group-title {color: {{acf_group_title_color}}; }',
					],
				],
			],

			'acf_label_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .acf-custom-field-wrap .tgp-cf-field-label {color: {{acf_label_color}}; }',
					],
				],
			],

			'acf_value_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .acf-custom-field-wrap .tgp-cf-field-value {color: {{acf_value_color}}; }',
					],
				],
			],

			'acf_group_title_color_hover'            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder:hover .tpg-cf-group-title {color: {{acf_group_title_color_hover}}; }',
					],
				],
			],

			'acf_label_color_hover'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-label {color: {{acf_label_color_hover}}; }',
					],
				],
			],

			'acf_value_color_hover'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-value {color: {{acf_value_color_hover}}; }',
					],
				],
			],

			// Read More Style

			'readmore_typography'                    => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' ],
				],
			],

			'readmore_spacing'                       => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more{{readmore_spacing}}',
					],
				],
			],

			'readmore_padding'                       => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a{{readmore_padding}}',
					],
				],
			],

			'readmore_btn_alignment'                 => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more {text-align: {{readmore_btn_alignment}}; }',
					],
				],
			],
			'readmore_icon_position'                 => [
				'type'    => 'string',
				'default' => 'right',
			],

			'readmore_icon_size'                     => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i {font-size: {{readmore_icon_size}}; }',
					],
				],
			],
			'readmore_icon_y_position'               => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i {transform: translateY({{readmore_icon_y_position}}); }',
					],
				],
			],

			'readmore_text_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a {color: {{readmore_text_color}}; }',
					],
				],
			],

			'readmore_icon_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i {color: {{readmore_icon_color}}; }',
					],
				],
			],

			'readmore_bg'                            => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a {background-color: {{readmore_bg}}; }',
					],
				],
			],

			'readmore_icon_margin'                   => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i{{readmore_icon_margin}}',
					],
				],
			],

			'border_radius'                          => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a{{border_radius}}',
					],
				],
			],
			'readmore_border'                        => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a',
					],
				],
			],
			'readmore_text_color_hover'              => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover {color: {{readmore_text_color_hover}}; }',
					],
				],
			],

			'readmore_icon_color_hover'              => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i {color: {{readmore_icon_color_hover}}; }',
					],
				],
			],

			'readmore_bg_hover'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover {background-color: {{readmore_bg_hover}}; }',
					],
				],
			],

			'readmore_icon_margin_hover'             => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i{{readmore_icon_margin_hover}}',
					],
				],
			],

			'border_radius_hover'                    => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover{{border_radius_hover}}',
					],
				],
			],

			'readmore_border_hover'                  => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
					'important'     => 1,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover',
					],
				],
			],

			'readmore_text_color_box_hover'          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a {color: {{readmore_text_color_box_hover}}; }',
					],
				],
			],

			'readmore_icon_color_box_hover'          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a i {color: {{readmore_icon_color_box_hover}}; }',
					],
				],
			],

			'readmore_bg_box_hover'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a {background-color: {{readmore_bg_box_hover}}; }',
					],
				],
			],

			'readmore_border_box_hover'              => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a',
					],
				],
			],

			// Link Style

			'popup_head_bg'                          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_head_bg}}; }',
					],
				],
			],

			'popup_head_txt_color'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_head_txt_color}}; }',
					],
				],
			],

			'popup_title_color'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_title_color}}; }',
					],
				],
			],

			'popup_meta_color'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_meta_color}}; }',
					],
				],
			],

			'popup_content_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_content_color}}; }',
					],
				],
			],

			'popup_bg'                               => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .selector {color: {{popup_bg}}; }',
					],
				],
			],

			// Pagination - Load more Style

			'pagination_style_tabs'                  => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'pagination_typography'                  => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a, {{RTTPG}} .rt-pagination .pagination-list > li > span, {{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn, {{RTTPG}} .rt-pagination .pagination-list > li i',
					],
				],
			],

			'pagination_text_align'                  => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination-wrap {justify-content: {{pagination_text_align}}; }',
					],
				],
			],

			'pagination_spacing'                     => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination-wrap{{pagination_spacing}}',
					],
				],
			],

			// "pagination_padding" => [
			// "type"    => "object",
			// "default" => [
			// 'lg' => [
			// "isLinked" => true,
			// "unit"     => "px",
			// "value"    => ''
			// ]
			// ],
			// 'style'   => [
			// (object) [
			// 'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a,
			// {{RTTPG}} .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a,
			// {{RTTPG}} .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > span,
			// {{RTTPG}} .rt-pagination .pagination-list > li > span{{pagination_padding}}
			// '
			// ]
			// ]
			// ],

			'pagination_btn_width'                   => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a,
						{{RTTPG}} .rt-pagination .pagination-list > li > span {min-width: {{pagination_btn_width}}; }',
					],
				],
			],

			'pagination_btn_height'                  => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a,
						{{RTTPG}} .rt-pagination .pagination-list > li > span {min-height: {{pagination_btn_height}}; line-height: {{pagination_btn_height}}; }',
					],
				],
			],

			'pagination_border_radius'               => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn, 
						{{RTTPG}} .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a,
						{{RTTPG}} .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > span,
						{{RTTPG}} .rt-pagination .pagination-list > li > a, 
						{{RTTPG}} .rt-pagination .pagination-list > li > span{{pagination_border_radius}}',
					],
				],
			],

			'pagination_color'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li:not(:hover) > a, 
						{{RTTPG}} .rt-pagination .pagination-list > li:not(:hover) > span, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover), 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn {color: {{pagination_color}}; }',
					],
				],
			],

			'pagination_bg'                          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a:not(:hover), 
						{{RTTPG}} .rt-pagination .pagination-list > li:not(:hover) > span,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn {background-color:{{pagination_bg}}; }',
					],
				],
			],

			'pagination_border_color'                => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li > a:not(:hover), 
						{{RTTPG}} .rt-pagination .pagination-list > li:not(:hover) > span, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn',
					],
				],
			],

			'pagination_border_color_hover'          => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li:hover > a, 
						{{RTTPG}} .rt-pagination .pagination-list > li:hover > span,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover',
					],
				],
			],

			'pagination_border_color_active'         => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > .active > a,
						{{RTTPG}} .rt-pagination .pagination-list > .active > span,
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:hover,
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:hover,
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:focus,
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:focus, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a',
					],
				],
			],

			'pagination_color_hover'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li:hover > a, 
						{{RTTPG}} .rt-pagination .pagination-list > li:hover > span,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover {color: {{pagination_color_hover}}; }',
					],
				],
			],

			'pagination_bg_hover'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > li:hover > a, 
						{{RTTPG}} .rt-pagination .pagination-list > li:hover > span,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a,
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover {background-color: {{pagination_bg_hover}}; }',
					],
				],
			],

			'pagination_color_active'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > .active > a, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > span, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:hover, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:hover, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:focus,
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:focus, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a {color: {{pagination_color_active}} !important; }',
					],
				],
			],

			'pagination_bg_active'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-pagination .pagination-list > .active > a, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > span, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:hover, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:hover, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > a:focus, 
						{{RTTPG}} .rt-pagination .pagination-list > .active > span:focus, 
						{{RTTPG}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a  {background-color: {{pagination_bg_active}} !important; }',
					],
				],
			],

			// Front-end Filter Style

			'front_filter_typography'                => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap, {{RTTPG}} .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide' ],
				],
			],

			'filter_text_alignment'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-layout-filter-container .rt-filter-wrap {justify-content: {{filter_text_alignment}}; text-align: {{filter_text_alignment}}; }',
					],
				],
			],
			'layout_vertical_align'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content {justify-content: {{layout_vertical_align}}; }',
					],
				],
			],
			'filter_v_alignment'                     => [
				'type'    => 'string',
				'default' => '',
			],
			'filter_button_width'                    => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-header-wrapper.carousel .rt-layout-filter-container {flex: 0 0 {{filter_button_width}}; max-width: {{filter_button_width}};}',
					],
				],
			],
			'border_style'                           => [
				'type'    => 'string',
				'default' => '',
			],
			'filter_next_prev_btn'                   => [
				'type'    => 'string',
				'default' => 'visible',
			],
			'filter_h_alignment'                     => [
				'type'    => 'string',
				'default' => '',
			],

			'filter_btn_radius'                      => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap,
						{{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input{{filter_btn_radius}}',
					],
				],
			],

			'frontend_filter_style_tabs'             => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'filter_color'                           => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item, 
						{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item, 
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action,
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap, 
						{{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input {color: {{filter_color}}; }
						{{RTTPG}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:before, 
						{{RTTPG}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:after {background-color: {{filter_color}}; }',
					],
				],
			],

			'filter_bg_color'                        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
						 {{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap,
						 {{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action
						 {background-color: {{filter_bg_color}}; }',
					],
				],
			],

			'filter_border_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
						 {{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap,
						 {{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action,
						 {{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input,
						 {{RTTPG}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container
						 {border-color: {{filter_border_color}}; }',
					],
				],
			],

			'filter_search_bg'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input {background-color: {{filter_search_bg}}; }',
					],
				],
			],

			'sub_menu_bg_color'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown {background-color: {{sub_menu_bg_color}}; }',
					],
				],
			],

			'sub_menu_color'                         => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item {color: {{sub_menu_color}}; }',
					],
				],
			],

			'sub_menu_border_bottom'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item {border-bottom-color: {{sub_menu_border_bottom}}; }',
					],
				],
			],

			'filter_nav_color'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {color: {{filter_nav_color}}; }',
					],
				],
			],

			'filter_nav_bg'                          => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {background-color: {{filter_nav_bg}}; }',
					],
				],
			],

			'filter_nav_border'                      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {border-color: {{filter_nav_border}}; }',
					],
				],
			],

			'filter_color_hover'                     => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, 
						{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover,
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover {color: {{filter_color_hover}}; }
						{{RTTPG}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:before, 
						{{RTTPG}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:after{background-color: {{filter_color_hover}}; }',
					],
				],
			],

			'filter_bg_color_hover'                  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, 
						{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover, 
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover, 
						{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover {background-color: {{filter_bg_color_hover}}; }',
					],
				],
			],

			'filter_border_color_hover'              => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
							{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, 
							{{RTTPG}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover,
							{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover,
							{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover,
							{{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover,
							{{RTTPG}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container:hover
							{border-color: {{filter_border_color_hover}}; }',
					],
				],
			],

			'filter_search_bg_hover'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover {background-color: {{filter_search_bg_hover}}; }',
					],
				],
			],

			'sub_menu_bg_color_hover'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover {background-color: {{sub_menu_bg_color_hover}}; }',
					],
				],
			],

			'sub_menu_color_hover'                   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover {color: {{sub_menu_bg_color_hover}}; }',
					],
				],
			],

			'sub_menu_border_bottom_hover'           => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover {border-bottom-color: {{sub_menu_border_bottom_hover}}; }',
					],
				],
			],

			'filter_nav_color_hover'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover {color: {{filter_nav_color_hover}}; }',
					],
				],
			],

			'filter_nav_bg_hover'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover {background-color: {{filter_nav_bg_hover}}; }',
					],
				],
			],

			'filter_nav_border_hover'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover {border-color: {{filter_nav_border_hover}}; }',
					],
				],
			],

			// Box Settings

			'box_margin'                             => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .grid_hover-layout8 .display-grid-wrapper,
						{{RTTPG}} .tpg-el-main-wrapper .rt-row [class*="rt-col"]
						{{box_margin}}',
					],
				],
			],

			'box_margin_extra'                       => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .rt-row [class*="rt-col"]{{box_margin_extra}}',
					],
				],
			],
			// TODO: should apply condition

			'content_box_padding'                    => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .rt-el-content-wrapper, 
						body {{RTTPG}} .rt-tpg-container .rt-el-content-wrapper-flex .post-right-content{{content_box_padding}}',
					],
				],
			],

			'content_box_padding_offset'             => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .offset-left .tpg-post-holder .offset-content, {{RTTPG}} .rt-tpg-container .list-layout4 .post-right-content{{content_box_padding_offset}}',
					],
				],
			],

			'content_box_padding_2'                  => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content{{content_box_padding_2}}',
					],
				],
			],

			'box_radius'                             => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder, body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content{{box_radius}}',
					],
				],
			],

			'is_box_border'                          => [
				'type'    => 'string',
				'default' => '',
			],

			'box_border_bottom'                      => [
				'type'    => 'string',
				'default' => 'disable',
			],

			'box_border_bottom_color'                => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder {border-bottom-color: {{box_border_bottom_color}}; }',
					],
				],
			],

			'box_border_spacing'                     => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder {padding-bottom: {{box_border_spacing}}; }',
					],
				],
			],

			'box_style_tabs'                         => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'box_background'                         => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder',
					],
				],
			],

			'box_background2'                        => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					],
				],
			],

			'box_border'                             => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder {border: 1px solid {{box_border}}; }',
					],
				],
			],

			'box_box_shadow_normal'                  => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0,
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5,
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder' ],
				],
			],

			'box_box_shadow_hover'                   => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0,
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5,
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover' ],
				],
			],

			'box_box_shadow2'                        => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0,
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5,
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' ],
				],
			],

			'box_box_shadow_hover2'                  => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0,
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5,
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' ],
				],
			],

			'box_background_hover'                   => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .tpg-post-holder:hover',
					],
				],
			],

			'box_background_hover2'                  => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					],
				],
			],
			'box_border_hover'                       => [
				'type'    => 'string',
				'default' => '',
			],

			'tpg_wrapper_padding'                    => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container {{tpg_wrapper_padding}}',
					],
				],
			],

			'tpg_wrapper_radius'                     => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} {{tpg_wrapper_radius}}',
					],
				],
			],

			'tpg_wrapper_margin'                     => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container {{tpg_wrapper_margin}}',
					],
				],
			],

			'tpg_wrapper_width'                      => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .rt-tpg-container{max-width:{{tpg_wrapper_width}}; width:100%;}',
					],
				],
			],

			'tpg_wrapper_background'                 => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => null,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}}',
					],
				],
			],

			'scroll_bar_color'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .tpg-el-main-wrapper .swiper-thumb-pagination .swiper-pagination-progressbar-fill {background-color: {{scroll_bar_color}}; }
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail::before {background-color: {{scroll_bar_color}}; }
						',
					],
				],
			],

			'scroll_bar_bg_color'                    => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .tpg-el-main-wrapper .slider-thumb-main-wrapper .swiper-pagination-progressbar {background-color: {{scroll_bar_bg_color}}; }
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper::before {background-color: {{scroll_bar_bg_color}};opacity:1; }
						',
					],
				],
			],

			'thumb_font_color'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .p-content *,
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout12 .swiper-thumb-wrapper .swiper-wrapper .p-content * {color: {{thumb_font_color}}; }',
					],
				],
			],

			'slider_thumb_bg_active'                 => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide:hover .p-thumbnail img,
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide-thumb-active .p-thumbnail img,
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail {background-color: {{slider_thumb_bg_active}}; }',
					],
				],
			],

			'thumb_wrapper_bg'                       => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-el-main-wrapper .slider-thumb-main-wrapper,
						{{RTTPG}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper {background-color: {{thumb_wrapper_bg}}; }',
					],
				],
			],

			'enable_loader'                          => [
				'type'    => 'string',
				'default' => 'disable',
			],

		];

		return apply_filters( 'rttpg_guten_style_attribute', $style_attribute );
	}
}
