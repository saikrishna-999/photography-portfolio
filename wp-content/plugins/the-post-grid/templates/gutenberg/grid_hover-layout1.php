<?php
/**
 * Grid Layout Template - 1
 *
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pID     = get_the_ID();
$excerpt = Fns::get_the_excerpt( $pID, $data );
$title   = Fns::get_the_title( $pID, $data );

/**
 * Get post link markup
 * $link_start, $link_end, $readmore_link_start, $readmore_link_end
 */

$post_link = Fns::get_post_link( $pID, $data );
extract( $post_link );

if ( is_array( $data['grid_column'] ) && ! empty( $data['grid_column'] ) ) {
	foreach ( $data['grid_column'] as $key => $value ) {
		if ( $data['grid_column'][ $key ] == '1' ) {
			$data['grid_column'][ $key ] = '12';
		} elseif ( $data['grid_column'][ $key ] == '2' ) {
			$data['grid_column'][ $key ] = '6';
		} elseif ( $data['grid_column'][ $key ] == '3' ) {
			$data['grid_column'][ $key ] = '4';
		} elseif ( $data['grid_column'][ $key ] == '4' ) {
			$data['grid_column'][ $key ] = '3';
		} elseif ( $data['grid_column'][ $key ] == '5' ) {
			$data['grid_column'][ $key ] = '24';
		} elseif ( $data['grid_column'][ $key ] == '6' ) {
			$data['grid_column'][ $key ] = '2';
		}
	}
}

// Grid Column:
$grid_column_desktop = ( isset( $data['grid_column']['lg'] ) && '' != $data['grid_column']['lg'] ) ? $data['grid_column']['lg'] : '4';
$grid_column_tab     = ( isset( $data['grid_column']['md'] ) && '' != $data['grid_column']['md'] ) ? $data['grid_column']['md'] : '6';
$grid_column_mobile  = ( isset( $data['grid_column']['sm'] ) && '' != $data['grid_column']['sm'] ) ? $data['grid_column']['sm'] : '12';
$col_class           = "rt-col-md-{$grid_column_desktop} rt-col-sm-{$grid_column_tab} rt-col-xs-{$grid_column_mobile}";

// Column Dynamic Class
$column_classes = [];

$column_classes[] .= $data['hover_animation'];
$column_classes[] .= 'rt-grid-hover-item rt-grid-item';
if ( 'masonry' == $data['layout_style'] ) {
	$column_classes[] .= 'masonry-grid-item';
}

?>

<div class="<?php echo esc_attr( $col_class . ' ' . implode( ' ', $column_classes ) ); ?>" data-id="<?php echo esc_attr( $pID ); ?>">
	<div class="rt-holder tpg-post-holder">
		<div class="rt-detail rt-el-content-wrapper">
			<?php
			if ( 'show' == $data['show_thumb'] ) :
				$has_thumbnail = has_post_thumbnail() ? 'has-thumbnail' : 'has-no-thumbnail';
				?>
				<div class="rt-img-holder tpg-el-image-wrap <?php echo esc_attr( $has_thumbnail ); ?>">
					<?php Fns::get_post_thumbnail( $pID, $data, $link_start, $link_end ); ?>
				</div>
			<?php endif; ?>

			<div class="grid-hover-content">

				<?php
				if ( 'show' == $data['show_title'] ) {
					Fns::get_el_post_title( $data['title_tag'], $title, $link_start, $link_end, $data );
				}
				?>

				<?php if ( 'show' == $data['show_meta'] ) : ?>
					<div class="post-meta-tags rt-el-post-meta">
						<?php Fns::get_post_meta_html( $pID, $data ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 'show' == $data['show_excerpt'] || 'show' == $data['show_acf'] ) : ?>
					<div class="tpg-excerpt tpg-el-excerpt">
						<?php if ( $excerpt && 'show' == $data['show_excerpt'] ) : ?>
							<div class="tpg-excerpt-inner">
								<?php echo wp_kses_post( $excerpt ); ?>
							</div>
						<?php endif; ?>
						<?php Fns::tpg_get_acf_data_elementor( $data, $pID ); ?>
					</div>
				<?php endif; ?>

				<?php
				if ( rtTPG()->hasPro() && 'show' === $data['show_social_share'] ) {
					Fns::print_html( \RT\ThePostGridPro\Helpers\Functions::rtShare( $pID ) );
				}

				if ( 'show' === $data['show_read_more'] && $data['read_more_label'] ) {
					Fns::get_read_more_button( $data, $readmore_link_start, $readmore_link_end, 'gutenberg' );
				}
				?>
			</div>
		</div>
	</div>
</div>
