<?php
/**
 * Fields class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Models;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Fields class.
 */
class Field {
	private $type;
	private $name;
	private $value;
	private $default;
	private $label;
	private $id;
	private $class;
	private $holderClass = '';
	private $description;
	private $descriptionAdv;
	private $options;
	private $option;
	private $optionLabel;
	private $attr;
	private $multiple;
	private $alignment;
	private $placeholder;
	private $blank;

	function __construct() {
	}

	private function setArgument( $key, $attr ) {
		global $pagenow;

		$this->type     = isset( $attr['type'] ) ? ( $attr['type'] ? $attr['type'] : 'text' ) : 'text';
		$this->multiple = isset( $attr['multiple'] ) ? ( $attr['multiple'] ? $attr['multiple'] : false ) : false;
		$this->name     = ! empty( $key ) ? $key : null;
		$id             = isset( $attr['id'] ) ? $attr['id'] : null;
		$this->id       = ! empty( $id ) ? $id : $this->name;
		$this->default  = isset( $attr['default'] ) ? $attr['default'] : null;
		$this->value    = isset( $attr['value'] ) ? ( $attr['value'] ? $attr['value'] : null ) : null;

		if ( ! $this->value ) {
			$post_id = get_the_ID();

			if ( ! Fns::meta_exist( $this->name, $post_id ) && $pagenow == 'post-new.php' ) {
				$this->value = $this->default;
			} elseif ( $this->multiple ) {
				if ( metadata_exists( 'post', $post_id, $this->name ) ) {
					$this->value = get_post_meta( $post_id, $this->name );
				} else {
					$this->value = $this->default;
				}
			} elseif ( metadata_exists( 'post', $post_id, $this->name ) ) {
				$this->value = get_post_meta( $post_id, $this->name, true );
			} else {
				$this->value = $this->default;
			}
		}

		$this->label          = isset( $attr['label'] ) ? ( $attr['label'] ? $attr['label'] : null ) : null;
		$this->class          = isset( $attr['class'] ) ? ( $attr['class'] ? $attr['class'] : null ) : null;
		$this->holderClass    = isset( $attr['holderClass'] ) ? ( $attr['holderClass'] ? $attr['holderClass'] : '' ) : '';
		$this->placeholder    = isset( $attr['placeholder'] ) ? ( $attr['placeholder'] ? $attr['placeholder'] : null ) : null;
		$this->description    = isset( $attr['description'] ) ? ( $attr['description'] ? $attr['description'] : null ) : null;
		$this->descriptionAdv = isset( $attr['description_adv'] ) ? ( $attr['description_adv'] ? $attr['description_adv'] : null ) : null;
		$this->options        = isset( $attr['options'] ) ? ( $attr['options'] ? $attr['options'] : [] ) : [];
		$this->option         = isset( $attr['option'] ) ? ( $attr['option'] ? $attr['option'] : null ) : null;
		$this->optionLabel    = isset( $attr['optionLabel'] ) ? ( $attr['optionLabel'] ? $attr['optionLabel'] : null ) : null;
		$this->attr           = isset( $attr['attr'] ) ? ( $attr['attr'] ? $attr['attr'] : null ) : null;
		$this->alignment      = isset( $attr['alignment'] ) ? ( $attr['alignment'] ? $attr['alignment'] : null ) : null;
		$this->blank          = ! empty( $attr['blank'] ) ? $attr['blank'] : null;
	}

