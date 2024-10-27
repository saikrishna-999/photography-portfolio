<?php
/**
 * TPG Widget Class
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Widgets;

use RT\ThePostGrid\Helpers\Fns;
use WP_Widget;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * TPG Widget Class
 *
 * @package RT_TPG
 */
class TPGWidget extends WP_Widget {

	public function __construct() {
		$widget_ops = [
			'classname'   => 'widget_tpg_post_grid',
			'description' => esc_html__( 'Display the post grid.', 'the-post-grid' ),
		];

		parent::__construct( 'widget_tpg_post_grid', esc_html__( 'The Post Grid', 'the-post-grid' ), $widget_ops );
	}

	/**
	 * display the widgets on the screen.
	 */
	public function widget( $args, $instance ) {
		$id = ( ! empty( $instance['id'] ) ? absint( $instance['id'] ) : null );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', ( isset( $instance['title'] ) ? $instance['title'] : 'The Post Grid' ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! empty( $id ) ) {
			echo do_shortcode( '[the-post-grid id="' . absint( $id ) . '" ]' );
		}

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function form( $instance ) {

		$scList   = Fns::getAllTPGShortCodeList();
		$defaults = [
			'title' => 'The Post Grid',
			'id'    => null,
		];
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"> <?php esc_html_e( 'Title:', 'the-post-grid' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"
				style="width:100%;"/></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Select post grid', 'the-post-grid' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>">
				<option value="">Select one</option>
				<?php
				if ( ! empty( $scList ) ) {
					foreach ( $scList as $scId => $sc ) {
						$selected = ( $scId == $instance['id'] ? 'selected' : null );
						echo '<option value="' . absint( $scId ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $sc ) . '</option>';
					}
				}
				?>
			</select></p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['id']    = ( ! empty( $new_instance['id'] ) ) ? absint( $new_instance['id'] ) : '';

		return $instance;
	}

}
