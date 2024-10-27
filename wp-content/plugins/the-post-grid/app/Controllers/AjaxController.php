<?php
/**
 * Ajax Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

//phpcs:disable WordPress.Security.NonceVerification.Recommended

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Ajax Controller class.
 */
class AjaxController {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_rtTPGSettings', [ $this, 'rtTPGSaveSettings' ] );
		add_action( 'wp_ajax_rtTPGShortCodeList', [ $this, 'shortCodeList' ] );
		add_action( 'wp_ajax_rtTPGTaxonomyListByPostType', [ $this, 'rtTPGTaxonomyListByPostType' ] );
		add_action( 'wp_ajax_rtTPGIsotopeFilter', [ $this, 'rtTPGIsotopeFilter' ] );
		add_action( 'wp_ajax_rtTPGTermListByTaxonomy', [ $this, 'rtTPGTermListByTaxonomy' ] );
		add_action( 'wp_ajax_defaultFilterItem', [ $this, 'defaultFilterItem' ] );
		add_action( 'wp_ajax_getCfGroupListAsField', [ $this, 'getCfGroupListAsField' ] );
	}

	/**
	 * Render
	 *
	 * @return void
	 */
	public function getCfGroupListAsField() {
		$error = true;
		$data  = $msg = null;
		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( ! Fns::verifyNonce() ) {
			$is_ok = false;
		}
		if ( $is_ok ) {
			$fields    = [];
			$post_type = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) : null;

			if ( $cf = Fns::is_acf() && $post_type ) {
				$fields['cf_group'] = [
					'type'        => 'checkbox',
					'name'        => 'cf_group',
					'holderClass' => 'tpg-hidden cf-fields cf-group',
					'label'       => esc_html__( 'Custom Field group', 'the-post-grid' ),
					'multiple'    => true,
					'alignment'   => 'vertical',
					'id'          => 'cf_group',
					'options'     => Fns::get_groups_by_post_type( $post_type, $cf ),
				];
				$error              = false;
				$data               = Fns::rtFieldGenerator( $fields );
			}
		} else {
			$msg = esc_html__( 'Server Error !!', 'the-post-grid' );
		}

		$response = [
			'error' => $error,
			'msg'   => $msg,
			'data'  => $data,
		];

		wp_send_json( $response );
		die();
	}

	/**
	 * Default filter.
	 *
	 * @return void
	 */
	public function defaultFilterItem() {
		$error = true;
		$data  = $msg = null;
		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( ! Fns::verifyNonce() ) {
			$is_ok = false;
		}
		if ( $is_ok ) {
			$filter = isset( $_REQUEST['filter'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['filter'] ) ) : null;
			$term   = isset( $_REQUEST['include'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['include'] ) ) : null;

			if ( ! empty( $filter ) ) {
				$include = [];

				if ( ! empty( $term ) ) {
					$include = explode( ',', $term );
				}

				$error = false;
				$msg   = esc_html__( 'Success', 'the-post-grid' );
				$data  .= "<option value=''>" . esc_html__( 'Show All', 'the-post-grid' ) . '</option>';
				$items = Fns::rt_get_selected_term_by_taxonomy( $filter, $include, '', 0 );

				if ( ! empty( $items ) ) {
					foreach ( $items as $id => $item ) {
						$data .= '<option value="' . absint( $id ) . '">' . esc_html( $item ) . '</option>';
					}
				}
			}
		} else {
			$msg = esc_html__( 'Session Error !!', 'the-post-grid' );
		}
		$response = [
			'error' => $error,
			'msg'   => $msg,
			'data'  => $data,
		];

		wp_send_json( $response );
		die();
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function rtTPGSaveSettings() {
		$error  = true;
		$userId = get_current_user_id();

		if ( $userId != $_REQUEST['uid'] ) {
			wp_send_json(
				[
					'error' => true,
					'msg'   => esc_html__( 'You are not a valid user to modification the options.', 'the-post-grid' ),
				]
			);
			die();
		}

		if ( ! ( current_user_can( 'manage_options' ) ) ) {
			wp_send_json(
				[
					'error' => true,
					'msg'   => esc_html__( 'You have no permission to modification.', 'the-post-grid' ),
				]
			);
			die();
		}

		if ( Fns::verifyNonce() ) {
			unset( $_REQUEST['action'] );
			unset( $_REQUEST[ rtTPG()->nonceId() ] );
			unset( $_REQUEST['_wp_http_referer'] );
			unset( $_REQUEST['uid'] );

			update_option( rtTPG()->options['settings'], wp_unslash( $_REQUEST ) );

			$response = [
				'error' => false,
				'msg'   => esc_html__( 'Settings successfully updated', 'the-post-grid' ),
			];
		} else {
			$response = [
				'error' => $error,
				'msg'   => esc_html__( 'Session Error !!', 'the-post-grid' ),
			];
		}

		wp_send_json( $response );
		die();
	}

	/**
	 * Taxonomy list.
	 *
	 * @return void
	 */
	public function rtTPGTaxonomyListByPostType() {
		$error = true;
		$msg   = $data = null;
		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( ! Fns::verifyNonce() ) {
			$is_ok = false;
		}
		if ( $is_ok ) {
			$error      = false;
			$taxonomies = Fns::rt_get_all_taxonomy_by_post_type( $_REQUEST['post_type'] );

			if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
				$data .= Fns::rtFieldGenerator(
					[
						'tpg_taxonomy' => [
							'type'     => 'checkbox',
							'label'    => esc_html__( 'Taxonomy', 'the-post-grid' ),
							'id'       => 'post-taxonomy',
							'multiple' => true,
							'value'    => isset( $_REQUEST['taxonomy'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['taxonomy'] ) ) : [],
							'options'  => $taxonomies,
						],
					]
				);
			} else {
				$data = '<div class="field-holder">' . esc_html__( 'No Taxonomy found', 'the-post-grid' ) . '</div>';
			}
		} else {
			$msg = esc_html__( 'Security error', 'the-post-grid' );
		}

		wp_send_json(
			[
				'error' => $error,
				'msg'   => $msg,
				'data'  => $data,
			]
		);
		die();
	}

	/**
	 * Isotope Filter
	 *
	 * @return void
	 */
	public function rtTPGIsotopeFilter() {
		$error = true;
		$msg   = $data = null;

		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( ! Fns::verifyNonce() ) {
			$is_ok = false;
		}
		if ( $is_ok ) {
			$error      = false;
			$post_type  = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) : null;
			$taxonomies = Fns::rt_get_taxonomy_for_filter( $post_type );

			if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $tKey => $tax ) {
					$data .= '<option value="' . absint( $tKey ) . '">' . esc_html( $tax ) . '</option>';
				}
			}
		} else {
			$msg = esc_html__( 'Security error', 'the-post-grid' );
		}

		wp_send_json(
			[
				'error' => $error,
				'msg'   => $msg,
				'data'  => $data,
			]
		);
		die();
	}

	/**
	 * Term list
	 *
	 * @return void
	 */
	public function rtTPGTermListByTaxonomy() {
		$error = true;
		$msg   = $data = null;

		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( ! Fns::verifyNonce() ) {
			$is_ok = false;
		}
		if ( $is_ok ) {
			$error    = false;
			$taxonomy = isset( $_REQUEST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['taxonomy'] ) ) : null;

			$data .= "<div class='term-filter-item-container {$taxonomy}'>";
			$data .= Fns::rtFieldGenerator(
				[
					'term_' . $taxonomy => [
						'type'        => 'select',
						'label'       => ucfirst( str_replace( '_', ' ', $taxonomy ) ),
						'class'       => 'rt-select2 full',
						'id'          => 'term-' . wp_rand(),
						'holderClass' => "term-filter-item {$taxonomy}",
						'value'       => null,
						'multiple'    => true,
						'options'     => Fns::rt_get_all_term_by_taxonomy( $taxonomy ),
					],
				]
			);
			$data .= Fns::rtFieldGenerator(
				[
					'term_operator_' . $taxonomy => [
						'type'        => 'select',
						'label'       => esc_html__( 'Operator', 'the-post-grid' ),
						'class'       => 'rt-select2 full',
						'holderClass' => "term-filter-item-operator {$taxonomy}",
						'options'     => Options::rtTermOperators(),
					],
				]
			);
			$data .= '</div>';
		} else {
			$msg = esc_html__( 'Security error', 'the-post-grid' );
		}
		wp_send_json(
			[
				'error' => $error,
				'msg'   => $msg,
				'data'  => $data,
			]
		);
		die();
	}

	/**
	 * Shortcode list
	 *
	 * @return void
	 */
	public function shortCodeList() {

		$is_ok = true;
		if ( ! current_user_can( 'edit_posts' ) ) {
			$is_ok = false;
		}
		if ( $is_ok ) {

			$html = null;
			$scQ  = new \WP_Query(
				apply_filters(
					'tpg_sc_list_query_args',
					[
						'post_type'      => rtTPG()->post_type,
						'order_by'       => 'title',
						'order'          => 'DESC',
						'post_status'    => 'publish',
						'posts_per_page' => - 1,
					]
				)
			);
			if ( $scQ->have_posts() ) {
				$html .= "<div class='mce-container mce-form'>";
				$html .= "<div class='mce-container-body'>";
				$html .= '<label class="mce-widget mce-label" style="padding: 20px;font-weight: bold;" for="scid">' . esc_html__( 'Select Short code', 'the-post-grid' ) . '</label>';
				$html .= "<select name='id' id='scid' style='width: 150px;margin: 15px;'>";
				$html .= "<option value=''>" . esc_html__( 'Default', 'the-post-grid' ) . '</option>';

				while ( $scQ->have_posts() ) {
					$scQ->the_post();
					$html .= "<option value='" . get_the_ID() . "'>" . get_the_title() . '</option>';
				}

				$html .= '</select>';
				$html .= '</div>';
				$html .= '</div>';
			} else {
				$html .= '<div>' . esc_html__( 'No shortCode found.', 'the-post-grid' ) . '</div>';
			}

			Fns::print_html( $html, true );
		} else {
			echo esc_html__( 'Security error', 'the-post-grid' );
		}
		die();
	}
}
