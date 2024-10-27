<?php
/**
 * Options Helper class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Helpers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Options Helper class.
 */
class Options {

	public static function rtPostTypes() {
		$args = apply_filters(
			'tpg_get_post_type',
			[
				'public' => true,
			]
		);

		$post_types = get_post_types( $args );

		$exclude = [ 'attachment', 'revision', 'nav_menu_item', 'elementor_library', 'tpg_builder' ];

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

	public static function rtPostOrders() {
		return [
			'ASC'  => esc_html__( 'Ascending', 'the-post-grid' ),
			'DESC' => esc_html__( 'Descending', 'the-post-grid' ),
		];
	}

	public static function rtTermOperators() {
		return [
			'IN'     => esc_html__(
				'IN — show posts which associate with one or more of selected terms',
				'the-post-grid'
			),
			'NOT IN' => esc_html__(
				'NOT IN — show posts which do not associate with any of selected terms',
				'the-post-grid'
			),
			'AND'    => esc_html__( 'AND — show posts which associate with all of selected terms', 'the-post-grid' ),
		];
	}

	public static function rtTermRelations() {
		return [
			'AND' => esc_html__( 'AND — show posts which match all settings', 'the-post-grid' ),
			'OR'  => esc_html__( 'OR — show posts which match one or more settings', 'the-post-grid' ),
		];
	}

	public static function rtMetaKeyType() {
		return [
			'meta_value'          => esc_html__( 'Meta value', 'the-post-grid' ), //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'meta_value_num'      => esc_html__( 'Meta value number', 'the-post-grid' ),
			'meta_value_datetime' => esc_html__( 'Meta value datetime', 'the-post-grid' ),
		];
	}

	public static function rtPostOrderBy( $isWoCom = false, $metaOrder = false ) {
		$orderBy = [
			'title'      => esc_html__( 'Title', 'the-post-grid' ),
			'date'       => esc_html__( 'Created date', 'the-post-grid' ),
			'modified'   => esc_html__( 'Modified date', 'the-post-grid' ),
			'menu_order' => esc_html__( 'Menu Order', 'the-post-grid' ),
		];

		return apply_filters( 'rt_tpg_post_orderby', $orderBy, $isWoCom, $metaOrder );
	}

	public static function rtTPGSettingsCustomScriptFields() {
		$settings = get_option( rtTPG()->options['settings'] );

		return [
			'script_before_item_load' => [
				'label'       => esc_html__( 'Script before item load', 'the-post-grid' ),
				'type'        => 'textarea',
				'holderClass' => 'rt-script-wrapper full',
				'id'          => 'script-before-item-load',
				'value'       => isset( $settings['script_before_item_load'] ) ? stripslashes( $settings['script_before_item_load'] ) : null,
			],
			'script_after_item_load'  => [
				'label'       => esc_html__( 'Script After item load', 'the-post-grid' ),
				'type'        => 'textarea',
				'holderClass' => 'rt-script-wrapper full',
				'id'          => 'script-after-item-load',
				'value'       => isset( $settings['script_after_item_load'] ) ? stripslashes( $settings['script_after_item_load'] ) : null,
			],
			'script_loaded'           => [
				'label'       => esc_html__( 'After Loaded script', 'the-post-grid' ),
				'type'        => 'textarea',
				'holderClass' => 'rt-script-wrapper full',
				'id'          => 'script-loaded',
				'value'       => isset( $settings['script_loaded'] ) ? stripslashes( $settings['script_loaded'] ) : null,
			],
		];
	}

	public static function rtTPGSettingsOtherSettingsFields() {
		$settings = get_option( rtTPG()->options['settings'] );

		$other_settings = [
			'template_author'          => [
				'type'        => 'select',
				'name'        => 'template_author',
				'label'       => esc_html__( 'Template Author', 'the-post-grid' ),
				'id'          => 'template_author',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'blank'       => 'Select a layout',
				'options'     => Fns::getTPGShortCodeList(),
				'value'       => isset( $settings['template_author'] ) ? $settings['template_author'] : [],
			],
			'template_category'        => [
				'type'        => 'select',
				'name'        => 'template_category',
				'label'       => esc_html__( 'Template Category', 'the-post-grid' ),
				'id'          => 'template_category',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'blank'       => 'Select a layout',
				'options'     => Fns::getTPGShortCodeList(),
				'value'       => isset( $settings['template_category'] ) ? $settings['template_category'] : [],
			],
			'template_search'          => [
				'type'        => 'select',
				'name'        => 'template_search',
				'label'       => esc_html__( 'Template Search', 'the-post-grid' ),
				'id'          => 'template_search',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'blank'       => 'Select a layout',
				'options'     => Fns::getTPGShortCodeList(),
				'value'       => isset( $settings['template_search'] ) ? $settings['template_search'] : [],
			],
			'template_tag'             => [
				'type'        => 'select',
				'name'        => 'template_tag',
				'label'       => esc_html__( 'Template Tag', 'the-post-grid' ),
				'id'          => 'template_tag',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'blank'       => 'Select a layout',
				'options'     => Fns::getTPGShortCodeList(),
				'value'       => isset( $settings['template_tag'] ) ? $settings['template_tag'] : [],
			],

			'tpg_primary_color_main'   => [
				'type'    => 'text',
				'label'   => esc_html__( 'Primary Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_primary_color_main'] ) ? $settings['tpg_primary_color_main'] : '#0d6efd',
			],

			'tpg_secondary_color_main' => [
				'type'    => 'text',
				'label'   => esc_html__( 'Secondary Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_secondary_color_main'] ) ? $settings['tpg_secondary_color_main'] : '#0654c4',
			],

			'tpg_loader_color'         => [
				'type'    => 'text',
				'label'   => esc_html__( 'Preloader Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_loader_color'] ) ? $settings['tpg_loader_color'] : '#0367bf',
			],

