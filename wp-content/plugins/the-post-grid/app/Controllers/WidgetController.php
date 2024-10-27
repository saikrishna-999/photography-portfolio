<?php
/**
 * Widget Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Widgets\TPGWidget;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Widget Controller class.
 */
class WidgetController {
	/**
	 * Class construct
	 */
	public function __construct() {
		add_action( 'widgets_init', [ $this, 'initWidget' ] );
	}

	/**
	 * Widgets
	 *
	 * @return void
	 */
	public function initWidget() {
		register_widget( TPGWidget::class );
	}
}
