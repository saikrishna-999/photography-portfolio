<?php
/**
 * Base Abstract Class
 *
 * @package RT_TPG
 */

use Elementor\Widget_Base;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;


// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Base Abstract Class
 */
abstract class Custom_Widget_Base extends Widget_Base {
	public $tpg_name;
	public $tpg_base;
	public $tpg_category;
	public $tpg_archive_category;
	public $tpg_icon;
	public $tpg_dir;
	public $tpg_pro_dir;
	public $is_post_layout;
	public $pro_label;
	public $get_pro_message;
	public $prefix;
	public $last_post_id;

	public function __construct( $data = [], $args = null ) {
		$this->tpg_category         = RT_THE_POST_GRID_PLUGIN_SLUG . '-elements'; // Category /@dev.
		$this->tpg_archive_category = 'tpg-block-builder-widgets';
		$this->tpg_icon             = 'eicon-gallery-grid tpg-grid-icon';
		$this->tpg_dir              = dirname( ( new ReflectionClass( $this ) )->getFileName() );
		$this->tpg_pro_dir          = null;
		$this->pro_label            = null;
		$this->is_post_layout       = null;
		$this->get_pro_message      = null;
		$this->last_post_id         = Fns::get_last_post_id();

		if ( ! rtTPG()->hasPro() ) {
			$this->pro_label       = sprintf( '<span class="tpg-pro-label">%s</span>', esc_html__( 'Pro', 'the-post-grid' ) );
			$this->is_post_layout  = ' the-post-grid-pro-needed';
			$this->get_pro_message = 'Please <a target="_blank" href="' . esc_url( rtTpg()->proLink() ) . '">upgrade</a> to pro for more options';
		}

		parent::__construct( $data, $args );
	}

	public function get_pro_message( $message = 'more options.' ) {
		if ( rtTPG()->hasPro() ) {
			return;
		}

		return 'Please <a target="_blank" href="' . esc_url( rtTpg()->proLink() ) . '">upgrade</a> to pro for ' . esc_html( $message );
	}

	public function get_name() {
		return $this->tpg_base;
	}

	public function get_title() {
		return $this->tpg_name;
	}

	public function get_icon() {
		return $this->tpg_icon;
	}

	public function get_categories() {
		return [ $this->tpg_category ];
	}

	public function pro_label() {
		if ( ! rtTPG()->hasPro() ) {
			return esc_html__( '[Pro]', 'the-post-grid' );
		}
		return '';
	}


	// post category list.
	function tpg_category_list() {
		$categories = get_categories( [ 'hide_empty' => false ] );
		$lists      = [];
		foreach ( $categories as $category ) {
			$lists[ $category->cat_ID ] = $category->name;
		}

		return $lists;
	}

	// post tags lists.
	public function tpg_tag_list() {
		$tags     = get_tags( [ 'hide_empty' => false ] );
		$tag_list = [];
		foreach ( $tags as $tag ) {
			$tag_list[ $tag->slug ] = $tag->name;
		}

		return $tag_list;
	}

	// Get Custom post category:.
	protected function tpg_get_categories_by_slug( $cat ) {
		$terms   = get_terms(
			[
				'taxonomy'   => $cat,
				'hide_empty' => true,
			]
		);
		$options = [ '0' => esc_html__( 'All Categories', 'the-post-grid' ) ];

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		return $options;
	}


	public function get_all_post_type() {
		$post_types = get_post_types( [], 'objects' );
		$pt_list    = [];

		foreach ( $post_types as $type ) {
			if ( isset( $type->rewrite->slug ) ) {
				$pt_list[ $type->rewrite->slug ] = $type->rewrite->name;
			}
		}

		return $pt_list;
	}

	public static function get_post_types() {
		$post_types = get_post_types(
			[
				'public'            => true,
				'show_in_nav_menus' => true,
			],
			'objects'
		);
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		return array_diff_key( $post_types, [ 'elementor_library', 'attachment' ] );
	}

	/**
	 * Get Excluded Taxonomy
	 *
	 * @return string[]
	 */
	public static function get_excluded_taxonomy() {
		return [
			'post_format',
			'nav_menu',
			'link_category',
			'wp_theme',
			'elementor_library_type',
			'elementor_library_type',
			'elementor_library_category',
			'product_visibility',
			'product_shipping_class',
		];
	}

