<?php
/**
 * Settings: Shortcode Settings
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

?>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'ShortCode Heading', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCHeadingSettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label">
		<label><?php esc_html_e( 'Category', 'the-post-grid' ); ?></label>
	</div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCCategorySettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'Title', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCTitleSettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'Meta', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCMetaSettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'Image', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCImageSettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'Excerpt', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCExcerptSettings() ), true ); ?>
	</div>
</div>
<div class="field-holder">
	<div class="field-label"><?php esc_html_e( 'Read More Button', 'the-post-grid' ); ?></div>
	<div class="field">
		<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGSCButtonSettings() ), true ); ?>
	</div>
</div>
