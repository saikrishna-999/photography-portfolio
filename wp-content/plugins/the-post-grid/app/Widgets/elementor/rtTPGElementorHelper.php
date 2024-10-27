<?php
/**
 * Elementor Helper Class
 *
 * @package RT_TPG
 */

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
require_once RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/rtTPGElementorQuery.php';

/**
 * Elementor Helper Class
 */
class rtTPGElementorHelper {
	/**
	 *  Post Query Builderx
	 *
	 * @param $ref
	 */
	public static function query( $ref ) {
		$post_types = Fns::get_post_types();

		$taxonomies = get_taxonomies( [], 'objects' );

		do_action( 'rt_tpg_el_query_build', $ref );
		$ref->start_controls_section(
			'rt_post_query',
			[
				'label' => esc_html__( 'Query Build', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'multiple_post_type',
			[
				'label'        => esc_html__( 'Multiple Post Types?', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'condition'    => [
					'post_type!' => 'current_query',
				],
			]
		);

		$ref->add_control(
			'multiple_post_type_notice',
			[
				'type'       => Controls_Manager::ALERT,
				'alert_type' => 'info',
				'condition'  => [
					'multiple_post_type' => 'yes',
				],
				'content'    => esc_html__( 'If you enable Multiple Post Types the Front-end Filter will not work.', 'the-post-grid' ),
			]
		);

		$singlePostType = $post_types;
		if ( rtTPG()->hasPro() ) {
			$singlePostType = $post_types + [ 'current_query' => __( 'Current Query', 'the-post-grid' ) ];
		}
		$ref->add_control(
			'post_type',
			[
				'label'       => esc_html__( 'Post Source', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $singlePostType,
				'default'     => 'post',
				'description' => $ref->get_pro_message( 'all post type.' ),
				'condition'   => [
					'multiple_post_type!' => 'yes',
				],
			]
		);

		$ref->add_control(
			'current_query_note',
			[
				'type'        => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'dismissible' => true,
				'condition'   => [
					'post_type' => 'current_query',
				],
				'content'     => esc_html__( 'The Current Query is used only for the archive page. If you use the Current Query the below settings under this tab will not work.', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'post_types',
			[
				'label'       => esc_html__( 'Post Sources', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $post_types,
				'default'     => [ 'post' ],
				'description' => $ref->get_pro_message( 'all post type.' ),
				'condition'   => [
					'multiple_post_type' => 'yes',
				],
			]
		);

		// TODO: Common Filter

		$ref->add_control(
			'common_filters_heading',
			[
				'label'     => esc_html__( 'Common Filters:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$ref->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Include only', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter the post IDs separated by comma for include', 'the-post-grid' ),
				'placeholder' => 'Eg. 10, 15, 17',
			]
		);

		$ref->add_control(
			'exclude',
			[
				'label'       => esc_html__( 'Exclude', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter the post IDs separated by comma for exclude', 'the-post-grid' ),
				'placeholder' => 'Eg. 12, 13',
			]
		);

		$ref->add_control(
			'post_limit',
			[
				'label'       => esc_html__( 'Limit', 'the-post-grid' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'The number of posts to show. Enter -1 to show all found posts.', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'display_per_page',
			[
				'label'       => esc_html__( 'Display Per Page', 'the-post-grid' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Enter how may posts will display per page. It works only for the the pagination and ajax-filer.', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'offset',
			[
				'label'       => esc_html__( 'Offset', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Post offset', 'the-post-grid' ),
				'description' => esc_html__( 'Number of posts to skip. The offset parameter is ignored when post limit => -1 is used.', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'instant_query',
			[
				'label'       => esc_html__( 'Quick Query', 'the-post-grid' ) . $ref->pro_label,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default'                     => esc_html__( '--Quick Query--', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'popular_post_1_day_view'     => esc_html__( 'Popular Post (1 Day View)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'popular_post_7_days_view'    => esc_html__( 'Popular Post (7 Days View)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'popular_post_30_days_view'   => esc_html__( 'Popular Post (30 Days View)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'popular_post_all_times_view' => esc_html__( 'Popular Post (All time View)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'most_comment_1_day'          => esc_html__( 'Most Comment (1 Day)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'most_comment_7_days'         => esc_html__( 'Most Comment (7 Days)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'most_comment_30_days'        => esc_html__( 'Most Comment (30 Days)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'random_post_7_days'          => esc_html__( 'Random Posts (7 Days)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'random_post_30_days'         => esc_html__( 'Random Post (30 Days)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'related_category'            => esc_html__( 'Related Posts (Category)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'related_tag'                 => esc_html__( 'Related Posts (Tag)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
					'related_cat_tag'             => esc_html__( 'Related Posts (Tag and Category)', 'the-post-grid' ) . ' ' . $ref->pro_label(),
				],
				'classes'     => rtTPG()->hasPro() ? '' : 'tpg-pro-field-select',
				'default'     => 'default',
				'description' => esc_html__( 'If you choose any value from here the orderby worn\'t work.', 'the-post-grid' ),
			]
		);

		// Advance Filter

		$ref->add_control(
			'advanced_filters_heading',
			[
				'label'     => esc_html__( 'Advanced Filters:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$_url = site_url( 'wp-admin/edit.php?post_type=rttpg&page=tgp_taxonomy_order' );

		foreach ( $taxonomies as $taxonomy => $object ) {

			if ( ! isset( $object->object_type[0] )
			     || ! in_array( $object->object_type[0], array_keys( $post_types ) )
			     || in_array( $taxonomy, Fns::get_excluded_taxonomy() )
			) {
				continue;
			}
			$ref->add_control(
				$taxonomy . '_ids',
				[
					'label'       => esc_html__( 'By ', 'the-post-grid' ) . $object->label,
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => Fns::tpg_get_categories_by_id( $taxonomy ),
					'condition'   => [
						'post_type'           => $object->object_type,
						'multiple_post_type!' => 'yes',
					],
					'description' => "For custom order: <a target='_blank' href='" . $_url . "'>The Post Grid > Taxonomy Order</a>",
				]
			);

			$ref->add_control(
				$taxonomy . '_ids2',
				[
					'label'       => esc_html__( 'By ', 'the-post-grid' ) . $object->label,
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => Fns::tpg_get_categories_by_id( $taxonomy ),
					'condition'   => [
						'multiple_post_type' => 'yes',
					],
					'description' => "For custom order: <a target='_blank' href='" . $_url . "'>The Post Grid > Taxonomy Order</a>",
				]
			);
		}

		$ref->add_control(
			'relation',
			[
				'label'   => esc_html__( 'Taxonomies Relation', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'OR',
				'options' => [
					'OR'  => __( 'OR', 'the-post-grid' ),
					'AND' => __( 'AND', 'the-post-grid' ),
				],
			]
		);

		$ref->add_control(
			'author',
			[
				'label'       => esc_html__( 'By Author', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Fns::rt_get_users(),
			]
		);

		$ref->add_control(
			'post_keyword',
			[
				'label'       => esc_html__( 'By Keyword', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Search by keyword', 'the-post-grid' ),
				'description' => esc_html__( 'Search by post title or content keyword', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'date_range',
			[
				'label'          => esc_html__( 'Date Range (Start to End)', 'the-post-grid' ) . $ref->pro_label,
				'type'           => Controls_Manager::DATE_TIME,
				'placeholder'    => 'Choose date...',
				'description'    => esc_html__( 'NB: Enter DEL button for delete date range', 'the-post-grid' ),
				'classes'        => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'picker_options' => [
					'enableTime' => false,
					'mode'       => 'range',
					'dateFormat' => 'M j, Y',
				],
			]
		);

		$orderby_opt = [
			'date'          => esc_html__( 'Date', 'the-post-grid' ),
			'ID'            => esc_html__( 'Order by post ID', 'the-post-grid' ),
			'author'        => esc_html__( 'Author', 'the-post-grid' ),
			'title'         => esc_html__( 'Title', 'the-post-grid' ),
			'modified'      => esc_html__( 'Last modified date', 'the-post-grid' ),
			'parent'        => esc_html__( 'Post parent ID', 'the-post-grid' ),
			'comment_count' => esc_html__( 'Number of comments', 'the-post-grid' ),
			'menu_order'    => esc_html__( 'Menu order', 'the-post-grid' ),
		];

		if ( rtTPG()->hasPro() ) {
			$prderby_pro_opt = [
				'rand'                => esc_html__( 'Random order', 'the-post-grid' ),
				'meta_value'          => esc_html__( 'Meta value', 'the-post-grid' ), //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_value_num'      => esc_html__( 'Meta value number', 'the-post-grid' ),
				'meta_value_datetime' => esc_html__( 'Meta value datetime', 'the-post-grid' ),
			];
			$orderby_opt     = array_merge( $orderby_opt, $prderby_pro_opt );
		}

		$ref->add_control(
			'orderby',
			[
				'label'       => esc_html__( 'Order by', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $orderby_opt,
				'default'     => 'date',
				'description' => $ref->get_pro_message( 'Random Order.' ),
				'condition'   => [
					'instant_query' => 'default',
				],
			]
		);

		$ref->add_control(
			'meta_key',
			[
				'label'       => esc_html__( 'Meta Key', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Meta Key.', 'the-post-grid' ),
				'condition'   => [
					'orderby' => [ 'meta_value', 'meta_value_num', 'meta_value_datetime' ],
				],
			]
		);

		$ref->add_control(
			'order',
			[
				'label'     => esc_html__( 'Sort order', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'ASC'  => esc_html__( 'ASC', 'the-post-grid' ),
					'DESC' => esc_html__( 'DESC', 'the-post-grid' ),
				],
				'default'   => 'DESC',
				'condition' => [
					'orderby!' => 'rand',
				],
			]
		);

		/*
		 * the below code should remove from later
		 * $ref->add_control(
			'post_status',
			[
				'label'   => esc_html__( 'Post Status', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Options::rtTPGPostStatus(),
				'default' => 'publish',
			]
		);*/

		$ref->add_control(
			'ignore_sticky_posts',
			[
				'label'        => esc_html__( 'Ignore sticky posts at the top', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'disabled'     => true,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'no_posts_found_text',
			[
				'label'       => esc_html__( 'No post found Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'No posts found.', 'the-post-grid' ),
				'placeholder' => esc_html__( 'Enter No post found', 'the-post-grid' ),
				'separator'   => 'before',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Archive Query Builder
	 *
	 * @param $ref
	 * @param $layout_type
	 *
	 * @return void
	 */
	public static function query_builder( $ref, $layout_type = '' ) {
		$post_types = Fns::get_post_types();

		$taxonomies = get_object_taxonomies( 'post', 'object' );

		do_action( 'rt_tpg_el_query_build', $ref );
		$ref->start_controls_section(
			'rt_post_query',
			[
				'label' => esc_html__( 'Query Build', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'post_limit',
			[
				'label'       => esc_html__( 'Posts per page', 'the-post-grid' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'The number of posts to show. Enter -1 to show all found posts.', 'the-post-grid' ),
			]
		);

		if ( 'single' == $layout_type ) {
			$get_all_taxonomy = [];
			foreach ( $taxonomies as $taxonomy => $object ) {
				if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) )
				     || in_array( $taxonomy, Fns::get_excluded_taxonomy() )
				) {
					continue;
				}
				$get_all_taxonomy[ $object->name ] = $object->label;
			}

			$ref->add_control(
				'taxonomy_lists',
				[
					'label'   => esc_html__( 'Select a Taxonomy for relation', 'the-post-grid' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'category',
					'options' => $get_all_taxonomy,
				]
			);

			$orderby_opt = [
				'date'          => esc_html__( 'Date', 'the-post-grid' ),
				'ID'            => esc_html__( 'Order by post ID', 'the-post-grid' ),
				'author'        => esc_html__( 'Author', 'the-post-grid' ),
				'title'         => esc_html__( 'Title', 'the-post-grid' ),
				'modified'      => esc_html__( 'Last modified date', 'the-post-grid' ),
				'parent'        => esc_html__( 'Post parent ID', 'the-post-grid' ),
				'comment_count' => esc_html__( 'Number of comments', 'the-post-grid' ),
				'menu_order'    => esc_html__( 'Menu order', 'the-post-grid' ),
				'rand'          => esc_html__( 'Random order', 'the-post-grid' ),

			];

			$ref->add_control(
				'orderby',
				[
					'label'   => esc_html__( 'Order by', 'the-post-grid' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $orderby_opt,
					'default' => 'date',
				]
			);

			$ref->add_control(
				'order',
				[
					'label'     => esc_html__( 'Sort order', 'the-post-grid' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'ASC'  => esc_html__( 'ASC', 'the-post-grid' ),
						'DESC' => esc_html__( 'DESC', 'the-post-grid' ),
					],
					'default'   => 'DESC',
					'condition' => [
						'orderby!' => 'menu_order',
					],
				]
			);
		} else {
			$ref->add_control(
				'post_id',
				[
					'label'       => esc_html__( 'Include only', 'the-post-grid' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'Enter the post IDs separated by comma for include', 'the-post-grid' ),
					'placeholder' => 'Eg. 10, 15, 17',
				]
			);

			$ref->add_control(
				'exclude',
				[
					'label'       => esc_html__( 'Exclude', 'the-post-grid' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'Enter the post IDs separated by comma for exclude', 'the-post-grid' ),
					'placeholder' => 'Eg. 12, 13',
				]
			);

			$ref->add_control(
				'offset',
				[
					'label'       => esc_html__( 'Offset', 'the-post-grid' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter Post offset', 'the-post-grid' ),
					'description' => esc_html__( 'Number of posts to skip. The offset parameter is ignored when post limit => -1 is used.', 'the-post-grid' ),
				]
			);

			$ref->add_control(
				'no_posts_found_text_archive',
				[
					'label'       => esc_html__( 'No post found Text', 'the-post-grid' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'No posts found.', 'the-post-grid' ),
					'placeholder' => esc_html__( 'Enter No post found', 'the-post-grid' ),
					'separator'   => 'before',
				]
			);
		}
		$ref->end_controls_section();
	}

	/**
	 * Grid Layout Settings
	 *
	 * @param $ref
	 */
	public static function grid_layouts( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			$prefix . '_layout_settings',
			[
				'label' => esc_html__( 'Layout', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( 'grid' === $prefix ) {
			$layout_class   = 'grid-layout';
			$layout_options = [
				$prefix . '-layout1'   => [
					'title' => esc_html__( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout3'   => [
					'title' => esc_html__( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout4'   => [
					'title' => esc_html__( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout2'   => [
					'title' => esc_html__( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout5'   => [
					'title' => esc_html__( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout5-2' => [
					'title' => esc_html__( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout6'   => [
					'title' => esc_html__( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout6-2' => [
					'title' => esc_html__( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout7'   => [
					'title' => esc_html__( 'Gallery', 'the-post-grid' ),
				],
			];
		}

		if ( 'grid_hover' === $prefix ) {
			$layout_class   = 'grid-hover-layout';
			$layout_options = [
				$prefix . '-layout1'   => [
					'title' => esc_html__( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout2'   => [
					'title' => esc_html__( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout3'   => [
					'title' => esc_html__( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout4'   => [
					'title' => esc_html__( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout4-2' => [
					'title' => esc_html__( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout5'   => [
					'title' => esc_html__( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout5-2' => [
					'title' => esc_html__( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout6'   => [
					'title' => esc_html__( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout6-2' => [
					'title' => esc_html__( 'Layout 9', 'the-post-grid' ),
				],
				$prefix . '-layout7'   => [
					'title' => esc_html__( 'Layout 10', 'the-post-grid' ),
				],
				$prefix . '-layout7-2' => [
					'title' => esc_html__( 'Layout 11', 'the-post-grid' ),
				],
				$prefix . '-layout8'   => [
					'title' => esc_html__( 'Layout 12', 'the-post-grid' ),
				],
				$prefix . '-layout9'   => [
					'title' => esc_html__( 'Layout 13', 'the-post-grid' ),
				],
				$prefix . '-layout9-2' => [
					'title' => esc_html__( 'Layout 14', 'the-post-grid' ),
				],
				$prefix . '-layout10'  => [
					'title' => esc_html__( 'Layout 15', 'the-post-grid' ),
				],
				$prefix . '-layout11'  => [
					'title' => esc_html__( 'Layout 16', 'the-post-grid' ),
				],
			];
		}

		if ( 'slider' === $prefix ) {
			$layout_class   = 'slider-layout';
			$layout_options = [
				$prefix . '-layout1'  => [
					'title' => esc_html__( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout2'  => [
					'title' => esc_html__( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout3'  => [
					'title' => esc_html__( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout4'  => [
					'title' => esc_html__( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout5'  => [
					'title' => esc_html__( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout6'  => [
					'title' => esc_html__( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout7'  => [
					'title' => esc_html__( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout8'  => [
					'title' => esc_html__( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout9'  => [
					'title' => esc_html__( 'Layout 9', 'the-post-grid' ),
				],
				$prefix . '-layout10' => [
					'title' => esc_html__( 'Layout 10', 'the-post-grid' ),
				],
				$prefix . '-layout11' => [
					'title' => esc_html__( 'Layout 11', 'the-post-grid' ),
				],
				$prefix . '-layout12' => [
					'title' => esc_html__( 'Layout 12', 'the-post-grid' ),
				],
				$prefix . '-layout13' => [
					'title' => esc_html__( 'Layout 13', 'the-post-grid' ),
				],
			];

			if ( 'single' === $layout_type ) {
				$layout_options = array_slice( $layout_options, 0, 9 );
			}
		}

		$ref->add_control(
			$prefix . '_layout',
			[
				'label'          => esc_html__( 'Choose Layout', 'the-post-grid' ),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => true,
				'options'        => $layout_options,
				'toggle'         => false,
				'default'        => $prefix . '-layout1',
				'style_transfer' => true,
				'classes'        => 'tpg-image-select ' . $layout_class . ' ' . $ref->is_post_layout,
			]
		);

		$ref->add_control(
			'offset_img_position',
			[
				'label'        => esc_html__( 'Offset Image Position', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'image-left',
				'options'      => [
					'image-left'  => esc_html__( 'Left (Default)', 'the-post-grid' ),
					'image-right' => esc_html__( 'Right', 'the-post-grid' ),
				],
				'prefix_class' => 'offset-',
				'condition'    => [
					'grid_layout' => [
						'grid-layout5',
						'grid-layout5-2',
						'list-layout2',
						'list-layout2-2',
						'list-layout3',
						'list-layout3-2',
					],
				],
			]
		);

		$ref->add_control(
			'middle_border',
			[
				'label'     => esc_html__( 'Middle Border?', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'yes',
				'options'   => [
					'yes' => esc_html__( 'Yes', 'the-post-grid' ),
					'no'  => esc_html__( 'No', 'the-post-grid' ),
				],
				'condition' => [
					'grid_layout' => [ 'grid-layout6', 'grid-layout6-2' ],
				],
			]
		);

		$ref->add_control(
			'layout_options_heading',
			[
				'label'   => esc_html__( 'Layout Options:', 'the-post-grid' ),
				'type'    => Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$column_options = [
			'0'  => esc_html__( 'Default from layout', 'the-post-grid' ),
			'12' => esc_html__( '1 Columns', 'the-post-grid' ),
			'6'  => esc_html__( '2 Columns', 'the-post-grid' ),
			'4'  => esc_html__( '3 Columns', 'the-post-grid' ),
			'3'  => esc_html__( '4 Columns', 'the-post-grid' ),
		];

		if ( 'grid' === $prefix ) {
			$grid_column_condition = [
				'grid_layout!' => [ 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2' ],
			];
		}

		if ( 'grid_hover' === $prefix ) {
			$grid_column_condition = [
				'grid_hover_layout!' => [ 'grid_hover-layout8' ],
			];
		}

		if ( 'slider' === $prefix ) {
			$column_options        = [
				'0' => esc_html__( 'Default from layout', 'the-post-grid' ),
				'1' => esc_html__( '1 Columns', 'the-post-grid' ),
				'2' => esc_html__( '2 Columns', 'the-post-grid' ),
				'3' => esc_html__( '3 Columns', 'the-post-grid' ),
				'4' => esc_html__( '4 Columns', 'the-post-grid' ),
				'5' => esc_html__( '5 Columns', 'the-post-grid' ),
				'6' => esc_html__( '6 Columns', 'the-post-grid' ),
			];
			$grid_column_condition = [
				'slider_layout!' => [ 'slider-layout10', 'slider-layout11', 'slider-layout13' ],
			];
		}

		$ref->add_responsive_control(
			$prefix . '_column',
			[
				'label'          => esc_html__( 'Column', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => $column_options,
				'default'        => '0',
				'tablet_default' => '0',
				'mobile_default' => '0',
				'description'    => esc_html__( 'Choose Column for layout.', 'the-post-grid' ),
				'condition'      => $grid_column_condition,
			]
		);

		if ( 'single' === $layout_type ) {
			$ref->add_control(
				'enable_related_slider',
				[
					'label'        => esc_html__( 'Enable Slider', 'the-post-grid' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
					'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);

			$ref->add_responsive_control(
				'slider_gap_2',
				[
					'label'      => esc_html__( 'Grid Gap', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						],
					],
					'selectors'  => [
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-slider-item'    => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}}; padding-bottom: calc({{SIZE}}{{UNIT}} * 2)',
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'enable_related_slider!' => 'yes',
					],
				]
			);
		}

		$ref->add_responsive_control(
			$prefix . '_offset_col_width',
			[
				'label'      => esc_html__( 'Offset Column Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 30,
						'max'  => 70,
						'step' => 1,
					],
					'%'  => [
						'min'  => 30,
						'max'  => 70,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .offset-left'  => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .tpg-el-main-wrapper .offset-right' => 'width: calc( 100% - {{SIZE}}%);',
				],
				'condition'  => [
					$prefix . '_layout' => [
						'grid-layout5',
						'grid-layout5-2',
						'grid-layout6',
						'grid-layout6-2',
						'grid_hover-layout4',
						'grid_hover-layout4-2',
						'grid_hover-layout5',
						'grid_hover-layout5-2',
						'grid_hover-layout6',
						'grid_hover-layout6-2',
						'grid_hover-layout7',
						'grid_hover-layout7-2',
						'grid_hover-layout9',
						'grid_hover-layout9-2',
					],
				],
			]
		);

		if ( 'grid' === $prefix ) {
			$layout_style_opt = [
				'tpg-even'        => esc_html__( 'Grid', 'the-post-grid' ),
				'tpg-full-height' => esc_html__( 'Grid Equal Height', 'the-post-grid' ),
			];
			if ( rtTPG()->hasPro() ) {
				$layout_style_new_opt = [
					'masonry' => esc_html__( 'Masonry', 'the-post-grid' ),
				];
				$layout_style_opt     = array_merge( $layout_style_opt, $layout_style_new_opt );
			}

			$ref->add_control(
				$prefix . '_layout_style',
				[
					'label'       => esc_html__( 'Layout Style', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'tpg-full-height',
					'options'     => $layout_style_opt,
					'description' => esc_html__( 'If you use card border then equal height will work. ', 'the-post-grid' ) . $ref->get_pro_message( 'masonry layout' ),
					'classes'     => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
					'condition'   => [
						$prefix . '_layout!' => [
							'grid-layout2',
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'grid-layout7',
							'grid-layout7-2',
						],
					],
				]
			);
		}

		if ( ! in_array( $prefix, [ 'slider' ] ) ) {
			$layout_align_css = [
				'{{WRAPPER}} .rt-tpg-container .grid-layout2 .rt-holder .post-right-content' => 'justify-content: {{VALUE}};',
			];

			if ( $prefix === 'grid_hover' ) {
				$layout_align_css = [
					'{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content' => 'justify-content: {{VALUE}};',
				];
			}

			// Grid layout
			$ref->add_control(
				$prefix . '_layout_alignment',
				[
					'label'     => esc_html__( 'Vertical Align', 'the-post-grid' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''              => esc_html__( 'Default', 'the-post-grid' ),
						'flex-start'    => esc_html__( 'Start', 'the-post-grid' ),
						'center'        => esc_html__( 'Center', 'the-post-grid' ),
						'flex-end'      => esc_html__( 'End', 'the-post-grid' ),
						'space-around'  => esc_html__( 'Space Around', 'the-post-grid' ),
						'space-between' => esc_html__( 'Space Between', 'the-post-grid' ),
					],
					'condition' => [
						$prefix . '_layout!' => [
							'grid-layout1',
							'grid-layout3',
							'grid-layout4',
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'grid-layout7',
						],
					],
					'selectors' => $layout_align_css,
				]
			);
		}

		if ( $prefix === 'slider' ) {
			// Grid layout
			$ref->add_control(
				$prefix . '_layout_alignment_2',
				[
					'label'     => esc_html__( 'Vertical Align', 'the-post-grid' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''              => esc_html__( 'Default', 'the-post-grid' ),
						'flex-start'    => esc_html__( 'Start', 'the-post-grid' ),
						'center'        => esc_html__( 'Center', 'the-post-grid' ),
						'flex-end'      => esc_html__( 'End', 'the-post-grid' ),
						'space-around'  => esc_html__( 'Space Around', 'the-post-grid' ),
						'space-between' => esc_html__( 'Space Between', 'the-post-grid' ),
					],
					'condition' => [
						$prefix . '_layout!' => [
							'slider-layout1',
							'slider-layout2',
							'slider-layout3',
							'slider-layout13',
							'slider-layout4',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tpg-el-main-wrapper .grid-behaviour .rt-holder .rt-el-content-wrapper .gallery-content' => 'justify-content: {{VALUE}};height:100%;',
						'{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content'                    => 'justify-content: {{VALUE}};',
					],
				]
			);
		}

		$ref->add_responsive_control(
			'full_wrapper_align',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tpg-wrapper-align-',
				'render_type'  => 'template',
				'toggle'       => true,
				'selectors'    => [
					'{{WRAPPER}} .tpg-post-holder div'               => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .rt-tpg-container .rt-el-post-meta' => 'justify-content: {{VALUE}};',
				],
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],

			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Front-end Filter Settings
	 *
	 * @param $ref
	 */
	public static function filter_settings( $ref ) {
		$prefix = $ref->prefix;

		if ( ! rtTPG()->hasPro() ) {
			return;
		}
		$ref->start_controls_section(
			$prefix . '_filter_settings',
			[
				'label' => esc_html__( 'Filter (Front-end)', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'show_taxonomy_filter',
			[
				'label'        => esc_html__( 'Taxonomy Filter', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_author_filter',
			[
				'label'        => esc_html__( 'Author filter', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_order_by',
			[
				'label'        => esc_html__( 'Order By Filter', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_sort_order',
			[
				'label'        => esc_html__( 'Sort Order Filter', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_search',
			[
				'label'        => esc_html__( 'Search filter', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'search_by',
			[
				'label'     => esc_html__( 'Search By', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'all_content',
				'options'   => [
					'all_content' => esc_html__( 'All Content', 'the-post-grid' ),
					'title'       => esc_html__( 'Title only', 'the-post-grid' ),
				],
				'condition' => [ 'show_search' => 'show' ],
			]
		);

		// TODO: Filter Settings
		// ======================================================

		$front_end_filter_condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'show_taxonomy_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_author_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_order_by',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_sort_order',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_search',
					'operator' => '==',
					'value'    => 'show',
				],
			],
		];

		$ref->add_control(
			'filter_type',
			[
				'label'        => esc_html__( 'Filter Type', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'dropdown',
				'options'      => [
					'dropdown' => esc_html__( 'Dropdown', 'the-post-grid' ),
					'button'   => esc_html__( 'Button', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'tpg-filter-type-',
				'conditions'   => $front_end_filter_condition,
				'separator'    => 'before',
			]
		);

		$ref->add_control(
			'multiple_taxonomy',
			[
				'label'        => esc_html__( 'Multiple Taxonomy', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'description'  => esc_html__( 'You must choose taxonomy terms from the query build for each taxonomy for the multiple taxonomy. Otherwise it won\'t work. ', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'conditions'   => $front_end_filter_condition,
				'condition'    => [
					'show_taxonomy_filter' => 'show',
				],
			]
		);

		$post_types      = Fns::get_post_types();
		$_all_taxonomies = [];

		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );
			if ( empty( $_taxonomies ) ) {
				continue;
			}
			$taxonomies_list = [];
			foreach ( $_taxonomies as $tax ) {
				if ( in_array(
					$tax->name,
					[
						'post_format',
						'elementor_library_type',
						'product_visibility',
						'product_shipping_class',
					]
				) ) {
					continue;
				}
				// if ( in_array( $tax->name, $_all_taxonomies ) ) {
				// continue;
				// }

				$taxonomies_list[ $tax->name ] = $tax->label;
				$_all_taxonomies[]             = $tax->name;
			}

			if ( 'post' === $post_type ) {
				$default_cat = 'category';
			} elseif ( 'product' === $post_type ) {
				$default_cat = 'product_cat';
			} elseif ( 'download' === $post_type ) {
				$default_cat = 'download_category';
			} elseif ( 'docs' === $post_type ) {
				$default_cat = 'doc_category';
			} elseif ( 'lp_course' === $post_type ) {
				$default_cat = 'course_category';
			} else {
				$taxonomie_keys = array_keys( $_taxonomies );
				$filter_cat     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'cat' ) !== false;
					}
				);

				if ( is_array( $filter_cat ) && ! empty( $filter_cat ) ) {
					$default_cat = array_shift( $filter_cat );
				}
			}

			$ref->add_control(
				$post_type . '_filter_taxonomy',
				[
					'label'       => esc_html__( 'Choose Taxonomy', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $default_cat,
					'options'     => $taxonomies_list,
					'condition'   => [
						'post_type'            => $post_type,
						'show_taxonomy_filter' => 'show',
						'multiple_taxonomy!'   => 'yes',
					],
					'description' => esc_html__( 'Select a taxonomy for showing in filter', 'the-post-grid' ),
				]
			);

			$ref->add_control(
				$post_type . '_filter_taxonomies',
				[
					'label'       => esc_html__( 'Choose Taxonomies', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'default'     => $default_cat,
					'options'     => $taxonomies_list,
					'condition'   => [
						'post_type'            => $post_type,
						'show_taxonomy_filter' => 'show',
						'multiple_taxonomy'    => 'yes',
					],
					'description' => esc_html__( 'Select a taxonomies for showing in filter', 'the-post-grid' ),
				]
			);

			foreach ( $_taxonomies as $tax ) {
				if ( in_array(
					$tax->name,
					[
						'post_format',
						'elementor_library_type',
						'product_visibility',
						'product_shipping_class',
					]
				) ) {
					continue;
				}
				// if ( in_array( $tax->name, $_all_taxonomies ) ) {
				// continue;
				// }

				$term_first = [ '0' => esc_html__( '--Select--', 'the-post-grid' ) ];
				$term_lists = get_terms(
					[
						'taxonomy'   => $tax->name, // Custom taxonomy name.
						'hide_empty' => true,
						'fields'     => 'id=>name',
					]
				);

				$term_lists = $term_first + $term_lists;

				$terms_prefix = '';
				if ( 'post' != $post_type ) {
					$terms_prefix = $post_type . '_';
				}
				$terms_prefix .= $tax->name;

				$ref->add_control(
					$terms_prefix . '_default_terms',
					[
						'label'     => esc_html__( 'Default ', 'the-post-grid' ) . $tax->label,
						'type'      => Controls_Manager::SELECT,
						'default'   => '0',
						'options'   => $term_lists,
						'condition' => [
							$post_type . '_filter_taxonomy' => $tax->name,
							'post_type'                     => $post_type,
							'show_taxonomy_filter'          => 'show',
							'multiple_taxonomy!'            => 'yes',
						],
					]
				);
			}
		}

		$ref->add_control(
			'filter_btn_style',
			[
				'label'       => esc_html__( 'Filter Style', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'  => esc_html__( 'Default', 'the-post-grid' ),
					'carousel' => esc_html__( 'Collapsable', 'the-post-grid' ),
				],
				'condition'   => [
					'filter_type'        => 'button',
					'multiple_taxonomy!' => 'yes',
				],
				'conditions'  => $front_end_filter_condition,
				'description' => esc_html__( 'If you use collapsable then only taxonomy section will show on the filter', 'the-post-grid' ),
			]
		);

		$ref->add_responsive_control(
			'filter_btn_item_per_page',
			[
				'label'          => esc_html__( 'Button Item Per Slider', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => [
					'auto' => esc_html__( 'Auto', 'the-post-grid' ),
					'2'    => esc_html__( '2', 'the-post-grid' ),
					'3'    => esc_html__( '3', 'the-post-grid' ),
					'4'    => esc_html__( '4', 'the-post-grid' ),
					'5'    => esc_html__( '5', 'the-post-grid' ),
					'6'    => esc_html__( '6', 'the-post-grid' ),
					'7'    => esc_html__( '7', 'the-post-grid' ),
					'8'    => esc_html__( '8', 'the-post-grid' ),
					'9'    => esc_html__( '9', 'the-post-grid' ),
					'10'   => esc_html__( '10', 'the-post-grid' ),
					'11'   => esc_html__( '11', 'the-post-grid' ),
					'12'   => esc_html__( '12', 'the-post-grid' ),
				],
				'default'        => 'auto',
				'tablet_default' => 'auto',
				'mobile_default' => 'auto',
				'condition'      => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
				'conditions'     => $front_end_filter_condition,
				'description'    => esc_html__( 'If you use carousel then only category section show on the filter', 'the-post-grid' ),
			]
		);

		$front_end_filter_tax_condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'show_taxonomy_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_author_filter',
					'operator' => '==',
					'value'    => 'show',
				],
			],
		];

		$ref->add_control(
			'filter_post_count',
			[
				'label'      => esc_html__( 'Filter Post Count', 'the-post-grid' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'no',
				'options'    => [
					'yes' => esc_html__( 'Yes', 'the-post-grid' ),
					'no'  => esc_html__( 'No', 'the-post-grid' ),
				],
				'conditions' => $front_end_filter_tax_condition,
			]
		);

		$ref->add_control(
			'tgp_filter_taxonomy_hierarchical',
			[
				'label'        => esc_html__( 'Tax Hierarchical', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions'   => $front_end_filter_tax_condition,
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'tpg_hide_all_button',
			[
				'label'        => esc_html__( 'Hide Show all button', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions'   => $front_end_filter_tax_condition,
				'condition'    => [
					'filter_type' => 'button',
				],
			]
		);

		$ref->add_control(
			'custom_taxonomy_order',
			[
				'label'        => esc_html__( 'Taxonomy Order', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'description'  => esc_html__( 'You must sort taxonomy from the dashboard first.', 'the-post-grid' ),
				'condition'    => [
					'show_taxonomy_filter' => 'show',
				],
			]
		);

		$ref->add_control(
			'tax_filter_all_text',
			[
				'label'       => esc_html__( 'All Taxonomy Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter All Category Text Here..', 'the-post-grid' ),
				'conditions'  => $front_end_filter_tax_condition,
			]
		);
		$ref->add_control(
			'author_filter_all_text',
			[
				'label'       => esc_html__( 'All Users Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter All Users Text Here..', 'the-post-grid' ),
				'condition'   => [
					'show_author_filter' => 'show',
					'filter_btn_style'   => 'default',
				],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * List Layout Settings
	 *
	 * @param $ref
	 */
	public static function list_layouts( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'list_layout_settings',
			[
				'label' => esc_html__( 'Layout', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'list_layout',
			[
				'label'          => esc_html__( 'Choose Layout', 'the-post-grid' ),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => true,
				'options'        => [
					'list-layout1'   => [
						'title' => esc_html__( 'Layout 1', 'the-post-grid' ),
					],
					'list-layout2'   => [
						'title' => esc_html__( 'Layout 2', 'the-post-grid' ),
					],
					'list-layout2-2' => [
						'title' => esc_html__( 'Layout 3', 'the-post-grid' ),
					],
					'list-layout3'   => [
						'title' => esc_html__( 'Layout 4', 'the-post-grid' ),
					],
					'list-layout3-2' => [
						'title' => esc_html__( 'Layout 5', 'the-post-grid' ),
					],
					'list-layout4'   => [
						'title' => esc_html__( 'Layout 6', 'the-post-grid' ),
					],
					'list-layout5'   => [
						'title' => esc_html__( 'Layout 7', 'the-post-grid' ),
					],
				],
				'toggle'         => false,
				'default'        => 'list-layout1',
				'style_transfer' => true,
				'classes'        => 'tpg-image-select list-layout ' . $ref->is_post_layout,
			]
		);

		$ref->add_control(
			'layout_options_heading2',
			[
				'label'   => esc_html__( 'Layout Options:', 'the-post-grid' ),
				'type'    => Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$ref->add_responsive_control(
			'list_column',
			[
				'label'          => esc_html__( 'Column', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => [
					'0'  => esc_html__( 'Default from layout', 'the-post-grid' ),
					'12' => esc_html__( '1 Columns', 'the-post-grid' ),
					'6'  => esc_html__( '2 Columns', 'the-post-grid' ),
					'4'  => esc_html__( '3 Columns', 'the-post-grid' ),
					'3'  => esc_html__( '4 Columns', 'the-post-grid' ),
				],
				'default'        => '0',
				'tablet_default' => '0',
				'mobile_default' => '0',
				'description'    => esc_html__( 'Choose Column for layout', 'the-post-grid' ),
				'condition'      => [
					'list_layout!' => [
						'list-layout2',
						'list-layout2-2',
						'list-layout3',
						'list-layout3-2',
						'list-layout4',
					],
				],
			]
		);

		$ref->add_responsive_control(
			'list_layout_alignment',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''              => esc_html__( 'Default', 'the-post-grid' ),
					'flex-start'    => esc_html__( 'Start', 'the-post-grid' ),
					'center'        => esc_html__( 'Center', 'the-post-grid' ),
					'flex-end'      => esc_html__( 'End', 'the-post-grid' ),
					'space-around'  => esc_html__( 'Space Around', 'the-post-grid' ),
					'space-between' => esc_html__( 'Space Between', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .list-behaviour .rt-holder .rt-el-content-wrapper' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'list_layout!' => [ 'list-layout2', 'list-layout2-2' ],
				],
			]
		);

		$ref->add_control(
			'list_flex_direction',
			[
				'label'     => esc_html__( 'Flex Direction', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''               => esc_html__( 'Default', 'the-post-grid' ),
					'row-reverse'    => esc_html__( 'Row Reverse', 'the-post-grid' ),
					'column'         => esc_html__( 'Column', 'the-post-grid' ),
					'column-reverse' => esc_html__( 'Column Reverse', 'the-post-grid' ),
				],
				'condition' => [
					'list_layout' => [
						'list-layout1',
						'list-layout5',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .list-behaviour .rt-holder .rt-el-content-wrapper' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$ref->add_responsive_control(
			'list_left_side_width',
			[
				'label'      => esc_html__( 'Offset Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 700,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper .offset-left'  => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper .offset-right' => 'width: calc( 100% - {{SIZE}}%);',
				],
				'condition'  => [
					'list_layout' => [ 'list-layout2', 'list-layout3', 'list-layout2-2', 'list-layout3-2' ],
				],
			]
		);

		$layout_style_opt = [
			// translators: %s represents the prefix.
			'tpg-even' => sprintf( esc_html__( '%s Default', 'the-post-grid' ), ucwords( $ref->prefix ) ),
		];
		if ( rtTPG()->hasPro() ) {
			$layout_style_new_opt = [
				'masonry' => esc_html__( 'Masonry', 'the-post-grid' ),
			];
			$layout_style_opt     = array_merge( $layout_style_opt, $layout_style_new_opt );
		}

		$ref->add_control(
			'list_layout_style',
			[
				'label'       => esc_html__( 'Layout Style', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'tpg-even',
				'options'     => $layout_style_opt,
				'description' => $ref->get_pro_message( 'masonry layout' ),
				'condition'   => [
					'list_layout'  => [ 'list-layout1', 'list-layout5' ],
					'list_column!' => [ '0', '12' ],
				],
			]
		);

		$ref->add_responsive_control(
			'full_wrapper_align',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'    => [
					'{{WRAPPER}} .tpg-post-holder div' => 'text-align: {{VALUE}};',
				],
				'render_type'  => 'template',
				'prefix_class' => 'tpg-wrapper-align-',
				'toggle'       => true,
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Pagination and Load more style tab
	 *
	 * @param        $ref
	 * @param bool $is_print
	 */
	public static function pagination_settings( $ref, $layout_type = '' ) {
		$ref->start_controls_section(
			'pagination_settings',
			[
				'label' => esc_html__( 'Pagination', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'show_pagination',
			[
				'label'        => esc_html__( 'Show Pagination', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'default',
				'render_type'  => 'template',
				// 'prefix_class' => 'pagination-visibility-',
			]
		);

		$default_pagination = 'pagination';
		if ( 'archive' == $layout_type ) {
			$pagination_type    = [];
			$default_pagination = 'pagination_ajax';
		} else {
			$pagination_type = [
				'pagination' => esc_html__( 'Default Pagination', 'the-post-grid' ),
			];
		}

		if ( rtTPG()->hasPro() ) {
			$pagination_type_pro = [
				'pagination_ajax' => esc_html__( 'Ajax Pagination ( Only for Grid )', 'the-post-grid' ),
				'load_more'       => esc_html__( 'Load More - On Click', 'the-post-grid' ),
				'load_on_scroll'  => esc_html__( 'Load On Scroll', 'the-post-grid' ),
			];
			$pagination_type     = array_merge( $pagination_type, $pagination_type_pro );
		}

		$ref->add_control(
			'pagination_type',
			[
				'label'       => esc_html__( 'Pagination Type', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => $default_pagination,
				'options'     => $pagination_type,
				'description' => $ref->get_pro_message( 'loadmore and ajax pagination' ),
				'condition'   => [
					'show_pagination' => 'show',
				],
			]
		);

		$ref->add_control(
			'ajax_pagination_type',
			[
				'label'        => esc_html__( 'Enable Ajax Next Previous', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'condition'    => [
					'pagination_type' => 'pagination_ajax',
					'show_pagination' => 'show',
				],
				'prefix_class' => 'ajax-pagination-type-next-prev-',
			]
		);

		$ref->add_control(
			'load_more_button_text',
			[
				'label'     => esc_html__( 'Button Text', 'the-post-grid' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Load More', 'the-post-grid' ),
				'condition' => [
					'pagination_type' => 'load_more',
					'show_pagination' => 'show',
				],
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Get Field Selections
	 *
	 * @param $ref
	 */
	public static function field_selection( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'field_selection_settings',
			[
				'label' => esc_html__( 'Field Selection', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$ref->add_control(
			'show_section_title',
			[
				'label'        => esc_html__( 'Section Title', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				// 'prefix_class' => 'section-title-visibility-',
			]
		);

		$ref->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Post Title', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				// 'prefix_class' => 'title-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],

			]
		);

		$ref->add_control(
			'show_thumb',
			[
				'label'        => esc_html__( 'Post Thumbnail', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				// 'prefix_class' => 'thumbnail-visibility-',
			]
		);

		$ref->add_control(
			'show_excerpt',
			[
				'label'        => esc_html__( 'Post Excerpt', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				// 'prefix_class' => 'excerpt-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_meta',
			[
				'label'        => esc_html__( 'Meta Data', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'meta-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_date',
			[
				'label'        => esc_html__( 'Post Date', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'classes'      => 'tpg-padding-left',
				// 'prefix_class' => 'date-visibility-',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_category',
			[
				'label'        => esc_html__( 'Post Categories', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'classes'      => 'tpg-padding-left',
				// 'prefix_class' => 'category-visibility-',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_author',
			[
				'label'        => esc_html__( 'Post Author', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_tags',
			[
				'label'        => esc_html__( 'Post Tags', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_comment_count',
			[
				'label'        => esc_html__( 'Post Comment Count', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_post_count',
			[
				'label'        => esc_html__( 'Post View Count', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => rtTPG()->hasPro() ? 'tpg-padding-left' : 'the-post-grid-field-hide tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_read_more',
			[
				'label'        => esc_html__( 'Read More Button', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'show_social_share',
			[
				'label'        => esc_html__( 'Social Share', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'default',
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		if ( Fns::is_woocommerce() ) {
			$ref->add_control(
				'show_woocommerce_rating',
				[
					'label'        => __( 'Rating (WooCommerce)', 'the-post-grid' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'the-post-grid' ),
					'label_off'    => __( 'Hide', 'the-post-grid' ),
					'return_value' => 'show',
					'default'      => 'default',
					'condition'    => [
						'post_type' => [ 'product', 'download' ],
					],
				]
			);
		}

		$cf = Fns::is_acf();
		if ( $cf ) {
			$ref->add_control(
				'show_acf',
				[
					'label'        => esc_html__( 'Advanced Custom Field', 'the-post-grid' ) . $ref->pro_label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
					'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
					'return_value' => 'show',
					'default'      => false,
					'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
					'condition'    => [
						$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
					],
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Section Title Settings
	 *
	 * @param $ref
	 */
	public static function section_title_settings( $ref, $layout_type = '' ) {
		$default = ( 'single' == $layout_type ) ? esc_html__( 'Related Posts', 'the-post-grid' ) : esc_html__( 'Section Title', 'the-post-grid' );
		$ref->start_controls_section(
			'section_title_settings',
			[
				'label'     => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);

		$ref->add_control(
			'section_title_style',
			[
				'label'        => esc_html__( 'Section Title Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'style1',
				'options'      => [
					'default' => esc_html__( 'Default - Text', 'the-post-grid' ),
					'style1'  => esc_html__( 'Style 1 - Dot & Border', 'the-post-grid' ),
					'style2'  => esc_html__( 'Style 2 - BG & Border', 'the-post-grid' ),
					'style3'  => esc_html__( 'Style 3 - BG & Border - 2', 'the-post-grid' ),
					'style4'  => esc_html__( 'Style 4 - Border Bottom', 'the-post-grid' ),
				],
				'prefix_class' => 'section-title-style-',
				'render_type'  => 'template',
				'condition'    => [
					'show_section_title' => 'show',
				],
			]
		);

		if ( 'single' === $layout_type ) {
			$ref->add_control(
				'section_title_source',
				[
					'label'   => esc_html__( 'Title source', 'the-post-grid' ),
					'type'    => Controls_Manager::HIDDEN,
					'default' => 'custom_title',
				]
			);
		} else {
			$ref->add_control(
				'section_title_source',
				[
					'label'     => esc_html__( 'Title Source', 'the-post-grid' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'custom_title',
					'options'   => [
						'page_title'   => esc_html__( 'Page Title', 'the-post-grid' ),
						'custom_title' => esc_html__( 'Custom Title', 'the-post-grid' ),
					],
					'condition' => [
						'show_section_title' => 'show',
					],
				]
			);
		}

		$ref->add_control(
			'section_title_text',
			[
				'label'       => esc_html__( 'Title', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'the-post-grid' ),
				'default'     => $default,
				'label_block' => true,
				'condition'   => [
					'section_title_source' => 'custom_title',
					'show_section_title'   => 'show',
				],
			]
		);

		$ref->add_control(
			'title_prefix',
			[
				'label'       => esc_html__( 'Title Prefix Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title prefix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$ref->add_control(
			'title_suffix',
			[
				'label'       => esc_html__( 'Title Suffix Text', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title suffix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$ref->add_control(
			'section_title_tag',
			[
				'label'     => esc_html__( 'Title Tag', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => [
					'h1' => esc_html__( 'H1', 'the-post-grid' ),
					'h2' => esc_html__( 'H2', 'the-post-grid' ),
					'h3' => esc_html__( 'H3', 'the-post-grid' ),
					'h4' => esc_html__( 'H4', 'the-post-grid' ),
					'h5' => esc_html__( 'H5', 'the-post-grid' ),
					'h6' => esc_html__( 'H6', 'the-post-grid' ),
				],
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);

		$ref->add_control(
			'enable_external_link',
			[
				'label'        => esc_html__( 'Enable External Link', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
			]
		);

		$ref->add_control(
			'section_external_url',
			[
				'label'       => esc_html__( 'External Link', 'the-post-grid' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'the-post-grid' ),
				'options'     => [ 'url', 'is_external', 'nofollow' ],
				'default'     => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				],
				'label_block' => true,
				'condition'   => [
					'enable_external_link' => 'show',
				],
			]
		);

		$ref->add_control(
			'section_external_text',
			[
				'label'     => esc_html__( 'Link Text', 'the-post-grid' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'See More', 'the-post-grid' ),
				'condition' => [
					'enable_external_link' => 'show',
				],
			]
		);

		if ( 'archive' == $layout_type ) {
			$ref->add_control(
				'show_cat_desc',
				[
					'label'        => esc_html__( 'Show Archive Description', 'the-post-grid' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
					'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => false,
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Thumbnail Settings
	 *
	 * @param $ref
	 */
	public static function post_thumbnail_settings( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'post_thumbnail_settings',
			[
				'label'     => esc_html__( 'Thumbnail', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_thumb' => 'show',
				],
			]
		);

		$ref->add_control(
			'media_source',
			[
				'label'   => esc_html__( 'Media Source', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'feature_image',
				'options' => [
					'feature_image' => esc_html__( 'Feature Image', 'the-post-grid' ),
					'first_image'   => esc_html__( 'First Image from content', 'the-post-grid' ),
				],
			]
		);

		$thumb_exclude = '';
		if ( ! rtTPG()->hasPro() ) {
			$thumb_exclude = 'custom';
		}

		// Default Image
		$ref->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'exclude'   => [ $thumb_exclude ],
				'default'   => 'medium_large',
				'label'     => $ref->get_pro_message( 'custom dimension.' ),
				'condition' => [
					'media_source' => 'feature_image',
				],
			]
		);

		$ref->add_control(
			'img_crop_style',
			[
				'label'     => esc_html__( 'Image Crop Style', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hard',
				'options'   => [
					'soft' => esc_html__( 'Soft Crop', 'the-post-grid' ),
					'hard' => esc_html__( 'Hard Crop', 'the-post-grid' ),
				],
				'condition' => [
					'image_size'   => 'custom',
					'media_source' => 'feature_image',
				],
			]
		);

		$thumb_condition = [
			'media_source' => 'feature_image',
			'grid_layout'  => [ 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2' ],
		];

		if ( $ref->prefix === 'list' ) {
			$thumb_condition = [
				'media_source' => 'feature_image',
				'list_layout'  => [ 'list-layout2', 'list-layout3', 'list-layout2-2', 'list-layout3-2' ],
			];
		}

		if ( $ref->prefix === 'grid_hover' ) {
			$thumb_condition = [
				'media_source'      => 'feature_image',
				'grid_hover_layout' => [
					'grid_hover-layout4',
					'grid_hover-layout4-2',
					'grid_hover-layout5',
					'grid_hover-layout5-2',
					'grid_hover-layout6',
					'grid_hover-layout6-2',
					'grid_hover-layout7',
					'grid_hover-layout7-2',
					'grid_hover-layout9',
					'grid_hover-layout9-2',
				],
			];
		}
		if ( $ref->prefix === 'slider' ) {
			$thumb_condition = [
				'media_source'  => 'feature_image',
				'slider_layout' => [ 'slider-layout10' ],
			];
		}

		// Offset Image
		$ref->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image_offset',
				'exclude'   => [ 'custom' ],
				'default'   => 'medium_large',
				'condition' => $thumb_condition,
				'classes'   => 'tpg-offset-thumb-size',
			]
		);

		if ( 'list' == $prefix ) {
			$ref->add_responsive_control(
				'list_image_side_width',
				[
					'label'      => esc_html__( 'List Image Width', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range'      => [
						'px' => [
							'min'  => 0,
							'max'  => 700,
							'step' => 5,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper [class*="rt-col"]:not(.offset-left) .rt-holder .tpg-el-image-wrap' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'list_layout!' => [ 'list-layout4' ],
					],
				]
			);
		}

		if ( rtTPG()->hasPro() ) {
			$ref->add_responsive_control(
				'image_height',
				[
					'label'      => esc_html__( 'Image Height', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range'      => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap'                => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap img'            => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper.slider-layout11-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper' => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper.slider-layout12-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$ref->add_responsive_control(
				'offset_image_height',
				[
					'label'      => esc_html__( 'Offset Image Height', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range'      => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						],
					],
					'condition'  => $thumb_condition,
					'selectors'  => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap'     => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap img' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$ref->add_control(
			'hover_animation',
			[
				'label'        => esc_html__( 'Image Hover Animation', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'        => esc_html__( 'Default', 'the-post-grid' ),
					'img_zoom_in'    => esc_html__( 'Zoom In', 'the-post-grid' ),
					'img_zoom_out'   => esc_html__( 'Zoom Out', 'the-post-grid' ),
					'slide_to_right' => esc_html__( 'Slide to Right', 'the-post-grid' ),
					'slide_to_left'  => esc_html__( 'Slide to Left', 'the-post-grid' ),
					'img_no_effect'  => esc_html__( 'None', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'img_hover_animation_',
			]
		);

		$ref->add_control(
			'is_thumb_lightbox',
			[
				'label'   => esc_html__( 'Light Box', 'the-post-grid' ) . $ref->pro_label,
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'show'    => esc_html__( 'Show', 'the-post-grid' ),
					'hide'    => esc_html__( 'Hide', 'the-post-grid' ),
				],
				'classes' => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'light_box_icon',
			[
				'label'     => esc_html__( 'Light Box Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'is_default_img',
			[
				'label'        => esc_html__( 'Enable Default Image', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'default_image',
			[
				'label'     => esc_html__( 'Default Image', 'the-post-grid' ) . $ref->pro_label,
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => rtTPG()->get_assets_uri( 'images/placeholder.jpg' ),
				],
				'condition' => [
					'is_default_img' => 'yes',
				],
				'classes'   => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Post Title Settings
	 *
	 * @param $ref
	 */
	public static function post_title_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_title_settings',
			[
				'label'     => esc_html__( 'Post Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_title'         => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Title Tag', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => esc_html__( 'H1', 'the-post-grid' ),
					'h2' => esc_html__( 'H2', 'the-post-grid' ),
					'h3' => esc_html__( 'H3', 'the-post-grid' ),
					'h4' => esc_html__( 'H4', 'the-post-grid' ),
					'h5' => esc_html__( 'H5', 'the-post-grid' ),
					'h6' => esc_html__( 'H6', 'the-post-grid' ),
				],
			]
		);

		$title_position = [
			'default' => esc_html__( 'Default', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$title_position_pro = [
				'above_image' => esc_html__( 'Above Image', 'the-post-grid' ),
				'below_image' => esc_html__( 'Below Image', 'the-post-grid' ),
			];
			$title_position     = array_merge( $title_position, $title_position_pro );
		}

		$ref->add_control(
			'title_position',
			[
				'label'        => esc_html__( 'Title Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'title_position_',
				'render_type'  => 'template',
				'classes'      => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
				'options'      => $title_position,
				'description'  => $ref->get_pro_message( 'more position (above/below image)' ),
				'condition'    => [
					$prefix . '_layout' => [
						'grid-layout1',
						'grid-layout2',
						'grid-layout3',
						'grid-layout4',
						'slider-layout1',
						'slider-layout2',
						'slider-layout3',
					],
				],
			]
		);

		$ref->add_control(
			'title_position_hidden',
			[
				'label'        => esc_html__( 'Title Position', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'title_position_',
				'render_type'  => 'template',
				'classes'      => 'tpg-should-hide-field',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
				],
				'condition'    => [
					$prefix . '_layout!' => [
						'grid-layout1',
						'grid-layout2',
						'grid-layout3',
						'grid-layout4',
						'slider-layout1',
						'slider-layout2',
						'slider-layout3',
					],
				],
			]
		);

		$ref->add_control(
			'title_hover_underline',
			[
				'label'        => esc_html__( 'Title Hover Underline', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'title_hover_border_',
				'render_type'  => 'template',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'enable'  => esc_html__( 'Enable', 'the-post-grid' ),
					'disable' => esc_html__( 'Disable', 'the-post-grid' ),
				],
			]
		);

		$ref->add_control(
			'title_visibility_style',
			[
				'label'        => esc_html__( 'Title Visibility Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => esc_html__( 'Default', 'the-post-grid' ),
					'one-line'   => esc_html__( 'Show in 1 line', 'the-post-grid' ),
					'two-line'   => esc_html__( 'Show in 2 lines', 'the-post-grid' ),
					'three-line' => esc_html__( 'Show in 3 lines', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'title-',
			]
		);

		$ref->add_control(
			'title_limit',
			[
				'label' => esc_html__( 'Title Length', 'the-post-grid' ),
				'type'  => Controls_Manager::NUMBER,
				'step'  => 1,
			]
		);

		$ref->add_control(
			'title_limit_type',
			[
				'label'   => esc_html__( 'Title Crop by', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'word',
				'options' => [
					'word'      => esc_html__( 'Words', 'the-post-grid' ),
					'character' => esc_html__( 'Characters', 'the-post-grid' ),
				],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Post Excerpt Settings
	 *
	 * @param $ref
	 */
	public static function post_excerpt_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_excerpt_settings',
			[
				'label'     => esc_html__( 'Excerpt / Content', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_excerpt'       => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$excerpt_type = [
			'character' => esc_html__( 'Character', 'the-post-grid' ),
			'word'      => esc_html__( 'Word', 'the-post-grid' ),
		];

		if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
			$excerpt_type['full'] = esc_html__( 'Full Content', 'the-post-grid' );
		}

		$ref->add_control(
			'excerpt_type',
			[
				'label'   => esc_html__( 'Excerpt Type', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'character',
				'options' => $excerpt_type,
			]
		);

		$default_excerpt_limit = 100;
		if ( 'grid' == $prefix ) {
			$default_excerpt_limit = 200;
		}

		$ref->add_control(
			'excerpt_limit',
			[
				'label'     => esc_html__( 'Excerpt Limit', 'the-post-grid' ),
				'type'      => Controls_Manager::NUMBER,
				'step'      => 1,
				'default'   => $default_excerpt_limit,
				'condition' => [
					'excerpt_type' => [ 'character', 'word' ],
				],
			]
		);

		$ref->add_control(
			'excerpt_more_text',
			[
				'label'     => esc_html__( 'Expansion Indicator', 'the-post-grid' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '...',
				'condition' => [
					'excerpt_type' => [ 'character', 'word' ],
				],
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Post Meta Settings
	 *
	 * @param $ref
	 */
	public static function post_meta_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_meta_settings',
			[
				'label'      => esc_html__( 'Meta Data', 'the-post-grid' ),
				'tab'        => Controls_Manager::TAB_SETTINGS,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_category',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_author',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_tags',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_comment_count',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_post_count',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
				'condition'  => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$meta_position = [
			'default' => esc_html__( 'Default', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$meta_position_pro = [
				'above_title'   => esc_html__( 'Above Title', 'the-post-grid' ),
				'below_title'   => esc_html__( 'Below Title', 'the-post-grid' ),
				'above_excerpt' => esc_html__( 'Above excerpt', 'the-post-grid' ),
				'below_excerpt' => esc_html__( 'Below excerpt', 'the-post-grid' ),
			];
			$meta_position     = array_merge( $meta_position, $meta_position_pro );
		}

		$ref->add_control(
			'meta_position',
			[
				'label'        => esc_html__( 'Meta Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'meta_position_',
				'render_type'  => 'template',
				'options'      => $meta_position,
				'classes'      => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
			]
		);

		$ref->add_control(
			'show_meta_icon',
			[
				'label'        => esc_html__( 'Show Meta Icon', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'meta_separator',
			[
				'label'   => esc_html__( 'Meta Separator', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default - None', 'the-post-grid' ),
					'.'       => esc_html__( 'Dot ( . )', 'the-post-grid' ),
					'/'       => esc_html__( 'Single Slash ( / )', 'the-post-grid' ),
					'//'      => esc_html__( 'Double Slash ( // )', 'the-post-grid' ),
					'-'       => esc_html__( 'Hyphen ( - )', 'the-post-grid' ),
					'|'       => esc_html__( 'Vertical Pipe ( | )', 'the-post-grid' ),
				],
			]
		);

		$ref->add_control(
			'meta_popover_toggle',
			[
				'label'        => esc_html__( 'Change Meta Icon', 'the-post-grid' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'the-post-grid' ),
				'label_on'     => esc_html__( 'Custom', 'the-post-grid' ),
				'return_value' => 'yes',
				'condition'    => [
					'show_meta_icon' => 'yes',
				],
			]
		);

		$ref->start_popover();

		$ref->add_control(
			'user_icon',
			[
				'label'     => esc_html__( 'Author Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_author_image!' => 'icon',
				],
			]
		);

		$ref->add_control(
			'cat_icon',
			[
				'label'     => esc_html__( 'Category Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_category' => 'show',
				],
			]
		);

		$ref->add_control(
			'date_icon',
			[
				'label'     => esc_html__( 'Date Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_date' => 'show',
				],
			]
		);

		$ref->add_control(
			'tag_icon',
			[
				'label'     => esc_html__( 'Tags Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_tags' => 'show',
				],
			]
		);

		$ref->add_control(
			'comment_icon',
			[
				'label'     => esc_html__( 'Comment Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'post_count_icon',
			[
				'label'     => esc_html__( 'Post Count Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_post_count' => 'show',
				],
			]
		);

		$ref->end_popover();

		/**
		 * TODO: Author Style
		 * ********************
		 */

		$ref->add_control(
			'meta_author_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'meta_author_heading',
			[
				'label'     => esc_html__( 'Author Setting:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'author_prefix',
			[
				'label'       => esc_html__( 'Author Prefix', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'By',
				'placeholder' => esc_html__( 'By', 'the-post-grid' ),
				'condition'   => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'author_icon_visibility',
			[
				'label'        => esc_html__( 'Author Icon Visibility', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'show',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'hide'    => esc_html__( 'Hide', 'the-post-grid' ),
					'show'    => esc_html__( 'Show', 'the-post-grid' ),
				],
				'condition'    => [
					'show_author!' => '',
				],
				'prefix_class' => 'tpg-is-author-icon-',
			]
		);

		$ref->add_control(
			'show_author_image',
			[
				'label'        => esc_html__( 'Author Image / Icon', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'icon',
				'options'      => [
					'image' => esc_html__( 'Image', 'the-post-grid' ),
					'icon'  => esc_html__( 'Icon', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'author-image-visibility-',
				'condition'    => [
					'show_author!'            => '',
					'author_icon_visibility!' => 'hide',
					$prefix . '_layout!'      => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_responsive_control(
			'author_icon_width',
			[
				'label'      => esc_html__( 'Author Image Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags span img' => 'width: {{SIZE}}{{UNIT}} !important;max-width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'show_author!'            => '',
					'author_icon_visibility!' => 'hide',
					'show_author_image!'      => 'icon',
				],
			]
		);

		/**
		 * TODO: Category Style
		 * ********************
		 */

		$ref->add_control(
			'category_heading',
			[
				'label'      => esc_html__( 'Category and Tags Setting:', 'the-post-grid' ),
				'type'       => Controls_Manager::HEADING,
				'classes'    => 'tpg-control-type-heading',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_category',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_tags',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
			]
		);

		$ref->add_control(
			'category_position',
			[
				'label'        => esc_html__( 'Category Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'      => esc_html__( 'Default', 'the-post-grid' ),
					'above_title'  => esc_html__( 'Above Title', 'the-post-grid' ),
					'with_meta'    => esc_html__( 'With Meta', 'the-post-grid' ),
					'top_left'     => esc_html__( 'Over image (Top Left)', 'the-post-grid' ),
					'top_right'    => esc_html__( 'Over image (Top Right)', 'the-post-grid' ),
					'bottom_left'  => esc_html__( 'Over image (Bottom Left)', 'the-post-grid' ),
					'bottom_right' => esc_html__( 'Over image (Bottom Right)', 'the-post-grid' ),
					'image_center' => esc_html__( 'Over image (Center)', 'the-post-grid' ),
				],
				'condition'    => [
					'show_category' => 'show',
				],
				'render_type'  => 'template',
				'divider'      => 'before',
				'prefix_class' => 'tpg-category-position-',
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$category_style_condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'category_position',
					'operator' => '!=',
					'value'    => 'default',
				],
				[
					'name'     => $prefix . '_layout',
					'operator' => 'in',
					'value'    => [ 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2' ],
				],
			],
		];

		$ref->add_control(
			'category_style',
			[
				'label'        => esc_html__( 'Category Style', 'the-post-grid' ) . $ref->pro_label,
				'type'         => Controls_Manager::SELECT,
				'default'      => 'style1',
				'options'      => [
					'style1' => esc_html__( 'Style 1 - Only Text', 'the-post-grid' ),
					'style2' => esc_html__( 'Style 2 - Background', 'the-post-grid' ),
					'style3' => esc_html__( 'Style 3 - Fold edge', 'the-post-grid' ),
					'style4' => esc_html__( 'Style 4 - Different Color', 'the-post-grid' ),
				],
				'prefix_class' => 'tpg-cat-',
				'render_type'  => 'template',
				'description'  => rtTPG()->hasPro() ? esc_html( 'Different background color will work if you use style 1 and 2' ) : '',
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'conditions'   => $category_style_condition,
			]
		);

		if ( rtTPG()->hasPro() ) {
			$ref->add_control(
				'important_note',
				[
					'raw'  => esc_html__( 'NB. If you use different background color for category then please choose style 2 or 3 from above', 'the-post-grid' ),
					'type' => Controls_Manager::RAW_HTML,
				]
			);
		}

		if ( rtTPG()->hasPro() ) {
			$ref->add_control(
				'show_cat_icon',
				[
					'label'        => esc_html__( 'Show Over Image Category Icon', 'the-post-grid' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
					'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => false,
					'conditions'   => $category_style_condition,
				]
			);
		}

		$post_types      = Fns::get_post_types();
		$_all_taxonomies = [];
		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );
			if ( empty( $_taxonomies ) ) {
				continue;
			}
			$term_options = [];
			foreach ( $_taxonomies as $tax ) {
				if ( 'post_format' == $tax->name ) {
					continue;
				}
				// if ( in_array( $tax->name, $_all_taxonomies ) ) {
				// continue;
				// }
				$_all_taxonomies[]          = $tax->name;
				$term_options[ $tax->name ] = $tax->label;
			}

			if ( 'post' === $post_type ) {
				$default_cat = 'category';
				$default_tag = 'post_tag';
			} elseif ( 'product' === $post_type ) {
				$default_cat = 'product_cat';
				$default_tag = 'product_tag';
			} elseif ( 'download' === $post_type ) {
				$default_cat = 'download_category';
				$default_tag = 'download_tag';
			} elseif ( 'docs' === $post_type ) {
				$default_cat = 'doc_category';
				$default_tag = 'doc_tag';
			} elseif ( 'lp_course' === $post_type ) {
				$default_cat = 'course_category';
				$default_tag = 'course_tag';
			} else {
				$taxonomie_keys = array_keys( $_taxonomies );
				$filter_cat     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'cat' ) !== false;
					}
				);
				$filter_tag     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'tag' ) !== false;
					}
				);

				if ( is_array( $filter_cat ) && ! empty( $filter_cat ) ) {
					$default_cat = array_shift( $filter_cat );
				}
				if ( is_array( $filter_tag ) && ! empty( $filter_tag ) ) {
					$default_tag = array_shift( $filter_tag );
				}
			}

			$ref->add_control(
				$post_type . '_taxonomy',
				[
					'label'       => esc_html__( 'Category Source', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $default_cat,
					'options'     => $term_options,
					'condition'   => [
						'show_category' => 'show',
						'post_type'     => $post_type,
					],
					'description' => esc_html__( 'Select which taxonomy should sit in the place of categories. Default: Category', 'the-post-grid' ),
				]
			);

			$ref->add_control(
				$post_type . '_tags',
				[
					'label'       => esc_html__( 'Tags Source', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $default_tag,
					'options'     => $term_options,
					'condition'   => [
						'show_category' => 'show',
						'post_type'     => $post_type,
					],
					'description' => esc_html__( 'Select which taxonomy should sit in the place of tags. Default: Tags', 'the-post-grid' ),
				]
			);
		}

		$ref->add_control(
			'comment_count_heading',
			[
				'label'     => esc_html__( 'Comment Count ', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'show_comment_count_label',
			[
				'label'        => esc_html__( 'Show Comment Label', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'comment_count_label_singular',
			[
				'label'       => esc_html__( 'Comment Label Singular', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Comment', 'the-post-grid' ),
				'placeholder' => esc_html__( 'Type your title here', 'the-post-grid' ),
				'condition'   => [
					'show_comment_count'       => 'show',
					'show_comment_count_label' => 'yes',
				],
			]
		);

		$ref->add_control(
			'comment_count_label_plural',
			[
				'label'       => esc_html__( 'Comment Label Plural', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Comments', 'the-post-grid' ),
				'placeholder' => esc_html__( 'Type your title here', 'the-post-grid' ),
				'condition'   => [
					'show_comment_count'       => 'show',
					'show_comment_count_label' => 'yes',
				],
			]
		);

		$ref->add_control(
			'meta_ordering_heading',
			[
				'label'   => esc_html__( 'Meta Ordering', 'the-post-grid' ),
				'type'    => Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'repeater_hidden',
			[
				'type' => Controls_Manager::HIDDEN,
			]
		);

		$ref->add_control(
			'meta_ordering',
			[
				'label'       => esc_html__( 'Meta Ordering (Drag and Drop)', 'the-post-grid' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'meta_title' => esc_html__( 'Author', 'the-post-grid' ),
						'meta_name'  => 'author',
					],
					[
						'meta_title' => esc_html__( 'Date', 'the-post-grid' ),
						'meta_name'  => 'date',
					],
					[
						'meta_title' => esc_html__( 'Category', 'the-post-grid' ),
						'meta_name'  => 'category',
					],
					[
						'meta_title' => esc_html__( 'Tags', 'the-post-grid' ),
						'meta_name'  => 'tags',
					],
					[
						'meta_title' => esc_html__( 'Comment Count', 'the-post-grid' ),
						'meta_name'  => 'comment_count',
					],
					[
						'meta_title' => esc_html__( 'Post Count', 'the-post-grid' ),
						'meta_name'  => 'post_count',
					],
					[
						'meta_title' => esc_html__( 'Post Like', 'the-post-grid' ),
						'meta_name'  => 'post_like',
					],
				],
				'classes'     => 'tpg-item-order-repeater',
				'title_field' => '{{{ meta_title }}}',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Read More Settings
	 *
	 * @param $ref
	 */
	public static function post_readmore_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_readmore_settings',
			[
				'label'     => esc_html__( 'Read More', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_read_more'     => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_control(
			'readmore_btn_style',
			[
				'label'        => esc_html__( 'Button Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default-style',
				'options'      => [
					'default-style' => esc_html__( 'Default from style', 'the-post-grid' ),
					'only-text'     => esc_html__( 'Only Text Button', 'the-post-grid' ),

				],
				'prefix_class' => 'readmore-btn-',
			]
		);

		$ref->add_control(
			'read_more_label',
			[
				'label'       => esc_html__( 'Read More Label', 'the-post-grid' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'the-post-grid' ),
				'placeholder' => esc_html__( 'Type Read More Label here', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'show_btn_icon',
			[
				'label'        => esc_html__( 'Show Button Icon', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
			]
		);

		$ref->add_control(
			'readmore_btn_icon',
			[
				'label'     => esc_html__( 'Choose Icon', 'the-post-grid' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_position',
			[
				'label'     => esc_html__( 'Icon Position', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => esc_html__( 'Left', 'the-post-grid' ),
					'right' => esc_html__( 'Right', 'the-post-grid' ),
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 *  Advanced Custom Field ACF Settings
	 *
	 * @param $ref
	 */
	public static function tpg_acf_settings( $ref ) {
		$prefix = $ref->prefix;
		$cf     = Fns::is_acf();

		if ( ! $cf || ! rtTPG()->hasPro() ) {
			return;
		}

		$ref->start_controls_section(
			'tgp_acf_settings',
			[
				'label'     => esc_html__( 'ACF Settings', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_acf'           => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		self::get_tpg_acf_settings( $ref );

		$ref->end_controls_section();
	}

	public static function get_tpg_acf_settings( $ref, $is_archive = false ) {

		$post_types = Fns::get_post_types();

		if ( $is_archive ) {
			$get_acf_field   = Fns::get_groups_by_post_type( 'post' );
			$selected_acf_id = '';
			if ( ! empty( $get_acf_field ) && is_array( $get_acf_field ) ) {
				$selected_acf_id = array_key_first( $get_acf_field );
			}

			$ref->add_control(
				'cf_group',
				[
					'label'       => esc_html__( 'Choose Advanced Custom Field (ACF)', 'the-post-grid' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'default'     => [ $selected_acf_id ],
					'options'     => Fns::get_groups_by_post_type( 'post' ),
				]
			);

		} else {
			foreach ( $post_types as $post_type => $post_type_title ) {
				$get_acf_field   = Fns::get_groups_by_post_type( $post_type );
				$selected_acf_id = '';
				if ( ! empty( $get_acf_field ) && is_array( $get_acf_field ) ) {
					$selected_acf_id = array_key_first( $get_acf_field );
				}

				$ref->add_control(
					$post_type . '_cf_group',
					[
						'label'       => esc_html__( 'Choose Advanced Custom Field (ACF)', 'the-post-grid' ),
						'type'        => Controls_Manager::SELECT2,
						'label_block' => true,
						'multiple'    => true,
						'default'     => [ $selected_acf_id ],
						'options'     => Fns::get_groups_by_post_type( $post_type ),
						'condition'   => [
							'post_type' => $post_type,
						],
					]
				);
			}
		}

		$ref->add_control(
			'cf_hide_empty_value',
			[
				'label'        => esc_html__( 'Hide field with empty value?', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'No', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'cf_hide_group_title',
			[
				'label'        => esc_html__( 'Show group title?', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'No', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'cf_show_only_value',
			[
				'label'        => esc_html__( 'Show label?', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'No', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
	}


	/**
	 * Links Settings
	 *
	 * @param $ref
	 */
	public static function links( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'tpg_links_settings',
			[
				'label'     => esc_html__( 'Links', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],

			]
		);

		$link_type = [
			'default' => esc_html__( 'Link to details page', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$link_type['popup']       = esc_html__( 'Single Popup', 'the-post-grid' );
			$link_type['multi_popup'] = esc_html__( 'Multi Popup', 'the-post-grid' );
		}
		$link_type['none'] = esc_html__( 'No Link', 'the-post-grid' );

		$ref->add_control(
			'post_link_type',
			[
				'label'       => esc_html__( 'Post link type', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => $link_type,
				'description' => $ref->get_pro_message( 'popup options' ),
			]
		);

		$ref->add_control(
			'link_target',
			[
				'label'     => esc_html__( 'Link Target', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '_self',
				'options'   => [
					'_self'  => esc_html__( 'Same Window', 'the-post-grid' ),
					'_blank' => esc_html__( 'New Window', 'the-post-grid' ),
				],
				'condition' => [
					'post_link_type' => 'default',
				],
			]
		);

		$ref->add_control(
			'is_thumb_linked',
			[
				'label'        => esc_html__( 'Thumbnail Link', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Promotions
	 *
	 * @param $ref
	 */
	public static function promotions( $ref ) {
		if ( rtTPG()->hasPro() ) {
			return;
		}
		$pro_url = '//www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/';

		$ref->start_controls_section(
			'tpg_pro_alert',
			[
				'label' => sprintf(
					'<span style="color: #f54">%s</span>',
					__( 'Go Premium for More Features', 'the-post-grid' )
				),
			]
		);

		$ref->add_control(
			'tpg_control_get_pro',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div class="elementor-nerd-box"><div class="elementor-nerd-box-title" style="margin-top: 0; margin-bottom: 20px;">Unlock more possibilities</div><div class="elementor-nerd-box-message"><span class="pro-feature" style="font-size: 13px;"> Get the <a href="'
				          . $pro_url
				          . '" target="_blank" style="color: #f54">Pro version</a> for more stunning layouts and customization options.</span></div><a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-button-go-pro" href="'
				          . $pro_url . '" target="_blank">Get Pro</a></div>',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Section Title Style
	 *
	 * @param $ref
	 */
	public static function sectionTitle( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'tpg_section_title_style',
			[
				'label'     => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);

		$ref->add_control(
			'section_title_alignment',
			[
				'label'        => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'render_type'  => 'template',
				'prefix_class' => 'section-title-align-',
			]
		);

		$ref->add_responsive_control(
			'section_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'section_title_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		if ( 'slider' === $prefix ) {
			$ref->add_responsive_control(
				'section_title_padding',
				[
					'label'              => esc_html__( 'Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all', // horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
					'default'            => [
						'top'      => '',
						'right'    => '',
						'bottom'   => '',
						'left'     => '',
						'isLinked' => false,
					],
					'selectors'          => [
						'{{WRAPPER}} .slider-layout-main .tpg-widget-heading-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'section_title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading',
			]
		);

		$ref->add_control(
			'section_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'section_title_bg_color',
			[
				'label'     => esc_html__( 'Title Background Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading'                => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::before' => 'border-color: {{VALUE}} transparent',
				],
				'condition' => [
					'section_title_style' => [ 'style2', 'style3' ],
				],
			]
		);

		$ref->add_control(
			'section_title_dot_color',
			[
				'label'     => esc_html__( 'Dot / Bar Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before'                     => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'section_title_style' => [ 'style1', 'style4' ],
				],
			]
		);

		$ref->add_control(
			'section_title_line_color',
			[
				'label'     => esc_html__( 'Line / Border Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line'                                                                                                                                                                                                                                                                      => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected'                                                                                       => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover, {{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover'                                                                                             => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before'                                                                                         => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'                                                                                                                                                                                                                                                                                        => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'section_title_style!' => 'default',
				],
			]
		);

		$ref->add_responsive_control(
			'section_title_line_width',
			[
				'label'      => esc_html__( 'Line Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before' => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'  => 'width: calc(100% - calc({{SIZE}}px + 10px))',
				],
				'condition'  => [
					'section_title_style' => [ 'style4' ],
				],
			]
		);

		$ref->add_responsive_control(
			'section_title_line_spacing',
			[
				'label'      => esc_html__( 'Line Spacing', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => - 17,
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::before' => 'bottom: {{SIZE}}px;',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style4::after'  => 'bottom: calc({{SIZE}}px + 2px)',
				],
				'condition'  => [
					'section_title_style' => [ 'style4' ],
				],
			]
		);

		$ref->add_control(
			'prefix_text_color',
			[
				'label'     => esc_html__( 'Prefix Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .prefix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);
		$ref->add_control(
			'suffix_text_color',
			[
				'label'     => esc_html__( 'Suffix Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .suffix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$ref->add_control(
			'external_icon_color',
			[
				'label'     => esc_html__( 'External Link Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'external_icon_color_hover',
			[
				'label'     => esc_html__( 'External Link Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_responsive_control(
			'external_icon_size',
			[
				'label'      => esc_html__( 'External Icon Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$ref->add_responsive_control(
			'external_icon_position',
			[
				'label'      => esc_html__( 'External Icon Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 70,
						'max'  => 70,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .external-link' => 'top: {{SIZE}}px;',
				],
			]
		);

		if ( 'archive' == $layout_type ) {
			$ref->add_control(
				'cat_tag_description_heading',
				[
					'label'     => esc_html__( 'Category / Tag Description', 'the-post-grid' ),
					'type'      => Controls_Manager::HEADING,
					'classes'   => 'tpg-control-type-heading',
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name'      => 'taxonomy_des_typography',
					'label'     => esc_html__( 'Description Typography', 'the-post-grid' ),
					'selector'  => '{{WRAPPER}} .tpg-category-description',
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_responsive_control(
				'taxonomy_des_alignment',
				[
					'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => esc_html__( 'Left', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-center',
						],
						'right'  => [
							'title' => esc_html__( 'Right', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tpg-category-description' => 'text-align: {{VALUE}}',
					],
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_control(
				'taxonomy_des_color',
				[
					'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tpg-category-description' => 'color: {{VALUE}}',
					],
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_responsive_control(
				'taxonomy_des_dimension',
				[
					'label'      => esc_html__( 'Padding', 'the-post-grid' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .tpg-category-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'show_cat_desc' => 'yes',
					],
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Thumbnail Style Tab
	 *
	 * @param $ref
	 */
	public static function thumbnailStyle( $ref ) {
		$prefix = $ref->prefix;
		// Thumbnail style
		// ========================================================
		$ref->start_controls_section(
			'thumbnail_style',
			[
				'label'     => esc_html__( 'Thumbnail', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumb' => 'show',
				],
			]
		);

		$ref->add_responsive_control(
			'img_border_radius',
			[
				'label'              => esc_html__( 'Border Radius', 'the-post-grid' ) . $ref->pro_label,
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all', // horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => true,
				],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap, {{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img, {{WRAPPER}} .rt-grid-hover-item .grid-hover-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'description'        => $ref->get_pro_message( 'image radius.' ),
				'classes'            => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'image_width',
			[
				'label'     => esc_html__( 'Image Width (Optional)', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inherit',
				'options'   => [
					'inherit' => esc_html__( 'Default', 'the-post-grid' ),
					'100%'    => esc_html__( '100%', 'the-post-grid' ),
					'auto'    => esc_html__( 'Auto', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img' => 'width: {{VALUE}};',
				],
			]
		);

		$ref->add_responsive_control(
			'thumbnail_content_width',
			[
				'label'      => esc_html__( 'Thumbnail Content Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 100,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-grid-hover-item .rt-holder .grid-hover-content' => 'width: {{SIZE}}{{UNIT}};margin-left:auto;margin-right:auto;',
				],
				'condition'  => [
					$prefix . '_layout' => 'slider-layout7',
				],
			]
		);

		$ref->add_responsive_control(
			'thumbnail_content_margin',
			[
				'label'              => esc_html__( 'Thumbnail Content Margin', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', 'custom' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-grid-hover-item .rt-holder .grid-hover-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					$prefix . '_layout' => 'slider-layout7',
				],
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		if ( 'grid_hover' != $prefix ) {
			$ref->add_responsive_control(
				'thumbnail_spacing',
				[
					'label'      => esc_html__( 'Thumbnail Margin', 'the-post-grid' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'default'    => [
						'top'      => '',
						'right'    => '',
						'bottom'   => '',
						'left'     => '',
						'isLinked' => false,
					],
				]
			);
		}

		if ( in_array( $prefix, [ 'grid_hover', 'slider' ] ) ) {
			if ( 'slider' == $prefix ) {
				$thumbnail_padding_condition = [
					'slider_layout' => [
						'slider-layout4',
						'slider-layout5',
						'slider-layout6',
						'slider-layout7',
						'slider-layout8',
						'slider-layout9',
						'slider-layout10',
					],
				];
			} else {
				$thumbnail_padding_condition = [
					'grid_hover_layout!' => '',
				];
			}
			$ref->add_responsive_control(
				'grid_hover_thumbnail_margin',
				[
					'label'      => esc_html__( 'Thumbnail padding', 'the-post-grid' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .rt-tpg-container .rt-el-content-wrapper .gallery-content'            => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => $thumbnail_padding_condition,
				]
			);
		}

		// Overlay Style Heading

		$ref->add_control(
			'thumb_overlay_style_heading',
			[
				'label'   => esc_html__( 'Overlay Style:', 'the-post-grid' ),
				'type'    => Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		// TODO: Tab normal
		$ref->start_controls_tabs(
			'grid_hover_style_tabs'
		);

		$ref->start_controls_tab(
			'grid_hover_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'grid_hover_overlay_color',
				'label'          => esc_html__( 'Overlay BG', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:before, {{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay Background Type', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_bg',
			[
				'label'     => esc_html__( 'Light Box Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_color',
			[
				'label'     => esc_html__( 'Light Box Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumbnail_position',
			[
				'label'     => esc_html__( 'Thumb Position', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inherit',
				'options'   => [
					''              => esc_html__( 'Center Center', 'the-post-grid' ),
					'top center'    => esc_html__( 'Top Center', 'the-post-grid' ),
					'bottom center' => esc_html__( 'Bottom Center', 'the-post-grid' ),
					'center left'   => esc_html__( 'Left Center', 'the-post-grid' ),
					'center right'  => esc_html__( 'Right Center', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-holder .tpg-el-image-wrap img' => 'object-position: {{VALUE}};',
				],
			]
		);

		$ref->add_responsive_control(
			'thumbnail_opacity',
			[
				'label'      => esc_html__( 'Thumbnail Opacity', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Tab Hover
		$ref->start_controls_tab(
			'grid_hover_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'grid_hover_overlay_color_hover',
				'label'          => esc_html__( 'Overlay BG - Hover', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:after, {{WRAPPER}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay Background Type - Hover', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_bg_hover',
			[
				'label'     => esc_html__( 'Light Box Background - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_color_hover',
			[
				'label'     => esc_html__( 'Light Box Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumbnail_position_hover',
			[
				'label'     => esc_html__( 'Thumb Position - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inherit',
				'options'   => [
					''              => esc_html__( 'Center Center', 'the-post-grid' ),
					'top center'    => esc_html__( 'Top Center', 'the-post-grid' ),
					'bottom center' => esc_html__( 'Bottom Center', 'the-post-grid' ),
					'center left'   => esc_html__( 'Left Center', 'the-post-grid' ),
					'center right'  => esc_html__( 'Right Center', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap img' => 'object-position: {{VALUE}};',
				],
			]
		);

		$ref->add_responsive_control(
			'thumbnail_opacity_hover',
			[
				'label'      => esc_html__( 'Thumbnail Opacity - Hover', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap img ' => 'opacity: {{SIZE}};',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		// TODO: End Tab Hover

		$ref->add_responsive_control(
			'thumbnail_transition_duration',
			[
				'label'      => esc_html__( 'Thumbnail Transition Duration', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img ' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$ref->add_control(
			'hr_for_overlay',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$overlay_type_opt = [
			'always'              => esc_html__( 'Show Always', 'the-post-grid' ),
			'fadein-on-hover'     => esc_html__( 'FadeIn on hover', 'the-post-grid' ),
			'fadeout-on-hover'    => esc_html__( 'FadeOut on hover', 'the-post-grid' ),
			'slidein-on-hover'    => esc_html__( 'SlideIn on hover', 'the-post-grid' ),
			'slideout-on-hover'   => esc_html__( 'SlideOut on hover', 'the-post-grid' ),
			'zoomin-on-hover'     => esc_html__( 'ZoomIn on hover', 'the-post-grid' ),
			'zoomout-on-hover'    => esc_html__( 'ZoomOut on hover', 'the-post-grid' ),
			'zoominall-on-hover'  => esc_html__( 'ZoomIn Content on hover', 'the-post-grid' ),
			'zoomoutall-on-hover' => esc_html__( 'ZoomOut Content on hover', 'the-post-grid' ),
		];

		if ( $ref->prefix == 'grid_hover' || $ref->prefix == 'slider' ) {
			$overlay_type_opt2 = [
				'flipin-on-hover'  => esc_html__( 'FlipIn on hover', 'the-post-grid' ),
				'flipout-on-hover' => esc_html__( 'FlipOut on hover', 'the-post-grid' ),
			];
			$overlay_type_opt  = array_merge( $overlay_type_opt, $overlay_type_opt2 );
		}

		$ref->add_control(
			'grid_hover_overlay_type',
			[
				'label'        => esc_html__( 'Overlay Interaction', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'always',
				'options'      => $overlay_type_opt,
				'description'  => esc_html__( 'If you don\'t choose overlay background then it will work only for some selected layout ', 'the-post-grid' ),
				'prefix_class' => 'grid-hover-overlay-type-',
			]
		);

		$overlay_height_condition = [
			'grid_hover_layout!' => [ 'grid_hover-layout3' ],
		];
		if ( $ref->prefix === 'slider' ) {
			$overlay_height_condition = [
				'slider_layout!' => [ '' ],
			];
		}
		$ref->add_control(
			'grid_hover_overlay_height',
			[
				'label'        => esc_html__( 'Overlay Height', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'full'    => esc_html__( '100%', 'the-post-grid' ),
					'auto'    => esc_html__( 'Auto', 'the-post-grid' ),
				],
				'condition'    => $overlay_height_condition,
				'prefix_class' => 'grid-hover-overlay-height-',
			]
		);

		$ref->add_control(
			'on_hover_overlay',
			[
				'label'        => esc_html__( 'Overlay Height on hover', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'full'    => esc_html__( '100%', 'the-post-grid' ),
					'auto'    => esc_html__( 'Auto', 'the-post-grid' ),
				],
				'condition'    => $overlay_height_condition,
				'prefix_class' => 'hover-overlay-height-',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Post Title Style
	 *
	 * @param $ref
	 */
	public static function titleStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'title_style',
			[
				'label'     => esc_html__( 'Post Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title'         => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_responsive_control(
			'title_spacing',
			[
				'label'              => esc_html__( 'Title Margin', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_responsive_control(
			'title_padding',
			[
				'label'              => esc_html__( 'Title Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_responsive_control(
			'title_min_height',
			[
				'label'      => esc_html__( 'Title Minimum Height (Optional)', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .entry-title-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .tpg-el-main-wrapper .entry-title-wrapper .entry-title',
			]
		);

		// Offset Title
		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'        => 'title_offset_typography',
				'label'       => esc_html__( 'Offset Typography', 'the-post-grid' ),
				'selector'    => '{{WRAPPER}} .tpg-el-main-wrapper .offset-left .entry-title-wrapper .entry-title',
				'description' => esc_html__( 'You can overwrite offset title font style', 'the-post-grid' ),
				'condition'   => [
					$prefix . '_layout' => [
						'grid-layout5',
						'grid-layout5-2',
						'grid-layout6',
						'grid-layout6-2',
						'list-layout2',
						'list-layout3',
						'list-layout2-2',
						'list-layout3-2',
						'grid_hover-layout4',
						'grid_hover-layout4-2',
						'grid_hover-layout5',
						'grid_hover-layout5-2',
						'grid_hover-layout6',
						'grid_hover-layout6-2',
						'grid_hover-layout7',
						'grid_hover-layout7-2',
						'grid_hover-layout9',
						'grid_hover-layout9-2',
					],
				],
			]
		);

		$ref->add_control(
			'title_border_visibility',
			[
				'label'        => esc_html__( 'Title Border Bottom', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'show'    => esc_html__( 'Show', 'the-post-grid' ),
					'hide'    => esc_html__( 'Hide', 'the-post-grid' ),
				],
				'prefix_class' => 'tpg-title-border-',
				'condition'    => [
					$prefix . '_layout' => 'grid_hover-layout3',
				],
			]
		);

		$ref->add_responsive_control(
			'title_alignment',
			[
				'label'        => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'title-alignment-',
				'toggle'       => true,
				'selectors'    => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		// TODO: Start Title Style Tba
		$ref->start_controls_tabs(
			'title_style_tabs'
		);

		$ref->start_controls_tab(
			'title_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);
		// TODO: Normal Tab
		$ref->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_bg_color',
			[
				'label'     => esc_html__( 'Title Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_border_color',
			[
				'label'     => esc_html__( 'Title Separator Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					$prefix . '_layout'        => 'grid_hover-layout3',
					'title_border_visibility!' => 'hide',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'title_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		// TODO: Hover Tab
		$ref->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__( 'Title Color on Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder .entry-title:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'title_bg_color_hover',
			[
				'label'     => esc_html__( 'Title Background on hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'title_hover_border_color',
			[
				'label'     => esc_html__( 'Title Hover Border Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--tpg-primary-color: {{VALUE}}',
				],
				'condition' => [
					'title_hover_underline' => 'enable',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'title_box_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		// TODO: Box Hover Tab
		$ref->add_control(
			'title_color_box_hover',
			[
				'label'     => esc_html__( 'Title color on boxhover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_bg_color_box_hover',
			[
				'label'     => esc_html__( 'Title Background on boxhover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_border_color_hover',
			[
				'label'     => esc_html__( 'Title Separator color - boxhover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .entry-title-wrapper .entry-title::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					$prefix . '_layout'        => 'grid_hover-layout3',
					'title_border_visibility!' => 'hide',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}

	/**
	 * Content Style / Excerpt Style Tab
	 *
	 * @param $ref
	 */
	public static function contentStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'excerpt_style',
			[
				'label'     => esc_html__( 'Excerpt / Content', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt'       => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner',
			]
		);

		$ref->add_responsive_control(
			'excerpt_spacing',
			[
				'label'              => esc_html__( 'Excerpt Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_responsive_control(
			'content_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner' => 'text-align: {{VALUE}}',
				],
			]
		);

		// TODO: Start Content Tab

		$ref->start_controls_tabs(
			'excerpt_style_tabs'
		);

		$ref->start_controls_tab(
			'excerpt_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		// TODO: Normal Tab
		$ref->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__( 'Excerpt color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'excerpt_border',
			[
				'label'     => esc_html__( 'Border color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.meta_position_default .tpg-el-main-wrapper .grid-layout3 .rt-holder .rt-el-post-meta::before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'meta_position'     => 'default',
					$prefix . '_layout' => [ 'grid-layout3' ],
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'excerpt_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		// TODO: Hover Tab
		$ref->add_control(
			'excerpt_hover_color',
			[
				'label'     => esc_html__( 'Excerpt color on hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-el-excerpt .tpg-excerpt-inner' => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'excerpt_border_hover',
			[
				'label'     => esc_html__( 'Border color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.meta_position_default .tpg-el-main-wrapper .grid-layout3 .rt-holder:hover .rt-el-post-meta::before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'meta_position'     => 'default',
					$prefix . '_layout' => [ 'grid-layout3' ],
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}

	/**
	 * Post Meta Style
	 *
	 * @param $ref
	 */
	public static function metaInfoStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_meta_style',
			[
				'label'     => esc_html__( 'Meta Data', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'post_meta_typography',
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-el-post-meta, {{WRAPPER}} .tpg-post-holder .tpg-separate-category .categories-links a',
			]
		);
		$ref->add_responsive_control(
			'post_meta_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-el-post-meta' => 'text-align: {{VALUE}};justify-content: {{VALUE}}',
				],
			]
		);

		$ref->add_responsive_control(
			'meta_spacing',
			[
				'label'              => esc_html__( 'Meta Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-holder .rt-el-post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		// TODO: Start Content Tab

		$ref->start_controls_tabs(
			'meta_info_style_tabs'
		);

		$ref->start_controls_tab(
			'meta_info_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		// TODO: Normal Tab

		$ref->add_control(
			'meta_info_color',
			[
				'label'     => esc_html__( 'Meta Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags span' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'meta_link_color',
			[
				'label'     => esc_html__( 'Meta Link Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'meta_separator_color',
			[
				'label'     => esc_html__( 'Separator Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .separator' => 'color: {{VALUE}}',
				],
				'condition' => [
					'meta_separator!' => 'default',
				],
			]
		);

		$ref->add_control(
			'meta_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags i' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'meta_info_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		// TODO: Hover Tab

		$ref->add_control(
			'meta_link_colo_hover',
			[
				'label'     => esc_html__( 'Meta Link Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder .post-meta-tags a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'meta_info_box_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		// TODO: Box Hover Tab

		$ref->add_control(
			'meta_link_colo_box_hover',
			[
				'label'     => esc_html__( 'Meta Color - Box Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags *' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		// Separate Category style

		$ref->add_control(
			'separator_cat_heading',
			[
				'label'       => esc_html__( 'Separate Category', 'the-post-grid' ),
				'description' => esc_html__( 'Separate Category', 'the-post-grid' ),
				'type'        => Controls_Manager::HEADING,
				'classes'     => 'tpg-control-type-heading',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'separator_cat_typography',
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .categories-links a',
			]
		);

		$ref->add_control(
			'category_margin_bottom',
			[
				'label'     => esc_html__( 'Category Margin Bottom', 'the-post-grid' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 50,
				'step'      => 1,
				'condition' => [
					'category_position' => 'above_title',
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.above_title' => 'margin-bottom: {{VALUE}}px;',
				],
			]
		);

		$ref->add_responsive_control(
			'category_radius',
			[
				'label'      => esc_html__( 'Category Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a'        => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->start_controls_tabs(
			'separate_cat_info_style_tabs'
		);

		$ref->start_controls_tab(
			'separate_cat_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		// TODO: Normal Tab

		$ref->add_control(
			'separate_category_color',
			[
				'label'     => esc_html__( 'Category Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a'        => 'color: {{VALUE}}',
				],
			]
		);
		$ref->add_control(
			'separate_category_bg',
			[
				'label'       => esc_html__( 'Category Background', 'the-post-grid' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links a'             => 'background-color: {{VALUE}};padding: 3px 8px 1px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a'       => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a:after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a'                           => 'background-color: {{VALUE}}',
				],
				'description' => rtTPG()->hasPro() ? esc_html__( 'If you use different background color then avoid this color', 'the-post-grid' ) : esc_html__( 'Choose separate category background', 'the-post-grid' ),
			]
		);
		$ref->add_control(
			'separate_category_icon_color',
			[
				'label'     => esc_html__( 'Category Icon Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links i'        => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_cat_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'separate_cat_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		// TODO: Hover Tab

		$ref->add_control(
			'separate_category_color_hover',
			[
				'label'     => esc_html__( 'Category Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a:hover' => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover'        => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'separate_category_bg_hover',
			[
				'label'     => esc_html__( 'Category Background - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links:hover'                => 'background-color: {{VALUE}};padding: 3px 8px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links:not(.style1) a:hover'        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links:not(.style1) a:hover::after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover'                            => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'separate_cat_box_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		// TODO: Box Hover Tab

		$ref->add_control(
			'separate_category_color_box_hover',
			[
				'label'     => esc_html__( 'Category Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a'        => 'color: {{VALUE}}',
				],
			]
		);
		$ref->add_control(
			'separate_category_bg_box_hover',
			[
				'label'     => esc_html__( 'Category Background - Box Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category.style1 .categories-links'                => 'background-color: {{VALUE}};padding: 3px 8px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category:not(.style1) .categories-links a'        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category:not(.style1) .categories-links a::after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a'                            => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'separate_category_icon_color_box_hover',
			[
				'label'     => esc_html__( 'Category Icon Color - Box Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links i'        => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_cat_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Read More style
	 *
	 * @param $ref
	 */
	public static function readmoreStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'readmore_button_style',
			[
				'label'     => esc_html__( 'Read More', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_read_more'     => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a',
			]
		);

		$ref->add_responsive_control(
			'readmore_spacing',
			[
				'label'              => esc_html__( 'Button Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__( 'Button Padding', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_btn_alignment',
			[
				'label'     => esc_html__( 'Button Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more' => 'text-align:{{VALUE}}',
				],
				'toggle'    => true,
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_y_position',
			[
				'label'      => esc_html__( 'Icon Vertical Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 20,
						'max'  => 20,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'transform: translateY( {{SIZE}}{{UNIT}} );',
				],
				'condition'  => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		// TODO: Button style Tabs
		$ref->start_controls_tabs(
			'readmore_style_tabs'
		);

		$ref->start_controls_tab(
			'readmore_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg',
			[
				'label'     => esc_html__( 'Background Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_margin',
			[
				'label'              => esc_html__( 'Icon Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'horizontal',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'border_radius',
			[
				'label'              => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'readmore_border',
				'label'          => esc_html__( 'Button Border', 'the-post-grid' ),
				'selector'       => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#D4D4D4',
					],
				],
				'condition'      => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Hover Tab

		$ref->start_controls_tab(
			'readmore_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color_hover',
			[
				'label'     => esc_html__( 'Text Color hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color_hover',
			[
				'label'     => esc_html__( 'Icon Color Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg_hover',
			[
				'label'     => esc_html__( 'Background Color hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_margin_hover',
			[
				'label'              => esc_html__( 'Icon Spacing - Hover', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'horizontal',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'border_radius_hover',
			[
				'label'              => esc_html__( 'Border Radius - Hover', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'readmore_border_hover',
				'label'          => esc_html__( 'Button Border - Hover', 'the-post-grid' ),
				'selector'       => 'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#7a64f2',
					],
				],
				'condition'      => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Box Hover Tab

		$ref->start_controls_tab(
			'readmore_style_box_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color_box_hover',
			[
				'label'     => esc_html__( 'Text Color - BoxHover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color_box_hover',
			[
				'label'     => esc_html__( 'Icon Color - BoxHover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg_box_hover',
			[
				'label'     => esc_html__( 'Background Color - BoxHover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'readmore_border_box_hover',
				'label'     => esc_html__( 'Button Border - Box Hover', 'the-post-grid' ),
				'selector'  => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a',
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Pagination and Load more style tab
	 *
	 * @param $ref
	 */
	public static function paginationStyle( $ref ) {
		$ref->start_controls_section(
			'pagination_loadmore_style',
			[
				'label'     => esc_html__( 'Pagination / Load More', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'show',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .rt-pagination .pagination-list > li > a, {{WRAPPER}} .rt-pagination .pagination-list > li > span',

			]
		);

		$ref->add_responsive_control(
			'pagination_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rt-pagination-wrap' => 'justify-content: {{VALUE}};',
				],
				'default'   => 'center',
				'toggle'    => true,
			]
		);

		$ref->add_responsive_control(
			'pagination_spacing',
			[
				'label'              => esc_html__( 'Button Vertical Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		$ref->add_responsive_control(
			'pagination_padding',
			[
				'label'              => esc_html__( 'Button Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a, {{WRAPPER}} .rt-pagination .pagination-list > li > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		$ref->add_responsive_control(
			'pagination_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:first-child > a, {{WRAPPER}} .rt-pagination .pagination-list > li:first-child > span' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}}; border-top-left-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-pagination .pagination-list > li:last-child > a, {{WRAPPER}} .rt-pagination .pagination-list > li:last-child > span'   => 'border-bottom-right-radius: {{SIZE}}{{UNIT}}; border-top-right-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		// Button style Tabs
		$ref->start_controls_tabs(
			'pagination_style_tabs',
			[
				'condition' => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		// TODO: Normal Tab
		$ref->start_controls_tab(
			'pagination_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color',
			[
				'label'     => esc_html__( 'Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:not(:hover) > a, {{WRAPPER}} .rt-pagination .pagination-list > li:not(:hover) > span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a'            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover)'                => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_bg',
			[
				'label'     => esc_html__( 'Background Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:not(:hover), {{WRAPPER}} .rt-pagination .pagination-list > li:not(:hover) > span' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a'            => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'background-color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'pagination_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:not(:hover), {{WRAPPER}} .rt-pagination .pagination-list > li:not(:hover) > span' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:not(:hover) > a'            => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Hover Tab
		$ref->start_controls_tab(
			'pagination_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color_hover',
			[
				'label'     => esc_html__( 'Color - hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:hover > a, {{WRAPPER}} .rt-pagination .pagination-list > li:hover > span' => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a'      => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'pagination_bg_hover',
			[
				'label'     => esc_html__( 'Background Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:hover > a, {{WRAPPER}} .rt-pagination .pagination-list > li:hover > span' => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a'      => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'pagination_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:hover > a, {{WRAPPER}} .rt-pagination .pagination-list > li:hover > span' => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li:hover > a'      => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'border-color: {{VALUE}} !important',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Active Tab
		$ref->start_controls_tab(
			'pagination_style_active_tab',
			[
				'label' => esc_html__( 'Active', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Color - Active', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_bg_active',
			[
				'label'     => esc_html__( 'Background Color - Active', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_border_color_active',
			[
				'label'     => esc_html__( 'Border Color - Active', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus,
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Front-end Filter style / frontend style
	 *
	 * @param $ref
	 */
	public static function frontEndFilter( $ref ) {
		if ( ! rtTPG()->hasPro() ) {
			return;
		}
		$ref->start_controls_section(
			'front_end_filter_style',
			[
				'label'      => esc_html__( 'Front-End Filter', 'the-post-grid' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_taxonomy_filter',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_author_filter',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_order_by',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_sort_order',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_search',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'front_filter_typography',
				'label'    => esc_html__( 'Filter Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap, {{WRAPPER}} .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide',
			]
		);

		$ref->add_responsive_control(
			'filter_text_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-layout-filter-container .rt-filter-wrap' => 'text-align: {{VALUE}};justify-content:{{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_v_alignment',
			[
				'label'        => esc_html__( 'Vertical Alignment', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Top', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
				'prefix_class' => 'tpg-filter-alignment-',
				'toggle'       => true,
			]
		);

		$ref->add_responsive_control(
			'filter_button_width',
			[
				'label'      => esc_html__( 'Filter Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-header-wrapper.carousel .rt-layout-filter-container' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
			]
		);

		$ref->add_control(
			'border_style',
			[
				'label'        => esc_html__( 'Filter Border', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'disable',
				'options'      => [
					'disable' => esc_html__( 'Disable', 'the-post-grid' ),
					'enable'  => esc_html__( 'Enable', 'the-post-grid' ),
				],
				'condition'    => [
					'filter_type'          => 'button',
					'filter_btn_style'     => 'carousel',
					'section_title_style!' => [ 'style2', 'style3' ],
				],
				'prefix_class' => 'filter-button-border-',
			]
		);

		$ref->add_control(
			'filter_next_prev_btn',
			[
				'label'        => esc_html__( 'Next/Prev Button', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'visible',
				'options'      => [
					'visible' => esc_html__( 'Visible', 'the-post-grid' ),
					'hidden'  => esc_html__( 'Hidden', 'the-post-grid' ),
				],
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
				'prefix_class' => 'filter-nex-prev-btn-',
			]
		);

		$ref->add_control(
			'filter_h_alignment',
			[
				'label'        => esc_html__( 'Vertical Alignment', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'          => [
						'title' => esc_html__( 'Top', 'the-post-grid' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'center'        => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-justify-center-h',
					],
					'right'         => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html__( 'Space Between', 'the-post-grid' ),
						'icon'  => 'eicon-justify-space-between-h',
					],
				],
				'condition'    => [
					'filter_type!' => 'button',
				],
				'prefix_class' => 'tpg-filter-h-alignment-',
				'toggle'       => true,
			]
		);

		$ref->add_responsive_control(
			'filter_btn_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap'      => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'      => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_responsive_control(
			'filter_btn_spacing',
			[
				'label'      => esc_html__( 'Filter wrapper Spacing', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'filter_btn_style' => 'default',
				],
			]
		);

		// TODO: Start Tab
		$ref->start_controls_tabs(
			'frontend_filter_style_tabs'
		);

		$ref->start_controls_tab(
			'frontend_filter_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'filter_color',
			[
				'label'     => esc_html__( 'Filter Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap'                                                                                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:before, {{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'                                                                                                                    => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_bg_color',
			[
				'label'     => esc_html__( 'Filter Background Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                    => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action'    => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_border_color',
			[
				'label'     => esc_html__( 'Filter Border Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                    => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action'    => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'                         => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container'     => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_search_bg',
			[
				'label'     => esc_html__( 'Search Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_search'      => 'show',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_heading',
			[
				'label'     => esc_html__( 'Sub Menu Options', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_bg_color',
			[
				'label'     => esc_html__( 'Submenu Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color',
			[
				'label'     => esc_html__( 'Submenu Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_border_bottom',
			[
				'label'     => esc_html__( 'Submenu Border', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'filter_nav_color',
			[
				'label'     => esc_html__( 'Filter Nav Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_bg',
			[
				'label'     => esc_html__( 'Filter Nav Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_border',
			[
				'label'     => esc_html__( 'Filter Nav Border', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->end_controls_tab();

		// TODO: Start Tab Hover
		$ref->start_controls_tab(
			'frontend_filter_style_hover_tab',
			[
				'label' => esc_html__( 'Hover / Active', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'filter_color_hover',
			[
				'label'     => esc_html__( 'Filter Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover'                         => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                                                  => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:before, {{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_bg_color_hover',
			[
				'label'     => esc_html__( 'Filter Background Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                          => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover'                                                                             => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_border_color_hover',
			[
				'label'     => esc_html__( 'Filter Border Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                          => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover'                                                                             => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover'                                                                                                  => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container:hover'                                                                              => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_search_bg_hover',
			[
				'label'     => esc_html__( 'Search Background - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_search'      => 'show',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_heading_hover',
			[
				'label'     => esc_html__( 'Sub Menu Options - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_bg_color_hover',
			[
				'label'     => esc_html__( 'Submenu Background - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_hover',
			[
				'label'     => esc_html__( 'Submenu Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_border_bottom_hover',
			[
				'label'     => esc_html__( 'Submenu Border - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'filter_nav_color_hover',
			[
				'label'     => esc_html__( 'Filter Nav Color - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_bg_hover',
			[
				'label'     => esc_html__( 'Filter Nav Background - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_border_hover',
			[
				'label'     => esc_html__( 'Filter Nav Border - Hover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();
		// TODO: End Tab

		$ref->end_controls_section();
	}


	/**
	 * Social Share Style
	 *
	 * @param $ref
	 */
	public static function socialShareStyle( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'social_share_style',
			[
				'label'     => esc_html__( 'Social Share', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_share'  => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		self::get_social_share_control( $ref );

		$ref->end_controls_section();
	}

	/**
	 * Get Social Share
	 *
	 * @param $ref
	 * @param $prefix
	 */
	public static function get_social_share_control( $ref ) {
		$settings = get_option( rtTPG()->options['settings'] );
		$ssList   = ! empty( $settings['social_share_items'] ) ? $settings['social_share_items'] : [];

		$ref->add_responsive_control(
			'social_icon_margin',
			[
				'label'              => esc_html__( 'Icon Margin', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'social_wrapper_margin',
			[
				'label'              => esc_html__( 'Icon Wrapper Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all', // horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'social_icon_radius',
			[
				'label'              => esc_html__( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => true,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_control(
			'icon_width_height',
			[
				'label'       => esc_html__( 'Icon Dimension', 'the-post-grid' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'default'     => [
					'width'  => '',
					'height' => '',
				],
				'selectors'   => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'width:{{width}}px; height:{{height}}px; line-height:{{height}}px; text-align:center',
				],
				'description' => esc_html__( 'Just write number. Don\'t use (px or em).', 'the-post-grid' ),
				'classes'     => 'should-show-title',
			]
		);

		$ref->add_responsive_control(
			'icon_font_size',
			[
				'label'      => esc_html__( 'Icon Font Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 12,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$ref->add_control(
			'social_icon_color_heading',
			[
				'label'     => esc_html__( 'Icon Color:', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$ref->add_control(
			'social_icon_style',
			[
				'label'       => esc_html__( 'Icon Color Style', 'the-post-grid' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'         => esc_html__( 'Default (Brand Color)', 'the-post-grid' ),
					'different_color' => esc_html__( 'Different Color for each', 'the-post-grid' ),
					'custom'          => esc_html__( 'Custom color', 'the-post-grid' ),
				],
				'description' => esc_html__( 'Select Custom for your own customize', 'the-post-grid' ),
			]
		);

		// TODO: Start Social Share Tabs Tab
		$ref->start_controls_tabs(
			'social_share_style_tabs'
		);

		$ref->start_controls_tab(
			'social_share_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);
		// TODO: Normal Tab

		$ref->add_control(
			'social_icon_color',
			[
				'label'     => esc_html__( 'Social Icon color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);

		$ref->add_control(
			'social_icon_bg_color',
			[
				'label'     => esc_html__( 'Social Icon Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);

		foreach ( $ssList as $ss ) {
			$ref->add_control(
				$ss . '_social_icon_color',
				[
					'label'     => ucwords( $ss ) . esc_html__( ' color', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ' i' => 'color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);

			$ref->add_control(
				$ss . '_social_icon_bg_color',
				[
					'label'     => ucwords( $ss ) . esc_html__( ' Background', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ' i' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);
		}

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'social_icon_border',
				'label'    => esc_html__( 'Icon Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-social-share a i',
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'socia_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		// TODO: Hover Tab

		$ref->add_control(
			'social_icon_color_hover',
			[
				'label'     => esc_html__( 'Icon color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a:hover i' => 'color: {{VALUE}} !important',
				],
				// 'condition' => [
				// 'social_icon_style' => 'custom',
				// ],
			]
		);

		$ref->add_control(
			'social_icon_bg_color_hover',
			[
				'label'     => esc_html__( 'Icon Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a:hover i' => 'background-color: {{VALUE}} !important',
				],
				// 'condition' => [
				// 'social_icon_style' => 'custom',
				// ],
			]
		);

		// foreach ( $ssList as $ss ) {
		// $ref->add_control(
		// $ss . '_social_icon_color_hover',
		// [
		// 'label'     => ucwords( $ss ) . esc_html__( ' color - Hover', 'the-post-grid' ),
		// 'type'      => \Elementor\Controls_Manager::COLOR,
		// 'selectors' => [
		// '{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ':hover i' => 'color: {{VALUE}}',
		// ],
		// 'condition' => [
		// 'social_icon_style' => 'different_color',
		// ],
		// ]
		// );
		//
		// $ref->add_control(
		// $ss . '_social_icon_bg_color_hover',
		// [
		// 'label'     => ucwords( $ss ) . esc_html__( ' Background - Hover', 'the-post-grid' ),
		// 'type'      => \Elementor\Controls_Manager::COLOR,
		// 'selectors' => [
		// '{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ':hover i' => 'background-color: {{VALUE}}',
		// ],
		// 'condition' => [
		// 'social_icon_style' => 'different_color',
		// ],
		// ]
		// );
		// }

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'social_icon_border_hover',
				'label'    => esc_html__( 'Icon Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-social-share a:hover i',
			]
		);

		$ref->end_controls_tab();

		// TODO: ============================
		$ref->start_controls_tab(
			'socia_box_hover_tab',
			[
				'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'social_icon_color_box_hover',
			[
				'label'     => esc_html__( 'Icon color - BoxHover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .rt-tpg-social-share a i' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'social_icon_bg_color_box_hover',
			[
				'label'     => esc_html__( 'Icon Background - BoxHover', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .rt-tpg-social-share a i' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();
	}

	/**
	 * Box style / Card style
	 *
	 * @param $ref
	 */
	public static function articlBoxSettings( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'article_box_settings',
			[
				'label' => esc_html__( 'Card (Post Item)', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		if ( 'slider' !== $prefix ) {
			$ref->add_responsive_control(
				'box_margin',
				[
					'label'       => esc_html__( 'Card Gap', 'the-post-grid' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => [ 'px' ],
					'render_type' => 'template',
					'selectors'   => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row [class*="rt-col"]'              => 'padding-left: {{LEFT}}{{UNIT}} !important; padding-right: {{RIGHT}}{{UNIT}} !important; padding-bottom: calc(2 * {{BOTTOM}}{{UNIT}}) !important;',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row'                                => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}}',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row .rt-row'                        => 'margin-bottom: -{{RIGHT}}{{UNIT}}',
						'{{WRAPPER}} .rt-tpg-container .grid_hover-layout8 .display-grid-wrapper' => 'grid-gap: {{TOP}}{{UNIT}};margin-bottom: {{TOP}}{{UNIT}}',
					],
					'condition'   => [
						$prefix . '_layout!' => [
							'grid-layout2',
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'list-layout4',
						],
					],
				]
			);
		}

		if ( in_array( $prefix, [ 'grid', 'list', 'slider' ] ) ) {
			if ( 'slider' == $prefix ) {
				$box_padding = [
					$prefix . '_layout' => [
						'slider-layout1',
						'slider-layout2',
						'slider-layout3',
					],
				];
			} else {
				$box_padding = [
					$prefix . '_layout!' => [
						'grid-layout5',
						'grid-layout5-2',
						'grid-layout6',
						'grid-layout6-2',
						'grid-layout7',
						'list-layout1',
						'list-layout2',
						'list-layout2-2',
						'list-layout3',
						'list-layout3-2',
						'list-layout4',
						'list-layout5',
						'slider-layout1',
						'slider-layout2',
						'slider-layout3',
					],
				];
			}
			$ref->add_responsive_control(
				'content_box_padding',
				[
					'label'              => esc_html__( 'Content Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all',
					'selectors'          => [
						'body {{WRAPPER}} .rt-tpg-container .rt-el-content-wrapper'                                  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'body {{WRAPPER}} .rt-tpg-container .rt-el-content-wrapper-flex .post-right-content'         => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-holder .rt-el-content-wrapper .tpg-el-image-wrap' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
					],
					'condition'          => $box_padding,
				]
			);

			$ref->add_responsive_control(
				'content_box_padding_offset',
				[
					'label'              => esc_html__( 'Content Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all',
					'selectors'          => [
						'body {{WRAPPER}} .tpg-el-main-wrapper .offset-left .tpg-post-holder .offset-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'{{WRAPPER}} .rt-tpg-container .list-layout4 .post-right-content'                     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					],
					'condition'          => [
						$prefix . '_layout' => [
							'grid-layout5',
							'grid-layout5-2',
							'list-layout4',
						],
					],
				]
			);
		}

		$ref->add_responsive_control(
			'content_box_padding_2',
			[
				'label'              => esc_html__( 'Content Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition'          => [
					$prefix . '_layout' => [ 'slider-layout13' ],
				],
			]
		);

		$ref->add_responsive_control(
			'box_radius',
			[
				'label'      => esc_html__( 'Card Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder'                       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
					'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
				],
				'condition'  => [
					$prefix . '_layout!' => [
						'list-layout2',
						'list-layout2-2',
						'list-layout3',
						'list-layout3-2',
						'list-layout4',
						'list-layout4-2',
						'list-layout5',
						'list-layout5-2',
						'slider-layout11',
						'slider-layout12',
						'slider-layout13',
					],
				],
			]
		);

		if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
			$ref->add_control(
				'is_box_border',
				[
					'label'        => esc_html__( 'Enable Border & Box Shadow', 'the-post-grid' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'enable',
					'options'      => [
						'enable'  => esc_html__( 'Enable', 'the-post-grid' ),
						'disable' => esc_html__( 'Disable', 'the-post-grid' ),
					],
					'prefix_class' => 'tpg-el-box-border-',
					'condition'    => [
						$prefix . '_layout!' => [
							'slider-layout11',
							'slider-layout12',
							'slider-layout13',
						],
					],
				]
			);
		}

		if ( 'slider' ) {
			$ref->add_control(
				'box_border_bottom',
				[
					'label'        => esc_html__( 'Enable Border Bottom', 'the-post-grid' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'disable',
					'options'      => [
						'enable'  => esc_html__( 'Enable', 'the-post-grid' ),
						'disable' => esc_html__( 'Disable', 'the-post-grid' ),
					],
					'prefix_class' => 'tpg-border-bottom-',
					'condition'    => [
						$prefix . '_layout!' => [
							'slider-layout11',
							'slider-layout12',
							'slider-layout13',
						],
					],
				]
			);
		}

		$ref->add_control(
			'box_border_bottom_color',
			[
				'label'     => esc_html__( 'Border Bottom Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'box_border_bottom' => 'enable',
				],
			]
		);

		$ref->add_responsive_control(
			'box_border_spacing',
			[
				'label'      => esc_html__( 'Border Spacing Bottom', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'box_border_bottom' => 'enable',
				],
			]
		);

		if ( 'grid_hover' !== $prefix ) {
			// TODO: Start Tab
			$ref->start_controls_tabs(
				'box_style_tabs'
			);

			// TODO: Normal Tab
			$ref->start_controls_tab(
				'box_style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'the-post-grid' ),
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background',
					'label'          => esc_html__( 'Background', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder',
					'condition'      => [
						$prefix . '_layout!' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background2',
					'label'          => esc_html__( 'Background', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition'      => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
				$ref->add_control(
					'box_border',
					[
						'label'     => esc_html__( 'Border Color', 'the-post-grid' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder' => 'border: 1px solid {{VALUE}}',
						],
						'condition' => [
							'is_box_border' => 'enable',
						],
					]
				);

				$ref->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'box_box_shadow',
						'label'     => esc_html__( 'Box Shadow', 'the-post-grid' ),
						'selector'  => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder',
						'condition' => [
							'is_box_border' => 'enable',
						],
					]
				);
			}

			$ref->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'box_box_shadow2',
					'label'     => esc_html__( 'Box Shadow', 'the-post-grid' ),
					'selector'  => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition' => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->end_controls_tab();

			// TODO: Hover Tab
			$ref->start_controls_tab(
				'box_style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'the-post-grid' ),
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background_hover',
					'label'          => esc_html__( 'Background - Hover', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background - Hover', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover',
					'condition'      => [
						$prefix . '_layout!' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background_hover2',
					'label'          => esc_html__( 'Background - Hover', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background - Hover', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition'      => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
				$ref->add_control(
					'box_border_hover',
					[
						'label'     => esc_html__( 'Border Color - Hover', 'the-post-grid' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover' => 'border: 1px solid {{VALUE}}',
						],
						'condition' => [
							'is_box_border' => 'enable',
						],
					]
				);

				$ref->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'box_box_shadow_hover',
						'label'     => esc_html__( 'Box Shadow - Hover', 'the-post-grid' ),
						'selector'  => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover',
						'condition' => [
							'is_box_border' => 'enable',
						],
					]
				);
			}

			$ref->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'box_box_shadow_hover2',
					'label'     => esc_html__( 'Box Shadow - Hover', 'the-post-grid' ),
					'selector'  => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition' => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->end_controls_tab();

			$ref->end_controls_tabs();
			// TODO: End Tab

		}

		$ref->end_controls_section();
	}


	/**
	 * Slider Settings
	 *
	 * @param $ref
	 */
	public static function slider_settings( $ref, $layout_type = '' ) {
		$slider_condition = '';
		if ( 'single' === $layout_type ) {
			$slider_condition = [
				'enable_related_slider!' => '',
			];
		}
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'slider_settings',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => $slider_condition,
			]
		);

		$ref->add_responsive_control(
			'slider_gap',
			[
				'label'      => esc_html__( 'Slider Gap', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'body {{WRAPPER}} .tpg-el-main-wrapper .rt-slider-item'                     => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
					'body {{WRAPPER}} .tpg-el-main-wrapper .rt-swiper-holder'                   => 'margin-left: calc(-{{SIZE}}{{UNIT}} - 5px);margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .slider-column.swiper-slide .rt-slider-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					$prefix . '_layout!' => [
						'slider-layout10',
						'slider-layout11',
						'slider-layout12',
						'slider-layout13',
					],
				],
			]
		);

		$ref->add_control(
			'arrows',
			[
				'label'        => esc_html__( 'Arrow Visibility', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		$ref->add_control(
			'arrow_position',
			[
				'label'        => esc_html__( 'Arrow Position', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => esc_html__( 'Default', 'the-post-grid' ),
					'top-right'  => esc_html__( 'Top Right', 'the-post-grid' ),
					'top-left'   => esc_html__( 'Top Left', 'the-post-grid' ),
					'show-hover' => esc_html__( 'Center (Show on hover)', 'the-post-grid' ),
				],
				'condition'    => [
					'arrows'             => 'yes',
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
				'prefix_class' => 'slider-arrow-position-',
			]
		);

		$ref->add_control(
			'dots',
			[
				'label'        => esc_html__( 'Dots Visibility', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'slider-dot-enable-',
				'render_type'  => 'template',
				'condition'    => [
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		$ref->add_control(
			'dynamic_dots',
			[
				'label'        => esc_html__( 'Enable Dynamic Dots', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'render_type'  => 'template',
				'condition'    => [
					'dots'               => 'yes',
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		$ref->add_control(
			'dots_style',
			[
				'label'        => esc_html__( 'Dots Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => esc_html__( 'Default', 'the-post-grid' ),
					'background' => esc_html__( 'With Background', 'the-post-grid' ),
				],
				'condition'    => [
					'dots'               => 'yes',
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
				'prefix_class' => 'slider-dots-style-',
			]
		);

		$ref->add_control(
			'infinite',
			[
				'label'        => esc_html__( 'Infinite', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
			]
		);

		$ref->add_control(
			'autoplaySpeed',
			[
				'label'     => esc_html__( 'Autoplay Speed', 'the-post-grid' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 10000,
				'step'      => 500,
				'default'   => 3000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$ref->add_control(
			'stopOnHover',
			[
				'label'        => esc_html__( 'Stop On Hover', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$ref->add_control(
			'grabCursor',
			[
				'label'        => esc_html__( 'Allow Touch Move', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'autoHeight',
			[
				'label'        => esc_html__( 'Auto Height', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'condition'    => [
					'enable_2_rows!'     => 'yes',
					$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		$ref->add_control(
			'lazyLoad',
			[
				'label'        => esc_html__( 'lazy Load', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the-post-grid' ),
				'label_off'    => esc_html__( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'prefix_class' => 'is-lazy-load-',
				'render_type'  => 'template',
			]
		);

		$ref->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Speed', 'the-post-grid' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 100,
				'default' => 500,
			]
		);

		$ref->add_control(
			'enable_2_rows',
			[
				'label'        => esc_html__( 'Enable 2 Rows', 'the-post-grid' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'prefix_class' => 'enable-two-rows-',
				'render_type'  => 'template',
				'description'  => esc_html__( 'If you use 2 rows then you have to put an even number for post limit', 'the-post-grid' ),
				'condition'    => [
					$prefix . '_layout!' => [
						'slider-layout13',
						'slider-layout11',
						'slider-layout12',
						'slider-layout10',
					],
				],
			]
		);

		$ref->add_control(
			'carousel_overflow',
			[
				'label'        => esc_html__( 'Slider Overflow', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'hidden',
				'options'      => [
					'hidden' => esc_html__( 'Hidden', 'the-post-grid' ),
					'none'   => esc_html__( 'None', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'is-carousel-overflow-',
				'condition'    => [
					'lazyLoad!' => 'yes',
				],
			]
		);

		$ref->add_control(
			'slider_direction',
			[
				'label'        => esc_html__( 'Direction', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'ltr',
				'options'      => [
					'ltr' => esc_html__( 'LTR', 'the-post-grid' ),
					'rtl' => esc_html__( 'RTL', 'the-post-grid' ),
				],
				'prefix_class' => 'slider-direction-',
				'render_type'  => 'template',
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Slider Style
	 *
	 * @param $ref
	 */
	public static function slider_style( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		if ( 'single' === $layout_type ) {
			$slider_condition = [
				'enable_related_slider!' => '',
				$prefix . '_layout!'     => [ 'slider-layout11', 'slider-layout12' ],
			];
		} else {
			$slider_condition = [
				$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
			];
		}

		$ref->start_controls_section(
			'slider_style',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $slider_condition,
			]
		);

		$ref->add_control(
			'arrow_style_heading',
			[
				'label'     => esc_html__( 'Arrow Style', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_font_size',
			[
				'label'      => esc_html__( 'Arrow Font Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_border_radius',
			[
				'label'      => esc_html__( 'Arrow Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_width',
			[
				'label'      => esc_html__( 'Arrow Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_height',
			[
				'label'      => esc_html__( 'Arrow Height', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'height: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_x_position',
			[
				'label'      => esc_html__( 'Arrow X Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-right .swiper-navigation'                  => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-left .swiper-navigation'                   => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_y_position',
			[
				'label'      => esc_html__( 'Arrow Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => - 150,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn'                                                                  => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-right .swiper-navigation, {{WRAPPER}}.slider-arrow-position-top-left .swiper-navigation' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		// TODO: Arrow Tabs Start
		$ref->start_controls_tabs(
			'arrow_style_tabs',
			[
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->start_controls_tab(
			'arrow_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'arrow_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Icon Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'arrow_arrow_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrow_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn',
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'arrow_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'arrow_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Icon Color - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'arrow_bg_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Background - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrow_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'border_hover',
				'label'    => esc_html__( 'Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover',
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();
		// TODO: Arrow Tabs End

		// TODO: Dots style Start

		$ref->add_control(
			'dot_style_heading',
			[
				'label'     => esc_html__( 'Dots Style', 'the-post-grid' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_text_align',
			[
				'label'        => esc_html__( 'Dots Alignment', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'       => true,
				'condition'    => [
					'dots' => 'yes',
				],
				'prefix_class' => 'slider-dots-align-',
			]
		);

		$ref->add_responsive_control(
			'dot_wrapper_radius',
			[
				'label'      => esc_html__( 'Dots Wrapper Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'dots_style' => 'background',
					'dots'       => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_border_radius',
			[
				'label'      => esc_html__( 'Dots Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_width',
			[
				'label'      => esc_html__( 'Dots Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet'                                 => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: calc({{SIZE}}{{UNIT}} + 15px);',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_height',
			[
				'label'      => esc_html__( 'Dots Height', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_margin',
			[
				'label'      => esc_html__( 'Dots Margin', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_position',
			[
				'label'      => esc_html__( 'Dots Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 150,
						'max'  => 150,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}} !important ;',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		// TODO: Dots Tab Start
		$ref->start_controls_tabs(
			'dots_style_tabs',
			[
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->start_controls_tab(
			'dots_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'dots_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Dots Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_wrap_bg',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Wrapper Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots'       => 'yes',
					'dots_style' => 'background',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'dots_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'dots_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Color - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 *  Link Style
	 *
	 * @param $ref
	 */
	public static function linkStyle( $ref ) {
		$ref->start_controls_section(
			'linkStyle',
			[
				'label'     => esc_html__( 'Popup Style', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_link_type' => [ 'popup', 'multi_popup' ],
				],
			]
		);

		$ref->add_control(
			'popup_head_bg',
			[
				'label'     => esc_html__( 'Header Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .rt-popup-wrap .rt-popup-navigation-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'popup_head_txt_color',
			[
				'label'     => esc_html__( 'Header Text Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body #rt-popup-wrap .rt-popup-singlePage-counter' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'popup_title_color',
			[
				'label'     => esc_html__( 'Popup Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content-holder > .md-header .entry-title' => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container h1.entry-title'          => 'color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'popup_meta_color',
			[
				'label'     => esc_html__( 'Popup Meta Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content-holder > .md-header .post-meta-user *' => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container .post-meta-user *'            => 'color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'popup_content_color',
			[
				'label'     => esc_html__( 'Popup Content Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content *'                       => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container .tpg-content *' => 'color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'popup_bg',
			[
				'label'     => esc_html__( 'Popup Background', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content, body #rt-popup-wrap .rt-popup-content' => 'background-color: {{VALUE}}',
				],

			]
		);

		$ref->end_controls_section();
	}


	/**
	 *  Slider thumb Settings for layout- 11, 12
	 *
	 * @param $ref
	 */
	public static function slider_thumb_style( $ref ) {
		$ref->start_controls_section(
			'slider_thumb_style',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_layout' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		// TODO: Crative slider style:
		$ref->add_control(
			'scroll_bar_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Scroll Foreground Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .swiper-thumb-pagination .swiper-pagination-progressbar-fill'                                                => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'scroll_bar_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Scroll Background Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-thumb-main-wrapper .swiper-pagination-progressbar'                 => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper::before' => 'background-color: {{VALUE}};opacity:1;',
				],
			]
		);

		$ref->add_control(
			'thumb_font_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Thumb Font Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .p-content *' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .swiper-thumb-wrapper .swiper-wrapper .p-content *' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'slider_thumb_bg_active',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Thumb Active/Hover Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide:hover .p-thumbnail img'         => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide-thumb-active .p-thumbnail img'  => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'thumb_wrapper_bg',
				'label'          => esc_html__( 'Thumb Wrapper Background', 'the-post-grid' ),
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Thumb Wrapper Background', 'the-post-grid' ),
					],
				],
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .tpg-el-main-wrapper .slider-thumb-main-wrapper, {{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper',
				'exclude'        => [ 'image' ],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Advanced Custom Field ACF Style
	 *
	 * @param $ref
	 */
	public static function tpg_acf_style( $ref ) {
		$cf = Fns::is_acf();
		if ( ! $cf || ! rtTPG()->hasPro() ) {
			return;
		}

		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'tgp_acf_style',
			[
				'label'     => esc_html__( 'Advanced Custom Field (ACF)', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_acf'           => 'show',
					$prefix . '_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],
			]
		);

		self::get_tpg_acf_style( $ref );

		$ref->end_controls_section();
	}

	public static function get_tpg_acf_style( $ref, $hover_control = true ) {
		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'acf_group_title_typography',
				'label'    => esc_html__( 'Group Title Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-cf-group-title',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'acf_typography',
				'label'    => esc_html__( 'ACF Fields Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-cf-fields',
			]
		);

		$ref->add_control(
			'acf_label_style',
			[
				'label'        => esc_html__( 'Label Style', 'the-post-grid' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'inline',
				'options'      => [
					'default' => esc_html__( 'Default', 'the-post-grid' ),
					'inline'  => esc_html__( 'Inline', 'the-post-grid' ),
					'block'   => esc_html__( 'Block', 'the-post-grid' ),
				],
				'condition'    => [
					'cf_show_only_value' => 'yes',
				],
				'render_type'  => 'template',
				'prefix_class' => 'act-label-style-',
			]
		);

		$ref->add_responsive_control(
			'acf_label_width',
			[
				'label'      => esc_html__( 'Label Min Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition'  => [
					'acf_label_style' => 'default',
				],
				'selectors'  => [
					'{{WRAPPER}} .tgp-cf-field-label' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$ref->add_control(
			'acf_alignment',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tpg-acf-align-',
				'toggle'       => true,
				'condition'    => [
					'grid_layout!' => [ 'grid-layout7', 'slider-layout4' ],
				],

			]
		);

		if ( $hover_control ) {
			// Start Tab
			$ref->start_controls_tabs(
				'acf_style_tabs'
			);

			// Normal Tab
			$ref->start_controls_tab(
				'acf_style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'the-post-grid' ),
				]
			);
		}
		$ref->add_control(
			'acf_group_title_color',
			[
				'label'     => esc_html__( 'Group Title Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tpg-cf-group-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cf_hide_group_title' => 'yes',
				],
			]
		);

		$ref->add_control(
			'acf_label_color',
			[
				'label'     => esc_html__( 'Label Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tgp-cf-field-label' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cf_show_only_value' => 'yes',
				],
			]
		);

		$ref->add_control(
			'acf_value_color',
			[
				'label'     => esc_html__( 'Value Color', 'the-post-grid' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tgp-cf-field-value' => 'color: {{VALUE}}',
				],
			]
		);

		if ( $hover_control ) {
			$ref->end_controls_tab();

			// Hover Tab
			$ref->start_controls_tab(
				'acf_style_hover_tab',
				[
					'label' => esc_html__( 'Box Hover', 'the-post-grid' ),
				]
			);

			$ref->add_control(
				'acf_group_title_color_hover',
				[
					'label'     => esc_html__( 'Group Title Color - Hover', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tpg-cf-group-title' => 'color: {{VALUE}}',
					],
					'condition' => [
						'cf_hide_group_title' => 'yes',
					],
				]
			);

			$ref->add_control(
				'acf_label_color_hover',
				[
					'label'     => esc_html__( 'Label Color - Hover', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-label' => 'color: {{VALUE}}',
					],
					'condition' => [
						'cf_show_only_value' => 'yes',
					],
				]
			);

			$ref->add_control(
				'acf_value_color_hover',
				[
					'label'     => esc_html__( 'Value Color - Hover', 'the-post-grid' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-value' => 'color: {{VALUE}}',
					],
				]
			);

			$ref->end_controls_tab();

			$ref->end_controls_tabs();
			// End Tab
		}
	}
}
