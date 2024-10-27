<?php
/**
 * Slider Layout Archive Class
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Slider Layout Archive Class
 */
class TPGSliderLayoutArchive extends Custom_Widget_Base {

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
		$this->prefix       = 'slider';
		$this->tpg_name     = esc_html__( 'TPG - Slider Layout Archive', 'the-post-grid' );
		$this->tpg_base     = 'tpg-slider-layout-archive';
		$this->tpg_icon     = 'eicon-post-slider tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}

	public function get_script_depends() {
		$scripts = [];

		array_push( $scripts, 'imagesloaded' );
		array_push( $scripts, 'swiper' );
		array_push( $scripts, 'rt-tpg' );
		array_push( $scripts, 'rttpg-block-pro' );

		return $scripts;
	}

	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-block' );
			array_push( $style, 'swiper' );

		}

		return $style;
	}

	protected function register_controls() {
		/**
		 * Content Tabs
		 * =============
		 */

		// Layout.
		rtTPGElementorHelper::grid_layouts( $this, 'archive' );

		// Query.
		rtTPGElementorHelper::query_builder( $this );

		// Links.
		rtTPGElementorHelper::links( $this );

		/**
		 * Settings Tabs
		 * =============
		 */

		// Field Selection.
		rtTPGElementorHelper::field_selection( $this );

		// Section Title Settings.
		rtTPGElementorHelper::section_title_settings( $this, 'archive' );

		// Title Settings.
		rtTPGElementorHelper::post_title_settings( $this );

		// Thumbnail Settings.
		rtTPGElementorHelper::post_thumbnail_settings( $this );

		// Excerpt Settings.
		rtTPGElementorHelper::post_excerpt_settings( $this );

		// Meta Settings.
		rtTPGElementorHelper::post_meta_settings( $this );

		// Advanced Custom Field ACF Settings.
		rtTPGElementorHelper::tpg_acf_settings( $this );

		// Readmore Settings.
		rtTPGElementorHelper::post_readmore_settings( $this );

		// Slider Settings.
		rtTPGElementorHelper::slider_settings( $this );

		/**
		 * Style Tabs
		 * =============
		 */

		// Section Title.
		rtTPGElementorHelper::sectionTitle( $this, 'archive' );

		// Title Style.
		rtTPGElementorHelper::titleStyle( $this );

		// Thumbnail Style.
		rtTPGElementorHelper::thumbnailStyle( $this );

		// Content Style.
		rtTPGElementorHelper::contentStyle( $this );

		// Meta Info Style.
		rtTPGElementorHelper::metaInfoStyle( $this );

		// Social Style.
		rtTPGElementorHelper::socialShareStyle( $this );

		// ACF Style.
		rtTPGElementorHelper::tpg_acf_style( $this );

		// Read more style.
		rtTPGElementorHelper::readmoreStyle( $this );

		// Link Style.
		rtTPGElementorHelper::linkStyle( $this );

		// Slider Style.
		rtTPGElementorHelper::slider_style( $this );
		rtTPGElementorHelper::slider_thumb_style( $this );

		// Box Settings.
		rtTPGElementorHelper::articlBoxSettings( $this );

		// Promotions.
		rtTPGElementorHelper::promotions( $this );
	}

	protected function render() {
		$data              = $this->get_settings();
		$_prefix           = $this->prefix;
		$data['post_type'] = 'post';

		if ( ! rtTPG()->hasPro() ) { ?>
			<h3 style="text-align: center"><?php echo esc_html__( 'Please upgrade to pro for slider layout!', 'the-post-grid' ); ?></h3>
			<?php
			return;
		}

		if ( rtTPG()->hasPro() && ( 'popup' == $data['post_link_type'] || 'multi_popup' == $data['post_link_type'] ) ) {
			wp_enqueue_style( 'rt-magnific-popup' );
			wp_enqueue_script( 'rt-scrollbar' );
			wp_enqueue_script( 'rt-magnific-popup' );
			add_action( 'wp_footer', [ Fns::class, 'get_modal_markup' ], 1 );
		}

		// Query.
		$query_args     = rtTPGElementorQuery::post_query_builder( $data, $_prefix );
		$query          = new WP_Query( $query_args );
		$rand           = wp_rand();
		$layoutID       = 'rt-tpg-container-' . $rand;
		$posts_per_page = $data['post_limit'];

		/**
		 * TODO: Get Post Data for render post
		 */
		$post_data = Fns::get_render_data_set( $data, $query->max_num_pages, $posts_per_page, $_prefix );
		$_layout   = $data[ $_prefix . '_layout' ];

		$post_data['lazy_load'] = $data['lazyLoad'];

		/**
		 * Post type render
		 */
		$post_types = Fns::get_post_types();
		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );

			if ( empty( $_taxonomies ) ) {
				continue;
			}

			$post_data[ $data['post_type'] . '_taxonomy' ] = isset( $data[ $data['post_type'] . '_taxonomy' ] ) ? $data[ $data['post_type'] . '_taxonomy' ] : '';
			$post_data[ $data['post_type'] . '_tags' ]     = isset( $data[ $data['post_type'] . '_tags' ] ) ? $data[ $data['post_type'] . '_tags' ] : '';
		}

		$post_data['enable_2_rows'] = $data['enable_2_rows'];

		$default_grid_column_desktop = '3';
		$default_grid_column_tab     = '2';
		$default_grid_column_mobile  = '1';

		if ( $_layout == 'slider-layout13' ) {
			$default_grid_column_desktop = '1';
			$default_grid_column_tab     = '1';
			$default_grid_column_mobile  = '1';
		}

		$grid_column_desktop = '0' !== $post_data['grid_column'] ? $post_data['grid_column'] : $default_grid_column_desktop;
		$grid_column_tab     = '0' !== $post_data['grid_column_tablet'] ? $post_data['grid_column_tablet'] : $default_grid_column_tab;
		$grid_column_mobile  = '0' !== $post_data['grid_column_mobile'] ? $post_data['grid_column_mobile'] : $default_grid_column_mobile;

		if ( in_array( $_layout, [ 'slider-layout10', 'slider-layout11' ] ) ) {
			$grid_column_desktop = $grid_column_tab = $grid_column_mobile = '1';
		}
		$dynamicClass = ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';
		?>
		<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper slider-layout-main loading <?php echo esc_attr( $_layout . '-main' . ' ' . $dynamicClass ); ?>"
			 id="<?php echo esc_attr( $layoutID ); ?>"
			 data-layout="<?php echo esc_attr( $data[ $_prefix . '_layout' ] ); ?>"
			 data-grid-style=""
			 data-desktop-col="<?php echo esc_attr( $grid_column_desktop ); ?>"
			 data-tab-col="<?php echo esc_attr( $grid_column_tab ); ?>"
			 data-mobile-col="<?php echo esc_attr( $grid_column_mobile ); ?>"
			 data-sc-id="elementor"
			 data-el-query=''
		>
			<?php

			$settings = get_option( rtTPG()->options['settings'] );
			if ( isset( $settings['tpg_load_script'] ) || isset( $settings['tpg_enable_preloader'] ) ) {
				?>
				<div id="bottom-script-loader" class="bottom-script-loader">
					<div class="rt-ball-clip-rotate">
						<div></div>
					</div>
				</div>
				<?php
			}

			$wrapper_class   = [];
			$wrapper_class[] = 'rt-content-loader grid-behaviour';

			if ( $_layout == 'slider-layout1' ) {
				$wrapper_class[] = 'grid-layout1 ';
			} elseif ( $_layout == 'slider-layout2' ) {
				$wrapper_class[] = 'grid-layout3';
			} elseif ( $_layout == 'slider-layout3' ) {
				$wrapper_class[] = 'grid-layout4';
			} elseif ( $_layout == 'slider-layout4' ) {
				$wrapper_class[] = 'grid-layout7';
			} elseif ( $_layout == 'slider-layout5' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout1 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout6' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout3 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout7' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout8' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout10 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout9' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout11 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout10' ) {
				$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout7 grid_hover_layout_wrapper';
			} elseif ( $_layout == 'slider-layout11' ) {
				$wrapper_class[] = ' grid_hover-layout5 slider-layout';
			} elseif ( $_layout == 'slider-layout12' ) {
				$wrapper_class[] = ' grid_hover-layout5 slider-layout';
			}

			$wrapper_class[] = $_prefix . '_layout_wrapper';

			// section title settings
			Fns::get_section_title( $data );

			$slider_data = [
				'speed'           => $data['speed'],
				'autoPlayTimeOut' => $data['autoplaySpeed'],
				'autoPlay'        => $data['autoplay'] == 'yes' ? true : false,
				'stopOnHover'     => $data['stopOnHover'] == 'yes' ? true : false,
				'nav'             => $data['arrows'] == 'yes' ? true : false,
				'dots'            => $data['dots'] == 'yes' ? true : false,
				'loop'            => $data['infinite'] == 'yes' ? true : false,
				'lazyLoad'        => $data['lazyLoad'] == 'yes' ? true : false,
				'autoHeight'      => $data['autoHeight'] == 'yes' ? true : false,
				'dynamic_dots'    => $data['dynamic_dots'] == 'yes' ? true : false,
				'grabCursor'      => $data['grabCursor'] == 'yes' ? true : false,
			];

			if ( $data['enable_2_rows'] == 'yes' ) {
				$slider_data['autoHeight'] = false;
			}
			?>

			<div class="slider-main-wrapper <?php echo esc_attr( $_layout ); ?>">
				<div class="rt-swiper-holder swiper"
					 data-rtowl-options='<?php echo wp_json_encode( $slider_data ); ?>'
					 dir="<?php echo esc_attr( $data['slider_direction'] ); ?>">
					<div class="swiper-wrapper <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
						<?php
						if ( $query->have_posts() ) {
							$pCount = 1;

							while ( $query->have_posts() ) {
								$query->the_post();
								set_query_var( 'tpg_post_count', $pCount );
								set_query_var( 'tpg_total_posts', $query->post_count );
								Fns::tpg_template( $post_data );

								if ( $_layout == 'slider-layout10' && $pCount == 5 ) {
									$pCount = 0;
								}
								$pCount++;
							}
						} else {
							if ( $data['no_posts_found_text'] ) {
								printf( "<div class='no_posts_found_text'>%s</div>", esc_html( $data['no_posts_found_text'] ) );
							} else {
								printf( "<div class='no_posts_found_text'>%s</div>", esc_html__( 'No post found', 'the-post-grid' ) );
							}
						}
						wp_reset_postdata();
						?>
					</div>

				</div>


				<?php if ( ! in_array( $_layout, [ 'slider-layout11', 'slider-layout12' ] ) ) : ?>
					<!--swiper-pagination-horizontal-->
					<?php if ( $data['dots'] == 'yes' ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>

					<?php if ( $data['arrows'] == 'yes' ) : ?>
						<div class="swiper-navigation">
							<div class="slider-btn swiper-button-prev"></div>
							<div class="slider-btn swiper-button-next"></div>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( in_array( $_layout, [ 'slider-layout11', 'slider-layout12' ] ) ) : ?>
					<div class="slider-thumb-main-wrapper">
						<div class="swiper-thumb-wrapper gallery-thumbs swiper">
							<div class="swiper-wrapper">
								<?php
								if ( $query->have_posts() ) {
									$pCount = 1;

									while ( $query->have_posts() ) {
										$query->the_post();
										set_query_var( 'tpg_post_count', $pCount );
										set_query_var( 'tpg_total_posts', $query->post_count );
										?>
										<div class="swiper-slide">
											<div class="post-thumbnail-wrap">
												<div class="p-thumbnail">
													<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' ); ?>
												</div>
												<div class="p-content">
													<div class="post-taxonomy">
														<?php
														$_cat_id = $data['post_type'] . '_taxonomy';
														echo get_the_term_list( get_the_ID(), $data[ $_cat_id ], null, '<span class="rt-separator">,</span>' );
														?>
													</div>
													<h3 class="thumb-title"><?php the_title(); ?></h3>
													<span class="thumb-date"><?php the_date(); ?></span>
												</div>
											</div>
										</div>
										<?php
										$pCount++;
									}
								} else {
									if ( $data['no_posts_found_text'] ) {
										printf( "<div class='no_posts_found_text'>%s</div>", esc_html( $data['no_posts_found_text'] ) );
									} else {
										printf( "<div class='no_posts_found_text'>%s</div>", esc_html__( 'No post found', 'the-post-grid' ) );
									}
								}
								wp_reset_postdata();
								?>
							</div>
							<div class="swiper-thumb-pagination"></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		do_action( 'tpg_elementor_script' );
	}
}
