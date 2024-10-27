<?php
/**
 * Admin Ajax class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers\Admin;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

//phpcs:disable WordPress.Security.NonceVerification.Recommended

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Admin Ajax class.
 */
class AdminAjaxController {
	/**
	 * Layout 4 toggle
	 *
	 * @var boolean
	 */
	private $l4toggle = false;

	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'wp_ajax_tpgPreviewAjaxCall', [ $this, 'tpgPreviewAjaxCall' ] );
	}

	/**
	 * Preview rendering
	 *
	 * @return void
	 */
	public function tpgPreviewAjaxCall() {
		$msg   = $data = null;
		$error = true;

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_success( new WP_Error( 'rttpg_block_user_permission', __( 'User permission error', 'the-post-grid' ) ) );
		}

		if ( Fns::verifyNonce() ) {
			$error    = false;
			$rand     = wp_rand();
			$layoutID = 'rt-tpg-container-' . $rand;

			$layout = ( isset( $_REQUEST['layout'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['layout'] ) ) : 'layout1' );

			if ( ! in_array( $layout, array_keys( Options::rtTPGLayouts() ), true ) ) {
				$layout = 'layout1';
			}

			$isIsotope   = preg_match( '/isotope/', $layout );
			$isCarousel  = preg_match( '/carousel/', $layout );
			$isGrid      = preg_match( '/layout/', $layout );
			$isWooCom    = preg_match( '/wc/', $layout );
			$isOffset    = preg_match( '/offset/', $layout );
			$isGridHover = preg_match( '/grid_hover/', $layout );

			$dCol = ( isset( $_REQUEST['column'] ) ? absint( $_REQUEST['column'] ) : 3 );
			$tCol = ( isset( $_REQUEST['tpg_tab_column'] ) ? absint( $_REQUEST['tpg_tab_column'] ) : 2 );
			$mCol = ( isset( $_REQUEST['tpg_mobile_column'] ) ? absint( $_REQUEST['tpg_mobile_column'] ) : 1 );

			if ( ! in_array( $dCol, array_keys( Options::scColumns() ), true ) ) {
				$dCol = 3;
			}

			if ( ! in_array( $tCol, array_keys( Options::scColumns() ), true ) ) {
				$tCol = 2;
			}

			if ( ! in_array( $dCol, array_keys( Options::scColumns() ), true ) ) {
				$mCol = 1;
			}

			if ( $isOffset ) {
				$dCol = ( $dCol < 3 ? 2 : $dCol );
				$tCol = ( $tCol < 3 ? 2 : $tCol );
				$mCol = ( $mCol < 3 ? 1 : $mCol );
			}

			$arg                        = [];
			$fImg                       = ! empty( $_REQUEST['feature_image'] );
			$fImgSize                   = ( isset( $_REQUEST['featured_image_size'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['featured_image_size'] ) ) : 'medium' );
			$mediaSource                = ( isset( $_REQUEST['media_source'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['media_source'] ) ) : 'feature_image' );
			$arg['excerpt_type']        = ( isset( $_REQUEST['tgp_excerpt_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_excerpt_type'] ) ) : 'character' );
			$arg['title_limit_type']    = ( isset( $_REQUEST['tpg_title_limit_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_title_limit_type'] ) ) : 'character' );
			$arg['excerpt_limit']       = ( isset( $_REQUEST['excerpt_limit'] ) ? absint( $_REQUEST['excerpt_limit'] ) : 0 );
			$arg['title_limit']         = ( isset( $_REQUEST['tpg_title_limit'] ) ? absint( $_REQUEST['tpg_title_limit'] ) : 0 );
			$arg['excerpt_more_text']   = ( isset( $_REQUEST['tgp_excerpt_more_text'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_excerpt_more_text'] ) ) : null );
			$arg['read_more_text']      = ( ! empty( $_REQUEST['tgp_read_more_text'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_read_more_text'] ) ) : esc_html__( 'Read More', 'the-post-grid' ) );
			$arg['show_all_text']       = ( ! empty( $_REQUEST['tpg_show_all_text'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_show_all_text'] ) ) : esc_html__( 'Show all', 'the-post-grid' ) );
			$arg['tpg_title_position']  = isset( $_REQUEST['tpg_title_position'] ) && ! empty( $_REQUEST['tpg_title_position'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_title_position'] ) ) : null;
			$arg['btn_alignment_class'] = isset( $_REQUEST['tpg_read_more_button_alignment'] ) && ! empty( $_REQUEST['tpg_read_more_button_alignment'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_read_more_button_alignment'] ) ) : '';
			$arg['category_position']   = isset( $_REQUEST['tpg_category_position'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_position'] ) ) : null;
			$arg['category_style']      = ! empty( $_REQUEST['tpg_category_style'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_style'] ) ) : '';
			$arg['catIcon']             = isset( $_REQUEST['tpg_category_icon'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_icon'] ) ) : true;
			$arg['metaPosition']        = isset( $_REQUEST['tpg_meta_position'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_meta_position'] ) ) : null;
			$arg['metaIcon']            = ! empty( $_REQUEST['tpg_meta_icon'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_meta_icon'] ) ) : true;
			$arg['metaSeparator']       = ! empty( $_REQUEST['tpg_meta_separator'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_meta_separator'] ) ) : '';

			$args     = [];
			$postType = ( isset( $_REQUEST['tpg_post_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_post_type'] ) ) : null );

			if ( $postType ) {
				$args['post_type'] = $postType;
			}

			/* post__in */
			$post__in = ( isset( $_REQUEST['post__in'] ) ? sanitize_text_field( $_REQUEST['post__in'] ) : null );

			if ( $post__in ) {
				$post__in         = explode( ',', $post__in );
				$args['post__in'] = $post__in;
			}

			/* post__not_in */
			$post__not_in = ( isset( $_REQUEST['post__not_in'] ) ? absint( $_REQUEST['post__not_in'] ) : null );

			if ( $post__not_in ) {
				$post__not_in         = explode( ',', $post__not_in );
				$args['post__not_in'] = $post__not_in; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			}

			/* LIMIT */
			$limit                  = ( ( empty( $_REQUEST['limit'] ) || '-1' === $_REQUEST['limit'] ) ? - 1 : absint( $_REQUEST['limit'] ) );
			$queryOffset            = empty( $_REQUEST['offset'] ) ? 0 : absint( $_REQUEST['offset'] );
			$args['posts_per_page'] = $limit;
			$pagination             = isset( $_REQUEST['pagination'] ) && ! empty( $_REQUEST['pagination'] );
			$posts_loading_type     = ( ! empty( $_REQUEST['posts_loading_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['posts_loading_type'] ) ) : 'pagination' );

			if ( $pagination ) {
				$posts_per_page = ( isset( $_REQUEST['posts_per_page'] ) ? absint( $_REQUEST['posts_per_page'] ) : $limit );

				if ( $posts_per_page > $limit && $limit != '-1' ) {
					$posts_per_page = $limit;
				}

				$args['posts_per_page'] = $posts_per_page;

				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

				$offset        = $posts_per_page * ( (int) $paged - 1 );
				$args['paged'] = $paged;

				if ( intval( $args['posts_per_page'] ) > $limit - $offset && $limit != '-1' ) {
					$args['posts_per_page'] = $limit - $offset;
				}
			}

			$adv_filter        = ( isset( $_REQUEST['post_filter'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['post_filter'] ) ) : [] );
			$taxFilter         = ( ! empty( $_REQUEST['tgp_filter_taxonomy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_filter_taxonomy'] ) ) : null );
			$taxHierarchical   = ! empty( $_REQUEST['tgp_filter_taxonomy_hierarchical'] );
			$taxFilterTerms    = [];
			$taxFilterOperator = 'IN';

			$taxQ = [];

			if ( in_array( 'tpg_taxonomy', $adv_filter ) && isset( $_REQUEST['tpg_taxonomy'] ) ) {
				$tpgTaxonomy = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tpg_taxonomy'] ) );

				if ( is_array( $tpgTaxonomy ) && ! empty( $tpgTaxonomy ) ) {
					foreach ( $tpgTaxonomy as $taxonomy ) {
						$terms = ( isset( $_REQUEST[ 'term_' . $taxonomy ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST[ 'term_' . $taxonomy ] ) ) : [] );

						if ( $taxonomy == $taxFilter ) {
							$taxFilterTerms = $terms;
						}

						if ( is_array( $terms ) && ! empty( $terms ) ) {
							$operator = ( isset( $_REQUEST[ 'term_operator_' . $taxonomy ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'term_operator_' . $taxonomy ] ) ) : 'IN' );
							$taxQ[]   = [
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $terms,
								'operator' => $operator,
							];
						}
					}
				}

				if ( count( $taxQ ) >= 2 ) {
					$relation         = ( isset( $_REQUEST['taxonomy_relation'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['taxonomy_relation'] ) ) : 'AND' );
					$taxQ['relation'] = $relation;
				}
			}

			if ( ! empty( $taxQ ) ) {
				$args['tax_query'] = $taxQ; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			}

			if ( in_array( 'order', $adv_filter ) ) {
				$order_by = ( isset( $_REQUEST['order_by'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order_by'] ) ) : null );
				$order    = ( isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : null );

				if ( $order ) {
					$args['order'] = $order;
				}

				if ( $order_by ) {
					$args['orderby'] = $order_by;
					$meta_key        = ! empty( $_REQUEST['tpg_meta_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_meta_key'] ) ) : null;

					if ( in_array( $order_by, array_keys( Options::rtMetaKeyType() ), true ) && $meta_key ) {
						$args['orderby']  = $order_by;
						$args['meta_key'] = $meta_key; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					}
				}
			}

			if ( isset( $_REQUEST['orderby'] ) ) {
				$orderby = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );

				switch ( $orderby ) {
					case 'menu_order':
						$args['orderby'] = 'menu_order title';
						$args['order']   = 'ASC';
						break;
					case 'date':
						$args['orderby'] = 'date';
						$args['order']   = 'DESC';
						break;
					case 'price':
						$args['orderby']  = 'meta_value_num';
						$args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						$args['order']    = 'ASC';
						break;
					case 'price-desc':
						$args['orderby']  = 'meta_value_num';
						$args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						$args['order']    = 'DESC';
						break;
					case 'rating':
						// Sorting handled later though a hook.
						add_filter( 'posts_clauses', [ $this, 'order_by_rating_post_clauses' ] );
						break;
					case 'title':
						$args['orderby'] = 'title';
						$args['order']   = 'ASC';
						break;
				}
			}

			/* Post status has removed

			 * if ( in_array( 'tpg_post_status', $adv_filter ) ) {
				$post_status = ( isset( $_REQUEST['tpg_post_status'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tpg_post_status'] ) ) : [] );

				if ( ! empty( $post_status ) ) {
					$args['post_status'] = $post_status;
				}
			} else {
				$args['post_status'] = 'publish';
			}*/

			$args['post_status'] = 'publish';

			$filterAuthors = [];
			$author        = ( isset( $_REQUEST['author'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['author'] ) ) : [] );

			if ( in_array( 'author', $adv_filter ) && ! empty( $author ) ) {
				$filterAuthors = $args['author__in'] = $author;
			}

			$s = ( isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : [] );

			if ( in_array( 's', $adv_filter ) && ! empty( $s ) ) {
				$args['s'] = $s;
			}

			if ( in_array( 'date_range', $adv_filter ) ) {
				$startDate = ( ! empty( $_REQUEST['date_range_start'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_range_start'] ) ) : null );
				$endDate   = ( ! empty( $_REQUEST['date_range_end'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_range_end'] ) ) : null );
				if ( $startDate && $endDate ) {
					$args['date_query'] = [
						[
							'after'     => $startDate,
							'before'    => $endDate,
							'inclusive' => true,
						],
					];
				}
			}

			$settings       = get_option( rtTPG()->options['settings'] );
			$override_items = ! empty( $settings['template_override_items'] ) ? $settings['template_override_items'] : [];
			$dataArchive    = null;

			if ( ( is_archive() || is_search() || is_tag() || is_author() ) && ! empty( $override_items ) ) {
				unset( $args['post_type'] );
				unset( $args['tax_query'] );
				unset( $args['author__in'] );
				$obj   = get_queried_object();
				$aType = $aValue = null;

				if ( in_array( 'tag-archive', $override_items ) && is_tag() ) {
					if ( ! empty( $obj->slug ) ) {
						$aValue = $args['tag'] = $obj->slug;
						$aType  = 'tag';
					}
				} elseif ( in_array( 'category-archive', $override_items ) && is_category() ) {
					if ( ! empty( $obj->slug ) ) {
						$aValue = $args['category_name'] = $obj->slug;
					}

					$aType = 'category';
				} elseif ( in_array( 'author-archive', $override_items ) && is_author() ) {
					$aValue = $args['author'] = $obj->ID;
					$aType  = 'author';
				} elseif ( in_array( 'search', $override_items ) && is_search() ) {
					$aValue = $args['s'] = get_search_query();
					$aType  = 'search';
				}

				$dataArchive                    = " data-archive='{$aType}' data-archive-value='{$aValue}'";
				$args['posts_per_archive_page'] = $args['posts_per_page'];
			}

			$containerDataAttr  = null;
			$containerDataAttr .= " data-layout='{$layout}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";

			$dCol = 5 === $dCol ? '24' : round( 12 / $dCol );
			$tCol = 5 === $dCol ? '24' : round( 12 / $tCol );
			$mCol = 5 === $dCol ? '24' : round( 12 / $mCol );

			if ( $isCarousel ) {
				$dCol = $tCol = $mCol = 12;
			}

			$arg['grid'] = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";

			if ( ( 'layout2' === $layout ) || ( 'layout3' === $layout ) ) {
				$iCol                = ( isset( $_REQUEST['tgp_layout2_image_column'] ) ? absint( $_REQUEST['tgp_layout2_image_column'] ) : 4 );
				$iCol                = $iCol > 12 ? 4 : $iCol;
				$cCol                = 12 - $iCol;
				$arg['image_area']   = "rt-col-sm-{$iCol} rt-col-xs-12 ";
				$arg['content_area'] = "rt-col-sm-{$cCol} rt-col-xs-12 ";
			}

			if ( 'layout4' === $layout ) {
				$arg['image_area']   = 'rt-col-lg-6 rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
				$arg['content_area'] = 'rt-col-lg-6 rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
			}

			$gridType    = ! empty( $_REQUEST['grid_style'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['grid_style'] ) ) : 'even';
			$arg_class   = [];
			$arg_class[] = ' rt-grid-item';

			if ( ! $isCarousel && ! $isOffset ) {
				$arg_class[] = $gridType . '-grid-item';
			}

			if ( $isOffset ) {
				$arg_class[] = 'rt-offset-item';
			}

			$catHaveBg = ( isset( $_REQUEST['tpg_category_bg'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_bg'] ) ) : '' );

			if ( ! empty( $catHaveBg ) ) {
				$arg_class[] = 'category-have-bg';
			}

			$imgAnimationType = isset( $_REQUEST['tpg_image_animation'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_image_animation'] ) ) : '';

			if ( ! empty( $imgAnimationType ) ) {
				$arg_class[] = $imgAnimationType;
			}

			$masonryG = null;

			if ( 'even' === $gridType && ! $isIsotope && ! $isCarousel ) {
				$masonryG = 'tpg-even';
			} elseif ( 'masonry' === $gridType && ! $isIsotope && ! $isCarousel ) {
				$masonryG = ' tpg-masonry';
			}

			$preLoader = $preLoaderHtml = null;

			if ( $isIsotope ) {
				$arg_class[] = 'isotope-item';
				$preLoader   = 'tpg-pre-loader';
			}

			if ( $isCarousel ) {
				$arg_class[] = 'swiper-slide';
				$preLoader   = 'tpg-pre-loader';
			}

			$arg['class'] = implode( ' ', $arg_class );

			if ( $preLoader ) {
				$preLoaderHtml = '<div class="rt-loading-overlay"></div><div class="rt-loading rt-ball-clip-rotate"><div></div></div>';
			}

			$margin = ! empty( $_REQUEST['margin_option'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['margin_option'] ) ) : 'default';

			if ( 'no' === $margin ) {
				$arg_class[] = 'no-margin';
			}

			if ( ! empty( $_REQUEST['tpg_image_type'] ) && 'circle' === $_REQUEST['tpg_image_type'] ) {
				$arg_class[] = 'tpg-img-circle';
			}

			$arg['anchorClass'] = null;
			$arg['anchorClass'] = $arg['link_target'] = null;
			$link               = isset( $_REQUEST['link_to_detail_page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['link_to_detail_page'] ) ) : '1';
			$link               = ( 'yes' === $link ) ? '1' : $link;
			$isSinglePopUp      = false;
			$linkType           = ! empty( $_REQUEST['detail_page_link_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['detail_page_link_type'] ) ) : 'popup';

			if ( '1' === $link ) {
				if ( 'popup' === $linkType && rtTPG()->hasPro() ) {
					$popupType = ! empty( $_REQUEST['popup_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['popup_type'] ) ) : 'single';
					if ( 'single' === $popupType ) {
						$arg['anchorClass'] .= ' tpg-single-popup';
						$isSinglePopUp       = true;
					} else {
						$arg['anchorClass'] .= ' tpg-multi-popup';
					}
				} else {
					$arg['link_target'] = ! empty( $_REQUEST['link_target'] ) ? ' target="' . sanitize_text_field( wp_unslash( $_REQUEST['link_target'] ) ) . '"' : null;
				}
			} else {
				$arg['anchorClass'] = ' disabled';
			}

			$isSinglePopUp = false;
			$linkType      = ! empty( $_REQUEST['detail_page_link_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['detail_page_link_type'] ) ) : 'popup';

			if ( '1' === $link && 'popup' === $linkType && rtTPG()->hasPro() ) {
				$popupType = ! empty( $_REQUEST['popup_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['popup_type'] ) ) : 'single';

				if ( 'single' === $popupType ) {
					$arg['anchorClass'] .= ' tpg-single-popup';
					$isSinglePopUp       = true;
				} else {
					$arg['anchorClass'] .= ' tpg-multi-popup';
				}
			}

			$parentClass        = ( ! empty( $_REQUEST['parent_class'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['parent_class'] ) ) : null );
			$defaultImgId       = ( ! empty( $_REQUEST['default_preview_image'] ) ? absint( $_REQUEST['default_preview_image'] ) : null );
			$customImgSize      = ( ! empty( $_REQUEST['custom_image_size'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['custom_image_size'] ) ) : [] );
			$fSmallImgSize      = ( isset( $_REQUEST['featured_small_image_size'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['featured_small_image_size'] ) ) : 'medium' );
			$customSmallImgSize = ( ! empty( $_REQUEST['custom_small_image_size'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['custom_small_image_size'] ) ) : [] );

			$arg['items'] = isset( $_REQUEST['item_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['item_fields'] ) ) : [];
			$arg['scID']  = $scID = isset( $_REQUEST['sc_id'] ) ? absint( $_REQUEST['sc_id'] ) : null;

			if ( isset( $arg['excerpt_type'] ) && 'full' === $arg['excerpt_type'] && ( $key = array_search( 'read_more', $arg['items'] ) ) !== false ) {
				unset( $arg['items'][ $key ] );
			}

			if ( isset( $_REQUEST['ignore_sticky_posts'] ) ) {
				$args['ignore_sticky_posts'] = isset( $_REQUEST['ignore_sticky_posts'] );
			}

			$filters         = ! empty( $_REQUEST['tgp_filter'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tgp_filter'] ) ) : [];
			$action_term     = ! empty( $_REQUEST['tgp_default_filter'] ) ? absint( $_REQUEST['tgp_default_filter'] ) : 0;
			$hide_all_button = ! empty( $_REQUEST['tpg_hide_all_button'] ) ? true : false;

			if ( $taxHierarchical ) {
				$terms = Fns::rt_get_all_term_by_taxonomy( $taxFilter, true, 0 );
			} else {
				$terms = Fns::rt_get_all_term_by_taxonomy( $taxFilter, true );
			}

			if ( $hide_all_button && ! $action_term ) {
				if ( ! empty( $terms ) ) {
					$allKeys     = array_keys( $terms );
					$action_term = $allKeys[0];
				}
			}

			if ( in_array( '_taxonomy_filter', $filters ) && $taxFilter && $action_term ) {
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				$args['tax_query'] = [
					[
						'taxonomy' => $taxFilter,
						'field'    => 'term_id',
						'terms'    => [ $action_term ],
					],
				];
			}

			if ( $limit != - 1 && $pagination ) {
				$tempArgs                   = $args;
				$tempArgs['posts_per_page'] = $limit;
				$tempArgs['paged']          = 1;
				$tempArgs['fields']         = 'ids';
				$tempQ                      = new \WP_Query( $tempArgs );

				if ( ! empty( $tempQ->posts ) ) {
					$args['post__in'] = $tempQ->posts;
				}
			}

			if ( $pagination && $queryOffset && isset( $args['paged'] ) ) {
				$queryOffset = ( $posts_per_page * ( $args['paged'] - 1 ) ) + $queryOffset;
			}

			if ( $queryOffset ) {
				$args['offset'] = $queryOffset;
			}

			$arg['title_tag'] = ( ! empty( $_REQUEST['title_tag'] ) && in_array( $_REQUEST['title_tag'], array_keys( Options::getTitleTags() ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['title_tag'] ) ) : 'h3';

			$gridQuery = new \WP_Query( $args );

			$styleMeta = [
				'primary_color'                      => isset( $_REQUEST['primary_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['primary_color'] ) ) : null,
				'button_bg_color'                    => isset( $_REQUEST['button_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_bg_color'] ) ) : null,
				'button_active_bg_color'             => isset( $_REQUEST['button_active_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_active_bg_color'] ) ) : null,
				'button_hover_bg_color'              => isset( $_REQUEST['button_hover_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_hover_bg_color'] ) ) : null,
				'button_text_color'                  => isset( $_REQUEST['button_text_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_text_color'] ) ) : null,
				'button_text_bg_color'               => isset( $_REQUEST['button_text_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_text_bg_color'] ) ) : null,
				'button_border_color'                => isset( $_REQUEST['button_border_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_border_color'] ) ) : null,
				'button_hover_text_color'            => isset( $_REQUEST['button_hover_text_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['button_hover_text_color'] ) ) : null,
				'overlay_color'                      => isset( $_REQUEST['overlay_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['overlay_color'] ) ) : null,
				'overlay_opacity'                    => isset( $_REQUEST['overlay_opacity'] ) ? absint( $_REQUEST['overlay_opacity'] ) : null,
				'overlay_padding'                    => isset( $_REQUEST['overlay_padding'] ) ? absint( $_REQUEST['overlay_padding'] ) : null,
				'tgp_gutter'                         => isset( $_REQUEST['tgp_gutter'] ) ? absint( $_REQUEST['tgp_gutter'] ) : null,
				'tpg_read_more_button_border_radius' => isset( $_REQUEST['tpg_read_more_button_border_radius'] ) ? absint( $_REQUEST['tpg_read_more_button_border_radius'] ) : null,
				'tpg_full_area_bg'                   => isset( $_REQUEST['tpg_full_area_bg'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_full_area_bg'] ) ) : null,
				'tpg_full_area_margin'               => isset( $_REQUEST['tpg_full_area_margin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_full_area_margin'] ) ) : null,
				'tpg_full_area_padding'              => isset( $_REQUEST['tpg_full_area_margin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_full_area_margin'] ) ) : null,
				'tpg_content_wrap_bg'                => isset( $_REQUEST['tpg_content_wrap_bg'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_content_wrap_bg'] ) ) : null,
				'tpg_content_wrap_border'            => isset( $_REQUEST['tpg_content_wrap_border'] ) ? absint( $_REQUEST['tpg_content_wrap_border'] ) : null,
				'tpg_content_wrap_border_color'      => isset( $_REQUEST['tpg_content_wrap_border_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_content_wrap_border_color'] ) ) : null,
				'tpg_content_wrap_border_radius'     => isset( $_REQUEST['tpg_content_wrap_border_radius'] ) ? absint( $_REQUEST['tpg_content_wrap_border_radius'] ) : null,
				'tpg_content_wrap_shadow'            => isset( $_REQUEST['tpg_content_wrap_shadow'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_content_wrap_shadow'] ) ) : null,
				'tpg_box_padding'                    => isset( $_REQUEST['tpg_box_padding'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_box_padding'] ) ) : null,
				'tpg_content_padding'                => isset( $_REQUEST['tpg_content_padding'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_content_padding'] ) ) : null,
				'tpg_heading_bg'                     => isset( $_REQUEST['tpg_heading_bg'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_heading_bg'] ) ) : null,
				'tpg_heading_color'                  => isset( $_REQUEST['tpg_heading_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_heading_color'] ) ) : null,
				'tpg_heading_border_color'           => isset( $_REQUEST['tpg_heading_border_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_heading_border_color'] ) ) : null,
				'tpg_heading_border_size'            => isset( $_REQUEST['tpg_heading_border_size'] ) ? absint( $_REQUEST['tpg_heading_border_size'] ) : null,
				'tpg_heading_margin'                 => isset( $_REQUEST['tpg_heading_margin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_margin'] ) ) : null,
				'tpg_heading_padding'                => isset( $_REQUEST['tpg_heading_padding'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_padding'] ) ) : null,
				'tpg_category_bg'                    => isset( $_REQUEST['tpg_category_bg'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_category_bg'] ) ) : null,
				'tpg_category_color'                 => isset( $_REQUEST['tpg_category_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['tpg_category_color'] ) ) : null,
				'tpg_category_border_radius'         => isset( $_REQUEST['tpg_category_border_radius'] ) ? absint( $_REQUEST['tpg_category_border_radius'] ) : null,
				'tpg_category_margin'                => isset( $_REQUEST['tpg_category_margin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_margin'] ) ) : null,
				'tpg_category_padding'               => isset( $_REQUEST['tpg_category_padding'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_category_padding'] ) ) : null,
				'rt_tpg_category_font_size'          => isset( $_REQUEST['rt_tpg_category_font_size'] ) ? absint( $_REQUEST['rt_tpg_category_font_size'] ) : null,
				'tpg_image_border_radius'            => isset( $_REQUEST['tpg_image_border_radius'] ) ? absint( $_REQUEST['tpg_image_border_radius'] ) : null,
				'title_color'                        => isset( $_REQUEST['title_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['title_color'] ) ) : null,
				'title_size'                         => isset( $_REQUEST['title_size'] ) ? absint( $_REQUEST['title_size'] ) : null,
				'title_weight'                       => isset( $_REQUEST['title_weight'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['title_weight'] ) ) : null,
				'title_alignment'                    => isset( $_REQUEST['title_alignment'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['title_alignment'] ) ) : null,
				'title_hover_color'                  => isset( $_REQUEST['title_hover_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['title_hover_color'] ) ) : null,
				'excerpt_color'                      => isset( $_REQUEST['excerpt_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['excerpt_color'] ) ) : null,
				'excerpt_size'                       => isset( $_REQUEST['excerpt_size'] ) ? absint( $_REQUEST['excerpt_size'] ) : null,
				'excerpt_weight'                     => isset( $_REQUEST['excerpt_weight'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['excerpt_weight'] ) ) : null,
				'excerpt_alignment'                  => isset( $_REQUEST['excerpt_alignment'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['excerpt_alignment'] ) ) : null,
				'meta_data_color'                    => isset( $_REQUEST['meta_data_color'] ) ? sanitize_hex_color( wp_unslash( $_REQUEST['meta_data_color'] ) ) : null,
				'meta_data_size'                     => isset( $_REQUEST['meta_data_size'] ) ? absint( $_REQUEST['meta_data_size'] ) : null,
				'meta_data_weight'                   => isset( $_REQUEST['meta_data_weight'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['meta_data_weight'] ) ) : null,
				'meta_data_alignment'                => isset( $_REQUEST['meta_data_alignment'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['meta_data_alignment'] ) ) : null,
			];

			$data              .= Fns::layoutStyle( $layoutID, $styleMeta, $layout );
			$containerDataAttr .= '';
			$parentClass        = esc_attr( $parentClass );
			$data              .= "<div class='rt-container-fluid rt-tpg-container tpg-shortcode-main-wrapper {$parentClass}' id='{$layoutID}' {$dataArchive} {$containerDataAttr}>";
			// widget heading.
			$heading_tag       = isset( $_REQUEST['tpg_heading_tag'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_tag'] ) ) : 'h2';
			$heading_style     = isset( $_REQUEST['tpg_heading_style'] ) && ! empty( $_REQUEST['tpg_heading_style'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_style'] ) ) : 'style1';
			$heading_alignment = isset( $_REQUEST['tpg_heading_alignment'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_alignment'] ) ) : '';
			$heading_link      = isset( $_REQUEST['tpg_heading_link'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_heading_link'] ) ) : '';

			if ( ! empty( $arg['items'] ) && in_array( 'heading', $arg['items'] ) ) {
				$data .= sprintf( '<div class="tpg-widget-heading-wrapper heading-%1$s %2$s">', $heading_style, $heading_alignment );
				$data .= '<span class="tpg-widget-heading-line line-left"></span>';

				if ( $heading_link ) {
					$data .= sprintf( '<%1$s class="tpg-widget-heading"><a href="%2$s" title="%3$s">%3$s</a></%1$s>', $heading_tag, $heading_link, get_the_title( $scID ) );
				} else {
					$data .= sprintf( '<%1$s class="tpg-widget-heading">%2$s</%1$s>', $heading_tag, get_the_title( $scID ) );
				}

				$data .= '<span class="tpg-widget-heading-line"></span>';
				$data .= '</div>';
			}

			$filters = ! empty( $_REQUEST['tgp_filter'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tgp_filter'] ) ) : [];

			if ( ! empty( $filters ) && ( $isGrid || $isOffset || $isWooCom ) ) {
				$data                     .= "<div class='rt-layout-filter-container rt-clear'><div class='rt-filter-wrap'>";
				$allText                   = apply_filters( 'tpg_filter_all_text', esc_html__( 'All', 'the-post-grid' ), $_REQUEST );
				$selectedSubTermsForButton = null;

				if ( in_array( '_taxonomy_filter', $filters ) && $taxFilter ) {
					$filterType     = ( ! empty( $_REQUEST['tgp_filter_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_filter_type'] ) ) : null );
					$post_count     = ( ! empty( $_REQUEST['tpg_post_count'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_post_count'] ) ) : null );
					$postCountClass = ( $post_count ? ' has-post-count' : null );

					$allSelect      = ' selected';
					$isTermSelected = false;

					if ( $action_term && $taxFilter ) {
						$isTermSelected = true;
						$allSelect      = null;
					}

					if ( ! $filterType || 'dropdown' === $filterType ) {
						$data            .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";
						$termDefaultText  = $allText;
						$dataTerm         = 'all';
						$htmlButton       = '';
						$selectedSubTerms = null;
						$pCount           = 0;

						if ( ! empty( $terms ) ) {
							$i = 0;

							foreach ( $terms as $id => $term ) {
								$pCount = $pCount + $term['count'];
								$sT     = null;

								if ( $taxHierarchical ) {
									$subTerms = Fns::rt_get_all_term_by_taxonomy( $taxFilter, true, $id );

									if ( ! empty( $subTerms ) ) {
										$count = 0;
										$item  = $allCount = null;

										foreach ( $subTerms as $stId => $t ) {
											$count       = $count + absint( $t['count'] );
											$sTPostCount = ( $post_count ? " (<span class='rt-post-count'>{$t['count']}</span>)" : null );
											$item       .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$stId}'><span class='rt-text'>{$t['name']}{$sTPostCount}</span></span>";
										}

										if ( $post_count ) {
											$allCount = " (<span class='rt-post-count'>{$count}</span>)";
										}

										$sT .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap sub-dropdown-wrap{$postCountClass}'>";
										$sT .= "<span class='term-default rt-filter-dropdown-default' data-term='{$id}'>
													<span class='rt-text'>" . $allText . "{$allCount}</span>
													<i class='fa fa-angle-down rt-arrow-angle' aria-hidden='true'></i>
												</span>";
										$sT .= '<span class="term-dropdown rt-filter-dropdown">';
										$sT .= $item;
										$sT .= '</span>';
										$sT .= '</div>';
									}

									if ( $action_term === $id ) {
										$selectedSubTerms = $sT;
									}
								}

								$postCount = ( $post_count ? " (<span class='rt-post-count'>{$term['count']}</span>)" : null );

								if ( $action_term && $action_term == $id ) {
									$termDefaultText = $term['name'] . $postCount;
									$dataTerm        = $id;
								}

								if ( is_array( $taxFilterTerms ) && ! empty( $taxFilterTerms ) ) {
									if ( 'NOT IN' === $taxFilterOperator ) {
										if ( ! in_array( $id, $taxFilterTerms ) && $action_term != $id ) {
											$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$id}'><span class='rt-text'>{$term['name']}{$postCount}</span>{$sT}</span>";
										}
									} else {
										if ( in_array( $id, $taxFilterTerms ) && $action_term != $id ) {
											$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$id}'><span class='rt-text'>{$term['name']}{$postCount}</span>{$sT}</span>";
										}
									}
								} else {
									$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$id}'><span class='rt-text'>{$term['name']}{$postCount}</span>{$sT}</span>";
								}

								$i++;
							}
						}

						$pAllCount = null;

						if ( $post_count ) {
							$pAllCount = " (<span class='rt-post-count'>{$pCount}</span>)";
							if ( ! $action_term ) {
								$termDefaultText = $termDefaultText . $pAllCount;
							}
						}

						if ( ! $hide_all_button ) {
							$htmlButton = "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'><span class='rt-text'>" . $allText . "{$pAllCount}</span></span>" . $htmlButton;
						}

						$htmlButton  = sprintf( '<span class="term-dropdown rt-filter-dropdown">%s</span>', $htmlButton );
						$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataTerm . '">
											<span class="rt-text">' . $termDefaultText . '</span>
											<i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
										</span>';

						$data .= $showAllhtml . $htmlButton;
						$data .= '</div>' . $selectedSubTerms;
					} else {
						$bCount = 0;
						$bItems = null;
						if ( ! empty( $terms ) ) {
							foreach ( $terms as $id => $term ) {
								$bCount = $bCount + absint( $term['count'] );
								$sT     = null;

								if ( $taxHierarchical ) {
									$subTerms = Fns::rt_get_all_term_by_taxonomy( $taxFilter, true, $id );

									if ( ! empty( $subTerms ) ) {
										$sT .= "<div class='rt-filter-sub-tax sub-button-group'>";

										foreach ( $subTerms as $stId => $t ) {
											$sTPostCount = ( $post_count ? " (<span class='rt-post-count'>{$t['count']}</span>)" : null );
											$sT         .= "<span class='rt-filter-button-item' data-term='{$stId}'>{$t['name']}{$sTPostCount}</span>";
										}

										$sT .= '</div>';

										if ( $action_term === $id ) {
											$selectedSubTermsForButton = $sT;
										}
									}
								}

								$postCount    = ( $post_count ? " (<span class='rt-post-count'>{$term['count']}</span>)" : null );
								$termSelected = null;

								if ( $isTermSelected && $id == $action_term ) {
									$termSelected = ' selected';
								}

								if ( is_array( $taxFilterTerms ) && ! empty( $taxFilterTerms ) ) {
									if ( 'NOT IN' === $taxFilterOperator ) {
										if ( ! in_array( $id, $taxFilterTerms ) ) {
											$bItems .= "<span class='term-button-item rt-filter-button-item {$termSelected}' data-term='{$id}'>{$term['name']}{$postCount}{$sT}</span>";
										}
									} else {
										if ( in_array( $id, $taxFilterTerms ) ) {
											$bItems .= "<span class='term-button-item rt-filter-button-item {$termSelected}' data-term='{$id}'>{$term['name']}{$postCount}{$sT}</span>";
										}
									}
								} else {
									$bItems .= "<span class='term-button-item rt-filter-button-item {$termSelected}' data-term='{$id}'>{$term['name']}{$postCount}{$sT}</span>";
								}
							}
						}

						$data .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-button-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";

						if ( ! $hide_all_button ) {
							$pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
							$data   .= "<span class='term-button-item rt-filter-button-item {$allSelect}' data-term='all'>" . $allText . "{$pCountH}</span>";
						}

						$data .= $bItems;
						$data .= '</div>';
					}
				}

				// Author filter.
				if ( in_array( '_author_filter', $filters ) ) {
					$filterType     = ( ! empty( $_REQUEST['tgp_filter_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_filter_type'] ) ) : null );
					$post_count     = ( ! empty( $_REQUEST['tpg_post_count'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tpg_post_count'] ) ) : null );
					$postCountClass = ( $post_count ? ' has-post-count' : null );
					$users          = get_users( apply_filters( 'tpg_author_arg', [] ) );
					$allSelect      = ' selected';
					$isTermSelected = false;

					if ( $action_term && $taxFilter ) {
						$isTermSelected = true;
						$allSelect      = null;
					}

					if ( ! $filterType || 'dropdown' === $filterType ) {
						$data           .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}'>";
						$termDefaultText = $allText;
						$dataAuthor      = 'all';
						$htmlButton      = '';
						$htmlButton     .= '<span class="author-dropdown rt-filter-dropdown">';

						if ( ! empty( $users ) ) {
							foreach ( $users as $user ) {
								if ( is_array( $filterAuthors ) && ! empty( $filterAuthors ) ) {
									if ( in_array( $user->ID, $filterAuthors ) ) {
										if ( $action_term == $user->ID ) {
											$termDefaultText = $user->display_name;
											$dataTerm        = $user->ID;
										} else {
											$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'>{$user->display_name}</span>";
										}
									}
								} else {
									if ( $action_term == $user->ID ) {
										$termDefaultText = $user->display_name;
										$dataTerm        = $user->ID;
									} else {
										$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'>{$user->display_name}</span>";
									}
								}
							}
						}

						if ( $isTermSelected ) {
							$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'>" . $allText . "{$pAllCount}</span>";
						}

						$htmlButton .= '</span>';

						$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataAuthor . '">
											<span class="rt-text">' . $termDefaultText . '</span>
											<i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
										</span>';

						$data .= $showAllhtml . $htmlButton;
						$data .= '</div>';
					} else {
						$bCount = 0;
						$bItems = null;

						if ( ! empty( $users ) ) {
							foreach ( $users as $user ) {
								if ( is_array( $filterAuthors ) && ! empty( $filterAuthors ) ) {
									if ( in_array( $user->ID, $filterAuthors ) ) {
										$bItems .= "<span class='author-button-item rt-filter-button-item data-author='{$user->ID}'>{$user->display_name}</span>";
									}
								} else {
									$bItems .= "<span class='author-button-item rt-filter-button-item data-author='{$user->ID}'>{$user->display_name}</span>";
								}
							}
						}

						$data .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-button-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";

						if ( ! $hide_all_button ) {
							$pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
							$data   .= "<span class='author-button-item rt-filter-button-item {$allSelect}' data-author='all'>" . $allText . "{$pCountH}</span>";
						}

						$data .= $bItems;
						$data .= '</div>';
					}
				}

				if ( in_array( '_search', $filters ) ) {
					$data .= '<div class="rt-filter-item-wrap rt-search-filter-wrap">';
					$data .= sprintf( '<input type="text" class="rt-search-input" placeholder="%s">', esc_html__( 'Search...', 'the-post-grid' ) );
					$data .= "<span class='rt-action'>&#128269;</span>";
					$data .= "<span class='rt-loading'></span>";
					$data .= '</div>';
				}

				if ( in_array( '_order_by', $filters ) ) {
					$wooFeature     = ( 'product' === $postType ? true : false );
					$orders         = Options::rtPostOrderBy( $wooFeature );
					$action_orderby = ( ! empty( $args['orderby'] ) ? trim( $args['orderby'] ) : 'none' );

					if ( 'none' === $action_orderby ) {
						$action_orderby_label = esc_html__( 'Sort By None', 'the-post-grid' );
					} elseif ( in_array( $action_orderby, array_keys( Options::rtMetaKeyType() ) ) ) {
						$action_orderby_label = esc_html__( 'Meta value', 'the-post-grid' );
					} else {
						$action_orderby_label = $orders[ $action_orderby ];
					}

					if ( 'none' !== $action_orderby ) {
						$orders['none'] = esc_html__( 'Sort By None', 'the-post-grid' );
					}

					$data .= '<div class="rt-filter-item-wrap rt-order-by-action rt-filter-dropdown-wrap">';
					$data .= "<span class='order-by-default rt-filter-dropdown-default' data-order-by='{$action_orderby}'>
								<span class='rt-text-order-by'>{$action_orderby_label}</span>
								<i class='fa fa-angle-down rt-arrow-angle' aria-hidden='true'></i>
							</span>";
					$data .= '<span class="order-by-dropdown rt-filter-dropdown">';

					foreach ( $orders as $orderKey => $order ) {
						$data .= '<span class="order-by-dropdown-item rt-filter-dropdown-item" data-order-by="' . $orderKey . '">' . $order . '</span>';
					}

					$data .= '</span>';
					$data .= '</div>';
				}

				if ( in_array( '_sort_order', $filters ) ) {
					$action_order = ( ! empty( $args['order'] ) ? strtoupper( trim( $args['order'] ) ) : 'DESC' );
					$data        .= '<div class="rt-filter-item-wrap rt-sort-order-action">';
					$data        .= "<span class='rt-sort-order-action-arrow' data-sort-order='{$action_order}'>&nbsp;<span></span></span>";
					$data        .= '</div>';
				}

				$data .= "</div>$selectedSubTermsForButton</div>";
			}

			$data .= "<div data-title='" . esc_html__( 'Loading ...', 'the-post-grid' ) . "' class='rt-row rt-content-loader {$layout} {$masonryG} {$preLoader}'>";

			if ( $gridQuery->have_posts() ) {
				if ( $isCarousel ) {
					$cOpt              = ! empty( $_REQUEST['carousel_property'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['carousel_property'] ) ) : [];
					$slider_js_options = apply_filters(
						'rttpg_slider_js_options',
						[
							'speed'           => ! empty( $_REQUEST['tpg_carousel_speed'] ) ? absint( $_REQUEST['tpg_carousel_speed'] ) : 250,
							'autoPlayTimeOut' => ! empty( $_REQUEST['tpg_carousel_autoplay_timeout'] ) ? absint( $_REQUEST['tpg_carousel_autoplay_timeout'] ) : 5000,
							'autoPlay'        => in_array( 'auto_play', $cOpt ) ? true : false,
							'stopOnHover'     => in_array( 'stop_hover', $cOpt ) ? true : false,
							'nav'             => in_array( 'nav_button', $cOpt ) ? true : false,
							'dots'            => in_array( 'pagination', $cOpt ) ? true : false,
							'loop'            => in_array( 'loop', $cOpt ) ? true : false,
							'lazyLoad'        => in_array( 'lazyLoad', $cOpt ) ? true : false,
							'autoHeight'      => in_array( 'auto_height', $cOpt ) ? true : false,
							'rtl'             => in_array( 'rtl', $cOpt ) ? true : false,
						]
					);
					$data             .= sprintf(
						'<div class="rt-swiper-holder swiper"  data-rtowl-options="%s"><div class="swiper-wrapper">',
						htmlspecialchars( wp_json_encode( $slider_js_options ) )
					);
				}

				$isotope_filter = null;

				if ( $isIsotope ) {
					$isotope_filter          = isset( $_REQUEST['isotope_filter'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['isotope_filter'] ) ) : null;
					$isotope_dropdown_filter = isset( $_REQUEST['isotope_filter_dropdown'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['isotope_filter_dropdown'] ) ) : null;
					$selectedTerms           = [];

					if ( isset( $_REQUEST['post_filter'] ) && in_array( 'tpg_taxonomy', $_REQUEST['post_filter'] ) && isset( $_REQUEST['tpg_taxonomy'] ) && in_array( $isotope_filter, $_REQUEST['tpg_taxonomy'] ) ) {
						$selectedTerms = ( isset( $_REQUEST[ 'term_' . $isotope_filter ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST[ 'term_' . $isotope_filter ] ) ) : [] );
					}

					global $wp_version;

					if ( version_compare( $wp_version, '4.5', '>=' ) ) {
						//phpcs:ignore WordPress.WP.DeprecatedParameters.Get_termsParam2Found
						$terms = get_terms(
							$isotope_filter,
							[
								'meta_key'   => '_rt_order', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
								'orderby'    => 'meta_value_num',
								'order'      => 'ASC',
								'hide_empty' => false,
								'include'    => $selectedTerms,
							]
						);
					} else {
						$terms = get_terms(
							[
								'taxonomy'   => $isotope_filter,
								'orderby'    => 'name',
								'order'      => 'ASC',
								'hide_empty' => false,
								'include'    => $selectedTerms,
							]
						);
					}

					$data          .= '<div class="tpg-iso-filter">';
					$htmlButton     = $drop = null;
					$fSelectTrigger = false;

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							$tItem     = ! empty( $_REQUEST['isotope_default_filter'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['isotope_default_filter'] ) ) : null;
							$fSelected = null;

							if ( $tItem == $term->term_id ) {
								$fSelected      = 'selected';
								$fSelectTrigger = true;
							}

							$htmlButton .= sprintf(
								'<button class="rt-iso-btn-%s%s" data-filter=".iso_%d">%s</button>',
								esc_attr( $term->slug ),
								$fSelected ? ' ' . $fSelected : '',
								$term->term_id,
								$term->name
							);
							$drop       .= "<option value='.iso_{$term->term_id}' {$fSelected}>{$term->name}</option>";
						}
					}

					if ( empty( $_REQUEST['isotope_filter_show_all'] ) ) {
						$fSelect    = ( $fSelectTrigger ? null : 'class="selected"' );
						$htmlButton = "<button data-filter='*' {$fSelect}>" . $arg['show_all_text'] . '</button>' . $htmlButton;
						$drop       = "<option value='*' {$fSelect}>{$arg['show_all_text']}</option>" . $drop;
					}

					$filter_count = ! empty( $_REQUEST['isotope_filter_count'] );
					$filter_url   = ! empty( $_REQUEST['isotope_filter_url'] );

					$htmlButton = "<div id='iso-button-{$rand}' class='rt-tpg-isotope-buttons button-group filter-button-group option-set' data-url='{$filter_url}' data-count='{$filter_count}'>{$htmlButton}</div>";

					if ( $isotope_dropdown_filter ) {
						$data .= "<select class='isotope-dropdown-filter'>{$drop}</select>";
					} else {
						$data .= $htmlButton;
					}

					if ( ! empty( $_REQUEST['isotope_search_filter'] ) ) {
						$data .= "<div class='iso-search'><input type='text' class='iso-search-input' placeholder='" . esc_html__( 'Search', 'the-post-grid' ) . "' /></div>";
					}

					$data .= '</div>';
					$data .= "<div class='rt-tpg-isotope' id='iso-tpg-{$rand}'>";
				}

				$l             = $offLoop = 0;
				$offsetBigHtml = $offsetSmallHtml = null;
				$tgCol         = 2;

				if ( 'layout4' === $layout ) {
					$tgCol = round( 12 / $dCol );
				}

				$gridPostCount    = 0;
				$arg['totalPost'] = $gridQuery->post_count;

				while ( $gridQuery->have_posts() ) :
					$gridQuery->the_post();

					if ( $tgCol == $l ) {
						if ( $this->l4toggle ) {
							$this->l4toggle = false;
						} else {
							$this->l4toggle = true;
						}
						$l = 0;
					}

					$arg['postCount']     = $gridPostCount++;
					$pID                  = get_the_ID();
					$arg['pID']           = $pID;
					$arg['title']         = Fns::get_the_title( $pID, $arg );
					$arg['pLink']         = get_permalink();
					$arg['toggle']        = $this->l4toggle;
					$arg['author']        = '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author() . '</a>';
					$comments_number      = get_comments_number( $pID );
					$comments_text        = sprintf( '(%s)', number_format_i18n( $comments_number ) );
					$arg['date']          = get_the_date();
					$arg['excerpt']       = Fns::get_the_excerpt( $pID, $arg );
					$arg['categories']    = get_the_term_list( $pID, 'category', null, ', ' );
					$arg['tags']          = get_the_term_list( $pID, 'post_tag', null, ', ' );
					$arg['post_count']    = get_post_meta( $pID, Fns::get_post_view_count_meta_key(), true );
					$arg['responsiveCol'] = [ $dCol, $tCol, $mCol ];

					if ( $isIsotope ) {
						$termAs    = wp_get_post_terms( $pID, $isotope_filter, [ 'fields' => 'all' ] );
						$isoFilter = [];

						if ( ! empty( $termAs ) ) {
							foreach ( $termAs as $term ) {
								$isoFilter[] = 'iso_' . $term->term_id;
								$isoFilter[] = 'rt-item-' . esc_attr( $term->slug );
							}
						}

						$arg['isoFilter'] = ! empty( $isoFilter ) ? implode( ' ', $isoFilter ) : '';
					}

					$deptClass = null;

					if ( ! empty( $deptAs ) ) {
						foreach ( $deptAs as $dept ) {
							$deptClass .= ' ' . $dept->slug;
						}
					}

					if ( comments_open() ) {
						$arg['comment'] = "<a href='" . get_comments_link( $pID ) . "'>{$comments_text} </a>";
					} else {
						$arg['comment'] = "{$comments_text}";
					}

					$imgSrc             = null;
					$arg['smallImgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
						$pID,
						$fSmallImgSize,
						$mediaSource,
						$defaultImgId,
						$customSmallImgSize
					) : null;

					if ( $isOffset ) {
						if ( 0 === $offLoop ) {
							$arg['imgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
								$pID,
								$fImgSize,
								$mediaSource,
								$defaultImgId,
								$customImgSize
							) : null;
							$arg['offset'] = 'big';
							$offsetBigHtml = Fns::get_template_html( 'layouts/' . $layout, $arg );
						} else {
							$arg['offset']    = 'small';
							$arg['offsetCol'] = [ $dCol, $tCol, $mCol ];
							$arg['imgSrc']    = ! $fImg ? Fns::getFeatureImageSrc(
								$pID,
								'thumbnail',
								$mediaSource,
								$defaultImgId,
								$customImgSize
							) : null;
							$offsetSmallHtml .= Fns::get_template_html( 'layouts/' . $layout, $arg );
						}
					} else {
						$arg['imgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
							$pID,
							$fImgSize,
							$mediaSource,
							$defaultImgId,
							$customImgSize
						) : null;
						$data         .= Fns::get_template_html( 'layouts/' . $layout, $arg );
					}
					$offLoop++;
					$l++;
				endwhile;

				if ( $isOffset ) {
					$oDCol = Fns::get_offset_col( $dCol );
					$oTCol = Fns::get_offset_col( $tCol );
					$oMCol = Fns::get_offset_col( $mCol );

					if ( 'offset03' === $layout || 'offset04' === $layout ) {
						$oDCol['big'] = $oTCol['big'] = $oDCol['small'] = $oTCol['small'] = 6;
						$oMCol['big'] = $oMCol['small'] = 12;
					} elseif ( 'offset06' === $layout ) {
						$oDCol['big']   = 7;
						$oDCol['small'] = 5;
					}

					$data .= "<div class='rt-col-md-{$oDCol['big']} rt-col-sm-{$oTCol['big']} rt-col-xs-{$oMCol['big']}'><div class='rt-row'>{$offsetBigHtml}</div></div>";
					$data .= "<div class='rt-col-md-{$oDCol['small']} rt-col-sm-{$oTCol['small']} rt-col-xs-{$oMCol['small']}'><div class='rt-row offset-small-wrap'>{$offsetSmallHtml}</div></div>";
				}

				if ( $isIsotope || $isCarousel ) {
					$data .= '</div>'; // End isotope / Carousel item holder.

					if ( $isCarousel ) {
						if ( in_array( 'pagination', $cOpt ) ) {
							$data .= '<div class="swiper-pagination"></div>';
						}

						$data .= '</div>';

						if ( in_array( 'nav_button', $cOpt ) ) {
							$data .= '<div class="swiper-navigation"><div class="slider-btn swiper-button-prev"></div><div class="slider-btn swiper-button-next"></div></div>';
						}
					}
				}
			} else {
				$not_found_text = isset( $_REQUEST['tgp_not_found_text'] ) && ! empty( $_REQUEST['tgp_not_found_text'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tgp_not_found_text'] ) ) : esc_html__( 'No post found', 'the-post-grid' );
				$data          .= '<p>' . $not_found_text . '</p>';
			}

			$data       .= $preLoaderHtml;
			$data       .= '</div>'; // End row.
			$htmlUtility = null;

			if ( $pagination && ! $isCarousel ) {
				if ( $isOffset || $isGridHover ) {
					$posts_loading_type = 'page_prev_next';

					$htmlUtility .= "<div class='rt-cb-page-prev-next'>
										<span class='rt-cb-prev-btn'><i class='fa fa-angle-left' aria-hidden='true'></i></span>
										<span class='rt-cb-next-btn'><i class='fa fa-angle-right' aria-hidden='true'></i></span>
									</div>";
				} else {
					if ( 'pagination' === $posts_loading_type ) {
						if ( $isGrid && empty( $filters ) ) {
							$htmlUtility .= Fns::rt_pagination( $gridQuery );
						}
					} elseif ( 'pagination_ajax' === $posts_loading_type && ! $isIsotope ) {
						if ( $isGrid ) {
							$htmlUtility .= "<div class='rt-page-numbers'></div>";
						} else {
							$htmlUtility .= Fns::rt_pagination( $gridQuery, '', true );
						}
					} elseif ( 'load_more' === $posts_loading_type ) {
						$load_more_btn_text = ( ! empty( $_REQUEST['load_more_text'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['load_more_text'] ) ) : '' );
						$load_more_text     = $load_more_btn_text ? $load_more_btn_text : esc_html__( 'Load More', 'the-post-grid' );

						if ( $isGrid ) {
							$htmlUtility .= "<div class='rt-loadmore-btn rt-loadmore-action rt-loadmore-style'>
												<span class='rt-loadmore-text'>" . esc_html( $load_more_text ) . "</span>
												<div class='rt-loadmore-loading rt-ball-scale-multiple rt-2x'><div></div><div></div><div></div></div>
											</div>";
						} else {
							$htmlUtility .= "<div class='rt-tpg-load-more'>
												<button data-sc-id='' data-paged='2'>" . esc_html( $load_more_text ) . '</button>
											</div>';
						}
					} elseif ( 'load_on_scroll' === $posts_loading_type ) {
						if ( $isGrid ) {
							$htmlUtility .= "<div class='rt-infinite-action'>
												<div class='rt-infinite-loading la-fire la-2x'>
													<div></div>
													<div></div>
													<div></div>
												</div>
											</div>";
						} else {
							$htmlUtility .= '<div class="rt-tpg-scroll-load-more" data-trigger="1" data-sc-id="' . absint( $scID ) . '" data-paged="2"></div>';
						}
					}
				}
			}

			if ( $htmlUtility ) {
				$l4toggle = null;

				if ( 'layout4' === $layout ) {
					$l4toggle = 'data-l4toggle="' . $this->l4toggle . '"';
				}

				if ( $isGrid || $isOffset || $isWooCom ) {
					$data .= '<div class="rt-pagination-wrap" data-total-pages="' . absint( $gridQuery->max_num_pages ) . '" data-posts-per-page="' . absint( $args['posts_per_page'] ) . '" data-type="' . esc_attr( $posts_loading_type ) . '" ' . $l4toggle . ' >' . $htmlUtility . '</div>';
				} else {
					$data .= '<div class="rt-tpg-utility" ' . $l4toggle . '>' . $htmlUtility . '</div>';
				}
			}

			$data .= '</div>'; // container rt-tpg.

		} else {
			$msg = esc_html__( 'Session Error !!', 'the-post-grid' );
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
}
