<?php
/**
 * Elementor Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class GutenBergController {
	function __construct() {
		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );
		if ( function_exists( 'register_block_type' ) ) {
			register_block_type(
				'rttpg/post-grid',
				array(
					'render_callback' => array( $this, 'render_shortcode' ),
				)
			);
		}
	}

	static function render_shortcode( $atts ) {
		if ( ! empty( $atts['gridId'] ) && $id = absint( $atts['gridId'] ) ) {
			// return do_shortcode( '[the-post-grid id="' . $id . '"]' );
			ob_start();
			echo do_shortcode( '[the-post-grid id="' . $id . '"]' );
			return ob_get_clean();
		}
	}


	function block_assets() {
		wp_enqueue_style( 'wp-blocks' );
	}

	function block_editor_assets() {
		// Scripts.
		wp_enqueue_script(
			'rt-tpg-cgb-block-js',
			rtTPG()->get_assets_uri( 'js/post-grid-blocks.js' ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RT_THE_POST_GRID_VERSION,
			true
		);
		wp_localize_script(
			'rt-tpg-cgb-block-js',
			'rttpgGB',
			array(
				'short_codes' => Fns::getAllTPGShortCodeList(),
				'icon'        => rtTPG()->get_assets_uri( 'images/icon-16x16.png' ),
			)
		);
		wp_enqueue_style( 'wp-edit-blocks' );
	}
}