	/**
	 * Get Filter markup
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public function get_frontend_filter_markup( $data ) {
		if ( ! rtTPG()->hasPro()
			|| ! ( $data['show_taxonomy_filter'] == 'show' || $data['show_author_filter'] == 'show' || $data['show_order_by'] == 'show'
					|| $data['show_sort_order'] == 'show'
					|| $data['show_search'] == 'show' )
		) {
			return;
		}

		$html             = null;
		$wrapperContainer = $wrapperClass = $itemClass = $filter_btn_item_per_page = '';

		if ( 'carousel' === $data['filter_btn_style'] ) {
			$wrapperContainer         = 'swiper';
			$wrapperClass             = 'swiper-wrapper';
			$itemClass                = 'swiper-slide';
			$filter_btn_mobile        = isset( $data['filter_btn_item_per_page_mobile'] ) ? $data['filter_btn_item_per_page_mobile'] : 'auto';
			$filter_btn_tablet        = isset( $data['filter_btn_item_per_page_tablet'] ) ? $data['filter_btn_item_per_page_tablet'] : 'auto';
			$filter_btn_item_per_page = "data-per-page = '{$data['filter_btn_item_per_page']}' data-per-page-mobile = '{$filter_btn_mobile}' data-per-tablet = '{$filter_btn_tablet}'";
		}

		$html .= "<div class='rt-layout-filter-container rt-clear'><div class='rt-filter-wrap'>";

		if ( 'show' == $data['show_author_filter'] || 'show' == $data['show_taxonomy_filter'] ) {
			$html .= "<div class='filter-left-wrapper {$wrapperContainer}' {$filter_btn_item_per_page}>";
		}

		$selectedSubTermsForButton = null;

		$filterType = $data['filter_type'];
		$post_count = ( 'yes' == $data['filter_post_count'] ) ? true : false;

		if ( 'show' == $data['show_taxonomy_filter'] ) {
			$postCountClass = ( $post_count ? ' has-post-count' : null );
			$allSelect      = ' selected';
			$isTermSelected = false;

			$taxFilterOperator = $data['relation'];

			$section_term_key = $data['post_type'] . '_filter_taxonomy';
			$taxFilter        = $data[ $section_term_key ];

			$taxonomy_label = '';

			if ( $taxFilter ) {
				$taxonomy_details = get_taxonomy( $taxFilter );
				$taxonomy_label   = $taxonomy_details->label;
			}

			$default_term_key = $taxFilter . '_default_terms';
			$default_term     = $data[ $default_term_key ];

			$allText = $data['tax_filter_all_text'] ? $data['tax_filter_all_text'] : esc_html__( 'All ', 'the-post-grid' ) . $taxonomy_label;

			$_taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );
			$terms       = [];

			foreach ( $_taxonomies as $index => $object ) {
				if ( $object->name != $taxFilter ) {
					continue;
				}

				$setting_key = $object->name . '_ids';

				if ( ! empty( $data[ $setting_key ] ) ) {
					$terms = $data[ $setting_key ];
				} else {
					$terms = get_terms(
						[
							'taxonomy' => $taxFilter,
							'fields'   => 'ids',
						]
					);
				}
			}
			$taxFilterTerms = $terms;

			if ( $default_term && $taxFilter ) {
				$isTermSelected = true;
				$allSelect      = null;
			}

			if ( $filterType == 'dropdown' ) {
				$html            .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-taxonomy='{$taxFilter}' data-filter='taxonomy'>";
				$termDefaultText  = $allText;
				$dataTerm         = 'all';
				$htmlButton       = '';
				$selectedSubTerms = null;
				$pCount           = 0;

				if ( ! empty( $terms ) ) {
					$i = 0;

					foreach ( $terms as $term_id ) {
						$term   = get_term( $term_id, $taxFilter, ARRAY_A );
						$id     = $term['term_id'];
						$pCount = $pCount + $term['count'];
						$sT     = null;

						if ( $data['tgp_filter_taxonomy_hierarchical'] == 'yes' ) {
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
											<span class='rt-text'>" . $allText . "</span>
											<i class='fa fa-angle-down rt-arrow-angle' aria-hidden='true'></i>
										</span>";
								$sT .= '<span class="term-dropdown rt-filter-dropdown">';
								$sT .= $item;
								$sT .= '</span>';
								$sT .= '</div>';
							}

							if ( $default_term === $id ) {
								$selectedSubTerms = $sT;
							}
						}
						$postCount = ( $post_count ? " (<span class='rt-post-count'>{$term['count']}</span>)" : null );

						if ( $default_term && $default_term == $id ) {
							$termDefaultText = $term['name'] . $postCount;
							$dataTerm        = $id;
						}

						if ( is_array( $taxFilterTerms ) && ! empty( $taxFilterTerms ) ) {
							if ( in_array( $id, $taxFilterTerms ) ) {
								$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$id}'><span class='rt-text'>{$term['name']}{$postCount}</span>{$sT}</span>";
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

					if ( ! $default_term ) {
						$termDefaultText = $termDefaultText;
					}
				}

				if ( 'yes' == $data['tpg_hide_all_button'] ) {
					$htmlButton = "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'><span class='rt-text'>" . $allText . '</span></span>' . $htmlButton;
				}

				$htmlButton = sprintf( '<span class="term-dropdown rt-filter-dropdown">%s</span>', $htmlButton );

				$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataTerm . '">
									<span class="rt-text">' . $termDefaultText . '</span>
									<i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
								</span>';

				$html .= $showAllhtml . $htmlButton;
				$html .= '</div>' . $selectedSubTerms;
			} else {
				// if Button the execute.
				// $termDefaultText = $allText;

				$bCount = 0;
				$bItems = null;

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term_id ) {
						$term   = get_term( $term_id, $taxFilter, ARRAY_A );
						$id     = $term['term_id'];
						$bCount = $bCount + absint( $term['count'] );
						$sT     = null;

						if ( $data['tgp_filter_taxonomy_hierarchical'] == 'yes' && $data['filter_btn_style'] === 'default' && $data['filter_type'] == 'button' ) {
							$subTerms = Fns::rt_get_all_term_by_taxonomy( $taxFilter, true, $id );

							if ( ! empty( $subTerms ) ) {
								$sT .= "<div class='rt-filter-sub-tax sub-button-group '>";

								foreach ( $subTerms as $stId => $t ) {
									$sTPostCount = ( $post_count ? " (<span class='rt-post-count'>{$t['count']}</span>)" : null );
									$sT         .= "<span class='term-button-item rt-filter-button-item ' data-term='{$stId}'>{$t['name']}{$sTPostCount}</span>";
								}

								$sT .= '</div>';

								if ( $default_term === $id ) {
									$selectedSubTermsForButton = $sT;
								}
							}
						}
						$postCount    = ( $post_count ? " (<span class='rt-post-count'>{$term['count']}</span>)" : null );
						$termSelected = null;

						if ( $isTermSelected && $id == $default_term ) {
							$termSelected = ' selected';
						}

						if ( is_array( $taxFilterTerms ) && ! empty( $taxFilterTerms ) ) {
							if ( in_array( $id, $taxFilterTerms ) ) {
								$bItems .= "<span class='term-button-item rt-filter-button-item {$termSelected} {$itemClass}' data-term='{$id}'>{$term['name']}{$postCount}{$sT}</span>";
							}
						} else {
							$bItems .= "<span class='term-button-item rt-filter-button-item {$termSelected} {$itemClass}' data-term='{$id}'>{$term['name']}{$postCount}{$sT}</span>";
						}
					}
				}
				$html .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-button-wrap{$postCountClass} {$wrapperClass}' data-taxonomy='{$taxFilter}' data-filter='taxonomy'>";

				if ( 'yes' == $data['tpg_hide_all_button'] ) {
					$html .= "<span class='term-button-item rt-filter-button-item {$allSelect} {$itemClass}' data-term='all'>" . $allText . '</span>';
				}

				$html .= $bItems;

				$html .= '</div>';

				if ( 'carousel' === $data['filter_btn_style'] ) {
					$html .= '<div class="swiper-navigation"><div class="swiper-button-prev slider-btn"></div><div class="swiper-button-next slider-btn"></div></div>';
				}
			}
		}

		// TODO: Author filter.
		if ( 'show' == $data['show_author_filter'] ) {
			$user_el = $data['author'];

			$filterAuthors = $user_el;

			if ( ! empty( $user_el ) ) {
				$users = get_users( apply_filters( 'tpg_author_arg', [ 'include' => $user_el ] ) );
			} else {
				$users = get_users( apply_filters( 'tpg_author_arg', [] ) );
			}

			$allText        = $allText = $data['author_filter_all_text'] ? $data['author_filter_all_text'] : esc_html__( 'All Users', 'the-post-grid' );
			$allSelect      = ' selected';
			$isTermSelected = true;

			if ( $filterType == 'dropdown' ) {
				$html           .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-filter='author'>";
				$termDefaultText = $allText;
				$dataAuthor      = 'all';
				$htmlButton      = '';
				$htmlButton     .= '<span class="author-dropdown rt-filter-dropdown">';
				$htmlButton     .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'>" . $allText . '</span>';

				if ( ! empty( $users ) ) {
					foreach ( $users as $user ) {
						$user_post_count = false;
						$post_count ? '(' . count_user_posts( $user->ID, $data['post_type'] ) . ')' : null;

						if ( is_array( $filterAuthors ) && ! empty( $filterAuthors ) ) {
							if ( in_array( $user->ID, $filterAuthors ) ) {
								if ( $default_term == $user->ID ) {
									$termDefaultText = $user->display_name;
									$dataTerm        = $user->ID;
								} else {
									$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'>{$user->display_name} <span class='rt-text'>{$user_post_count}</span></span>";
								}
							}
						} else {
							if ( $default_term == $user->ID ) {
								$termDefaultText = $user->display_name;
								$dataTerm        = $user->ID;
							} else {
								$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'><span class='rt-text'>{$user->display_name} {$user_post_count}</span></span>";
							}
						}
					}
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
								$bItems .= "<span class='author-button-item rt-filter-button-item' data-term='{$user->ID}'>{$user->display_name}</span>";
							}
						} else {
							$bItems .= "<span class='author-button-item rt-filter-button-item' data-term='{$user->ID}'>{$user->display_name}</span>";
						}
					}
				}

				$html .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-button-wrap{$postCountClass}' data-filter='author'>";
				$html .= "<span class='author-button-item rt-filter-button-item {$allSelect}' data-author='all'>" . $allText . '</span>';
				$html .= $bItems;
				$html .= '</div>';
			}
		}

		if ( 'show' == $data['show_author_filter'] || 'show' == $data['show_taxonomy_filter'] ) {
			$html .= '</div>';
		}

		if ( 'show' == $data['show_order_by'] || 'show' == $data['show_sort_order'] || 'show' == $data['show_search'] ) {
			$html .= "<div class='filter-right-wrapper'>";
		}

		// TODO: Order Filter.
		if ( 'show' == $data['show_sort_order'] ) {
			$action_order = ( $data['order'] ? strtoupper( $data['order'] ) : 'DESC' );
			$html        .= '<div class="rt-filter-item-wrap rt-sort-order-action" data-filter="order">';
			$html        .= "<span class='rt-sort-order-action-arrow' data-sort-order='{$action_order}'>&nbsp;<span></span></span>";
			$html        .= '</div>';
		}

		// TODO: Orderby Filter.
		if ( 'show' == $data['show_order_by'] ) {
			$wooFeature     = ( $data['post_type'] == 'product' ? true : false );
			$orders         = Options::rtPostOrderBy( $wooFeature );
			$action_orderby = ( ! empty( $data['orderby'] ) ? $data['orderby'] : 'none' );

			if ( $action_orderby == 'none' ) {
				$action_orderby_label = esc_html__( 'Sort By', 'the-post-grid' );
			} elseif ( in_array( $action_orderby, array_keys( Options::rtMetaKeyType() ) ) ) {
				$action_orderby_label = esc_html__( 'Meta value', 'the-post-grid' );
			} else {
				$action_orderby_label = esc_html__( 'By ', 'the-post-grid' ) . $action_orderby;
			}

			if ( $action_orderby !== 'none' ) {
				$orders['none'] = esc_html__( 'Sort By', 'the-post-grid' );
			}

			$html .= '<div class="rt-filter-item-wrap rt-order-by-action rt-filter-dropdown-wrap" data-filter="orderby">';
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

		// TODO: Search Filter.
		if ( 'show' == $data['show_search'] ) {
			$html .= '<div class="rt-filter-item-wrap rt-search-filter-wrap" data-filter="search">';
			$html .= sprintf( '<input type="text" class="rt-search-input" placeholder="%s">', esc_html__( 'Search...', 'the-post-grid' ) );
			$html .= "<span class='rt-action'>&#128269;</span>";
			$html .= "<span class='rt-loading'></span>";
			$html .= '</div>';
		}

		if ( 'show' == $data['show_order_by'] || 'show' == $data['show_sort_order'] || 'show' == $data['show_search'] ) {
			$html .= '</div>';
		}

		$html .= "</div>$selectedSubTermsForButton</div>";

		return $html;
	}

	/**
	 * Get Post Pagination, Load more & Scroll markup
	 *
	 * @param $query
	 * @param $data
	 *
	 * @return false|string|void
	 */
	public function get_pagination_markup( $query, $data ) {
		if ( 'show' !== $data['show_pagination'] ) {
			return;
		}

		$htmlUtility = null;

		$posts_loading_type = $data['pagination_type'];
		$posts_per_page     = ( isset( $data['display_per_page'] ) && $data['display_per_page'] ) ? $data['display_per_page']
			: ( $data['post_limit'] ? $data['post_limit'] : get_option( 'posts_per_page' ) );
		$hide               = ( $query->max_num_pages < 2 ? ' rt-hidden-elm' : null );

		if ( $posts_loading_type == 'pagination' ) {
			$htmlUtility .= Fns::rt_pagination( $query );
		} elseif ( rtTPG()->hasPro() && $posts_loading_type == 'pagination_ajax' ) { // && ! $isIsotope
			$htmlUtility .= "<div class='rt-page-numbers'></div>";
		} elseif ( rtTPG()->hasPro() && $posts_loading_type == 'load_more' ) {
			$load_more_btn_text = $data['load_more_button_text'] ? esc_html( $data['load_more_button_text'] ) : esc_html__( 'Load More', 'the-post-grid' );
			$htmlUtility       .= "<div class='rt-loadmore-btn rt-loadmore-action rt-loadmore-style{$hide}'>
										<span class='rt-loadmore-text'>" . $load_more_btn_text . "</span>
										<div class='rt-loadmore-loading rt-ball-scale-multiple rt-2x'><div></div><div></div><div></div></div>
									</div>";
		} elseif ( rtTPG()->hasPro() && $posts_loading_type == 'load_on_scroll' ) {
			$htmlUtility .= "<div class='rt-infinite-action'>
								<div class='rt-infinite-loading la-fire la-2x'>
									<div></div><div></div><div></div>
								</div>
							</div>";
		}

		if ( $htmlUtility ) {
			$html = "<div class='rt-pagination-wrap' data-total-pages='{$query->max_num_pages}' data-posts-per-page='{$posts_per_page}' data-type='{$posts_loading_type}' >" . $htmlUtility . '</div>';

			return $html;
		}

		return false;
	}

