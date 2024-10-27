<?php
/**
 * Style
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleFields() ), true );
?>

<div class="field-holder button-color-style-wrapper">
	<div class="field-label"><?php esc_html_e( 'Button Color', 'the-post-grid' ); ?></div>
	<div class="field">
		<div class="tpg-multiple-field-group">
			<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleButtonColorFields() ), true ); ?>
		</div>
	</div>
</div>

<div class="field-holder widget-heading-stle-wrapper">
	<div class="field-label"><?php esc_html_e( 'ShortCode Heading', 'the-post-grid' ); ?></div>
	<div class="field">
		<div class="tpg-multiple-field-group">
			<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleHeading() ), true ); ?>
		</div>
	</div>
</div>

<div class="field-holder full-area-style-wrapper">
	<div class="field-label"><?php esc_html_e( 'Full Area / Section', 'the-post-grid' ); ?></div>
	<div class="field">
		<div class="tpg-multiple-field-group">
			<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleFullArea() ), true ); ?>
		</div>
	</div>
</div>

<?php do_action( 'rt_tpg_sc_style_group_field' ); ?>

<?php Fns::print_html( Fns::rtSmartStyle( Options::extraStyle() ), true ); ?>
