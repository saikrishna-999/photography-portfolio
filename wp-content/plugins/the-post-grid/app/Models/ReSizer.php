<?php
/**
 * Image resize Class
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Models;

use WP_Error;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Image resize Class
 */
class ReSizer {
	/**
	 * The singleton instance
	 */
	private static $instance = null;

	/**
	 * Should an RtTpgException be thrown on error?
	 * If false (default), then the error will just be logged.
	 */
	public $throwOnError = false;

	/**
	 * No cloning allowed
	 */
	private function __clone() {
	}

	/**
	 * For your custom default usage you may want to initialize an rtTPGReSizer object by yourself and then have own defaults
	 */
	public static function getInstance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run, forest.
	 */
	public function process( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		try {
			// Validate inputs.
			if ( ! $url ) {
				return new WP_Error(
					'broke',
					sprintf(
						/* Translators: %s URL */
						esc_html__( '%s parameter is required', 'the-post-grid' ),
						$url
					)
				);
			}
			if ( ! $width ) {
				return new WP_Error(
					'broke',
					sprintf(
						/* Translators: %s Width */
						esc_html__( '%s parameter is required', 'the-post-grid' ),
						$width
					)
				);
			}
			if ( ! $height ) {
				return new WP_Error(
					'broke',
					sprintf(
						/* Translators: %s Height */
						esc_html__( '%s parameter is required', 'the-post-grid' ),
						$height
					)
				);
			}

			// Caipt'n, ready to hook.
			if ( true === $upscale ) {
				add_filter( 'image_resize_dimensions', [ $this, 'aq_upscale' ], 10, 6 );
			}

			// Define upload path & dir.
			$upload_info = wp_upload_dir();
			$upload_dir  = $upload_info['basedir'];
			$upload_url  = $upload_info['baseurl'];

			$http_prefix     = 'http://';
			$https_prefix    = 'https://';
			$relative_prefix = '//'; // The protocol-relative URL

			/*
			 if the $url scheme differs from $upload_url scheme, make them match
			   if the schemes differe, images don't show up. */
			if ( ! strncmp( $url, $https_prefix, strlen( $https_prefix ) ) ) { // if url begins with https:// make $upload_url begin with https:// as well
				$upload_url = str_replace( $http_prefix, $https_prefix, $upload_url );
			} elseif ( ! strncmp( $url, $http_prefix, strlen( $http_prefix ) ) ) { // if url begins with http:// make $upload_url begin with http:// as well
				$upload_url = str_replace( $https_prefix, $http_prefix, $upload_url );
			} elseif ( ! strncmp( $url, $relative_prefix, strlen( $relative_prefix ) ) ) { // if url begins with // make $upload_url begin with // as well
				$upload_url = str_replace(
					[
						0 => "$http_prefix",
						1 => "$https_prefix",
					],
					$relative_prefix,
					$upload_url
				);
			}

			// Check if $img_url is local.
			if ( false === strpos( $url, $upload_url ) ) {
				return new WP_Error(
					'broke',
					sprintf(
						/* Translators: %s URL */
						esc_html__( 'Image must be local: %s', 'the-post-grid' ),
						$url
					)
				);
			}

			// Define path of image.
			$rel_path = str_replace( $upload_url, '', $url );
			$img_path = $upload_dir . $rel_path;

			// Check if img path exists, and is an image indeed.
			if ( ! file_exists( $img_path ) or ! getimagesize( $img_path ) ) {
				return new WP_Error( 'broke', 'Image file does not exist (or is not an image): ' . $img_path );
				// throw new RtTpgException( 'Image file does not exist (or is not an image): ' . $img_path );
			}

			// Get image info.
			$info                    = pathinfo( $img_path );
			$ext                     = $info['extension'];
			list( $orig_w, $orig_h ) = getimagesize( $img_path );

			// Get image size after cropping.
			$dims  = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
			$dst_w = is_array( $dims ) && isset( $dims[4] ) ? $dims[4] : $orig_w;
			$dst_h = is_array( $dims ) && isset( $dims[5] ) ? $dims[5] : $orig_h;

			// Return the original image only if it exactly fits the needed measures.
			if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
				$img_url = $url;
				$dst_w   = $orig_w;
				$dst_h   = $orig_h;
			} else {
				// Use this to check if cropped image already exists, so we can return that instead.
				$suffix       = "{$dst_w}x{$dst_h}";
				$dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
				$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

				if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
					// Can't resize, so return false saying that the action to do could not be processed as planned.
					return new WP_Error( 'broke', 'Image file does not exist (or is not an image): ' . $img_path );
					// throw new RtTpgException( 'Unable to resize image because image_resize_dimensions() failed' );
				} // Else check if cache exists.
				elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
					$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
				} // Else, we resize the image and return the new resized image url.
				else {

					$editor = wp_get_image_editor( $img_path );

					if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {
						return new WP_Error( 'broke', 'Image file does not exist (or is not an image): ' . $img_path );
						// throw new RtTpgException( 'Unable to get WP_Image_Editor: ' .
						// $editor->get_error_message() . ' (is GD or ImageMagick installed?)' );
					}

					$resized_file = $editor->save();

					if ( ! is_wp_error( $resized_file ) ) {
						$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
						$img_url          = $upload_url . $resized_rel_path;
					} else {
						return new WP_Error( 'broke', 'Image file does not exist (or is not an image): ' . $img_path );
						// throw new RtTpgException( 'Unable to save resized image file: ' . $editor->get_error_message() );
					}
				}
			}

			// Okay, leave the ship.
			if ( true === $upscale ) {
				remove_filter( 'image_resize_dimensions', [ $this, 'aq_upscale' ] );
			}

			// Return the output.
			if ( $single ) {
				// str return.
				$image = $img_url;
			} else {
				// array return.
				$image = [
					0 => $img_url,
					1 => $dst_w,
					2 => $dst_h,
				];
			}

			return $image;
		} catch ( RtTpgException $ex ) {

			if ( $this->throwOnError ) {
				// Bubble up exception.
				throw $ex;
			} else {
				// Return false, so that this patch is backwards-compatible.
				return false;
			}
		}
	}

	/**
	 * Callback to overwrite WP computing of thumbnail measures
	 */
	function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
		if ( ! $crop ) {
			return null;
		} // Let the WordPress default function handle this.

		// Here is the point we allow to use larger image size than the original one.
		$aspect_ratio = $orig_w / $orig_h;
		$new_w        = $dest_w;
		$new_h        = $dest_h;

		if ( ! $new_w ) {
			$new_w = intval( $new_h * $aspect_ratio );
		}

		if ( ! $new_h ) {
			$new_h = intval( $new_w / $aspect_ratio );
		}

		$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

		$crop_w = round( $new_w / $size_ratio );
		$crop_h = round( $new_h / $size_ratio );

		$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		$s_y = floor( ( $orig_h - $crop_h ) / 2 );

		return [ 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h ];
	}
}
