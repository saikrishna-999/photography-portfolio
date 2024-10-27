<?php

namespace RT\ThePostGrid\Controllers\Blocks;

class RttpgRow {

	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		$this->block_type = 'rttpg/row';
	}

	/**
	 * Register Block
	 * @return void
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			$this->block_type,
			[
//				'attributes'      => $this->get_attributes(),
				'render_callback' => [ $this, 'render_block' ],
			]
		);
	}

	public function render_block( $settings, $content ) {

		return $content;
	}
}