	public function Field( $key, $attr = [] ) {
		$this->setArgument( $key, $attr );
		$holderId = $this->name . '_holder';

		if ( ! rtTPG()->hasPro() ) {
			$class = $this->holderClass;
		} else {
			$class = str_replace( 'pro-field', '', $this->holderClass );
		}

		$html = null;

		if ( $this->type == 'hr' ) {
			return "<hr/>";
		}

		$html .= '<div class="field-holder ' . esc_attr( $class ) . '" id="' . esc_attr( $holderId ) . '">';

		$holderClass = explode( ' ', $this->holderClass );

		if ( $this->label ) {
			$html .= "<div class='field-label'>";
			$html .= '<label>' . Fns::htmlKses( $this->label, 'basic' ) . '</label>';

			if ( in_array( 'pro-field', $holderClass, true ) && ! rtTPG()->hasPro() ) {
				$html .= '<span class="rttpg-tooltip">[Pro]<span class="rttpg-tooltip-text">' . esc_html__( 'Premium Option', 'the-post-grid' ) . '</span></span>';
			}

			$html .= '</div>';
		}

		$html .= "<div class='field'>";

		switch ( $this->type ) {
			case 'text':
				$html .= $this->text();
				break;

			case 'url':
				$html .= $this->url();
				break;

			case 'number':
				$html .= $this->number();
				break;

			case 'select':
				$html .= $this->select();
				break;

			case 'textarea':
				$html .= $this->textArea();
				break;

			case 'checkbox':
				$html .= $this->checkbox();
				break;

			case 'switch':
				$html .= $this->switchField();
				break;

			case 'checkboxFilter':
				$html .= $this->checkboxFilter();
				break;

			case 'radio':
				$html .= $this->radioField();
				break;

			case 'radio-image':
				$html .= $this->radioImage();
				break;

			case 'date_range':
				$html .= $this->dateRange();
				break;

			case 'script':
				$html .= $this->script();
				break;

			case 'image':
				$html .= $this->image();
				break;

			case 'image_size':
				$html .= $this->imageSize();
				break;
		}

		if ( $this->description ) {
			$html .= '<p class="description">' . Fns::htmlKses( $this->description, 'basic' ) . '</p>';
		}

		if ( $this->descriptionAdv ) {
			$html .= '<p class="description">' . Fns::htmlKses( $this->descriptionAdv, 'advanced' ) . '</p>';
		}

		$html .= '</div>'; // field.
		$html .= '</div>'; // field holder.

		return $html;
	}


	private function text() {
		$holderClass = explode( ' ', $this->holderClass );

		$h = null;
		$h .= '<input
				type="text"
				class="' . esc_attr( $this->class ) . '"
				id="' . esc_attr( $this->id ) . '"
				value="' . esc_attr( $this->value ) . '"
				name="' . esc_attr( $this->name ) . '"
				placeholder="' . esc_attr( $this->placeholder ) . '"
				' . Fns::htmlKses( $this->attr, 'basic' ) . '
				/>';

		return $h;
	}

	private function script() {
		$type = 'script';

		if ( $this->id == 'custom-css' ) {
			$type = 'css';
		}

		$h = null;
		$h .= '<div class="rt-script-wrapper" data-type="' . esc_attr( $type ) . '">';
		$h .= '<div class="rt-script-container">';
		$h .= "<div name='" . esc_attr( $this->name ) . "' id='ret-" . absint( wp_rand() ) . "' class='rt-script'>";
		$h .= '</div>';
		$h .= '</div>';

		$h .= '<textarea
				style="display: none;"
				class="rt-script-textarea"
				id="' . esc_attr( $this->id ) . '"
				name="' . esc_attr( $this->name ) . '"
				>' . wp_strip_all_tags( $this->value ) . '</textarea>';
		$h .= '</div>';

		return $h;
	}

	private function url() {
		$h = null;
		$h .= '<input
				type="url"
				class="' . esc_attr( $this->class ) . '"
				id="' . esc_attr( $this->id ) . '"
				value="' . esc_url( $this->value ) . '"
				name="' . esc_attr( $this->name ) . '"
				placeholder="' . esc_attr( $this->placeholder ) . '"
				' . Fns::htmlKses( $this->attr, 'basic' ) . '
				/>';

		return $h;
	}

