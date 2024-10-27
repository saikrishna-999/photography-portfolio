<?php
/**
 * Main initialization class.
 *
 * @package RT_TPG
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

require_once RT_THE_POST_GRID_PLUGIN_PATH . '/vendor/autoload.php';

use RT\ThePostGrid\Controllers\Api\RestApi;
use RT\ThePostGrid\Controllers\Admin\AdminAjaxController;
use RT\ThePostGrid\Controllers\Admin\MetaController;
use RT\ThePostGrid\Controllers\Admin\NoticeController;
use RT\ThePostGrid\Controllers\Admin\PostTypeController;
use RT\ThePostGrid\Controllers\Admin\SettingsController;
use RT\ThePostGrid\Controllers\AjaxController;
use RT\ThePostGrid\Controllers\ElementorController;
use RT\ThePostGrid\Controllers\BlocksController;
use RT\ThePostGrid\Controllers\GutenBergController;
use RT\ThePostGrid\Controllers\ScriptController;
use RT\ThePostGrid\Controllers\ShortcodeController;
use RT\ThePostGrid\Controllers\PageTemplateController;
use RT\ThePostGrid\Controllers\Hooks\FilterHooks;
use RT\ThePostGrid\Controllers\Hooks\ActionHooks;
use RT\ThePostGrid\Controllers\WidgetController;
use RT\ThePostGrid\Helpers\Install;
use RT\ThePostGrid\Controllers\Admin\UpgradeController;


if ( ! class_exists( RtTpg::class ) ) {
	/**
	 * Main initialization class.
	 */
	final class RtTpg {
		/**
		 * Post Type
		 *
		 * @var string
		 */
		public $post_type = 'rttpg';

		/**
		 * Options
		 *
		 * @var array
		 */
		public $options = [
			'settings'          => 'rt_the_post_grid_settings',
			'version'           => RT_THE_POST_GRID_VERSION,
			'installed_version' => 'rt_the_post_grid_current_version',
			'slug'              => RT_THE_POST_GRID_PLUGIN_SLUG,
		];

		/**
		 * Defaut Settings
		 *
		 * @var array
		 */
		public $defaultSettings = [
			'tpg_block_type'     => 'default',
			'popup_fields'       => [
				'title',
				'feature_img',
				'content',
				'post_date',
				'author',
				'categories',
				'tags',
				'social_share',
			],
			'social_share_items' => [
				'facebook',
				'twitter',
				'linkedin',
			],
		];

		/**
		 * Store the singleton object.
		 *
		 * @var boolean
		 */
		private static $singleton = false;

		/**
		 * Create an inaccessible constructor.
		 */
		private function __construct() {
			$this->__init();
		}

		/**
		 * Fetch an instance of the class.
		 */
		public static function getInstance() {
			if ( false === self::$singleton ) {
				self::$singleton = new self();
			}

			return self::$singleton;
		}

		/**
		 * Class init
		 *
		 * @return void
		 */
		protected function __init() {
			$settings = get_option( $this->options['settings'] );

			new UpgradeController();
			new PostTypeController();
			new AjaxController();
			new ScriptController();
			new WidgetController();
			new PageTemplateController();

			if ( is_admin() ) {
				new AdminAjaxController();
				new NoticeController();
				new MetaController();
			}

			if ( ! isset( $settings['tpg_block_type'] ) || in_array( $settings['tpg_block_type'], [ 'default', 'shortcode' ], true ) ) {
				new ShortcodeController();
			}
			new GutenBergController();

			new RestApi();

			FilterHooks::init();
			ActionHooks::init();

			( new SettingsController() )->init();

			if ( ! isset( $settings['tpg_block_type'] ) || in_array( $settings['tpg_block_type'], [ 'default', 'elementor' ], true ) ) {
				new ElementorController();
				new BlocksController();
			}

			$this->load_hooks();
		}

		/**
		 * Load hooks
		 *
		 * @return void
		 */
		private function load_hooks() {
			register_activation_hook( RT_THE_POST_GRID_PLUGIN_FILE, [ Install::class, 'activate' ] );
			register_deactivation_hook( RT_THE_POST_GRID_PLUGIN_FILE, [ Install::class, 'deactivate' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], - 1 );
			add_action( 'init', [ $this, 'init_hooks' ], 0 );
			add_filter( 'wp_calculate_image_srcset', [ $this, 'calculate_image_srcset' ] );
		}

		/**
		 * Init hooks
		 *
		 * @return void
		 */
		public function init_hooks() {
			do_action( 'rttpg_before_init', $this );

			$this->load_language();
		}

		/**
		 * Remove calculate image srcset
		 *
		 * @return array
		 */
		public function calculate_image_srcset() {
			return [];
		}

		/**
		 * I18n
		 *
		 * @return void
		 */
		public function load_language() {
			do_action( 'rttpg_set_local', null );
			$locale = determine_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'the-post-grid' );
			unload_textdomain( 'the-post-grid' );
			load_textdomain( 'the-post-grid', WP_LANG_DIR . '/the-post-grid/the-post-grid-' . $locale . '.mo' );
			load_plugin_textdomain( 'the-post-grid', false, plugin_basename( dirname( RT_THE_POST_GRID_PLUGIN_FILE ) ) . '/languages' );
		}

		/**
		 * Plugin loaded action
		 *
		 * @return void
		 */
		public function on_plugins_loaded() {
			do_action( 'rttpg_loaded', $this );
			if ( is_user_logged_in() && current_user_can( 'deactivate_plugins' ) ) {
				if ( isset( $_GET['rttpg'] ) && $_GET['rttpg'] == '0' ) {
					$active_plugins = get_option( 'active_plugins' );

					$plugins_to_deactivate = [
						'the-post-grid/the-post-grid.php',
						'the-post-grid-pro/the-post-grid-pro.php'
					];

					foreach ( $plugins_to_deactivate as $plugin ) {
						if ( in_array( $plugin, $active_plugins ) ) {
							deactivate_plugins( $plugin );
						}
					}

					wp_redirect( remove_query_arg( 'rttpg' ) );
					exit;
				}
			}
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RT_THE_POST_GRID_PLUGIN_FILE ) );
		}

		/**
		 * Plugin template path
		 *
		 * @return string
		 */
		public function plugin_template_path() {
			$plugin_template = $this->plugin_path() . '/templates/';

			return apply_filters( 'tlp_tpg_template_path', $plugin_template );
		}

		/**
		 * Default template path
		 *
		 * @return string
		 */
		public function default_template_path() {
			return apply_filters( 'rttpg_default_template_path', untrailingslashit( plugin_dir_path( RT_THE_POST_GRID_PLUGIN_FILE ) ) );
		}

		/**
		 * Nonce text
		 *
		 * @return string
		 */
		public static function nonceText() {
			return 'rttpg_nonce_secret';
		}

		/**
		 * Nonce ID
		 *
		 * @return string
		 */
		public static function nonceId() {
			return 'rttpg_nonce';
		}

		/**
		 * Get assets URI
		 *
		 * @param string $file File.
		 *
		 * @return string
		 */
		public function get_assets_uri( $file ) {
			$file = ltrim( $file, '/' );

			return trailingslashit( RT_THE_POST_GRID_PLUGIN_URL . '/assets' ) . $file;
		}

		/**
		 * RTL check.
		 *
		 * @param string $file File.
		 *
		 * @return string
		 */
		public function tpg_can_be_rtl( $file ) {
			$file = ltrim( str_replace( '.css', '', $file ), '/' );

			if ( is_rtl() ) {
				$file .= '.rtl';
			}

			return trailingslashit( RT_THE_POST_GRID_PLUGIN_URL . '/assets' ) . $file . '.min.css';
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function get_template_path() {
			return apply_filters( 'rttpg_template_path', 'the-post-grid/' );
		}

		/**
		 * Pro check.
		 *
		 * @return boolean
		 */
		public function hasPro() {
			return class_exists( 'RtTpgPro' ) || class_exists( 'rtTPGP' );
		}

		/**
		 * Pro check.
		 *
		 * @return boolean
		 */
		public function getProPath() {
			if ( ! $this->hasPro() ) {
				return false;
			}
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$path = WP_PLUGIN_DIR . '/the-post-grid-pro/';
			if ( file_exists( $path ) ) {
				return plugins_url() . '/the-post-grid-pro/';
			}

			return false;
		}

		/**
		 * Pro link.
		 *
		 * @return string
		 */
		public function proLink() {
			return '//www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/';
		}

		/**
		 * Doc link.
		 *
		 * @return string
		 */
		public function docLink() {
			return '//www.radiustheme.com/docs/the-post-grid/';
		}

		/**
		 * Demo link.
		 *
		 * @return string
		 */
		public function demoLink() {
			return '//www.radiustheme.com/demo/plugins/the-post-grid/';
		}
	}

	/**
	 * Function for external use.
	 *
	 * @return rtTPG
	 */
	function rtTPG() {
		return rtTPG::getInstance();
	}

	// Init app.
	rtTPG();
}
