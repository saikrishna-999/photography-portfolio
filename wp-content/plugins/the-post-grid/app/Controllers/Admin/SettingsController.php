<?php
/**
 * Settings Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers\Admin;

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Settings Controller class.
 */
class SettingsController {
	/**
	 * Shortcode tag
	 *
	 * @var string
	 */
	private $sc_tag = 'rt_tpg_scg';

	/**
	 * Class init.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register' ] );
		add_filter( 'plugin_action_links_' . RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'marketing' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_admin_enqueue_scripts' ], 99 );
		add_action( 'wp_print_styles', [ $this, 'tpg_dequeue_unnecessary_styles' ], 99 );
		add_action( 'admin_footer', [ $this, 'pro_alert_html' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
	}

	/**
	 * Calls functions into the correct filters
	 *
	 * @return void
	 */
	public function admin_head() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', [ $this, 'mce_external_plugins' ] );
			add_filter( 'mce_buttons', [ $this, 'mce_buttons' ] );
			echo '<style>';
			echo 'i.mce-i-rt_tpg_scg{';
			echo "background: url('" . esc_url( rtTPG()->get_assets_uri( 'images/icon-20x20.png' ) ) . "');";
			echo '}';
			echo '</style>';
		}
	}

	/**
	 * Adds tinymce plugin
	 *
	 * @param array $plugin_array Plugins.
	 *
	 * @return array
	 */
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array[ $this->sc_tag ] = rtTPG()->get_assets_uri( 'js/mce-button.js' );

		return $plugin_array;
	}

	/**
	 * Adds tinymce button
	 *
	 * @param array $buttons Buttons.
	 *
	 * @return array
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, $this->sc_tag );

		return $buttons;
	}

	/**
	 * Pro alert
	 *
	 * @return void
	 */
	public function pro_alert_html() {
		global $typenow;

		if ( rtTPG()->hasPro() ) {
			return;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ( isset( $_GET['page'] ) && 'rttpg_settings' !== $_GET['page'] ) || rtTPG()->post_type !== $typenow ) {
			return;
		}

		$html  = '';
		$html .= '<div class="rt-document-box rt-alert rt-pro-alert">
					<div class="rt-box-icon"><i class="dashicons dashicons-lock"></i></div>
					<div class="rt-box-content">
						<h3 class="rt-box-title">' . esc_html__( 'Pro field alert!', 'the-post-grid' ) . '</h3>
						<p><span></span>' . esc_html__( 'Sorry! this is a Pro field. To use this field, you need to use Pro plugin.', 'the-post-grid' ) . '</p>
						<a href="' . esc_url( rtTpg()->proLink() ) . '" target="_blank" class="rt-admin-btn">' . esc_html__( 'Upgrade to Pro', 'the-post-grid' ) . '</a>
						<a href="#" target="_blank" class="rt-alert-close rt-pro-alert-close">x</a>
					</div>
				</div>';

		Fns::print_html( $html );
	}

	/**
	 * Dequeue styles
	 *
	 * @return void
	 */
	public function tpg_dequeue_unnecessary_styles() {
		$settings = get_option( rtTPG()->options['settings'] );

		if ( isset( $settings['tpg_skip_fa'] ) ) {
			wp_dequeue_style( 'rt-fontawsome' );
			wp_deregister_style( 'rt-fontawsome' );
		}
	}

	/**
	 * Admin scripts
	 *
	 * @return void
	 */
	public function settings_admin_enqueue_scripts() {
		global $pagenow, $typenow;

		if ( ! in_array( $pagenow, [ 'edit.php' ], true ) ) {
			return;
		}
		if ( rtTPG()->post_type !== $typenow ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'rt-tpg-admin' );

		// styles.
		wp_enqueue_style( 'rt-tpg-admin' );

		$nonce = wp_create_nonce( rtTPG()->nonceText() );

		wp_localize_script(
			'rt-tpg-admin',
			'rttpg',
			[
				'nonceID' => esc_attr( rtTPG()->nonceId() ),
				'nonce'   => esc_attr( $nonce ),
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'uid'     => get_current_user_id(),
			]
		);
	}

	/**
	 * Marketing
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function marketing( $links ) {
		$links[] = '<a target="_blank" href="' . esc_url( rtTpg()->demoLink() ) . '">Demo</a>';
		$links[] = '<a target="_blank" href="' . esc_url( rtTpg()->docLink() ) . '">Documentation</a>';

		if ( ! rtTPG()->hasPro() ) {
			$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url( rtTpg()->proLink() ) . '">Get Pro</a>';
		}

		return $links;
	}

	/**
	 * Submenu
	 *
	 * @return void
	 */
	public function register() {
		add_submenu_page(
			'edit.php?post_type=' . rtTPG()->post_type,
			esc_html__( 'Settings', 'the-post-grid' ),
			esc_html__( 'Settings', 'the-post-grid' ),
			'administrator',
			'rttpg_settings',
			[ $this, 'settings' ]
		);

		add_submenu_page(
			'edit.php?post_type=' . rtTPG()->post_type,
			esc_html__( 'Get Help', 'the-post-grid' ),
			esc_html__( 'Get Help', 'the-post-grid' ),
			'administrator',
			'rttpg_get_help',
			[ $this, 'get_help' ]
		);
	}

	/**
	 * Get help view
	 *
	 * @return void
	 */
	public function get_help() {
		Fns::view( 'page.help' );
	}

	/**
	 * Settings view
	 *
	 * @return void
	 */
	public function settings() {
		Fns::view( 'settings.settings' );
	}

}
