<?php
/**
 * Related Post Class
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Related Post Class
 */
class TPGRelatedPost extends Custom_Widget_Base {

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
		$this->tpg_name     = esc_html__( 'TPG - Related Post', 'the-post-grid' );
		$this->tpg_base     = 'tpg-related-post';
		$this->tpg_icon     = 'eicon-posts-grid tpg-grid-icon'; // .tpg-grid-icon class for just style
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
		}

		return $style;
	}

	protected function register_controls() {
		/**
		 * Content Tabs
		 * =============
		 */

		// Layout.
		rtTPGElementorHelper::grid_layouts( $this, 'single' );

		// Query.
		rtTPGElementorHelper::query_builder( $this, 'single' );

		// Links.
		rtTPGElementorHelper::links( $this );

		/**
		 * Settings Tabs
		 * =============
		 */

		// Field Selection.
		rtTPGElementorHelper::field_selection( $this );

		// Section Title Settings.
		rtTPGElementorHelper::section_title_settings( $this, 'single' );

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
		rtTPGElementorHelper::slider_settings( $this, 'single' );

		/**
		 * Style Tabs
		 * =============
		 */

		// Section Title.
		rtTPGElementorHelper::sectionTitle( $this );

		// Title Style.
		rtTPGElementorHelper::titleStyle( $this );

		// Thumbnail Style.
		rtTPGElementorHelper::thumbnailStyle( $this );

		// Content Style.
		rtTPGElementorHelper::contentStyle( $this );

		// Meta Info Style.
		rtTPGElementorHelper::metaInfoStyle( $this );

		// ACF Style.
		rtTPGElementorHelper::tpg_acf_style( $this );

		// Social Style.
		rtTPGElementorHelper::socialShareStyle( $this );

		// Read more style.
		rtTPGElementorHelper::readmoreStyle( $this );

		// Slider Style.
		rtTPGElementorHelper::slider_style( $this, 'single' );
		rtTPGElementorHelper::slider_thumb_style( $this );

		// Link Style.
		rtTPGElementorHelper::linkStyle( $this );

		// Box Settings.
		rtTPGElementorHelper::articlBoxSettings( $this );

		// Promotions.
		rtTPGElementorHelper::promotions( $this );
	}

	protected function render() {
		$data                  = $this->get_settings();
		$data['post_type']     = 'post';
		$data['last_post_id']  = $this->last_post_id;
		$_prefix               = $this->prefix;
		$enable_related_slider = $data['enable_related_slider'];

		if ( $data['enable_related_slider'] !== 'yes' ) {
			$column_arr = [ '_column', '_column_tablet', '_column_mobile' ];
			foreach ( $column_arr as $device ) {
				if ( ! ( isset( $data[ 'slider' . $device ] ) && $data[ 'slider' . $device ] ) ) {
					continue;
				}
				if ( $data[ 'slider' . $device ] == '1' ) {
					$data[ 'slider' . $device ] = '12';
				} elseif ( $data[ 'slider' . $device ] == '2' ) {
					$data[ 'slider' . $device ] = '6';
				} elseif ( $data[ 'slider' . $device ] == '3' ) {
					$data[ 'slider' . $device ] = '4';
				} elseif ( $data[ 'slider' . $device ] == '4' ) {
					$data[ 'slider' . $device ] = '3';
				} elseif ( $data[ 'slider' . $device ] == '5' ) {
					$data[ 'slider' . $device ] = '24';
				} elseif ( $data[ 'slider' . $device ] == '6' ) {
					$data[ 'slider' . $device ] = '2';
				}
			}
		}

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

		// if ( 'show' == $data['show_pagination'] && 'pagination_ajax' == $data['pagination_type'] ) {
		// wp_enqueue_script( 'rt-pagination' );
		// }

		// Query.
		$query_args     = rtTPGElementorQuery::post_query_builder( $data, $_prefix, 'single' );
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

		$post_data['enable_2_rows'] = false;

		$default_grid_column_desktop = $enable_related_slider ? '3' : '4';
		$default_grid_column_tab     = $enable_related_slider ? '2' : '6';
		$default_grid_column_mobile  = $enable_related_slider ? '1' : '12';

		$grid_column_desktop = '0' !== $post_data['grid_column'] ? $post_data['grid_column'] : $default_grid_column_desktop;
		$grid_column_tab     = '0' !== $post_data['grid_column_tablet'] ? $post_data['grid_column_tablet'] : $default_grid_column_tab;
		$grid_column_mobile  = '0' !== $post_data['grid_column_mobile'] ? $post_data['grid_column_mobile'] : $default_grid_column_mobile;

		$item_column = "rt-col-md-{$grid_column_desktop} rt-col-sm-{$grid_column_tab} rt-col-xs-{$grid_column_mobile}";

		$slider_main_class = $enable_related_slider ? 'slider-layout-main loading' : 'slider-is-disable';
		$dynamicClass      = ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';

		if ( $query->have_posts() ) :
			?>
		<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper <?php echo esc_attr( $_layout . '-main' . ' ' . $slider_main_class . ' ' . $dynamicClass ); ?>"
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
			echo "<div class='tpg-header-wrapper'>";
			Fns::get_section_title( $data );
			echo '</div>';

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
			];

			if ( $data['enable_2_rows'] == 'yes' ) {
				$slider_data['autoHeight'] = false;
			}
			?>

			<?php if ( $enable_related_slider ) { ?>
			<div class="slider-main-wrapper <?php echo esc_attr( $_layout ); ?>">

			<div class="rt-swiper-holder swiper"
				 data-rtowl-options='<?php echo wp_json_encode( $slider_data ); ?>'
				 dir="<?php echo esc_attr( $data['slider_direction'] ); ?>">
			<div class="swiper-wrapper <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
			<?php } else { ?>
			<div class="rt-row rt-content-loader <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
				<?php
			}
			?>

			<?php
			$pCount = 1;

			while ( $query->have_posts() ) {
				$query->the_post();
				set_query_var( 'tpg_post_count', $pCount );
				set_query_var( 'tpg_total_posts', $query->post_count );
				?>
				<?php if ( ! $enable_related_slider ) { ?>
					<div class='<?php echo esc_attr( $item_column ); ?>'>
				<?php } ?>
				<?php
				Fns::tpg_template( $post_data );
				?>
				<?php if ( ! $enable_related_slider ) { ?>
					</div>
				<?php } ?>
				<?php

				if ( $_layout == 'slider-layout10' && $pCount == 5 ) {
					$pCount = 0;
				}

				$pCount++;
			}
			wp_reset_postdata();
			?>


			<?php if ( $enable_related_slider ) { ?>
			</div>

			</div>


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


		<?php } ?>
			</div>
			</div>
			<?php
		endif;

		do_action( 'tpg_elementor_script' );
	}
}