	private function number() {
		$holderClass = explode( ' ', $this->holderClass );

		$h = null;
		$h .= '<input
				type="number"
				class="' . esc_attr( $this->class ) . '"
				id="' . esc_attr( $this->id ) . '"
				value="' . ( ! empty( $this->value ) ? esc_html( $this->value ) : null ) . '"
				name="' . esc_attr( $this->name ) . '"
				placeholder="' . esc_attr( $this->placeholder ) . '"
				' . Fns::htmlKses( $this->attr, 'basic' ) . '
				/>';

		return $h;
	}

	private function select() {
		$holderClass = explode( ' ', $this->holderClass );
		$atts        = ( in_array( 'pro-field', $holderClass, true ) ) && ! rtTPG()->hasPro() ? 'disabled="true"' : '';
		$h           = null;

		if ( $this->multiple ) {
			$this->attr  = " style='min-width:160px;'";
			$this->name  = $this->name . '[]';
			$this->attr  = $this->attr . " multiple='multiple'";
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : [] );
		} else {
			$this->value = [ $this->value ];
		}

		$h .= '<select ' . $atts . ' name="' . esc_attr( $this->name ) . '" id="' . esc_attr( $this->id ) . '" class="' . esc_attr( $this->class ) . '" ' . Fns::htmlKses( $this->attr, 'basic' ) . '>';

		if ( $this->blank ) {
			$h .= '<option value="">' . esc_html( $this->blank ) . '</option>';
		}

		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			foreach ( $this->options as $key => $value ) {
				$slt = ( in_array( $key, $this->value ) ? 'selected' : null );
				$h   .= '<option ' . esc_attr( $slt ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			}
		}

		$h .= '</select>';

