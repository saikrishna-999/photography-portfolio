<?php
/**
 * Filter Hooks class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers\Hooks;

use Cassandra\Varint;
use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Filter Hooks class.
 *
 * @package RT_TPG
 */
class FilterHooks {
	/**
	 * Class init
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'tpg_author_arg', [ __CLASS__, 'filter_author_args' ], 10 );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );

		$settings = get_option( 'rt_the_post_grid_settings' );

		if ( isset( $settings['show_acf_details'] ) && $settings['show_acf_details'] ) {
			add_filter( 'the_content', [ __CLASS__, 'tpg_acf_content_filter' ] );
		}

		add_filter( 'wp_head', [ __CLASS__, 'set_post_view_count' ], 9999 );
		add_filter( 'body_class', [ __CLASS__, 'body_classes' ] );
		add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );
		add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'tpg_custom_wpkses_post_tags' ], 10, 2 );
		add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'custom_wpkses_post_tags' ], 10, 2 );
	}

	/**
	 * Add body classes
	 *
	 * @param $classes
	 *
	 * @return mixed
	 */
	public static function body_classes( $classes ) {
		global $post;

		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'tpg_my_account' ) ) {
			$classes[] = 'tpg-myaccount-page';
		}

		$icon_font = Fns::tpg_option( 'tpg_icon_font' );
		$classes[] = 'rttpg';
		$classes[] = 'rttpg-' . RT_THE_POST_GRID_VERSION;
		$classes[] = 'radius-frontend rttpg-body-wrap';
		if ( 'fontawesome' !== $icon_font ) {
			$classes[] = 'rttpg-flaticon';
		}

		return $classes;
	}

	/**
	 * Admin body class
	 *
	 * @param string $classes Classes.
	 *
	 * @return string
	 */
	public static function admin_body_class( $classes ) {
		$settings = get_option( 'rt_the_post_grid_settings' );
		global $pagenow;

		if ( isset( $settings['tpg_block_type'] ) && in_array(
				$settings['tpg_block_type'],
				[
					'elementor',
					'shortcode',
				]
			) ) {
			$classes .= ' tpg-block-type-elementor-or-shortcode';
		}

		// Check if the current page is post.php and if the post parameteris set.
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {

			if ( rtTPG()->hasPro() ) {
				$classes .= ' the-post-grid the-post-grid-pro';
			} else {
				$classes .= ' the-post-grid';
			}

			$classes .= ' radius-editor rttpg-body-wrap';
		}

		return $classes;
	}

	/**
	 * @param $tags
	 * @param $context
	 *
	 * @return mixed
	 */
	public static function tpg_custom_wpkses_post_tags( $tags, $context ) {

		if ( 'post' === $context ) {
			$tags['iframe'] = [
				'src'             => true,
				'height'          => true,
				'width'           => true,
				'frameborder'     => true,
				'allowfullscreen' => true,
			];
			$tags['input']  = [
				'type'        => true,
				'class'       => true,
				'placeholder' => true,
				'name'        => true,
			];
			$tags['style']  = [
				'src' => true,
			];
		}

		return $tags;
	}

	/**
	 * Set view count
	 *
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public static function set_post_view_count( $content ) {
		if ( is_single() ) {
			$pId = get_the_ID();
			Fns::update_post_views_count( $pId );
		}

		return $content;
	}

	/**
	 * Filter author args.
	 *
	 * @param array $args Args.
	 *
	 * @return array
	 */
	public static function filter_author_args( $args ) {
		$defaults = [ 'role__in' => [ 'administrator', 'editor', 'author', 'contributor' ] ];

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Add plugin row meta
	 *
	 * @param array $links Links.
	 * @param string $file File.
	 *
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME ) {
			$report_url         = 'https://www.radiustheme.com/contact/';
			$row_meta['issues'] = sprintf(
				'%2$s <a target="_blank" href="%1$s">%3$s</a>',
				esc_url( $report_url ),
				esc_html__( 'Facing issue?', 'the-post-grid' ),
				'<span style="color: red">' . esc_html__( 'Please open a support ticket.', 'the-post-grid' ) . '</span>'
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * ACF content filter
	 *
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public static function tpg_acf_content_filter( $content ) {
		// Check if we're inside the main loop in a post or page.
		if ( is_single() && in_the_loop() && is_main_query() && rtTPG()->hasPro() ) {
			$settings = get_option( rtTPG()->options['settings'] );

			$data = [
				'show_acf'            => isset( $settings['show_acf_details'] ) && $settings['show_acf_details'] ? 'show' : false,
				'cf_group'            => isset( $settings['cf_group_details'] ) ? $settings['cf_group_details'] : [],
				'cf_hide_empty_value' => isset( $settings['cf_hide_empty_value_details'] ) ? $settings['cf_hide_empty_value_details'] : false,
				'cf_show_only_value'  => isset( $settings['cf_show_only_value_details'] ) ? $settings['cf_show_only_value_details'] : false,
				'cf_hide_group_title' => isset( $settings['cf_hide_group_title_details'] ) ? $settings['cf_hide_group_title_details'] : false,
			];

			return $content . Fns::tpg_get_acf_data_elementor( $data, null, false );
		}

		return $content;
	}

	/**
	 * Add exceptions in wp_kses_post tags.
	 *
	 * @param array $tags Allowed tags, attributes, and/or entities.
	 * @param string $context Context to judge allowed tags by. Allowed values are 'post'.
	 *
	 * @return array
	 */
	public static function custom_wpkses_post_tags( $tags, $context ) {
		if ( 'post' === $context ) {
			$tags['iframe'] = [
				'src'             => true,
				'height'          => true,
				'width'           => true,
				'frameborder'     => true,
				'allowfullscreen' => true,
			];

			$tags['svg'] = [
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
				'stroke'          => true,
				'fill'            => true,
			];

			$tags['g']     = [ 'fill' => true ];
			$tags['title'] = [ 'title' => true ];
			$tags['path']  = [
				'd'               => true,
				'fill'            => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-rule'       => true,
				'clip-rule'       => true,
				'stroke'          => true,
			];
		}

		return $tags;
	}
}
