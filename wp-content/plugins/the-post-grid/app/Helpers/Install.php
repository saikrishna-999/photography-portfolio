<?php
/**
 * Install Helper class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Helpers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Install Helper class.
 */
class Install {

	public static function activate() {
		self::insertDefaultData();
		self::create_cron_jobs();
		add_option( 'rttpg_activation_redirect', true );
	}

	public static function deactivate() {
		update_option( 'tpg_flush_rewrite_rules', 0 );
		self::clean_cron_jobs();
	}

	/**
	 * Inset default data
	 *
	 * @return void
	 */
	public static function insertDefaultData() {
		update_option( rtTPG()->options['installed_version'], RT_THE_POST_GRID_VERSION );

		if ( ! get_option( rtTPG()->options['settings'] ) ) {
			update_option( rtTPG()->options['settings'], rtTPG()->defaultSettings );
		}

		if ( get_option( 'elementor_experiment-e_optimized_assets_loading' ) ) {
			update_option( 'elementor_experiment-e_optimized_assets_loading', 'default' );
		}

		if ( get_option( 'elementor_experiment-e_optimized_css_loading' ) ) {
			update_option( 'elementor_experiment-e_optimized_css_loading', 'default' );
		}
	}


	public static function clean_cron_jobs() {
		// Un-schedules all previously-scheduled cron jobs

		wp_clear_scheduled_hook( 'rttpg_daily_scheduled_events' );
	}

	/**
	 * Create cron jobs (clear them first)
	 *
	 * @return void
	 */
	private static function create_cron_jobs() {
		self::clean_cron_jobs();

		if ( ! wp_next_scheduled( 'rttpg_daily_scheduled_events' ) ) {
			$ve          = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
			$expire_time = strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' );
			wp_schedule_event( $expire_time, 'daily', 'rttpg_daily_scheduled_events' );
		}
	}
}