	/**
	 * Get Popup Modal Markup
	 */
	public function get_modal_markup() {
		$html  = null;
		$html .= '<div class="md-modal rt-md-effect" id="rt-modal">
					<div class="md-content">
						<div class="rt-md-content-holder"></div>
						<div class="md-cls-btn">
							<button class="md-close"><i class="fa fa-times" aria-hidden="true"></i></button>
						</div>
					</div>
				</div>';
		$html .= "<div class='md-overlay'></div>";

		Fns::print_html( $html );
	}

	/**
	 * Get Archive page title
	 */
	public function get_archive_title() {
		$queried_obj = get_queried_object();

		if ( is_tag() || is_category() ) {
			echo esc_html( $queried_obj->name );
		} elseif ( is_author() ) {
			echo esc_html( $queried_obj->display_name );
		} elseif ( is_date() ) {
			$year        = get_query_var( 'year' );
			$monthnum    = get_query_var( 'monthnum' );
			$day         = get_query_var( 'day' );
			$time_string = $year . '/' . $monthnum . '/' . $day;
			$time_stamp  = strtotime( $time_string );
			Fns::print_html( gmdate( get_option( 'date_format' ), $time_stamp ) );
		}
	}

	/**
	 * Get Section Title
	 *
	 * @param $data
	 */
	public function get_section_title( $data ) {
		if ( 'show' != $data['show_section_title'] ) {
			return;
		}

		$_is_link = false;

		if ( ! empty( $data['section_title_link']['url'] ) ) {
			$this->add_link_attributes( 'section_title_link', $data['section_title_link'] );
			$_is_link = true;
		}

		$this->add_inline_editing_attributes( 'section_title_text', 'none' );
		ob_start();
		?>

		<div class="tpg-widget-heading-wrapper rt-clear heading-<?php echo esc_attr( $data['section_title_style'] ); ?> ">
			<span class="tpg-widget-heading-line line-left"></span>

			<?php printf( "<%s class='tpg-widget-heading'>", esc_attr( Fns::print_validated_html_tag( $data['section_title_tag'] ) ) ); ?>

			<?php
			if ( $_is_link ) :
				?>
			<a <?php Fns::print_html( $this->get_render_attribute_string( 'section_title_link' ), true ); ?>>
				<?php endif; ?>

				<?php
				if ( 'page_title' == $data['section_title_source'] ) {
					$archive_prefix = $data['title_prefix'] ? $data['title_prefix'] . ' ' : null;
					$archive_suffix = $data['title_suffix'] ? ' ' . $data['title_suffix'] : null;
					printf( "<span class='prefix-text'>%s</span>", esc_html( $archive_prefix ) );

					if ( is_archive() ) {
						Fns::get_archive_title();
					} elseif ( is_search() ) {
						echo esc_html( get_query_var( 's' ) );
					} else {
						the_title();
					}

					printf( "<span class='suffix-text'>%s</span>", esc_html( $archive_suffix ) );
				} else {
					?>
					<span <?php $this->print_render_attribute_string( 'section_title_text' ); ?>>
						<?php $this->print_unescaped_setting( 'section_title_text' ); ?>
					</span>
					<?php
				}
				?>

				<?php if ( $_is_link ) : ?>
			</a>

		<?php endif; ?>
			<?php printf( '</%s>', esc_attr( Fns::print_validated_html_tag( $data['section_title_tag'] ) ) ); ?>
			<span class="tpg-widget-heading-line line-right"></span>
		</div>

		<?php if ( isset( $data['show_cat_desc'] ) && $data['show_cat_desc'] == 'yes' && category_description( Fns::get_last_category_id() ) ) : ?>
			<div class="tpg-category-description">
				<?php echo category_description( Fns::get_last_category_id() ); ?>
			</div>

		<?php endif; ?>

		<?php
		Fns::print_html( ob_get_clean() );
	}