		return $h;
	}

	private function textArea() {
		$holderClass = explode( ' ', $this->holderClass );

		$h = null;
		$h .= '<textarea
				class="' . esc_attr( $this->class ) . ' rt-textarea"
				id="' . esc_attr( $this->id ) . '"
				name="' . esc_attr( $this->name ) . '"
				placeholder="' . esc_attr( $this->placeholder ) . '"
				' . Fns::htmlKses( $this->attr, 'basic' ) . '
				>' . wp_kses_post( $this->value ) . '</textarea>';

		return $h;
	}

	private function image() {
		$holderClass = explode( ' ', $this->holderClass );

		$h   = null;
		$img = null;

		$h .= "<div class='rt-image-holder'>";
		$h .= '<input type="hidden" name="' . esc_attr( $this->name ) . '" value="' . absint( $this->value ) . '" id="' . esc_attr( $this->id ) . '" class="hidden-image-id" />';
		$c = 'hidden';

		if ( $id = absint( $this->value ) ) {
			$aImg = wp_get_attachment_image_src( $id, 'thumbnail' );
			$img  = '<img src="' . esc_url( $aImg[0] ) . '" >';
			$c    = null;
		}

		$h .= '<div class="rt-image-preview">' . Fns::htmlKses( $img, 'image' ) . '<span class="dashicons dashicons-plus-alt rtAddImage"></span><span class="dashicons dashicons-trash rtRemoveImage ' . esc_attr( $c ) . '"></span></div>';

		$h .= '</div>';

		return $h;
	}

	private function imageSize() {
		$width  = ( ! empty( $this->value[0] ) ? $this->value[0] : null );
		$height = ( ! empty( $this->value[1] ) ? $this->value[1] : null );
		$cropV  = ( ! empty( $this->value[2] ) ? $this->value[2] : 'soft' );

		$h = null;
		$h .= "<div class='rt-image-size-holder'>";
		$h .= "<div class='rt-image-size-width rt-image-size'>";
		$h .= '<label>Width</label>';
		$h .= '<input type="number" name="' . esc_attr( $this->name ) . '[]" value="' . absint( $width ) . '" />';
		$h .= '</div>';
		$h .= "<div class='rt-image-size-height rt-image-size'>";
		$h .= '<label>Height</label>';
		$h .= '<input type="number" name="' . esc_attr( $this->name ) . '[]" value="' . absint( $height ) . '" />';
		$h .= '</div>';
		$h .= "<div class='rt-image-size-crop rt-image-size'>";
		$h .= '<label>Crop</label>';
		$h .= '<select name="' . esc_attr( $this->name ) . '[]" class="rt-select2">';

		$cropList = Options::imageCropType();

		foreach ( $cropList as $crop => $cropLabel ) {
			$cSl = ( $crop == $cropV ? 'selected' : null );
			$h   .= '<option value="' . esc_attr( $crop ) . '" ' . esc_attr( $cSl ) . '>' . esc_html( $cropLabel ) . '</option>';
		}

		$h .= '</select>';
		$h .= '</div>';
		$h .= '</div>';

		return $h;
	}

	private function checkbox() {
		$holderClass     = explode( ' ', $this->holderClass );
		$this->alignment .= ( in_array( 'pro-field', $holderClass, true ) ) && ! rtTPG()->hasPro() ? ' disabled' : '';
		$h               = null;

		if ( $this->multiple ) {
			$this->name  = $this->name . '[]';
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : [] );
		}

		if ( $this->multiple ) {
			$h .= '<div class="checkbox-group ' . esc_attr( $this->alignment ) . '" id="' . esc_attr( $this->id ) . '">';

			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( in_array( $key, $this->value ) ? 'checked' : null );

					$h .= '<label for="' . esc_attr( $this->id ) . '-' . esc_attr( $key ) . '"><input type="checkbox" id="' . esc_attr( $this->id ) . '-' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</label>';
				}
			}

			$h .= '</div>';
		} else {
			$checked = ( $this->value ? 'checked' : null );
			$h       .= '<label><input type="checkbox" ' . esc_attr( $checked ) . ' id="' . esc_attr( $this->id ) . '" name="' . esc_attr( $this->name ) . '" value="1" />' . esc_html( $this->option ) . '</label>';
		}

		return $h;
	}

	private function switchField() {
		$h       = null;
		$checked = $this->value ? 'checked' : null;
		$h       .= '<label class="rttm-switch"><input type="checkbox" ' . esc_attr( $checked ) . ' id="' . esc_attr( $this->id ) . '" name="' . esc_attr( $this->name ) . '" value="1" /><span class="rttm-switch-slider round"></span></label>';

		return $h;
	}

	private function checkboxFilter() {
		global $post;

		$pt          = get_post_meta( $post->ID, 'tpg_post_type', true );
		$advFilters  = Options::rtTPAdvanceFilters();
		$holderClass = explode( ' ', $this->holderClass );
		$h           = null;

		if ( $this->multiple ) {
			$this->name  = $this->name . '[]';
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : [] );
		}

		if ( $this->multiple ) {
			$h .= '<div class="checkbox-group ' . esc_attr( $this->alignment ) . '" id="' . esc_attr( $this->id ) . '">';

			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( in_array( $key, $this->value ) ? 'checked' : null );

					$h .= '<div class="checkbox-filter-field">';
					$h .= '<label for="' . esc_attr( $this->id ) . '-' . esc_attr( $key ) . '"><input type="checkbox" id="' . esc_attr( $this->id ) . '-' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</label>';

					if ( $key == 'tpg_taxonomy' ) {
						$h .= "<div class='rt-tpg-filter taxonomy tpg_taxonomy tpg-hidden'>";

						if ( isset( $pt ) && $pt ) {
							$taxonomies  = Fns::rt_get_all_taxonomy_by_post_type( $pt );
							$taxA        = get_post_meta( $post->ID, 'tpg_taxonomy' );
							$post_filter = get_post_meta( $post->ID, 'post_filter' );

							$h .= "<div class='taxonomy-field'>";

							if ( is_array( $post_filter ) && ! empty( $post_filter ) && in_array( 'tpg_taxonomy', $post_filter ) && ! empty( $taxonomies ) ) {
								$h .= Fns::rtFieldGenerator(
									[
										'tpg_taxonomy' => [
											'type'     => 'checkbox',
											'label'    => '',
											'id'       => 'post-taxonomy',
											'multiple' => true,
											'options'  => $taxonomies,
										],
									]
								);
							} else {
								$h .= '<div class="field-holder">' . esc_html__( 'No Taxonomy found', 'the-post-grid' ) . '</div>';
							}

							$h .= '</div>';
							$h .= "<div class='rt-tpg-filter-item term-filter-item tpg-hidden'>";
							$h .= '<div class="field-holder">';
							$h .= '<div class="field-label">Terms</div>';
							$h .= '<div class="field term-filter-holder">';

							if ( is_array( $taxA ) && ! empty( $taxA ) ) {
								foreach ( $taxA as $tax ) {

									$h .= '<div class="term-filter-item-container ' . esc_attr( $tax ) . '">';
									$h .= Fns::rtFieldGenerator(
										[
											'term_' . $tax => [
												'type'        => 'select',
												'label'       => ucfirst( str_replace( '_', ' ', $tax ) ),
												'class'       => 'rt-select2 full',
												'holderClass' => "term-filter-item {$tax}",
												'value'       => get_post_meta( $post->ID, 'term_' . $tax ),
												'multiple'    => true,
												'options'     => Fns::rt_get_all_term_by_taxonomy( $tax ),
											],
										]
									);
									$h .= Fns::rtFieldGenerator(
										[
											'term_operator_' . $tax => [
												'type'        => 'select',
												'label'       => esc_html__( 'Operator', 'the-post-grid' ),
												'class'       => 'rt-select2 full',
												'holderClass' => "term-filter-item-operator {$tax}",
												'value'       => get_post_meta( $post->ID, 'term_operator_' . $tax, true ),
												'options'     => Options::rtTermOperators(),
											],
										]
									);
									$h .= '</div>';
								}
							}

							$h .= '</div>';
							$h .= '</div>';

							$h .= Fns::rtFieldGenerator(
								[
									'taxonomy_relation' => [
										'type'        => 'select',
										'label'       => esc_html__( 'Relation', 'the-post-grid' ),
										'class'       => 'rt-select2',
										'holderClass' => 'term-filter-item-relation ' . ( count( $taxA ) > 1 ? null : 'hidden' ),
										'value'       => get_post_meta( $post->ID, 'taxonomy_relation', true ),
										'options'     => Options::rtTermRelations(),
									],
								]
							);

							$h .= '</div>';
						} else {
							$h .= "<div class='taxonomy-field'>";
							$h .= '</div>';
							$h .= "<div class='rt-tpg-filter-item'>";
							$h .= '<div class="field-holder">';
							$h .= '<div class="field-label">Terms</div>';
							$h .= '<div class="field term-filter-holder">';
							$h .= '</div>';
							$h .= '</div>';
							$h .= '</div>';
							$h .= Fns::rtFieldGenerator(
								[
									'taxonomy_relation' => [
										'type'        => 'select',
										'label'       => esc_html__( 'Relation', 'the-post-grid' ),
										'class'       => 'rt-select2',
										'holderClass' => 'term-filter-item-relation tpg-hidden',
										'default'     => 'OR',
										'options'     => Options::rtTermRelations(),
									],
								]
							);
						}

						$h .= '</div>';
					} elseif ( $key == 'order' ) {
						$h .= '<div class="rt-tpg-filter ' . esc_attr( $key ) . ' tpg-hidden">';
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= "<div class='field-holder'>";
						$h .= "<div class='field'>";
						$h .= Fns::rtFieldGenerator(
							[
								'order_by' => [
									'type'        => 'select',
									'label'       => esc_html__( 'Order by', 'the-post-grid' ),
									'class'       => 'rt-select2 filter-item',
									'value'       => get_post_meta( $post->ID, 'order_by', true ),
									'options'     => Options::rtPostOrderBy( false, true ),
									'description' => esc_html__( 'If "Meta value", "Meta value Number" or "Meta value datetime" is chosen then meta key is required.', 'the-post-grid' ),
								],
							]
						);
						$h .= Fns::rtFieldGenerator(
							[
								'tpg_meta_key' => [
									'type'        => 'text',
									'label'       => esc_html__( 'Meta key', 'the-post-grid' ),
									'class'       => 'rt-select2 filter-item',
									'holderClass' => 'tpg-hidden',
									'value'       => get_post_meta( $post->ID, 'tpg_meta_key', true ),
								],
							]
						);
						$h .= Fns::rtFieldGenerator(
							[
								'order' => [
									'type'      => 'radio',
									'label'     => esc_html__( 'Order', 'the-post-grid' ),
									'class'     => 'rt-select2 filter-item',
									'alignment' => 'vertical',
									'default'   => 'DESC',
									'value'     => get_post_meta( $post->ID, 'order', true ),
									'options'   => Options::rtPostOrders(),
								],
							]
						);
						$h .= '</div>';
						$h .= '</div>';
						$h .= '</div>';
						$h .= '</div>';
					} elseif ( $key == 'author' ) {
						$h .= '<div class="rt-tpg-filter ' . esc_attr( $key ) . ' tpg-hidden">';
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							[
								$key => [
									'type'     => 'select',
									'label'    => '',
									'class'    => 'rt-select2 filter-item full',
									'value'    => get_post_meta( $post->ID, $key ),
									'multiple' => true,
									'options'  => Fns::rt_get_users(),
								],
							]
						);
						$h .= '</div>';
						$h .= '</div>';
					} elseif ( $key == 's' ) {
						$h .= '<div class="rt-tpg-filter ' . esc_attr( $key ) . ' tpg-hidden">';
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							[
								$key => [
									'type'  => 'text',
									'label' => esc_html__( 'Keyword', 'the-post-grid' ),
									'class' => 'filter-item full',
									'value' => get_post_meta( $post->ID, $key, true ),
								],
							]
						);
						$h .= '</div>';
						$h .= '</div>';
					} elseif ( $key == 'date_range' ) {
						$range_start = get_post_meta( $post->ID, 'date_range_start', true );
						$range_end   = get_post_meta( $post->ID, 'date_range_end', true );
						$range_value = [
							'start' => $range_start,
							'end'   => $range_end,
						];
						$h           .= '<div class="rt-tpg-filter ' . esc_attr( $key ) . ' tpg-hidden">';
						$h           .= "<div class='rt-tpg-filter-item'>";
						$h           .= Fns::rtFieldGenerator(
							[
								$key => [
									'type'        => 'date_range',
									'label'       => '',
									'class'       => 'filter-item full rt-date-range',
									'value'       => $range_value,
									'description' => "Date format should be 'yyyy-mm-dd'",
								],
							]
						);
						$h           .= '</div>';
						$h           .= '</div>';
					}
					/* Post status has removed
					 * elseif ( $key == 'tpg_post_status' ) {
											$h .= '<div class="rt-tpg-filter ' . esc_attr( $key ) . ' tpg-hidden">';
											$h .= "<div class='rt-tpg-filter-item'>";
											$h .= Fns::rtFieldGenerator(
												array(
													$key => array(
														'type'     => 'select',
														'label'    => '',
														'class'    => 'rt-select2 filter-item full',
														'default'  => array( 'publish' ),
														'value'    => get_post_meta( $post->ID, $key ),
														'multiple' => true,
														'options'  => Options::rtTPGPostStatus(),
													),
												)
											);
											$h .= '</div>';
											$h .= '</div>';
					}*/
					// }

					$h .= '</div>';
				}
			}
			$h .= '</div>';
		} else {
			$checked = ( $this->value ? 'checked' : null );
			$h       .= '<label><input type="checkbox" ' . esc_attr( $checked ) . ' id="' . esc_attr( $this->id ) . '" name="' . esc_attr( $this->name ) . '" value="1" />' . esc_html( $this->option ) . '</label>';
		}

		return $h;
	}

	private function radioField() {
		$holderClass     = explode( ' ', $this->holderClass );
		$this->alignment .= ( in_array( 'pro-field', $holderClass, true ) ) && ! rtTPG()->hasPro() ? ' disabled' : '';
		$h               = null;

		$h .= '<div class="radio-group ' . esc_attr( $this->alignment ) . '" id="' . esc_attr( $this->id ) . '">';

		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			foreach ( $this->options as $key => $value ) {
				$checked = ( $key == $this->value ? 'checked' : null );

				$h .= '<label for="' . esc_attr( $this->name ) . '-' . esc_attr( $key ) . '"><input type="radio" id="' . esc_attr( $this->id ) . '-' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</label>';
			}
		}

		$h .= '</div>';

		return $h;
	}

	/**
	 * Radio Image
	 *
	 * @return String
	 */
	private function radioImage() {
		$h  = null;
		$id = 'rttpg-' . $this->name;

		$h              .= sprintf( "<div class='rttpg-radio-image %s' id='%s'>", esc_attr( $this->alignment ), esc_attr( $id ) );
		$selected_value = $this->value;

		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			foreach ( $this->options as $key => $value ) {
				$checked  = ( $key == $selected_value ? 'checked' : null );
				$title    = isset( $value['title'] ) && $value['title'] ? $value['title'] : '';
				$link     = isset( $value['layout_link'] ) && $value['layout_link'] ? $value['layout_link'] : '';
				$linkHtml = empty( $link ) ? esc_html( $title ) : '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $title ) . '</a>';
				$layout   = isset( $value['layout'] ) ? $value['layout'] : '';
				$taghtml  = isset( $value['tag'] ) ? '<div class="rt-tpg-layout-tag"><span>' . $value['tag'] . '</span></div>' : '';
				$h        .= sprintf(
					'<div class="rt-tpg-radio-layout %7$s"><label data-type="%7$s" class="radio-image %7$s"  for="%2$s">
						<input type="radio" id="%2$s" %3$s name="%4$s" value="%2$s">
						<div class="rttpg-radio-image-wrap">
						<img src="%5$s" title="%6$s" alt="%2$s">
						<div class="rttpg-checked"><span class="dashicons dashicons-yes"></span></div>
						%9$s
						</div>
						</label>
						<div class="rttpg-demo-name">%8$s</div>
					</div>',
					'',
					esc_attr( $key ),
					esc_attr( $checked ),
					esc_attr( $this->name ),
					esc_url( $value['img'] ),
					esc_attr( $title ),
					esc_attr( $layout ),
					Fns::htmlKses( $linkHtml, 'basic' ),
					Fns::htmlKses( $taghtml, 'basic' )
				);
			}
		}
		$h .= '</div>';

		return $h;
	}

	private function dateRange() {
		$h          = null;
		$this->name = ( $this->name ? $this->name : 'date-range-' . wp_rand( 0, 1000 ) );
		$h          .= '<div class="date-range-container" id="' . esc_attr( $this->id ) . '">';
		$h          .= "<div class='date-range-content start'><span>" . esc_html__( 'Start', 'the-post-grid' ) . "</span><input
						type='text'
						class='date-range date-range-start {$this->class}'
						id='" . esc_attr( $this->id ) . "-start'
						value='" . esc_attr( $this->value['start'] ) . "'
						name='" . esc_attr( $this->name ) . "_start'
						placeholder='" . esc_attr( $this->name ) . "'
						" . Fns::htmlKses( $this->attr, 'basic' ) . '
						/></div>';
		$h          .= "<div class='date-range-content end'><span>" . esc_html__( 'End', 'the-post-grid' ) . "</span><input
						type='text'
						class='date-range date-range-end {$this->class}'
						id='" . esc_attr( $this->id ) . "-end'
						value='" . esc_attr( $this->value['end'] ) . "'
						name='" . esc_attr( $this->name ) . "_end'
						placeholder='" . esc_attr( $this->name ) . "'
						" . Fns::htmlKses( $this->attr, 'basic' ) . '
						/></div>';
		$h          .= '</div>';

		return $h;
	}
}
