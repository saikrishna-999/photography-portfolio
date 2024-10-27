<?php
/**
 * Page Template Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class PageTemplateController {

	/**
	 * PageTemplateController constructor
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_load' ) );

		$post_types = get_post_types();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				add_filter( "theme_{$post_type}_templates", array( $this, 'add_template' ) );
			}
		}
	}


	/**
	 * Load Template from plugin
	 *
	 * @param $template
	 *
	 * @return mixed|string
	 */
	public function template_load( $template ) {
		if ( is_singular() ) {
			$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( $page_template === 'rttpg_full_width' ) {
				$template = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/page-template/template-full-width.php';
			}
			if ( $page_template === 'rttpg_canvas' ) {
				$template = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/page-template/template-canvas.php';
			}
		}

		return $template;
	}


	/**
	 * Add template in dropdown of the editor
	 *
	 * @param $post_templates
	 *
	 * @return mixed
	 */
	public function add_template( $post_templates ) {
		$post_templates['rttpg_full_width'] = __( 'The Post Grid Full Width', 'the-post-grid' );
		$post_templates['rttpg_canvas']     = __( 'The Post Grid Canvas', 'the-post-grid' );

		return $post_templates;
	}
}