<?php

namespace RT\ThePostGrid\Controllers\Blocks\BlockController;

class SectionTitleSettingsStyle {

	/**
	 * @return mixed|void
	 */
	public static function get_controller() {

		$settings_attribute = [

			'uniqueId'                   => [
				'type'    => 'string',
				'default' => '',
			],

			'show_section_title'         => [
				'type'    => 'string',
				'default' => 'show',
			],

			// Section Title Settings.

			'section_title_style'        => [
				'type'    => 'string',
				'default' => 'style1',
			],

			'section_title_source'       => [
				'type'    => 'string',
				'default' => 'custom_title',
			],

			'section_title_text'         => [
				'type'    => 'string',
				'default' => 'Section Title',
			],

			'title_prefix'               => [
				'type'    => 'string',
				'default' => '',
			],

			'title_suffix'               => [
				'type'    => 'string',
				'default' => '',
			],

			'section_title_tag'          => [
				'type'    => 'string',
				'default' => 'h2',
			],

			'enable_external_link'       => [
				'type'    => 'string',
				'default' => false,
			],

			'section_external_text'      => [
				'type'    => 'string',
				'default' => 'See More',
			],

			'section_external_link'      => [
				'type'    => 'string',
				'default' => '#',
			],

			// Section Title Style
			'section_title_alignment'    => [
				'type'    => 'string',
				'default' => '',
			],

			'section_title_margin'       => [
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

			'section_title_radius'       => [
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

			'section_title_typography'   => [
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

			'section_title_color'        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .tpg-widget-heading {color: {{section_title_color}}; }',
					],
				],
			],

			'section_title_bg_color'     => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, {{RTTPG}} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading {background-color: {{section_title_bg_color}}; }
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after, {{RTTPG}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::before {border-color: {{section_title_bg_color}} transparent; }',
					],
				],
			],

			'section_title_dot_color'    => [
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

			'section_title_line_color'   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '
						    {{RTTPG}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line {border-color: {{section_title_line_color}} }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before {border-bottom-color: {{section_title_line_color}}; }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{RTTPG}}.section-title-style-style3 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel {border-bottom-color: {{section_title_line_color}}; }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected, {{RTTPG}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected {color: {{section_title_line_color}}; }
						    {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover, {{RTTPG}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover {color: {{section_title_line_color}}; }
						    {{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::after {background-color: {{section_title_line_color}} }
						',
					],
				],
			],

			'section_title_line_width'   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::before {width: {{section_title_line_width}}px; }
						{{RTTPG}} .tpg-widget-heading-wrapper.heading-style4::after {width: calc(100% - calc({{section_title_line_width}}px + 10px)) }
						',
					],
				],
			],

			'section_title_line_spacing' => [
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

			'external_icon_size'         => [
				'type'  => 'number',
				'style' => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link { font-size: {{external_icon_size}}px;}',
					],
				],
			],

			'external_icon_position'     => [
				'type'  => 'number',
				'style' => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link { top: {{external_icon_position}}px;}',
					],
				],
			],

			'external_icon_color'        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link {color: {{external_icon_color}}; }',
					],
				],
			],

			'external_icon_color_hover'  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-widget-heading-wrapper .external-link:hover {color: {{external_icon_color_hover}}; }',
					],
				],
			],
		];

		return apply_filters( 'rttpg_guten_settings_attribute', $settings_attribute );
	}
}
