<?php

namespace RT\ThePostGrid\Controllers\Blocks;

use RT\ThePostGrid\Controllers\Blocks\BlockController\SectionTitleSettingsStyle;
use RT\ThePostGrid\Helpers\Fns;

class SectionTitle extends BlockBase {

	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		$this->block_type = 'rttpg/tpg-section-title';
	}

	/**
	 * Register Block
	 *
	 * @return void
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			$this->block_type,
			[
				'attributes'      => $this->get_attributes(),
				'render_callback' => [ $this, 'render_block' ],
			]
		);
	}

	/**
	 * Get attributes
	 *
	 * @param bool $default
	 *
	 * @return array
	 */
	public function get_attributes() {

		/**
		 * All Attribute
		 * Content Tab | Settings Tab | Style Tab
		 */

		return SectionTitleSettingsStyle::get_controller();
	}

	/**
	 * @param array $data
	 *
	 * @return false|string
	 */
	public function render_block( $data ) {
		$uniqueId      = isset( $data['uniqueId'] ) ? $data['uniqueId'] : null;
		$uniqueClass   = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamicClass  = $uniqueClass;
		$dynamicClass .= ! empty( $data['section_title_style'] ) ? " section-title-style-{$data['section_title_style']}" : null;
		$dynamicClass .= ! empty( $data['section_title_alignment'] ) ? " section-title-align-{$data['section_title_alignment']}" : null;
		$dynamicClass .= ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';

		ob_start();
		?>
		<div class="<?php echo esc_attr( $dynamicClass ); ?>">
			<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper clearfix">
				<div class='tpg-header-wrapper'>
					<?php Fns::get_section_title( $data ); ?>
				</div>
			</div>
		</div>
		<?php

		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}
}