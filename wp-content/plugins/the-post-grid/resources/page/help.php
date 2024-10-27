<?php
/**
 * Get Help Page
 *
 * @package RT_TPG
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Get Help
 */
?>
	<style>
		.rttpg-help-wrapper {
			width: 60%;
			margin: 0 auto;
		}
		.rttpg-help-section iframe {
			max-width: 100%;
		}
		.rttpg-help-wrapper .rt-document-box .rt-box-title {
			margin-bottom: 30px;
		}
		.rttpg-help-wrapper .rttpg-help-section {
			margin-top: 30px;
		}
		.rttpg-feature-list ul {
			display: flex;
			flex-wrap: wrap;
		}
		.rttpg-feature-list ul li {
			margin: 5px 10px 5px 0;
			width: calc(50% - 20px);
			flex: 0 0 calc(50% - 20px);
			font-size: 14px;
		}
		.rttpg-feature-list ul li i {
			color: var(--rt-primary-color);
		}
		.rttpg-pro-feature-content {
			display: flex;
			flex-wrap: wrap;
		}
		.rttpg-pro-feature-content .rt-document-box + .rt-document-box {
			margin-left: 30px;
		}
		.rttpg-pro-feature-content .rt-document-box {
			flex: 0 0 calc(33.3333% - 60px);
			margin-top: 30px;
		}
		.rttpg-testimonials {
			display: flex;
			flex-wrap: wrap;
		}
		.rttpg-testimonials .rttpg-testimonial + .rttpg-testimonial  {
			margin-left: 30px;
		}
		.rttpg-testimonials .rttpg-testimonial  {
			flex: 0 0 calc(50% - 30px)
		}
		.rttpg-testimonial .client-info {
			display: flex;
			flex-wrap: wrap;
			font-size: 14px;
			align-items: center;
		}
		.rttpg-testimonial .client-info img {
			width: 60px;
			height: 60px;
			object-fit: cover;
			border-radius: 50%;
			margin-right: 10px;
		}
		.rttpg-testimonial .client-info .rttpg-star {
			color: var(--rt-primary-color);
		}
		.rttpg-testimonial .client-info .client-name {
			display: block;
			color: #000;
			font-size: 16px;
			font-weight: 600;
			margin: 8px 0 5px;
		}
		.rttpg-call-to-action {
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center;
			height: 150px;
			color: #ffffff;
			margin: 30px 0;
		}
		.rttpg-call-to-action a {
			color: inherit;
			display: flex;
			flex-wrap: wrap;
			width: 100%;
			height: 100%;
			flex: 1;
			justify-content: center;
			align-items: center;
			font-size: 28px;
			font-weight: 700;
			text-decoration: none;
		}
		.rttpg-call-to-action:hover a {
			text-decoration: underline;
		}
		@media all and (max-width: 1400px) {
			.rttpg-help-wrapper {
				width: 80%;
			}
			.rttpg-help-section iframe {
				max-width: 100%;
				height: 320px;
			}
		}
		@media all and (max-width: 991px) {
			.rttpg-help-wrapper {
				width: calc(100% - 40px);
			}
			.rttpg-pro-feature-content .rt-document-box {
				flex: 0 0 calc(50% - 55px)
			}
			.rttpg-pro-feature-content .rt-document-box + .rt-document-box + .rt-document-box {
				margin-left: 0;
			}
		}
		@media all and (max-width: 600px) {
			.rt-document-box .rt-box-content .rt-box-title {
				line-height: 28px;
			}
			.rttpg-help-section iframe {
				height: 250px;
			}
			.rttpg-feature-list ul {
				display: block;
			}
			.rttpg-feature-list ul li {
				width: 100%;
			}
			.rttpg-call-to-action a {
				padding-left: 25px;
				padding-right: 25px;
				font-size: 20px;
				line-height: 28px;
				width: 80%;
			}
			.rttpg-testimonials {
				display: block;
			}
			.rttpg-testimonials .rttpg-testimonial + .rttpg-testimonial {
				margin-left: 0;
				margin-top: 30px;
			}
			.rttpg-pro-feature-content .rt-document-box {
				width: 100%;
				flex: auto;
			}
			.rttpg-pro-feature-content .rt-document-box + .rt-document-box {
				margin-left: 0;
			}
		}
	</style>
	<div class="rttpg-help-wrapper" >
		<div class="rttpg-help-section rt-document-box">
			<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
			<div class="rt-box-content">
				<h3 class="rt-box-title">Thank you for installing The Post Grid</h3>

				<h3>Shortcode Demo</h3>
				<iframe style="width:calc(100% - 40px);" width="800" height="450" src="https://www.youtube.com/embed/_xZBDU4kgKk" title="The Post Grid" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

				<h3>Elementor Demo</h3>
				<iframe style="width:calc(100% - 40px);" width="800" height="450" src="https://www.youtube.com/embed/Px7c91A0W5Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

				<h3>Gutenberg Demo</h3>
				<iframe style="width:calc(100% - 40px);" width="800" height="450" src="https://www.youtube.com/embed/wHWAnfL0VhU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
		<div class="rt-document-box">
			<div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div>
			<div class="rt-box-content rttpg-feature-list">
				<h3 class="rt-box-title">Pro Features</h3>
				<ul>
					<li><i class="dashicons dashicons-saved"></i> Custom Post Type Supported.</li>
					<li><i class="dashicons dashicons-saved"></i> Advanced Post Filter.</li>
					<li><i class="dashicons dashicons-saved"></i> Single or Multi Popup.</li>
					<li><i class="dashicons dashicons-saved"></i> Custom Image Size.</li>
					<li><i class="dashicons dashicons-saved"></i> Meta Position Control.</li>
					<li><i class="dashicons dashicons-saved"></i> Social Share.</li>
					<li><i class="dashicons dashicons-saved"></i> 62 Different Layouts.</li>
					<li><i class="dashicons dashicons-saved"></i> Slider Layout.</li>
					<li><i class="dashicons dashicons-saved"></i> Fields Selection.</li>
					<li><i class="dashicons dashicons-saved"></i> All Text and Color control.</li>
					<li><i class="dashicons dashicons-saved"></i> AJAX Pagination (Load more and Load on Scrolling).</li>
					<li><i class="dashicons dashicons-saved"></i> Archive page builder for Elementor </li>
					<li><i class="dashicons dashicons-saved"></i> Advanced Custom Field support</li>
					<li><i class="dashicons dashicons-saved"></i> Post View Count</li>
				</ul>
			</div>
		</div>
		<div class="rttpg-call-to-action" style="background-image: url('<?php echo esc_url( rtTPG()->get_assets_uri( 'images/admin/banner.png' ) ); ?>')">
			<a href="<?php echo esc_url( rtTpg()->proLink() ); ?>" target="_blank" class="rt-update-pro-btn">
				Update Pro To Get More Features
			</a>
		</div>
		<div class="rt-document-box">
			<div class="rt-box-icon"><i class="dashicons dashicons-thumbs-up"></i></div>
			<div class="rt-box-content">
				<h3 class="rt-box-title">Happy clients of the Post Grid</h3>
				<div class="rttpg-testimonials">
					<div class="rttpg-testimonial">
						<p>So much functionality in the free version. Thank you very much! Many plugins offer a crippled free version to push into going to their PRO. The guys here provide a free version that brings lots of value also. I needed a flexible grid solution to my website that has dozen of grids in different configurations and the plugin could do everything I needed. Very easy to use and support it fantastic. Highly Recomended!</p>
						<div class="client-info">
							<img src="<?php echo esc_url( rtTPG()->get_assets_uri( 'images/admin/client1.jpeg' ) ); ?>">
							<div>
								<div class="rttpg-star">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</div>
								<span class="client-name">Erez Speiser</span>
							</div>
						</div>
					</div>
					<div class="rttpg-testimonial">
						<p>The post grid is a fantastic plugin! It's very easy to figure out without having to read any documentation. That is the mark of an excellent developer who knows how to make the user interface easy for people to use. I love that I can take my simple theme, and jazz it up with a nice grid, without having to go to a page builder. I am now teaching this to all my WordPress students 🙂 Hats off to this amazing plugin!</p>
						<div class="client-info">
							<img src="<?php echo esc_url( rtTPG()->get_assets_uri( 'images/admin/client2.jpeg' ) ); ?>">
							<div>
								<div class="rttpg-star">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</div>
								<span class="client-name">Christina Hills</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="rttpg-pro-feature-content">
			<div class="rt-document-box">
				<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
				<div class="rt-box-content">
					<h3 class="rt-box-title">Documentation</h3>
					<p>Get started by spending some time with the documentation we included step by step process with screenshots with video.</p>
					<a href="<?php echo esc_url( rtTpg()->docLink() ); ?>" target="_blank" class="rt-admin-btn">Documentation</a>
				</div>
			</div>
			<?php
			$rtContact = 'https://www.radiustheme.com/contact/';
			$rtFb      = 'https://www.facebook.com/groups/234799147426640/';
			$rtsite    = 'https://www.radiustheme.com/';
			$rtRating  = 'https://wordpress.org/support/plugin/the-post-grid/reviews/?filter=5#new-post';
			?>
			<div class="rt-document-box">
				<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
				<div class="rt-box-content">
					<h3 class="rt-box-title">Need Help?</h3>
					<p>Stuck with something? Please create a
						<a href="<?php echo esc_url( $rtContact ); ?>">ticket here</a> or post on <a href="<?php echo esc_url( $rtFb ); ?>">facebook group</a>. For emergency case join our <a href="<?php echo esc_url( $rtsite ); ?>">live chat</a>.</p>
					<a href="<?php echo esc_url( $rtContact ); ?>" target="_blank" class="rt-admin-btn">Get Support</a>
				</div>
			</div>
			<div class="rt-document-box">
				<div class="rt-box-icon"><i class="dashicons dashicons-smiley"></i></div>
				<div class="rt-box-content">
					<h3 class="rt-box-title">Happy Our Work?</h3>
					<p>If you happy with <strong>The Post Grid</strong> plugin, please add a rating. It would be glad to us.</p>
					<a href="<?php echo esc_url( $rtRating ); ?>" class="rt-admin-btn" target="_blank">Post Review</a>
				</div>
			</div>
		</div>
	</div>