			'template_class'           => [
				'type'        => 'text',
				'name'        => 'template_class',
				'label'       => esc_html__( 'Template class', 'the-post-grid' ),
				'holderClass' => 'pro-field',
				'id'          => 'template_class',
				'value'       => isset( $settings['template_class'] ) ? $settings['template_class'] : '',
			],
		];

		$insert_array = [
			'tpg_popupbar_color'      => [
				'type'    => 'text',
				'label'   => esc_html__( 'Popup Topbar Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_popupbar_color'] ) ? $settings['tpg_popupbar_color'] : '',
			],
			'tpg_popupbar_bg_color'   => [
				'type'    => 'text',
				'label'   => esc_html__( 'Popup Background Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_popupbar_bg_color'] ) ? $settings['tpg_popupbar_bg_color'] : '',
			],
			'tpg_popupbar_text_color' => [
				'type'    => 'text',
				'label'   => esc_html__( 'Popup Text Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => isset( $settings['tpg_popupbar_text_color'] ) ? $settings['tpg_popupbar_text_color'] : '',
			],
		];

		Fns::array_insert( $other_settings, 6, $insert_array );

		$plugin = Fns::is_acf();

		if ( $plugin ) {
			$acf_settings = [
				'show_acf_details'            => [
					'type'        => 'switch',
					'name'        => 'show_acf_details',
					'label'       => esc_html__( 'Enable Advanced Custom Field (ACF) for Single page', 'the-post-grid' ),
					'description' => esc_html__( 'You may enable advanced custom field (ACF) on details page', 'the-post-grid' ),
					'holderClass' => 'pro-field',
					'value'       => isset( $settings['show_acf_details'] ) ? $settings['show_acf_details'] : false,
				],

				'cf_group_details'            => [
					'type'        => 'checkbox',
					'name'        => 'cf_group_details',
					'label'       => esc_html__( 'Choose ACF Group', 'the-post-grid' ),
					'id'          => 'cf_group_details',
					'holderClass' => 'pro-field',
					'alignment'   => 'vertical',
					'multiple'    => true,
					'options'     => Fns::get_groups_by_post_type( 'all' ),
					'value'       => isset( $settings['cf_group_details'] ) ? $settings['cf_group_details'] : [],
				],

				'cf_hide_empty_value_details' => [
					'type'        => 'switch',
					'name'        => 'cf_hide_empty_value_details',
					'label'       => esc_html__( 'Hide field with empty value', 'the-post-grid' ),
					'value'       => isset( $settings['cf_hide_empty_value_details'] ) ? $settings['cf_hide_empty_value_details'] : false,
					'holderClass' => 'pro-field',
				],

				'cf_show_only_value_details'  => [
					'type'        => 'switch',
					'name'        => 'cf_show_only_value_details',
					'label'       => esc_html__( 'Show Title', 'the-post-grid' ),
					'description' => esc_html__( 'By default both name & value of field is shown', 'the-post-grid' ),
					'value'       => isset( $settings['cf_show_only_value_details'] ) ? $settings['cf_show_only_value_details'] : true,
					'holderClass' => 'pro-field',
				],

				'cf_hide_group_title_details' => [
					'type'        => 'switch',
					'name'        => 'cf_hide_group_title_details',
					'label'       => esc_html__( 'Show group title', 'the-post-grid' ),
					'value'       => isset( $settings['cf_hide_group_title_details'] ) ? $settings['cf_hide_group_title_details'] : false,
					'holderClass' => 'pro-field',
				],
			];

			$other_settings = array_merge( $other_settings, $acf_settings );
		}

		return $other_settings;
	}

	public static function rtTPGChatGPGSettings() {
		$settings = get_option( rtTPG()->options['settings'] );

		$other_settings = [

			'chatgpt_status'        => [
				'type'  => 'switch',
				'name'  => 'chatgpt_status',
				'label' => esc_html__( 'Enable ChatGPT', 'the-post-grid' ),
				'value' => isset( $settings['chatgpt_status'] ) ? $settings['chatgpt_status'] : false,
			],

			'chatgpt_secret_key'    => [
				'type'        => 'text',
				'name'        => 'chatgpt_secret_key',
				'label'       => esc_html__( 'OpenAI API Secret Key', 'the-post-grid' ),
				'id'          => 'template_author',
				'holderClass' => 'pro-field',
				'description' => __( 'Enter the OpenAI API Secret Key <a target="_blank" href="https://platform.openai.com/account/api-keys">Make your own API</a>', 'the-post-grid' ),
				'value'       => isset( $settings['chatgpt_secret_key'] ) ? $settings['chatgpt_secret_key'] : '',
			],
			'chatgpt_model'         => [
				'type'        => 'select',
				'name'        => 'chatgpt_model',
				'label'       => esc_html__( 'OpenAI Model', 'the-post-grid' ),
				'id'          => 'chatgpt_model',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'description' => __( 'Choose OpenAI model. Default: gpt-3.5-turbo', 'the-post-grid' ),
				'options'     => [
					'gpt-3.5-turbo'    => 'gpt-3.5-turbo',
					'text-davinci-002' => 'text-davinci-002',
					'text-davinci-003' => 'text-davinci-003',
					'gpt-4'            => 'gpt-4',
				],
				'value'       => isset( $settings['chatgpt_model'] ) ? $settings['chatgpt_model'] : 'gpt-3.5-turbo',
			],
			'chatgpt_response_time' => [
				'type'        => 'number',
				'name'        => 'chatgpt_response_time',
				'label'       => esc_html__( 'Response Time', 'the-post-grid' ),
				'id'          => 'chatgpt_response_time',
				'holderClass' => 'pro-field',
				'description' => __( 'Choose OpenAI response time', 'the-post-grid' ),
				'value'       => isset( $settings['chatgpt_response_time'] ) ? $settings['chatgpt_response_time'] : 60,
			],
			'chatgpt_max_tokens'    => [
				'type'        => 'number',
				'name'        => 'chatgpt_max_tokens',
				'label'       => esc_html__( 'Max Tokens', 'the-post-grid' ),
				'id'          => 'chatgpt_max_tokens',
				'holderClass' => 'pro-field',
				'description' => __( 'Enter OpenAi max token number', 'the-post-grid' ),
				'value'       => isset( $settings['chatgpt_max_tokens'] ) ? $settings['chatgpt_max_tokens'] : 1200,
			],

		];

		return $other_settings;
	}


	public static function rtTPGCMyAccountSettings() {
		$settings = get_option( rtTPG()->options['settings'] );

		$other_settings = [

			'tpg_myaccount'      => [
				'type'        => 'select',
				'name'        => 'tpg_myaccount',
				'label'       => 'My Account Page',
				'id'          => 'tpg_myaccount',
				'class'       => 'select2',
				'options'     => Fns::get_pages(),
				'description' => esc_html__( 'Choose My Account Page. Use must use the shortcode [tpg_my_account] in the page.', 'the-post-grid' ),
				'value'       => isset( $settings['tpg_myaccount'] ) ? $settings['tpg_myaccount'] : '',
			],

			'post_status'        => [
				'type'        => 'select',
				'name'        => 'post_status',
				'label'       => esc_html__( 'Post Status', 'the-post-grid' ),
				'id'          => 'post_status',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'description' => __( 'Submitted Post Status', 'the-post-grid' ),
				'options'     => [
					'pending' => esc_html__( 'Pending Review', 'the-post-grid' ),
					'draft'   => esc_html__( 'Draft', 'the-post-grid' ),
					'private' => esc_html__( 'Private', 'the-post-grid' ),
					'publish' => esc_html__( 'Publish', 'the-post-grid' ),
				],
				'value'       => isset( $settings['post_status'] ) ? $settings['post_status'] : 'pending',
			],

			'max_upload_file'    => [
				'type'        => 'text',
				'name'        => 'max_upload_file',
				'label'       => esc_html__( 'Max Image Size', 'the-post-grid' ),
				'id'          => 'max_upload_file',
				'holderClass' => 'pro-field',
				'description' => __( 'Input max upload image size KB. Default is: 1024 (1MB)', 'the-post-grid' ),
				'options'     => [
					'pending' => esc_html__( 'Pending Review', 'the-post-grid' ),
					'draft'   => esc_html__( 'Draft', 'the-post-grid' ),
					'private' => esc_html__( 'Private', 'the-post-grid' ),
					'publish' => esc_html__( 'Publish', 'the-post-grid' ),
				],
				'value'       => isset( $settings['max_upload_file'] ) ? $settings['max_upload_file'] : '1024',
			],

			'delete_post_status' => [
				'type'        => 'select',
				'name'        => 'delete_post_status',
				'label'       => esc_html__( 'Deleted Post', 'the-post-grid' ),
				'id'          => 'delete_post_status',
				'holderClass' => 'pro-field',
				'class'       => 'select2',
				'description' => __( 'What will it happen when the user delete the post?', 'the-post-grid' ),
				'options'     => [
					'trash'  => esc_html__( 'Go to Trash', 'the-post-grid' ),
					'delete' => esc_html__( 'Delete Forever', 'the-post-grid' ),
				],
				'value'       => isset( $settings['delete_post_status'] ) ? $settings['delete_post_status'] : 'trash',
			],

		];

		return $other_settings;
	}

	public static function rtTPGSettingsCommonSettingsFields() {
		$settings   = get_option( rtTPG()->options['settings'] );
		$block_type = $settings['tpg_block_type'] ?? 'default';

		$common_settings = [
			'tpg_common_settings_heading' => [
				'type'        => 'heading',
				'name'        => 'tpg_common_settings_heading',
				'label'       => esc_html__( 'Improve Performance', 'the-post-grid' ),
				'description' => esc_html__( 'Please choose a Resource Load Type first. Otherwise, all CSS & JS for shortcode, gutenberg and elementor will load on your site which can create a bad performance issues.', 'the-post-grid' ),
			],

			'tpg_block_type'              => [
				'type'        => 'select',
				'name'        => 'tpg_block_type',
				'label'       => 'Resource Load Type',
				'id'          => 'tpg_block_type',
				'class'       => 'select2',
				'options'     => [
					'default'   => esc_html__( 'Default (Shortcode add Elementor / Gutenberg)', 'the-post-grid' ),
					'elementor' => esc_html__( 'Elementor / Gutenberg', 'the-post-grid' ),
					'shortcode' => esc_html__( 'Shortcode Only', 'the-post-grid' ),
				],
				'description' => esc_html__( 'Please choose which type of block you want to use. If you select Default then all styles and scripts will load on your site. But if you use one then just this style and script will load on your site.', 'the-post-grid' ),
				'value'       => isset( $settings['tpg_block_type'] ) ? $settings['tpg_block_type'] : 'default',
			],
			'tpg_load_script'             => [
				'type'        => 'switch',
				'name'        => 'tpg_load_script',
				'label'       => esc_html__( 'Load Script dependent on block', 'the-post-grid' ),
				'description' => sprintf(
					'%s<b>%s</b>',
					esc_html__( 'Check, if you want to load script when ShortCode or Elementor block is used on a page. ', 'the-post-grid' ),
					esc_html__( 'If you enable this then you must have to enable Preloader from below.', 'the-post-grid' )
				),
				'value'       => isset( $settings['tpg_load_script'] ) ? $settings['tpg_load_script'] : false,
			],
			'tpg_enable_preloader'        => [
				'type'  => 'switch',
				'name'  => 'tpg_enable_preloader',
				'label' => esc_html__( 'Enable Pre-loader', 'the-post-grid' ),
				'value' => isset( $settings['tpg_enable_preloader'] ) ? $settings['tpg_enable_preloader'] : false,
			],
			'tpg_skip_fa'                 => [
				'type'        => 'switch',
				'name'        => 'tpg_skip_fa',
				'label'       => esc_html__( 'Disable Font Awesome Script', 'the-post-grid' ),
				'description' => esc_html__( "If Font Awesome 5 exist with theme, don't need to load twice.", 'the-post-grid' ),
				'value'       => isset( $settings['tpg_skip_fa'] ) ? $settings['tpg_skip_fa'] : false,
			],
			'tpg_icon_font'               => [
				'type'        => 'select',
				'name'        => 'tpg_icon_font',
				'label'       => 'Icon Type',
				'id'          => 'tpg_icon_font',
				'class'       => 'select2',
				'options'     => [
					'fontawesome' => esc_html__( 'Fontawesome Icon', 'the-post-grid' ),
					'flaticon'    => esc_html__( 'Flat icon', 'the-post-grid' ),
				],
				'description' => esc_html__( 'You can change icon font from here', 'the-post-grid' ),
				'value'       => isset( $settings['tpg_icon_font'] ) ? $settings['tpg_icon_font'] : 'fontawesome',
			],

			'tpg_pagination_range'        => [
				'type'        => 'number',
				'label'       => esc_html__( 'Pagination Range', 'the-post-grid' ),
				'description' => esc_html__( 'If the pagination items are greater than 4 it will enable the next and previous button.', 'the-post-grid' ),
				'value'       => $settings['tpg_pagination_range'] ?? '4',
			],

			'tpg_enable_external_link'    => [
				'type'        => 'switch',
				'name'        => 'tpg_enable_external_link',
				'holderClass' => 'pro-field',
				'label'       => esc_html__( 'Enable External Post Link', 'the-post-grid' ),
				'value'       => isset( $settings['tpg_enable_external_link'] ) ? $settings['tpg_enable_external_link'] : false,
			],

			'tpg_br1'                     => [
				'type' => 'hr',
			],

			'tpg_view_count_style'        => [
				'type'        => 'select',
				'name'        => 'tpg_view_count_style',
				'label'       => esc_html__( 'Post View Count Format', 'the-post-grid' ),
				'value'       => $settings['tpg_view_count_style'] ?? 'default',
				'holderClass' => 'pro-field',
				'options'     => [
					'default' => esc_html__( 'Default number', 'the-post-grid' ),
					'global'  => esc_html__( 'Global shorten (K, M, B, T, Qa, Qi)', 'the-post-grid' ),
					'indian'  => esc_html__( 'Indian shorten (K, Lac, Lacs, Cr, Crs)', 'the-post-grid' ),
				],
			],

			'tpg_count_key'               => [
				'type'        => 'text',
				'holderClass' => 'pro-field',
				'label'       => esc_html__( 'Post View Count Key', 'the-post-grid' ),
				'description' => esc_html__( 'If you already have the post-view count functionality in your theme, you have to put the key here to sync with the existing count value. NB. Please enter the correct key otherwise the post view count won\'t show.', 'the-post-grid' ),
				'value'       => $settings['tpg_count_key'] ?? '',
			],
		];

		if ( 'default' != $block_type ) {
			unset( $common_settings['tpg_common_settings_heading'] );
		}

		return $common_settings;
	}

	public static function rtTPGLicenceField() {
		$settings       = get_option( rtTPG()->options['settings'] );
		$status         = ! empty( $settings['license_status'] ) && $settings['license_status'] === 'valid' ? true : false;
		$license_status = ! empty( $settings['license_key'] ) ? sprintf(
			"<span class='license-status'>%s</span>",
			$status
				? "<input type='submit' class='button-secondary rt-licensing-btn danger' name='license_deactivate' value='" . esc_html__( 'Deactivate License', 'the-post-grid' ) . "'/>"
				: "<input type='submit' class='button-secondary rt-licensing-btn button-primary' name='license_activate' value='" . esc_html__( 'Activate License', 'the-post-grid' ) . "'/>"
		) : ' ';

		return [
			'license_key' => [
				'type'            => 'text',
				'name'            => 'license_key',
				'attr'            => 'style="min-width:300px;"',
				'label'           => esc_html__( 'Enter your license key', 'the-post-grid' ),
				'description_adv' => Fns::htmlKses( $license_status, 'advanced' ),
				'id'              => 'license_key',
				'value'           => isset( $settings['license_key'] ) ? $settings['license_key'] : '',
			],
		];
	}

	public static function rtTPGSettingsSocialShareFields() {
		$settings = get_option( rtTPG()->options['settings'] );

		return [
			'social_share_items' => [
				'type'        => 'checkbox',
				'name'        => 'social_share_items',
				'label'       => esc_html__( 'Social share items', 'the-post-grid' ),
				'id'          => 'social_share_items',
				'holderClass' => 'pro-field',
				'alignment'   => 'vertical',
				'multiple'    => true,
				'options'     => self::socialShareItemList(),
				'value'       => isset( $settings['social_share_items'] ) ? $settings['social_share_items'] : [],
			],
		];
	}

	public static function socialShareItemList() {
		return [
			'facebook'  => esc_html__( 'Facebook', 'the-post-grid' ),
			'twitter'   => esc_html__( 'Twitter', 'the-post-grid' ),
			'linkedin'  => esc_html__( 'LinkedIn', 'the-post-grid' ),
			'pinterest' => esc_html__( 'Pinterest', 'the-post-grid' ),
			'reddit'    => esc_html__( 'Reddit', 'the-post-grid' ),
			'email'     => esc_html__( 'Email', 'the-post-grid' ),
		];
	}

	public static function templateOverrideItemList() {
		return [
			'category-archive' => esc_html__( 'Category archive', 'the-post-grid' ),
			'tag-archive'      => esc_html__( 'Tag archive', 'the-post-grid' ),
			'author-archive'   => esc_html__( 'Author archive', 'the-post-grid' ),
			'search'           => esc_html__( 'Search page', 'the-post-grid' ),
		];
	}

	public static function rtTPGCommonFilterFields() {
		return [
			'post__in'     => [
				'name'        => 'post__in',
				'label'       => esc_html__( 'Include only', 'the-post-grid' ),
				'type'        => 'text',
				'class'       => 'full',
				'description' => esc_html__( 'List of post IDs to show (comma-separated values, for example: 1,2,3)', 'the-post-grid' ),
			],
			'post__not_in' => [ //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'name'        => 'post__not_in',
				'label'       => esc_html__( 'Exclude', 'the-post-grid' ),
				'type'        => 'text',
				'class'       => 'full',
				'description' => esc_html__( 'List of post IDs to hide (comma-separated values, for example: 1,2,3)', 'the-post-grid' ),
			],
			'limit'        => [
				'name'        => 'limit',
				'label'       => esc_html__( 'Limit', 'the-post-grid' ),
				'type'        => 'number',
				'class'       => 'full',
				'description' => esc_html__( 'The number of posts to show. Set empty to show all found posts.', 'the-post-grid' ),
			],
			'offset'       => [
				'name'        => 'offset',
				'label'       => esc_html__( 'Offset', 'the-post-grid' ),
				'type'        => 'number',
				'class'       => 'full',
				'description' => esc_html__( 'The number of posts to skip from start', 'the-post-grid' ),
			],
		];
	}

	public static function rtTPGPostType() {
		return [
			'tpg_post_type' => [
				'label'   => esc_html__( 'Post Type', 'the-post-grid' ),
				'type'    => 'select',
				'id'      => 'rt-sc-post-type',
				'class'   => '-rt-select2',
				'options' => self::rtPostTypes(),
			],
		];
	}

	public static function rtTPAdvanceFilters() {
		$fields = apply_filters(
			'rt_tpg_advanced_filters',
			[
				'tpg_taxonomy' => esc_html__( 'Taxonomy', 'the-post-grid' ),
				'order'        => esc_html__( 'Order', 'the-post-grid' ),
				'author'       => esc_html__( 'Author', 'the-post-grid' ),
				's'            => esc_html__( 'Search', 'the-post-grid' ),
				// 'tpg_post_status' => esc_html__( 'Status', 'the-post-grid' ),
			]
		);

		return [
			'post_filter' => [
				'type'      => 'checkboxFilter',
				'name'      => 'post_filter',
				'label'     => esc_html__( 'Advanced Filters', 'the-post-grid' ),
				'alignment' => 'vertical',
				'multiple'  => true,
				'default'   => [ 'tpg_taxonomy', 'order' ],
				'options'   => $fields,
			],
		];
	}

	public static function rtTPGPostStatus() {
		return [
			'publish'    => esc_html__( 'Publish', 'the-post-grid' ),
			'pending'    => esc_html__( 'Pending', 'the-post-grid' ),
			'draft'      => esc_html__( 'Draft', 'the-post-grid' ),
			'auto-draft' => esc_html__( 'Auto draft', 'the-post-grid' ),
			'future'     => esc_html__( 'Future', 'the-post-grid' ),
			'private'    => esc_html__( 'Private', 'the-post-grid' ),
			'inherit'    => esc_html__( 'Inherit', 'the-post-grid' ),
			'trash'      => esc_html__( 'Trash', 'the-post-grid' ),
		];
	}

	public static function owl_property() {
		return [
			'auto_play'   => esc_html__( 'Auto Play', 'the-post-grid' ),
			'loop'        => esc_html__( 'Loop', 'the-post-grid' ),
			'nav_button'  => esc_html__( 'Nav Button', 'the-post-grid' ),
			'pagination'  => esc_html__( 'Pagination', 'the-post-grid' ),
			'stop_hover'  => esc_html__( 'Stop Hover', 'the-post-grid' ),
			'auto_height' => esc_html__( 'Auto Height', 'the-post-grid' ),
			'lazy_load'   => esc_html__( 'Lazy Load', 'the-post-grid' ),
			'rtl'         => esc_html__( 'Right to left (RTL)', 'the-post-grid' ),
		];
	}

	public static function rtTPGLayoutSettingFields() {
		$options = [
			'layout_type'                      => [
				'type'    => 'radio-image',
				'label'   => esc_html__( 'Layout Type', 'the-post-grid' ),
				'id'      => 'rt-tpg-sc-layout-type',
				'options' => self::rtTPGLayoutType(),
			],
			'layout'                           => [
				'type'    => 'radio-image',
				'label'   => esc_html__( 'Layout', 'the-post-grid' ),
				'id'      => 'rt-tpg-sc-layout',
				'class'   => 'rt-select2',
				'options' => self::rtTPGLayouts(),
			],
			'tgp_filter'                       => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Filter', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter tpg-hidden pro-field',
				'multiple'    => true,
				'alignment'   => 'vertical',
				'options'     => self::tgp_filter_list(),
			],
			'tgp_filter_taxonomy'              => [
				'type'        => 'select',
				'label'       => esc_html__( 'Taxonomy Filter', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'class'       => 'rt-select2',
				'options'     => Fns::rt_get_taxonomy_for_filter(),
			],
			'tgp_filter_taxonomy_hierarchical' => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Display as sub category', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'option'      => 'Active',
			],
			'tgp_filter_type'                  => [
				'type'        => 'select',
				'label'       => esc_html__( 'Taxonomy filter type', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'class'       => 'rt-select2',
				'options'     => self::rt_filter_type(),
			],
			'tgp_default_filter'               => [
				'type'        => 'select',
				'label'       => esc_html__( 'Selected filter term (Selected item)', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'class'       => 'rt-select2',
				'attr'        => "data-selected='" . get_post_meta( get_the_ID(), 'tgp_default_filter', true ) . "'",
				'options'     => [ '' => esc_html__( 'Show All', 'the-post-grid' ) ],
			],
			'tpg_hide_all_button'              => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Hide All (Show all) button', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'option'      => 'Hide',
			],
			'tpg_post_count'                   => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Show post count', 'the-post-grid' ),
				'holderClass' => 'sc-tpg-grid-filter sc-tpg-filter tpg-hidden',
				'option'      => 'Enable',
			],
			'isotope_filter'                   => [
				'type'        => 'select',
				'label'       => esc_html__( 'Isotope Filter', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-filter tpg-hidden',
				'id'          => 'rt-tpg-sc-isotope-filter',
				'class'       => 'rt-select2',
				'options'     => Fns::rt_get_taxonomy_for_filter(),
			],
			'isotope_default_filter'           => [
				'type'        => 'select',
				'label'       => esc_html__( 'Isotope filter (Selected item)', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-default-filter tpg-hidden pro-field',
				'id'          => 'rt-tpg-sc-isotope-default-filter',
				'class'       => 'rt-select2',
				'attr'        => "data-selected='" . get_post_meta(
					get_the_ID(),
					'isotope_default_filter',
					true
				) . "'",
				'options'     => [ '' => esc_html__( 'Show all', 'the-post-grid' ) ],
			],
			'tpg_show_all_text'                => [
				'type'        => 'text',
				'holderClass' => 'isotope-item sc-isotope-filter tpg-hidden',
				'label'       => esc_html__( 'Show all text', 'the-post-grid' ),
				'default'     => esc_html__( 'Show all', 'the-post-grid' ),
			],
			'isotope_filter_dropdown'          => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Isotope dropdown filter', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-filter sc-isotope-filter-dropdown tpg-hidden pro-field',
			],
			'isotope_filter_show_all'          => [
				'type'        => 'switch',
				'name'        => 'isotope_filter_show_all',
				'label'       => esc_html__( 'Hide Isotope filter (Show All item)', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-filter-show-all tpg-hidden pro-field',
				'id'          => 'rt-tpg-sc-isotope-filter-show-all',
			],
			'isotope_filter_count'             => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Isotope filter count number', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-filter tpg-hidden pro-field',
				'option'      => 'Enable',
			],
			'isotope_filter_url'               => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Isotope filter URL', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-filter tpg-hidden pro-field',
			],
			'isotope_search_filter'            => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Isotope search filter', 'the-post-grid' ),
				'holderClass' => 'isotope-item sc-isotope-search-filter tpg-hidden pro-field',
				'id'          => 'rt-tpg-sc-isotope-search-filter',
				'option'      => 'Enable',
			],
			'carousel_property'                => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Carousel property', 'the-post-grid' ),
				'multiple'    => true,
				'alignment'   => 'vertical',
				'holderClass' => 'carousel-item carousel-property tpg-hidden',
				'id'          => 'carousel-property',
				'default'     => [ 'pagination' ],
				'options'     => self::owl_property(),
			],
			'tpg_carousel_speed'               => [
				'label'       => esc_html__( 'Speed', 'the-post-grid' ),
				'holderClass' => 'tpg-hidden carousel-item',
				'type'        => 'number',
				'default'     => 250,
				'description' => esc_html__( 'Auto play Speed in milliseconds', 'the-post-grid' ),
			],
			'tpg_carousel_autoplay_timeout'    => [
				'label'       => esc_html__( 'Autoplay timeout', 'the-post-grid' ),
				'holderClass' => 'tpg-hidden carousel-item tpg-carousel-auto-play-timeout',
				'type'        => 'number',
				'default'     => 5000,
				'description' => esc_html__( 'Autoplay interval timeout', 'the-post-grid' ),
			],
		];

		return apply_filters( 'rt_tpg_layout_options', $options );
	}

	public static function responsiveSettingsColumn() {
		$options = [
			'column'            => [
				'type'        => 'select',
				'label'       => esc_html__( 'Desktop', 'the-post-grid' ),
				'class'       => 'rt-select2',
				'holderClass' => 'offset-column-wrap rt-3-column',
				'default'     => 3,
				'options'     => self::scColumns(),
				'description' => esc_html__( 'Desktop > 991px', 'the-post-grid' ),
			],
			'tpg_tab_column'    => [
				'type'        => 'select',
				'label'       => esc_html__( 'Tab', 'the-post-grid' ),
				'class'       => 'rt-select2',
				'holderClass' => 'offset-column-wrap rt-3-column',
				'default'     => 2,
				'options'     => self::scColumns(),
				'description' => esc_html__( 'Tab < 992px', 'the-post-grid' ),
			],
			'tpg_mobile_column' => [
				'type'        => 'select',
				'label'       => esc_html__( 'Mobile', 'the-post-grid' ),
				'class'       => 'rt-select2',
				'holderClass' => 'offset-column-wrap rt-3-column',
				'default'     => 1,
				'options'     => self::scColumns(),
				'description' => esc_html__( 'Mobile < 768px', 'the-post-grid' ),
			],
		];

		return apply_filters( 'rt_tpg_layout_column_options', $options );
	}

	public static function layoutMiscSettings() {
		$options = [
			'pagination'            => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Pagination', 'the-post-grid' ),
				'holderClass' => 'pagination',
				'id'          => 'rt-tpg-pagination',
				'description' => esc_html__( 'Pagination not allow in Grid Hover layout', 'the-post-grid' ),
				'option'      => 'Enable',
				'default'     => 1,
			],

			'posts_loading_type'    => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Pagination Type', 'the-post-grid' ),
				'holderClass' => 'pagination-item posts-loading-type tpg-hidden pro-field',
				'alignment'   => 'vertical',
				'default'     => 'pagination',
				'options'     => self::postLoadingType(),
			],

			'posts_per_page'        => [
				'type'        => 'number',
				'label'       => esc_html__( 'Display per page', 'the-post-grid' ),
				'holderClass' => 'posts-per-page',
				'default'     => 9,
				'description' => esc_html__( 'If value of Limit setting is not blank (empty), this value should be smaller than Limit value.', 'the-post-grid' ),
			],

			'load_more_text'        => [
				'type'        => 'text',
				'name'        => 'load_more_text',
				'label'       => esc_html__( 'Load More Text', 'the-post-grid' ),
				'holderClass' => 'pagination-load-more-label tpg-hidden pro-field',
				'id'          => 'template_class',
				'value'       => isset( $settings['load_more_text'] ) ? $settings['load_more_text'] : '',
			],

			'link_to_detail_page'   => [
				'type'      => 'switch',
				'label'     => esc_html__( 'Link To Detail Page', 'the-post-grid' ),
				'alignment' => 'vertical',
				'default'   => true,
			],
			'detail_page_link_type' => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Detail page link type', 'the-post-grid' ),
				'holderClass' => 'detail-page-link-type tpg-hidden pro-field',
				'alignment'   => 'vertical',
				'default'     => 'new_page',
				'options'     => [
					'new_page' => esc_html__( 'New Page', 'the-post-grid' ),
					'popup'    => esc_html__( 'PopUp', 'the-post-grid' ),
				],
			],
			'popup_type'            => [
				'type'        => 'radio',
				'label'       => esc_html__( 'PopUp Type', 'the-post-grid' ),
				'holderClass' => 'popup-type tpg-hidden pro-field',
				'alignment'   => 'vertical',
				'default'     => 'single',
				'options'     => [
					'single' => esc_html__( 'Single PopUp', 'the-post-grid' ),
					'multi'  => esc_html__( 'Multi PopUp', 'the-post-grid' ),
				],
			],
			'link_target'           => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Link Target', 'the-post-grid' ),
				'holderClass' => 'tpg-link-target tpg-hidden',
				'alignment'   => 'vertical',
				'options'     => [
					''       => esc_html__( 'Same Window', 'the-post-grid' ),
					'_blank' => esc_html__( 'New Window', 'the-post-grid' ),
				],
			],
		];

		return apply_filters( 'rt_tpg_layout_misc_options', $options );
	}

	public static function stickySettings() {
		$options = [
			'ignore_sticky_posts' => [
				'type'        => 'switch',
				'label'       => esc_html__( 'Show sticky posts at the top', 'the-post-grid' ),
				'holderClass' => 'pro-field',
				'alignment'   => 'vertical',
				'default'     => false,
			],
		];

		return $options;
	}

	public static function scMarginOpt() {
		return [
			'default' => esc_html__( 'Bootstrap default', 'the-post-grid' ),
			'no'      => esc_html__( 'No Margin', 'the-post-grid' ),
		];
	}

	function scGridType() {
		return [
			'even'    => esc_html__( 'Even Grid', 'the-post-grid' ),
			'masonry' => esc_html__( 'Masonry', 'the-post-grid' ),
		];
	}

	public static function getTitleTags() {
		return [
			'h2' => esc_html__( 'H2', 'the-post-grid' ),
			'h3' => esc_html__( 'H3', 'the-post-grid' ),
			'h4' => esc_html__( 'H4', 'the-post-grid' ),
		];
	}

	public static function getHeadingTags() {
		return [
			'h1' => esc_html__( 'H1', 'the-post-grid' ),
			'h2' => esc_html__( 'H2', 'the-post-grid' ),
			'h3' => esc_html__( 'H3', 'the-post-grid' ),
			'h4' => esc_html__( 'H4', 'the-post-grid' ),
			'h5' => esc_html__( 'H5', 'the-post-grid' ),
			'h6' => esc_html__( 'H6', 'the-post-grid' ),
		];
	}

	public static function rtTpgSettingsDetailFieldSelection() {
		$settings = get_option( rtTPG()->options['settings'] );

		$fields = [
			'popup_fields' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Field Selection', 'the-post-grid' ),
				'id'          => 'popup-fields',
				'holderClass' => 'pro-field',
				'alignment'   => 'vertical',
				'multiple'    => true,
				'options'     => self::detailAvailableFields(),
				'default'     => array_keys( self::detailAvailableFields() ),
				'value'       => isset( $settings['popup_fields'] ) ? $settings['popup_fields'] : [],
			],
		];
		$cf     = Fns::is_acf();
		if ( $cf ) {
			$plist                         = self::getCFPluginList();
			$pName                         = ! empty( $plist[ $cf ] ) ? $plist[ $cf ] : ' - ';
			$fields['cf_group']            = [
				'type'        => 'checkbox',
				'name'        => 'cf_group',
				'holderClass' => 'tpg-hidden cfs-fields cf-group pro-field',
				'label'       => 'Custom Field group ' . " ({$pName})",
				'multiple'    => true,
				'alignment'   => 'vertical',
				'id'          => 'cf_group',
				'options'     => Fns::get_groups_by_post_type( 'all' ),
				'value'       => isset( $settings['cf_group'] ) ? $settings['cf_group'] : [],
			];
			$fields['cf_hide_empty_value'] = [
				'type'        => 'checkbox',
				'name'        => 'cf_hide_empty_value',
				'holderClass' => 'tpg-hidden cfs-fields pro-field',
				'label'       => esc_html__( 'Hide field with empty value', 'the-post-grid' ),
				'value'       => ! empty( $settings['cf_hide_empty_value'] ) ? 1 : 0,
			];
			$fields['cf_show_only_value']  = [
				'type'        => 'checkbox',
				'name'        => 'cf_show_only_value',
				'holderClass' => 'tpg-hidden cfs-fields pro-field',
				'label'       => esc_html__( 'Show only value of field', 'the-post-grid' ),
				'description' => esc_html__( 'By default both name & value of field is shown', 'the-post-grid' ),
				'value'       => ! empty( $settings['cf_show_only_value'] ) ? 1 : 0,
			];
			$fields['cf_hide_group_title'] = [
				'type'        => 'checkbox',
				'name'        => 'cf_hide_group_title',
				'holderClass' => 'tpg-hidden cfs-fields pro-field',
				'label'       => esc_html__( 'Hide group title', 'the-post-grid' ),
				'value'       => ! empty( $settings['cf_hide_group_title'] ) ? 1 : 0,
			];
		}

		return $fields;
	}

	public static function detailAvailableFields() {
		$fields   = self::rtTPGItemFields();
		$inserted = [
			'content'     => esc_html__( 'Content', 'the-post-grid' ),
			'feature_img' => esc_html__( 'Feature Image', 'the-post-grid' ),
		];

		unset( $fields['heading'] );
		unset( $fields['excerpt'] );
		unset( $fields['read_more'] );
		unset( $fields['comment_count'] );

		$offset                    = array_search( 'title', array_keys( $fields ) ) + 1;
		$newFields                 = array_slice( $fields, 0, $offset, true ) + $inserted + array_slice(
			$fields,
			$offset,
			null,
			true
		);
		$newFields['social_share'] = 'Social Share';

		return $newFields;
	}

	public static function rtTPGSCHeadingSettings() {
		$fields = [
			'tpg_heading_tag'       => [
				'type'    => 'select',
				'name'    => 'tpg_heading_tag',
				'label'   => esc_html__( 'Tag', 'the-post-grid' ),
				'class'   => 'rt-select2',
				'id'      => 'heading-tag',
				'options' => self::getHeadingTags(),
				'default' => 'h2',
			],
			'tpg_heading_style'     => [
				'type'    => 'select',
				'class'   => 'rt-select2',
				'label'   => esc_html__( 'Style', 'the-post-grid' ),
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => [
					'style1' => esc_html__( 'Style 1', 'the-post-grid' ),
					'style2' => esc_html__( 'Style 2', 'the-post-grid' ),
					'style3' => esc_html__( 'Style 3', 'the-post-grid' ),
				],
			],
			'tpg_heading_alignment' => [
				'type'    => 'select',
				'class'   => 'rt-select2',
				'label'   => esc_html__( 'Alignment', 'the-post-grid' ),
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => [
					'left'   => esc_html__( 'Left', 'the-post-grid' ),
					'right'  => esc_html__( 'Right', 'the-post-grid' ),
					'center' => esc_html__( 'Center', 'the-post-grid' ),
				],
			],
			'tpg_heading_link'      => [
				'type'  => 'url',
				'label' => esc_html__( 'Link', 'the-post-grid' ),
			],
		];

		return $fields;
	}

	public static function rtTPGSCCategorySettings() {
		$fields = [
			'tpg_category_position' => [
				'type'        => 'select',
				'class'       => 'rt-select2',
				'holderClass' => 'pro-field',
				'label'       => esc_html__( 'Position', 'the-post-grid' ),
				'blank'       => esc_html__( 'Default', 'the-post-grid' ),
				'options'     => [
					'above_title'  => esc_html__( 'Above Title', 'the-post-grid' ),
					'top_left'     => esc_html__( 'Over image (Top Left)', 'the-post-grid' ),
					'top_right'    => esc_html__( 'Over image (Top Right)', 'the-post-grid' ),
					'bottom_left'  => esc_html__( 'Over image (Bottom Left)', 'the-post-grid' ),
					'bottom_right' => esc_html__( 'Over image (Bottom Right)', 'the-post-grid' ),
					'image_center' => esc_html__( 'Over image (Center)', 'the-post-grid' ),
				],
			],
			'tpg_category_style'    => [
				'type'        => 'select',
				'class'       => 'rt-select2',
				'holderClass' => 'pro-field',
				'label'       => esc_html__( 'Style', 'the-post-grid' ),
				'blank'       => esc_html__( 'Default', 'the-post-grid' ),
				'options'     => [
					'style1' => esc_html__( 'Style 1', 'the-post-grid' ),
					'style2' => esc_html__( 'Style 2', 'the-post-grid' ),
					'style3' => esc_html__( 'Style 3', 'the-post-grid' ),
				],
			],
			'tpg_category_icon'     => [
				'type'    => 'switch',
				'label'   => esc_html__( 'Icon', 'the-post-grid' ),
				'default' => true,
			],
		];

		return $fields;
	}

	public static function rtTPGSCTitleSettings() {
		$fields = [
			'tpg_title_position'   => [
				'type'        => 'select',
				'label'       => esc_html__( 'Title Position (Above or Below image)', 'the-post-grid' ),
				'class'       => 'rt-select2 ',
				'holderClass' => 'pro-field',
				'blank'       => esc_html__( 'Default', 'the-post-grid' ),
				'options'     => [
					'above' => esc_html__( 'Above image', 'the-post-grid' ),
					'below' => esc_html__( 'Below image', 'the-post-grid' ),
				],
				'description' => __(
					"<span style='color:#ff0000'>Only Layout 1, Layout 12, Layout 14, Isotope1, Isotope8, Isotope10, Carousel Layout 1, Carousel Layout 8, Carousel Layout 10</span>",
					'the-post-grid'
				),
			],
			'title_tag'            => [
				'type'    => 'select',
				'name'    => 'title_tag',
				'label'   => esc_html__( 'Title tag', 'the-post-grid' ),
				'class'   => 'rt-select2',
				'id'      => 'title-tag',
				'options' => self::getTitleTags(),
				'default' => 'h3',
			],
			'tpg_title_limit'      => [
				'type'        => 'number',
				'label'       => esc_html__( 'Title limit', 'the-post-grid' ),
				'description' => esc_html__( 'Title limit only integer number is allowed, Leave it blank for full title.', 'the-post-grid' ),
			],
			'tpg_title_limit_type' => [
				'type'      => 'radio',
				'label'     => esc_html__( 'Title limit type', 'the-post-grid' ),
				'alignment' => 'vertical',
				'default'   => 'character',
				'options'   => self::get_limit_type(),
			],
		];

		return $fields;
	}

	public static function rtTPGSCMetaSettings() {
		$fields = [
			'tpg_meta_position'  => [
				'type'        => 'select',
				'label'       => esc_html__( 'Position', 'the-post-grid' ),
				'class'       => 'rt-select2 ',
				'holderClass' => 'pro-field',
				'blank'       => esc_html__( 'Default', 'the-post-grid' ),
				'options'     => [
					'above_title'   => esc_html__( 'Above Title', 'the-post-grid' ),
					'above_excerpt' => esc_html__( 'Above excerpt', 'the-post-grid' ),
					'below_excerpt' => esc_html__( 'Below excerpt', 'the-post-grid' ),
				],
			],
			'tpg_meta_icon'      => [
				'type'    => 'switch',
				'label'   => esc_html__( 'Icon', 'the-post-grid' ),
				'default' => true,
			],
			'tpg_meta_separator' => [
				'type'    => 'select',
				'class'   => 'rt-select2',
				'label'   => esc_html__( 'Separator', 'the-post-grid' ),
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => [
					'dot'     => esc_html__( 'Dot ( . )', 'the-post-grid' ),
					's_slash' => esc_html__( 'Single Slash ( / )', 'the-post-grid' ),
					'd_slash' => esc_html__( 'Double Slash ( // )', 'the-post-grid' ),
					'hypen'   => esc_html__( 'Hypen ( - )', 'the-post-grid' ),
					'v_pipe'  => esc_html__( 'Vertical Pipe ( | )', 'the-post-grid' ),
				],
			],
		];

		return $fields;
	}

	public static function rtTPGSCImageSettings() {
		$fields = [
			'feature_image'            => [
				'type'    => 'switch',
				'label'   => esc_html__( 'Hide Feature Image', 'the-post-grid' ),
				'id'      => 'rt-tpg-feature-image',
				'default' => false,
			],
			'featured_image_size'      => [
				'type'        => 'select',
				'label'       => esc_html__( 'Feature Image Size', 'the-post-grid' ),
				'class'       => 'rt-select2',
				'holderClass' => 'rt-feature-image-option tpg-hidden',
				'options'     => Fns::get_image_sizes(),
			],
			'custom_image_size'        => [
				'type'        => 'image_size',
				'label'       => esc_html__( 'Custom Image Size', 'the-post-grid' ),
				'holderClass' => 'rt-sc-custom-image-size-holder tpg-hidden',
				'multiple'    => true,
			],
			'media_source'             => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Media Source', 'the-post-grid' ),
				'default'     => 'feature_image',
				'alignment'   => 'vertical',
				'holderClass' => 'rt-feature-image-option tpg-hidden',
				'options'     => self::rtMediaSource(),
			],
			'tgp_layout2_image_column' => [
				'type'        => 'select',
				'label'       => esc_html__( 'Image column', 'the-post-grid' ),
				'class'       => 'rt-select2',
				'holderClass' => 'holder-layout2-image-column tpg-hidden',
				'default'     => 4,
				'options'     => self::scColumns(),
				'description' => 'Content column will calculate automatically',
			],
			'tpg_image_type'           => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Type', 'the-post-grid' ),
				'alignment'   => 'vertical',
				'holderClass' => 'rt-feature-image-option tpg-hidden pro-field',
				'default'     => 'normal',
				'options'     => self::get_image_types(),
			],
			'tpg_image_animation'      => [
				'type'    => 'select',
				'label'   => esc_html__( 'Hover Animation', 'the-post-grid' ),
				'class'   => 'rt-select2',
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => [
					'img_zoom_in'   => esc_html__( 'Zoom in', 'the-post-grid' ),
					'img_zoom_out'  => esc_html__( 'Zoom out', 'the-post-grid' ),
					'img_no_effect' => esc_html__( 'None', 'the-post-grid' ),
				],
			],
			'tpg_image_border_radius'  => [
				'type'        => 'number',
				'class'       => 'small-text',
				'holderClass' => 'pro-field',
				'label'       => esc_html__( 'Border radius', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default', 'the-post-grid' ),
			],
		];

		return apply_filters( 'rt_tpg_sc_image_settings', $fields );
	}

	public static function rtTPGSCExcerptSettings() {
		$fields = [
			'excerpt_limit'         => [
				'type'        => 'number',
				'label'       => esc_html__( 'Excerpt limit', 'the-post-grid' ),
				'default'     => 15,
				'description' => esc_html__( 'Excerpt limit only integer number is allowed, Leave it blank for full excerpt.', 'the-post-grid' ),
			],
			'tgp_excerpt_type'      => [
				'type'      => 'radio',
				'label'     => esc_html__( 'Excerpt Type', 'the-post-grid' ),
				'alignment' => 'vertical',
				'default'   => 'word',
				'options'   => self::get_limit_type( 'content' ),
			],
			'tgp_excerpt_more_text' => [
				'type'    => 'text',
				'label'   => esc_html__( 'Excerpt more text', 'the-post-grid' ),
				'default' => '...',
			],
		];

		return $fields;
	}

	public static function rtTPGSCButtonSettings() {
		$fields = [
			'tpg_read_more_button_border_radius' => [
				'type'        => 'number',
				'class'       => 'small-text',
				'label'       => esc_html__( 'Border radius', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default', 'the-post-grid' ),
			],
			'tpg_read_more_button_alignment'     => [
				'type'    => 'select',
				'class'   => 'rt-select2',
				'label'   => esc_html__( 'Alignment', 'the-post-grid' ),
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => [
					'left'   => esc_html__( 'Left', 'the-post-grid' ),
					'right'  => esc_html__( 'Right', 'the-post-grid' ),
					'center' => esc_html__( 'Center', 'the-post-grid' ),
				],
			],
			'tgp_read_more_text'                 => [
				'type'  => 'text',
				'label' => esc_html__( 'Text', 'the-post-grid' ),
			],
		];

		return $fields;
	}

	public static function rtTPGStyleFields() {
		$fields = [
			'parent_class'  => [
				'type'        => 'text',
				'label'       => esc_html__( 'Parent class', 'the-post-grid' ),
				'class'       => 'medium-text',
				'description' => esc_html__( 'Parent class for adding custom css', 'the-post-grid' ),
			],
			'primary_color' => [
				'type'    => 'text',
				'label'   => esc_html__( 'Primary Color', 'the-post-grid' ),
				'class'   => 'rt-color',
				'default' => '#0367bf',
			],
		];

		return apply_filters( 'rt_tpg_style_fields', $fields );
	}

	public static function rtTPGStyleButtonColorFields() {
		$fields = [

			'button_bg_color'         => [
				'type'  => 'text',
				'name'  => 'button_bg_color',
				'label' => esc_html__( 'Background', 'the-post-grid' ),
				'class' => 'rt-color',
			],
			'button_hover_bg_color'   => [
				'type'  => 'text',
				'name'  => 'button_hover_bg_color',
				'label' => esc_html__( 'Hover Background', 'the-post-grid' ),
				'class' => 'rt-color',
			],
			'button_active_bg_color'  => [
				'type'  => 'text',
				'label' => esc_html__( 'Active Background (Isotop)', 'the-post-grid' ),
				'class' => 'rt-color',
			],
			'button_text_bg_color'    => [
				'type'  => 'text',
				'label' => esc_html__( 'Text', 'the-post-grid' ),
				'class' => 'rt-color',
			],
			'button_hover_text_color' => [
				'type'  => 'text',
				'label' => esc_html__( 'Text Hover', 'the-post-grid' ),
				'class' => 'rt-color',
			],
		];

		return apply_filters( 'rt_tpg_style_button_css_fields', $fields );
	}

	public static function rtTPGStyleHeading() {
		$fields = [
			'tpg_heading_bg'           => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Background Color', 'the-post-grid' ),
			],
			'tpg_heading_color'        => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Text Color', 'the-post-grid' ),
			],
			'tpg_heading_border_color' => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Border Color', 'the-post-grid' ),
			],
			'tpg_heading_border_size'  => [
				'type'        => 'number',
				'class'       => 'small-text',
				'label'       => esc_html__( 'Border Size', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default', 'the-post-grid' ),
			],
			'tpg_heading_margin'       => [
				'type'        => 'text',
				'class'       => 'medium-text tpg-spacing-field',
				'label'       => esc_html__( 'Margin', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
			'tpg_heading_padding'      => [
				'type'        => 'text',
				'class'       => 'medium-text tpg-spacing-field',
				'label'       => esc_html__( 'Padding', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default, multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
		];

		return apply_filters( 'tpg_heading_style_fields', $fields );
	}

	public static function rtTPGStyleFullArea() {
		$fields = [
			'tpg_full_area_bg'      => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Background', 'the-post-grid' ),
			],
			'tpg_full_area_margin'  => [
				'type'        => 'text',
				'class'       => 'medium-text',
				'label'       => esc_html__( 'Margin', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
			'tpg_full_area_padding' => [
				'type'        => 'text',
				'class'       => 'medium-text',
				'label'       => esc_html__( 'Padding', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
		];

		return apply_filters( 'tpg_box_style_fields', $fields );
	}

	public static function rtTPGStyleContentWrap() {
		$fields = [
			'tpg_content_wrap_bg'            => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Background Color', 'the-post-grid' ),
			],
			'tpg_content_wrap_shadow'        => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Box Shadow Color', 'the-post-grid' ),
			],
			'tpg_content_wrap_border_color'  => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Border Color', 'the-post-grid' ),
			],
			'tpg_content_wrap_border'        => [
				'type'        => 'number',
				'class'       => 'small-text',
				'label'       => esc_html__( 'Border Width', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default', 'the-post-grid' ),
			],
			'tpg_content_wrap_border_radius' => [
				'type'  => 'number',
				'class' => 'small-text',
				'label' => esc_html__( 'Border Radius', 'the-post-grid' ),
			],
			'tpg_box_padding'                => [
				'type'        => 'text',
				'class'       => 'medium-text',
				'label'       => esc_html__( 'Box Padding', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
			'tpg_content_padding'            => [
				'type'        => 'text',
				'class'       => 'medium-text',
				'label'       => esc_html__( 'Content Padding', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
		];

		return apply_filters( 'tpg_content_style_fields', $fields );
	}

	public static function rtTPGStyleCategory() {
		$fields = [
			'tpg_category_bg'            => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Background Color', 'the-post-grid' ),
			],
			'tpg_category_color'         => [
				'type'  => 'text',
				'class' => 'rt-color',
				'label' => esc_html__( 'Text Color', 'the-post-grid' ),
			],
			'tpg_category_border_radius' => [
				'type'        => 'number',
				'class'       => 'small-text',
				'label'       => esc_html__( 'Border Radius', 'the-post-grid' ),
				'description' => esc_html__( 'Leave it blank for default', 'the-post-grid' ),
			],
			'tpg_category_margin'        => [
				'type'        => 'text',
				'class'       => 'medium-text tpg-spacing-field',
				'label'       => esc_html__( 'Margin', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
			'tpg_category_padding'       => [
				'type'        => 'text',
				'class'       => 'medium-text tpg-spacing-field',
				'label'       => esc_html__( 'Padding', 'the-post-grid' ),
				'description' => esc_html__( 'Multiple value allowed separated by comma 12,0,5,10', 'the-post-grid' ),
			],
			'rt_tpg_category_font_size'  => [
				'type'    => 'select',
				'class'   => 'rt-select2',
				'label'   => esc_html__( 'Font Size', 'the-post-grid' ),
				'blank'   => esc_html__( 'Default', 'the-post-grid' ),
				'options' => self::scFontSize(),
			],
		];

		return apply_filters( 'tpg_category_style_fields', $fields );
	}


	public static function itemFields() {

		$itemField                      = self::rtTPGItemFields();
		$itemField['tpg_default_value'] = 'Default';

		$fields = [
			'item_fields' => [
				'type'      => 'checkbox',
				'name'      => 'item_fields',
				'label'     => esc_html__( 'Field selection', 'the-post-grid' ),
				'id'        => 'item-fields',
				'multiple'  => true,
				'alignment' => 'vertical',
				'default'   => array_keys( $itemField ),
				'options'   => $itemField,
			],
		];
		if ( $cf = Fns::is_acf() ) {
			global $post;
			$post_type                     = get_post_meta( $post->ID, 'tpg_post_type', true );
			$plist                         = self::getCFPluginList();
			$fields['cf_group']            = [
				'type'        => 'checkbox',
				'name'        => 'cf_group',
				'holderClass' => 'tpg-hidden cf-fields cf-group',
				'label'       => 'Custom Field group ' . " ({$plist[$cf]})",
				'multiple'    => true,
				'alignment'   => 'vertical',
				'id'          => 'cf_group',
				'options'     => Fns::get_groups_by_post_type( $post_type, $cf ),
			];
			$fields['cf_hide_empty_value'] = [
				'type'        => 'checkbox',
				'name'        => 'cf_hide_empty_value',
				'holderClass' => 'tpg-hidden cf-fields',
				'label'       => esc_html__( 'Hide field with empty value', 'the-post-grid' ),
				'default'     => 1,
			];
			$fields['cf_show_only_value']  = [
				'type'        => 'checkbox',
				'name'        => 'cf_show_only_value',
				'holderClass' => 'tpg-hidden cf-fields',
				'label'       => esc_html__( 'Show only value of field', 'the-post-grid' ),
				'description' => esc_html__( 'By default both name & value of field is shown', 'the-post-grid' ),
			];
			$fields['cf_hide_group_title'] = [
				'type'        => 'checkbox',
				'name'        => 'cf_hide_group_title',
				'holderClass' => 'tpg-hidden cf-fields',
				'label'       => esc_html__( 'Hide group title', 'the-post-grid' ),
			];
		}

		return $fields;
	}


	public static function getCFPluginList() {
		return [
			'acf' => esc_html__( 'Advanced Custom Field', 'the-post-grid' ),
		];
	}

	public static function rtMediaSource() {
		return [
			'feature_image' => esc_html__( 'Feature Image', 'the-post-grid' ),
			'first_image'   => esc_html__( 'First Image from content', 'the-post-grid' ),
		];
	}

	public static function get_image_types() {
		return [
			'normal' => esc_html__( 'Normal', 'the-post-grid' ),
			'circle' => esc_html__( 'Circle', 'the-post-grid' ),
		];
	}

	public static function get_limit_type( $content = null ) {
		$types = [
			'character' => esc_html__( 'Character', 'the-post-grid' ),
			'word'      => esc_html__( 'Word', 'the-post-grid' ),
		];
		if ( $content === 'content' ) {
			$types['full'] = esc_html__( 'Full Content', 'the-post-grid' );
		}

		return apply_filters( 'tpg_limit_type', $types, $content );
	}

	public static function scColumns() {
		return [
			1 => esc_html__( 'Column 1', 'the-post-grid' ),
			2 => esc_html__( 'Column 2', 'the-post-grid' ),
			3 => esc_html__( 'Column 3', 'the-post-grid' ),
			4 => esc_html__( 'Column 4', 'the-post-grid' ),
			5 => esc_html__( 'Column 5', 'the-post-grid' ),
			6 => esc_html__( 'Column 6', 'the-post-grid' ),
		];
	}

	public static function tgp_filter_list() {
		return [
			'_taxonomy_filter' => esc_html__( 'Taxonomy filter', 'the-post-grid' ),
			'_author_filter'   => esc_html__( 'Author filter', 'the-post-grid' ),
			'_order_by'        => esc_html__( 'Order - Sort retrieved posts by parameter', 'the-post-grid' ),
			'_sort_order'      => esc_html__( 'Sort Order - Designates the ascending or descending order of the "orderby" parameter', 'the-post-grid' ),
			'_search'          => esc_html__( 'Search filter', 'the-post-grid' ),
		];
	}

	public static function overflowOpacity() {
		return [
			1 => esc_html__( '10%', 'the-post-grid' ),
			2 => esc_html__( '20%', 'the-post-grid' ),
			3 => esc_html__( '30%', 'the-post-grid' ),
			4 => esc_html__( '40%', 'the-post-grid' ),
			5 => esc_html__( '50%', 'the-post-grid' ),
			6 => esc_html__( '60%', 'the-post-grid' ),
			7 => esc_html__( '70%', 'the-post-grid' ),
			8 => esc_html__( '80%', 'the-post-grid' ),
			9 => esc_html__( '90%', 'the-post-grid' ),
		];
	}

	public static function rtTPGLayoutType() {
		$layoutType = [
			'grid'       => [
				'title' => esc_html__( 'Grid', 'the-post-grid' ),
				'img'   => rtTPG()->get_assets_uri( 'images/grid.png' ),
			],
			'grid_hover' => [
				'title' => esc_html__( 'Grid Hover', 'the-post-grid' ),
				'img'   => rtTPG()->get_assets_uri( 'images/grid_hover.png' ),
			],
			'list'       => [
				'title' => esc_html__( 'List', 'the-post-grid' ),
				'img'   => rtTPG()->get_assets_uri( 'images/list.png' ),
			],
			'isotope'    => [
				'title' => esc_html__( 'Isotope', 'the-post-grid' ),
				'img'   => rtTPG()->get_assets_uri( 'images/isotope.png' ),
			],
		];

		return apply_filters( 'rt_tpg_layouts_type', $layoutType );
	}

	public static function rtTPGLayouts() {
		$layouts = [
			'layout1'  => [
				'title'       => esc_html__( 'Grid Layout 1', 'the-post-grid' ),
				'layout'      => 'grid',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid1.png' ),
			],
			'layout12' => [
				'title'       => esc_html__( 'Grid Layout 2', 'the-post-grid' ),
				'layout'      => 'grid',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-2/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid10.png' ),
			],
			'layout5'  => [
				'title'       => esc_html__( 'Grid Hover 1', 'the-post-grid' ),
				'layout'      => 'grid_hover',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/hover-layout-1/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid3.png' ),
			],
			'layout6'  => [
				'title'       => esc_html__( 'Grid Hover 2', 'the-post-grid' ),
				'layout'      => 'grid_hover',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/hover-layout-2/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid4.png' ),
			],
			'layout7'  => [
				'title'       => esc_html__( 'Grid Hover 3', 'the-post-grid' ),
				'layout'      => 'grid_hover',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/hover-layout-3/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid5.png' ),
			],
			'layout2'  => [
				'title'       => esc_html__( 'List Layout 1', 'the-post-grid' ),
				'layout'      => 'list',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/list-layout-1/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/list1.png' ),
			],
			'layout3'  => [
				'title'       => esc_html__( 'List Layout 2', 'the-post-grid' ),
				'layout'      => 'list',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/list-layout-rounded-image/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/list2.png' ),
			],
			'isotope1' => [
				'title'       => esc_html__( 'Isotope Layout 1', 'the-post-grid' ),
				'layout'      => 'isotope',
				'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/layout-4-filter/',
				'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope1.png' ),
			],
		];

		return apply_filters( 'tpg_layouts', $layouts );
	}

	public static function rtTPGItemFields() {
		$items = [
			'heading'       => esc_html__( 'ShortCode Heading', 'the-post-grid' ),
			'title'         => esc_html__( 'Title', 'the-post-grid' ),
			'excerpt'       => esc_html__( 'Excerpt', 'the-post-grid' ),
			'read_more'     => esc_html__( 'Read More', 'the-post-grid' ),
			'post_date'     => esc_html__( 'Post Date', 'the-post-grid' ),
			'author'        => esc_html__( 'Author', 'the-post-grid' ),
			'categories'    => esc_html__( 'Categories', 'the-post-grid' ),
			'tags'          => esc_html__( 'Tags', 'the-post-grid' ),
			'comment_count' => esc_html__( 'Comment count', 'the-post-grid' ),
		];

		return apply_filters( 'tpg_field_selection_items', $items );
	}

	public static function postLoadingType() {
		return apply_filters(
			'rttpg_pagination_type',
			[
				'pagination' => esc_html__( 'Pagination', 'the-post-grid' ),
			]
		);
	}

	public static function scGridOpt() {
		return [
			'even'    => esc_html__( 'Even', 'the-post-grid' ),
			'masonry' => esc_html__( 'Masonry', 'the-post-grid' ),
		];
	}

	public static function extraStyle() {
		return apply_filters(
			'tpg_extra_style',
			[
				'title'       => esc_html__( 'Title', 'the-post-grid' ),
				'title_hover' => esc_html__( 'Title hover', 'the-post-grid' ),
				'excerpt'     => esc_html__( 'Excerpt', 'the-post-grid' ),
				'meta_data'   => esc_html__( 'Meta Data', 'the-post-grid' ),
			]
		);
	}

	public static function scFontSize() {
		$num = [];
		for ( $i = 10; $i <= 50; $i++ ) {
			$num[ $i ] = $i . 'px';
		}

		return $num;
	}

	public static function scAlignment() {
		return [
			'left'    => esc_html__( 'Left', 'the-post-grid' ),
			'right'   => esc_html__( 'Right', 'the-post-grid' ),
			'center'  => esc_html__( 'Center', 'the-post-grid' ),
			'justify' => esc_html__( 'Justify', 'the-post-grid' ),
		];
	}

	public static function scReadMoreButtonPositionList() {
		return [
			'left'   => esc_html__( 'Left', 'the-post-grid' ),
			'right'  => esc_html__( 'Right', 'the-post-grid' ),
			'center' => esc_html__( 'Center', 'the-post-grid' ),
		];
	}


	public static function scTextWeight() {
		return [
			'normal'  => esc_html__( 'Normal', 'the-post-grid' ),
			'bold'    => esc_html__( 'Bold', 'the-post-grid' ),
			'bolder'  => esc_html__( 'Bolder', 'the-post-grid' ),
			'lighter' => esc_html__( 'Lighter', 'the-post-grid' ),
			'inherit' => esc_html__( 'Inherit', 'the-post-grid' ),
			'initial' => esc_html__( 'Initial', 'the-post-grid' ),
			'unset'   => esc_html__( 'Unset', 'the-post-grid' ),
			100       => esc_html__( '100', 'the-post-grid' ),
			200       => esc_html__( '200', 'the-post-grid' ),
			300       => esc_html__( '300', 'the-post-grid' ),
			400       => esc_html__( '400', 'the-post-grid' ),
			500       => esc_html__( '500', 'the-post-grid' ),
			600       => esc_html__( '600', 'the-post-grid' ),
			700       => esc_html__( '700', 'the-post-grid' ),
			800       => esc_html__( '800', 'the-post-grid' ),
			900       => esc_html__( '900', 'the-post-grid' ),
		];
	}

	public static function imageCropType() {
		return [
			'soft' => esc_html__( 'Soft Crop', 'the-post-grid' ),
			'hard' => esc_html__( 'Hard Crop', 'the-post-grid' ),
		];
	}

	public static function rt_filter_type() {
		return [
			'dropdown' => esc_html__( 'Dropdown', 'the-post-grid' ),
			'button'   => esc_html__( 'Button', 'the-post-grid' ),
		];
	}

	public static function get_pro_feature_list() {
		return '<ol>
					<li>Fully responsive and mobile friendly.</li>
					<li>62 Different Layouts</li>
					<li>45 Elementor Layouts</li>
					<li>Creative Slider layouts</li>
					<li>Archive page builder for Elementor</li>
					<li>Even and Masonry Grid.</li>
					<li>WooCommerce supported.</li>
					<li>EDD supported for shortcode.</li>
					<li>Custom Post Type Supported</li>
					<li>Display posts by any Taxonomy like category(s), tag(s), author(s), keyword(s)</li>
					<li>Order by Id, Title, Created date, Modified date and Menu order.</li>
					<li>Display image size (thumbnail, medium, large, full)</li>
					<li>Ajax front-end filter by category(s), tag(s), author(s), keyword(s)</li>
					<li>Isotope filter for any taxonomy ie. categories, tags...</li>
					<li>Query Post with Relation.</li>
					<li>Fields Selection.</li>
					<li>All Text and Color control.</li>
					<li>Meta Position Control.</li>
					<li>Category Position Control.</li>
					<li>Content Wrapper Style Control.</li>
					<li>Enable/Disable Pagination.</li>
					<li>AJAX Pagination (Load more and Load on Scrolling)</li>
					<li>Advanced Custom Field support</li>
					<li>Post View Count</li>
				</ol>
				<a href="' . esc_url( rtTpg()->proLink() ) . '" class="rt-admin-btn" target="_blank">' . esc_html__( 'Get Pro Version', 'the-post-grid' ) . '</a>';
	}
}
