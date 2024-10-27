;(function ($) {

	// $(window).on('load', function(){
	$(document).ready(function () {

		elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {

			var bodyWrap = $('body');

			bodyWrap.on('click', '.the-post-grid-field-hide', function () {
				$(this).toggleClass('is-pro');
				$(this).find('label', 'input').on('click', function () {
					console.log($(this));
				})
				// return false;
			});

			bodyWrap.on('click', '.tpg-pro-field-select select', function () {
				var options = $(this).find('option').not('[value=default]');
				options.attr('disabled', true).css({backgroundColor: '#dfdfdf'})
			});

		})

	})

})(jQuery);