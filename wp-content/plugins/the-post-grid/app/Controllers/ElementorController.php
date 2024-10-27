<?php
/**
 * Elementor Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

// Do not allow directly accessing this file.
use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'ElementorController' ) ) :
	/**
	 * Elementor Controller class.
	 */
	class ElementorController {
		/**
		 * Category ID
		 *
		 * @var string
		 */
		public $el_cat_id;

		/**
		 * Version
		 *
		 * @var string
		 */
		private $version;

		/**
		 * Class constructor
		 */
		public function __construct() {
			$this->version   = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_THE_POST_GRID_VERSION;
			$this->el_cat_id = RT_THE_POST_GRID_PLUGIN_SLUG . '-elements';

			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
				add_action( 'elementor/elements/categories_registered', [ $this, 'widget_category' ] );
				add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'el_editor_script' ] );
				add_action( 'wp_footer', [ $this, 'tpg_el_scripts' ] );
				add_action( 'wp_enqueue_scripts', [ $this, 'tpg_el_style' ] );
				add_filter( 'elementor/editor/localize_settings', [ $this, 'promotePremiumWidgets' ] );
			}
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_el_enqueue' ] );
			add_action( 'wp_ajax_rttpg_get_el_layouts', [ $this, 'rttpg_get_el_layouts' ] );
			add_action( 'wp_ajax_rttpg_el_layout_count', [ $this, 'rttpg_el_layout_count' ] );
		}

		// TODO Import

		public function editor_el_enqueue() {

			//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_style(
				'rttpg-elementor-edition',
				rtTPG()->get_assets_uri( 'elementor/main.css' ) .
				null,
				$this->version
			);

			wp_enqueue_script(
				'rttpg-elementor-import',
				rtTPG()->get_assets_uri( 'elementor/main.js' ),
				[
					'wp-block-editor',
					'wp-blocks',
					'wp-i18n',
					'wp-element',
					'wp-hooks',
					'wp-util',
					'wp-components',
					'elementor-editor',
					'jquery',
				],
				$this->version,
				true
			);

			wp_localize_script(
				'rttpg-elementor-import',
				'rttpgParams',
				[
					'nonce'           => wp_create_nonce( 'rttpg_nonce' ),
					'hasPro'          => rtTPG()->hasPro(),
					'current_user_id' => get_current_user_id(),
					'ajaxurl'         => admin_url( 'admin-ajax.php' ),
					'site_url'        => site_url(),
					'plugin_url'      => RT_THE_POST_GRID_PLUGIN_URL,
					'admin_url'       => admin_url(),
					'plugin_pro_url'  => rtTPG()->getProPath(),
					'post_type'       => Fns::get_post_types(),
					'all_term_list'   => Fns::get_all_taxonomy_guten(),
				]
			);
		}

		public function rttpg_el_layout_count() {

			$BASE_URL = 'https://www.radiustheme.com/demo/plugins/the-post-grid-elementor/wp-json/rttpgapi/v1/layoutinfo/';
			// Verify the request.
			check_ajax_referer( 'rttpg_nonce', 'nonce' );

			// It's good let's do some capability check.
			$user          = wp_get_current_user();
			$allowed_roles = [ 'editor', 'administrator', 'author' ];

			if ( ! array_intersect( $allowed_roles, $user->roles ) ) {
				wp_die( esc_html__( 'You don\'t have permission to perform this action', 'the-post-grid' ) );
			}

			// Cool, we're almost there, let's check the user authenticity a little bit, shall we!
			if ( ! is_user_logged_in() && $user->ID !== sanitize_text_field( $_REQUEST['user_id'] ) ) {
				wp_die( esc_html__( 'You don\'t have proper authorization to perform this action', 'the-post-grid' ) );
			}

			$status    = $_REQUEST['status'] ?? '';
			$layout_id = $_REQUEST['layout_id'] ?? '';

			$post_args         = [ 'timeout' => 120 ];
			$post_args['body'] = [
				'status'    => $status,
				'layout_id' => $layout_id,
			];
			$layoutRequest     = wp_remote_post( $BASE_URL, $post_args );
			if ( is_wp_error( $layoutRequest ) ) {
				wp_send_json_error( [ 'messages' => $layoutRequest->get_error_messages() ] );
			}
			$layoutData = json_decode( $layoutRequest['body'], true );

			wp_send_json_success( $layoutData );
		}


		/**
		 * @return void
		 */
		public function rttpg_get_el_layouts() {

			$BASE_URL = 'https://www.radiustheme.com/demo/plugins/the-post-grid-elementor/wp-json/rttpgelapi/v1/layouts/';

			// Verify the request.
			check_ajax_referer( 'rttpg_nonce', 'nonce' );

			// It's good let's do some capability check.
			$user          = wp_get_current_user();
			$allowed_roles = [ 'editor', 'administrator', 'author' ];

			if ( ! array_intersect( $allowed_roles, $user->roles ) ) {
				wp_die( esc_html__( 'You don\'t have permission to perform this action', 'the-post-grid' ) );
			}

			// Cool, we're almost there, let's check the user authenticity a little bit, shall we!
			if ( ! is_user_logged_in() && $user->ID !== sanitize_text_field( $_REQUEST['user_id'] ) ) {
				wp_die( esc_html__( 'You don\'t have proper authorization to perform this action', 'the-post-grid' ) );
			}

			$status            = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
			$post_args         = [ 'timeout' => 120 ];
			$post_args['body'] = [ 'status' => $status ];
			$layoutRequest     = wp_remote_post( $BASE_URL, $post_args );
			if ( is_wp_error( $layoutRequest ) ) {
				wp_send_json_error( [ 'messages' => $layoutRequest->get_error_messages() ] );
			}
			$layoutData = json_decode( $layoutRequest['body'], true );

			wp_send_json_success( $layoutData );
		}


		/**
		 * Style
		 *
		 * @return void
		 */
		public function tpg_el_style() {
			// Custom CSS From Settings.
			$css = isset( $settings['custom_css'] ) ? stripslashes( $settings['custom_css'] ) : null;
			if ( $css ) {
				wp_add_inline_style( 'rt-tpg-block', $css );
			}
		}

		/**
		 * Scripts
		 *
		 * @return void
		 */
		public function tpg_el_scripts() {
			$ajaxurl = '';

			if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ) ) ) {
				$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
			} else {
				$ajaxurl .= admin_url( 'admin-ajax.php' );
			}

			$variables = [
				'nonceID' => esc_attr( rtTPG()->nonceId() ),
				'nonce'   => esc_attr( wp_create_nonce( rtTPG()->nonceText() ) ),
				'ajaxurl' => esc_url( $ajaxurl ),
				'uid'     => get_current_user_id(),
			];

			wp_localize_script( 'rt-tpg', 'rttpg', $variables );
		}

		/**
		 * Editor Scripts
		 *
		 * @return void
		 */
		public function el_editor_script() {
			wp_enqueue_script( 'tgp-el-editor-scripts', rtTPG()->get_assets_uri( 'js/tpg-el-editor.js' ), [ 'jquery' ], $this->version, true );
			wp_enqueue_style( 'tgp-el-editor-style', rtTPG()->get_assets_uri( 'css/admin/tpg-el-editor.css' ), [], $this->version );
		}

		/**
		 * Elementor widgets
		 *
		 * @param object $widgets_manager Manager.
		 *
		 * @return void
		 */
		public function init_widgets( $widgets_manager ) {
			require_once RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/base.php';
			require_once RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/rtTPGElementorHelper.php';

			// dir_name => class_name.
			$widgets = [
				'grid-layout'       => '\TPGGridLayout',
				'list-layout'       => '\TPGListLayout',
				'grid-hover-layout' => '\TPGGridHoverLayout',
				'slider-layout'     => '\TPGSliderLayout',
				'category-block'    => '\TPGCategoryBlock',
				'section-title'     => '\SectionTitle',
				'news-ticker'       => '\NewsTicker',
			];

			if ( rtTPG()->hasPro() && defined( 'RT_THE_POST_GRID_PRO_PLUGIN_PATH' ) ) {
				$proFileCheck = RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/app/Widgets/elementor/category-block.php';
				if ( file_exists( $proFileCheck ) ) {
					unset( $widgets['category-block'] );
				}
			}

			$widgets = apply_filters( 'tpg_el_widget_register', $widgets );

			foreach ( $widgets as $file_name => $class ) {

				if ( ! rtTPG()->hasPro() && in_array( $file_name, [ 'slider-layout', 'category-block' ] ) ) {
					continue;
				}

				$template_name = 'the-post-grid/elementor/' . $file_name . '.php';

				if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
					$file = get_stylesheet_directory() . $template_name;
				} elseif ( file_exists( get_template_directory() . $template_name ) ) {
					$file = get_template_directory() . $template_name;
				} else {
					$file = RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/widgets/' . $file_name . '.php';
				}

				require_once $file;

				$widgets_manager->register( new $class() );
			}
		}

		/**
		 * Widget category
		 *
		 * @param object $elements_manager Manager.
		 *
		 * @return void
		 */
		public function widget_category( $elements_manager ) {
			$categories['tpg-block-builder-widgets'] = [
				'title' => esc_html__( 'TPG Template Builder Element', 'the-post-grid' ),
				'icon'  => 'fa fa-plug',
			];

			$categories[ RT_THE_POST_GRID_PLUGIN_SLUG . '-elements' ] = [
				'title' => esc_html__( 'The Post Grid', 'the-post-grid' ),
				'icon'  => 'fa fa-plug',
			];

			$get_all_categories = $elements_manager->get_categories();
			$categories         = array_merge( $categories, $get_all_categories );
			$set_categories     = function ( $categories ) {
				$this->categories = $categories;
			};

			$set_categories->call( $elements_manager, $categories );
		}

		/**
		 * Promotion
		 *
		 * @param array $config Config.
		 *
		 * @return array
		 */
		public function promotePremiumWidgets( $config ) {
			if ( rtTPG()->hasPro() ) {
				return $config;
			}

			if ( ! isset( $config['promotionWidgets'] ) || ! is_array( $config['promotionWidgets'] ) ) {
				$config['promotionWidgets'] = [];
			}

			$pro_widgets = [
				[
					'name'        => 'tpg-slider-layout',
					'title'       => esc_html__( 'TPG - Slider Layout', 'the-post-grid' ),
					'description' => esc_html__( 'TPG - Slider Layout', 'the-post-grid' ),
					'icon'        => 'eicon-post-slider tpg-grid-icon tss-promotional-element',
					'categories'  => '[ "the-post-grid-elements" ]',
				],
				[
					'name'        => 'tpg-category-block',
					'title'       => esc_html__( 'TPG - Category Block', 'the-post-grid' ),
					'description' => esc_html__( 'TPG - Category Block', 'the-post-grid' ),
					'icon'        => 'eicon-folder-o tpg-grid-icon tss-promotional-element',
					'categories'  => '[ "the-post-grid-elements" ]',
				],
				[
					'name'        => 'tpg-news-ticker',
					'title'       => esc_html__( 'TPG - News Ticker', 'the-post-grid' ),
					'description' => esc_html__( 'TPG - News Ticker', 'the-post-grid' ),
					'icon'        => 'eicon-carousel tpg-grid-icon tss-promotional-element',
					'categories'  => '[ "the-post-grid-elements" ]',
				],
			];

			$config['promotionWidgets'] = array_merge( $config['promotionWidgets'], $pro_widgets );

			return $config;
		}
	}
endif;
