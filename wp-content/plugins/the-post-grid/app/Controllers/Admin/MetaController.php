<?php
/**
 * Meta Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers\Admin;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

//phpcs:disable WordPress.Security.NonceVerification.Recommended

/**
 * Meta Controller class.
 */
class MetaController {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'edit_form_after_title', [ $this, 'tpg_sc_after_title' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
		add_filter( 'manage_edit-rttpg_columns', [ $this, 'arrange_rttpg_columns' ] );
		add_action( 'manage_rttpg_posts_custom_column', [ $this, 'manage_rttpg_columns' ], 10, 2 );
		add_action( 'created_term', [ $this, 'save_taxonomy_fields' ], 10, 3 );
	}

	/**
	 * manage Column
	 *
	 * @param string $column Column.
	 *
	 * @return void
	 */
	public function manage_rttpg_columns( $column ) {
		switch ( $column ) {
			case 'shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[the-post-grid id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			default:
				break;
		}
	}

	/**
	 * Arrange Columns
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	public function arrange_rttpg_columns( $columns ) {
		$shortcode = [ 'shortcode' => esc_html__( 'Shortcode', 'the-post-grid' ) ];

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}

	/**
	 * Admin Scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $pagenow, $typenow;

		if ( 'tpg_builder' === $typenow ) {
			wp_enqueue_style( 'rt-tpg-admin' );
		}

		if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}

		if ( rtTPG()->post_type !== $typenow ) {
			return;
		}

		wp_dequeue_script( 'autosave' );
		wp_enqueue_media();

		$select2Id = 'rt-select2';
		if ( class_exists( 'Avada' ) ) {
			$select2Id = 'select2-avada-js';
		}

		// scripts.
		wp_enqueue_script(
			[
				'jquery',
				'jquery-ui-datepicker',
				'wp-color-picker',
				$select2Id,
				'imagesloaded',
				'rt-isotope-js',
				'rt-tpg-admin',
				'rt-tpg-admin-preview',
			]
		);

		// styles.
		wp_enqueue_style(
			[
				'wp-color-picker',
				'rt-select2',
				'rt-fontawsome',
				'rt-flaticon',
				'rt-tpg-admin',
				'rt-tpg-admin-preview',
			]
		);

		wp_localize_script(
			'rt-tpg-admin',
			'rttpg',
			[
				'nonceID' => esc_attr( rtTPG()->nonceId() ),
				'nonce'   => esc_attr( wp_create_nonce( rtTPG()->nonceText() ) ),
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'uid'     => get_current_user_id(),
			]
		);
	}

	/**
	 * Add Metabox.
	 *
	 * @return void
	 */
	public function admin_head() {
		add_meta_box(
			'rttpg_meta',
			esc_html__( 'Short Code Generator', 'the-post-grid' ),
			[ $this, 'rttpg_meta_settings_selection' ],
			rtTPG()->post_type,
			'normal',
			'high'
		);

		add_meta_box(
			rtTPG()->post_type . '_sc_preview_meta',
			esc_html__( 'Layout Preview', 'the-post-grid' ),
			[ $this, 'tpg_sc_preview_selection' ],
			rtTPG()->post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'rt_plugin_sc_pro_information',
			esc_html__( 'Documentation', 'the-post-grid' ),
			[ $this, 'rt_plugin_sc_pro_information' ],
			rtTPG()->post_type,
			'side',
			'low'
		);
	}

