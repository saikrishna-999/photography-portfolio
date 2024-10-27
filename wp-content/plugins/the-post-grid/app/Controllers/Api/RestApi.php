<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

/**
 * RestApi Class
 */
class RestApi {
	/**
	 * Register rest route
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'init_rest_routes' ], 99 );
		new ImageSizeV1();
		new GetPostsV1();
		new GetTickerPostsV1();
		new ACFV1();
		new FrontEndFilterV1();
		//new ElImport();
		new GetCategories();
		new GetBuilderData();
	}

	/**
	 * Init rest route
	 *
	 * @return void
	 */
	public function init_rest_routes() {
		$this->rttpg_register_rest_fields();
	}

	function rttpg_register_rest_fields() {
		$post_type = Fns::get_post_types();

		foreach ( $post_type as $key => $value ) {

			// Featured image.
			register_rest_field(
				$key,
				'rttpg_featured_image_url',
				[
					'get_callback'    => [ $this, 'rttpg_get_featured_image_url' ],
					'update_callback' => null,
					'schema'          => [
						'description' => __( 'Different sized featured images' ),
						'type'        => 'array',
					],
				]
			);

			// Author info.
			register_rest_field(
				$key,
				'rttpg_author',
				[
					'get_callback'    => [ $this, 'rttpg_get_author_info' ],
					'update_callback' => null,
					'schema'          => null,
				]
			);

			// Add comment info.
			register_rest_field(
				$key,
				'rttpg_comment',
				[
					'get_callback'    => [ $this, 'rttpg_get_comment_info' ],
					'update_callback' => null,
					'schema'          => null,
				]
			);

			// Category links.
			register_rest_field(
				$key,
				'rttpg_category',
				[
					'get_callback'    => [ $this, 'rttpg_get_category_list' ],
					'update_callback' => null,
					'schema'          => [
						'description' => __( 'Category list links' ),
						'type'        => 'string',
					],
				]
			);

			// Excerpt.
			register_rest_field(
				$key,
				'rttpg_excerpt',
				[
					'get_callback'    => [ $this, 'rttpg_get_excerpt' ],
					'update_callback' => null,
					'schema'          => null,
				]
			);
		}
	}


	// Author.
	function rttpg_get_author_info( $object ) {
		$author = ( isset( $object['author'] ) ) ? $object['author'] : '';

		$author_data['display_name'] = get_the_author_meta( 'display_name', $author );
		$author_data['author_link']  = get_author_posts_url( $author );

		return $author_data;
	}

// Comment.
	function rttpg_get_comment_info( $object ) {
		$comments_count = wp_count_comments( $object['id'] );

		return $comments_count->total_comments;
	}

// Category list.

	function rttpg_get_category_list( $object ) {
		$taxonomies = get_post_taxonomies( $object['id'] );
		if ( 'post' === get_post_type() ) {
			return get_the_category_list( esc_html( ' ' ), '', $object['id'] );
		} else {
			if ( ! empty( $taxonomies ) ) {
				return get_the_term_list( $object['id'], $taxonomies[0], ' ' );
			}
		}
	}


	// Feature image.
	function rttpg_get_featured_image_url( $object ) {

		$featured_images = [];
		if ( ! isset( $object['featured_media'] ) ) {
			return $featured_images;
		}

		$image = wp_get_attachment_image_src( $object['featured_media'], 'full', false );
		if ( is_array( $image ) ) {
			$featured_images['full']      = $image;
			$featured_images['landscape'] = wp_get_attachment_image_src( $object['featured_media'], 'rttpg_landscape', false );
			$featured_images['portraits'] = wp_get_attachment_image_src( $object['featured_media'], 'rttpg_portrait', false );
			$featured_images['thumbnail'] = wp_get_attachment_image_src( $object['featured_media'], 'rttpg_thumbnail', false );

			$image_sizes = Fns::get_image_sizes();
			foreach ( $image_sizes as $key => $value ) {
				$size                     = $key;
				$featured_images[ $size ] = wp_get_attachment_image_src(
					$object['featured_media'],
					$size,
					false
				);
			}

			return $featured_images;
		}

	}

	// Excerpt.
	function rttpg_get_excerpt( $object ) {
		$excerpt = wp_trim_words( get_the_excerpt( $object['id'] ) );
		if ( ! $excerpt ) {
			$excerpt = null;
		}

		return $excerpt;
	}

}