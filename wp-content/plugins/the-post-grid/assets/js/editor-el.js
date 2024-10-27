"use strict";


(function ($) {
    $("document").ready(function () {

        var templateAddSection = $("#tmpl-elementor-add-section");

        if (0 < templateAddSection.length) {
            var oldTemplateButton = templateAddSection.html();
            oldTemplateButton = oldTemplateButton.replace(
                '<div class="elementor-add-section-drag-title',
                '<div class="elementor-add-section-area-button rttpg-import-button"><img src="http://postgrid.test/wp-content/plugins/the-post-grid/assets/images/icon-16x16.png" alt=""></div><div class="elementor-add-section-drag-title'
            );
            templateAddSection.html(oldTemplateButton);

        }


    });
})(jQuery);