	/**
	 * Marketing.
	 *
	 * @param string $post Post.
	 *
	 * @return void
	 */
	public function rt_plugin_sc_pro_information( $post ) {
		$html = '';

		if ( 'settings' === $post ) {
			$html .= '<div class="rt-document-box rt-update-pro-btn-wrap">
						<a href="' . esc_url( rtTpg()->proLink() ) . '" target="_blank" class="rt-update-pro-btn">' . esc_html__( 'Update Pro To Get More Features', 'the-post-grid' ) . '</a>
					</div>';
		} else {
			if ( ! rtTPG()->hasPro() ) {
				$html .= sprintf(
					'<div class="rt-document-box"><div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div><div class="rt-box-content"><h3 class="rt-box-title">%1$s</h3>%2$s</div></div>',
					esc_html__( 'Pro Features', 'the-post-grid' ),
					Options::get_pro_feature_list()
				);
			}
		}

		$html .= sprintf(
			'<div class="rt-document-box">
				<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
				<div class="rt-box-content">
					<h3 class="rt-box-title">%1$s</h3>
					<p>%2$s</p>
					<a href="' . esc_url( rtTpg()->docLink() ) . '" target="_blank" class="rt-admin-btn">%1$s</a>
				</div>
			</div>',
			esc_html__( 'Documentation', 'the-post-grid' ),
			esc_html__( 'Get started by spending some time with the documentation we included step by step process with screenshots with video.', 'the-post-grid' )
		);

		$rtContact = 'https://www.radiustheme.com/contact/';
		$rtFb      = 'https://www.facebook.com/groups/234799147426640/';
		$rtsite    = 'https://www.radiustheme.com/';

		$html .= '<div class="rt-document-box">
						<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
						<div class="rt-box-content">
							<h3 class="rt-box-title">Need Help?</h3>
							<p>Stuck with something? Please create a
							<a href="' . esc_url( $rtContact ) . '">ticket here</a> or post on <a href="' . esc_url( $rtFb ) . '">facebook group</a>. For emergency case join our <a href="' . esc_url( $rtsite ) . '">live chat</a>.</p>
							<a href="' . esc_url( $rtContact ) . '" target="_blank" class="rt-admin-btn">' . esc_html__( 'Get Support', 'the-post-grid' ) . '</a>
						</div>
					</div>';

		Fns::print_html( $html );
	}

	/**
	 * Preview
	 *
	 * @return void
	 */
	public function tpg_sc_preview_selection() {
		$html = null;
		$html .= "<div class='rt-response'></div>";
		$html .= "<div id='tpg-preview-container'></div>";

		Fns::print_html( $html, true );
	}