	/**
	 * Get Post Data for render post
	 *
	 * @param $data
	 * @param $total_pages
	 * @param $posts_per_page
	 *
	 * @return array
	 */
	public function get_render_data_set( $data, $total_pages, $posts_per_page ) {
		$_prefix = $this->prefix;

		$data_set = [
			'block_type'                   => 'elementor',
			'prefix'                       => $_prefix,
			'grid_column'                  => $data[ $_prefix . '_column' ],
			'grid_column_tablet'           => isset( $data[ $_prefix . '_column_tablet' ] ) ? $data[ $_prefix . '_column_tablet' ] : '0',
			'grid_column_mobile'           => isset( $data[ $_prefix . '_column_mobile' ] ) ? $data[ $_prefix . '_column_mobile' ] : '0',
			'layout'                       => $data[ $_prefix . '_layout' ],
			'pagination_type'              => 'slider' === $_prefix ? 'slider' : $data['pagination_type'],
			'total_pages'                  => $total_pages,
			'posts_per_page'               => $posts_per_page,
			'layout_style'                 => isset( $data[ $_prefix . '_layout_style' ] ) ? $data[ $_prefix . '_layout_style' ] : '',
			'show_title'                   => $data['show_title'],
			'excerpt_type'                 => $data['excerpt_type'],
			'excerpt_limit'                => $data['excerpt_limit'],
			'excerpt_more_text'            => $data['excerpt_more_text'],
			'title_limit'                  => $data['title_limit'],
			'title_limit_type'             => $data['title_limit_type'],
			'title_visibility_style'       => $data['title_visibility_style'],
			'post_link_type'               => $data['post_link_type'],
			'link_target'                  => $data['link_target'],
			'hover_animation'              => isset( $data['hover_animation'] ) ? $data['hover_animation'] : '',
			'show_thumb'                   => $data['show_thumb'],
			'show_meta'                    => $data['show_meta'],
			'show_author'                  => $data['show_author'],
			'show_author_image'            => $data['show_author_image'],
			'show_meta_icon'               => $data['show_meta_icon'],
			'show_category'                => $data['show_category'],
			'show_date'                    => $data['show_date'],
			'show_tags'                    => $data['show_tags'],
			'show_comment_count'           => $data['show_comment_count'],
			'show_comment_count_label'     => isset( $data['show_comment_count_label'] ) ? $data['show_comment_count_label'] : '',
			'comment_count_label_singular' => isset( $data['comment_count_label_singular'] ) ? $data['comment_count_label_singular'] : '',
			'comment_count_label_plural'   => isset( $data['comment_count_label_plural'] ) ? $data['comment_count_label_plural'] : '',
			'show_post_count'              => $data['show_post_count'],
			'post_count_icon'              => $data['post_count_icon'],
			'show_excerpt'                 => $data['show_excerpt'],
			'show_read_more'               => $data['show_read_more'],
			'show_btn_icon'                => $data['show_btn_icon'],
			'show_social_share'            => $data['show_social_share'],
			'show_cat_icon'                => isset( $data['show_cat_icon'] ) ? $data['show_cat_icon'] : '',
			'is_thumb_linked'              => $data['is_thumb_linked'],
			'media_source'                 => $data['media_source'],
			'no_posts_found_text'          => isset( $data['no_posts_found_text'] ) ? $data['no_posts_found_text'] : '',
			'image_size'                   => $data['image_size'],
			'image_offset'                 => $data['image_offset_size'],
			'is_default_img'               => $data['is_default_img'],
			'default_image'                => $data['default_image'],
			'thumb_overlay_visibility'     => isset( $data['thumb_overlay_visibility'] ) ? $data['thumb_overlay_visibility'] : '',
			'overlay_type'                 => isset( $data['overlay_type'] ) ? $data['overlay_type'] : '',
			'title_tag'                    => $data['title_tag'],
			'post_type'                    => $data['post_type'],
			'meta_separator'               => $data['meta_separator'],
			'readmore_icon_position'       => $data['readmore_icon_position'],
			'read_more_label'              => $data['read_more_label'],
			'readmore_btn_icon'            => $data['readmore_btn_icon'],
			'category_position'            => $data['category_position'],
			'title_position'               => $data['title_position'],
			'category_style'               => $data['category_style'],
			'is_thumb_lightbox'            => $data['is_thumb_lightbox'],
			'light_box_icon'               => $data['light_box_icon'],
			'author_prefix'                => $data['author_prefix'],
			'cat_icon'                     => $data['cat_icon'],
			'tag_icon'                     => $data['tag_icon'],
			'date_icon'                    => $data['date_icon'],
			'user_icon'                    => $data['user_icon'],
			'meta_ordering'                => $data['meta_ordering'],
			'comment_icon'                 => $data['comment_icon'],
			'image_custom_dimension'       => ( $data['image_size'] == 'custom' && isset( $data['image_custom_dimension'] ) ) ? $data['image_custom_dimension'] : '',
			'img_crop_style'               => ( $data['image_size'] == 'custom' && isset( $data['img_crop_style'] ) ) ? $data['img_crop_style'] : '',
			'show_acf'                     => isset( $data['show_acf'] ) ? $data['show_acf'] : '',
		];

		$cf = Fns::is_acf();
		if ( $cf && rtTPG()->hasPro() ) {
			$post_type                       = $data['post_type'];
			$data_set['cf_group']            = $data[ $post_type . '_cf_group' ];
			$data_set['cf_hide_empty_value'] = $data['cf_hide_empty_value'];
			$data_set['cf_show_only_value']  = $data['cf_show_only_value'];
			$data_set['cf_hide_group_title'] = $data['cf_hide_group_title'];
		}

		return $data_set;
	}
}
