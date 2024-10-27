<?php
/**
 * Elementor: ACF Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude

/**
 * Elementor: ACF Widget.
 */
class TPGCategoryBlock extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param array $data
	 * @param null  $args
	 *
	 * @throws \Exception
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->tpg_name = esc_html__( 'TPG - Category Block', 'the-post-grid' );
		$this->tpg_base = 'tpg-category-block';
		$this->tpg_icon = 'eicon-folder-o tpg-grid-icon'; // .tpg-grid-icon class for just style
	}

	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-common' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'tpg_acf_section',
			[
				'label' => esc_html__( 'TPG Advance Custom Field', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$layout_options = [
			'category-layout1' => [
				'title' => esc_html__( 'Layout 1', 'the-post-grid' ),
			],
			'category-layout2' => [
				'title' => esc_html__( 'Layout 2', 'the-post-grid' ),
			],
			'category-layout3' => [
				'title' => esc_html__( 'Layout 3', 'the-post-grid' ),
			],
		];

		$this->add_control(
			'category_layout',
			[
				'label'          => esc_html__( 'Choose Layout', 'the-post-grid' ),
				'type'           => \Elementor\Controls_Manager::CHOOSE,
				'label_block'    => true,
				'options'        => $layout_options,
				'toggle'         => false,
				'default'        => 'category-layout1',
				'style_transfer' => true,
				'classes'        => 'tpg-image-select category-layout',
			]
		);

		$this->add_responsive_control(
			'grid_column',
			[
				'label'          => esc_html__( 'Column', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => [
					'0'  => esc_html__( 'Default from layout', 'the-post-grid' ),
					'12' => esc_html__( '1 Columns', 'the-post-grid' ),
					'6'  => esc_html__( '2 Columns', 'the-post-grid' ),
					'4'  => esc_html__( '3 Columns', 'the-post-grid' ),
					'3'  => esc_html__( '4 Columns', 'the-post-grid' ),
					'24' => esc_html__( '5 Columns', 'the-post-grid' ),
					'2'  => esc_html__( '6 Columns', 'the-post-grid' ),
				],
				'default'        => '0',
				'tablet_default' => '0',
				'mobile_default' => '0',
				'description'    => esc_html__( 'Choose Column for layout.', 'the-post-grid' ),
				// 'render_type'    => 'template'
			]
		);

		$this->add_control(
			'category',
			[
				'label'       => esc_html__( 'Choose Category', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Fns::tpg_get_categories_by_id(),
			]
		);

		$this->add_control(
			'category_number',
			[
				'label' => esc_html__( 'Category Number', 'the-post-grid' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
				'min'   => 0,
				'max'   => 50,
				'step'  => 1,
			]
		);

		$this->add_responsive_control(
			'cat_gap',
			[
				'label'      => esc_html__( 'Grid Gap', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-row'                 => 'margin-left: -{{SIZE}}px;margin-right: -{{SIZE}}px',
					'{{WRAPPER}} .rt-row > .cat-item-col' => 'padding-left: {{SIZE}}px;padding-right: {{SIZE}}px; padding-bottom: calc({{SIZE}}px * 2)',
				],
			]
		);

		$this->add_control(
			'category_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ '{{WRAPPER}} .tpg-category-block-wrapper' => 'text-align: {{VALUE}};' ],
				'condition' => [
					'category_layout' => 'category-layout1',
				],
			]
		);

		$this->end_controls_section();

		// TODO: Category Style
		$this->start_controls_section(
			'category_style',
			[
				'label' => esc_html__( 'Category', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cat_tag',
			[
				'label'   => esc_html__( 'Choose Category Tag', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => esc_html__( 'H1', 'the-post-grid' ),
					'h2' => esc_html__( 'H2', 'the-post-grid' ),
					'h3' => esc_html__( 'H3', 'the-post-grid' ),
					'h4' => esc_html__( 'H4', 'the-post-grid' ),
					'h5' => esc_html__( 'H5', 'the-post-grid' ),
					'h6' => esc_html__( 'H6', 'the-post-grid' ),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .category-name a',
			]
		);

		$this->add_control(
			'cat_spacing',
			[
				'label'              => esc_html__( 'Category Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [ '{{WRAPPER}} .category-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$this->add_control(
			'cat_padding',
			[
				'label'      => esc_html__( 'Category Padding', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .category-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'category_style_tabs'
		);

		$this->start_controls_tab(
			'category_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__( 'Category Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .tpg-category-block-wrapper .category-name a' => 'color: {{VALUE}}' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'category_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'category_color_hover',
			[
				'label'     => esc_html__( 'Category Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .tpg-category-block-wrapper .category-name a:hover' => 'color: {{VALUE}}' ],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// TODO: Image Style
		$this->start_controls_section(
			'image_style',
			[
				'label' => esc_html__( 'Image', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'img_visibility',
			[
				'label'        => esc_html__( 'Count Visibility', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		// Default Image
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'image',
				'exclude' => [ 'custom' ],
				'default' => 'medium_large',
				'label'   => esc_html__( 'Image Size', 'the-post-grid' ),
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => esc_html__( 'Image Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,

				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-category-block-wrapper .cat-thumb' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Image Height', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,

				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-category-block-wrapper .cat-thumb' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'              => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%' ],
				'selectors'          => [ '{{WRAPPER}} .cat-thumb .cat-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'allowed_dimensions' => 'all',
			]
		);

		$this->start_controls_tabs(
			'image_style_tabs'
		);

		$this->start_controls_tab(
			'image_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'img_border',
				'label'    => esc_html__( 'Image Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .cat-thumb .cat-link',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'cat_thumb_bg',
				'label'          => esc_html__( 'Image Overlay', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .cat-thumb .cat-link .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Image Overlay', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'img_border_hover',
				'label'    => esc_html__( 'Image Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .cat-thumb:hover .cat-link',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'cat_thumb_bg_hover',
				'label'          => esc_html__( 'Image Overlay - Hover', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .cat-thumb:hover .cat-link .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Image Overlay - Hover', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		// TODO: Count Style
		$this->start_controls_section(
			'count_style',
			[
				'label' => esc_html__( 'Count', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'count_visibility',
			[
				'label'        => esc_html__( 'Count Visibility', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'show_bracket',
			[
				'label'        => esc_html__( 'Show Bracket', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .count',
			]
		);

		$this->add_control(
			'count_position',
			[
				'label'   => esc_html__( 'Count Position', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'thumb',
				'options' => [
					'thumb' => esc_html__( 'With Image', 'the-post-grid' ),
					'title' => esc_html__( 'With Title', 'the-post-grid' ),
				],
			]
		);

		$this->add_control(
			'count_absolute_position',
			[
				'label'        => esc_html__( 'Change Count Position', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'the-post-grid' ),
				'label_on'     => esc_html__( 'Custom', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'count_position' => [ 'thumb' ] ],
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'count_left_pos',
			[
				'label'      => esc_html__( 'Left Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [ '{{WRAPPER}} .tpg-category-block-wrapper .count-thumb' => 'left: {{SIZE}}px;right:auto;' ],

			]
		);
		$this->add_responsive_control(
			'count_top_pos',
			[
				'label'      => esc_html__( 'Top Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [ '{{WRAPPER}} .tpg-category-block-wrapper .count-thumb' => 'top: {{SIZE}}px;bottom:auto;' ],

			]
		);
		$this->add_responsive_control(
			'count_right_pos',
			[
				'label'      => esc_html__( 'Right Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [ '{{WRAPPER}} .tpg-category-block-wrapper .count-thumb' => 'right: {{SIZE}}px;left:auto;' ],

			]
		);
		$this->add_responsive_control(
			'count_bottom_pos',
			[
				'label'      => esc_html__( 'Bottom Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [ '{{WRAPPER}} .tpg-category-block-wrapper .count-thumb' => 'bottom: {{SIZE}}px;top:auto;' ],

			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'count_padding',
			[
				'label'      => esc_html__( 'Padding', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .count' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .count' => 'color: {{VALUE}}' ],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'count_bg',
			[
				'label'     => esc_html__( 'Count Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .count' => 'background-color: {{VALUE}}' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'count_border',
				'label'    => esc_html__( 'Count Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .count',
			]
		);

		$this->end_controls_section();

		// TODO: Count Style
		$this->start_controls_section(
			'card_style',
			[
				'label' => esc_html__( 'Card', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Card Padding', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [ '{{WRAPPER}} .card-inner-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'              => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ '%', 'px' ],
				'selectors'          => [ '{{WRAPPER}} .card-inner-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'allowed_dimensions' => 'all',
			]
		);

		$this->start_controls_tabs(
			'card_style_tabs'
		);

		$this->start_controls_tab(
			'card_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'label'    => esc_html__( 'Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .card-inner-wrapper',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'card_background',
				'label'          => esc_html__( 'Background', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .card-inner-wrapper',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'card_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'card_border_hover',
				'label'    => esc_html__( 'Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .card-inner-wrapper:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'card_background_hover',
				'label'          => esc_html__( 'Background Hover', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .card-inner-wrapper:hover',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Hover', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$data = $this->get_settings();

		$categories = $data['category'];

		if ( empty( $categories ) ) {
			$categories = get_terms(
				array(
					'taxonomy'   => 'category',
					'orderby'    => 'count',
					'order'      => 'DESC',
					'hide_empty' => 0,
					'fields'     => 'ids',
					'number'     => ! empty( $data['category_number'] ) ? $data['category_number'] : 5,
				)
			);
		}

		$uniqueId        = $data['uniqueId'] ?? null;
		$uniqueClass     = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamic_classes = 'category-layout3' == $data['category_layout'] ? ' category-layout2' : '';
		?>
		<div class="<?php echo esc_attr( $uniqueClass ); ?>">
			<div class="tpg-category-block-wrapper clearfix <?php echo esc_attr( $data['category_layout'] . ' ' . $dynamic_classes ); ?>">
				<?php if ( is_array( $categories ) ) { ?>
				<div class="rt-row">
					<?php
					$category_date                       = [];
					$category_date['layout']             = $data['category_layout'];
					$category_date['image_size']         = $data['image_size'];
					$category_date['img_visibility']     = $data['img_visibility'];
					$category_date['count_position']     = $data['count_position'];
					$category_date['show_bracket']       = $data['show_bracket'];
					$category_date['cat_tag']            = $data['cat_tag'];
					$category_date['count_visibility']   = $data['count_visibility'];
					$category_date['grid_column']        = $data['grid_column'] ?? '0';
					$category_date['grid_column_tablet'] = $data['grid_column_tablet'] ?? '0';
					$category_date['grid_column_mobile'] = $data['grid_column_mobile'] ?? '0';

					$count = 1;

					foreach ( $categories as $cat ) {
						if ( ! empty( $data['category_number'] ) && $data['category_number'] < $count ) {
							break;
						}
						$category_date['cat'] = $cat;
						Fns::tpg_template( $category_date );
						$count++;
					}
					?>
				</div>
			</div>
					<?php
				} else {
					?>
				<p style="padding: 30px;background: #d1ecf1;"><?php echo esc_html__( 'Please choose few categories from the category lists.', 'the-post-grid' ); ?></p>
					<?php
				}
				?>
		</div>
		<?php
	}
}