	/**
	 * Text after title
	 *
	 * @param object $post Post object.
	 *
	 * @return void
	 */
	public function tpg_sc_after_title( $post ) {
		if ( rtTPG()->post_type !== $post->post_type ) {
			return;
		}

		$html = null;
		$html .= '<div class="postbox rt-after-title" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p>
					<input type="text" onfocus="this.select();" readonly="readonly" value="[the-post-grid id=&quot;' . absint( $post->ID ) . '&quot; title=&quot;' . esc_attr( $post->post_title ) . '&quot;]" class="large-text code rt-code-sc">
					<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[the-post-grid id=&quot;' . absint( $post->ID ) . '&quot; title=&quot;' . esc_attr( $post->post_title ) . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
				</p>';
		$html .= '</div></div>';

		Fns::print_html( $html, true );
	}

	/**
	 * Meta settings
	 *
	 * @param object $post Post object.
	 *
	 * @return void
	 */
	public function rttpg_meta_settings_selection( $post ) {
		$last_tab = trim( get_post_meta( $post->ID, '_tpg_last_active_tab', true ) );
		$last_tab = $last_tab ? $last_tab : 'sc-post-post-source';
		$post     = [
			'post' => $post,
		];

		wp_nonce_field( rtTPG()->nonceText(), rtTPG()->nonceId() );

		$html = null;
		$html .= '<div id="sc-tabs" class="rttpg-wrapper rt-tab-container rt-setting-holder">';
		$html .= sprintf(
			'<ul class="rt-tab-nav">
				<li%s><a href="#sc-post-post-source">%s</a></li>
				<li%s><a href="#sc-post-layout-settings">%s</a></li>
				<li%s><a href="#sc-settings">%s</a></li>
				<li%s><a href="#sc-field-selection">%s</a></li>
				<li%s><a href="#sc-style">%s</a></li>
			</ul>',
			'sc-post-post-source' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Query Build', 'the-post-grid' ),
			'sc-post-layout-settings' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Layout Settings', 'the-post-grid' ),
			'sc-settings' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Settings', 'the-post-grid' ),
			'sc-field-selection' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Field Selection', 'the-post-grid' ),
			'sc-style' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Style', 'the-post-grid' )
		);

		// Query Build tab.
		$html .= sprintf( '<div id="sc-post-post-source" class="rt-tab-content"%s>', 'sc-post-post-source' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.post-source', $post, true );
		$html .= '</div>';

		// Layout Setting tab.
		$html .= sprintf( '<div id="sc-post-layout-settings" class="rt-tab-content"%s>', 'sc-post-layout-settings' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.layout-settings', $post, true );
		$html .= '</div>';

		// Settings tab.
		$html .= sprintf( '<div id="sc-settings" class="rt-tab-content"%s>', 'sc-settings' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.sc-settings', $post, true );
		$html .= '</div>';

		// Field Selection tab.
		$html .= sprintf( '<div id="sc-field-selection" class="rt-tab-content"%s>', 'sc-field-selection' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.item-fields', $post, true );
		$html .= '</div>';

		// Style tab.
		$html .= sprintf( '<div id="sc-style" class="rt-tab-content"%s>', 'sc-style' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::view( 'settings.style', $post, true );
		$html .= '</div>';
		$html .= sprintf( '<input type="hidden" id="_tpg_last_active_tab" name="_tpg_last_active_tab"  value="%s"/>', $last_tab );
		$html .= '</div>';

		Fns::print_html( $html, true );
	}

	/**
	 * Save meta box.
	 *
	 * @param int $post_id Post ID.
	 * @param object $post Post object.
	 *
	 * @return mixed
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( ! Fns::verifyNonce() ) {
			return $post_id;
		}

		if ( rtTPG()->post_type !== $post->post_type ) {
			return $post_id;
		}

		$mates = Fns::rtAllOptionFields();

		foreach ( $mates as $metaKey => $field ) {
			$rValue = ! empty( $_REQUEST[ $metaKey ] ) ? $_REQUEST[ $metaKey ] : null; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$value  = Fns::sanitize( $field, $rValue );

			if ( empty( $field['multiple'] ) ) {
				update_post_meta( $post_id, $metaKey, $value );
			} else {
				delete_post_meta( $post_id, $metaKey );
				if ( is_array( $value ) && ! empty( $value ) ) {
					foreach ( $value as $item ) {
						add_post_meta( $post_id, $metaKey, $item );
					}
				}
			}
		}

		$post_filter = ( isset( $_REQUEST['post_filter'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['post_filter'] ) ) : [] );
		$advFilter   = Options::rtTPAdvanceFilters();

		foreach ( $advFilter['post_filter']['options'] as $filter => $fValue ) {
			if ( $filter == 'tpg_taxonomy' ) {
				delete_post_meta( $post_id, $filter );

				if ( ! empty( $_REQUEST[ $filter ] ) && is_array( $_REQUEST[ $filter ] ) ) {
					foreach ( $_REQUEST[ $filter ] as $tax ) {
						if ( in_array( $filter, $post_filter ) ) {
							add_post_meta( $post_id, $filter, trim( $tax ) );
						}

						delete_post_meta( $post_id, 'term_' . $tax );

						$tt = isset( $_REQUEST[ 'term_' . $tax ] ) ? $_REQUEST[ 'term_' . $tax ] : [];

						if ( is_array( $tt ) && ! empty( $tt ) && in_array( $filter, $post_filter ) ) {
							foreach ( $tt as $termID ) {
								add_post_meta( $post_id, 'term_' . $tax, trim( $termID ) );
							}
						}

						$tto = isset( $_REQUEST[ 'term_operator_' . $tax ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'term_operator_' . $tax ] ) ) : null;

						if ( $tto ) {
							update_post_meta( $post_id, 'term_operator_' . $tax, trim( $tto ) );
						}
					}

					$filterCount = isset( $_REQUEST[ $filter ] ) ? $_REQUEST[ $filter ] : [];
					$tr          = isset( $_REQUEST['taxonomy_relation'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['taxonomy_relation'] ) ) : null;

					if ( count( $filterCount ) > 1 && $tr ) {
						update_post_meta( $post_id, 'taxonomy_relation', trim( $tr ) );
					} else {
						delete_post_meta( $post_id, 'taxonomy_relation' );
					}
				}
			} elseif ( $filter == 'author' ) {
				delete_post_meta( $post_id, 'author' );

				$authors = ( isset( $_REQUEST['author'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['author'] ) ) : [] );

				if ( is_array( $authors ) && ! empty( $authors ) && in_array( 'author', $post_filter ) ) {
					foreach ( $authors as $authorID ) {
						add_post_meta( $post_id, 'author', trim( $authorID ) );
					}
				}
			} elseif ( $filter == 's' ) {
				delete_post_meta( $post_id, 's' );

				$s = ( isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : null );

				if ( $s && in_array( 's', $post_filter ) ) {
					update_post_meta( $post_id, 's', sanitize_text_field( trim( $s ) ) );
				}
			} elseif ( $filter == 'order' ) {
				if ( in_array( 'order', $post_filter ) ) {
					$order        = ( isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : null );
					$order_by     = ( isset( $_REQUEST['order_by'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order_by'] ) ) : null );
					$tpg_meta_key = isset( $_REQUEST['tpg_meta_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_meta_key'] ) ) : null;

					if ( $order && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'order', sanitize_text_field( trim( $order ) ) );
					}

					if ( $order_by && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'order_by', sanitize_text_field( trim( $order_by ) ) );
					}

					if ( in_array( $order_by, array_keys( Options::rtMetaKeyType() ) ) && $tpg_meta_key && in_array( 'order', $post_filter ) ) {
						update_post_meta( $post_id, 'tpg_meta_key', sanitize_text_field( trim( $tpg_meta_key ) ) );
					} else {
						delete_post_meta( $post_id, 'tpg_meta_key' );
					}
				} else {
					delete_post_meta( $post_id, 'order' );
					delete_post_meta( $post_id, 'tpg_meta_key' );
					delete_post_meta( $post_id, 'order_by' );
				}
			} elseif ( $filter == 'date_range' ) {
				if ( in_array( 'date_range', $post_filter ) ) {
					$start = ! empty( $_REQUEST[ $filter . '_start' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $filter . '_start' ] ) ) : null;
					$end   = ! empty( $_REQUEST[ $filter . '_end' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $filter . '_end' ] ) ) : null;

					update_post_meta( $post_id, $filter . '_start', trim( $start ) );
					update_post_meta( $post_id, $filter . '_end', trim( $end ) );
				} else {
					delete_post_meta( $post_id, $filter . '_start' );
					delete_post_meta( $post_id, $filter . '_end' );
				}
			}
			/*
			 * tpg_post_status has removed
			 * elseif ( $filter == 'tpg_post_status' ) {
				delete_post_meta( $post_id, $filter );

				$statuses = isset( $_REQUEST[ $filter ] ) ? $_REQUEST[ $filter ] : [];

				if ( is_array( $statuses ) && ! empty( $statuses ) && in_array( $filter, $post_filter ) ) {
					foreach ( $statuses as $post_status ) {
						add_post_meta( $post_id, $filter, trim( $post_status ) );
					}
				}
			} */
		}

		// Extra css.
		$extraFields = Options::extraStyle();
		$extraTypes  = [ 'color', 'size', 'weight', 'alignment' ];

		foreach ( $extraFields as $key => $title ) {
			foreach ( $extraTypes as $type ) {
				$newKew = $key . "_{$type}";
				if ( isset( $_REQUEST[ $newKew ] ) ) {
					$value = sanitize_text_field( wp_unslash( $_REQUEST[ $newKew ] ) );

					update_post_meta( $post_id, $newKew, $value );
				}
			}
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['_tpg_last_active_tab'] ) && $active_tab = sanitize_text_field( wp_unslash( $_POST['_tpg_last_active_tab'] ) ) ) {
			update_post_meta( $post_id, '_tpg_last_active_tab', $active_tab );
		}
	}

	/**
	 * save_taxonomy_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
	 */
	public function save_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

		$nonce = ! empty( $_POST['_wpnonce_add-tag'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce_add-tag'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'add-tag' ) ) {
			return;
		}

		update_term_meta( $term_id, '_rt_order', 0 );
	}
}
