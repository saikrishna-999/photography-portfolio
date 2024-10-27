<?php
/**
 * Shortcode Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Shortcode Controller class.
 */
class ShortcodeController {
	private $scA = [];
	private $l4toggle = false;

	public function __construct() {
		add_shortcode( 'the-post-grid', [ $this, 'the_post_grid_short_code' ] );
		add_action( 'pre_get_posts', [ $this, 'make_sticky_work' ] );
	}

	public function make_sticky_work( $q ) {
		if ( true === $q->get( 'wp_tpg_is_home' ) ) {
			$q->is_home = true;
		}
	}

	public function register_sc_scripts() {
		$settings = get_option( rtTPG()->options['settings'] );
		$caro     = $isSinglePopUp = false;
		$ajaxurl  = '';

		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ) ) ) {
			$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
		} else {
			$ajaxurl .= admin_url( 'admin-ajax.php' );
		}

		$variables = [
			'nonceID' => esc_attr( rtTPG()->nonceId() ),
			'nonce'   => esc_attr( wp_create_nonce( rtTPG()->nonceText() ) ),
			'ajaxurl' => esc_url( $ajaxurl ),
			'uid'     => get_current_user_id(),
		];

		foreach ( $this->scA as $sc ) {
			if ( isset( $sc ) && is_array( $sc ) ) {
				if ( $sc['isSinglePopUp'] ) {
					$isSinglePopUp = true;
				}

				if ( $sc['isWooCom'] ) {
					$variables['woocommerce_enable_ajax_add_to_cart'] = get_option( 'woocommerce_enable_ajax_add_to_cart' );
					$variables['woocommerce_cart_redirect_after_add'] = get_option( 'woocommerce_cart_redirect_after_add' );
				}
			}
		}
		if ( count( $this->scA ) ) {
			wp_localize_script( 'rt-tpg', 'rttpg', $variables );

			do_action( 'tpg_after_script', $isSinglePopUp );
		}

		if ( $isSinglePopUp && rtTPG()->hasPro() ) {
			$html = null;
			$html .= '<div class="md-modal rt-md-effect" id="rt-modal">
						<div class="md-content">
							<div class="rt-md-content-holder">

							</div>
							<div class="md-cls-btn">
								<button class="md-close"><i class="fa fa-times" aria-hidden="true"></i></button>
							</div>
						</div>
					</div>';
			$html .= "<div class='md-overlay'></div>";

			Fns::print_html( $html );
		}
	}

	public function the_post_grid_short_code( $atts, $content = null ) {
		$rand     = wp_rand();
		$layoutID = 'rt-tpg-container-' . $rand;
		$html     = null;
		$arg      = [];
		$atts     = shortcode_atts(
			[
				'id' => null,
			],
			$atts,
			'the-post-grid'
		);
		$scID     = $atts['id'];

		if ( $scID && ! is_null( get_post( $scID ) ) ) {

			$scMeta    = get_post_meta( $scID );
			$layout    = ( isset( $scMeta['layout'][0] ) ? $scMeta['layout'][0] : 'layout1' );
			$gridStyle = ( isset( $scMeta['grid_style'][0] ) ? $scMeta['grid_style'][0] : 'even' );

			if ( ! in_array( $layout, array_keys( Options::rtTPGLayouts() ) ) ) {
				$layout = 'layout1';
			}

			$isIsotope   = preg_match( '/isotope/', $layout );
			$isCarousel  = preg_match( '/carousel/', $layout );
			$isGrid      = preg_match( '/layout/', $layout );
			$isWooCom    = preg_match( '/wc/', $layout );
			$isEdd       = preg_match( '/edd/', $layout );
			$isOffset    = preg_match( '/offset/', $layout );
			$isGridHover = preg_match( '/grid_hover/', $layout );

			$colStore = $dCol = ( isset( $scMeta['column'][0] ) ? absint( $scMeta['column'][0] ) : 3 );
			$tCol     = ( isset( $scMeta['tpg_tab_column'][0] ) ? absint( $scMeta['tpg_tab_column'][0] ) : 2 );
			$mCol     = ( isset( $scMeta['tpg_mobile_column'][0] ) ? absint( $scMeta['tpg_mobile_column'][0] ) : 1 );

			if ( ! in_array( $dCol, array_keys( Options::scColumns() ) ) ) {
				$dCol = 3;
			}

			if ( ! in_array( $tCol, array_keys( Options::scColumns() ) ) ) {
				$tCol = 2;
			}

			if ( ! in_array( $dCol, array_keys( Options::scColumns() ) ) ) {
				$mCol = 1;
			}

			if ( $isOffset ) {
				$dCol = ( $dCol < 3 ? 2 : $dCol );
				$tCol = ( $tCol < 3 ? 2 : $tCol );
				$mCol = ( $mCol < 3 ? 1 : $mCol );
			}

			$arg                        = [];
			$fImg                       = ( ! empty( $scMeta['feature_image'][0] ) ? true : false );
			$fImgSize                   = ( isset( $scMeta['featured_image_size'][0] ) ? $scMeta['featured_image_size'][0] : 'medium' );
			$mediaSource                = ( isset( $scMeta['media_source'][0] ) ? $scMeta['media_source'][0] : 'feature_image' );
			$arg['excerpt_type']        = ( isset( $scMeta['tgp_excerpt_type'][0] ) ? $scMeta['tgp_excerpt_type'][0] : 'character' );
			$arg['title_limit_type']    = ( isset( $scMeta['tpg_title_limit_type'][0] ) ? $scMeta['tpg_title_limit_type'][0] : 'character' );
			$arg['excerpt_limit']       = ( isset( $scMeta['excerpt_limit'][0] ) ? absint( $scMeta['excerpt_limit'][0] ) : 0 );
			$arg['title_limit']         = ( isset( $scMeta['tpg_title_limit'][0] ) ? absint( $scMeta['tpg_title_limit'][0] ) : 0 );
			$arg['excerpt_more_text']   = ( isset( $scMeta['tgp_excerpt_more_text'][0] ) ? $scMeta['tgp_excerpt_more_text'][0] : null );
			$arg['read_more_text']      = ( ! empty( $scMeta['tgp_read_more_text'][0] ) ? $scMeta['tgp_read_more_text'][0] : esc_html__( 'Read More', 'the-post-grid' ) );
			$arg['show_all_text']       = ( ! empty( $scMeta['tpg_show_all_text'][0] ) ? $scMeta['tpg_show_all_text'][0] : esc_html__( 'Show all', 'the-post-grid' ) );
			$arg['tpg_title_position']  = isset( $scMeta['tpg_title_position'][0] ) && ! empty( $scMeta['tpg_title_position'][0] ) ? $scMeta['tpg_title_position'][0] : null;
			$arg['btn_alignment_class'] = isset( $scMeta['tpg_read_more_button_alignment'][0] ) && ! empty( $scMeta['tpg_read_more_button_alignment'][0] )
				? $scMeta['tpg_read_more_button_alignment'][0] : '';
			// Category Settings.
			$arg['category_position'] = isset( $scMeta['tpg_category_position'][0] ) ? $scMeta['tpg_category_position'][0] : null;
			$arg['category_style']    = ! empty( $scMeta['tpg_category_style'][0] ) ? $scMeta['tpg_category_style'][0] : '';
			$arg['catIcon']           = isset( $scMeta['tpg_category_icon'][0] ) ? $scMeta['tpg_category_icon'][0] : true;
			// Meta Settings.
			$arg['metaPosition']  = isset( $scMeta['tpg_meta_position'][0] ) ? $scMeta['tpg_meta_position'][0] : null;
			$arg['metaIcon']      = isset( $scMeta['tpg_meta_icon'][0] ) ? $scMeta['tpg_meta_icon'][0] : true;
			$arg['metaSeparator'] = ! empty( $scMeta['tpg_meta_separator'][0] ) ? $scMeta['tpg_meta_separator'][0] : '';
			/* Argument create */
			$args     = [];
			$postType = ( isset( $scMeta['tpg_post_type'][0] ) ? $scMeta['tpg_post_type'][0] : 'post' );

			if ( $postType ) {
				$args['post_type'] = Fns::available_post_type( $postType );
			}

			// Common filters.
			/* post__in */
			$post__in = ( isset( $scMeta['post__in'][0] ) ? $scMeta['post__in'][0] : null );

			if ( $post__in ) {
				$post__in         = explode( ',', $post__in );
				$args['post__in'] = $post__in;
			}

			/* post__not_in */
			$post__not_in = ( isset( $scMeta['post__not_in'][0] ) ? $scMeta['post__not_in'][0] : null ); //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in

			if ( $post__not_in ) {
				$post__not_in         = explode( ',', $post__not_in );
				$args['post__not_in'] = $post__not_in; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			}

			/* LIMIT */
			$limit                  = ( ( empty( $scMeta['limit'][0] ) || $scMeta['limit'][0] === '-1' ) ? - 1 : absint( $scMeta['limit'][0] ) );
			$queryOffset            = empty( $scMeta['offset'][0] ) ? 0 : absint( $scMeta['offset'][0] );
			$args['posts_per_page'] = $limit;
			$pagination             = ! empty( $scMeta['pagination'][0] );
			$posts_loading_type     = ( ! empty( $scMeta['posts_loading_type'][0] ) ? $scMeta['posts_loading_type'][0] : 'pagination' );

			if ( ! $isCarousel ) {
				$posts_per_page         = ( isset( $scMeta['posts_per_page'][0] ) ? intval( $scMeta['posts_per_page'][0] ) : $limit );
				$args['posts_per_page'] = $posts_per_page;
			}

			if ( $isIsotope && ! $pagination ) {
				$args['posts_per_page'] = $limit;
			}

			if ( empty( $posts_per_page ) || ( $posts_per_page == '-1' && $limit ) ) {
				$args['posts_per_page'] = $limit;
			}

			if ( $pagination && ! $isCarousel ) {
				$args['paged'] = get_query_var( 'page' ) ? get_query_var( 'page' ) : ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );
			}

			// Advanced Filters.
			$adv_filter        = get_post_meta( $scID, 'post_filter' );
			$taxFilter         = get_post_meta( $scID, 'tgp_filter_taxonomy', true );
			$taxHierarchical   = get_post_meta( $scID, 'tgp_filter_taxonomy_hierarchical', true );
			$taxFilterTerms    = [];
			$taxFilterOperator = 'IN';
			// Taxonomy.
			$taxQ = [];

			if ( in_array( 'tpg_taxonomy', $adv_filter ) && isset( $scMeta['tpg_taxonomy'] ) ) {
				if ( is_array( $scMeta['tpg_taxonomy'] ) && ! empty( $scMeta['tpg_taxonomy'] ) ) {
					foreach ( $scMeta['tpg_taxonomy'] as $taxonomy ) {
						$terms = ( isset( $scMeta[ 'term_' . $taxonomy ] ) ? $scMeta[ 'term_' . $taxonomy ] : [] );

						if ( is_array( $terms ) && ! empty( $terms ) ) {
							$operator = ( isset( $scMeta[ 'term_operator_' . $taxonomy ][0] ) ? $scMeta[ 'term_operator_' . $taxonomy ][0] : 'IN' );
							$taxQ[]   = [
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $terms,
								'operator' => $operator,
							];

							if ( $taxonomy == $taxFilter ) {
								$taxFilterOperator = $operator;
							}
						}

						if ( $taxonomy == $taxFilter ) {
							$taxFilterTerms = $terms;
						}
					}
				}

				if ( count( $taxQ ) >= 2 ) {
					$relation         = ( isset( $scMeta['taxonomy_relation'][0] ) ? $scMeta['taxonomy_relation'][0] : 'AND' );
					$taxQ['relation'] = $relation;
				}
			}

			if ( ! empty( $taxQ ) ) {
				$args['tax_query'] = $taxQ; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			}

			// Order.
			if ( in_array( 'order', $adv_filter ) ) {
				$order_by = ( isset( $scMeta['order_by'][0] ) ? $scMeta['order_by'][0] : null );
				$order    = ( isset( $scMeta['order'][0] ) ? $scMeta['order'][0] : null );

				if ( $order ) {
					$args['order'] = $order;
				}

				if ( $order_by ) {
					$args['orderby'] = $order_by;
					$meta_key        = ! empty( $scMeta['tpg_meta_key'][0] ) ? trim( $scMeta['tpg_meta_key'][0] ) : null;

					if ( in_array( $order_by, array_keys( Options::rtMetaKeyType() ) ) && $meta_key ) {
						$args['orderby']  = $order_by;
						$args['meta_key'] = $meta_key; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key

						if ( $order_by === 'meta_value_datetime' ) {
							$args['orderby'] = 'meta_value_num';
						}
					}
				}
			}

			// The below code should remove later
			/*if ( in_array( 'tpg_post_status', $adv_filter ) ) {
				$post_status = ( isset( $scMeta['tpg_post_status'] ) ? $scMeta['tpg_post_status'] : [] );

				if ( ! empty( $post_status ) ) {
					$args['post_status'] = $post_status;
				}
			} else {
				$args['post_status'] = 'publish';
			}*/

			$args['post_status'] = 'publish';

			// Author.
			$author        = ( isset( $scMeta['author'] ) ? $scMeta['author'] : [] );
			$filterAuthors = [];

			if ( in_array( 'author', $adv_filter ) && ! empty( $author ) ) {
				$filterAuthors = $args['author__in'] = $author;
			}

			// Search.
			$s = ( isset( $scMeta['s'][0] ) ? $scMeta['s'][0] : [] );
			if ( in_array( 's', $adv_filter ) && ! empty( $s ) ) {
				$args['s'] = $s;
			}

			// Date query.
			if ( in_array( 'date_range', $adv_filter ) ) {
				$startDate = ( ! empty( $scMeta['date_range_start'][0] ) ? $scMeta['date_range_start'][0] : null );
				$endDate   = ( ! empty( $scMeta['date_range_end'][0] ) ? $scMeta['date_range_end'][0] : null );

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

			$settings        = get_option( rtTPG()->options['settings'] );
			$oLayoutTag      = ! empty( $settings['template_tag'] ) ? absint( $settings['template_tag'] ) : null;
			$oLayoutAuthor   = ! empty( $settings['template_author'] ) ? $settings['template_author'] : null;
			$oLayoutCategory = ! empty( $settings['template_category'] ) ? $settings['template_category'] : null;
			$oLayoutSearch   = ! empty( $settings['template_search'] ) ? $settings['template_search'] : null;
			$dataArchive     = null;

			if ( ( is_category() && $oLayoutCategory ) || ( is_search() && $oLayoutSearch ) || ( is_tag() && $oLayoutTag ) || ( is_author() && $oLayoutAuthor ) ) {
				unset( $args['post_type'] );
				unset( $args['tax_query'] );
				unset( $args['author__in'] );
				$obj   = get_queried_object();
				$aType = $aValue = null;

				if ( $oLayoutTag && is_tag() ) {
					if ( ! empty( $obj->slug ) ) {
						$aValue = $args['tag'] = $obj->slug;
						$aType  = 'tag';
					}
				} elseif ( $oLayoutCategory && is_category() ) {
					if ( ! empty( $obj->slug ) ) {
						$aValue = $args['category_name'] = $obj->slug;
					}
					$aType = 'category';
				} elseif ( $oLayoutAuthor && is_author() ) {
					$aValue = $args['author'] = $obj->ID;
					$aType  = 'author';
				} elseif ( $oLayoutSearch && is_search() ) {
					$aValue = $args['s'] = get_search_query();
					$aType  = 'search';
				}

				$dataArchive                    = " data-archive='{$aType}' data-archive-value='{$aValue}'";
				$args['posts_per_archive_page'] = $args['posts_per_page'];
			}

			// Validation.
			$containerDataAttr = null;
			$containerDataAttr .= " data-layout='{$layout}' data-grid-style='{$gridStyle}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";

			$dCol = $dCol == 5 ? '24' : round( 12 / $dCol );
			$tCol = $dCol == 5 ? '24' : round( 12 / $tCol );
			$mCol = $dCol == 5 ? '24' : round( 12 / $mCol );

			if ( $isCarousel ) {
				$dCol = $tCol = $mCol = 12;
			}

			$arg['grid'] = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";

			if ( $layout == 'layout2' || $layout == 'layout3' ) {
				$iCol                = ( isset( $scMeta['tgp_layout2_image_column'][0] ) ? absint( $scMeta['tgp_layout2_image_column'][0] ) : 4 );
				$iCol                = $iCol > 12 ? 4 : $iCol;
				$cCol                = 12 - $iCol;
				$arg['image_area']   = "rt-col-sm-{$iCol} rt-col-xs-12 ";
				$arg['content_area'] = "rt-col-sm-{$cCol} rt-col-xs-12 ";
			} elseif ( $layout == 'layout4' ) {
				$arg['image_area']   = 'rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
				$arg['content_area'] = 'rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
			}

			$arg_class = [];
			$gridType  = ! empty( $scMeta['grid_style'][0] ) ? $scMeta['grid_style'][0] : 'even';

			if ( $isIsotope && ! rtTPG()->hasPro() ) {
				$arg_class[] = 'masonry-grid-item';
			} elseif ( ! $isCarousel && ! $isOffset ) {
				$arg_class[] = $gridType . '-grid-item';
			}

			$arg_class[] = 'rt-grid-item';

			if ( $isOffset ) {
				$arg_class[] = 'rt-offset-item';
			}

			// Category class.
			$catHaveBg = ( isset( $scMeta['tpg_category_bg'][0] ) ? $scMeta['tpg_category_bg'][0] : '' );

			if ( ! empty( $catHaveBg ) ) {
				$arg_class[] = 'category-have-bg';
			}

			// Image animation type.
			$imgAnimationType = isset( $scMeta['tpg_image_animation'][0] ) ? $scMeta['tpg_image_animation'][0] : '';

			if ( ! empty( $imgAnimationType ) ) {
				$arg_class[] = $imgAnimationType;
			}

			$masonryG = null;

			if ( $gridType == 'even' && ! $isIsotope && ! $isCarousel ) {
				$masonryG = ' tpg-even';
			} elseif ( $gridType == 'masonry' && ! $isIsotope && ! $isCarousel ) {
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

			if ( $preLoader && rtTPG()->hasPro() ) {
				$preLoaderHtml = '<div class="rt-loading-overlay"></div><div class="rt-loading rt-ball-clip-rotate"><div></div></div>';
			}

			$margin = ! empty( $scMeta['margin_option'][0] ) ? $scMeta['margin_option'][0] : 'default';

			if ( $margin == 'no' ) {
				$arg_class[] = 'no-margin';
			}

			if ( ! empty( $scMeta['tpg_image_type'][0] ) && $scMeta['tpg_image_type'][0] == 'circle' ) {
				$arg_class[] = 'tpg-img-circle';
			}

			$arg['class']       = implode( ' ', $arg_class );
			$arg['anchorClass'] = $arg['link_target'] = null;
			$link               = isset( $scMeta['link_to_detail_page'][0] ) ? $scMeta['link_to_detail_page'][0] : '1';
			$link               = ( $link == 'yes' ) ? '1' : $link;

			if ( ! $link ) {
				$arg['anchorClass'] = ' disabled';
			}

			$isSinglePopUp = false;
			$linkType      = ! empty( $scMeta['detail_page_link_type'][0] ) ? $scMeta['detail_page_link_type'][0] : 'popup';

			if ( $link == '1' ) {
				if ( $linkType == 'popup' && rtTPG()->hasPro() ) {
					$popupType = ! empty( $scMeta['popup_type'][0] ) ? $scMeta['popup_type'][0] : 'single';

					if ( $popupType == 'single' ) {
						$arg['anchorClass'] .= ' tpg-single-popup';
						$isSinglePopUp      = true;
					} else {
						$arg['anchorClass'] .= ' tpg-multi-popup';
					}
				} else {
					$arg['link_target'] = ! empty( $scMeta['link_target'][0] ) ? " target='{$scMeta['link_target'][0]}'" : null;
				}
			}

			$parentClass   = ( ! empty( $scMeta['parent_class'][0] ) ? trim( $scMeta['parent_class'][0] ) : null );
			$defaultImgId  = ( ! empty( $scMeta['default_preview_image'][0] ) ? absint( $scMeta['default_preview_image'][0] ) : null );
			$customImgSize = ( ! empty( $scMeta['custom_image_size'] ) ? $scMeta['custom_image_size'] : [] );
			// Grid Hover Layout.
			$fSmallImgSize      = ( isset( $scMeta['featured_small_image_size'][0] ) ? $scMeta['featured_small_image_size'][0] : 'medium' );
			$customSmallImgSize = ( ! empty( $scMeta['custom_small_image_size'] ) ? $scMeta['custom_small_image_size'] : [] );

			$arg['scID']  = $scID;
			$arg['items'] = isset( $scMeta['item_fields'] ) ? ( $scMeta['item_fields'] ? $scMeta['item_fields'] : [] ) : [];

			if ( in_array( 'cf', $arg['items'] ) ) {
				$arg['cf_group'] = [];
				$arg['cf_group'] = get_post_meta( $scID, 'cf_group' );
				$arg['format']   = [
					'hide_empty'       => get_post_meta( $scID, 'cf_hide_empty_value', true ),
					'show_value'       => get_post_meta( $scID, 'cf_show_only_value', true ),
					'hide_group_title' => get_post_meta( $scID, 'cf_hide_group_title', true ),
				];
			}

			// Set readmore false if excerpt type = full content.
			if ( isset( $arg['excerpt_type'] ) && $arg['excerpt_type'] === 'full' && ( $key = array_search( 'read_more', $arg['items'] ) ) !== false ) {
				unset( $arg['items'][ $key ] );
			}

			if ( empty( $scMeta['ignore_sticky_posts'][0] ) ) {
				$args['ignore_sticky_posts'] = true;
			} else {
				$args['wp_tpg_is_home'] = true;
			}

			$filters         = ! empty( $scMeta['tgp_filter'] ) ? $scMeta['tgp_filter'] : [];
			$action_term     = ! empty( $scMeta['tgp_default_filter'][0] ) ? absint( $scMeta['tgp_default_filter'][0] ) : 0;
			$hide_all_button = ! empty( $scMeta['tpg_hide_all_button'][0] ) ? true : false;

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

			$arg['title_tag'] = ( ! empty( $scMeta['title_tag'][0] ) && in_array( $scMeta['title_tag'][0], array_keys( Options::getTitleTags() ) ) ) ? esc_attr( $scMeta['title_tag'][0] ) : 'h3';

			$gridQuery = new \WP_Query( apply_filters( 'tpg_sc_query_args', $args, $scMeta ) );

			// Start layout.
			$html .= Fns::layoutStyle( $layoutID, $scMeta, $layout, $scID );

			$containerDataAttr .= " data-sc-id='{$scID}'";

			if ( isset( $settings['tpg_load_script'] ) ) {
				$parentClass .= ' loading';
			}

			$carousel_nav = ! empty( $scMeta['carousel_property'] ) ? $scMeta['carousel_property'] : [];
			$is_nav       = in_array( 'nav_button', $carousel_nav );

			if ( $isCarousel ) {
				$parentClass .= ' tpg-carousel-main';

				if ( $is_nav ) {
					$parentClass .= ' tpg-has-nav';
				}

				$cOptMeta = ! empty( $scMeta['carousel_property'] ) ? $scMeta['carousel_property'] : [];

				if ( in_array( 'lazy_load', $cOptMeta ) ) {
					$parentClass .= ' is-lazy-load-yes';
				}

				if ( in_array( 'auto_height', $cOptMeta ) ) {
					$parentClass .= ' is-auto-height-yes';
				}
			}

			$parentClass = esc_attr( $parentClass );

			$html .= "<div class='rt-container-fluid rt-tpg-container tpg-shortcode-main-wrapper {$parentClass}' id='{$layoutID}' {$dataArchive} {$containerDataAttr}>";

			// widget heading.
			$heading_tag       = isset( $scMeta['tpg_heading_tag'][0] ) ? esc_attr( $scMeta['tpg_heading_tag'][0] ) : 'h2';
			$heading_style     = isset( $scMeta['tpg_heading_style'][0] ) && ! empty( $scMeta['tpg_heading_style'][0] ) ? esc_attr( $scMeta['tpg_heading_style'][0] ) : 'style1';
			$heading_alignment = isset( $scMeta['tpg_heading_alignment'][0] ) ? esc_attr( $scMeta['tpg_heading_alignment'][0] ) : '';
			$heading_link      = isset( $scMeta['tpg_heading_link'][0] ) ? esc_attr( $scMeta['tpg_heading_link'][0] ) : '';

			if ( ! empty( $arg['items'] ) && in_array( 'heading', $arg['items'] ) ) {
				$html .= sprintf( '<div class="tpg-widget-heading-wrapper heading-%1$s %2$s">', $heading_style, $heading_alignment );
				$html .= '<span class="tpg-widget-heading-line line-left"></span>';

				if ( $heading_link ) {
					$html .= sprintf( '<%1$s class="tpg-widget-heading"><a href="%2$s" title="%3$s">%3$s</a></%1$s>', esc_attr( Fns::print_validated_html_tag( $heading_tag ) ), $heading_link, esc_html( get_the_title() ) );
				} else {
					$html .= sprintf( '<%1$s class="tpg-widget-heading">%2$s</%1$s>', esc_attr( Fns::print_validated_html_tag( $heading_tag ) ), esc_html( get_the_title( $scID ) ) );
				}

				$html .= '<span class="tpg-widget-heading-line"></span>';
				$html .= '</div>';
			}

			if ( ! $isCarousel && isset( $settings['tpg_enable_preloader'] ) ) {
				$html .= '<div id="bottom-script-loader" class="bottom-script-loader"><div class="rt-ball-clip-rotate"><div></div></div></div>';
			}

			if ( ! empty( $filters ) && ( $isGrid || $isOffset || $isWooCom || $isEdd || $isGridHover ) ) {
				$html                      .= "<div class='rt-layout-filter-container rt-clear'><div class='rt-filter-wrap'>";
				$selectedSubTermsForButton = null;
				$allText                   = apply_filters( 'tpg_filter_all_text', esc_html__( 'All', 'the-post-grid' ), $scMeta );

				if ( in_array( '_taxonomy_filter', $filters ) && $taxFilter ) {
					$filterType     = ( ! empty( $scMeta['tgp_filter_type'][0] ) ? $scMeta['tgp_filter_type'][0] : null );
					$post_count     = ( ! empty( $scMeta['tpg_post_count'][0] ) ? $scMeta['tpg_post_count'][0] : null );
					$postCountClass = ( $post_count ? ' has-post-count' : null );
					$allSelect      = ' selected';
					$isTermSelected = false;

					if ( $action_term && $taxFilter ) {
						$isTermSelected = true;
						$allSelect      = null;
					}

					if ( ! $filterType || $filterType == 'dropdown' ) {
						$html             .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";
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
											$item        .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$stId}'><span class='rt-text'>{$t['name']}{$sTPostCount}</span></span>";
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
									if ( $taxFilterOperator == 'NOT IN' ) {
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

								$i ++;
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
						$htmlButton = sprintf( '<span class="term-dropdown rt-filter-dropdown">%s</span>', $htmlButton );

						$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataTerm . '">
											<span class="rt-text">' . $termDefaultText . '</span>
											<i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
										</span>';

						$html .= $showAllhtml . $htmlButton;
						$html .= '</div>' . $selectedSubTerms;
					} else {
						$termDefaultText = $allText;
						$bCount          = 0;
						$bItems          = null;

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
											$sT          .= "<span class='rt-filter-button-item' data-term='{$stId}'>{$t['name']}{$sTPostCount}</span>";
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
									if ( $taxFilterOperator == 'NOT IN' ) {
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

						$html .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-button-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";

						if ( ! $hide_all_button ) {
							$pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
							$html    .= "<span class='term-button-item rt-filter-button-item {$allSelect}' data-term='all'>" . $allText . "{$pCountH}</span>";
						}

						$html .= $bItems;
						$html .= '</div>';
					}
				}

				// Author filter.
				if ( in_array( '_author_filter', $filters ) ) {
					$filterType = ( ! empty( $scMeta['tgp_filter_type'][0] ) ? $scMeta['tgp_filter_type'][0] : null );
					$post_count = ( ! empty( $scMeta['tpg_post_count'][0] ) ? $scMeta['tpg_post_count'][0] : null );
					$users      = get_users( apply_filters( 'tpg_author_arg', [] ) );

					$allSelect      = ' selected';
					$isTermSelected = false;

					if ( $action_term && $taxFilter ) {
						$isTermSelected = true;
						$allSelect      = null;
					}

					$postCountClass = $postCountClass ?? '';
					if ( ! $filterType || $filterType == 'dropdown' ) {
						$html            .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}'>";
						$termDefaultText = $allText;
						$dataAuthor      = 'all';
						$htmlButton      = '';
						$htmlButton      .= '<span class="author-dropdown rt-filter-dropdown">';

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

						$html .= $showAllhtml . $htmlButton;
						$html .= '</div>';
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

						$html .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-button-wrap{$postCountClass}' data-taxonomy='{$taxFilter}'>";

						if ( ! $hide_all_button ) {
							$pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
							$html    .= "<span class='author-button-item rt-filter-button-item {$allSelect}' data-author='all'>" . $allText . "{$pCountH}</span>";
						}

						$html .= $bItems;
						$html .= '</div>';
					}
				}

				if ( in_array( '_search', $filters ) ) {
					$html .= '<div class="rt-filter-item-wrap rt-search-filter-wrap">';
					$html .= sprintf( '<input type="text" class="rt-search-input" placeholder="%s">', esc_html__( 'Search...', 'the-post-grid' ) );
					$html .= "<span class='rt-action'>&#128269;</span>";
					$html .= "<span class='rt-loading'></span>";
					$html .= '</div>';
				}

				if ( in_array( '_order_by', $filters ) ) {
					$wooFeature     = ( $postType == 'product' ? true : false );
					$orders         = Options::rtPostOrderBy( $wooFeature );
					$action_orderby = ( ! empty( $args['orderby'] ) ? trim( $args['orderby'] ) : 'none' );

					if ( $action_orderby == 'ID' ) {
						$action_orderby = 'title';
					}

					if ( $action_orderby == 'none' ) {
						$action_orderby_label = esc_html__( 'Sort By None', 'the-post-grid' );
					} elseif ( in_array( $action_orderby, array_keys( Options::rtMetaKeyType() ) ) ) {
						$action_orderby_label = esc_html__( 'Meta value', 'the-post-grid' );
					} else {
						$action_orderby_label = isset( $orders[ $action_orderby ] ) ? $orders[ $action_orderby ] : '';
					}

					if ( $action_orderby !== 'none' ) {
						$orders['none'] = esc_html__( 'Sort By None', 'the-post-grid' );
					}
					$html .= '<div class="rt-filter-item-wrap rt-order-by-action rt-filter-dropdown-wrap">';
					$html .= "<span class='order-by-default rt-filter-dropdown-default' data-order-by='{$action_orderby}'>
								<span class='rt-text-order-by'>{$action_orderby_label}</span>
								<i class='fa fa-angle-down rt-arrow-angle' aria-hidden='true'></i>
							</span>";
					$html .= '<span class="order-by-dropdown rt-filter-dropdown">';

					foreach ( $orders as $orderKey => $order ) {
						$html .= '<span class="order-by-dropdown-item rt-filter-dropdown-item" data-order-by="' . $orderKey . '">' . $order . '</span>';
					}

					$html .= '</span>';
					$html .= '</div>';
				}

				if ( in_array( '_sort_order', $filters ) ) {
					$action_order = ( ! empty( $args['order'] ) ? strtoupper( trim( $args['order'] ) ) : 'DESC' );
					$html         .= '<div class="rt-filter-item-wrap rt-sort-order-action">';
					$html         .= "<span class='rt-sort-order-action-arrow' data-sort-order='{$action_order}'>&nbsp;<span></span></span>";
					$html         .= '</div>';
				}

				$html .= "</div>$selectedSubTermsForButton</div>";
			}
			$is_gallery_layout = ' ';
			if ( $layout === 'layout17' ) {
				$is_gallery_layout = 'grid-layout7';
			}
			$html .= "<div data-title='" . esc_html__( 'Loading ...', 'the-post-grid' ) . "' class='rt-row rt-content-loader {$is_gallery_layout} {$layout}{$masonryG} {$preLoader}'>";

			$not_found_text = isset( $scMeta['tgp_not_found_text'][0] ) && ! empty( $scMeta['tgp_not_found_text'][0] ) ? esc_html( $scMeta['tgp_not_found_text'][0] ) : esc_html__( 'No post found', 'the-post-grid' );

			if ( $gridQuery->have_posts() ) {
				$is_lazy_load = '';

				if ( $isCarousel ) {
					$cOpt              = ! empty( $scMeta['carousel_property'] ) ? $scMeta['carousel_property'] : [];
					$slider_js_options = apply_filters(
						'rttpg_slider_js_options',
						[
							'speed'           => ! empty( $scMeta['tpg_carousel_speed'][0] ) ? absint( $scMeta['tpg_carousel_speed'][0] ) : 250,
							'autoPlayTimeOut' => ! empty( $scMeta['tpg_carousel_autoplay_timeout'][0] ) ? absint( $scMeta['tpg_carousel_autoplay_timeout'][0] ) : 5000,
							'autoPlay'        => in_array( 'auto_play', $cOpt ),
							'stopOnHover'     => in_array( 'stop_hover', $cOpt ),
							'nav'             => in_array( 'nav_button', $cOpt ),
							'dots'            => in_array( 'pagination', $cOpt ),
							'loop'            => in_array( 'loop', $cOpt ),
							'lazy'            => in_array( 'lazy_load', $cOpt ),
							'autoHeight'      => in_array( 'auto_height', $cOpt ),
							'rtl'             => in_array( 'rtl', $cOpt ) ? 'rtl' : 'ltr',
						],
						$scMeta
					);
					$html              .= sprintf(
						'<div class="rt-swiper-holder swiper"  data-rtowl-options="%s" dir="%s"><div class="swiper-wrapper">',
						htmlspecialchars( wp_json_encode( $slider_js_options ) ),
						esc_attr( $slider_js_options['rtl'] )
					);

					if ( in_array( 'lazy_load', $cOpt ) ) {
						$is_lazy_load = 'swiper-lazy';
					}
				}

				$isotope_filter = null;

				if ( $isIsotope ) {
					$isotope_filter          = isset( $scMeta['isotope_filter'][0] ) ? $scMeta['isotope_filter'][0] : null;
					$isotope_dropdown_filter = isset( $scMeta['isotope_filter_dropdown'][0] ) ? $scMeta['isotope_filter_dropdown'][0] : null;
					$selectedTerms           = [];

					if ( isset( $scMeta['post_filter'] )
					     && in_array(
						     'tpg_taxonomy',
						     $scMeta['post_filter']
					     )
					     && isset( $scMeta['tpg_taxonomy'] )
					     && in_array(
						     $isotope_filter,
						     $scMeta['tpg_taxonomy']
					     )
					) {
						$selectedTerms = ( isset( $scMeta[ 'term_' . $isotope_filter ] ) ? $scMeta[ 'term_' . $isotope_filter ] : [] );
					}
					$termArgs = [
						'taxonomy'   => $isotope_filter,
						'orderby'    => 'meta_value_num',
						'order'      => 'ASC',
						'hide_empty' => false,
						'include'    => $selectedTerms,
					];

					if ( rtTPG()->hasPro() ) {
						$termArgs['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					}

					$terms = get_terms( $termArgs );

					$html           .= '<div class="tpg-iso-filter">';
					$htmlButton     = $drop = null;
					$fSelectTrigger = false;

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							$tItem     = ! empty( $scMeta['isotope_default_filter'][0] ) ? $scMeta['isotope_default_filter'][0] : null;
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

					if ( empty( $scMeta['isotope_filter_show_all'][0] ) ) {
						$is_select_iso_term = ! empty( $scMeta['isotope_default_filter'][0] ) ? '' : 'selected';
						$fSelect            = ( $fSelectTrigger ? null : 'class="selected"' );
						$htmlButton         = "<button class='rt-iso-btn-all " . $is_select_iso_term . "' data-filter='*'>" . $arg['show_all_text'] . '</button>' . $htmlButton;
						$drop               = "<option value='*' {$fSelect}>{$arg['show_all_text']}</option>" . $drop;
					}

					$filter_count = ! empty( $scMeta['isotope_filter_count'][0] ) ? true : false;
					$filter_url   = ! empty( $scMeta['isotope_filter_url'][0] ) ? true : false;
					$htmlButton   = "<div id='iso-button-{$rand}' class='rt-tpg-isotope-buttons button-group filter-button-group option-set' data-url='{$filter_url}' data-count='{$filter_count}'>{$htmlButton}</div>";

					if ( $isotope_dropdown_filter ) {
						$html .= "<select class='isotope-dropdown-filter'>{$drop}</select>";
					} else {
						$html .= $htmlButton;
					}

					if ( ! empty( $scMeta['isotope_search_filter'][0] ) ) {
						$html .= "<div class='iso-search'><input type='text' class='iso-search-input' placeholder='" . esc_html__( 'Search', 'the-post-grid' ) . "' /></div>";
					}

					$html .= '</div>';
					$html .= "<div class='rt-tpg-isotope' id='iso-tpg-{$rand}'>";
				}

				$l                = $offLoop = 0;
				$offsetBigHtml    = $offsetSmallHtml = null;
				$gridPostCount    = 0;
				$arg['totalPost'] = $gridQuery->post_count;

				while ( $gridQuery->have_posts() ) :
					$gridQuery->the_post();

					if ( $colStore == $l ) {
						if ( $this->l4toggle ) {
							$this->l4toggle = false;
						} else {
							$this->l4toggle = true;
						}

						$l = 0;
					}
					$pID              = get_the_ID();
					$external_link    = get_post_meta( $pID, 'tpg_read_more', true );
					$arg['postCount'] = $gridPostCount ++;
					$arg['pID']       = $pID;
					$arg['title']     = Fns::get_the_title( $pID, $arg );
					$arg['pLink']     = $external_link['url'] ?? get_permalink();
					$arg['toggle']    = $this->l4toggle;
					$arg['layoutID']  = $layoutID;
					$arg['author']    = apply_filters(
						'rttpg_author_link',
						sprintf( '<a href="%s">%s</a>', get_author_posts_url( get_the_author_meta( 'ID' ) ), get_the_author() )
					);
					$comments_number  = get_comments_number( $pID );
					$comments_text    = sprintf( '(%s)', number_format_i18n( $comments_number ) );

					$arg['date']      = get_the_date();
					$arg['excerpt']   = Fns::get_the_excerpt( $pID, $arg );
					$default_taxonomy = 'category';
					$_all_post_types  = array_keys( Fns::get_post_types() );

					if ( $postType && ! in_array(
							$postType,
							[
								'post',
								'page',
							]
						) && in_array( $postType, $_all_post_types ) ) {
						$taxonomies = get_object_taxonomies( $postType );

						if ( in_array( '_taxonomy_filter', $filters ) && $taxFilter ) {
							$default_taxonomy = $taxFilter;
						} elseif ( ! empty( $scMeta['tpg_taxonomy'] ) ) {
							$default_taxonomy = $scMeta['tpg_taxonomy'][0];
						} elseif ( ! empty( $taxonomies ) ) {
							$default_taxonomy = $taxonomies[0];
						}
					}

					$arg['categories']    = Fns::rt_get_the_term_list( $pID, $default_taxonomy, null, '<span class="rt-separator">,</span>' );
					$arg['tags']          = get_the_term_list( $pID, 'post_tag', null, '<span class="rt-separator">,</span>' );
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

					if ( comments_open() ) {
						$arg['comment'] = "<a href='" . get_comments_link( $pID ) . "'>{$comments_text} </a>";
					} else {
						$arg['comment'] = "{$comments_text}";
					}

					$imgSrc = null;

					// TODO: Image Thumbnail.
					$arg['smallImgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
						$pID,
						$fSmallImgSize,
						$mediaSource,
						$defaultImgId,
						$customSmallImgSize,
						$is_lazy_load
					) : null;
					if ( $isOffset ) {
						if ( $offLoop == 0 ) {
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
							$offsetSmallHtml  .= Fns::get_template_html( 'layouts/' . $layout, $arg );
						}
					} else {
						$arg['imgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
							$pID,
							$fImgSize,
							$mediaSource,
							$defaultImgId,
							$customImgSize,
							$is_lazy_load
						) : null;
						$html          .= Fns::get_template_html( 'layouts/' . $layout, $arg );
					}

					$offLoop ++;
					$l ++;
				endwhile;

				if ( $isOffset ) {
					$oDCol = Fns::get_offset_col( $dCol );
					$oTCol = Fns::get_offset_col( $tCol );
					$oMCol = Fns::get_offset_col( $mCol );

					if ( $layout == 'offset03' || $layout == 'offset04' ) {
						$oDCol['big'] = $oTCol['big'] = $oDCol['small'] = $oTCol['small'] = 6;
						$oMCol['big'] = $oMCol['small'] = 12;
					} elseif ( $layout == 'offset06' ) {
						$oDCol['big']   = 7;
						$oDCol['small'] = 5;
					}

					$html .= "<div class='rt-col-md-{$oDCol['big']} rt-col-sm-{$oTCol['big']} rt-col-xs-{$oMCol['big']}'><div class='rt-row'>{$offsetBigHtml}</div></div>";
					$html .= "<div class='rt-col-md-{$oDCol['small']} rt-col-sm-{$oTCol['small']} rt-col-xs-{$oMCol['small']}'><div class='rt-row offset-small-wrap'>{$offsetSmallHtml}</div></div>";
				}

				if ( $isIsotope || $isCarousel ) {
					$html .= '</div>'; // End isotope / Carousel item holder.

					if ( $isIsotope ) {
						$html .= '<div class="isotope-term-no-post"><p>' . $not_found_text . '</p></div>';
					}

					if ( $isCarousel ) {
						$html .= '</div>';

						if ( in_array( 'pagination', $cOpt ) ) {
							$html .= '<div class="swiper-pagination"></div>';
						}

						if ( in_array( 'nav_button', $cOpt ) ) {
							$html .= '<div class="swiper-navigation"><div class="slider-btn swiper-button-prev"></div><div class="slider-btn swiper-button-next"></div></div>';
						}
					}
				}
			} else {
				$html .= sprintf(
					'<p>%s</p>',
					apply_filters( 'tpg_not_found_text', $not_found_text, $args, $scMeta )
				);
			}

			$html        .= $preLoaderHtml;
			$html        .= '</div>'; // End row.
			$htmlUtility = null;

			if ( $pagination && ! $isCarousel ) {
				if ( $isOffset || $isGridHover ) {
					$posts_loading_type = 'page_prev_next';
					$htmlUtility        .= "<div class='rt-cb-page-prev-next'>
											<span class='rt-cb-prev-btn'><i class='fa fa-angle-left' aria-hidden='true'></i></span>
											<span class='rt-cb-next-btn'><i class='fa fa-angle-right' aria-hidden='true'></i></span>
										</div>";
				} else {
					$hide = ( $gridQuery->max_num_pages < 2 ? ' rt-hidden-elm' : null );
					if ( $posts_loading_type == 'pagination' ) {
						if ( ( $isGrid || $isWooCom || $isEdd ) && empty( $filters ) ) {
							$htmlUtility .= Fns::rt_pagination( $gridQuery );
						}
					} elseif ( $posts_loading_type == 'pagination_ajax' && ! $isIsotope ) {
						$htmlUtility .= "<div class='rt-page-numbers'></div>";
					} elseif ( $posts_loading_type == 'load_more' && rtTPG()->hasPro() ) {
						$load_more_btn_text = ( ! empty( $scMeta['load_more_text'][0] ) ? $scMeta['load_more_text'][0] : '' );
						$load_more_text     = $load_more_btn_text ? esc_html( $load_more_btn_text ) : esc_html__( 'Load More', 'the-post-grid' );

						$htmlUtility .= "<div class='rt-loadmore-btn rt-loadmore-action rt-loadmore-style{$hide}'>
											<span class='rt-loadmore-text'>" . $load_more_text . "</span>
											<div class='rt-loadmore-loading rt-ball-scale-multiple rt-2x'><div></div><div></div><div></div></div>
										</div>";
					} elseif ( $posts_loading_type == 'load_on_scroll' && rtTPG()->hasPro() ) {
						$htmlUtility .= "<div class='rt-infinite-action'>
												<div class='rt-infinite-loading la-fire la-2x'>
													<div></div>
													<div></div>
													<div></div>
												</div>
											</div>";
					}
				}
			}

			if ( $htmlUtility ) {
				$l4toggle = null;
				if ( $layout == 'layout4' ) {
					$l4toggle = "data-l4toggle='{$this->l4toggle}'";
				}
				$html .= "<div class='rt-pagination-wrap' data-total-pages='{$gridQuery->max_num_pages}' data-posts-per-page='{$args['posts_per_page']}' data-type='{$posts_loading_type}' {$l4toggle} >" . $htmlUtility . '</div>';
			}

			$html .= '</div>'; // container rt-tpg.

			wp_reset_postdata();

			$scriptGenerator                  = [];
			$scriptGenerator['layout']        = $layoutID;
			$scriptGenerator['rand']          = $rand;
			$scriptGenerator['scMeta']        = $scMeta;
			$scriptGenerator['isCarousel']    = $isCarousel;
			$scriptGenerator['isSinglePopUp'] = $isSinglePopUp;
			$scriptGenerator['isWooCom']      = $isWooCom;
			$this->scA[]                      = $scriptGenerator;

			add_action( 'wp_footer', [ $this, 'register_sc_scripts' ] );

			// Script Load Conditionally
			$script = [];
			$style  = [];

			array_push( $script, 'jquery' );
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );

			if ( 'masonry' == $gridType || $isIsotope ) {
				array_push( $script, 'rt-isotope-js' );
			}

			array_push( $script, 'imagesloaded' );
			array_push( $script, 'rt-tpg' );

			// Pro Scripts and Styles.
			if ( rtTPG()->hasPro() ) {

				if ( isset( $posts_loading_type ) && 'pagination_ajax' == $posts_loading_type ) {
					array_push( $script, 'rt-pagination' );
				}

				if ( $layout == 'layout17' || 'popup' == $linkType ) {
					array_push( $style, 'rt-magnific-popup' );
					array_push( $script, 'rt-magnific-popup' );
				}

				if ( 'popup' == $linkType ) {
					array_push( $script, 'rt-scrollbar' );
				}

				if ( class_exists( 'WooCommerce' ) ) {
					array_push( $script, 'rt-jzoom' );
				}
			}

			array_push( $style, 'rt-tpg-shortcode' );

			if ( ( $isCarousel && rtTPG()->hasPro() ) || $isWooCom ) {
				array_push( $style, 'swiper' );
				array_push( $script, 'swiper' );
			}

			if ( rtTPG()->hasPro() ) {
				array_push( $script, 'rt-tpg-pro' );
			}

			if ( isset( $settings['tpg_load_script'] ) ) {
				wp_enqueue_style( $style );
			}

			wp_enqueue_script( $script );

		} else {
			$html .= '<p>' . esc_html__( 'No shortCode found', 'the-post-grid' ) . '</p>';
		}

		// restriction issue.
		$restriction = ( ! empty( $scMeta['restriction_user_role'] ) ? $scMeta['restriction_user_role'] : [] );
		if ( ! empty( $restriction ) ) {
			if ( is_user_logged_in() ) {
				$currentUserRoles = Fns::getCurrentUserRoles();

				if ( in_array( 'administrator', $currentUserRoles ) ) {
					$html = $html;
				} else {
					if ( count( array_intersect( $restriction, $currentUserRoles ) ) ) {
						$html = $html;
					} else {
						$html = '<p>' . esc_html__(
								'You are not permitted to view this content.',
								'the-post-grid'
							) . '</p>';
					}
				}
			} else {
				$html = '<p>' . esc_html__( 'This is a restricted content, you need to logged in to view this content.', 'the-post-grid' ) . '</p>';
			}
		}

		return $html;
	}
}
