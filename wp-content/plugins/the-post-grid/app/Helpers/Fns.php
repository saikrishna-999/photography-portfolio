<?php
/**
 * Helper class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Helpers;

use RT\ThePostGrid\Models\Field;
use RT\ThePostGrid\Models\ReSizer;


// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Helper class.
 */
class Fns {

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}


	/**
	 * Render view
	 *
	 * @param string $viewName View name.
	 * @param array $args Args.
	 * @param boolean $return Include/return.
	 *
	 * @return string
	 */
	public static function view( $viewName, $args = [], $return = false ) {
		$file     = str_replace( '.', '/', $viewName );
		$file     = ltrim( $file, '/' );
		$viewFile = trailingslashit( RT_THE_POST_GRID_PLUGIN_PATH . '/resources' ) . $file . '.php';

		if ( ! file_exists( $viewFile ) ) {
			return new \WP_Error(
				'brock',
				sprintf(
				/* translators: %s File name */
					esc_html__( '%s file not found', 'the-post-grid' ),
					$viewFile
				)
			);
		}

		if ( $args ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		if ( $return ) {
			ob_start();
			include $viewFile;

			return ob_get_clean();
		}

		include $viewFile;
	}

	/**
	 * Update post view
	 *
	 * @param integer $post_id Listing ID.
	 *
	 * @return void
	 */
	public static function update_post_views_count( $post_id ) {

		if ( ! $post_id && is_admin() ) {
			return;
		}

		$user_ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // retrieve the current IP address of the visitor.
		$key     = 'tpg_cache_' . $user_ip . '_' . $post_id;
		$value   = [ $user_ip, $post_id ];
		$visited = get_transient( $key );

		if ( false === ( $visited ) ) {
			// set_transient( $key, $value, HOUR_IN_SECONDS * 12 ); // store the unique key, Post ID & IP address for 12 hours if it does not exist.
			set_transient( $key, $value, HOUR_IN_SECONDS * 12 ); // store the unique key, Post ID & IP address for 12 hours if it does not exist.

			// now run post views function.
			$count_key = self::get_post_view_count_meta_key();
			$count     = get_post_meta( $post_id, $count_key, true );

			if ( '' == $count ) {
				update_post_meta( $post_id, $count_key, 1 );
			} else {
				$count = absint( $count );
				$count ++;

				update_post_meta( $post_id, $count_key, $count );
			}
		}
	}

	public static function get_pages() {
		$page_list = [];
		$pages     = get_pages(
			[
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'hierarchical' => 0,
			]
		);
		foreach ( $pages as $page ) {
			$page_list[ $page->ID ] = ! empty( $page->post_title ) ? $page->post_title : '#' . $page->ID;
		}

		return $page_list;
	}

	/**
	 * Template Content
	 *
	 * @param string $template_name Template name.
	 * @param array $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 */
	public static function get_template( $template_name, $args = null, $template_path = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		$located = self::locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			/* translators: %s template */
			self::doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'the-post-grid' ), '<code>' . $located . '</code>' ), '1.0' );

			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'rttpg_get_template', $located, $template_name, $args );

		do_action( 'rttpg_before_template_part', $template_name, $located, $args );
		include $located;

		do_action( 'rttpg_after_template_part', $template_name, $located, $args );
	}

	/**
	 * Get template content and return
	 *
	 * @param string $template_name Template name.
	 * @param array $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 *
	 * @return string
	 */
	public static function get_template_html( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		ob_start();
		self::get_template( $template_name, $args, $template_path, $default_path );

		return ob_get_clean();
	}

	/**
	 * Locate template.
	 *
	 * @param string $template_name Template.
	 * @param string $template_path Path.
	 * @param string $default_path Default path.
	 *
	 * @return mixed|void
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		$template_name = $template_name . '.php';

		if ( ! $template_path ) {
			$template_path = rtTPG()->get_template_path();
		}

		if ( ! $default_path ) {
			$default_path = rtTPG()->default_template_path() . '/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template_files   = [];
		$template_files[] = trailingslashit( $template_path ) . $template_name;

		$template = locate_template( apply_filters( 'rttpg_locate_template_files', $template_files, $template_name, $template_path, $default_path ) );

		// Get default template/.
		if ( ! $template ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		return apply_filters( 'rttpg_locate_template', $template, $template_name );
	}

	/**
	 * Mark something as being incorrectly called.
	 *
	 * @param string $function — The function that was called.
	 * @param string $message — A message explaining what has been done incorrectly.
	 * @param string $version — The version of WordPress where the message was added.
	 *
	 * @return void
	 */
	public static function doing_it_wrong( $function, $message, $version ) {
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
		_doing_it_wrong( $function, $message, $version ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Verify nonce.
	 *
	 * @return bool
	 */
	public static function verifyNonce() {
		$nonce     = isset( $_REQUEST[ rtTPG()->nonceId() ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ rtTPG()->nonceId() ] ) ) : null;
		$nonceText = rtTPG()->nonceText();

		if ( ! wp_verify_nonce( $nonce, $nonceText ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $data
	 * @param $temp_path
	 *
	 * @return void
	 */
	public static function tpg_template( $data, $temp_path = 'elementor' ) {
		$layout = str_replace( '-2', '', $data['layout'] );

		$template_name = '/the-post-grid/' . $temp_path . '/' . $layout . '.php';
		if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
			$file = get_stylesheet_directory() . $template_name;
		} elseif ( file_exists( get_template_directory() . $template_name ) ) {
			$file = get_template_directory() . $template_name;
		} else {
			$file = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/' . $temp_path . '/' . $layout . '.php';
			if ( ! file_exists( $file ) ) {
				if ( rtTPG()->hasPro() ) {
					$file = RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/templates/' . $temp_path . '/' . $layout . '.php';
				} else {
					$layout = substr( $layout, 0, - 1 );
					$layout = strpos( $layout, '1' ) ? str_replace( '1', '', $layout ) : $layout;
					$file   = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/' . $temp_path . '/' . $layout . '1.php';
				}
			}
		}
		if ( ! file_exists( $file ) ) {
			/* translators: %s template */
			self::doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'the-post-grid' ), '<code>' . $file . '</code>' ), '1.0' );

			return;
		}
		include $file;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function tpg_template_path( $data, $temp_path = 'elementor' ) {
		$layout        = str_replace( '-2', '', $data['layout'] );
		$template_name = '/the-post-grid/' . $temp_path . '/' . $layout . '.php';
		$path          = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/' . $temp_path . '/';
		if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
			$path = get_stylesheet_directory() . '/the-post-grid/' . $temp_path . '/';
		} elseif ( file_exists( get_template_directory() . $template_name ) ) {
			$path = get_template_directory() . '/the-post-grid/' . $temp_path . '/';
		} else {
			$template_path = RT_THE_POST_GRID_PLUGIN_PATH . '/templates/' . $temp_path . '/' . $layout . '.php';

			if ( ! file_exists( $template_path ) && rtTPG()->hasPro() ) {
				$path = RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/templates/' . $temp_path . '/';
			}
		}

		return $path;
	}

	/**
	 * Get Post Pagination, Load more & Scroll markup
	 *
	 * @param $query
	 * @param $data
	 *
	 * @return false|string|void
	 */
	public static function get_pagination_markup( $query, $data ) {
		if ( 'show' !== $data['show_pagination'] ) {
			return;
		}

		$htmlUtility = null;

		$posts_loading_type = $data['pagination_type'];

		$posts_per_page = ( isset( $data['display_per_page'] ) && $data['display_per_page'] ) ? $data['display_per_page'] : ( $data['post_limit'] ?? get_option( 'posts_per_page' ) );

		if ( ! empty( $data['is_builder'] ) && 'yes' === $data['is_builder'] ) {
			$posts_per_page = get_option( 'posts_per_page' );
		}

		$hide = ( $query->max_num_pages < 2 ? ' rt-hidden-elm' : null );

		if ( $posts_loading_type == 'pagination' ) {
			$htmlUtility .= self::rt_pagination( $query );
		} elseif ( rtTPG()->hasPro() && $posts_loading_type == 'pagination_ajax' ) { // && ! $isIsotope
			$htmlUtility .= "<div class='rt-page-numbers'></div>";
		} elseif ( rtTPG()->hasPro() && $posts_loading_type == 'load_more' ) {
			$load_more_button_text = $data['load_more_button_text'] ? $data['load_more_button_text'] : __( 'Load More', 'the-post-grid' );
			$htmlUtility           .= "<div class='rt-loadmore-btn rt-loadmore-action rt-loadmore-style{$hide}'>
											<span class='rt-loadmore-text'>" . $load_more_button_text . "</span>
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
			$html = "<div class='rt-pagination-wrap' data-total-pages='{$query->max_num_pages}' data-posts-per-page='{$posts_per_page}' data-type='{$posts_loading_type}' >"
			        . $htmlUtility . '</div>';

			return $html;
		}

		return false;
	}

	/**
	 * @param $data
	 * @param $total_pages
	 * @param $posts_per_page
	 * @param $_prefix
	 * @param $is_gutenberg
	 *
	 * @return array
	 */
	public static function get_render_data_set( $data, $total_pages, $posts_per_page, $_prefix, $is_gutenberg = '' ) {

		if ( ! empty( $data['is_builder'] ) && $data['is_builder'] === 'yes' ) {
			$posts_per_page = get_option( 'posts_per_page' );
		}

		$data_set = [
			'block_type'                   => 'elementor',
			'is_gutenberg'                 => $is_gutenberg,
			'prefix'                       => $_prefix,
			'grid_column'                  => $data[ $_prefix . '_column' ] ?? '0',
			'grid_column_tablet'           => $data[ $_prefix . '_column_tablet' ] ?? '0',
			'grid_column_mobile'           => $data[ $_prefix . '_column_mobile' ] ?? '0',
			'layout'                       => $data[ $_prefix . '_layout' ],
			'pagination_type'              => 'slider' === $_prefix ? 'slider' : $data['pagination_type'],
			'total_pages'                  => $total_pages,
			'posts_per_page'               => $posts_per_page,
			'layout_style'                 => $data[ $_prefix . '_layout_style' ] ?? '',
			'show_title'                   => $data['show_title'],
			'excerpt_type'                 => $data['excerpt_type'],
			'excerpt_limit'                => $data['excerpt_limit'],
			'excerpt_more_text'            => $data['excerpt_more_text'],
			'title_limit'                  => $data['title_limit'],
			'title_limit_type'             => $data['title_limit_type'],
			'title_visibility_style'       => $data['title_visibility_style'],
			'post_link_type'               => $data['post_link_type'],
			'link_target'                  => $data['link_target'],
			'hover_animation'              => $data['hover_animation'] ?? '',
			'show_thumb'                   => $data['show_thumb'],
			'show_meta'                    => $data['show_meta'],
			'show_author'                  => $data['show_author'],
			'show_author_image'            => $data['show_author_image'],
			'author_icon_visibility'       => $data['author_icon_visibility'],
			'show_meta_icon'               => $data['show_meta_icon'],
			'show_category'                => $data['show_category'],
			'show_date'                    => $data['show_date'],
			'show_tags'                    => $data['show_tags'],
			'show_comment_count'           => $data['show_comment_count'],
			'show_comment_count_label'     => $data['show_comment_count_label'] ?? '',
			'comment_count_label_singular' => $data['comment_count_label_singular'] ?? '',
			'comment_count_label_plural'   => $data['comment_count_label_plural'] ?? '',
			'show_post_count'              => $data['show_post_count'],
			'post_count_icon'              => $data['post_count_icon'] ?? '',
			'show_excerpt'                 => $data['show_excerpt'],
			'show_read_more'               => $data['show_read_more'],
			'show_btn_icon'                => $data['show_btn_icon'],
			'show_social_share'            => $data['show_social_share'],
			'show_cat_icon'                => $data['show_cat_icon'] ?? '',
			'is_thumb_linked'              => $data['is_thumb_linked'],
			'media_source'                 => $data['media_source'],
			'no_posts_found_text'          => isset( $data['no_posts_found_text'] ) ? esc_html( $data['no_posts_found_text'] ) : '',
			'image_size'                   => $data['image_size'],
			'image_offset'                 => $data['image_offset_size'],
			'is_default_img'               => $data['is_default_img'],
			'default_image'                => $data['default_image'],
			'thumb_overlay_visibility'     => $data['thumb_overlay_visibility'] ?? '',
			'overlay_type'                 => $data['overlay_type'] ?? '',
			'title_tag'                    => $data['title_tag'],
			'post_type'                    => $data['post_type'],
			'meta_separator'               => $data['meta_separator'],
			'readmore_icon_position'       => $data['readmore_icon_position'],
			'read_more_label'              => $data['read_more_label'],
			'readmore_btn_icon'            => $data['readmore_btn_icon'],
			'category_position'            => $data['category_position'],
			'title_position'               => $data['title_position'] ?? 'default',
			'category_style'               => $data['category_style'] ?? '',
			'is_thumb_lightbox'            => $data['is_thumb_lightbox'],
			'author_prefix'                => $data['author_prefix'],
			'cat_icon'                     => $data['cat_icon'] ?? '',
			'tag_icon'                     => $data['tag_icon'] ?? '',
			'date_icon'                    => $data['date_icon'] ?? '',
			'user_icon'                    => $data['user_icon'] ?? '',
			'meta_ordering'                => $data['meta_ordering'],
			'comment_icon'                 => $data['comment_icon'] ?? '',
			'image_custom_dimension'       => ( $data['image_size'] == 'custom' && isset( $data['image_custom_dimension'] ) ) ? $data['image_custom_dimension'] : [],
			'img_crop_style'               => ( $data['image_size'] == 'custom' && isset( $data['img_crop_style'] ) ) ? $data['img_crop_style'] : '',
			'show_acf'                     => $data['show_acf'] ?? '',
			'search_by'                    => $data['search_by'] ?? '',
			'multiple_taxonomy'            => $data['multiple_taxonomy'] ?? '',
		];

		$cf = self::is_acf();
		if ( $cf && rtTPG()->hasPro() ) {
			$post_type = self::available_post_type( $data['post_type'] );
			if ( $is_gutenberg && isset( $data['acf_data_lists'][ $post_type . '_cf_group' ] ) ) {
				$cf_group             = $data['acf_data_lists'][ $post_type . '_cf_group' ]['options'];
				$data_set['cf_group'] = wp_list_pluck( $cf_group, 'value' );
			} else {
				$data_set['cf_group'] = $data[ $post_type . '_cf_group' ];
			}
			$data_set['cf_hide_empty_value'] = $data['cf_hide_empty_value'];
			$data_set['cf_show_only_value']  = $data['cf_show_only_value'];
			$data_set['cf_hide_group_title'] = $data['cf_hide_group_title'];
		}
		if ( $is_gutenberg ) {
			unset( $data_set['grid_column'] );
			unset( $data_set['grid_column_mobile'] );
			unset( $data_set['grid_column_mobile'] );
			unset( $data_set['layout_style'] );
			$data_set['c_image_width']  = $data['c_image_width'] ?? '';
			$data_set['c_image_height'] = $data['c_image_height'] ?? '';
			$data_set['grid_column']    = (array) $data['grid_column'];
			$data_set['layout_style']   = $data['grid_layout_style'];
		}

		return $data_set;
	}


	/**
	 * Get Filter markup
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function get_frontend_filter_markup( $data, $is_guten = false ) {
		if (
			! rtTPG()->hasPro() ||
			! in_array(
				'show',
				[
					$data['show_taxonomy_filter'],
					$data['show_author_filter'],
					$data['show_order_by'],
					$data['show_sort_order'],
					$data['show_search'],
				]
			)
		) {
			return;
		}

		$html             = null;
		$wrapperContainer = $wrapperClass = $itemClass = $filter_btn_item_per_page = '';
		$postCountClass   = null;

		if ( 'carousel' === $data['filter_btn_style'] ) {
			$wrapperContainer  = 'swiper';
			$wrapperClass      = 'swiper-wrapper';
			$itemClass         = 'swiper-slide';
			$filter_btn_mobile = isset( $data['filter_btn_item_per_page_mobile'] ) ? $data['filter_btn_item_per_page_mobile'] : 'auto';
			$filter_btn_tablet = isset( $data['filter_btn_item_per_page_tablet'] ) ? $data['filter_btn_item_per_page_tablet'] : 'auto';
			$filter_btn_item_per_page
			                   = "data-per-page = '{$data['filter_btn_item_per_page']}' data-per-page-mobile = '{$filter_btn_mobile}' data-per-tablet = '{$filter_btn_tablet}'";
		}

		$html .= "<div class='rt-layout-filter-container rt-clear'><div class='rt-filter-wrap'>";

		if ( 'show' == $data['show_author_filter'] || 'show' == $data['show_taxonomy_filter'] ) {
			$html .= "<div class='filter-left-wrapper {$wrapperContainer}' {$filter_btn_item_per_page}>";
		}
		// if($data['filter_btn_style'] == 'carousel') {
		// $html .= "<div class='swiper-pagination'></div>";
		// }
		$selectedSubTermsForButton = null;

		$filterType = $data['filter_type'];
		$post_count = ( 'yes' == $data['filter_post_count'] ) ? true : false;

		if ( 'show' == $data['show_taxonomy_filter'] ) {
			if ( $data['multiple_taxonomy'] == 'yes' ) {
				$html .= self::taxonomies_filter( $data, $is_guten, $filterType, $post_count, $wrapperClass, $itemClass );
			} else {
				$html .= self::taxonomy_filter( $data, $is_guten, $filterType, $post_count, $wrapperClass, $itemClass );
			}
		}

		// TODO: Author filter
		if ( 'show' == $data['show_author_filter'] ) {
			$user_el = $data['author'];

			$filterAuthors = $user_el;

			if ( ! empty( $user_el ) ) {
				$users = get_users( apply_filters( 'tpg_author_arg', [ 'include' => $user_el ] ) );
			} else {
				$users = get_users( apply_filters( 'tpg_author_arg', [] ) );
			}
			$allText   = $data['author_filter_all_text'] ?: __( 'All Users', 'the-post-grid' );
			$allSelect = ' selected';

			if ( $filterType == 'dropdown' ) {
				$html            .= "<div class='rt-filter-item-wrap rt-author-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-filter='author'>";
				$termDefaultText = $allText;
				$dataAuthor      = 'all';
				$htmlButton      = '';
				$htmlButton      .= '<span class="author-dropdown rt-filter-dropdown">';
				$htmlButton      .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'>" . $allText . '</span>';

				if ( ! empty( $users ) ) {
					foreach ( $users as $user ) {
						$user_post_count = false;
						$post_count ? '(' . count_user_posts( $user->ID, $data['post_type'] ) . ')' : null;
						if ( is_array( $filterAuthors ) && ! empty( $filterAuthors ) ) {
							if ( in_array( $user->ID, $filterAuthors ) ) {
								$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'>{$user->display_name} <span class='rt-text'>{$user_post_count}</span></span>";
							}
						} else {
							$htmlButton .= "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='{$user->ID}'><span class='rt-text'>{$user->display_name} {$user_post_count}</span></span>";
						}
					}
				}
				$htmlButton  .= '</span>';
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
				// if ( 'yes' == $data['tax_filter_all_text'] ) {
				// $pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
				$html .= "<span class='author-button-item rt-filter-button-item {$allSelect}' data-author='all'>" . $allText . '</span>';
				// }
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

		// TODO: Order Filter
		if ( 'show' == $data['show_sort_order'] ) {
			$action_order = ( $data['order'] ? strtoupper( $data['order'] ) : 'DESC' );
			$html         .= '<div class="rt-filter-item-wrap rt-sort-order-action" data-filter="order">';
			$html         .= "<span class='rt-sort-order-action-arrow' data-sort-order='{$action_order}'>&nbsp;<span></span></span>";
			$html         .= '</div>';
		}

		// TODO: Orderby Filter
		if ( 'show' == $data['show_order_by'] ) {
			$wooFeature     = ( $data['post_type'] == 'product' ? true : false );
			$orders         = Options::rtPostOrderBy( $wooFeature );
			$action_orderby = ( ! empty( $data['orderby'] ) ? $data['orderby'] : 'none' );
			if ( $action_orderby == 'none' ) {
				$action_orderby_label = __( 'Sort By', 'the-post-grid' );
			} elseif ( in_array( $action_orderby, array_keys( Options::rtMetaKeyType() ) ) ) {
				$action_orderby_label = __( 'Meta value', 'the-post-grid' );
			} else {
				$action_orderby_label = __( 'By ', 'the-post-grid' ) . $action_orderby;
			}
			if ( $action_orderby !== 'none' ) {
				$orders['none'] = __( 'Sort By', 'the-post-grid' );
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

		// TODO: Search Filter
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

	public static function taxonomies_filter( $data, $is_guten, $filterType, $post_count, $wrapperClass, $itemClass ) {
		$postCountClass = ( $post_count ? ' has-post-count' : null );
		$allSelect      = ' selected';
		$isTermSelected = false;
		$html           = '';

		$taxonomy_label = $default_term = '';
		if ( $is_guten ) {
			if ( $data['multiple_taxonomy'] === 'yes' ) {
				$taxFilter = wp_list_pluck( $data['filter_taxonomies'], 'value' );
			} else {
				$taxFilter = $data['filter_taxonomy'];
			}
		} else {
			$section_term_key = $data['post_type'] . '_filter_taxonomies';
			$taxFilter        = $data[ $section_term_key ];
		}

		$_taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );

		foreach ( $_taxonomies as $index => $object ) {
			if ( ! is_array( $taxFilter ) || ! in_array( $object->name, $taxFilter ) ) {
				continue;
			}
			$terms = [];

			$taxonomy_details = get_taxonomy( $object->name );
			$taxonomy_label   = $taxonomy_details->label;
			$default_term_key = $object->name . '_default_terms';
			$default_term     = isset( $data[ $default_term_key ] ) ? $data[ $default_term_key ] : '';
			$allText          = $data['tax_filter_all_text'] ?: __( 'All ', 'the-post-grid' ) . $taxonomy_label;
			$setting_key      = $object->name . '_ids';

			// Gutenberg.
			if ( $is_guten && ! empty( $data['taxonomy_lists'][ $object->name ]['options'] ) ) {
				// This block execute if gutenberg editor has taxonomy query.
				$terms = wp_list_pluck( $data['taxonomy_lists'][ $object->name ]['options'], 'value' );
			} //Elementor.
            elseif ( ! empty( $data[ $setting_key ] ) ) {
				// This block execute for Elementor editor has taxonomy query.
				$_terms = $data[ $setting_key ];
				$args   = [
					'taxonomy' => $object->name,
					'fields'   => 'ids',
					'include'  => $_terms,
				];

				if ( $data['custom_taxonomy_order'] ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$args['order']    = 'ASC';
				}
				$terms = get_terms( $args );
			} else {
				// Execute if there is no taxonomy query.

				$args = [
					'taxonomy' => $object->name,
					'fields'   => 'ids',
				];

				if ( $data['custom_taxonomy_order'] ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$args['order']    = 'ASC';
				}
				$terms = get_terms( $args );

			}

			// TODO===========
			$taxFilterTerms = $terms;

			if ( $default_term ) {
				$isTermSelected = true;
				$allSelect      = null;
			}

			if ( $filterType == 'dropdown' ) {
				$html             .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-taxonomy='{$object->name}' data-filter='taxonomy'>";
				$termDefaultText  = $allText;
				$dataTerm         = 'all';
				$htmlButton       = '';
				$selectedSubTerms = null;
				$pCount           = 0;

				if ( ! empty( $terms ) ) {
					$i = 0;
					foreach ( $terms as $term_id ) {
						$term   = get_term( $term_id, $object->name, ARRAY_A );
						$id     = $term['term_id'];
						$pCount = $pCount + $term['count'];
						$sT     = null;
						if ( $data['tgp_filter_taxonomy_hierarchical'] == 'yes' ) {
							$subTerms = self::rt_get_all_term_by_taxonomy( $object->name, true, $id );
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
						$i ++;
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
					$htmlButton = "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'><span class='rt-text'>" . $allText . '</span></span>'
					              . $htmlButton;
				}
				$htmlButton = sprintf( '<span class="term-dropdown rt-filter-dropdown">%s</span>', $htmlButton );

				$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataTerm . '">
                    								                        <span class="rt-text">' . $termDefaultText . '</span>
                    								                        <i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
                    								                    </span>';

				$html .= $showAllhtml . $htmlButton;
				$html .= '</div>' . $selectedSubTerms;
			} else {
				// if Button the execute
				// $termDefaultText = $allText;

				$bCount = 0;
				$bItems = null;

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term_id ) {
						$term = get_term( $term_id, $object->name, ARRAY_A );
						if ( ! isset( $term['term_id'] ) ) {
							continue;
						}
						$id     = $term['term_id'];
						$bCount = $bCount + absint( $term['count'] );
						$sT     = null;
						if ( $data['tgp_filter_taxonomy_hierarchical'] == 'yes' && $data['filter_btn_style'] === 'default' && $data['filter_type'] == 'button' ) {
							$subTerms = self::rt_get_all_term_by_taxonomy( $object->name, true, $id );
							if ( ! empty( $subTerms ) ) {
								$sT .= "<div class='rt-filter-sub-tax sub-button-group '>";
								foreach ( $subTerms as $stId => $t ) {
									$sTPostCount = ( $post_count ? " (<span class='rt-post-count'>{$t['count']}</span>)" : null );
									$sT          .= "<span class='term-button-item rt-filter-button-item ' data-term='{$stId}'>{$t['name']}{$sTPostCount}</span>";
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
				$html .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-button-wrap{$postCountClass} {$wrapperClass}' data-taxonomy='{$object->name}' data-filter='taxonomy'>";

				// $pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
				if ( 'yes' == $data['tpg_hide_all_button'] ) {
					$html .= "<span class='term-button-item rt-filter-button-item {$allSelect} {$itemClass}' data-term='all'>" . $allText . '</span>';
				}

				$html .= $bItems;

				$html .= '</div>';
				if ( 'carousel' === $data['filter_btn_style'] ) {
					$html .= '<div class="swiper-navigation"><div class="swiper-button-prev slider-btn"></div><div class="swiper-button-next slider-btn"></div></div>';
				}
			}
			// TODO===========End
		}

		return $html;
	}

	public static function taxonomy_filter( $data, $is_guten, $filterType, $post_count, $wrapperClass, $itemClass ) {
		$postCountClass = ( $post_count ? ' has-post-count' : null );
		$allSelect      = ' selected';
		$isTermSelected = false;
		$html           = '';

		$taxonomy_label = $default_term = '';
		if ( $is_guten ) {
			$taxFilter = $data['filter_taxonomy'];
		} else {
			$section_term_key = $data['post_type'] . '_filter_taxonomy';
			$taxFilter        = $data[ $section_term_key ];
		}

		if ( $taxFilter ) {
			$taxonomy_details = get_taxonomy( $taxFilter );
			$taxonomy_label   = $taxonomy_details->label;
			$default_term_key = $taxFilter . '_default_terms';
			$default_term     = isset( $data[ $default_term_key ] ) ? $data[ $default_term_key ] : '';
		}

		$allText = $data['tax_filter_all_text'] ? $data['tax_filter_all_text'] : __( 'All ', 'the-post-grid' ) . $taxonomy_label;

		$_taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );
		$terms       = [];

		foreach ( $_taxonomies as $index => $object ) {
			if ( $object->name != $taxFilter ) {
				continue;
			}
			$setting_key = $object->name . '_ids';

			// Gutenberg.
			if ( $is_guten && ! empty( $data['taxonomy_lists'][ $object->name ]['options'] ) ) {
				// This block execute if gutenberg editor has taxonomy query.
				$terms = wp_list_pluck( $data['taxonomy_lists'][ $object->name ]['options'], 'value' );
			} //Elementor.
            elseif ( ! empty( $data[ $setting_key ] ) ) {
				// This block execute for Elementor editor has taxonomy query.
				$_terms = $data[ $setting_key ];
				$args   = [
					'taxonomy' => $taxFilter,
					'fields'   => 'ids',
					'include'  => $_terms,
				];

				if ( $data['custom_taxonomy_order'] ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$args['order']    = 'ASC';
				}
				$terms = get_terms( $args );
			} //Shortcode.
			else {
				// Execute if there is no taxonomy query.

				$args = [
					'taxonomy' => $taxFilter,
					'fields'   => 'ids',
				];

				if ( $data['custom_taxonomy_order'] ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$args['order']    = 'ASC';
				}
				$terms = get_terms( $args );

			}
		}

		$taxFilterTerms = $terms;

		if ( $default_term && $taxFilter ) {
			$isTermSelected = true;
			$allSelect      = null;
		}

		if ( $filterType == 'dropdown' ) {
			$html             .= "<div class='rt-filter-item-wrap rt-tax-filter rt-filter-dropdown-wrap parent-dropdown-wrap{$postCountClass}' data-taxonomy='{$taxFilter}' data-filter='taxonomy'>";
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
						$subTerms = self::rt_get_all_term_by_taxonomy( $taxFilter, true, $id );
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
					$i ++;
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
				$htmlButton = "<span class='term-dropdown-item rt-filter-dropdown-item' data-term='all'><span class='rt-text'>" . $allText . '</span></span>'
				              . $htmlButton;
			}
			$htmlButton = sprintf( '<span class="term-dropdown rt-filter-dropdown">%s</span>', $htmlButton );

			$showAllhtml = '<span class="term-default rt-filter-dropdown-default" data-term="' . $dataTerm . '">
        								                        <span class="rt-text">' . $termDefaultText . '</span>
        								                        <i class="fa fa-angle-down rt-arrow-angle" aria-hidden="true"></i>
        								                    </span>';

			$html .= $showAllhtml . $htmlButton;
			$html .= '</div>' . $selectedSubTerms;
		} else {
			// if Button the execute
			// $termDefaultText = $allText;

			$bCount = 0;
			$bItems = null;

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term_id ) {
					$term = get_term( $term_id, $taxFilter, ARRAY_A );
					if ( ! isset( $term['term_id'] ) ) {
						continue;
					}
					$id     = $term['term_id'];
					$bCount = $bCount + absint( $term['count'] );
					$sT     = null;
					if ( $data['tgp_filter_taxonomy_hierarchical'] == 'yes' && $data['filter_btn_style'] === 'default' && $data['filter_type'] == 'button' ) {
						$subTerms = self::rt_get_all_term_by_taxonomy( $taxFilter, true, $id );
						if ( ! empty( $subTerms ) ) {
							$sT .= "<div class='rt-filter-sub-tax sub-button-group '>";
							foreach ( $subTerms as $stId => $t ) {
								$sTPostCount = ( $post_count ? " (<span class='rt-post-count'>{$t['count']}</span>)" : null );
								$sT          .= "<span class='term-button-item rt-filter-button-item ' data-term='{$stId}'>{$t['name']}{$sTPostCount}</span>";
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

			// $pCountH = ( $post_count ? " (<span class='rt-post-count'>{$bCount}</span>)" : null );
			if ( 'yes' == $data['tpg_hide_all_button'] ) {
				$html .= "<span class='term-button-item rt-filter-button-item {$allSelect} {$itemClass}' data-term='all'>" . $allText . '</span>';
			}

			$html .= $bItems;

			$html .= '</div>';
			if ( 'carousel' === $data['filter_btn_style'] ) {
				$html .= '<div class="swiper-navigation"><div class="swiper-button-prev slider-btn"></div><div class="swiper-button-next slider-btn"></div></div>';
			}
		}

		return $html;
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
	 * Get Popup Modal Markup
	 */
	public static function get_modal_markup() {
		$html = null;
		$html .= '<div class="md-modal rt-md-effect" id="rt-modal">
                        <div class="md-content">
                            <div class="rt-md-content-holder"></div>
                            <div class="md-cls-btn">
                                <button class="md-close"><i class="fa fa-times" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>';
		$html .= "<div class='md-overlay'></div>";
		self::print_html( $html, true );
	}

	/**
	 * Get Archive page title
	 */
	public static function get_archive_title() {
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
			echo esc_html( gmdate( get_option( 'date_format' ), $time_stamp ) );
		}
	}

	/**
	 * Get Last Category ID
	 *
	 * @return mixed
	 */
	public static function get_last_category_id() {
		if ( is_archive() ) {
			return;
		}
		$categories = get_terms(
			[
				'taxonomy'   => 'category',
				'hide_empty' => false,
				'number'     => 1,
			]
		);
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			return $categories[0]->term_id;
		}
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function get_dynamic_class_gutenberg( $data ) {
		$uniqueId     = isset( $data['uniqueId'] ) ? $data['uniqueId'] : null;
		$uniqueClass  = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamicClass = $uniqueClass;
		$dynamicClass .= ! empty( $data['align'] ) ? ' align' . $data['align'] : null;
		$dynamicClass .= ! empty( $data['className'] ) ? ' ' . $data['className'] : null;
		$dynamicClass .= ! empty( $data['full_wrapper_align']['lg'] ) ? " tpg-wrapper-align-{$data['full_wrapper_align']['lg']}" : null;
		$dynamicClass .= ! empty( $data['filter_type'] ) ? " tpg-filter-type-{$data['filter_type']}" : null;
		$dynamicClass .= ! empty( $data['show_pagination'] ) ? " pagination-visibility-{$data['show_pagination']}" : null;
		$dynamicClass .= ! empty( $data['ajax_pagination_type'] ) ? " ajax-pagination-type-next-prev-{$data['ajax_pagination_type']}" : null;
		$dynamicClass .= ! empty( $data['show_meta'] ) ? " meta-visibility-{$data['show_meta']}" : null;
		$dynamicClass .= ! empty( $data['section_title_style'] ) ? " section-title-style-{$data['section_title_style']}" : null;
		$dynamicClass .= ! empty( $data['section_title_alignment'] ) ? " section-title-align-{$data['section_title_alignment']}" : null;
		$dynamicClass .= ! empty( $data['hover_animation'] ) ? " img_hover_animation_{$data['hover_animation']}" : null;
		$dynamicClass .= ! empty( $data['title_visibility_style'] ) ? " title-{$data['title_visibility_style']}" : null;
		$dynamicClass .= ! empty( $data['title_position'] ) ? " title_position_{$data['title_position']}" : null;
		$dynamicClass .= ! empty( $data['title_hover_underline'] ) ? " title_hover_border_{$data['title_hover_underline']}" : null;
		$dynamicClass .= ! empty( $data['meta_position'] ) ? " meta_position_{$data['meta_position']}" : null;
		$dynamicClass .= ! empty( $data['author_icon_visibility'] ) ? " tpg-is-author-icon-{$data['author_icon_visibility']}" : null;
		$dynamicClass .= ! empty( $data['show_author_image'] ) ? " author-image-visibility-{$data['show_author_image']}" : null;
		$dynamicClass .= ! empty( $data['category_position'] ) ? " tpg-category-position-{$data['category_position']}" : null;
		$dynamicClass .= ! empty( $data['readmore_btn_style'] ) ? " readmore-btn-{$data['readmore_btn_style']}" : null;
		$dynamicClass .= ! empty( $data['grid_hover_overlay_type'] ) ? " grid-hover-overlay-type-{$data['grid_hover_overlay_type']}" : null;
		$dynamicClass .= ! empty( $data['grid_hover_overlay_height'] ) ? " grid-hover-overlay-height-{$data['grid_hover_overlay_height']}" : null;
		$dynamicClass .= ! empty( $data['on_hover_overlay'] ) ? " hover-overlay-height-{$data['on_hover_overlay']}" : null;
		$dynamicClass .= ! empty( $data['title_border_visibility'] ) ? " tpg-title-border-{$data['title_border_visibility']}" : null;
		$dynamicClass .= ! empty( $data['title_alignment'] ) ? " title-alignment-{$data['title_alignment']}" : null;
		$dynamicClass .= ! empty( $data['filter_v_alignment'] ) ? " tpg-filter-alignment-{$data['filter_v_alignment']}" : null;
		$dynamicClass .= ! empty( $data['border_style'] ) ? " filter-button-border-{$data['border_style']}" : null;
		$dynamicClass .= ! empty( $data['filter_next_prev_btn'] ) ? " filter-nex-prev-btn-{$data['filter_next_prev_btn']}" : null;
		$dynamicClass .= ! empty( $data['filter_h_alignment'] ) ? " tpg-filter-h-alignment-{$data['filter_h_alignment']}" : null;
		$dynamicClass .= ! empty( $data['is_box_border'] ) ? " tpg-el-box-border-{$data['is_box_border']}" : null;
		$dynamicClass .= ! empty( $data['category_style'] ) ? " tpg-cat-{$data['category_style']}" : null;

		// Slider layout
		$dynamicClass .= ! empty( $data['arrow_position'] ) ? " slider-arrow-position-{$data['arrow_position']}" : null;
		$dynamicClass .= ! empty( $data['dots'] ) ? " slider-dot-enable-{$data['dots']}" : null;
		$dynamicClass .= ! empty( $data['dots_style'] ) ? " slider-dots-style-{$data['dots_style']}" : null;
		$dynamicClass .= ! empty( $data['lazyLoad'] ) ? " is-lazy-load-{$data['lazyLoad']}" : null;
		$dynamicClass .= ! empty( $data['carousel_overflow'] ) ? " is-carousel-overflow-{$data['carousel_overflow']}" : null;
		$dynamicClass .= ! empty( $data['slider_direction'] ) ? " slider-direction-{$data['slider_direction']}" : null;
		$dynamicClass .= ! empty( $data['dots_text_align'] ) ? " slider-dots-align-{$data['dots_text_align']}" : null;
		$dynamicClass .= ! empty( $data['pagination_btn_space_btween'] ) && $data['pagination_btn_space_btween'] === 'space-between' ? ' tpg-prev-next-space-between' : null;
		$dynamicClass .= ! empty( $data['pagination_btn_position'] ) && $data['pagination_btn_position'] === 'absolute' ? ' tpg-prev-next-absolute' : null;
		$dynamicClass .= ! empty( $data['box_border_bottom'] ) && $data['box_border_bottom'] === 'enable' ? ' tpg-border-bottom-enable' : null;
		$dynamicClass .= ! empty( $data['offset_img_position'] ) && $data['offset_img_position'] === 'offset-image-right' ? ' offset-image-right' : null;
		$dynamicClass .= ! empty( $data['scroll_visibility'] ) && $data['scroll_visibility'] === 'yes' ? '' : ' slider-scroll-hide';
		$dynamicClass .= ! empty( $data['enable_external_link'] ) && $data['enable_external_link'] === 'show' ? ' has-external-link' : '';

		// ACF
		$dynamicClass .= ! empty( $data['acf_label_style'] ) ? " act-label-style-{$data['acf_label_style']}" : null;
		$dynamicClass .= ! empty( $data['acf_alignment'] ) && ! is_array( $data['acf_alignment'] ) ? " tpg-acf-align-{$data['acf_alignment']}" : null;

		return $dynamicClass;
	}

	/**
	 * Print Validated html tags
	 *
	 * @param $tag
	 *
	 * @return string|null
	 */
	public static function print_validated_html_tag( $tag ) {
		$allowed_html_wrapper_tags = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'div',
		];

		return in_array( strtolower( $tag ), $allowed_html_wrapper_tags, true ) ? $tag : 'div';
	}

	/**
	 * Get Section Title
	 *
	 * @param $data
	 */
	public static function get_section_title( $data ) {
		if ( 'show' != $data['show_section_title'] ) {
			return;
		}

		$_is_link = false;
		if ( ! empty( $data['section_title_link']['url'] ) ) {
			$_is_link = true;
		}

		?>

        <div class="tpg-widget-heading-wrapper rt-clear heading-<?php echo esc_attr( $data['section_title_style'] ); ?> ">
            <span class="tpg-widget-heading-line line-left"></span>
			<?php
			// Start Section title tag.
			printf( "<%s class='tpg-widget-heading'>", esc_attr( self::print_validated_html_tag( $data['section_title_tag'] ) ) );
			?>

			<?php
			if ( $_is_link ) :
			?>
            <a href="#">
				<?php endif; ?>

				<?php
				if ( 'page_title' == $data['section_title_source'] ) {
					$archive_prefix = $data['title_prefix'] ? $data['title_prefix'] . ' ' : '';
					$archive_suffix = $data['title_suffix'] ? ' ' . $data['title_suffix'] : '';
					printf( "<span class='prefix-text'>%s</span>", esc_html( $archive_prefix ) );
					if ( is_archive() ) {
						self::get_archive_title();
					} elseif ( is_search() ) {
						echo esc_html( get_query_var( 's' ) );
					} else {
						the_title();
					}
					printf( "<span class='suffix-text'>%s</span>", esc_html( $archive_suffix ) );
				} else {
					?>
                    <span>
						<?php echo esc_html( $data['section_title_text'] ); ?>
					</span>
					<?php
				}
				?>

				<?php if ( $_is_link ) : ?>
            </a>

		<?php endif; ?>
			<?php printf( '</%s>', esc_attr( self::print_validated_html_tag( $data['section_title_tag'] ) ) ); // End Section Title tag ?>
            <span class="tpg-widget-heading-line line-right"></span>

			<?php if ( isset( $data['enable_external_link'] ) && 'show' === $data['enable_external_link'] ) : ?>
                <a class='external-link' href='<?php echo esc_url( $data['section_external_link'] ?? '#' ); ?>'>
					<?php if ( $data['section_external_text'] ) : ?>
                        <span class="external-lable"><?php echo esc_html( $data['section_external_text'] ); ?></span>
					<?php endif; ?>
					<?php
					printf(
						"<i class='left-icon %s'></i>",
						esc_attr( self::change_icon( 'fas fa-angle-right', 'right-arrow', 'left-icon' ) )
					);
					?>
                </a>
			<?php endif; ?>

        </div>

		<?php if ( isset( $data['show_cat_desc'] ) && $data['show_cat_desc'] == 'yes' && category_description( self::get_last_category_id() ) ) : ?>
            <div class="tpg-category-description">
				<?php echo category_description( self::get_last_category_id() ); ?>
            </div>
		<?php endif; ?>

		<?php
	}

	/**
	 * rtAllOptionFields
	 * All settings.
	 *
	 * @return array
	 */
	public static function rtAllOptionFields() {
		$fields = array_merge(
			Options::rtTPGCommonFilterFields(),
			Options::rtTPGLayoutSettingFields(),
			Options::responsiveSettingsColumn(),
			Options::layoutMiscSettings(),
			Options::stickySettings(),
			// settings.
			Options::rtTPGSCHeadingSettings(),
			Options::rtTPGSCCategorySettings(),
			Options::rtTPGSCTitleSettings(),
			Options::rtTPGSCMetaSettings(),
			Options::rtTPGSCImageSettings(),
			Options::rtTPGSCExcerptSettings(),
			Options::rtTPGSCButtonSettings(),
			// style.
			Options::rtTPGStyleFields(),
			Options::rtTPGStyleHeading(),
			Options::rtTPGStyleFullArea(),
			Options::rtTPGStyleContentWrap(),
			Options::rtTPGStyleCategory(),
			Options::rtTPGPostType(),
			Options::rtTPGStyleButtonColorFields(),
			Options::rtTPAdvanceFilters(),
			Options::itemFields()
		);

		return $fields;
	}

	public static function rt_get_all_term_by_taxonomy( $taxonomy = null, $count = false, $parent = false ) {
		$terms = [];

		if ( $taxonomy ) {
			$temp_terms = get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => 0,
				]
			);

			if ( is_array( $temp_terms ) && ! empty( $temp_terms ) && empty( $temp_terms['errors'] ) ) {
				foreach ( $temp_terms as $term ) {
					$order = get_term_meta( $term->term_id, '_rt_order', true );
					if ( $order === '' ) {
						update_term_meta( $term->term_id, '_rt_order', 0 );
					}
				}

				global $wp_version;

				$args = [
					'taxonomy'   => $taxonomy,
					'orderby'    => 'meta_value_num',
					'meta_key'   => '_rt_order', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'hide_empty' => apply_filters( 'rttpg_category_hide_empty', false ),
				];

				if ( $parent >= 0 && $parent !== false ) {
					$args['parent'] = absint( $parent );
				}

				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key

				$termObjs = get_terms( $args );

				foreach ( $termObjs as $term ) {
					if ( $count ) {
						$terms[ $term->term_id ] = [
							'name'  => $term->name,
							'count' => $term->count,
						];
					} else {
						$terms[ $term->term_id ] = $term->name;
					}
				}
			}
		}

		return $terms;
	}

	public static function rt_get_selected_term_by_taxonomy( $taxonomy = null, $include = [], $count = false, $parent = false ) {
		$terms = [];

		if ( $taxonomy ) {
			$temp_terms = get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => 0,
				]
			);

			if ( is_array( $temp_terms ) && ! empty( $temp_terms ) && empty( $temp_terms['errors'] ) ) {
				foreach ( $temp_terms as $term ) {
					$order = get_term_meta( $term->term_id, '_rt_order', true );
					if ( $order === '' ) {
						update_term_meta( $term->term_id, '_rt_order', 0 );
					}
				}

				global $wp_version;

				$args = [
					'taxonomy'   => $taxonomy,
					'orderby'    => 'meta_value_num',
					'meta_key'   => '_rt_order', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'include'    => $include,
					'hide_empty' => false,
				];

				if ( $parent >= 0 && $parent !== false ) {
					$args['parent'] = absint( $parent );
				}

				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_rt_order'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key

				$termObjs = get_terms( $args );

				foreach ( $termObjs as $term ) {
					if ( $count ) {
						$terms[ $term->term_id ] = [
							'name'  => $term->name,
							'count' => $term->count,
						];
					} else {
						$terms[ $term->term_id ] = $term->name;
					}
				}
			}
		}

		return $terms;
	}

	public static function getCurrentUserRoles() {
		global $current_user;

		return $current_user->roles;
	}

	public static function rt_get_taxonomy_for_filter( $post_type = null ) {
		if ( ! $post_type ) {
			$post_type = get_post_meta( get_the_ID(), 'tpg_post_type', true );
		}

		if ( ! $post_type ) {
			$post_type = 'post';
		}

		return self::rt_get_all_taxonomy_by_post_type( $post_type );
	}

	public static function rt_get_all_taxonomy_by_post_type( $post_type = null ) {
		$taxonomies = [];

		if ( $post_type && post_type_exists( $post_type ) ) {
			$taxObj = get_object_taxonomies( $post_type, 'objects' );

			if ( is_array( $taxObj ) && ! empty( $taxObj ) ) {
				foreach ( $taxObj as $tKey => $taxonomy ) {
					$taxonomies[ $tKey ] = $taxonomy->label;
				}
			}
		}

		if ( $post_type == 'post' ) {
			unset( $taxonomies['post_format'] );
		}

		return $taxonomies;
	}

	public static function rt_get_users() {
		$users = [];
		$u     = get_users( apply_filters( 'tpg_author_arg', [] ) );

		if ( ! empty( $u ) ) {
			foreach ( $u as $user ) {
				$users[ $user->ID ] = $user->display_name;
			}
		}

		return $users;
	}

	public static function rtFieldGenerator( $fields = [] ) {
		$html = null;

		if ( is_array( $fields ) && ! empty( $fields ) ) {
			$tpgField = new Field();
			foreach ( $fields as $fieldKey => $field ) {
				$html .= $tpgField->Field( $fieldKey, $field );
			}
		}

		return $html;
	}

	/**
	 * Sanitize field value
	 *
	 * @param array $field
	 * @param null $value
	 *
	 * @return array|null
	 * @internal param $value
	 */
	public static function sanitize( $field = [], $value = null ) {
		$newValue = null;

		if ( is_array( $field ) ) {
			$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );

			if ( empty( $field['multiple'] ) ) {
				if ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
					$newValue = sanitize_text_field( $value );
				} elseif ( $type == 'url' ) {
					$newValue = esc_url( $value );
				} elseif ( $type == 'slug' ) {
					$newValue = sanitize_title_with_dashes( $value );
				} elseif ( $type == 'textarea' ) {
					$newValue = wp_kses_post( $value );
				} elseif ( $type == 'script' ) {
					$newValue = trim( $value );
				} elseif ( $type == 'colorpicker' ) {
					$newValue = self::sanitize_hex_color( $value );
				} elseif ( $type == 'image_size' ) {
					$newValue = [];

					foreach ( $value as $k => $v ) {
						$newValue[ $k ] = esc_attr( $v );
					}
				} elseif ( $type == 'style' ) {
					$newValue = [];

					foreach ( $value as $k => $v ) {
						if ( $k == 'color' ) {
							$newValue[ $k ] = self::sanitize_hex_color( $v );
						} else {
							$newValue[ $k ] = self::sanitize( [ 'type' => 'text' ], $v );
						}
					}
				} else {
					$newValue = sanitize_text_field( $value );
				}
			} else {
				$newValue = [];

				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $key => $val ) {
							if ( $type == 'style' && $key == 0 ) {
								if ( function_exists( 'sanitize_hex_color' ) ) {
									$newValue = sanitize_hex_color( $val );
								} else {
									$newValue[] = self::sanitize_hex_color( $val );
								}
							} else {
								$newValue[] = sanitize_text_field( $val );
							}
						}
					} else {
						$newValue[] = sanitize_text_field( $value );
					}
				}
			}
		}

		return $newValue;
	}

	public static function sanitize_hex_color( $color ) {
		if ( function_exists( 'sanitize_hex_color' ) ) {
			return sanitize_hex_color( $color );
		} else {
			if ( '' === $color ) {
				return '';
			}

			// 3 or 6 hex digits, or the empty string.
			if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
				return $color;
			}
		}
	}

	public static function rtFieldGeneratorBackup( $fields = [], $multi = false ) {
		$html = null;

		if ( is_array( $fields ) && ! empty( $fields ) ) {
			$rtField = new Field();

			if ( $multi ) {
				foreach ( $fields as $field ) {
					$html .= $rtField->Field( $field );
				}
			} else {
				$html .= $rtField->Field( $fields );
			}
		}

		return $html;
	}

	public static function rtSmartStyle( $fields = [] ) {
		$h = null;

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $key => $label ) {
				$atts    = '';
				$proText = '';
				$class   = '';

				$h .= '<div class="field-holder ' . esc_attr( $class ) . '">';

				$h .= '<div class="field-label"><label>' . esc_html( $label ) . '' . self::htmlKses( $proText, 'basic' ) . '</label></div>';
				$h .= "<div class='field'>";
				// color.
				$h      .= "<div class='field-inner col-4'>";
				$h      .= "<div class='field-inner-container size'>";
				$h      .= "<span class='label'>Color</span>";
				$cValue = get_post_meta( get_the_ID(), $key . '_color', true );
				$h      .= '<input type="text" value="' . esc_attr( $cValue ) . '" class="rt-color" name="' . esc_attr( $key ) . '_color">';
				$h      .= '</div>';
				$h      .= '</div>';

				// Font size.
				$h      .= "<div class='field-inner col-4'>";
				$h      .= "<div class='field-inner-container size'>";
				$h      .= "<span class='label'>Font size</span>";
				$h      .= '<select ' . self::htmlKses( $atts, 'basic' ) . ' name="' . esc_attr( $key ) . '_size" class="rt-select2">';
				$fSizes = Options::scFontSize();
				$sValue = get_post_meta( get_the_ID(), $key . '_size', true );
				$h      .= "<option value=''>Default</option>";

				foreach ( $fSizes as $size => $sizeLabel ) {
					$sSlt = ( $size == $sValue ? 'selected' : null );
					$h    .= '<option value="' . esc_attr( $size ) . '" ' . esc_attr( $sSlt ) . '>' . esc_html( $sizeLabel ) . '</option>';
				}

				$h .= '</select>';
				$h .= '</div>';
				$h .= '</div>';

				// Weight.
				$h       .= "<div class='field-inner col-4'>";
				$h       .= "<div class='field-inner-container weight'>";
				$h       .= "<span class='label'>Weight</span>";
				$h       .= '<select ' . self::htmlKses( $atts, 'basic' ) . ' name="' . esc_attr( $key ) . '_weight" class="rt-select2">';
				$h       .= "<option value=''>Default</option>";
				$weights = Options::scTextWeight();
				$wValue  = get_post_meta( get_the_ID(), $key . '_weight', true );

				foreach ( $weights as $weight => $weightLabel ) {
					$wSlt = ( $weight == $wValue ? 'selected' : null );
					$h    .= '<option value="' . esc_attr( $weight ) . '" ' . esc_attr( $wSlt ) . '>' . esc_html( $weightLabel ) . '</option>';
				}

				$h .= '</select>';
				$h .= '</div>';
				$h .= '</div>';

				// Alignment.
				$h      .= "<div class='field-inner col-4'>";
				$h      .= "<div class='field-inner-container alignment'>";
				$h      .= "<span class='label'>Alignment</span>";
				$h      .= '<select ' . self::htmlKses( $atts, 'basic' ) . ' name="' . esc_attr( $key ) . '_alignment" class="rt-select2">';
				$h      .= "<option value=''>Default</option>";
				$aligns = Options::scAlignment();
				$aValue = get_post_meta( get_the_ID(), $key . '_alignment', true );

				foreach ( $aligns as $align => $alignLabel ) {
					$aSlt = ( $align == $aValue ? 'selected' : null );
					$h    .= '<option value="' . esc_attr( $align ) . '" ' . esc_attr( $aSlt ) . '>' . esc_html( $alignLabel ) . '</option>';
				}

				$h .= '</select>';
				$h .= '</div>';
				$h .= '</div>';

				$h .= '</div>';
				$h .= '</div>';
			}
		}

		return $h;
	}

	public static function custom_variation_price( $product ) {
		$price = '';
		$max   = $product->get_variation_sale_price( 'max' );
		$min   = $product->get_variation_sale_price( 'min' );

		if ( ! $min || $min !== $max ) {
			$price .= wc_price( $product->get_price() );
		}

		if ( $max && $max !== $min ) {
			$price .= ' - ';
			$price .= wc_price( $max );
		}

		return $price;
	}

	public static function getTPGShortCodeList() {
		$scList = null;
		$scQ    = get_posts(
			[
				'post_type'      => rtTPG()->post_type,
				'order_by'       => 'title',
				'order'          => 'DESC',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'     => [
					[
						'key'     => 'layout',
						'value'   => 'layout',
						'compare' => 'LIKE',
					],
				],
			]
		);

		if ( ! empty( $scQ ) ) {
			foreach ( $scQ as $sc ) {
				$scList[ $sc->ID ] = $sc->post_title;
			}
		}

		return $scList;
	}

	public static function getAllTPGShortCodeList() {
		$scList = null;
		$scQ    = get_posts(
			[
				'post_type'      => rtTPG()->post_type,
				'order_by'       => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			]
		);
		if ( ! empty( $scQ ) ) {
			foreach ( $scQ as $sc ) {
				$scList[ $sc->ID ] = $sc->post_title;
			}
		}

		return $scList;
	}

	public static function socialShare( $pLink ) {
		$html = null;
		$html .= "<div class='single-tpg-share'>
					<div class='fb-share'>
						<div class='fb-share-button' data-href='" . esc_url( $pLink ) . "' data-layout='button_count'></div>
					</div>
					<div class='twitter-share'>
						<a href='" . esc_url( $pLink ) . "' class='twitter-share-button'{count} data-url='https://about.twitter.com/resources/buttons#tweet'>Tweet</a>
					</div>
					<div class='googleplus-share'>
						<div class='g-plusone'></div>
					</div>
					<div class='linkedin-share'>
						<script type='IN/Share' data-counter='right'></script>
					</div>
					<div class='linkedin-share'>
						<a data-pin-do='buttonPin' data-pin-count='beside' href='https://www.pinterest.com/pin/create/button/?url=https%3A%2F%2Fwww.flickr.com%2Fphotos%2Fkentbrew%2F6851755809%2F&media=https%3A%2F%2Ffarm8.staticflickr.com%2F7027%2F6851755809_df5b2051c9_z.jpg&description=Next%20stop%3A%20Pinterest'><img src='//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png' /></a>
					</div>
				</div>";
		$html .= '<div id="fb-root"></div>
					<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s); js.id = id;
							js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
							fjs.parentNode.insertBefore(js, fjs);
						}(document, "script", "facebook-jssdk"));</script>';
		$html .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
            <script>window.___gcfg = { lang: 'en-US', parsetags: 'onload', };</script>";
		$html .= "<script src='https://apis.google.com/js/platform.js' async defer></script>";
		$html .= '<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>';
		$html .= '<script async defer src="//assets.pinterest.com/js/pinit.js"></script>';

		return $html;
	}

	public static function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes      = [];
		$interSizes = get_intermediate_image_sizes();
		if ( ! empty( $interSizes ) ) {
			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ] ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = [
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					];
				}
			}
		}

		$imgSize = [];

		if ( ! empty( $sizes ) ) {
			$imgSize['full'] = esc_html__( 'Full Size', 'the-post-grid' );
			foreach ( $sizes as $key => $img ) {
				$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
			}
		}

		return apply_filters( 'tpg_image_sizes', $imgSize );
	}

	public static function getFeatureImageSrc(
		$post_id = null,
		$fImgSize = 'medium',
		$mediaSource = 'feature_image',
		$defaultImgId = null,
		$customImgSize = [],
		$img_Class = ''
	) {
		global $post;

		$imgSrc    = null;
		$img_class = 'rt-img-responsive ';

		if ( $img_Class ) {
			$img_class .= $img_Class;
		}

		$post_id = ( $post_id ? absint( $post_id ) : $post->ID );
		$alt     = get_the_title( $post_id );
		$image   = null;
		$cSize   = false;

		if ( $fImgSize == 'rt_custom' ) {
			$fImgSize = 'full';
			$cSize    = true;
		}

		if ( $mediaSource == 'feature_image' ) {
			if ( $aID = get_post_thumbnail_id( $post_id ) ) {
				$image  = wp_get_attachment_image(
					$aID,
					$fImgSize,
					'',
					[
						'class'   => $img_class,
						'loading' => false,
					]
				);
				$imgSrc = wp_get_attachment_image_src( $aID, $fImgSize );

				if ( ! empty( $imgSrc ) && $img_Class == 'swiper-lazy' ) {
					$image = '<img class="' . esc_attr( $img_class ) . '" data-src="' . esc_url( $imgSrc[0] ) . '" src="#none" width="' . absint( $imgSrc[1] ) . '" height="' . absint( $imgSrc[2] ) . '" alt="' . esc_attr( $alt ) . '"/><div class="lazy-overlay-wrap"><div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div></div>';
				}

				$imgSrc = ! empty( $imgSrc ) ? $imgSrc[0] : $imgSrc;
			}
		} elseif ( $mediaSource == 'first_image' ) {
			if ( $img = preg_match_all(
				'/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',
				get_the_content( $post_id ),
				$matches
			)
			) {
				$imgSrc = $matches[1][0];
				$size   = '';

				if ( strpos( $imgSrc, site_url() ) !== false ) {
					$imgAbs = str_replace( trailingslashit( site_url() ), ABSPATH, $imgSrc );
				} else {
					$imgAbs = ABSPATH . $imgSrc;
				}

				$imgAbs = apply_filters( 'rt_tpg_sc_first_image_src', $imgAbs );

				if ( file_exists( $imgAbs ) ) {
					$info = getimagesize( $imgAbs );
					$size = isset( $info[3] ) ? $info[3] : '';
				}

				$image = '<img class="' . esc_attr( $img_class ) . '" src="' . esc_url( $imgSrc ) . '" ' . $size . ' alt="' . esc_attr( $alt ) . '"/>';

				if ( $img_Class == 'swiper-lazy' ) {
					$image = '<img class="' . esc_attr( $img_class ) . ' img-responsive" data-src="' . esc_url( $imgSrc ) . '" src="#none" ' . $size . ' alt="' . esc_attr( $alt ) . '"/><div class="lazy-overlay-wrap"><div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div></div>';
				}
			}
		}

		if ( ! $imgSrc && $defaultImgId ) {
			$image = wp_get_attachment_image( $defaultImgId, $fImgSize );
		}

		if ( $imgSrc && $cSize ) {
			$w = ( ! empty( $customImgSize[0] ) ? absint( $customImgSize[0] ) : null );
			$h = ( ! empty( $customImgSize[1] ) ? absint( $customImgSize[1] ) : null );
			$c = ( ! empty( $customImgSize[2] ) && $customImgSize[2] == 'soft' ? false : true );

			if ( $w && $h ) {
				$post_thumb_id = get_post_thumbnail_id( $post_id );

				if ( $post_thumb_id ) {
					$featured_image = wp_get_attachment_image_src( $post_thumb_id, 'full' );
					$w              = $featured_image[1] < $w ? $featured_image[1] : $w;
					$h              = $featured_image[2] < $h ? $featured_image[2] : $h;
				}

				$imgSrc = self::rtImageReSize( $imgSrc, $w, $h, $c );

				if ( $img_Class !== 'swiper-lazy' ) {
					$image = '<img class="' . esc_attr( $img_class ) . '" src="' . esc_url( $imgSrc ) . '" width="' . absint( $w ) . '" height="' . absint( $h ) . '" alt="' . esc_attr( $alt ) . '"/>';
				} else {
					$image = '<img class="' . esc_attr( $img_class ) . ' img-responsive" data-src="' . esc_url( $imgSrc ) . '" src="#none" width="' . absint( $w ) . '" height="' . absint( $h ) . '" alt="' . esc_attr( $alt ) . '"/><div class="lazy-overlay-wrap"><div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div></div>';
				}
			}
		}

		return $image;
	}

	public static function getFeatureImageUrl( $post_id = null, $fImgSize = 'medium' ) {
		$image = $imgSrc = null;

		if ( $aID = get_post_thumbnail_id( $post_id ) ) {
			$image = wp_get_attachment_image_src( $aID, $fImgSize );
		}

		if ( is_array( $image ) ) {
			$imgSrc = $image[0];
		}

		return $imgSrc;
	}

	public static function tpgCharacterLimit( $limit, $content ) {
		$limit ++;

		$text = '';

		if ( mb_strlen( $content ) > $limit ) {
			$subex   = mb_substr( $content, 0, $limit );
			$exwords = explode( ' ', $subex );
			$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );

			if ( $excut < 0 ) {
				$text = mb_substr( $subex, 0, $excut );
			} else {
				$text = $subex;
			}
		} else {
			$text = $content;
		}

		return $text;
	}

	public static function get_the_excerpt( $post_id, $data = [] ) {
		$type = $data['excerpt_type'] ?? '';
		$post = get_post( $post_id );

		if ( empty( $post ) ) {
			return '';
		}

		if ( $type == 'full' ) {
			ob_start();
			the_content();
			$content = ob_get_clean();

			return apply_filters( 'tpg_content_full', $content, $post_id, $data );
		} else {
			if ( class_exists( 'ET_GB_Block_Layout' ) ) {
				$defaultExcerpt = $post->post_excerpt ?: wp_trim_words( $post->post_content, 55 );
			} elseif ( defined( 'WPB_VC_VERSION' ) ) {
				$the_content    = $post->post_excerpt ?: wp_trim_words( $post->post_content, 55 );
				$shortcode_tags = [ 'VC_COLUMN_INNTER' ];
				$values         = array_values( $shortcode_tags );
				$exclude_codes  = implode( '|', $values );
				$defaultExcerpt = trim( preg_replace( "~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $the_content ?? '' ) );
			} else {
				$defaultExcerpt = get_the_excerpt( $post_id );
			}

			$limit   = isset( $data['excerpt_limit'] ) && $data['excerpt_limit'] ? abs( $data['excerpt_limit'] ) : 0;
			$more    = $data['excerpt_more_text'] ?? '';
			$excerpt = preg_replace( '`\[[^\]]*\]`', '', $defaultExcerpt ?? '' );
			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt ?? '' );
			$excerpt = str_replace( '…', '', $excerpt );

			if ( $limit ) {
				$excerpt = wp_strip_all_tags( $excerpt );

				if ( $type == 'word' ) {
					$limit      = $limit + 1;
					$rawExcerpt = $excerpt;
					$excerpt    = explode( ' ', $excerpt, $limit );

					if ( count( $excerpt ) >= $limit ) {
						array_pop( $excerpt );
						$excerpt = implode( ' ', $excerpt );
					} else {
						$excerpt = $rawExcerpt;
					}
				} else {
					$excerpt = self::tpgCharacterLimit( $limit, $excerpt );
				}
				$excerpt = stripslashes( $excerpt );
			} else {
				$allowed_html = [
					'a'      => [
						'href'  => [],
						'title' => [],
					],
					'strong' => [],
					'b'      => [],
					'br'     => [ [] ],
				];

				$excerpt = nl2br( wp_kses( $excerpt, $allowed_html ) );
			}

			$excerpt = ( $more ? rtrim( $excerpt, ' .,-_' ) . $more : $excerpt );

			return apply_filters( 'tpg_get_the_excerpt', $excerpt, $post_id, $data, $defaultExcerpt );
		}
	}

	public static function get_the_title( $post_id, $data = [] ) {
		$title      = $originalTitle = get_the_title( $post_id );
		$limit      = isset( $data['title_limit'] ) ? absint( $data['title_limit'] ) : 0;
		$limit_type = isset( $data['title_limit_type'] ) ? trim( $data['title_limit_type'] ) : 'character';

		if ( $limit ) {
			if ( $limit_type == 'word' ) {
				$limit = $limit + 1;
				$title = explode( ' ', $title, $limit );

				if ( count( $title ) >= $limit ) {
					array_pop( $title );
					$title = implode( ' ', $title );
				} else {
					$title = $originalTitle;
				}
			} else {
				if ( $limit > 0 && strlen( $title ) > $limit ) {
					$title = mb_substr( $title, 0, $limit, 'utf-8' );
					$title = preg_replace( '/\W\w+\s*(\W*)$/', '$1', $title ?? '' );
				}
			}
		}

		return apply_filters( 'tpg_get_the_title', $title, $post_id, $data, $originalTitle );
	}


	public static function rt_pagination( $postGrid, $range = 4, $ajax = false ) {
		$range = 4;
		if ( ! empty( self::tpg_option( 'tpg_pagination_range' ) ) ) {
			$range = self::tpg_option( 'tpg_pagination_range' );
		}
		$html      = null;
		$showitems = ( $range * 2 ) + 1;

		$wpQuery = $postGrid;

		global $wp_query;

		if ( empty( $wpQuery ) ) {
			$wpQuery = $wp_query;
		}

		$pages = ! empty( $wpQuery->max_num_pages ) ? $wpQuery->max_num_pages : 1;
		$paged = ! empty( $wpQuery->query['paged'] ) ? $wpQuery->query['paged'] : 1;

		if ( is_front_page() ) {
			if ( ! empty( $wp_query->query['paged'] ) ) {
				$paged = $wp_query->query['paged'];
			} elseif ( get_query_var( 'paged' ) ) {
				$paged = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$paged = get_query_var( 'page' );
			} else {
				$paged = 1;
			}
		}

		$ajaxClass = null;
		$dataAttr  = null;

		if ( $ajax ) {
			$ajaxClass = ' rt-ajax';
			$dataAttr  = "data-paged='1'";
		}

		if ( 1 != $pages ) {
			$html .= '<div class="rt-pagination' . $ajaxClass . '" ' . $dataAttr . '>';
			$html .= '<ul class="pagination-list">';
			if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages && ! $ajax ) {
				$html .= "<li><a data-paged='1' href='" . get_pagenum_link( 1 ) . "' aria-label='First'>&laquo;</a></li>";
			}

			if ( $paged > 1 && $showitems < $pages && ! $ajax ) {
				$p    = $paged - 1;
				$html .= "<li><a data-paged='{$p}' href='" . get_pagenum_link( $p ) . "' aria-label='Previous'>&lsaquo;</a></li>";
			}

			if ( $ajax ) {
				for ( $i = 1; $i <= $pages; $i ++ ) {
					$html .= ( $paged == $i ) ? '<li class="active"><span>' . $i . '</span></li>' : "<li><a data-paged='{$i}' href='" . get_pagenum_link( $i ) . "'>" . $i . '</a></li>';
				}
			} else {
				for ( $i = 1; $i <= $pages; $i ++ ) {
					if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
						$html .= ( $paged == $i ) ? '<li class="active"><span>' . $i . '</span></li>' : "<li><a data-paged='{$i}' href='" . get_pagenum_link( $i ) . "'>" . $i . '</a></li>';
					}
				}
			}

			if ( $paged < $pages && $showitems < $pages && ! $ajax ) {
				$p    = $paged + 1;
				$html .= "<li><a data-paged='{$p}' href=\"" . get_pagenum_link( $paged + 1 ) . "\"  aria-label='Next'>&rsaquo;</a></li>";
			}

			if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages && ! $ajax ) {
				$html .= "<li><a data-paged='{$pages}' href='" . get_pagenum_link( $pages ) . "' aria-label='Last'>&raquo;</a></li>";
			}

			$html .= '</ul>';
			$html .= '</div>';
		}

		return $html;
	}

	public static function rt_pagination_ajax( $scID, $range = 4, $pages = '' ) {
		$html = null;

		$html .= "<div class='rt-tpg-pagination-ajax' data-sc-id='{$scID}' data-paged='1'>";
		$html .= '</div>';

		return $html;
	}

	/**
	 * Call the Image resize model for resize function
	 *
	 * @param              $url
	 * @param null $width
	 * @param null $height
	 * @param null $crop
	 * @param bool|true $single
	 * @param bool|false $upscale
	 *
	 * @return array|bool|string
	 * @throws Exception
	 * @throws Rt_Exception
	 */
	public static function rtImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		$rtResize = new ReSizer();

		return $rtResize->process( $url, $width, $height, $crop, $single, $upscale );
	}


	/* Convert hexdec color string to rgb(a) string */
	public static function rtHex2rgba( $color, $opacity = .5 ) {
		$default = 'rgb(0,0,0)';

		// Return default if no color provided.
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided.
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values.
		if ( strlen( $color ) == 6 ) {
			$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
		} elseif ( strlen( $color ) == 3 ) {
			$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
		} else {
			return $default;
		}

		// Convert hexadec to rgb.
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(rgba or rgb).
		if ( $opacity ) {
			if ( absint( $opacity ) > 1 ) {
				$opacity = 1.0;
			}

			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

		// Return rgb(a) color string.
		return $output;
	}

	public static function meta_exist( $meta_key, $post_id = null, $type = 'post' ) {
		if ( ! $post_id ) {
			return false;
		}

		return metadata_exists( $type, $post_id, $meta_key );
	}


	public static function get_offset_col( $col ) {
		$return = [
			'big'   => 6,
			'small' => 6,
		];

		if ( $col ) {
			if ( $col == 12 ) {
				$return['big']   = 12;
				$return['small'] = 12;
			} elseif ( $col == 6 ) {
				$return['big']   = 6;
				$return['small'] = 6;
			} elseif ( $col == 4 ) {
				$return['big']   = 4;
				$return['small'] = 8;
			}
		}

		return $return;
	}

	public static function formatSpacing( $data = '' ) {
		if ( ! empty( $data ) ) {
			$spacing = array_filter( explode( ',', $data ), 'is_numeric' );

			if ( count( $spacing ) > 4 ) {
				$spacing = array_slice( $spacing, 0, 4, true );
			}

			$data = implode( 'px ', $spacing );
		}

		return $data;
	}

	public static function layoutStyle( $layoutID, $scMeta, $layout, $scId = null ) {
		$css = null;
		$css .= "<style type='text/css' media='all'>";
		// primary color
		if ( $scId ) {
			$primaryColor                   = ( isset( $scMeta['primary_color'][0] ) ? $scMeta['primary_color'][0] : null );
			$button_bg_color                = ( isset( $scMeta['button_bg_color'][0] ) ? $scMeta['button_bg_color'][0] : null );
			$button_active_bg_color         = ( isset( $scMeta['button_active_bg_color'][0] ) ? $scMeta['button_active_bg_color'][0] : null );
			$button_hover_bg_color          = ( isset( $scMeta['button_hover_bg_color'][0] ) ? $scMeta['button_hover_bg_color'][0] : null );
			$button_text_color              = ( isset( $scMeta['button_text_bg_color'][0] ) ? $scMeta['button_text_bg_color'][0]
				: ( isset( $scMeta['button_text_color'][0] ) ? $scMeta['button_text_color'][0] : null ) );
			$button_hover_text_color        = ( isset( $scMeta['button_hover_text_color'][0] ) ? $scMeta['button_hover_text_color'][0] : null );
			$button_border_color            = ( isset( $scMeta['button_border_color'][0] ) ? $scMeta['button_border_color'][0] : null );
			$overlay_color                  = ( ! empty( $scMeta['overlay_color'][0] ) ? self::rtHex2rgba(
				$scMeta['overlay_color'][0],
				! empty( $scMeta['overlay_opacity'][0] ) ? absint( $scMeta['overlay_opacity'][0] ) / 10 : .8
			) : null );
			$overlay_padding                = ( ! empty( $scMeta['overlay_padding'][0] ) ? absint( $scMeta['overlay_padding'][0] ) : null );
			$gutter                         = ! empty( $scMeta['tgp_gutter'][0] ) ? absint( $scMeta['tgp_gutter'][0] ) : null;
			$read_more_button_border_radius = isset( $scMeta['tpg_read_more_button_border_radius'][0] ) ? $scMeta['tpg_read_more_button_border_radius'][0] : '';
			// Section
			$sectionBg      = ( isset( $scMeta['tpg_full_area_bg'][0] ) ? $scMeta['tpg_full_area_bg'][0] : null );
			$sectionMargin  = ( isset( $scMeta['tpg_full_area_margin'][0] ) ? $scMeta['tpg_full_area_margin'][0] : null );
			$sectionMargin  = self::formatSpacing( $sectionMargin );
			$sectionPadding = ( isset( $scMeta['tpg_full_area_padding'][0] ) ? $scMeta['tpg_full_area_padding'][0] : null );
			$sectionPadding = self::formatSpacing( $sectionPadding );
			// Box
			$boxBg           = ( isset( $scMeta['tpg_content_wrap_bg'][0] ) ? $scMeta['tpg_content_wrap_bg'][0] : null );
			$boxBorder       = ( isset( $scMeta['tpg_content_wrap_border'][0] ) ? $scMeta['tpg_content_wrap_border'][0] : null );
			$boxBorderColor  = ( isset( $scMeta['tpg_content_wrap_border_color'][0] ) ? $scMeta['tpg_content_wrap_border_color'][0] : null );
			$boxBorderRadius = ( isset( $scMeta['tpg_content_wrap_border_radius'][0] ) ? $scMeta['tpg_content_wrap_border_radius'][0] : null );
			$boxShadow       = ( isset( $scMeta['tpg_content_wrap_shadow'][0] ) ? $scMeta['tpg_content_wrap_shadow'][0] : null );
			$boxPadding      = ( isset( $scMeta['tpg_box_padding'][0] ) ? $scMeta['tpg_box_padding'][0] : null );
			$boxPadding      = self::formatSpacing( $boxPadding );
			$contentPadding  = ( isset( $scMeta['tpg_content_padding'][0] ) ? $scMeta['tpg_content_padding'][0] : null );
			$contentPadding  = self::formatSpacing( $contentPadding );
			// Heading
			$headingBg          = ( isset( $scMeta['tpg_heading_bg'][0] ) ? $scMeta['tpg_heading_bg'][0] : null );
			$headingColor       = ( isset( $scMeta['tpg_heading_color'][0] ) ? $scMeta['tpg_heading_color'][0] : null );
			$headingBorderColor = ( isset( $scMeta['tpg_heading_border_color'][0] ) ? $scMeta['tpg_heading_border_color'][0] : null );
			$headingBorderSize  = ( isset( $scMeta['tpg_heading_border_size'][0] ) ? $scMeta['tpg_heading_border_size'][0] : null );
			$headingMargin      = ( isset( $scMeta['tpg_heading_margin'][0] ) ? $scMeta['tpg_heading_margin'][0] : null );
			$headingMargin      = self::formatSpacing( $headingMargin );
			$headingPadding     = ( isset( $scMeta['tpg_heading_padding'][0] ) ? $scMeta['tpg_heading_padding'][0] : null );
			$headingPadding     = self::formatSpacing( $headingPadding );
			// Category
			$catBg           = ( isset( $scMeta['tpg_category_bg'][0] ) ? $scMeta['tpg_category_bg'][0] : null );
			$catTextColor    = ( isset( $scMeta['tpg_category_color'][0] ) ? $scMeta['tpg_category_color'][0] : null );
			$catBorderRadius = ( isset( $scMeta['tpg_category_border_radius'][0] ) ? $scMeta['tpg_category_border_radius'][0] : null );
			$catMargin       = ( isset( $scMeta['tpg_category_margin'][0] ) ? $scMeta['tpg_category_margin'][0] : null );
			$catMargin       = self::formatSpacing( $catMargin );
			$catPadding      = ( isset( $scMeta['tpg_category_padding'][0] ) ? $scMeta['tpg_category_padding'][0] : null );
			$catPadding      = self::formatSpacing( $catPadding );
			$categorySize    = ( ! empty( $scMeta['rt_tpg_category_font_size'][0] ) ? absint( $scMeta['rt_tpg_category_font_size'][0] ) : null );
			// Image
			$image_border_radius = isset( $scMeta['tpg_image_border_radius'][0] ) ? $scMeta['tpg_image_border_radius'][0] : '';
			// Title
			$title_color     = ( ! empty( $scMeta['title_color'][0] ) ? $scMeta['title_color'][0] : null );
			$title_size      = ( ! empty( $scMeta['title_size'][0] ) ? absint( $scMeta['title_size'][0] ) : null );
			$title_weight    = ( ! empty( $scMeta['title_weight'][0] ) ? $scMeta['title_weight'][0] : null );
			$title_alignment = ( ! empty( $scMeta['title_alignment'][0] ) ? $scMeta['title_alignment'][0] : null );

			$title_hover_color = ( ! empty( $scMeta['title_hover_color'][0] ) ? $scMeta['title_hover_color'][0] : null );

			$excerpt_color     = ( ! empty( $scMeta['excerpt_color'][0] ) ? $scMeta['excerpt_color'][0] : null );
			$excerpt_size      = ( ! empty( $scMeta['excerpt_size'][0] ) ? absint( $scMeta['excerpt_size'][0] ) : null );
			$excerpt_weight    = ( ! empty( $scMeta['excerpt_weight'][0] ) ? $scMeta['excerpt_weight'][0] : null );
			$excerpt_alignment = ( ! empty( $scMeta['excerpt_alignment'][0] ) ? $scMeta['excerpt_alignment'][0] : null );

			$meta_data_color     = ( ! empty( $scMeta['meta_data_color'][0] ) ? $scMeta['meta_data_color'][0] : null );
			$meta_data_size      = ( ! empty( $scMeta['meta_data_size'][0] ) ? absint( $scMeta['meta_data_size'][0] ) : null );
			$meta_data_weight    = ( ! empty( $scMeta['meta_data_weight'][0] ) ? $scMeta['meta_data_weight'][0] : null );
			$meta_data_alignment = ( ! empty( $scMeta['meta_data_alignment'][0] ) ? $scMeta['meta_data_alignment'][0] : null );
		} else {
			$primaryColor                   = ( isset( $scMeta['primary_color'] ) ? $scMeta['primary_color'] : null );
			$button_bg_color                = ( isset( $scMeta['button_bg_color'] ) ? $scMeta['button_bg_color'] : null );
			$button_active_bg_color         = ( isset( $scMeta['button_active_bg_color'] ) ? $scMeta['button_active_bg_color'] : null );
			$button_hover_bg_color          = ( isset( $scMeta['button_hover_bg_color'] ) ? $scMeta['button_hover_bg_color'] : null );
			$btn_text_color                 = ( isset( $scMeta['button_text_color'] ) ? $scMeta['button_text_color'] : null );
			$button_text_color              = ( ! empty( $scMeta['button_text_bg_color'] ) ? $scMeta['button_text_bg_color']
				: ( ! empty( $btn_text_color ) ? $btn_text_color : null ) );
			$button_border_color            = ( isset( $scMeta['button_border_color'] ) ? $scMeta['button_border_color'] : null );
			$button_hover_text_color        = ( isset( $scMeta['button_hover_text_color'] ) ? $scMeta['button_hover_text_color'] : null );
			$overlay_color                  = ( ! empty( $scMeta['overlay_color'] ) ? self::rtHex2rgba(
				$scMeta['overlay_color'],
				! empty( $scMeta['overlay_opacity'] ) ? absint( $scMeta['overlay_opacity'] ) / 10 : .8
			) : null );
			$overlay_padding                = ( ! empty( $scMeta['overlay_padding'] ) ? absint( $scMeta['overlay_padding'] ) : null );
			$gutter                         = ! empty( $scMeta['tgp_gutter'] ) ? absint( $scMeta['tgp_gutter'] ) : null;
			$read_more_button_border_radius = isset( $scMeta['tpg_read_more_button_border_radius'] ) ? $scMeta['tpg_read_more_button_border_radius'] : '';
			// Section
			$sectionBg      = ( isset( $scMeta['tpg_full_area_bg'] ) ? $scMeta['tpg_full_area_bg'] : null );
			$sectionMargin  = ( isset( $scMeta['tpg_full_area_margin'] ) ? $scMeta['tpg_full_area_margin'] : null );
			$sectionMargin  = self::formatSpacing( $sectionMargin );
			$sectionPadding = ( isset( $scMeta['tpg_full_area_padding'] ) ? $scMeta['tpg_full_area_padding'] : null );
			$sectionPadding = self::formatSpacing( $sectionPadding );
			// Box
			$boxBg           = ( isset( $scMeta['tpg_content_wrap_bg'] ) ? $scMeta['tpg_content_wrap_bg'] : null );
			$boxBorder       = ( isset( $scMeta['tpg_content_wrap_border'] ) ? $scMeta['tpg_content_wrap_border'] : null );
			$boxBorderColor  = ( isset( $scMeta['tpg_content_wrap_border_color'] ) ? $scMeta['tpg_content_wrap_border_color'] : null );
			$boxBorderRadius = ( isset( $scMeta['tpg_content_wrap_border_radius'] ) ? $scMeta['tpg_content_wrap_border_radius'] : null );
			$boxShadow       = ( isset( $scMeta['tpg_content_wrap_shadow'] ) ? $scMeta['tpg_content_wrap_shadow'] : null );
			$boxPadding      = ( isset( $scMeta['tpg_box_padding'] ) ? $scMeta['tpg_box_padding'] : null );
			$boxPadding      = self::formatSpacing( $boxPadding );
			$contentPadding  = ( isset( $scMeta['tpg_content_padding'] ) ? $scMeta['tpg_content_padding'] : null );
			$contentPadding  = self::formatSpacing( $contentPadding );
			// Heading
			$headingBg          = ( isset( $scMeta['tpg_heading_bg'] ) ? $scMeta['tpg_heading_bg'] : null );
			$headingColor       = ( isset( $scMeta['tpg_heading_color'] ) ? $scMeta['tpg_heading_color'] : null );
			$headingBorderColor = ( isset( $scMeta['tpg_heading_border_color'] ) ? $scMeta['tpg_heading_border_color'] : null );
			$headingBorderSize  = ( isset( $scMeta['tpg_heading_border_size'] ) ? $scMeta['tpg_heading_border_size'] : null );
			$headingMargin      = ( isset( $scMeta['tpg_heading_margin'] ) ? $scMeta['tpg_heading_margin'] : null );
			$headingMargin      = self::formatSpacing( $headingMargin );
			$headingPadding     = ( isset( $scMeta['tpg_heading_padding'] ) ? $scMeta['tpg_heading_padding'] : null );
			$headingPadding     = self::formatSpacing( $headingPadding );
			// Category
			$catBg           = ( isset( $scMeta['tpg_category_bg'] ) ? $scMeta['tpg_category_bg'] : null );
			$catTextColor    = ( isset( $scMeta['tpg_category_color'] ) ? $scMeta['tpg_category_color'] : null );
			$catBorderRadius = ( isset( $scMeta['tpg_category_border_radius'] ) ? $scMeta['tpg_category_border_radius'] : null );
			$catMargin       = ( isset( $scMeta['tpg_category_margin'] ) ? $scMeta['tpg_category_margin'] : null );
			$catPadding      = ( isset( $scMeta['tpg_category_padding'] ) ? $scMeta['tpg_category_padding'] : null );
			$categorySize    = ( ! empty( $scMeta['rt_tpg_category_font_size'] ) ? absint( $scMeta['rt_tpg_category_font_size'] ) : null );
			// Image
			$image_border_radius = isset( $scMeta['tpg_image_border_radius'] ) ? $scMeta['tpg_image_border_radius'] : '';
			// Title
			$title_color     = ( ! empty( $scMeta['title_color'] ) ? $scMeta['title_color'] : null );
			$title_size      = ( ! empty( $scMeta['title_size'] ) ? absint( $scMeta['title_size'] ) : null );
			$title_weight    = ( ! empty( $scMeta['title_weight'] ) ? $scMeta['title_weight'] : null );
			$title_alignment = ( ! empty( $scMeta['title_alignment'] ) ? $scMeta['title_alignment'] : null );

			$title_hover_color = ( ! empty( $scMeta['title_hover_color'] ) ? $scMeta['title_hover_color'] : null );

			$excerpt_color     = ( ! empty( $scMeta['excerpt_color'] ) ? $scMeta['excerpt_color'] : null );
			$excerpt_size      = ( ! empty( $scMeta['excerpt_size'] ) ? absint( $scMeta['excerpt_size'] ) : null );
			$excerpt_weight    = ( ! empty( $scMeta['excerpt_weight'] ) ? $scMeta['excerpt_weight'] : null );
			$excerpt_alignment = ( ! empty( $scMeta['excerpt_alignment'] ) ? $scMeta['excerpt_alignment'] : null );

			$meta_data_color     = ( ! empty( $scMeta['meta_data_color'] ) ? $scMeta['meta_data_color'] : null );
			$meta_data_size      = ( ! empty( $scMeta['meta_data_size'] ) ? absint( $scMeta['meta_data_size'] ) : null );
			$meta_data_weight    = ( ! empty( $scMeta['meta_data_weight'] ) ? $scMeta['meta_data_weight'] : null );
			$meta_data_alignment = ( ! empty( $scMeta['meta_data_alignment'] ) ? $scMeta['meta_data_alignment'] : null );
		}

		$id = str_replace( 'rt-tpg-container-', '', $layoutID );

		if ( $primaryColor ) {
			$css .= "#{$layoutID} .rt-holder .rt-woo-info .price{";
			$css .= 'color:' . $primaryColor . ';';
			$css .= '}';
			$css .= "body .rt-tpg-container .rt-tpg-isotope-buttons .selected,
					#{$layoutID} .layout12 .rt-holder:hover .rt-detail,
					#{$layoutID} .isotope8 .rt-holder:hover .rt-detail,
					#{$layoutID} .carousel8 .rt-holder:hover .rt-detail,
					#{$layoutID} .layout13 .rt-holder .overlay .post-info,
					#{$layoutID} .isotope9 .rt-holder .overlay .post-info,
					#{$layoutID}.rt-tpg-container .layout4 .rt-holder .rt-detail,
					.rt-modal-{$id} .md-content,
					.rt-modal-{$id} .md-content > .rt-md-content-holder .rt-md-content,
					.rt-popup-wrap-{$id}.rt-popup-wrap .rt-popup-navigation-wrap,
					#{$layoutID} .carousel9 .rt-holder .overlay .post-info{";
			$css .= 'background-color:' . $primaryColor . ';';
			$css .= '}';

			$ocp = self::rtHex2rgba(
				$primaryColor,
				! empty( $scMeta['overlay_opacity'][0] ) ? absint( $scMeta['overlay_opacity'][0] ) / 10 : .8
			);
			$css .= "#{$layoutID} .layout5 .rt-holder .overlay, #{$layoutID} .isotope2 .rt-holder .overlay, #{$layoutID} .carousel2 .rt-holder .overlay,#{$layoutID} .layout15 .rt-holder h3, #{$layoutID} .isotope11 .rt-holder h3, #{$layoutID} .carousel11 .rt-holder h3, #{$layoutID} .layout16 .rt-holder h3,
					#{$layoutID} .isotope12 .rt-holder h3, #{$layoutID} .carousel12 .rt-holder h3 {";
			$css .= 'background-color:' . $ocp . ';';
			$css .= '}';
		}

		if ( $button_border_color ) {
			$css .= "#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item,
					#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap{";
			$css .= 'border-color:' . $button_border_color . ' !important;';
			$css .= '}';
			$css .= "#{$layoutID} .rt-holder .read-more a {";
			$css .= 'border-color:' . $button_border_color . ';';
			$css .= '}';
		}

		if ( $button_bg_color ) {
			$css .= "#{$layoutID} .pagination-list li a,
					{$layoutID} .pagination-list li span,
					{$layoutID} .pagination li a,
					#{$layoutID} .rt-tpg-isotope-buttons button,
					#{$layoutID} .rt-tpg-utility .rt-tpg-load-more button,
					#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn,
					#{$layoutID}.rt-tpg-container .swiper-pagination-bullet,
					#{$layoutID} .wc1 .rt-holder .rt-img-holder .overlay .product-more ul li a,
					#{$layoutID} .wc2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc3 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc4 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc-carousel2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc-isotope2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li>a,
					#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap  .rt-loadmore-btn,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-cb-page-prev-next > *,
					#{$layoutID} .rt-read-more,
					#rt-tooltip-{$id}, #rt-tooltip-{$id} .rt-tooltip-bottom:after{";
			$css .= 'background-color:' . $button_bg_color . ';';
			$css .= '}';
			$css .= "#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item{";
			$css .= 'border-color:' . $button_bg_color . ';';
			$css .= '}';
			$css .= "#{$layoutID}.rt-tpg-container .layout17 .rt-holder .overlay a.tpg-zoom .fa{";
			$css .= 'color:' . $button_bg_color . ';';
			$css .= '}';

			$css .= "#{$layoutID} .rt-holder .read-more a {";
			$css .= 'background-color:' . $button_bg_color . ';padding: 8px 15px;';
			$css .= '}';
		}

		// button active color.
		if ( $button_active_bg_color ) {
			$css .= "#{$layoutID} .pagination li.active span,
					#{$layoutID} .pagination-list li.active span,
					#{$layoutID} .rt-tpg-isotope-buttons button.selected,
					#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item.selected,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active>a,
					#{$layoutID}.rt-tpg-container .swiper-pagination-bullet.swiper-pagination-bullet-active-main{";
			$css .= 'background-color:' . $button_active_bg_color . ';';
			$css .= '}';

			$css .= "#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item.selected,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active>a{";
			$css .= 'border-color:' . $button_active_bg_color . ';';
			$css .= '}';
		}

		// Button hover bg color.
		if ( $button_hover_bg_color ) {
			$css .= "#{$layoutID} .pagination-list li a:hover,
					#{$layoutID} .pagination li a:hover,
					#{$layoutID} .rt-tpg-isotope-buttons button:hover,
					#{$layoutID} .rt-holder .read-more a:hover,
					#{$layoutID} .rt-tpg-utility .rt-tpg-load-more button:hover,
					#{$layoutID}.rt-tpg-container .swiper-pagination-bullet:hover,
					#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn:hover,
					#{$layoutID} .wc1 .rt-holder .rt-img-holder .overlay .product-more ul li a:hover,
					#{$layoutID} .wc2 .rt-detail .rt-wc-add-to-cart:hover,
					#{$layoutID} .wc3 .rt-detail .rt-wc-add-to-cart:hover,
					#{$layoutID} .wc4 .rt-detail .rt-wc-add-to-cart:hover,
					#{$layoutID} .wc-carousel2 .rt-detail .rt-wc-add-to-cart:hover,
					#{$layoutID} .wc-isotope2 .rt-detail .rt-wc-add-to-cart:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item.selected,
					#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item:hover,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li>a:hover,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-cb-page-prev-next > *:hover,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap  .rt-loadmore-btn:hover,
					#{$layoutID} .rt-read-more:hover,
					#{$layoutID} .rt-tpg-utility .rt-tpg-load-more button:hover{";
			$css .= 'background-color:' . $button_hover_bg_color . ';';
			$css .= '}';

			$css .= "#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover,
						#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item:hover,
						#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn:hover,
						#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li>a:hover{";
			$css .= 'border-color:' . $button_hover_bg_color . ';';
			$css .= '}';
			$css .= "#{$layoutID}.rt-tpg-container .layout17 .rt-holder .overlay a.tpg-zoom:hover .fa{";
			$css .= 'color:' . $button_hover_bg_color . ';';
			$css .= '}';
		}

		// Button text color.
		if ( $button_text_color ) {
			$css .= "#{$layoutID} .pagination-list li a,
					#{$layoutID} .pagination li a,
					#{$layoutID} .rt-tpg-isotope-buttons button,
					#{$layoutID} .rt-holder .read-more a,
					#{$layoutID} .rt-tpg-utility .rt-tpg-load-more button,
					#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn,
					#{$layoutID} .wc1 .rt-holder .rt-img-holder .overlay .product-more ul li a,
					#{$layoutID} .edd1 .rt-holder .rt-img-holder .overlay .product-more ul li a,
					#{$layoutID} .wc2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc3 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .edd2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc4 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .edd3 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc-carousel2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .wc-isotope2 .rt-detail .rt-wc-add-to-cart,
					#{$layoutID} .rt-tpg-utility .rt-tpg-load-more button,
					#rt-tooltip-{$id},
					#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li>a,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-cb-page-prev-next > *,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap  .rt-loadmore-btn,
					#{$layoutID} .rt-read-more,
					#rt-tooltip-{$id} .rt-tooltip-bottom:after{";
			$css .= 'color:' . $button_text_color . ';';
			$css .= '}';
		}

		if ( $button_hover_text_color ) {
			$css .= "#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover,
					#{$layoutID} .rt-holder .read-more a:hover,
					#{$layoutID}.rt-tpg-container .swiper-navigation .slider-btn:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-sub-tax.sub-button-group .rt-filter-button-item:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item.selected,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active>a:hover,
					#{$layoutID} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected,
					#{$layoutID} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap  .rt-loadmore-btn:hover,
					#{$layoutID} .rt-read-more:hover,
					#{$layoutID}.rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active>a{";
			$css .= 'color:' . $button_hover_text_color . ';';
			$css .= '}';
		}

		if ( $overlay_color || $overlay_padding ) {
			if ( in_array( $layout, [ 'layout15', 'isotope11', 'carousel11' ] ) ) {
				$css .= "#{$layoutID} .{$layout} .rt-holder:hover .overlay .post-info{";
			} elseif ( in_array(
				$layout,
				[ 'layout10', 'isotope7', 'carousel6', 'carousel7', 'layout9', 'offset04' ]
			)
			) {
				$css .= "#{$layoutID} .{$layout} .rt-holder .post-info{";
			} elseif ( in_array( $layout, [ 'layout7', 'isotope4', 'carousel4' ] ) ) {
				$css .= "#{$layoutID} .{$layout} .rt-holder .overlay:hover{";
			} elseif ( in_array( $layout, [ 'layout16', 'isotope12', 'carousel12' ] ) ) {
				$css .= "#{$layoutID} .{$layout} .rt-holder .overlay .post-info {";
			} elseif ( in_array( $layout, [ 'offset03', 'carousel5' ] ) ) {
				$css .= "#{$layoutID} .{$layout} .rt-holder .overlay{";
			} else {
				$css .= "#{$layoutID} .rt-post-overlay .post-img > a:first-of-type::after,";
				$css .= "#{$layoutID} .rt-holder .overlay:hover{";
			}

			if ( $overlay_color ) {
				$css .= 'background-image: none;';
				$css .= 'background-color:' . $overlay_color . ';';
			}

			if ( $overlay_padding ) {
				$css .= 'padding-top:' . $overlay_padding . '%;';
			}

			$css .= '}';
		}

		if ( $boxShadow ) {
			$css .= "#{$layoutID} .{$layout} .rt-holder {";
			$css .= "box-shadow : 0px 0px 2px 0px {$boxShadow};";
			$css .= '}';
		}

		/* gutter */
		if ( $gutter ) {
			$css .= "#{$layoutID} [class*='rt-col-'] {";
			$css .= "padding-left : {$gutter}px !important;";
			$css .= "padding-right : {$gutter}px !important;";
			$css .= "margin-top : {$gutter}px;";
			$css .= "margin-bottom : {$gutter}px;";
			$css .= '}';
			$css .= "#{$layoutID} .rt-row{";
			$css .= "margin-left : -{$gutter}px !important;";
			$css .= "margin-right : -{$gutter}px !important;";
			$css .= '}';
			$css .= "#{$layoutID}.rt-container-fluid,#{$layoutID}.rt-container{";
			$css .= "padding-left : {$gutter}px;";
			$css .= "padding-right : {$gutter}px;";
			$css .= '}';

			// remove inner row margin.
			$css .= "#{$layoutID} .rt-row .rt-row [class*='rt-col-'] {";
			$css .= 'margin-top : 0;';
			$css .= '}';
		}

		// Read more button border radius.
		if ( isset( $read_more_button_border_radius ) || trim( $read_more_button_border_radius ) !== '' ) {
			$css .= "#{$layoutID} .read-more a{";
			$css .= 'border-radius:' . $read_more_button_border_radius . 'px;';
			$css .= '}';
		}

		// Section.
		if ( $sectionBg ) {
			$css .= "#{$layoutID}.rt-tpg-container {";
			$css .= 'background:' . $sectionBg . ';';
			$css .= '}';
		}

		if ( $sectionMargin ) {
			$css .= "#{$layoutID}.rt-tpg-container {";
			$css .= 'margin:' . $sectionMargin . 'px;';
			$css .= '}';
		}

		if ( $sectionPadding ) {
			$css .= "#{$layoutID}.rt-tpg-container {";
			$css .= 'padding:' . $sectionPadding . 'px;';
			$css .= '}';
		}

		// Box.
		if ( $boxBg ) {
			$css .= "#{$layoutID} .rt-holder, #{$layoutID} .rt-holder .rt-detail,#{$layoutID} .rt-post-overlay .post-img + .post-content {";
			$css .= 'background-color:' . $boxBg . ';';
			$css .= '}';
		}

		if ( $boxBorderColor ) {
			$css .= "#{$layoutID} .rt-holder {";
			$css .= 'border-color:' . $boxBorderColor . ';';
			$css .= '}';
		}

		if ( $boxBorder ) {
			$css .= "#{$layoutID} .rt-holder {";
			$css .= 'border-style: solid;';
			$css .= 'border-width:' . $boxBorder . 'px;';
			$css .= '}';
		}

		if ( $boxBorderRadius ) {
			$css .= "#{$layoutID} .rt-holder {";
			$css .= 'border-radius:' . $boxBorderRadius . 'px;';
			$css .= '}';
		}

		if ( $boxPadding ) {
			$css .= "#{$layoutID} .rt-holder {";
			$css .= 'padding:' . $boxPadding . 'px;';
			$css .= '}';
		}

		if ( $contentPadding ) {
			$css .= "#{$layoutID} .rt-holder .rt-detail {";
			$css .= 'padding:' . $contentPadding . 'px;';
			$css .= '}';
		}

		// Widget heading.
		if ( $headingBg ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading, #{$layoutID} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, #{$layoutID} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading {";
			$css .= 'background:' . $headingBg . ';';
			$css .= '}';

			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after {";
			$css .= 'border-top-color:' . $headingBg . ';';
			$css .= '}';
		}

		if ( $headingColor ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading, #{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading a, #{$layoutID} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, #{$layoutID} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading a, #{$layoutID} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading, #{$layoutID} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading a  {";
			$css .= 'color:' . $headingColor . ';';
			$css .= '}';
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading::before  {";
			$css .= 'background-color:' . $headingColor . ';';
			$css .= '}';
		}

		if ( $headingBorderSize ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1, #{$layoutID} .tpg-widget-heading-wrapper.heading-style2, #{$layoutID} .tpg-widget-heading-wrapper.heading-style3 {";
			// $css .= "border-bottom-style: solid;";
			$css .= 'border-bottom-width:' . $headingBorderSize . 'px;';
			$css .= '}';

			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line {";
			$css .= 'border-width:' . $headingBorderSize . 'px 0;';
			$css .= '}';
		}

		if ( $headingBorderColor ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line, #{$layoutID} .tpg-widget-heading-wrapper.heading-style2, #{$layoutID} .tpg-widget-heading-wrapper.heading-style3  {";
			$css .= 'border-color:' . $headingBorderColor . ';';
			$css .= '}';
		}

		if ( $headingMargin ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper {";
			$css .= 'margin:' . $headingMargin . 'px;';
			$css .= '}';
		}

		if ( $headingPadding ) {
			$css .= "#{$layoutID} .tpg-widget-heading-wrapper .tpg-widget-heading {";
			$css .= 'padding:' . $headingPadding . 'px;';
			$css .= '}';
		}

		// Image border.
		if ( isset( $image_border_radius ) || trim( $image_border_radius ) !== '' ) {
			$css .= "#{$layoutID} .rt-img-holder img.rt-img-responsive,#{$layoutID} .rt-img-holder,
					#{$layoutID} .rt-post-overlay .post-img,
					#{$layoutID} .post-sm .post-img,
					#{$layoutID} .rt-post-grid .post-img,
					#{$layoutID} .post-img img {";
			$css .= 'border-radius:' . $image_border_radius . 'px;';
			$css .= '}';
		}

		// Title decoration.
		if ( $title_color || $title_size || $title_weight || $title_alignment ) {
			$css .= "#{$layoutID} .{$layout} .rt-holder h2.entry-title,
					#{$layoutID} .{$layout} .rt-holder h3.entry-title,
					#{$layoutID} .{$layout} .rt-holder h4.entry-title,
					#{$layoutID} .{$layout} .rt-holder h2.entry-title a,
					#{$layoutID} .{$layout} .rt-holder h3.entry-title a,
					#{$layoutID} .{$layout} .rt-holder h4.entry-title a,
					#{$layoutID} .rt-holder .rt-woo-info h2 a,
					#{$layoutID} .rt-holder .rt-woo-info h3 a,
					#{$layoutID} .rt-holder .rt-woo-info h4 a,
					#{$layoutID} .post-content .post-title,
					#{$layoutID} .rt-post-grid .post-title,
					#{$layoutID} .rt-post-grid .post-title a,
					#{$layoutID} .post-content .post-title a,
					#{$layoutID} .rt-holder .rt-woo-info h2,
					#{$layoutID} .rt-holder .rt-woo-info h3,
					#{$layoutID} .rt-holder .rt-woo-info h4{";

			if ( $title_color ) {
				$css .= 'color:' . $title_color . ';';
			}

			if ( $title_size ) {
				$lineHeight = $title_size + 10;
				$css        .= 'font-size:' . $title_size . 'px;';
				$css        .= 'line-height:' . $lineHeight . 'px;';
			}

			if ( $title_weight ) {
				$css .= 'font-weight:' . $title_weight . ';';
			}

			if ( $title_alignment ) {
				$css .= 'text-align:' . $title_alignment . ';';
			}

			$css .= '}';

			if ( $title_size ) {
				$css .= "#{$layoutID} .post-grid-lg-style-1 .post-title,
						#{$layoutID} .post-grid-lg-style-1 .post-title a,
						#{$layoutID} .big-layout .post-title,
						#{$layoutID} .big-layout .post-title a,
						#{$layoutID} .post-grid-lg-style-1 .post-title,
						#{$layoutID} .post-grid-lg-style-1 .post-title a {";
				$css .= 'font-size:' . ( $title_size + 8 ) . 'px;';
				$css .= 'line-height:' . ( $lineHeight + 8 ) . 'px;';
				$css .= '}';
			}
		}

		// Title hover color.
		if ( $title_hover_color ) {
			$css .= "#{$layoutID} .{$layout} .rt-holder h2.entry-title:hover,
					#{$layoutID} .{$layout} .rt-holder h3.entry-title:hover,
					#{$layoutID} .{$layout} .rt-holder h4.entry-title:hover,
					#{$layoutID} .{$layout} .rt-holder h2.entry-title a:hover,
					#{$layoutID} .{$layout} .rt-holder h3.entry-title a:hover,
					#{$layoutID} .{$layout} .rt-holder h4.entry-title a:hover,
					#{$layoutID} .post-content .post-title a:hover,
					#{$layoutID} .rt-post-grid .post-title a:hover,
					#{$layoutID} .rt-holder .rt-woo-info h2 a:hover,
					#{$layoutID} .rt-holder .rt-woo-info h3 a:hover,
					#{$layoutID} .rt-holder .rt-woo-info h4 a:hover,
					#{$layoutID} .rt-holder .rt-woo-info h2:hover,
					#{$layoutID} .rt-holder .rt-woo-info h3:hover,
					#{$layoutID} .rt-holder .rt-woo-info h4:hover{";
			$css .= 'color:' . $title_hover_color . ' !important;';
			$css .= '}';
		}
		// Excerpt decoration.
		if ( $excerpt_color || $excerpt_size || $excerpt_weight || $excerpt_alignment ) {
			$css .= "#{$layoutID} .{$layout} .rt-holder .tpg-excerpt,#{$layoutID} .{$layout} .tpg-excerpt,#{$layoutID} .{$layout} .rt-holder .post-content,#{$layoutID} .rt-holder .rt-woo-info p,#{$layoutID} .post-content p {";

			if ( $excerpt_color ) {
				$css .= 'color:' . $excerpt_color . ';';
			}

			if ( $excerpt_size ) {
				$css .= 'font-size:' . $excerpt_size . 'px;';
			}

			if ( $excerpt_weight ) {
				$css .= 'font-weight:' . $excerpt_weight . ';';
			}

			if ( $excerpt_alignment ) {
				$css .= 'text-align:' . $excerpt_alignment . ';';
			}

			$css .= '}';
		}

		// Post meta decoration.
		if ( $meta_data_color || $meta_data_size || $meta_data_weight || $meta_data_alignment ) {
			$css .= "#{$layoutID} .{$layout} .rt-holder .post-meta-user,
					#{$layoutID} .{$layout} .rt-meta,
					#{$layoutID} .{$layout} .rt-meta a,
					#{$layoutID} .{$layout} .rt-holder .post-meta-user .meta-data,
					#{$layoutID} .{$layout} .rt-holder .post-meta-user a,
					#{$layoutID} .{$layout} .rt-holder .rt-detail .post-meta .rt-tpg-social-share,
					#{$layoutID} .rt-post-overlay .post-meta-user span,
					#{$layoutID} .rt-post-overlay .post-meta-user,
					#{$layoutID} .rt-post-overlay .post-meta-user a,
					#{$layoutID} .rt-post-grid .post-meta-user,
					#{$layoutID} .rt-post-grid .post-meta-user a,
					#{$layoutID} .rt-post-box-media-style .post-meta-user,
					#{$layoutID} .rt-post-box-media-style .post-meta-user a,
					#{$layoutID} .{$layout} .post-meta-user i,
					#{$layoutID} .rt-detail .post-meta-category a,
					#{$layoutID} .{$layout} .post-meta-user a
					#{$layoutID} .{$layout} .post-meta-user a {";

			if ( $meta_data_color ) {
				$css .= 'color:' . $meta_data_color . ';';
			}

			if ( $meta_data_size ) {
				$css .= 'font-size:' . $meta_data_size . 'px;';
			}

			if ( $meta_data_weight ) {
				$css .= 'font-weight:' . $meta_data_weight . ';';
			}

			if ( $meta_data_alignment ) {
				$css .= 'text-align:' . $meta_data_alignment . ';';
			}

			$css .= '}';
		}

		// Category.
		if ( $catBg ) {
			$css .= "#{$layoutID} .cat-over-image.style2 .categories-links a,
					#{$layoutID} .cat-over-image.style3 .categories-links a,
					#{$layoutID} .cat-above-title.style2 .categories-links a,
					#{$layoutID} .cat-above-title.style3 .categories-links a,
					#{$layoutID} .rt-tpg-category > a {
						background-color: {$catBg};
					}";

			$css .= "#{$layoutID} .cat-above-title.style3 .categories-links a:after,
					.cat-over-image.style3 .categories-links a:after,
					#{$layoutID} .rt-tpg-category > a,
					#{$layoutID} .rt-tpg-category.style3 > a:after {
						border-top-color: {$catBg} ;
					}";

			$css .= "#{$layoutID} .rt-tpg-category:not(style1) i {
					color: {$catBg};
				}";
		}

		if ( $catTextColor ) {
			$css .= "#{$layoutID} .cat-over-image .categories-links a,
				#{$layoutID} .cat-above-title .categories-links a,
				#{$layoutID} .rt-tpg-category.style1 > i,
				#{$layoutID} .rt-tpg-category > a {";
			$css .= 'color:' . $catTextColor . ';';
			$css .= '}';
		}

		if ( $catBorderRadius ) {
			$css .= "#{$layoutID} .cat-over-image .categories-links a,#{$layoutID} .cat-above-title .categories-links a,#{$layoutID} .rt-tpg-category > a{";
			$css .= 'border-radius:' . $catBorderRadius . 'px;';
			$css .= '}';
		}

		if ( $catPadding ) {
			$css .= "#{$layoutID} .cat-over-image .categories-links a,#{$layoutID} .cat-above-title .categories-links a,#{$layoutID} .rt-tpg-category > a{";
			$css .= 'padding:' . $catPadding . 'px;';
			$css .= '}';
		}

		if ( $catMargin ) {
			$css .= "#{$layoutID} .categories-links,#{$layoutID} .rt-tpg-category > a{";
			$css .= 'margin:' . $catMargin . 'px;';
			$css .= '}';
		}

		if ( $categorySize ) {
			$css .= "#{$layoutID} .categories-links,#{$layoutID} .rt-tpg-category > a {";
			$css .= 'font-size:' . $categorySize . 'px;';
			$css .= '}';
		}

		$css .= '</style>';

		return $css;
	}

	public static function get_meta_keys( $post_type ) {
		$meta_keys = self::generate_meta_keys( $post_type );

		return $meta_keys;
	}

	public static function generate_meta_keys( $post_type ) {
		$meta_keys = [];

		if ( $post_type ) {
			global $wpdb;
			$query     = "SELECT DISTINCT($wpdb->postmeta.meta_key)
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_type = '%s'
					AND $wpdb->postmeta.meta_key != ''
					AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
					AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'";
			$meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		}

		return $meta_keys;
	}

	public static function remove_all_shortcode( $content ) {
		return preg_replace( '#\[[^\]]+\]#', '', $content ?? '' );
	}

	public static function remove_divi_shortcodes( $content ) {
		return preg_replace( '/\[\/?et_pb.*?\]/', '', $content ?? '' );
	}

	public static function is_acf() {
		$plugin = null;

		if ( class_exists( 'acf' ) ) {
			$plugin = 'acf';
		}

		return $plugin;
	}

	public static function is_woocommerce() {
		$plugin = null;
		if ( class_exists( 'WooCommerce' ) ) {
			$plugin = 'woo';
		}

		return $plugin;
	}

	public static function get_groups_by_post_type( $post_type ) {
		$post_type = $post_type ? $post_type : 'post';
		$groups    = [];
		$plugin    = self::is_acf();

		switch ( $plugin ) {
			case 'acf':
				$groups = self::get_groups_by_post_type_acf( $post_type );
				break;
		}

		return $groups;
	}

	/**
	 * Get ACF post group
	 *
	 * @param $post_type
	 *
	 * @return array
	 */
	public static function get_groups_by_post_type_acf( $post_type ) {
		$groups   = [];
		$groups_q = get_posts(
			[
				'post_type'      => 'acf-field-group',
				'posts_per_page' => - 1,
			]
		);

		if ( ! empty( $groups_q ) ) {
			foreach ( $groups_q as $group ) {
				$c    = $group->post_content ? unserialize( $group->post_content ) : [];
				$flag = false;

				if ( ! empty( $c['location'] ) ) {
					foreach ( $c['location'] as $rules ) {
						foreach ( $rules as $rule ) {
							if ( 'all' === $post_type ) {
								if ( ( ! empty( $rule['param'] ) && $rule['param'] == 'post_type' ) && ( ! empty( $rule['operator'] ) && $rule['operator'] == '==' )
								) {
									$flag = true;
								}
							} else {
								if ( ( ! empty( $rule['param'] ) && ( $rule['param'] == 'post_type' || ( $rule['param'] == 'post_category' && 'post' == $post_type ) ) ) && ( ! empty( $rule['operator'] ) && $rule['operator'] == '==' ) && ( ! empty( $rule['value'] ) && ( $rule['value'] == $post_type || ( $rule['param'] == 'post_category' && 'post' == $post_type ) ) )

								) {
									$flag = true;
								}
							}
						}
					}
				}
				if ( $flag ) {
					$groups[ $group->ID ] = $group->post_title;
				}
			}
		}

		return $groups;
	}

	/**
	 * Get Post view count meta key
	 *
	 * @return string
	 */
	public static function get_post_view_count_meta_key() {
		$settings  = get_option( rtTPG()->options['settings'] );
		$count_key = ! empty( $settings['tpg_count_key'] ) ? sanitize_text_field( $settings['tpg_count_key'] ) : 'tpg-post-view-count';

		return apply_filters( 'tpg_post_view_count', $count_key );
	}

	public static function get_post_view_count( $pid ) {
		$settings = get_option( rtTPG()->options['settings'] );

		$count = get_post_meta( $pid, self::get_post_view_count_meta_key(), true );

		if ( ! rtTPG()->hasPro() || ! isset( $settings['tpg_view_count_style'] ) ) {
			return $count;
		}

		switch ( $settings['tpg_view_count_style'] ) {
			case 'global':
				return self::number_shorten( $count );
			case 'indian':
				return self::number_to_lac( $count );
			default:
				return $count;
		}
	}



	/**
	 * Elementor Functionality
	 * ************************************************
	 */


	/**
	 * Default layout style check
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public static function el_ignore_layout( $data ) {

		if ( isset( $data['category'] ) && 'category' == $data['category'] ) {
			return true;
		}
		$all_layout_list = [
			'grid-layout4',
			'grid-layout5',
			'grid-layout5-2',
			'grid-layout6',
			'grid-layout6-2',
			'list-layout4',
			'list-layout5',
			'grid_hover-layout5',
			'grid_hover-layout6',
			'grid_hover-layout7',
			'grid_hover-layout8',
			'grid_hover-layout9',
			'grid_hover-layout10',
			'grid_hover-layout5-2',
			'grid_hover-layout6-2',
			'grid_hover-layout7-2',
			'grid_hover-layout9-2',
			'grid_hover-layout11',
			'slider-layout3',
			'slider-layout5',
			'slider-layout6',
			'slider-layout7',
			'slider-layout8',
			'slider-layout9',
			'slider-layout11',
			'slider-layout12',
		];

		if ( 'default' == $data['category_position'] && in_array( $data['layout'], $all_layout_list ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Post Link
	 *
	 * @param $data
	 * @param $pID
	 *
	 * @return array
	 */
	public static function get_post_link( $pID, $data ) {
		$link_class    = $link_start = $link_end = $readmore_link_start = $readmore_link_end = null;
		$external_link = get_post_meta( $pID, 'tpg_read_more', true );

		if ( 'default' == $data['post_link_type'] ) {
			$link_class = 'tpg-post-link';
			$link_start = $readmore_link_start = sprintf(
				'<a data-id="%s" href="%s" class="%s" target="%s">',
				absint( $pID ),
				esc_url( $external_link['url'] ?? get_permalink() ),
				esc_attr( $link_class ),
				esc_attr( $external_link['target'] ?? $data['link_target'] )
			);
			$link_end   = $readmore_link_end = '</a>';
		} elseif ( 'popup' == $data['post_link_type'] ) {
			$link_class = 'tpg-single-popup tpg-post-link';

			if ( did_action( 'elementor/loaded' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$link_class = 'tpg-post-link';
			}

			$link_start = $readmore_link_start = sprintf(
				'<a data-id="%s" href="%s" class="%s" target="%s">',
				absint( $pID ),
				esc_url( get_permalink() ),
				esc_attr( $link_class ),
				esc_attr( $data['link_target'] )
			);
			$link_end   = $readmore_link_end = '</a>';
		} elseif ( 'multi_popup' == $data['post_link_type'] ) {
			$link_class = 'tpg-multi-popup tpg-post-link';
			$link_start = $readmore_link_start = sprintf(
				'<a data-id="%s" href="%s" class="%s" target="%s">',
				absint( $pID ),
				esc_url( get_permalink() ),
				esc_attr( $link_class ),
				esc_attr( $data['link_target'] )
			);
			$link_end   = $readmore_link_end = '</a>';
		} else {
			$link_class          = 'tpg-post-link';
			$readmore_link_start = sprintf(
				'<a data-id="%s" href="%s" class="%s" target="%s">',
				absint( $pID ),
				esc_url( get_permalink() ),
				esc_attr( $link_class ),
				esc_attr( $data['link_target'] )
			);
			$readmore_link_end   = '</a>';
		}

		return [
			'link_start'          => $link_start,
			'link_end'            => $link_end,
			'readmore_link_start' => $readmore_link_start,
			'readmore_link_end'   => $readmore_link_end,
		];
	}

	/**
	 * Get Post Type
	 *
	 * @return string[]|\WP_Post_Type[]
	 */
	public static function get_post_types() {
		$post_types = get_post_types(
			[
				'public' => true,
			],
			'objects'
		);
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		$exclude = [ 'attachment', 'revision', 'nav_menu_item', 'elementor_library', 'tpg_builder', 'e-landing-page' ];

		foreach ( $exclude as $ex ) {
			unset( $post_types[ $ex ] );
		}

		if ( ! rtTPG()->hasPro() ) {
			$post_types = [
				'post' => $post_types['post'],
				'page' => $post_types['page'],
			];
		}

		return $post_types;
	}

	public static function rt_get_the_term_list( $post_id, $taxonomy, $before = '', $sep = '', $after = '' ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		if ( empty( $terms ) ) {
			return false;
		}

		$links = [];

		foreach ( $terms as $term ) {
			$meta_color      = get_term_meta( $term->term_id, 'rttpg_category_color', true );
			$meta_color_code = $meta_color ? '--tpg-primary-color:#' . ltrim( $meta_color, '#' ) : '';

			$link = get_term_link( $term, $taxonomy );
			if ( is_wp_error( $link ) ) {
				return $link;
			}
			if ( rtTPG()->hasPro() ) {
				$links[] = '<a class="' . $term->slug . '" style="' . esc_attr( $meta_color_code ) . '" href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>';
			} else {
				$links[] = '<a class="' . $term->slug . '" href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>';
			}
		}

		return $before . implode( $sep, $links ) . $after;
	}

	/**
	 * Get Post Meta HTML for Elementor
	 *
	 * @param $post_id
	 * @param $data
	 *
	 * @return string markup
	 */
	public static function get_post_meta_html( $post_id, $data, $echo = true ) {
		if ( $post_id ) {
			$post = get_post( $post_id );
		} else {
			global $post;
		}
		$author_id       = $post->post_author;
		$author_name     = get_the_author_meta( 'display_name', $post->post_author );
		$author          = apply_filters( 'rttpg_author_link', sprintf( '<a href="%s">%s</a>', get_author_posts_url( $author_id ), $author_name ) );
		$comments_number = get_comments_number( $post_id );
		$comment_label   = '';

		if ( isset( $data['show_comment_count_label'] ) && $data['show_comment_count_label'] ) {
			$comment_label = $data['comment_count_label_singular'];

			if ( $comments_number > 1 ) {
				$comment_label = $data['comment_count_label_plural'];
			}
		}

		$comments_text = sprintf( '%s (%s)', esc_html( $comment_label ), number_format_i18n( $comments_number ) );
		$date          = get_the_date( '', $post );

		// Category and Tags Management
		$_cat_id            = isset( $data['post_type'] ) ? $data['post_type'] . '_taxonomy' : 'category';
		$_tag_id            = isset( $data['post_type'] ) ? $data['post_type'] . '_tags' : 'post_tag';
		$_category_id       = isset( $data[ $_cat_id ] ) ? $data[ $_cat_id ] : 'category';
		$_tag_id            = isset( $data[ $_tag_id ] ) ? $data[ $_tag_id ] : 'post_tag';
		$categories         = self::rt_get_the_term_list( $post_id, $_category_id, null, '<span class="rt-separator">,</span>' );
		$tags               = self::rt_get_the_term_list( $post_id, $_tag_id, null, '<span class="rt-separator">,</span>' );
		$get_view_count     = self::get_post_view_count( $post_id );
		$meta_separator     = ( $data['meta_separator'] && 'default' !== $data['meta_separator'] ) ? sprintf( "<span class='separator'>%s</span>", $data['meta_separator'] ) : '';
		$category_condition = ( $categories && 'show' == $data['show_category'] );
		if ( ! isset( $data['is_guten_builder'] ) && rtTPG()->hasPro() ) {
			$category_condition = ( $categories && 'show' == $data['show_category'] && self::el_ignore_layout( $data ) && in_array(
					$data['category_position'],
					[
						'default',
						'with_meta',
					]
				) );
		}
		$post_meta_html = [];

		// Author Meta.
		ob_start();
		if ( 'show' === $data['show_author'] ) {
			$is_author_avatar = null;

			if ( 'icon' !== $data['show_author_image'] ) {
				$is_author_avatar = 'has-author-avatar';
			}
			?>
            <span class='author <?php echo esc_attr( $is_author_avatar ); ?>'>

				<?php
				if ( isset( $data['author_icon_visibility'] ) && $data['author_icon_visibility'] !== 'hide' ) {
					if ( 'icon' !== $data['show_author_image'] ) {
						echo get_avatar( $author_id, 80 );
					} else {
						if ( $data['show_meta_icon'] === 'yes' ) {
							if ( did_action( 'elementor/loaded' ) && isset( $data['user_icon']['value'] ) && $data['user_icon']['value'] ) {
								\Elementor\Icons_Manager::render_icon( $data['user_icon'], [ 'aria-hidden' => 'true' ] );
							} else {
								printf(
									"<i class='%s'></i>",
									esc_attr( self::change_icon( 'fa fa-user', 'user' ) )
								);
							}
						}
					}
				}

				if ( $data['author_prefix'] ) {
					echo "<span class='author-prefix'>" . esc_html( $data['author_prefix'] ) . '</span>';
				}
				echo wp_kses( $author, self::allowedHtml() );
				?>
			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}

		$post_meta_html['author'] = ob_get_clean();

		// Category Meta.

		ob_start();
		if ( $category_condition ) {
			?>
            <span class='categories-links'>
				<?php
				if ( $data['show_meta_icon'] === 'yes' ) {
					if ( did_action( 'elementor/loaded' ) && isset( $data['cat_icon']['value'] ) && $data['cat_icon']['value'] ) {
						\Elementor\Icons_Manager::render_icon( $data['cat_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						printf(
							"<i class='%s'></i>",
							esc_attr( self::change_icon( 'fas fa-folder-open', 'folder' ) )
						);
					}
				}
				echo wp_kses( $categories, self::allowedHtml() );
				?>

			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}
		$post_meta_html['category'] = ob_get_clean();

		ob_start();
		// Date Meta.
		if ( '' !== $data['show_date'] ) {
			$archive_year  = get_the_date( 'Y', $post );
			$archive_month = get_the_date( 'm', $post );
			$archive_day   = get_the_date( 'j', $post );
			?>
            <span class='date'>
				<?php
				if ( $data['show_meta_icon'] === 'yes' ) {
					if ( did_action( 'elementor/loaded' ) && isset( $data['date_icon']['value'] ) && $data['date_icon']['value'] ) {
						\Elementor\Icons_Manager::render_icon( $data['date_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						printf(
							"<i class='%s'></i>",
							esc_attr( self::change_icon( 'far fa-calendar-alt', 'calendar' ) )
						);
					}
				}
				?>
				<a href="<?php echo esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ); ?>">
					<?php echo esc_html( $date ); ?>
				</a>
			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}

		$post_meta_html['date'] = ob_get_clean();

		ob_start();
		// Tags Meta.
		if ( $tags && 'show' == $data['show_tags'] ) {
			?>
            <span class='post-tags-links'>
				<?php
				if ( $data['show_meta_icon'] === 'yes' ) {
					if ( did_action( 'elementor/loaded' ) && isset( $data['tag_icon']['value'] ) && $data['tag_icon']['value'] ) {
						\Elementor\Icons_Manager::render_icon( $data['tag_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						printf(
							"<i class='%s'></i>",
							esc_attr( self::change_icon( 'fa fa-tags', 'tag' ) )
						);

					}
				}
				echo wp_kses( $tags, self::allowedHtml() );
				?>
			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}
		$post_meta_html['tags'] = ob_get_clean();

		ob_start();
		// Comment Meta.
		if ( 'show' == $data['show_comment_count'] ) {
			?>
            <span class="comment-count">
				<?php
				if ( $data['show_meta_icon'] === 'yes' ) {
					if ( did_action( 'elementor/loaded' ) && isset( $data['comment_icon']['value'] ) && $data['comment_icon']['value'] ) {
						\Elementor\Icons_Manager::render_icon( $data['comment_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						printf(
							"<i class='%s'></i>",
							esc_attr( self::change_icon( 'fas fa-comments', 'chat' ) )
						);
					}
				}
				echo wp_kses( $comments_text, self::allowedHtml() );
				?>
			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}

		$post_meta_html['comment_count'] = ob_get_clean();

		ob_start();
		// Post Count.
		if ( rtTPG()->hasPro() && 'show' == $data['show_post_count'] && ! empty( $get_view_count ) ) {
			?>
            <span class="post-count">
				<?php
				if ( $data['show_meta_icon'] === 'yes' ) {
					if ( did_action( 'elementor/loaded' ) && isset( $data['post_count_icon']['value'] ) && $data['post_count_icon']['value'] ) {
						\Elementor\Icons_Manager::render_icon( $data['post_count_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						printf(
							"<i class='%s'></i>",
							esc_attr( self::change_icon( 'fa fa-eye', 'visible' ) )
						);

					}
				}
				echo wp_kses( $get_view_count, self::allowedHtml() );
				?>
			</span>
			<?php
			echo wp_kses( $meta_separator, self::allowedHtml() );
		}

		$post_meta_html['post_count'] = ob_get_clean();

		if ( isset( $data['is_gutenberg'] ) && $data['is_gutenberg'] ) {
			$meta_ordering = array_keys( $post_meta_html );

			if ( isset( $data['meta_ordering'] ) && is_array( $data['meta_ordering'] ) ) {
				$meta_ordering      = wp_list_pluck( $data['meta_ordering'], 'value' );
				$extra_meta         = [];
				$post_meta_html_key = array_keys( $post_meta_html );

				$count_order = count( $meta_ordering );

				if ( $count_order != count( $post_meta_html_key ) ) {
					foreach ( $post_meta_html_key as $key ) {
						if ( ! in_array( $key, $meta_ordering ) ) {
							$extra_meta[] = $key;
						}
					}
				}

				if ( ! empty( $extra_meta ) ) {
					$meta_ordering = array_merge( $meta_ordering, $extra_meta );
				}
			}
			$_meta_html = '';

			foreach ( $meta_ordering as $val ) {
				$_meta_html .= $post_meta_html[ $val ];
				// echo $post_meta_html[ $val ];
			}

			if ( $echo ) {
				echo wp_kses_post( $_meta_html );
			} else {
				return $_meta_html;
			}
		} else {
			$meta_ordering = isset( $data['meta_ordering'] ) && is_array( $data['meta_ordering'] ) ? $data['meta_ordering'] : [];
			foreach ( $meta_ordering as $val ) {
				if ( isset( $post_meta_html[ $val['meta_name'] ] ) ) {
					echo wp_kses_post( $post_meta_html[ $val['meta_name'] ] );
				}
			}
		}
	}

	/**
	 * Custom wp_kses
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function wp_kses( $string ) {
		$allowed_html = [
			'a'      => [
				'href'    => [],
				'title'   => [],
				'data-id' => [],
				'target'  => [],
				'class'   => [],
			],
			'strong' => [],
			'b'      => [],
			'br'     => [ [] ],
		];

		echo wp_kses( $string, $allowed_html );
	}


	/**
	 * Get Elementor Post Title for Elementor
	 *
	 * @param $title_tag
	 * @param $title
	 * @param $link_start
	 * @param $link_end
	 * @param $data
	 */
	public static function get_el_post_title( $title_tag, $title, $link_start, $link_end, $data ) {

		echo '<div class="entry-title-wrapper">';

		if ( rtTPG()->hasPro() && 'above_title' === $data['category_position'] || ! self::el_ignore_layout( $data ) ) {
			self::get_el_thumb_cat( $data, 'cat-above-title' );
		}

		printf( '<%s class="entry-title">', esc_attr( self::print_validated_html_tag( $title_tag ) ) );
		self::print_html( $link_start );
		self::print_html( $title );
		self::print_html( $link_end );
		printf( '</%s>', esc_attr( self::print_validated_html_tag( $title_tag ) ) );
		echo '</div>';
	}

	static function get_el_thumb_cat( $data, $class = 'cat-over-image' ) {
		if ( ! ( 'show' == $data['show_meta'] && 'show' == $data['show_category'] ) ) {
			return;
		}

		$pID               = get_the_ID();
		$_cat_id           = $data['post_type'] . '_taxonomy';
		$_post_taxonomy    = isset( $data[ $_cat_id ] ) ? $data[ $_cat_id ] : 'category';
		$categories        = self::rt_get_the_term_list( $pID, $_post_taxonomy, null, '<span class="rt-separator">,</span>' );
		$category_position = $data['category_position'];

		if ( in_array(
			     $data['layout'],
			     [
				     'grid-layout4',
				     'slider-layout3',
				     'grid_hover-layout11',
			     ]
		     ) && 'default' === $data['category_position'] ) {
			$category_position = 'top_left';
		}
		?>
        <div class="tpg-separate-category <?php echo esc_attr( $data['category_style'] . ' ' . $category_position . ' ' . $class ); ?>">
			<span class='categories-links'>
			<?php
			if ( 'yes' === $data['show_cat_icon'] ) {
				echo "<i class='" . esc_attr( self::change_icon( 'fas fa-folder-open', 'folder' ) ) . "'></i>";
			}
			?>


			<?php echo wp_kses( $categories, self::allowedHtml() ); ?>
			</span>
        </div>
		<?php
	}


	/**
	 * Get first image from the content
	 *
	 * @param          $post_id
	 * @param string $type
	 *
	 * @return mixed|string
	 */
	public static function get_content_first_image( $post_id, $type = 'markup', $imgClass = '' ) {
		if ( $img = preg_match_all(
			'/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',
			get_the_content( $post_id ),
			$matches
		)
		) {
			$imgSrc = $matches[1][0];
			$size   = '';

			$imgAbs = str_replace( trailingslashit( site_url() ), ABSPATH, $imgSrc );

			if ( file_exists( $imgAbs ) ) {
				$info = getimagesize( $imgAbs );
				$size = isset( $info[3] ) ? $info[3] : '';
			}

			$attachment_id = attachment_url_to_postid( $imgSrc );
			$alt_text      = null;

			if ( ! empty( $attachment_id ) ) {
				$alt_text = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
			}

			$alt = $alt_text ? $alt_text : get_the_title( $post_id );

			if ( $type == 'markup' ) {
				if ( $imgClass !== 'swiper-lazy' ) {
					return "<img class='rt-img-responsive' src='{$imgSrc}' {$size} alt='{$alt}'>";
				} else {
					return "<img class='{$imgClass}' data-src='{$imgSrc}' alt='{$alt}'>";
				}
			} else {
				return $imgSrc;
			}
		}
	}

	/**
	 * Get post thumbnail html
	 *
	 * @param         $pID
	 * @param         $data
	 * @param         $link_start
	 * @param         $link_end
	 * @param false $offset_size
	 */
	public static function get_post_thumbnail( $pID, $data, $link_start, $link_end, $offset_size = false ) {
		$thumb_cat_condition = ( ! ( 'above_title' === $data['category_position'] || 'default' === $data['category_position'] ) );

		if ( 'grid-layout4' === $data['layout'] && 'default' === $data['category_position'] ) {
			$thumb_cat_condition = true;
		} elseif ( in_array(
			           $data['layout'],
			           [
				           'grid-layout4',
				           'grid_hover-layout11',
				           'slider-layout3',
			           ]
		           ) && 'default' === $data['category_position'] ) {
			$thumb_cat_condition = true;
		}

		if ( rtTPG()->hasPro() && $data['show_category'] == 'show' && $thumb_cat_condition && 'with_meta' !== $data['category_position'] ) {
			self::get_el_thumb_cat( $data );
		}

		$img_link     = get_the_post_thumbnail_url( $pID, 'full' );
		$img_size_key = 'image_size';

		if ( $offset_size ) {
			$img_size_key = 'image_offset_size';
		}

		$lazy_load  = ( $data['prefix'] == 'slider' && $data['lazy_load'] == 'yes' ) ? true : false;
		$lazy_class = 'rt-img-responsive';

		if ( $lazy_load ) {
			$lazy_class = 'swiper-lazy';
		}

		if ( 'yes' === $data['is_thumb_linked'] ) {
			self::print_html( $link_start );
		}

		if ( has_post_thumbnail() && 'feature_image' === $data['media_source'] ) {
			$fImgSize = $data['image_size'];

			if ( $offset_size ) {
				echo get_the_post_thumbnail( $pID, $data['image_offset'] );
			} else {
				if ( $data['image_size'] !== 'custom' ) {
					$attachment_id = get_post_thumbnail_id( $pID );
					$thumb_info    = wp_get_attachment_image_src( $attachment_id, $fImgSize );
					$thumb_alt     = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
					if ( $lazy_load ) {
						?>
                        <img data-src="<?php echo esc_url( isset( $thumb_info[0] ) ? $thumb_info[0] : '' ); ?>"
                             src="#none"
                             class="<?php echo esc_attr( $lazy_class ); ?>"
                             width="<?php echo esc_attr( isset( $thumb_info[1] ) ? $thumb_info[1] : '' ); ?>"
                             height="<?php echo esc_attr( isset( $thumb_info[2] ) ? $thumb_info[2] : '' ); ?>"
                             alt="<?php echo esc_attr( $thumb_alt ? $thumb_alt : the_title() ); ?>">
						<?php
					} else {
						?>
                        <img src="<?php echo esc_url( isset( $thumb_info[0] ) ? $thumb_info[0] : '' ); ?>"
                             class="<?php echo esc_attr( $lazy_class ); ?>"
                             width="<?php echo esc_attr( isset( $thumb_info[1] ) ? $thumb_info[1] : '' ); ?>"
                             height="<?php echo esc_attr( isset( $thumb_info[2] ) ? $thumb_info[2] : '' ); ?>"
                             alt="<?php echo esc_attr( $thumb_alt ? $thumb_alt : the_title() ); ?>">
						<?php
					}
					?>

					<?php
				} else {
					$fImgSize      = 'rt_custom';
					$mediaSource   = 'feature_image';
					$defaultImgId  = null;
					$customImgSize = [];

					if ( $data['is_gutenberg'] && isset( $data['c_image_width'] ) && isset( $data['c_image_height'] ) ) {
						$data['image_custom_dimension']['width']  = intval( $data['c_image_width'] );
						$data['image_custom_dimension']['height'] = intval( $data['c_image_height'] );
					}

					if ( isset( $data['image_custom_dimension'] ) ) {
						$post_thumb_id           = get_post_thumbnail_id( $pID );
						$default_image_dimension = wp_get_attachment_image_src( $post_thumb_id, 'full' );

						if ( $default_image_dimension[1] <= $data['image_custom_dimension']['width'] || $default_image_dimension[2] <= $data['image_custom_dimension']['height'] ) {
							$customImgSize = [];
						} else {
							$customImgSize[0] = $data['image_custom_dimension']['width'];
							$customImgSize[1] = $data['image_custom_dimension']['height'];
							$customImgSize[2] = $data['img_crop_style'];
						}
					}

					echo wp_kses_post( self::getFeatureImageSrc( $pID, $fImgSize, $mediaSource, $defaultImgId, $customImgSize, $lazy_class ) );
				}
			}
		} elseif ( 'first_image' === $data['media_source'] && self::get_content_first_image( $pID ) ) {
			echo wp_kses_post( self::get_content_first_image( $pID, 'markup', $lazy_class ) );
			$img_link = self::get_content_first_image( $pID, 'url' );
		} elseif ( 'yes' === $data['is_default_img'] || 'grid_hover' == $data['prefix'] ) {
			// echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $data, $img_size_key, 'default_image' );
			if ( isset( $data['default_image']['id'] ) ) {
				echo wp_get_attachment_image( $data['default_image']['id'], $data[ $img_size_key ], '', [ 'class' => 'rt-img-responsive' ] );
			}
			if ( ! empty( $data['default_image'] ) && isset( $data['default_image']['url'] ) ) {
				$img_link = $data['default_image']['url'];
			}
		}

		?>
		<?php if ( $lazy_load ) : ?>
            <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
		<?php endif; ?>

		<?php echo 'yes' === $data['is_thumb_linked'] ? wp_kses( $link_end, self::allowedHtml() ) : null; ?>

		<?php
		if ( 'show' === $data['is_thumb_lightbox'] || ( in_array(
			                                                $data['layout'],
			                                                [
				                                                'grid-layout7',
				                                                'slider-layout4',
			                                                ]
		                                                ) && in_array(
			                                                $data['is_thumb_lightbox'],
			                                                [
				                                                'default',
				                                                'show',
			                                                ]
		                                                ) ) ) :
			?>

            <a class="tpg-zoom mfp-fade"
               data-elementor-open-lightbox="yes"
               data-elementor-lightbox-slideshow="<?php echo esc_attr( $data['layout'] ); ?>"
               title="<?php echo esc_attr( get_the_title() ); ?>"
               href="<?php echo esc_url( $img_link ); ?>">

				<?php
				if ( did_action( 'elementor/loaded' ) && isset( $data['light_box_icon']['value'] ) && $data['light_box_icon']['value'] ) {
					\Elementor\Icons_Manager::render_icon( $data['light_box_icon'], [ 'aria-hidden' => 'true' ] );
				} else {
					echo "<i class='fa fa-plus'></i>";
				}
				?>
            </a>

		<?php endif; ?>
        <div class="overlay grid-hover-content"></div>
		<?php
	}


	/**
	 * Get ACF data for elementor
	 *
	 * @param $data
	 * @param $pID
	 *
	 * @return bool
	 */
	public static function tpg_get_acf_data_elementor( $data, $pID, $return_type = true ) {
		if ( ! ( rtTPG()->hasPro() && self::is_acf() ) ) {
			return;
		}

		if ( isset( $data['show_acf'] ) && 'show' == $data['show_acf'] ) {
			$cf_group = $data['cf_group'];

			$format = [
				'hide_empty'       => ( isset( $data['cf_hide_empty_value'] ) && $data['cf_hide_empty_value'] ) ? 'yes' : '',
				'show_value'       => ( isset( $data['cf_show_only_value'] ) && $data['cf_show_only_value'] ) ? '' : 'yes',
				'hide_group_title' => ( isset( $data['cf_hide_group_title'] ) && $data['cf_hide_group_title'] ) ? '' : 'yes',
			];

			if ( ! empty( $cf_group ) ) {

				$acf_html = "<div class='acf-custom-field-wrap'>";

				$acf_html .= \RT\ThePostGridPro\Helpers\Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
				$acf_html .= '</div>';

				if ( $return_type ) {
					self::print_html( $acf_html, true );
				} else {
					return $acf_html;
				}
			}
		}
	}

	/**
	 * Get Read More Button
	 *
	 * @param $data
	 * @param $readmore_link_start
	 * @param $readmore_link_end
	 *
	 * @return void
	 */
	public static function get_read_more_button( $data, $readmore_link_start, $readmore_link_end, $type = 'elementor' ) {
		?>
        <div class="post-footer">
            <div class="post-footer">
                <div class="read-more">
					<?php
					self::wp_kses( $readmore_link_start );
					if ( 'yes' == $data['show_btn_icon'] && 'left' == $data['readmore_icon_position'] ) {
						if ( $type === 'elementor' ) {
							if ( did_action( 'elementor/loaded' ) ) {
								\Elementor\Icons_Manager::render_icon(
									$data['readmore_btn_icon'],
									[
										'aria-hidden' => 'true',
										'class'       => 'left-icon',
									]
								);
							}
						} else {
							printf(
								"<i class='left-icon %s'></i>",
								esc_attr( self::change_icon( 'fas fa-angle-right', 'left-arrow', 'left-icon' ) )
							);
						}
					}
					echo esc_html( $data['read_more_label'] );
					if ( 'yes' == $data['show_btn_icon'] && 'right' == $data['readmore_icon_position'] ) {
						if ( $type === 'elementor' ) {
							if ( did_action( 'elementor/loaded' ) ) {
								\Elementor\Icons_Manager::render_icon(
									$data['readmore_btn_icon'],
									[
										'aria-hidden' => 'true',
										'class'       => 'right-icon',
									]
								);
							}
						} else {
							printf(
								"<i class='left-icon %s'></i>",
								esc_attr( self::change_icon( 'fas fa-angle-right', 'right-arrow', 'right-icon' ) )
							);
						}
					}
					self::wp_kses( $readmore_link_end );
					?>
                </div>
            </div>
        </div>
		<?php
	}


	/**
	 * Check is filter enable or not
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public static function is_filter_enable( $data ) {
		if ( rtTPG()->hasPro()
		     && ( $data['show_taxonomy_filter'] == 'show'
		          || $data['show_author_filter'] == 'show'
		          || $data['show_order_by'] == 'show'
		          || $data['show_sort_order'] == 'show'
		          || $data['show_search'] == 'show'
		          || ( $data['show_pagination'] == 'show' && $data['pagination_type'] != 'pagination' ) )
		) {
			return true;
		}

		return false;
	}


	// Get Custom post category:
	public static function tpg_get_categories_by_id( $cat = 'category' ) {
		$terms = get_terms(
			[
				'taxonomy'   => $cat,
				'hide_empty' => true,
			]
		);

		$options = [];
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}



	/**
	 * Gutenberg Functionality
	 * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	 */

	/**
	 * Get Post Types.
	 *
	 * @since 1.0.9
	 */
	public static function get_post_types_guten() {
		$post_types = get_post_types(
			[
				'public'       => true,
				'show_in_rest' => true,
			],
			'objects'
		);

		$options = [];

		foreach ( $post_types as $post_type ) {
			if ( 'product' === $post_type->name ) {
				continue;
			}

			if ( 'attachment' === $post_type->name ) {
				continue;
			}

			if ( 'page' === $post_type->name ) {
				continue;
			}

			$options[] = [
				'value' => $post_type->name,
				'label' => $post_type->label,
			];
		}

		return $options;
	}

	/**
	 * Get all taxonomies.
	 *
	 * @since 1.0.9
	 */
	public static function get_all_taxonomy_guten() {
		$post_types     = self::get_post_types();
		$taxonomies     = get_taxonomies( [], 'objects' );
		$all_taxonomies = [];
		foreach ( $taxonomies as $taxonomy => $object ) {
			if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) )
			     || in_array( $taxonomy, self::get_excluded_taxonomy() )
			) {
				continue;
			}
			$all_taxonomies[ $taxonomy ] = self::tpg_get_categories_by_id( $taxonomy );
		}

		return $all_taxonomies;
	}

	/**
	 * Get all image sizes.
	 *
	 * @since 1.0.9
	 */
	public static function get_all_image_sizes_guten() {
		global $_wp_additional_image_sizes;

		$sizes       = get_intermediate_image_sizes();
		$image_sizes = [];

		$image_sizes[] = [
			'value' => 'full',
			'label' => esc_html__( 'Full', 'the-post-grid' ),
		];

		foreach ( $sizes as $size ) {
			if ( in_array( $size, [ 'thumbnail', 'medium', 'medium_large', 'large' ], true ) ) {
				$image_sizes[] = [
					'value' => $size,
					'label' => ucwords( trim( str_replace( [ '-', '_' ], [ ' ', ' ' ], $size ) ) ),
				];
			} else {
				$image_sizes[] = [
					'value' => $size,
					'label' => sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( [ '-', '_' ], [ ' ', ' ' ], $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					),
				];
			}
		}

		if ( rtTPG()->hasPro() ) {
			$image_sizes[] = [
				'value' => 'custom',
				'label' => esc_html__( 'Custom', 'the-post-grid' ),
			];
		}

		return apply_filters( 'tpg_image_size_guten', $image_sizes );
	}


	/**
	 * Prints HTML.
	 *
	 * @param string $html HTML.
	 * @param bool $allHtml All HTML.
	 *
	 * @return mixed
	 */
	public static function print_html( $html, $allHtml = false ) {
		if ( ! $html ) {
			return '';
		}
		if ( $allHtml ) {
			echo stripslashes_deep( $html ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo wp_kses_post( stripslashes_deep( $html ) );
		}
	}

	/**
	 * Allowed HTML for wp_kses.
	 *
	 * @param string $level Tag level.
	 *
	 * @return mixed
	 */
	public static function allowedHtml( $level = 'basic' ) {
		$allowed_html = [];

		switch ( $level ) {
			case 'basic':
				$allowed_html = [
					'b'      => [
						'class' => [],
						'id'    => [],
					],
					'i'      => [
						'class' => [],
						'id'    => [],
					],
					'u'      => [
						'class' => [],
						'id'    => [],
					],
					'br'     => [
						'class' => [],
						'id'    => [],
					],
					'em'     => [
						'class' => [],
						'id'    => [],
					],
					'span'   => [
						'class' => [],
						'id'    => [],
					],
					'strong' => [
						'class' => [],
						'id'    => [],
					],
					'hr'     => [
						'class' => [],
						'id'    => [],
					],
					'a'      => [
						'href'   => [],
						'title'  => [],
						'class'  => [],
						'id'     => [],
						'target' => [],
						'style'  => [],
					],
					'div'    => [
						'class' => [],
						'id'    => [],
					],
				];
				break;

			case 'advanced':
				$allowed_html = [
					'b'      => [
						'class' => [],
						'id'    => [],
					],
					'i'      => [
						'class' => [],
						'id'    => [],
					],
					'u'      => [
						'class' => [],
						'id'    => [],
					],
					'br'     => [
						'class' => [],
						'id'    => [],
					],
					'em'     => [
						'class' => [],
						'id'    => [],
					],
					'span'   => [
						'class' => [],
						'id'    => [],
					],
					'strong' => [
						'class' => [],
						'id'    => [],
					],
					'hr'     => [
						'class' => [],
						'id'    => [],
					],
					'a'      => [
						'href'    => [],
						'title'   => [],
						'class'   => [],
						'id'      => [],
						'data-id' => [],
						'target'  => [],
					],
					'input'  => [
						'type'  => [],
						'name'  => [],
						'class' => [],
						'value' => [],
					],
				];
				break;

			case 'image':
				$allowed_html = [
					'img' => [
						'src'      => [],
						'data-src' => [],
						'alt'      => [],
						'height'   => [],
						'width'    => [],
						'class'    => [],
						'id'       => [],
						'style'    => [],
						'srcset'   => [],
						'loading'  => [],
						'sizes'    => [],
					],
					'div' => [
						'class' => [],
					],
				];
				break;

			case 'anchor':
				$allowed_html = [
					'a' => [
						'href'  => [],
						'title' => [],
						'class' => [],
						'id'    => [],
						'style' => [],
					],
				];
				break;

			default:
				// code...
				break;
		}

		return $allowed_html;
	}

	/**
	 * Definition for wp_kses.
	 *
	 * @param string $string String to check.
	 * @param string $level Tag level.
	 *
	 * @return mixed
	 */
	public static function htmlKses( $string, $level ) {
		if ( empty( $string ) ) {
			return;
		}

		return wp_kses( $string, self::allowedHtml( $level ) );
	}


	/**
	 * Insert Array Item in specific position
	 *
	 * @param $array
	 * @param $position
	 * @param $insert_array
	 *
	 * @return void
	 */
	public static function array_insert( &$array, $position, $insert_array ) {
		$first_array = array_splice( $array, 0, $position + 1 );
		$array       = array_merge( $first_array, $insert_array, $array );
	}

	/**
	 * tpg_option
	 *
	 * @param $option_name
	 * @param $default_value
	 *
	 * @return string
	 */
	public static function tpg_option( $option_name, $default_value = '' ) {
		$settings = get_option( rtTPG()->options['settings'] );
		if ( ! empty( $settings[ $option_name ] ) ) {
			return $settings[ $option_name ];
		} elseif ( $default_value ) {
			return $default_value;
		}

		return '';
	}

	/**
	 * Get Instant Query Argument
	 *
	 * @param $instant_query
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function get_instant_query( $instant_query, $args ) {
		if ( 'default' === $instant_query ) {
			return $args;
		}
		//phpcs:disable WordPress.Security.NonceVerification.Missing

		switch ( $instant_query ) {
			case 'random_post_7_days':
			case 'random_post_30_days':
				$args['orderby']    = 'rand';
				$args['order']      = 'ASC';
				$args['date_query'] = [ [ 'after' => $instant_query === 'random_post_7_days' ? '1 week ago' : '1 month ago' ] ];
				break;
			case 'most_comment_1_day':
			case 'most_comment_7_days':
			case 'most_comment_30_days':
				$args['orderby']    = 'comment_count';
				$args['order']      = 'DESC';
				$args['date_query'] = [ [ 'after' => $instant_query === 'most_comment_1_day' ? '1 day ago' : ( $instant_query === 'most_comment_7_days' ? '1 week ago' : '1 month ago' ) ] ];
				break;
			case 'popular_post_1_day_view':
			case 'popular_post_7_days_view':
			case 'popular_post_30_days_view':
			case 'popular_post_all_times_view':
				$args['meta_key'] = self::get_post_view_count_meta_key(); //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				if ( $instant_query !== 'popular_post_all_times_view' ) {
					$args['date_query'] = [ [ 'after' => ( $instant_query === 'popular_post_1_day_view' ? '1 day ago' : ( $instant_query === 'popular_post_7_days_view' ? '1 week ago' : '1 month ago' ) ) ] ];
				}
				break;
			case 'related_category':
				global $post;
				$p_id = isset( $post->ID ) && $post->ID ? $post->ID : ( isset( $prams['current_post'] ) && $prams['current_post'] ? $prams['current_post'] : ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : '' ) );
				if ( $p_id ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query']    = [
						[
							'taxonomy' => 'category',
							'terms'    => self::get_terms_id( $p_id, 'category' ),
							'field'    => 'term_id',
						],
					];
					$args['post__not_in'] = [ $p_id ]; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				}
				break;
			case 'related_tag':
				global $post;
				$p_id = isset( $post->ID ) && $post->ID ? $post->ID : ( isset( $prams['current_post'] ) && $prams['current_post'] ? $prams['current_post'] : ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : '' ) );
				if ( $p_id ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query']    = [
						[
							'taxonomy' => 'post_tag',
							'terms'    => self::get_terms_id( $p_id, 'post_tag' ),
							'field'    => 'term_id',
						],
					];
					$args['post__not_in'] = [ $p_id ]; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				}
				break;
			case 'related_cat_tag':
				global $post;
				$p_id = isset( $post->ID ) && $post->ID ? $post->ID : ( isset( $prams['current_post'] ) && $prams['current_post'] ? $prams['current_post'] : ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : '' ) );
				if ( $p_id ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query']    = [
						[
							'taxonomy' => 'post_tag',
							'terms'    => self::get_terms_id( $p_id, 'post_tag' ),
							'field'    => 'term_id',
						],
						[
							'taxonomy' => 'category',
							'terms'    => self::get_terms_id( $p_id, 'category' ),
							'field'    => 'term_id',
						],
					];
					$args['post__not_in'] = [ $p_id ]; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				}
				break;
			default:
				break;
		}

		return $args;
	}

	/**
	 * change_icon
	 *
	 * @param $fontawesome
	 * @param $flaticon
	 * @param $default_class
	 *
	 * @return mixed|string
	 */
	public static function change_icon( $fontawesome, $flaticon, $default_class = '' ) {
		if ( self::tpg_option( 'tpg_icon_font' ) === 'fontawesome' ) {
			$fontawesome = ( $fontawesome === 'fab fa-twitter' ? 'fab fa-x-twitter' : $fontawesome );

			return $fontawesome . ' ' . $default_class;
		} else {
			$flaticon = ( $flaticon == 'twitter' ? 'twitter-x' : $flaticon );

			return 'flaticon-' . $flaticon . ' ' . $default_class;
		}
	}

	public static function get_terms_id( $id, $type ) {
		$data = [];
		$arr  = get_the_terms( $id, $type );
		if ( is_array( $arr ) ) {
			foreach ( $arr as $key => $val ) {
				$data[] = $val->term_id;
			}
		}

		return $data;
	}

	/**
	 *
	 * Get last post id
	 *
	 * @param string $post_type
	 * @param false $all_content
	 *
	 * @return int
	 */
	public static function get_last_post_id( $post_type = 'post' ): int {
		if ( is_singular( $post_type ) ) {
			return get_the_ID();
		}

		global $wpdb;
		$cache_key = 'tpg_last_post_id';
		$_post_id  = get_transient( $cache_key );

		if ( false === $_post_id || 'publish' !== get_post_status( $_post_id ) ) {
			delete_transient( $cache_key );
			//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$_post_id = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(ID) FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_status = %s", $post_type, 'publish' ) );
			set_transient( $cache_key, $_post_id, 12 * HOUR_IN_SECONDS );
		}

		return absint( $_post_id );
	}

	/**
	 * Gutenberg
	 *
	 * @return mixed|null
	 */
	public static function get_builder_type_list() {
		return apply_filters(
			'tpg_builder_type_list',
			[
				'single'           => __( 'Single', 'the-post-grid-pro' ),
				'archive'          => __( 'Post Archive', 'the-post-grid-pro' ),
				'author-archive'   => __( 'Author Archive', 'the-post-grid-pro' ),
				'search-archive'   => __( 'Search Archive', 'the-post-grid-pro' ),
				'date-archive'     => __( 'Date Archive', 'the-post-grid-pro' ),
				'category-archive' => __( 'Category Archive', 'the-post-grid-pro' ),
				'tags-archive'     => __( 'Tags Archive', 'the-post-grid-pro' ),
			]
		);
	}

	/**
	 * Endpoint list
	 *
	 * @return mixed|null
	 */
	public static function get_endpoint() {
		return apply_filters(
			'tpg_myaccount_endpoint',
			[
				'my-post',
				'edit-account',
				'edit-post',
				'submit-post',
				'view-post',
			]
		);
	}

	public static function get_account_menu_items() {
		$endpoints = self::get_endpoint();

		$menu_items         = [];
		$default_menu_items = apply_filters(
			'tpg_account_default_menu_items',
			[
				'dashboard' => esc_html__( 'Dashboard', 'the-post-grid' ),
				'my-post'   => esc_html__( 'My Post', 'the-post-grid' ),
				// 'edit-account' => esc_html__( 'Account Details', 'the-post-grid' ),
				// 'logout'    => esc_html__( 'Logout', 'the-post-grid' ),
			],
			$endpoints
		);

		// Remove unused endpoints.
		foreach ( $default_menu_items as $item_id => $item ) {
			$menu_items[ $item_id ] = $item;
		}

		return apply_filters( 'tpg_account_menu_items', $menu_items, $default_menu_items, $endpoints );
	}

	/**
	 * @param string $page
	 *
	 * @return int|void
	 */
	public static function get_page_id( $page ) {

		$page_id          = 0;
		$settings_page_id = self::tpg_option( 'tpg_myaccount' );
		if ( $settings_page_id && get_post( $settings_page_id ) ) {
			$page_id = $settings_page_id;
		}

		$page_id = apply_filters( 'tpg_get_page_id', $page_id, $page );

		return $page_id ? absint( $page_id ) : - 1;
	}

	public static function get_page_permalink( $page ) {
		$page_id   = self::get_page_id( $page );
		$permalink = 0 < $page_id ? get_permalink( $page_id ) : '';

		return apply_filters( 'tpg_get_' . $page . '_page_permalink', $permalink );
	}

	public static function logout_url( $redirect = '' ) {
		$redirect = $redirect ? $redirect : self::get_page_permalink( 'myaccount' );

		return wp_logout_url( $redirect );
	}

	/**
	 * @param $endpoint
	 *
	 * @return mixed
	 */
	public static function get_account_endpoint_url( $endpoint = false ) {
		if ( ! $endpoint || 'dashboard' === $endpoint ) {
			$url = self::get_page_permalink( 'myaccount' );
		} elseif ( 'logout' === $endpoint ) {
			$url = self::logout_url();
		} else {
			$url = self::get_endpoint_url( $endpoint, '', self::get_page_permalink( 'myaccount' ) );
		}

		// Hooks BuddyPress Support .
		return apply_filters( 'tpg_get_account_endpoint_url', $url, $endpoint );
	}

	public static function get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		// Map endpoint to options.
		$query_vars = self::get_endpoint();
		$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;
		if ( get_option( 'permalink_structure' ) ) {
			if ( str_contains( $permalink, '?' ) ) {
				$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
				$permalink    = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}
			$url = trailingslashit( $permalink );
			if ( $endpoint ) {
				$url .= trailingslashit( $endpoint );
			}

			if ( $value ) {
				$url .= trailingslashit( $value );
			}

			$url .= $query_string;
		} else {
			$url = add_query_arg( $endpoint, $value, $permalink );
		}

		return apply_filters( 'tpg_get_endpoint_url', $url, $endpoint, $value, $permalink );
	}

	/**
	 * Dashboard Icons
	 *
	 * @param $name
	 *
	 * @return void
	 */
	public static function dashboard_icon( $name ) {
		$icons = [
			'dashboard'    => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_2003_58)">
<path d="M14.3 14.3C13.0849 14.3 12.1 15.2849 12.1 16.5V19.8C12.1 21.015 13.0849 22 14.3 22H14.3264C15.7048 22 16.8876 21.9276 17.8759 21.7079C18.8767 21.4855 19.7443 21.0978 20.421 20.421C21.0978 19.7443 21.4855 18.8768 21.7079 17.8759C21.8088 17.422 21.8753 16.9613 21.919 16.4977C22.0331 15.288 21.015 14.3 19.8 14.3H14.3Z" fill="#A8C7EC"/>
<path d="M0 14.3264V12.1C0 10.8849 0.984973 9.89999 2.2 9.89999H7.7C8.91506 9.89999 9.9 10.8849 9.9 12.1V19.8C9.9 21.0151 8.91506 22 7.7 22H7.6736C6.29516 22 5.11238 21.9276 4.12406 21.7079C3.12326 21.4855 2.25571 21.0978 1.57897 20.4211C0.902231 19.7443 0.514492 18.8768 0.292072 17.8759C0.0724241 16.8876 0 15.7048 0 14.3264Z" fill="#729ACB"/>
<path d="M22 9.9C22 11.1151 21.015 12.1 19.8 12.1H14.3C13.0849 12.1 12.1 11.1151 12.1 9.9V2.2C12.1 0.984973 13.0849 0 14.3 0H14.3264C15.7048 0 16.8876 0.0724241 17.8759 0.292072C18.8767 0.514492 19.7443 0.902231 20.421 1.57897C21.0978 2.25572 21.4855 3.12326 21.7079 4.12406C21.9276 5.11238 22 6.29516 22 7.6736V9.9Z" fill="#729ACB"/>
<path d="M9.89996 2.2C9.89996 0.984973 8.91502 0 7.69996 0H7.67356C6.29512 0 5.11234 0.0724241 4.12403 0.292072C3.12323 0.514492 2.25568 0.902231 1.57894 1.57897C0.902194 2.25572 0.514455 3.12326 0.292035 4.12406C0.191154 4.57798 0.124582 5.03869 0.0808789 5.50231C-0.0331361 6.71198 0.984936 7.7 2.19996 7.7H7.69996C8.91502 7.7 9.89996 6.71503 9.89996 5.5V2.2Z" fill="#A8C7EC"/>
</g>
<defs>
<clipPath id="clip0_2003_58">
<rect width="22" height="22" fill="white"/>
</clipPath>
</defs>
</svg>',
			'my-post'      => '<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M-0.00012207 6.6C-0.00012207 5.38498 0.984851 4.4 2.19988 4.4H14.2999C15.5149 4.4 16.4999 5.38497 16.4999 6.6V19.8C16.4999 21.015 15.5149 22 14.2999 22H2.19988C0.984851 22 -0.00012207 21.015 -0.00012207 19.8V6.6Z" fill="#729ACB"/>
<path d="M3.67317 2.2C3.67317 0.984971 4.65814 0 5.87317 0H16.8732C18.0882 0 19.0732 0.984973 19.0732 2.2V16.5C19.0732 17.715 18.0882 18.7 16.8732 18.7H5.87317C4.65814 18.7 3.67317 17.715 3.67317 16.5V2.2Z" fill="#A8C7EC"/>
</svg>',
			'edit-account' => '<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13.2564 12.4103H6.48718C2.91077 12.4103 0 15.321 0 18.8974C0 20.6089 1.39108 22 3.10256 22H16.641C18.3525 22 19.7436 20.6089 19.7436 18.8974C19.7436 15.321 16.8328 12.4103 13.2564 12.4103Z" fill="#729ACB"/>
<path d="M9.87179 0C6.91703 0 4.51282 2.40421 4.51282 5.35897C4.51282 8.31374 6.91703 10.7179 9.87179 10.7179C12.8266 10.7179 15.2308 8.31374 15.2308 5.35897C15.2308 2.40421 12.8266 0 9.87179 0Z" fill="#A8C7EC"/>
</svg>',
			'logout'       => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M3.4 2.8C3.24087 2.8 3.08826 2.86321 2.97574 2.97574C2.86321 3.08826 2.8 3.24087 2.8 3.4V17.4C2.8 17.5591 2.86321 17.7117 2.97574 17.8243C3.08826 17.9368 3.24087 18 3.4 18H7.4C8.1732 18 8.8 18.6268 8.8 19.4C8.8 20.1732 8.1732 20.8 7.4 20.8H3.4C2.49826 20.8 1.63346 20.4418 0.995837 19.8042C0.358213 19.1665 0 18.3017 0 17.4V3.4C0 2.49826 0.358213 1.63346 0.995837 0.995837C1.63346 0.358213 2.49826 0 3.4 0H7.4C8.1732 0 8.8 0.626801 8.8 1.4C8.8 2.1732 8.1732 2.8 7.4 2.8H3.4ZM13.4101 4.41005C13.9568 3.86332 14.8432 3.86332 15.3899 4.41005L20.3899 9.41005C20.9367 9.95678 20.9367 10.8432 20.3899 11.3899L15.3899 16.3899C14.8432 16.9367 13.9568 16.9367 13.4101 16.3899C12.8633 15.8432 12.8633 14.9568 13.4101 14.4101L16.0201 11.8H7.4C6.6268 11.8 6 11.1732 6 10.4C6 9.6268 6.6268 9 7.4 9H16.0201L13.4101 6.38995C12.8633 5.84322 12.8633 4.95678 13.4101 4.41005Z" fill="#729ACB"/>
</svg>',
			'total-post'   => '<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M42.9998 12H9.99976C6.68605 12 3.99976 14.6863 3.99976 18V54C3.99976 57.3137 6.68605 60 9.99976 60H42.9998C46.3135 60 48.9998 57.3137 48.9998 54V18C48.9998 14.6863 46.3135 12 42.9998 12Z" fill="#004EB2"/>
<path d="M50.0178 0H20.0178C16.7041 0 14.0178 2.68629 14.0178 6V45C14.0178 48.3137 16.7041 51 20.0178 51H50.0178C53.3315 51 56.0178 48.3137 56.0178 45V6C56.0178 2.68629 53.3315 0 50.0178 0Z" fill="#006FFF"/>
<path d="M25 16H47" stroke="white" stroke-width="3" stroke-linecap="round"/>
<path d="M25 24H43" stroke="white" stroke-width="3" stroke-linecap="round"/>
<path d="M25 32H36" stroke="white" stroke-width="3" stroke-linecap="round"/>
</svg>',
			'publish-post' => '<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M15.0231 16.7444C15.9483 16.7444 16.8356 16.3769 17.4898 15.7227C18.144 15.0685 18.5115 14.1813 18.5115 13.2561V1.56313C18.0904 1.88181 17.7066 2.2469 17.3673 2.65149L5.86969 15.8793C5.64934 16.1562 5.44438 16.445 5.25574 16.7444H15.0231Z" fill="#004EB2"/>
<path d="M53.3672 2.23289C52.6604 1.51698 51.8169 0.95044 50.8869 0.566919C49.9568 0.183399 48.9592 -0.00927592 47.9532 0.000343198H23.1441H22.6976V13.2561C22.6976 15.2915 21.889 17.2435 20.4498 18.6827C19.0106 20.1219 17.0586 20.9305 15.0232 20.9305H4V52.3256C4 54.361 4.80855 56.313 6.24777 57.7522C7.687 59.1915 9.639 60 11.6744 60H47.9532C48.961 60 49.959 59.8015 50.8901 59.4158C51.8212 59.0301 52.6672 58.4649 53.3798 57.7522C54.0925 57.0396 54.6578 56.1936 55.0434 55.2625C55.4291 54.3314 55.6276 53.3334 55.6276 52.3256V7.67472C55.6295 6.66323 55.4307 5.66142 55.0426 4.72731C54.6546 3.7932 54.0851 2.94536 53.3672 2.23289ZM31.7673 43.2559H20.6046C20.0495 43.2559 19.5171 43.0354 19.1246 42.6429C18.7321 42.2504 18.5115 41.718 18.5115 41.1629C18.5115 40.6078 18.7321 40.0754 19.1246 39.6829C19.5171 39.2904 20.0495 39.0699 20.6046 39.0699H31.7673C32.3224 39.0699 32.8547 39.2904 33.2473 39.6829C33.6398 40.0754 33.8603 40.6078 33.8603 41.1629C33.8603 41.718 33.6398 42.2504 33.2473 42.6429C32.8547 43.0354 32.3224 43.2559 31.7673 43.2559ZM42.93 32.0932H20.6046C20.0495 32.0932 19.5171 31.8727 19.1246 31.4802C18.7321 31.0876 18.5115 30.5553 18.5115 30.0002C18.5115 29.4451 18.7321 28.9127 19.1246 28.5202C19.5171 28.1277 20.0495 27.9072 20.6046 27.9072H42.93C43.4851 27.9072 44.0175 28.1277 44.41 28.5202C44.8025 28.9127 45.023 29.4451 45.023 30.0002C45.023 30.5553 44.8025 31.0876 44.41 31.4802C44.0175 31.8727 43.4851 32.0932 42.93 32.0932Z" fill="#006FFF"/>
</svg>',
			'pending-post' => '<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M29.3117 47.4457C29.3126 44.4801 30.1128 41.5696 31.628 39.0203C33.1432 36.471 35.3175 34.3772 37.9221 32.9592C40.5267 31.5411 43.4653 30.8513 46.4288 30.9622C49.3923 31.0731 52.2712 31.9807 54.7625 33.5895C54.7801 32.3672 54.7894 31.0629 54.7894 29.6555C54.7894 22.4191 54.5434 17.9027 54.057 13.2246H54.1262C53.8573 10.6178 52.8617 8.1389 51.253 6.07019C49.6442 4.00147 47.4868 2.42609 45.0266 1.52344C44.8965 1.47422 44.7664 1.42734 44.6352 1.38281C43.6629 1.05599 42.6576 0.837096 41.6375 0.730078L41.5402 0.719531C37.6484 0.319922 37.0238 0 28.8172 0C20.6105 0 19.2828 0.319922 15.3945 0.725391L15.2973 0.735937C14.2264 0.848397 13.1721 1.08434 12.1555 1.43906V1.38633C9.62051 2.24821 7.38583 3.81886 5.71632 5.91209C4.04681 8.00531 3.01248 10.5333 2.73594 13.1965C2.24844 17.884 2 22.4051 2 29.6555C2 36.9059 2.24844 41.4269 2.73594 46.1144C3.06882 49.3132 4.49171 52.3001 6.76586 54.574C9.04001 56.8479 12.027 58.2705 15.2258 58.6031L15.323 58.6137C19.2148 59.0191 19.8383 59.3391 28.0449 59.3391C30.7637 59.3391 32.7324 59.3039 34.2781 59.2418C32.704 57.7072 31.4535 55.8727 30.6005 53.8466C29.7475 51.8205 29.3093 49.644 29.3117 47.4457ZM15.6008 15.6035H41.184C41.9243 15.6035 42.6343 15.8976 43.1578 16.4211C43.6813 16.9446 43.9754 17.6546 43.9754 18.3949C43.9754 19.1352 43.6813 19.8453 43.1578 20.3687C42.6343 20.8922 41.9243 21.1863 41.184 21.1863H15.6066C14.8663 21.1863 14.1563 20.8922 13.6328 20.3687C13.1093 19.8453 12.8152 19.1352 12.8152 18.3949C12.8152 17.6546 13.1093 16.9446 13.6328 16.4211C14.1563 15.8976 14.8663 15.6035 15.6066 15.6035H15.6008ZM15.6008 33.1312C14.8702 33.1163 14.1746 32.8155 13.6632 32.2936C13.1518 31.7716 12.8654 31.07 12.8654 30.3393C12.8654 29.6085 13.1518 28.9069 13.6632 28.3849C14.1746 27.863 14.8702 27.5622 15.6008 27.5473H30.1918C30.5633 27.5397 30.9325 27.6062 31.2779 27.7431C31.6233 27.88 31.9379 28.0845 32.2033 28.3445C32.4687 28.6045 32.6796 28.9149 32.8235 29.2574C32.9674 29.5999 33.0416 29.9677 33.0416 30.3393C33.0416 30.7108 32.9674 31.0786 32.8235 31.4211C32.6796 31.7637 32.4687 32.074 32.2033 32.334C31.9379 32.594 31.6233 32.7985 31.2779 32.9354C30.9325 33.0723 30.5633 33.1389 30.1918 33.1312H15.6008Z" fill="#006FFF"/>
<path d="M45.8082 34.8914C43.3252 34.8914 40.898 35.6277 38.8334 37.0072C36.7689 38.3867 35.1598 40.3474 34.2096 42.6414C33.2593 44.9354 33.0107 47.4596 33.4951 49.8949C33.9795 52.3302 35.1752 54.5672 36.931 56.3229C38.6867 58.0787 40.9237 59.2744 43.359 59.7588C45.7943 60.2432 48.3185 59.9946 50.6125 59.0444C52.9065 58.0941 54.8672 56.485 56.2467 54.4205C57.6262 52.3559 58.3625 49.9287 58.3625 47.4457C58.3625 44.1161 57.0398 40.9229 54.6854 38.5685C52.331 36.2141 49.1378 34.8914 45.8082 34.8914ZM50.784 49.2832H45.5586C45.1623 49.2832 44.7823 49.1258 44.5021 48.8456C44.2219 48.5654 44.0645 48.1853 44.0645 47.7891V39.7617C44.0645 39.3654 44.2219 38.9854 44.5021 38.7052C44.7823 38.425 45.1623 38.2676 45.5586 38.2676C45.9549 38.2676 46.3349 38.425 46.6151 38.7052C46.8953 38.9854 47.0527 39.3654 47.0527 39.7617V46.2949H50.784C51.1803 46.2949 51.5603 46.4523 51.8405 46.7325C52.1207 47.0127 52.2781 47.3928 52.2781 47.7891C52.2781 48.1853 52.1207 48.5654 51.8405 48.8456C51.5603 49.1258 51.1803 49.2832 50.784 49.2832Z" fill="#004EB2"/>
</svg>',
			'draft-post'   => '<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M52.3277 16.9966H7.51415C4.46641 17.0147 2.00443 19.4888 2.00142 22.5367C1.99936 22.5676 1.99936 22.5989 2.00142 22.6298L5.20395 46.5256C5.24282 49.5584 7.71096 51.9973 10.7441 52H49.1197C52.1526 51.9973 54.6209 49.5584 54.6598 46.5256L57.8404 22.6298C57.8425 22.5989 57.8425 22.5676 57.8404 22.5367C57.8375 19.4888 55.3756 17.0147 52.3277 16.9966Z" fill="#006FFF"/>
<path d="M48.2383 11.4784H30.2384L26.68 7.24668C26.5452 7.08464 26.3432 6.99376 26.1326 7.00033H11.6035C9.33722 7.00334 7.50069 8.83987 7.49768 11.1061V15.0532H8.86628H52.3057C52.0393 13.0096 50.2991 11.4803 48.2383 11.4784Z" fill="#004EB2"/>
</svg>',
			'submit-post'  => '<svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16.9323 0H2.05736C0.925088 0 3.08037e-05 0.917657 3.08037e-05 2.05733V4.07025H18.9896V2.05733C18.9896 0.917657 18.0645 0 16.9323 0ZM2.91063 2.62124C2.58649 2.62124 2.32451 2.35853 2.32451 2.03513C2.32451 1.71173 2.58723 1.44901 2.91063 1.44901C3.23403 1.44901 3.49749 1.71099 3.49749 2.03513C3.49749 2.35927 3.23477 2.62124 2.91063 2.62124ZM4.9761 2.62124C4.65196 2.62124 4.38998 2.35853 4.38998 2.03513C4.38998 1.71173 4.6527 1.44901 4.9761 1.44901C5.30024 1.44901 5.56221 1.71173 5.56221 2.03513C5.56221 2.35853 5.30024 2.62124 4.9761 2.62124ZM7.04157 2.62124C6.71743 2.62124 6.45545 2.35853 6.45545 2.03513C6.45545 1.71173 6.71817 1.44901 7.04157 1.44901C7.36497 1.44901 7.62768 1.71173 7.62768 2.03513C7.62768 2.35853 7.36571 2.62124 7.04157 2.62124Z" fill="#89ADD9"/>
<path d="M16.1469 10.7092L11.8332 15.0229C11.737 15.1117 11.6778 15.2301 11.6556 15.3559L11.33 17.2727C11.2634 17.6945 11.626 18.0645 12.0552 17.9905L13.9645 17.6649C14.0977 17.6427 14.2088 17.5835 14.305 17.4947L18.6194 13.1802L16.1469 10.7092Z" fill="#B9D3F1"/>
<path d="M10.5611 15.1694C10.6292 14.7883 10.8075 14.459 11.0791 14.2074L16.9528 8.3329C17.4879 7.79193 18.2057 7.49147 18.9709 7.49147C18.9769 7.49147 18.9828 7.49295 18.9887 7.49295V5.18031H-0.00012207V14.2163C-0.00012207 15.3559 0.917535 16.2736 2.0572 16.2736H10.3738L10.5611 15.1694Z" fill="#89ADD9"/>
<path d="M20.2105 9.11735C19.5223 8.42911 18.4196 8.42911 17.7388 9.11735L16.9321 9.924L19.4039 12.3958L20.2105 11.5891C20.8914 10.9083 20.8914 9.79819 20.2105 9.11735Z" fill="#89ADD9"/>
</svg>',
			'edit'         => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M11.4849 1.5C11.3483 1.5 11.2172 1.55427 11.1206 1.65087L5.6019 7.16961L5.35907 8.14093L6.33039 7.8981L11.8491 2.37936C11.9457 2.28276 12 2.15174 12 2.01512C12 1.8785 11.9457 1.74748 11.8491 1.65087C11.7525 1.55427 11.6215 1.5 11.4849 1.5ZM10.06 0.590214C10.4379 0.212306 10.9504 0 11.4849 0C12.0193 0 12.5319 0.212306 12.9098 0.590214C13.2877 0.968122 13.5 1.48067 13.5 2.01512C13.5 2.54956 13.2877 3.06211 12.9098 3.44002L7.24415 9.10565C7.14803 9.20177 7.0276 9.26996 6.89572 9.30293L4.5102 9.89931C4.25461 9.96321 3.98425 9.88832 3.79796 9.70204C3.61168 9.51575 3.53679 9.24539 3.60069 8.9898L4.19707 6.60428C4.23004 6.4724 4.29823 6.35197 4.39435 6.25585L10.06 0.590214ZM0.569023 1.83414C0.933362 1.4698 1.42751 1.26512 1.94276 1.26512H6.11744C6.53165 1.26512 6.86744 1.6009 6.86744 2.01512C6.86744 2.42933 6.53165 2.76512 6.11744 2.76512H1.94276C1.82534 2.76512 1.71272 2.81177 1.62968 2.8948C1.54665 2.97784 1.5 3.09045 1.5 3.20788V11.5572C1.5 11.6747 1.54665 11.7873 1.62968 11.8703C1.71272 11.9534 1.82534 12 1.94276 12H10.2921C10.4095 12 10.5222 11.9534 10.6052 11.8703C10.6882 11.7873 10.7349 11.6747 10.7349 11.5572V7.38256C10.7349 6.96835 11.0707 6.63256 11.4849 6.63256C11.8991 6.63256 12.2349 6.96835 12.2349 7.38256V11.5572C12.2349 12.0725 12.0302 12.5666 11.6659 12.931C11.3015 13.2953 10.8074 13.5 10.2921 13.5H1.94276C1.42751 13.5 0.933361 13.2953 0.569023 12.931C0.204684 12.5666 0 12.0725 0 11.5572V3.20788C0 2.69263 0.204683 2.19848 0.569023 1.83414Z" fill="black"/>
</svg>',
			'delete'       => '<svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M5.20005 1.7C5.06744 1.7 4.94026 1.75268 4.8465 1.84645C4.75273 1.94022 4.70005 2.0674 4.70005 2.2V2.7H8.10005V2.2C8.10005 2.0674 8.04737 1.94022 7.9536 1.84645C7.85983 1.75268 7.73266 1.7 7.60005 1.7H5.20005ZM9.50005 2.7V2.2C9.50005 1.69609 9.29987 1.21282 8.94355 0.8565C8.58723 0.500181 8.10396 0.300003 7.60005 0.300003H5.20005C4.69614 0.300003 4.21287 0.500181 3.85655 0.8565C3.50023 1.21282 3.30005 1.69609 3.30005 2.2V2.7H1.00005C0.613449 2.7 0.300049 3.0134 0.300049 3.4C0.300049 3.7866 0.613449 4.1 1.00005 4.1H1.50005V11.8C1.50005 12.3039 1.70023 12.7872 2.05655 13.1435C2.41286 13.4998 2.89614 13.7 3.40005 13.7H9.40005C9.90396 13.7 10.3872 13.4998 10.7436 13.1435C11.0999 12.7872 11.3 12.3039 11.3 11.8V4.1H11.8C12.1866 4.1 12.5 3.7866 12.5 3.4C12.5 3.0134 12.1866 2.7 11.8 2.7H9.50005ZM2.90005 4.1V11.8C2.90005 11.9326 2.95273 12.0598 3.0465 12.1536C3.14026 12.2473 3.26744 12.3 3.40005 12.3H9.40005C9.53266 12.3 9.65983 12.2473 9.7536 12.1536C9.84737 12.0598 9.90005 11.9326 9.90005 11.8V4.1H2.90005Z" fill="#F11212"/>
</svg>',
			'calender'     => '<svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 0.279999C5.89764 0.279999 6.22 0.602354 6.22 0.999999V2.08H11.98V0.999999C11.98 0.602354 12.3024 0.279999 12.7 0.279999C13.0976 0.279999 13.42 0.602354 13.42 0.999999V2.08H15.4C16.7918 2.08 17.92 3.20824 17.92 4.6V17.2C17.92 18.5918 16.7918 19.72 15.4 19.72H2.8C1.40824 19.72 0.279999 18.5918 0.279999 17.2V4.6C0.279999 3.20824 1.40824 2.08 2.8 2.08H4.78V0.999999C4.78 0.602354 5.10235 0.279999 5.5 0.279999ZM4.78 3.52H2.8C2.20353 3.52 1.72 4.00353 1.72 4.6V7.48H16.48V4.6C16.48 4.00353 15.9965 3.52 15.4 3.52H13.42V4.6C13.42 4.99764 13.0976 5.32 12.7 5.32C12.3024 5.32 11.98 4.99764 11.98 4.6V3.52H6.22V4.6C6.22 4.99764 5.89764 5.32 5.5 5.32C5.10235 5.32 4.78 4.99764 4.78 4.6V3.52ZM16.48 8.92H1.72V17.2C1.72 17.7965 2.20353 18.28 2.8 18.28H15.4C15.9965 18.28 16.48 17.7965 16.48 17.2V8.92Z" fill="#444444"/>
</svg>',
			'comment'      => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M3 1.75C2.66848 1.75 2.35054 1.8817 2.11612 2.11612C1.8817 2.35054 1.75 2.66848 1.75 3V17.1893L4.46967 14.4697C4.61032 14.329 4.80109 14.25 5 14.25H17C17.3315 14.25 17.6495 14.1183 17.8839 13.8839C18.1183 13.6495 18.25 13.3315 18.25 13V3C18.25 2.66848 18.1183 2.35054 17.8839 2.11612C17.6495 1.8817 17.3315 1.75 17 1.75H3ZM1.05546 1.05546C1.57118 0.539731 2.27065 0.25 3 0.25H17C17.7293 0.25 18.4288 0.539731 18.9445 1.05546C19.4603 1.57118 19.75 2.27065 19.75 3V13C19.75 13.7293 19.4603 14.4288 18.9445 14.9445C18.4288 15.4603 17.7293 15.75 17 15.75H5.31066L1.53033 19.5303C1.31583 19.7448 0.993243 19.809 0.712987 19.6929C0.432732 19.5768 0.25 19.3033 0.25 19V3C0.25 2.27065 0.539731 1.57118 1.05546 1.05546Z" fill="#444444"/>
</svg>',
			'folder'       => '<svg width="19" height="17" viewBox="0 0 19 17" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M2.7 1.75C2.4433 1.75 2.20004 1.85011 2.02297 2.02371C1.84641 2.19681 1.75 2.4284 1.75 2.66667V14.3333C1.75 14.5716 1.84641 14.8032 2.02297 14.9763C2.20004 15.1499 2.44331 15.25 2.7 15.25H16.3C16.5567 15.25 16.8 15.1499 16.977 14.9763C17.1536 14.8032 17.25 14.5716 17.25 14.3333V5.16667C17.25 4.9284 17.1536 4.69681 16.977 4.52371C16.8 4.35011 16.5567 4.25 16.3 4.25H8.65C8.40167 4.25 8.16944 4.12708 8.02981 3.92173L6.55303 1.75H2.7ZM0.972865 0.952601C1.43342 0.501077 2.05496 0.25 2.7 0.25H6.95C7.19833 0.25 7.43056 0.372918 7.5702 0.578267L9.04697 2.75H16.3C16.945 2.75 17.5666 3.00108 18.0271 3.4526C18.4882 3.90462 18.75 4.52088 18.75 5.16667V14.3333C18.75 14.9791 18.4882 15.5954 18.0271 16.0474C17.5666 16.4989 16.945 16.75 16.3 16.75H2.7C2.05496 16.75 1.43342 16.4989 0.972865 16.0474C0.511807 15.5954 0.25 14.9791 0.25 14.3333V2.66667C0.25 2.02088 0.511807 1.40462 0.972865 0.952601Z" fill="#444444"/>
</svg>
',
			'tags'         => '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M0.25 1C0.25 0.585786 0.585786 0.25 1 0.25H8.91667C9.11558 0.25 9.30634 0.329018 9.447 0.46967L16.249 7.27166C16.6836 7.70885 16.9275 8.30023 16.9275 8.91667C16.9275 9.5331 16.6836 10.1245 16.249 10.5617L16.2474 10.5632L10.5715 16.2392C10.5714 16.2393 10.5716 16.2391 10.5715 16.2392C10.3548 16.456 10.0973 16.6283 9.81415 16.7457C9.53089 16.8631 9.22726 16.9235 8.92062 16.9235C8.61399 16.9235 8.31036 16.8631 8.0271 16.7457C7.74398 16.6283 7.48676 16.4563 7.27013 16.2395C7.27002 16.2394 7.27024 16.2397 7.27013 16.2395L0.469979 9.4473C0.329138 9.30663 0.25 9.11573 0.25 8.91667V1ZM1.75 1.75V8.60575L8.33044 15.1785C8.40783 15.256 8.50034 15.3181 8.60151 15.36C8.70267 15.402 8.81111 15.4235 8.92062 15.4235C9.03014 15.4235 9.13858 15.402 9.23974 15.36C9.34091 15.3181 9.43281 15.2566 9.51021 15.1791L15.1852 9.50417C15.1854 9.50398 15.185 9.50435 15.1852 9.50417C15.34 9.34809 15.4275 9.13656 15.4275 8.91667C15.4275 8.69683 15.3406 8.48591 15.1858 8.32984C15.1856 8.32962 15.1861 8.33007 15.1858 8.32984L8.60601 1.75H1.75Z" fill="#444444"/>
</svg>
',
			'eye'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z"/></svg>',
		];

		if ( isset( $icons[ $name ] ) ) {
			self::print_html( $icons[ $name ] );
		}
	}

	/**
	 * Get Current Time
	 *
	 * @return void
	 */
	public static function current_time() {
		$timezone    = new \DateTimeZone( wp_timezone_string() );
		$date_format = get_option( 'date_format' ); // e.g. "F j, Y"
		$time_format = get_option( 'time_format' ); // e.g. "H:i:s"

		printf( '<time>%s</time>', esc_html( wp_date( "$date_format $time_format", null, $timezone ) ) );
	}

	/**
	 * Count post by type
	 *
	 * @param $userid
	 * @param $post_type
	 *
	 * @return int|null
	 */
	public static function count_post( $userid, $status = 'publish', $post_type = 'post' ) {
		$args   = [
			'numberposts' => - 1,
			'post_type'   => $post_type,
			'post_status' => $status,
			'author'      => $userid,
			'fields'      => 'ids',
		];
		$number = count( get_posts( $args ) );
		if ( $number < 10 ) {
			$number = str_pad( $number, 2, '0', STR_PAD_LEFT );
		}

		return $number;
	}

	/**
	 * Count paged variable
	 *
	 * @param $current_url
	 *
	 * @return int
	 */
	public static function paged( $current_url = '' ) {
		if ( ! $current_url ) {
			global $wp;
			$current_url = home_url( add_query_arg( [], $wp->request ) );
		}
		$url_parts = explode( '/', $current_url );

		$page_number = end( $url_parts );

		if ( is_numeric( $page_number ) ) {
			$page_number = intval( $page_number );
		} else {
			$page_number = 1;
		}

		return $page_number;
	}

	public static function status_message( $status = '' ) {
		echo "<div class='tpg-post-submit-status " . esc_attr( $status ) . " '>";
		if ( 'success' === $status ) {
			esc_html_e( 'Post submit successful', 'the-post-grid' );
		} elseif ( 'fail' === $status ) {
			esc_html_e( 'Post submit fail.', 'the-post-grid' );
		} elseif ( 'error' === $status ) {
			esc_html_e( 'You are not authorize to submit a post.', 'the-post-grid' );
		}
		echo '</div>';
	}


	/**
	 * Get vailable post status for the user.
	 *
	 * @param $post_status
	 *
	 * @return mixed|string
	 */
	public static function available_user_post_status( $post_status ) {

		$post_statuses = [ 'publish' ];
		if ( is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$roles = (array) $user->roles;

			if ( in_array( 'administrator', $roles ) || in_array( 'editor', $roles ) ) {
				$post_statuses = [ 'publish', 'private', 'pending', 'draft', 'auto-draft', 'future', 'inherit', 'trash' ];
			} elseif ( in_array( 'author', $roles ) ) {
				$post_statuses = [ 'publish', 'private', 'draft', 'future' ];
			} elseif ( in_array( 'contributor', $roles ) ) {
				$post_statuses = [ 'publish', 'pending', 'draft' ];
			}
		}

		return in_array( $post_status, $post_statuses ) ? $post_status : 'publish';
	}

	/**
	 * Get Available Post Type
	 *
	 * @param $post_type
	 *
	 * @return mixed|string
	 */
	public static function available_post_type( $post_type ) {

		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return 'post';
		}

		if ( $post_type_object->public ) {
			return $post_type;
		}

		if ( is_user_logged_in() ) {
			$user          = wp_get_current_user();
			$roles         = (array) $user->roles;
			$allowed_roles = [ 'administrator', 'editor', 'author', 'contributor' ];

			if ( array_intersect( $roles, $allowed_roles ) ) {
				return $post_type;
			}
		}

		return 'post';
	}

	public static function available_post_types( $post_types, $is_guten = false ) {

		if ( $is_guten ) {
			$post_types = wp_list_pluck( $post_types, 'value' );
		}

		$final_post_type = [];
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				return 'post';
			}

			if ( $post_type_object->public ) {
				$final_post_type[] = $post_type;
				continue;
			}

			if ( is_user_logged_in() ) {
				$user          = wp_get_current_user();
				$roles         = (array) $user->roles;
				$allowed_roles = [ 'administrator', 'editor', 'author', 'contributor' ];

				if ( array_intersect( $roles, $allowed_roles ) ) {
					$final_post_type[] = $post_type;
				}
			}
		}

		return $final_post_type;
	}

	/**
	 * Scaping array
	 *
	 * @param $arr
	 *
	 * @return array|mixed
	 */
	public static function escape_array( $arr ) {

		if ( ! is_array( $arr ) ) {
			return $arr;
		}
		$escaped_arr = [];
		foreach ( $arr as $key => $value ) {
			if ( is_array( $value ) ) {
				$escaped_arr[ esc_attr( $key ) ] = self::escape_array( $value );
			} else {
				$escaped_arr[ esc_attr( $key ) ] = $value;
			}
		}

		return $escaped_arr;
	}

	/**
	 * Remove unnecessary zero after point
	 *
	 * @param $value
	 *
	 * @return mixed|string
	 */
	public static function remove_unnecessary_zero( $value, $return_type = '' ) {

		if ( strpos( $value, '.' ) ) {
			[ $a, $b ] = explode( '.', $value );

			if ( $return_type == '1' ) {
				return $a;
			}

			if ( $return_type == '2' ) {
				return $b;
			}

			if ( ! array_filter( str_split( $b ) ) ) {
				$value = $a;
			} else {
				$value = $a . '.' . rtrim( $b, '0' );
			}
		}

		return $value;
	}

	/**
	 * Number Shorten
	 *
	 * @param $number
	 * @param $precision
	 * @param $divisors
	 *
	 * @return mixed|string
	 */
	public static function number_shorten( $number, $precision = 1, $divisors = null ) {
		$number = str_replace( ',', '', $number );
		if ( $number < 1000 ) {
			return $number;
		}

		$thousand    = _x( 'K', 'Thousand Shorthand', 'the-post-grid' );
		$million     = _x( 'M', 'Million Shorthand', 'the-post-grid' );
		$billion     = _x( 'B', 'Billion Shorthand', 'the-post-grid' );
		$trillion    = _x( 'T', 'Trillion Shorthand', 'the-post-grid' );
		$quadrillion = _x( 'Qa', 'Quadrillion Shorthand', 'the-post-grid' );
		$quintillion = _x( 'Qi', 'Quintillion Shorthand', 'the-post-grid' );

		$shorthand_label = apply_filters(
			'tpg_shorthand_price_label',
			[
				'thousand'    => $thousand,
				'million'     => $million,
				'billion'     => $billion,
				'trillion'    => $trillion,
				'quadrillion' => $quadrillion,
				'quintillion' => $quintillion,
			]
		);

		// Setup default $divisors if not provided
		if ( ! isset( $divisors ) ) {
			$divisors = [
				pow( 1000, 0 ) => '', // 1000^0 == 1
				pow( 1000, 1 ) => isset( $shorthand_label['thousand'] ) ? $shorthand_label['thousand'] : $thousand,
				pow( 1000, 2 ) => isset( $shorthand_label['million'] ) ? $shorthand_label['million'] : $million,
				pow( 1000, 3 ) => isset( $shorthand_label['billion'] ) ? $shorthand_label['billion'] : $billion,
				pow( 1000, 4 ) => isset( $shorthand_label['trillion'] ) ? $shorthand_label['trillion'] : $trillion,
				pow( 1000, 5 ) => isset( $shorthand_label['quadrillion'] ) ? $shorthand_label['quadrillion'] : $quadrillion,
				pow( 1000, 6 ) => isset( $shorthand_label['quintillion'] ) ? $shorthand_label['quintillion'] : $quintillion,
			];
		}

		// Loop through each $divisor and find the
		// lowest amount that matches
		foreach ( $divisors as $divisor => $shorthand ) {
			if ( abs( $number ) < ( $divisor * 1000 ) ) {
				// We found a match!
				break;
			}
		}

		// We found our match, or there were no matches.
		// Either way, use the last defined value for $divisor.

		$shorthand_price = apply_filters( 'tpg_shorthand_price', number_format( $number / $divisor, $precision ) );

		return self::remove_unnecessary_zero( $shorthand_price ) . "<span class='number-shorthand'>{$shorthand}</span>";
	}

	/**
	 * Number to K, Lac, Cr convert
	 *
	 * @param $number
	 *
	 * @return mixed|string
	 */
	public static function number_to_lac( $number, $precision = 1 ) {

		$number = (int) str_replace( ',', '', $number );

		$hundred   = '';
		$thousand  = _x( 'K', 'Thousand Shorthand', 'the-post-grid' );
		$thousands = _x( 'K', 'Thousands Shorthand', 'the-post-grid' );
		$lac       = _x( ' Lac', 'Lac Shorthand', 'the-post-grid' );
		$lacs      = _x( ' Lacs', 'Lacs Shorthand', 'the-post-grid' );
		$cr        = _x( ' Cr', 'Cr Shorthand', 'the-post-grid' );
		$crs       = _x( ' Crs', 'Crs Shorthand', 'the-post-grid' );

		if ( 0 == $number ) {
			return '';
		}

		$n_count = strlen( self::remove_unnecessary_zero( $number, '1' ) ); // 7
		switch ( $n_count ) {
			case 3:
				$val       = $number / 100;
				$val       = number_format( $val, $precision );
				$shorthand = $hundred;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 4:
				$val       = $number / 1000;
				$val       = number_format( $val, $precision );
				$shorthand = $thousand;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 5:
				$val       = $number / 1000;
				$val       = number_format( $val, $precision );
				$shorthand = $thousands;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 6:
				$val       = $number / 100000;
				$val       = number_format( $val, $precision );
				$shorthand = $lac;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 7:
				$val       = $number / 100000;
				$val       = number_format( $val, $precision );
				$shorthand = $lacs;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 8:
				$val       = $number / 10000000;
				$val       = number_format( $val, $precision );
				$shorthand = $cr;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			case 8 < $n_count:
				$val       = $number / 10000000;
				$val       = number_format( $val, $precision );
				$shorthand = $crs;
				$finalval  = self::remove_unnecessary_zero( $val ) . "<span class='number-shorthand'>{$shorthand}</span>";
				break;
			default:
				$finalval = $number;
		}

		return $finalval;
	}
}