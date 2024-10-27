<?php
/**
 * Grid Layout Class
 *
 * @package RT_TPG
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Grid Layout Class
 */
class NewsTicker extends Custom_Widget_Base {

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
		$this->tpg_name = esc_html__( 'TPG - News Ticker', 'the-post-grid' );
		$this->tpg_base = 'tpg-news-ticker';
		$this->tpg_icon = 'eicon-carousel tpg-grid-icon'; // .tpg-grid-icon class for just style
	}

	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}

	public function get_script_depends() {
		$scripts = [];
		array_push( $scripts, 'swiper' );
		array_push( $scripts, 'rt-tpg' );
		array_push( $scripts, 'rt-tpg' );
		array_push( $scripts, 'rttpg-block-pro' );

		return $scripts;
	}

	/**
	 * Register Controls
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->news_ticker_settings();
		$this->news_ticker_query();
		$this->news_ticker_style();
		$this->news_ticker_title();
		$this->news_ticker_post();
		$this->news_ticker_control();

		// Promotions.
		rtTPGElementorHelper::promotions( $this );
	}

	/**
	 * Render content
	 *
	 * @return void
	 */
	protected function render() {
		$data  = $this->get_settings();
		$query = new WP_Query( $this->post_query( $data ) );
		?>
		<div class="rttpg-news-tickewr-main clearfix">
			<div class="rt-news-ticker-inner animation-<?php echo esc_attr( $data['direction'] ); ?>">
				<?php if ( $data['title'] ) : ?>
					<div class="ticker-title">
						<?php
						if ( 'none' !== $data['show_icon'] ) {
							?>
							<span class="ticker-live-icon">
							<?php
							if ( 'custom' == $data['show_icon'] && ! empty( $data['title_icon']['value'] ) ) {
								Icons_Manager::render_icon( $data['title_icon'], [ 'aria-hidden' => 'true' ] );
							} else {
								$this->get_breaking_icon( $data['show_icon'] );
							}
							?>
							</span>
							<?php
						}
						?>
						<span><?php echo esc_html( $data['title'] ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $query->have_posts() ) : ?>
					<?php
					if ( 'marquee' === $data['direction'] ) {
						$this->marquee_slider( $query, $data );
					} else {
						$this->swiper_slider( $query, $data );
					}
					?>
				<?php endif; ?>
			</div>
		</div>

		<?php
		do_action( 'tpg_elementor_script' );
	}


	public function swiper_slider( $query, $data ) {

		$speed = $data['speed'] ?? 300;

		if ( $data['direction'] == 'type' ) {
			$speed = 0;
		}

		$swiperConfig = [
			'slidesPerView'     => 1,
			'speed'             => $speed,
			'effect'            => 'fade',
			'loop'              => true,
			'allowTouchMove'    => false,
			'pauseOnMouseEnter' => (bool) $data['pause_on_hover'],
			'autoplay'          => [
				'delay'                => $data['delay'] ?? 2000,
				'disableOnInteraction' => true,
			],
			'navigation'        => [
				'nextEl' => '.newsticker-button-next',
				'prevEl' => '.newsticker-button-prev',
			],
		];

		if ( in_array( $data['direction'], [ 'vertical', 'horizontal' ] ) ) {
			$swiperConfig['parallax'] = true;
		}
		$direction = '';
		if ( 'vertical' === $data['direction'] ) {
			$direction = 'data-swiper-parallax-y=-40';
		}

		if ( 'horizontal' === $data['direction'] ) {
			$direction = 'data-swiper-parallax=-120';
		}
		?>
		<div class="swiper tpg-news-ticker news-ticker-slider" data-swiper='<?php echo wp_json_encode( $swiperConfig ); ?>'>

			<div class="swiper-wrapper">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					?>
					<div class="swiper-slide">
						<div style="--transitionDuration:<?php echo esc_attr( $data['delay'] ); ?>ms" class="ticker-content" <?php echo esc_attr( $direction ); ?>>
							<?php
							if ( 'none' !== $data['post_icon'] ) {
								$this->get_breaking_icon( $data['post_icon'] );
							}
							?>
							<?php if ( 'yes' == $data['post_title_link'] ) { ?>
								<a class="post-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<?php } else { ?>
								<span class="post-link"><?php the_title(); ?></span>
							<?php } ?>
						</div>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_query(); ?>
			</div>
			<?php if ( 'yes' == $data['control_visibility'] ) : ?>
				<div class="navigation">
					<div class="newsticker-button-prev news-ticker-nav">
						<?php $this->get_breaking_icon( 'prev' ); ?>
					</div>
					<div class="swiper-pause news-ticker-nav">
						<?php $this->get_breaking_icon( 'pause' ); ?>
					</div>
					<div class="newsticker-button-next news-ticker-nav">
						<?php $this->get_breaking_icon( 'next' ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}


	public function marquee_slider( $query, $data ) {
		$hoverEffect = '';
		if ( 'yes' === $data['pause_on_hover'] ) {
			$hoverEffect = 'onmouseover=this.stop() onmouseout=this.start()';
		}
		?>
		<div class="tpg-news-ticker tpg-marquee-ticker">
			<marquee class="news-scroll ticker-content"
					 behavior="scroll"
					 direction="left"
				<?php echo esc_attr( $hoverEffect ); ?>
			>
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					if ( 'none' !== $data['post_icon'] ) {
						$this->get_breaking_icon( $data['post_icon'] );
					}
					if ( 'yes' == $data['post_title_link'] ) {
						?>
						<a class="post-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<?php } else { ?>
						<span class="post-link"><?php the_title(); ?></span>
						<?php
					}
				endwhile;
				?>
				<?php wp_reset_query(); ?>
			</marquee>
		</div>
		<?php
	}


	/**
	 * Get breaking icon
	 *
	 * @param $icon
	 *
	 * @return void
	 */
	public function get_breaking_icon( $icon = '' ): void {
		switch ( $icon ) {
			case 'bolt-round':
				echo '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M12.7365 0.963608C6.1091 0.963608 0.736511 6.33619 0.736511 12.9636C0.736511 19.591 6.1091 24.9636 12.7365 24.9636C19.3639 24.9636 24.7365 19.591 24.7365 12.9636C24.7365 6.33619 19.3639 0.963608 12.7365 0.963608ZM13.8782 6.47904C13.9052 6.2628 13.7864 6.05462 13.5865 5.96784C13.3866 5.88106 13.1534 5.93646 13.0139 6.10388L6.46846 13.9584C6.34653 14.1048 6.32024 14.3084 6.40103 14.4809C6.48183 14.6534 6.65511 14.7636 6.8456 14.7636H12.1804L11.5948 19.4482C11.5678 19.6644 11.6866 19.8726 11.8865 19.9594C12.0864 20.0462 12.3196 19.9908 12.4591 19.8233L19.0045 11.9688C19.1265 11.8225 19.1528 11.6188 19.072 11.4463C18.9912 11.2738 18.8179 11.1636 18.6274 11.1636H13.2926L13.8782 6.47904ZM12.7365 13.7818H7.89371L12.6962 8.01874L12.2494 11.5936C12.2319 11.7333 12.2753 11.8738 12.3685 11.9794C12.4617 12.085 12.5957 12.1454 12.7365 12.1454H17.5793L12.7768 17.9085L13.2236 14.3336C13.2411 14.1939 13.1977 14.0534 13.1045 13.9478C13.0113 13.8423 12.8773 13.7818 12.7365 13.7818Z" fill="currentColor"/>
				</svg>';
				break;
			case 'bolt':
				echo ' <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.87273 1.45456L1.32727 9.30911H7.21818L6.56363 14.5455L13.1091 6.69092H7.21818L7.87273 1.45456Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'live':
				echo '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 13C10.3137 13 13 10.3137 13 7C13 3.68629 10.3137 1 7 1C3.68629 1 1 3.68629 1 7C1 10.3137 3.68629 13 7 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 8.8C7.99411 8.8 8.8 7.99411 8.8 7C8.8 6.00589 7.99411 5.2 7 5.2C6.00589 5.2 5.2 6.00589 5.2 7C5.2 7.99411 6.00589 8.8 7 8.8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'prev':
				echo '<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 11L1 5.99998L6 0.999985" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'next':
				echo ' <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 11L6 5.99998L1 0.999985" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'pause':
				echo '<svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.5 1H1V15H4.5V1Z" stroke="currentColor" stroke-width="1.3125" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.5 1H8V15H11.5V1Z" stroke="currentColor" stroke-width="1.3125" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'star':
				echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M4.02818 0.879112C4.36444 0.197874 5.33587 0.197874 5.67214 0.879112L6.42415 2.4026C6.55756 2.67288 6.81531 2.8603 7.11355 2.90389L8.79674 3.14991C9.54833 3.25977 9.84788 4.18362 9.30375 4.7136L8.08724 5.89849C7.87102 6.10908 7.77233 6.41262 7.82335 6.71011L8.11027 8.38295C8.23871 9.13183 7.45262 9.70288 6.78013 9.34922L5.27682 8.55864C5.00972 8.41818 4.6906 8.41818 4.42349 8.55864L2.92019 9.34922C2.2477 9.70288 1.46161 9.13183 1.59005 8.38294L1.87696 6.71011C1.92798 6.41262 1.82929 6.10908 1.61308 5.89849L0.396561 4.7136C-0.147566 4.18362 0.151985 3.25977 0.903574 3.14991L2.58676 2.90389C2.885 2.8603 3.14276 2.67288 3.27617 2.4026L4.02818 0.879112Z" fill="currentColor"/>
				</svg>';
				break;
			case 'star-outline':
				echo '<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.02818 1.71462C5.36444 1.03338 6.33587 1.03338 6.67214 1.71462L7.42415 3.23811C7.55756 3.50839 7.81531 3.69581 8.11355 3.7394L9.79674 3.98542C10.5483 4.09528 10.8479 5.01913 10.3038 5.54911L9.08724 6.734C8.87102 6.94459 8.77233 7.24813 8.82335 7.54562L9.11027 9.21846C9.23871 9.96734 8.45262 10.5384 7.78013 10.1847L6.27682 9.39416C6.00972 9.25369 5.6906 9.25369 5.42349 9.39415L3.92019 10.1847C3.2477 10.5384 2.46161 9.96734 2.59005 9.21845L2.87696 7.54562C2.92798 7.24813 2.82929 6.94459 2.61308 6.734L1.39656 5.54911C0.852434 5.01913 1.15199 4.09528 1.90357 3.98542L3.58676 3.7394C3.885 3.69581 4.14276 3.50839 4.27617 3.23811L5.02818 1.71462Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>';
				break;
			case 'dot':
				echo '<svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect width="8" height="8" rx="4" fill="currentColor"/>
				</svg>';
				break;
		}
	}

	/**
	 * Newsticker Settings
	 *
	 * @return void
	 */
	public function news_ticker_settings() {
		$this->start_controls_section(
			'news_ticker_settings',
			[
				'label' => esc_html__( 'News Ticker', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'        => esc_html__( 'Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'style-1',
				'options'      => [
					'style-1' => esc_html__( 'Style 1', 'the-post-grid' ),
					'style-2' => esc_html__( 'Style 2', 'the-post-grid' ),
					'style-3' => esc_html__( 'Style 3', 'the-post-grid' ),
					'style-4' => esc_html__( 'Style 4', 'the-post-grid' ),
					'style-5' => esc_html__( 'Style 5', 'the-post-grid' ),
					'style-6' => esc_html__( 'Style 6', 'the-post-grid' ),
					'style-7' => esc_html__( 'Style 7', 'the-post-grid' ),
					'style-8' => esc_html__( 'Style 8', 'the-post-grid' ),
				],
				'prefix_class' => 'ticker-',
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Breaking Title', 'the-post-grid' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Breaking News', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'animation_control',
			[
				'label'     => esc_html__( 'Animation Control', 'textdomain' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'direction',
			[
				'label'   => esc_html__( 'Direction', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Slide Horizontal', 'the-post-grid' ),
					'vertical'   => esc_html__( 'Slide Vertical', 'the-post-grid' ),
					'type'       => esc_html__( 'Typing', 'the-post-grid' ),
					'marquee'    => esc_html__( 'Marquee', 'the-post-grid' ),
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Speed', 'the-post-grid' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 300,
			]
		);

		$this->add_control(
			'delay',
			[
				'label'   => esc_html__( 'Delay', 'the-post-grid' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1500,
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause On Hover', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Off', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();
	}


	/**
	 *  Post Query Builderx
	 *
	 * @param $this
	 */
	public function news_ticker_query() {
		$post_types = Fns::get_post_types();

		$taxonomies = get_taxonomies( [], 'objects' );

		$this->start_controls_section(
			'rt_post_query',
			[
				'label' => esc_html__( 'Query Build', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'       => esc_html__( 'Post Source', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $post_types,
				'default'     => 'post',
				'description' => $this->get_pro_message( 'all post type.' ),
			]
		);

		$this->add_control(
			'common_filters_heading',
			[
				'label'     => esc_html__( 'Common Filters:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$this->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Include only', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter the post IDs separated by comma for include', 'the-post-grid' ),
				'placeholder' => 'Eg. 10, 15, 17',
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__( 'Exclude', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter the post IDs separated by comma for exclude', 'the-post-grid' ),
				'placeholder' => 'Eg. 12, 13',
			]
		);

		$this->add_control(
			'post_limit',
			[
				'label'       => esc_html__( 'Limit', 'the-post-grid' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'The number of posts to show. Enter -1 to show all found posts.', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'offset',
			[
				'label'       => esc_html__( 'Offset', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Post offset', 'the-post-grid' ),
				'description' => esc_html__( 'Number of posts to skip. The offset parameter is ignored when post limit => -1 is used.', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'instant_query',
			[
				'label'       => esc_html__( 'Quick Query', 'the-post-grid' ) . $this->pro_label,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default'                     => esc_html__( '--Quick Query--', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'popular_post_1_day_view'     => esc_html__( 'Popular Post (1 Day View)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'popular_post_7_days_view'    => esc_html__( 'Popular Post (7 Days View)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'popular_post_30_days_view'   => esc_html__( 'Popular Post (30 Days View)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'popular_post_all_times_view' => esc_html__( 'Popular Post (All time View)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'most_comment_1_day'          => esc_html__( 'Most Comment (1 Day)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'most_comment_7_days'         => esc_html__( 'Most Comment (7 Days)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'most_comment_30_days'        => esc_html__( 'Most Comment (30 Days)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'random_post_7_days'          => esc_html__( 'Random Posts (7 Days)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'random_post_30_days'         => esc_html__( 'Random Post (30 Days)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'related_category'            => esc_html__( 'Related Posts (Category)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'related_tag'                 => esc_html__( 'Related Posts (Tag)', 'the-post-grid' ) . ' ' . $this->pro_label(),
					'related_cat_tag'             => esc_html__( 'Related Posts (Tag and Category)', 'the-post-grid' ) . ' ' . $this->pro_label(),
				],
				'classes'     => rtTPG()->hasPro() ? '' : 'tpg-pro-field-select',
				'default'     => 'default',
				'description' => esc_html__( 'If you choose any value from here the orderby worn\'t work.', 'the-post-grid' ),
			]
		);

		// Advance Filter

		$this->add_control(
			'advanced_filters_heading',
			[
				'label'     => esc_html__( 'Advanced Filters:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$_url = site_url( 'wp-admin/edit.php?post_type=rttpg&page=tgp_taxonomy_order' );

		foreach ( $taxonomies as $taxonomy => $object ) {

			if ( ! isset( $object->object_type[0] )
				 || ! in_array( $object->object_type[0], array_keys( $post_types ) )
				 || in_array( $taxonomy, Fns::get_excluded_taxonomy() )
			) {
				continue;
			}
			$this->add_control(
				$taxonomy . '_ids',
				[
					'label'       => esc_html__( 'By ', 'the-post-grid' ) . $object->label,
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => Fns::tpg_get_categories_by_id( $taxonomy ),
					'condition'   => [
						'post_type' => $object->object_type,
					],
					'description' => "For custom order: <a target='_blank' href='" . $_url . "'>The Post Grid > Taxonomy Order</a>",
				]
			);
		}

		$this->add_control(
			'relation',
			[
				'label'   => esc_html__( 'Taxonomies Relation', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'OR',
				'options' => [
					'OR'  => __( 'OR', 'the-post-grid' ),
					'AND' => __( 'AND', 'the-post-grid' ),
				],
			]
		);

		$this->add_control(
			'author',
			[
				'label'       => esc_html__( 'By Author', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Fns::rt_get_users(),
			]
		);

		$this->add_control(
			'post_keyword',
			[
				'label'       => esc_html__( 'By Keyword', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Search by keyword', 'the-post-grid' ),
				'description' => esc_html__( 'Search by post title or content keyword', 'the-post-grid' ),
			]
		);

		$this->add_control(
			'date_range',
			[
				'label'          => esc_html__( 'Date Range (Start to End)', 'the-post-grid' ) . $this->pro_label,
				'type'           => Controls_Manager::DATE_TIME,
				'placeholder'    => 'Choose date...',
				'description'    => esc_html__( 'NB: Enter DEL button for delete date range', 'the-post-grid' ),
				'classes'        => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'picker_options' => [
					'enableTime' => false,
					'mode'       => 'range',
					'dateFormat' => 'M j, Y',
				],
			]
		);

		$orderby_opt = [
			'date'          => esc_html__( 'Date', 'the-post-grid' ),
			'ID'            => esc_html__( 'Order by post ID', 'the-post-grid' ),
			'author'        => esc_html__( 'Author', 'the-post-grid' ),
			'title'         => esc_html__( 'Title', 'the-post-grid' ),
			'modified'      => esc_html__( 'Last modified date', 'the-post-grid' ),
			'parent'        => esc_html__( 'Post parent ID', 'the-post-grid' ),
			'comment_count' => esc_html__( 'Number of comments', 'the-post-grid' ),
			'menu_order'    => esc_html__( 'Menu order', 'the-post-grid' ),
		];

		if ( rtTPG()->hasPro() ) {
			$prderby_pro_opt = [
				'rand'                => esc_html__( 'Random order', 'the-post-grid' ),
				'meta_value'          => esc_html__( 'Meta value', 'the-post-grid' ), //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_value_num'      => esc_html__( 'Meta value number', 'the-post-grid' ),
				'meta_value_datetime' => esc_html__( 'Meta value datetime', 'the-post-grid' ),
			];
			$orderby_opt     = array_merge( $orderby_opt, $prderby_pro_opt );
		}

		$this->add_control(
			'orderby',
			[
				'label'       => esc_html__( 'Order by', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $orderby_opt,
				'default'     => 'date',
				'description' => $this->get_pro_message( 'Random Order.' ),
				'condition'   => [
					'instant_query' => 'default',
				],
			]
		);

		$this->add_control(
			'meta_key',
			[
				'label'       => esc_html__( 'Meta Key', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Meta Key.', 'the-post-grid' ),
				'condition'   => [
					'orderby' => [ 'meta_value', 'meta_value_num', 'meta_value_datetime' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Sort order', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'ASC'  => esc_html__( 'ASC', 'the-post-grid' ),
					'DESC' => esc_html__( 'DESC', 'the-post-grid' ),
				],
				'default'   => 'DESC',
				'condition' => [
					'orderby!' => 'rand',
				],
			]
		);

		$this->add_control(
			'ignore_sticky_posts',
			[
				'label'        => esc_html__( 'Ignore sticky posts at the top', 'the-post-grid' ) . $this->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'disabled'     => true,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$this->add_control(
			'no_posts_found_text',
			[
				'label'       => esc_html__( 'No post found Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'No posts found.', 'the-post-grid' ),
				'placeholder' => esc_html__( 'Enter No post found', 'the-post-grid' ),
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Newsticker Style
	 *
	 * @return void
	 */
	public function news_ticker_style() {
		$this->start_controls_section(
			'news_ticker_style',
			[
				'label' => esc_html__( 'News Ticker', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'ticker_border',
				'label'    => esc_html__( 'Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-news-ticker-inner',
			]
		);

		$this->add_control(
			'ticker_background_color',
			[
				'label'     => esc_html__( 'Ticker Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.ticker-style-8 .rt-news-ticker-inner .ticker-title' => 'box-shadow: -6px 6px 0 {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'height',
			[
				'label'     => esc_html__( 'Height', 'the-post-grid' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 30,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner' => 'height:{{VALUE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-news-ticker-inner' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Newsticker Title settings
	 *
	 * @return void
	 */
	public function news_ticker_title() {
		$this->start_controls_section(
			'tpg_news_ticker_title',
			[
				'label' => esc_html__( 'Breaking Title', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'breaking_title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-news-ticker-inner .ticker-title',
			]
		);

		$this->add_control(
			'breaking_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .ticker-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'title_background_color',
				'label'          => esc_html__( 'Title Background', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .rt-news-ticker-inner .ticker-title',
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

		$this->add_control(
			'show_icon',
			[
				'label'   => esc_html__( 'Icon', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bolt-round',
				'options' => [
					'bolt-round' => esc_html__( 'Bolt Round', 'the-post-grid' ),
					'bolt'       => esc_html__( 'Bolt', 'the-post-grid' ),
					'live'       => esc_html__( 'Live', 'the-post-grid' ),
					'custom'     => esc_html__( 'Custom Icon', 'the-post-grid' ),
					'none'       => esc_html__( 'No Icon', 'the-post-grid' ),
				],
			]
		);

		$this->add_control(
			'title_icon',
			[
				'label'     => esc_html__( 'Choose Icon', 'textdomain' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_icon' => 'custom',
				],
			]
		);

		$this->add_control(
			'live_animation',
			[
				'label'        => esc_html__( 'Live Animation', 'textdomain' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => false,
				'return_value' => 'yes',
				'prefix_class' => 'live-animation-',
				'condition'    => [
					'show_icon!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'breaking_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ticker-title :is(i, svg)' => 'font-size: {{SIZE}}px;',
				],
				'condition'  => [
					'show_icon!' => 'none',
				],
			]
		);

		$this->add_control(
			'breaking_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .ticker-title :is(i, svg)' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_icon!' => 'none',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Newsticker Post Title
	 *
	 * @return void
	 */
	public function news_ticker_post() {
		$this->start_controls_section(
			'tpg_news_ticker_post',
			[
				'label' => esc_html__( 'Post Title', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-news-ticker-inner .post-link',
			]
		);

		$this->add_control(
			'post_title_link',
			[
				'label'        => esc_html__( 'Enable Link?', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'post_icon',
			[
				'label'   => esc_html__( 'Icon', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'         => esc_html__( 'None', 'the-post-grid' ),
					'star'         => esc_html__( 'Star Icon', 'the-post-grid' ),
					'star-outline' => esc_html__( 'Star-Outline Icon', 'the-post-grid' ),
					'dot'          => esc_html__( 'Dot Icon', 'the-post-grid' ),
				],
				'default' => 'dot',
			]
		);

		$this->add_responsive_control(
			'post_icon_gap',
			[
				'label'      => esc_html__( 'Icon Gap', 'the-post-grid' ),
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
					'{{WRAPPER}} .rt-news-ticker-inner .ticker-content svg' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .rt-news-ticker-inner .post-link'          => 'margin-right: {{SIZE}}px;',
				],
				'condition'  => [
					'post_icon!' => 'none',
				],
			]
		);

		$this->add_control(
			'post_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .post-link' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_title_color_hover',
			[
				'label'     => esc_html__( 'Title Color:hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .post-link:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'post_title_link' => 'yes',
				],
			]
		);

		$this->add_control(
			'post_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .ticker-content :is(i, svg)' => 'color: {{VALUE}}',
				],
				'condition' => [
					'post_icon!' => 'none',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Newsticker Control
	 *
	 * @return void
	 */
	public function news_ticker_control() {
		$this->start_controls_section(
			'tpg_news_ticker_control',
			[
				'label'     => esc_html__( 'Control Button', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'direction!' => 'marquee',
				],
			]
		);

		$this->add_control(
			'control_visibility',
			[
				'label'        => esc_html__( 'Visibility', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'control-visible-',
			]
		);

		$this->add_control(
			'control_color',
			[
				'label'     => esc_html__( 'Control Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .navigation .news-ticker-nav' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'control_color_h',
			[
				'label'     => esc_html__( 'Control Color:hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .navigation .news-ticker-nav:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'control_bg',
			[
				'label'     => esc_html__( 'Control Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .navigation .news-ticker-nav' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'control_bg_h',
			[
				'label'     => esc_html__( 'Control Background:hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-news-ticker-inner .navigation .news-ticker-nav:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Make Query Qrguments
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function post_query( $data ) {

		$_post_type = ! empty( $data['post_type'] ) ? esc_html( $data['post_type'] ) : 'post';
		$post_type  = Fns::available_post_type( $_post_type );

		$args = [
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $data['post_limit'],
		];

		if ( $data['post_id'] ) {
			$post_ids = explode( ',', esc_html( $data['post_id'] ) );
			$post_ids = array_map( 'trim', $post_ids );

			$args['post__in'] = $post_ids;
		}

		if ( 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}

		if ( $orderby = $data['orderby'] ) {

			$order_by        = ( $orderby == 'meta_value_datetime' ) ? 'meta_value_num' : $orderby;
			$args['orderby'] = esc_html( $order_by );

			if ( in_array( $orderby, [ 'meta_value', 'meta_value_num', 'meta_value_datetime' ] ) && $data['meta_key'] ) {
				$args['meta_key'] = esc_html( $data['meta_key'] ); //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			}
		}

		if ( $data['order'] ) {
			$args['order'] = esc_html( $data['order'] );
		}

		if ( $data['instant_query'] ) {
			$args = Fns::get_instant_query( $data['instant_query'], $args );
		}

		if ( $data['author'] ) {
			$args['author__in'] = array_map( 'intval', $data['author'] );
		}

		if ( isset( $data['date_range'] ) ) :
			if ( rtTPG()->hasPro() && $data['date_range'] ) {
				if ( strpos( $data['date_range'], 'to' ) ) {
					$date_range         = explode( 'to', esc_html( $data['date_range'] ) );
					$args['date_query'] = [
						[
							'after'     => trim( $date_range[0] ),
							'before'    => trim( $date_range[1] ),
							'inclusive' => true,
						],
					];
				}
			}
		endif;

		$_taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$taxo_id     = '_ids';

		foreach ( $_taxonomies as $index => $object ) {
			if ( in_array( $object->name, Fns::get_excluded_taxonomy() ) ) {
				continue;
			}

			$setting_key = $object->name . $taxo_id;

			if ( ! empty( $data[ $setting_key ] ) ) {
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				$args['tax_query'][] = [
					'taxonomy' => $object->name,
					'field'    => 'term_id',
					'terms'    => $data[ $setting_key ],
				];
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
				$_temp_args['posts_per_page'] = $data['offset'];
				$_temp_args['fields']         = 'ids';

				$offset_posts = get_posts( $_temp_args );
			}

			$excluded_post_ids    = array_merge( $offset_posts, $excluded_ids );
			$args['post__not_in'] = array_unique( $excluded_post_ids );  //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		}

		return $args;
	}
}
