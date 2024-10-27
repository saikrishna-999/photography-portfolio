<?php
/**
 * Grid Layout Class
 *
 * @package RT_TPG
 */

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Grid Layout Class
 */
class SectionTitle extends Custom_Widget_Base {

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
		$this->prefix   = 'grid';
		$this->tpg_name = esc_html__( 'TPG - Section Title', 'the-post-grid' );
		$this->tpg_base = 'tpg-section-title';
		$this->tpg_icon = 'eicon-heading tpg-grid-icon'; // .tpg-grid-icon class for just style
	}


	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}

	protected function register_controls() {

		// Section Title Settings.

		$this->start_controls_section(
			'section_title_settings',
			[
				'label' => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'section_title_style',
			[
				'label'        => esc_html__( 'Section Title Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'style1',
				'options'      => [
					'default' => esc_html__( 'Default - Text', 'the-post-grid' ),
					'style1'  => esc_html__( 'Style 1 - Dot & Border', 'the-post-grid' ),
					'style2'  => esc_html__( 'Style 2 - BG & Border', 'the-post-grid' ),
					'style3'  => esc_html__( 'Style 3 - BG & Border - 2', 'the-post-grid' ),
					'style4'  => esc_html__( 'Style 4 - Border Bottom', 'the-post-grid' ),
				],
				'prefix_class' => 'section-title-style-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'section_title_source',
			[
				'label'   => esc_html__( 'Title Source', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'custom_title',
				'options' => [
					'page_title'   => esc_html__( 'Page Title', 'the-post-grid' ),
					'custom_title' => esc_html__( 'Custom Title', 'the-post-grid' ),
				],
			]
		);

		$this->add_control(
			'section_title_text',
			[
				'label'       => esc_html__( 'Title', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'the-post-grid' ),
				'default'     => esc_html__( 'Section Title', 'the-post-grid' ),
				'label_block' => true,
				'condition'   => [
					'section_title_source' => 'custom_title',
				],
			]
		);

		$this->add_control(
			'title_prefix',
			[
				'label'       => esc_html__( 'Title Prefix Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title prefix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$this->add_control(
			'title_suffix',
			[
				'label'       => esc_html__( 'Title Suffix Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title suffix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$this->add_control(
			'section_title_tag',
			[
				'label'   => esc_html__( 'Title Tag', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
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

		$this->add_control(
			'enable_external_link',
			[
				'label'        => esc_html__( 'Enable External Link', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
			]
		);

		$this->add_control(
			'section_external_url',
			[
				'label'       => esc_html__( 'External Link', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'the-post-grid' ),
				'options'     => [ 'url', 'is_external', 'nofollow' ],
				'default'     => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				],
				'label_block' => true,
				'condition'   => [
					'enable_external_link' => 'show',
				],
			]
		);

		$this->add_control(
			'section_external_text',
			[
				'label'     => esc_html__( 'Link Text', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'See More', 'the-post-grid' ),
				'condition' => [
					'enable_external_link' => 'show',
				],
			]
		);

		$this->end_controls_section();

		// TODO: Tab Style Start

		$this->start_controls_section(
			'tpg_section_title_style',
			[
				'label' => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_title_alignment',
			[
				'label'        => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
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
				'render_type'  => 'template',
				'prefix_class' => 'section-title-align-',
			]
		);

		$this->add_responsive_control(
			'section_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_title_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'section_title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading',
			]
		);

		$this->add_control(
			'section_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'section_title_bg_color',
			[
				'label'     => esc_html__( 'Title Background Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading'                => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::before' => 'border-color: {{VALUE}} transparent',
				],
				'condition' => [
					'section_title_style' => [ 'style2', 'style3' ],
				],
			]
		);

		$this->add_control(
			'section_title_dot_color',
			[
				'label'     => esc_html__( 'Dot / Bar Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before'                     => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'section_title_style' => [ 'style1', 'style4' ],
				],
			]
		);

		$this->add_control(
			'section_title_line_color',
			[
				'label'     => esc_html__( 'Line / Border Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line'                                                                                                                                                                                                                                                                      => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected'                                                                                       => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover, {{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover'                                                                                             => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before'                                                                                         => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'                                                                                                                                                                                                                                                                                        => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'section_title_style!' => 'default',
				],
			]
		);

		$this->add_responsive_control(
			'section_title_line_width',
			[
				'label'      => esc_html__( 'Line Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before' => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'  => 'width: calc(100% - calc({{SIZE}}px + 10px))',
				],
				'condition'  => [
					'section_title_style' => [ 'style4' ],
				],
			]
		);

		$this->add_responsive_control(
			'section_title_line_spacing',
			[
				'label'      => esc_html__( 'Line Spacing', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => - 17,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before' => 'bottom: {{SIZE}}px;',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'  => 'bottom: calc({{SIZE}}px + 2px)',
				],
				'condition'  => [
					'section_title_style' => [ 'style4' ],
				],
			]
		);

		$this->add_control(
			'prefix_text_color',
			[
				'label'     => esc_html__( 'Prefix Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .prefix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);
		$this->add_control(
			'suffix_text_color',
			[
				'label'     => esc_html__( 'Suffix Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .suffix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$this->add_control(
			'external_icon_color',
			[
				'label'     => esc_html__( 'External Link Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'external_icon_color_hover',
			[
				'label'     => esc_html__( 'External Link Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'external_icon_size',
			[
				'label'      => esc_html__( 'External Icon Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'external_icon_position',
			[
				'label'      => esc_html__( 'External Icon Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 70,
						'max'  => 70,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'top: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();

		// Promotions.
		rtTPGElementorHelper::promotions( $this );
	}

	protected function render() {
		$data                          = $this->get_settings();
		$data['show_section_title']    = 'show';
		$data['section_external_link'] = $data['section_external_url']['url'] ?? '#';
		$dynamicClass                  = ! empty( $data['section_title_style'] ) ? " section-title-style-{$data['section_title_style']}" : null;
		$dynamicClass                 .= ! empty( $data['section_title_alignment'] ) ? " section-title-align-{$data['section_title_alignment']}" : null;
		$dynamicClass                 .= ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';

		?>
		<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper clearfix <?php echo esc_attr( $dynamicClass ); ?>">
			<?php
			echo "<div class='tpg-header-wrapper'>";
			Fns::get_section_title( $data );
			echo '</div>';
			?>
		</div>
		<?php
	}
}
