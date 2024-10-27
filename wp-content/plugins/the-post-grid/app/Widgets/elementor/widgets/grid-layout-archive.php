<?php
/**
 * Grid Layout Archive Class
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Grid Layout Archive Class
 */
class TPGGridLayoutArchive extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param  array $data
	 * @param  null  $args
	 *
	 * @throws \Exception
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->prefix       = 'grid';
		$this->tpg_name     = esc_html__( 'TPG - Grid Layout Archive', 'the-post-grid' );
		$this->tpg_base     = 'tpg-grid-layout-archive';
		$this->tpg_icon     = 'eicon-posts-grid tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}

	public function get_script_depends() {
		$scripts = [];
		array_push( $scripts, 'imagesloaded' );
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
		/** Content TAB */

		// Layout.
		rtTPGElementorHelper::grid_layouts( $this, 'archive' );

		// Query.
		rtTPGElementorHelper::query_builder( $this );

		// Filter  Settings.
		// rtTPGElementorHelper::filter_settings( $this, 'archive' );

		// Pagination Settings.
		rtTPGElementorHelper::pagination_settings( $this, 'archive' );

		// Links.
		rtTPGElementorHelper::links( $this );

		/**
		 * Settings Tab
		 * ===========
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

		/** Style TAB */

		// Section Title Style.
		rtTPGElementorHelper::sectionTitle( $this, 'archive' );

		// Title Style.
		rtTPGElementorHelper::titleStyle( $this );

		// Thumbnail Style.
		rtTPGElementorHelper::thumbnailStyle( $this );

		// Content Style.
		rtTPGElementorHelper::contentStyle( $this );

		// Meta Info Style.
		rtTPGElementorHelper::metaInfoStyle( $this );

		// Social Share Settings.
		rtTPGElementorHelper::socialShareStyle( $this );

		// ACF Style.
		rtTPGElementorHelper::tpg_acf_style( $this );

		// Read More Style.
		rtTPGElementorHelper::readmoreStyle( $this );

		// Link Style.
		rtTPGElementorHelper::linkStyle( $this );

		// Pagination - Loadmore Style.
		rtTPGElementorHelper::paginationStyle( $this );

		// Box Settings.
		rtTPGElementorHelper::articlBoxSettings( $this );

		// Promotions.
		rtTPGElementorHelper::promotions( $this );
	}

	protected function render() {
		$data              = $this->get_settings();
		$_prefix           = $this->prefix;
		$data['post_type'] = 'post';

		if ( ! rtTPG()->hasPro() && ! in_array( $data[ $_prefix . '_layout' ], [ 'grid-layout1', 'grid-layout2' ] ) ) {
			$data[ $_prefix . '_layout' ] = 'grid-layout1';
		}

		if ( rtTPG()->hasPro() && ( 'popup' == $data['post_link_type'] || 'multi_popup' == $data['post_link_type'] ) ) {
			wp_enqueue_style( 'rt-magnific-popup' );
			wp_enqueue_script( 'rt-scrollbar' );
			wp_enqueue_script( 'rt-magnific-popup' );
			add_action( 'wp_footer', [ Fns::class, 'get_modal_markup' ] );
		}

		if ( 'masonry' === $data['grid_layout_style'] ) {
			wp_enqueue_script( 'rt-isotope-js' );
			wp_enqueue_script( 'jquery-masonry' );
		}

		if ( 'show' == $data['show_pagination'] && 'pagination_ajax' == $data['pagination_type'] ) {
			wp_enqueue_script( 'rt-pagination' );
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

		$template_path = Fns::tpg_template_path( $post_data );
		$_layout       = $data[ $_prefix . '_layout' ];
		$_layout_style = $data[ $_prefix . '_layout_style' ];
		$dynamicClass  = ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';
		?>

		<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper clearfix <?php echo esc_attr( $_layout . '-main' . ' ' . $dynamicClass ); ?>"
			id="<?php echo esc_attr( $layoutID ); ?>"
			data-layout="<?php echo esc_attr( $data[ $_prefix . '_layout' ] ); ?>"
			data-grid-style="<?php echo esc_attr( $data[ $_prefix . '_layout_style' ] ); ?>"
			data-sc-id="elementor"
			data-el-settings='<?php Fns::print_html( htmlspecialchars( wp_json_encode( $post_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ), true ); ?>'
			data-el-query='<?php Fns::print_html( htmlspecialchars( wp_json_encode( $query_args ) ), true ); ?>'
			data-el-path='<?php echo esc_attr( $template_path ); ?>'
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
			$wrapper_class[] = str_replace( '-2', '', $_layout );
			$wrapper_class[] = 'grid-behaviour';
			$wrapper_class[] = ( in_array( $_layout, [ 'grid-layout2' ] ) ) ? 'tpg-even' : $_layout_style;
			$wrapper_class[] = $_prefix . '_layout_wrapper';

			if ( 'masonry' === $_layout_style && ! in_array( $_layout, [ $this->prefix . '-layout2', $this->prefix . '-layout5', $this->prefix . '-layout6' ] ) ) {
				$wrapper_class[] = 'tpg-masonry';
			}

			// section title settings.
			echo "<div class='tpg-header-wrapper'>";
			Fns::get_section_title( $data );
			echo '</div>';

			?>

			<div data-title="Loading ..." class="rt-row rt-content-loader <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
				<?php
				if ( $query->have_posts() ) {
					$pCount = 1;

					while ( $query->have_posts() ) {
						$query->the_post();
						set_query_var( 'tpg_post_count', $pCount );
						set_query_var( 'tpg_total_posts', $query->post_count );
						Fns::tpg_template( $post_data );
						$pCount++;
						// rtTPGElementorHelper::tpg_template($data, $this->tpg_dir);
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

			<?php Fns::print_html( Fns::get_pagination_markup( $query, $data ) ); ?>
		</div>

		<?php
		if ( 'masonry' === $data[ $_prefix . '_layout_style' ] && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>jQuery('.rt-row.rt-content-loader.tpg-masonry').isotope();</script>
			<?php
		}
		do_action( 'tpg_elementor_script' );
	}
}
