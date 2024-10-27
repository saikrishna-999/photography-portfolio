<?php
/**
 * Upgrade Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers\Admin;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Upgrade Controller class.
 */
class UpgradeController {
	/**
	 * Version compare
	 *
	 * @var string
	 */
	public static $compare_version;

	/**
	 * Class constructor
	 */
	public function __construct() {
		self::$compare_version = '6.0.0';

		if ( ! self::check_plugin_version() ) {
			add_filter( 'plugin_row_meta', [ $this, 'show_update_notification' ], 10, 2 );
			$this->version_notice();
		}
	}

	/**
	 * Plugin version check
	 *
	 * @return bool
	 */
	public static function check_plugin_version() {

		if ( defined( 'RT_TPG_PRO_VERSION' ) && version_compare( RT_TPG_PRO_VERSION, self::$compare_version, '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Notice
	 *
	 * @return void
	 */
	public function version_notice() {
		if ( class_exists( 'RtTpgPro' ) || class_exists( 'rtTPGP' ) ) {
			add_action(
				'admin_notices',
				function () {
					$class    = 'notice notice-error';
					$text     = esc_html__( 'The Post Grid Pro', 'the-post-grid' );
					$link_pro = '//www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/';

					if ( class_exists( 'RtTpgPro' ) ) {
						printf(
							'<div class="%1$s"><p><a target="_blank" href="%2$s"><strong>The Post Grid Pro</strong></a> is not working properly, You need to update <strong>%3$s</strong> version to %4$s or more to get the pro features.</p></div>',
							esc_attr( $class ),
							esc_url( $link_pro ),
							esc_html( $text ),
							esc_html( self::$compare_version )
						);
					}
				}
			);
		}
	}

	/**
	 * Notification
	 *
	 * @param array  $links Link.
	 * @param string $file File.
	 *
	 * @return array
	 */
	public function show_update_notification( $links, $file ) {
		if ( $file == 'the-post-grid-pro/the-post-grid-pro.php' ) {
			$row_meta['tpg_update'] = '<span style="color: red">The Plugin is not compatible with the post grid free. Please update the plugin to ' . self::$compare_version . ' or more otherwise it will not activate.</span>';

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}
