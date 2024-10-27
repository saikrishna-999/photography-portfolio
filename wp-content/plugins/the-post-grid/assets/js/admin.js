(function (global, $) {
    'use strict';

    var postType = $("#rt-sc-post-type").val();
    $(document).on('change', '#post_filter input[type=checkbox]', function () {
        var id = $(this).val();
        var postType = $("#rt-sc-post-type").val();
        if (id == 'tpg_taxonomy') {
            if (this.checked) {
                rtTPGTaxonomyListByPostType(postType, $(this));
            } else {
                $('.rt-tpg-filter.taxonomy > .taxonomy-field').hide('slow').html('');
                $('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-holder').hide('slow').html('');
                $('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-item-relation').hide('slow');
            }
        }
        if (this.checked) {
            $(".rt-tpg-filter." + id).show('slow');
        } else {
            $(".rt-tpg-filter." + id).hide('slow');
        }
    });

    $(".field-holder.pro-field").on('click', '.field', function (e) {
        e.preventDefault();
        $('.rt-pro-alert').show();
    });

    $('.rt-pro-alert-close').on('click', function (e) {
        e.preventDefault();
        $('.rt-pro-alert').hide();
    });

    $('.select2-results__option--highlighted').on('click', function (e) {
        e.preventDefault();
    });

    $(document).on('change', '#post-taxonomy input[type=checkbox]', function () {
        tlpShowHideScMeta();
        rtTPGTermListByTaxonomy($(this));
    });
    $(document).on('change', '#tgp_filter input[type=checkbox]', function () {
        tlpShowHideScMeta();
    });
    $("#sc-field-selection").on('change', 'label[for=item-fields-cf] input[type=checkbox]', function () {
        checkCustomField(true);
    });
    $("#popup_fields_holder").on('change', 'label[for=popup-fields-cf] input[type=checkbox]', function () {
        checkCustomFieldSettings();
    });

    $("#rt-tpg-pagination").on('change', function () {
        if (this.checked) {
            $(".field-holder.pagination-item").show();
        } else {
            $(".field-holder.pagination-item").hide();
        }
    });

    function checkCustomField() {
        if ($("#item-fields-cf").is(':checked')) {
            $(".field-holder.cf-fields").show();
        } else {
            $(".field-holder.cf-fields").hide();
        }
    }

    function checkCustomFieldSettings() {
        if ($("#popup-fields-cf").is(':checked')) {
            $(".field-holder.cfs-fields").show();
        } else {
            $(".field-holder.cfs-fields").hide();
        }
    }

    function loadCustomField($this) {
        var post_type = $this.val();
        if (post_type) {
            var arg = "post_type=" + post_type;
            tpgAjaxCall($this, 'getCfGroupListAsField', arg, function (data) {
                if (!data.error) {
                    $("#cf_group_holder").replaceWith(data.data);
                    checkCustomField();
                } else {
                    console.log(data.msg)
                }
            });
        }
    }

    function featureImageEffect() {
        if ($("#rt-tpg-feature-image").is(':checked')) {
            $(".field-holder.rt-feature-image-option").hide();
        } else {
            $(".field-holder.rt-feature-image-option").show();
        }
    }

    $("#rt-tpg-feature-image").on('change', function () {
        featureImageEffect();
    });

    $("#tgp_filter-_taxonomy_filter").on('change', function () {
        tpgTaxonomyFilterTrigger();
    });

    $("#order_by").on('change', function () {
        tpgOrderByEffect();
    });

    $("#rttpg-layout_type input[name=layout_type], #rttpg-layout input[name=layout]").on('change', function () {
        $('#layout_holder').show();
        tlpShowHideScMeta();
        rtTPGSelectedlayoutType();
    });

    $("#rt-sc-post-type").on("change", function (e) {
        var postType = $(this).val(),
            self = $(this);
        if (postType) {
            loadCustomField(self);
            rtTPGIsotopeFilter(self);
            rtTPGIsotopTaxonomyFilter(self);
            $('#post_filter input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
            });
            $(".rt-tpg-filter.taxonomy > .taxonomy-field").html('');
            $(".rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-item-container").remove();
            $(".rt-tpg-filter.hidden").hide();
            $(".field-holder.term-filter-item-relation ").hide();
        }

    });

    $(document).ready(function () {
        checkCustomFieldSettings();
        rtTgpFilter();
        if ($(".rt-select2").length) {
            tgpLiveReloadScript();
        }
        tlpShowHideScMeta();
        checkCustomField();
        if ($('.rt-color').length) {
            $('.rt-color').wpColorPicker();
        }
        if ($(".date-range").length) {
            $(".date-range-start").datepicker({
                defaultDate: "+1w",
                changeYear: true,
                changeMonth: true,
                dateFormat: "yy-mm-dd",
                onClose: function (selectedDate) {
                    $(".date-range-end").datepicker("option", "minDate", selectedDate);
                }
            });
            $(".date-range-end").datepicker({
                defaultDate: "+1w",
                changeYear: true,
                changeMonth: true,
                dateFormat: "yy-mm-dd",
                onClose: function (selectedDate) {
                    $(".date-range-start").datepicker("option", "maxDate", selectedDate);
                }
            });
        }

        $('.tpg-spacing-field').on('change, keyup', function () {
            var marginInput = $(this).val();
            var marginValue = marginInput.replace(/[^\d,]+/g, '');
            $(this).val(marginValue);
        });

        if ($("#tpg_image_type").length) {
            setImageBorderRadius();
        }

        $('#tpg_image_type').on('change', function () {
            setImageBorderRadius();
        });

    });

    function setImageBorderRadius() {
        var img_type = $("#tpg_image_type input[name='tpg_image_type']:checked").val(),
            img_border_radius = $("#tpg_image_border_radius").val(),
            img_border_radius = (img_type == 'circle') ? 50 : img_border_radius;
        $("#tpg_image_border_radius").val(img_border_radius);
    }

    function setGetParameter(paramName, paramValue) {
        let url = window.location.href;
        let hash = location.hash;
        url = url.replace(hash, '');
        if (url.indexOf("?") >= 0) {
            let params = url.substring(url.indexOf("?") + 1).split("&");
            let paramFound = false;
            params.forEach(function (param, index) {
                let p = param.split("=");
                if (p[0] == paramName) {
                    params[index] = paramName + "=" + paramValue;
                    paramFound = true;
                }
            });
            if (!paramFound) params.push(paramName + "=" + paramValue);
            url = url.substring(0, url.indexOf("?") + 1) + params.join("&");
        } else
            url += "?" + paramName + "=" + paramValue;
        return url + hash;
    }

    $(".rttpg-wrapper .rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var container = $(this).parents('.rt-tab-container'),
            nav = container.children('.rt-tab-nav'),
            content = container.children(".rt-tab-content"),
            $this = $(this),
            $id = $this.attr('href'),
            _target = $id.replace('#', '');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
        $('#_tpg_last_active_tab').val(_target);
        if (history.pushState) {
            var newurl = setGetParameter('section', _target);
            window.history.pushState({path: newurl}, '', newurl);
        }
    });

    rtTPGlayoutType();
    detailLinkEffect();
    customImageSize();
    customSmallImageSize();
    // preLoaderEffect();
    tpgEnableACF();
    featureImageEffect();
    tpgOrderByEffect();
    $("#link_to_detail_page_holder").on("change", "input[type='checkbox']", function () {
        detailLinkEffect();
    });
    $("#detail_page_link_type_holder").on("click", "input[type='radio']", function () {
        linkTypeEffect();
    });

    $("#posts_loading_type_holder").on("change", "input[type='radio']", function () {
        loadMoreButtonVisibility($(this).val());
    });

    $("#rt-tpg-sc-isotope-filter").on('change', function () {
        setDefaultItems();
    });
    $("#tgp_filter_taxonomy").on('change', function () {
        setDefaultItemsForFilter();
    });
    $("#ttp_filter-_taxonomy_filter").on('change', function () {
        taxonomyFilterEffect();
    });

    $("#featured_image_size").on('change', function () {
        customImageSize();
    });

    $("#featured_small_image_size").on('change', function () {
        customSmallImageSize();
    });

    $("#tpg_load_script").on('change', function () {
        //preLoaderEffect();
    });

    $("#show_acf_details").on('change', function () {
        tpgEnableACF();
    });

    function preLoaderEffect() {
        var preLoader = $("#tpg_load_script_holder input[name='tpg_load_script']:checked").val();
        if (preLoader) {
            $("#tpg_enable_preloader_holder").show();
        } else {
            $("#tpg_enable_preloader_holder").hide();
        }
    }

    function tpgEnableACF() {
        var tpgACF = $("#show_acf_details_holder input[name='show_acf_details']:checked").val();
        if (tpgACF) {
            $("#cf_group_details_holder, #cf_hide_empty_value_details_holder, #cf_show_only_value_details_holder, #cf_hide_group_title_details_holder").fadeIn();
        } else {
            $("#cf_group_details_holder, #cf_hide_empty_value_details_holder, #cf_show_only_value_details_holder, #cf_hide_group_title_details_holder").fadeOut();
        }
    }

    function customImageSize() {
        /* custom image size jquery */
        var fImageSize = $("#featured_image_size").val();
        if (fImageSize == "rt_custom") {
            $(".rt-sc-custom-image-size-holder").show();
        } else {
            $(".rt-sc-custom-image-size-holder").hide();
        }
    }

    function customSmallImageSize() {
        /* custom image size jquery */
        var fImageSize = $("#featured_small_image_size").val();
        if (fImageSize == "rt_custom") {
            $(".rt-sc-custom-small-image-size-holder").show();
        } else {
            $(".rt-sc-custom-small-image-size-holder").hide();
        }
    }

    function rtTPGlayoutType() {
        var $layout = $("#rttpg-layout input[name=layout]:checked"),
            layoutType = $layout.parent('.radio-image').attr('data-type'),
            selector = ".rt-tpg-radio-layout." + layoutType;

        $('#rttpg-layout .rt-tpg-radio-layout').hide();
        $('#layout_holder').hide();

        if (layoutType) {
            $("#rttpg-layout_type input[id=" + layoutType + "]").prop('checked', true);
            $('#layout_holder').show();
            $(selector).show();
        }
    }

    function rtTPGSelectedlayoutType() {
        var layout_type = $("#rttpg-layout_type input[name=layout_type]:checked"),
            layout_type_value = layout_type.val(),
            selector = ".rt-tpg-radio-layout." + layout_type_value;
        $('#rttpg-layout .rt-tpg-radio-layout').hide();

        if (layout_type_value == 'grid_hover') {
            $('#featured_small_image_size_holder').show();
            customSmallImageSize();
        } else {
            $('#featured_small_image_size_holder').hide();
            $('.rt-sc-custom-small-image-size-holder').hide();
        }

        if (!layout_type_value) {
            var selectChildByValue = $("#rttpg-layout input[name=layout]:checked"),
                ownParent = selectChildByValue.parent('.radio-image'),
                parentId = ownParent.attr('data-type');

            $("#rttpg-layout_type input[id=" + parentId + "]").prop('checked', true);
            selector = ".rt-tpg-radio-layout." + parentId;
            if (!selectChildByValue.val()) {
                $('#layout_holder').hide();
            } else {
                $('#layout_holder').show();
            }
        }
        $(selector).show();
    }

    function rtTPGIsotopTaxonomyFilter($this) {
        var arg = "post_type=" + $this.val();
        var bindElement = $this;
        var target = $('#tgp_filter_taxonomy_holder select');
        tpgAjaxCall(bindElement, 'rtTPGIsotopeFilter', arg, function (data) {
            if (!data.error) {
                target.html(data.data);
                setDefaultItems();
                setDefaultItemsForFilter();
                tgpLiveReloadScript();
            } else {
                console.log(data.msg);
            }
        });
    }

    function tpgTaxonomyFilterTrigger() {
        var target = $(".field-holder.sc-tpg-filter");
        if ($("#tgp_filter-_taxonomy_filter").is(':checked')) {
            target.show();
        } else {
            target.hide();
        }
    }

    function rtTPGTaxonomyListByPostType(postType, $this) {

        var arg = "post_type=" + postType;
        var bindElement = $this;

        $('#post-taxonomy input[name="tpg_taxonomy[]"]:checked').each(function () {
            arg += '&taxonomy[]=' + this.value;
        });

        tpgAjaxCall(bindElement, 'rtTPGTaxonomyListByPostType', arg, function (data) {
            if (!data.error) {
                $('.rt-tpg-filter.taxonomy > .taxonomy-field').html(data.data).show('slow');
            } else {
                console.log(data.msg);
            }
        });
    }

    function tlpShowHideScMeta() {
        tpgTaxonomyFilterTrigger();
        //var layout = $("#rt-tpg-sc-layout").val(),
        var layout_type = $("#rttpg-layout_type input[name=layout_type]:checked"),
            layout = layout_type.val(),
            selectedLayout = '',
            isIsotope = false,
            isCarousel = false,
            isWc = false,
            isEdd = false,
            isWcIsotope = false,
            isWcCarousel = false,
            isGrid = false,
            isList = false,
            isLOffset = false;

        if ($("#rttpg-layout input[name=layout]").length) {
            selectedLayout = $("#rttpg-layout input[name=layout]:checked").val();
        }

        if (layout) {
            isGrid = layout.match(/^grid/i);
            isList = layout.match(/^list/i);
            isCarousel = layout.match(/^carousel/i);
            isIsotope = layout.match(/^isotope/i);
            isWc = layout.match(/^wc/i) || layout.match(/^edd/i);
            isEdd = layout.match(/^edd/i);
            isWcIsotope = layout.match(/^wc-isotope/i) || layout.match(/^edd-isotope/i);
            isWcCarousel = layout.match(/^wc-carousel/i) || layout.match(/^edd-carousel/i);
            isLOffset = layout.match(/^offset/i);
            var lArray = ['layout4', 'layout5', 'layout6', 'layout7', 'layout8', 'layout9', 'layout10', 'layout13', 'layout15', 'layout16'];
            var target = jQuery("#rt-tpg-sc-layout").parent();
            target.find('.description').remove();
            if ($.inArray(layout, lArray) >= 0) {
                target.append("<p class='description' style='color:red'>Default or a feature image is mandatory for this layout</p>");
            }
        }

        var plType = $("#posts_loading_type");
        plType.find("label[for='posts_loading_type-pagination'],label[for='posts_loading_type-pagination_ajax']").show();
        $("#tgp_layout2_image_column_holder").hide();

        if (isGrid || isList || (isWc && !isWcCarousel && !isWcIsotope)) {
            $("#tgp_filter_holder").show();
            taxonomyFilterEffect();
            if (selectedLayout == "layout2" || selectedLayout == "layout3") {
                $("#tgp_layout2_image_column_holder").show();
            }
            $(".field-holder.isotope-item").hide();
        } else if (isLOffset) {
            $("#posts_loading_type_holder,.field-holder.isotope-item").hide();
            $("#tgp_filter_holder").show();
            taxonomyFilterEffect();
            $(".field-holder.offset-column-wrap select").find('option[value="4"]').remove();
        } else if (isCarousel || isWcCarousel) {
            $(".field-holder.sc-product-filter,.field-holder.pagination, .field-holder.pagination-item,.field-holder.isotope-item,.field-holder.sc-tpg-grid-filter").hide();
            $(".field-holder.carousel-item").show();
        } else if (isIsotope) {
            $(".field-holder.sc-product-filter,.field-holder.carousel-item,.field-holder.sc-tpg-grid-filter").hide();
            $(".field-holder.isotope-item,.field-holder.pagination").show();
            $("#posts_loading_type").find("label[for='posts_loading_type-pagination'],label[for='posts_loading_type-pagination_ajax']").hide();
            var ltype = $("#posts_loading_type").find("input[name=posts_loading_type]:checked").val();
            if (ltype == "pagination" || ltype == "pagination_ajax") {
                $("#posts_loading_type").find("label[for='posts_loading_type-load_more'] input").prop("checked", true);
            }
            if ($("#rt-tpg-sc-isotope-filter option:selected").length) {
                setDefaultItems();
            }
        } else if (isWc && !isWcIsotope && !isWcCarousel) {
            $(".field-holder.isotope-item,.field-holder.carousel-item,.field-holder.sc-product-filter,.field-holder.sc-tpg-grid-filter").hide();
            $(".field-holder.sc-product-filter,.field-holder.pagination").show();
        } else if (isWcIsotope) {
            $(".field-holder.sc-product-filter,.field-holder.carousel-item,.field-holder.sc-tpg-grid-filter").hide();
            $(".field-holder.isotope-item,.field-holder.pagination").show();
            $("#posts_loading_type").find("label[for='posts_loading_type-pagination'],label[for='posts_loading_type-pagination_ajax']").hide();
            var ltype = $("#posts_loading_type").find("input[name=posts_loading_type]:checked").val();
            if (ltype == "pagination" || ltype == "pagination_ajax") {
                $("#posts_loading_type").find("label[for='posts_loading_type-load_more'] input").prop("checked", true);
            }
            if ($("#rt-tpg-sc-isotope-filter option:selected").length) {
                setDefaultItems();
            }
        } else {
            $(".field-holder.isotope-item,.field-holder.carousel-item,.field-holder.sc-product-filter,.field-holder.sc-tpg-grid-filter").hide();
            $(".field-holder.pagination").show();
        }
        setDefaultItemsForFilter();
        tpgOrderByEffect();
        if ($("#post-taxonomy input[name='tpg_taxonomy[]']").is(":checked")) {
            $(".rt-tpg-filter-item.term-filter-item").show();
        } else {
            $(".rt-tpg-filter-item.term-filter-item").hide();
        }

        var pagination = $("#rt-tpg-pagination").is(':checked');
        var isLoadMore = $("#posts_loading_type_holder #posts_loading_type-load_more").is(':checked');

        if (pagination && !(isCarousel || isWc || isWcIsotope || isWcCarousel)) {
            $(".field-holder.pagination-item").show();

        } else if (pagination && (isLOffset)) {

            $("#posts_loading_type_holder").hide();
        } else {
            $(".field-holder.pagination-item").hide();
        }

        if ((isLoadMore && pagination) && !(isCarousel || isEdd)) {
            $('.field-holder.pagination-load-more-label').show();
        } else {
            $('.field-holder.pagination-load-more-label').hide();
        }
    }

    function taxonomyFilterEffect() {
        if ($("#tgp_filter-_taxonomy_filter").is(':checked')) {
            $(".field-holder.sc-tpg-grid-filter").show();
            filterEffectToPagination();
        } else {
            $(".field-holder.sc-tpg-grid-filter").not("#tgp_filter_holder").hide();
        }
    }

    function filterEffectToPagination() {
        var plType = $("#posts_loading_type"),
            lType = plType.find("input[name=posts_loading_type]:checked").val();
        if ($("#tgp_filter_holder input[name='tgp_filter[]']").is(':checked')) {
            plType.find("label[for='posts_loading_type-pagination']").hide();
            if (lType == "pagination") {
                plType.find("label[for='posts_loading_type-pagination_ajax'] input").prop("checked", true);
            }
        } else {
            plType.find("label[for='posts_loading_type-pagination']").show();
        }
    }

    function tpgOrderByEffect() {
        var Oval = $('#order_by').val(),
            vList = ['meta_value_num', 'meta_value', 'meta_value_datetime'];

        if ($.inArray(Oval, vList) !== -1) {
            $('#tpg_meta_key_holder').show();
        } else {
            $('#tpg_meta_key_holder').hide();
        }
    }

    $('#term_category_holder select').on('change', function (e) {
        setDefaultItems();
        if ($("#tgp_filter-_taxonomy_filter").is(':checked')) {
            setDefaultItemsForFilter();
        }
    });

    $('#term_post_tag_holder select').on('change', function (e) {
        setDefaultItems();
        if ($("#tgp_filter-_taxonomy_filter").is(':checked')) {
            setDefaultItemsForFilter();
        }
    });

    function setDefaultItems() {
        var target_from = $("#rt-tpg-sc-isotope-filter"),
            target = $("#rt-tpg-sc-isotope-default-filter"),
            $fId = target_from.val();

        var $term = [];

        if ($fId == 'category') {
            if ($('#term_category_holder').length && $('#post-taxonomy-category')) {
                var selected_term = $('#term_category_holder select').select2('data');
                selected_term.forEach(function (element) {
                    $term.push(element.id);
                });
            }
        } else if ($fId == 'post_tag') {
            $term = [];
            if ($('#term_post_tag_holder').length && $('#post-taxonomy-post_tag')) {
                var selected_term = $('#term_post_tag_holder select').select2('data');
                selected_term.forEach(function (element) {
                    $term.push(element.id);
                });
            }
        }

        if ($fId) {
            var data = 'action=defaultFilterItem&filter=' + $fId + '&include=' + $term + "&rttpg_nonce=" + rttpg.nonce;
            $.ajax({
                type: "post",
                url: rttpg.ajaxurl,
                data: data,
                beforeSend: function () {
                    $("<span class='rt-loading'></span>").insertAfter(target);
                },
                success: function (data) {
                    if (!data.error) {
                        var selected = target.data('selected');
                        target.html(data.data);
                        if (selected) {
                            target.val(selected).trigger("change");
                        }
                    } else {
                        console.log(data.msg);
                    }
                    target.parent().find(".rt-loading").remove();
                },
                error: function () {
                    target.parent().find(".rt-loading").remove();
                }
            });
        }
    }

    function setDefaultItemsForFilter() {
        var target_from = $("#tgp_filter_taxonomy"),
            target = $('#tgp_default_filter'),
            $fId = target_from.val();

        var $term = [];

        if ($fId == 'category') {
            if ($('#term_category_holder').length && $('#post-taxonomy-category')) {
                var selected_term = $('#term_category_holder select').select2('data');
                selected_term.forEach(function (element) {
                    $term.push(element.id);
                });
            }
        } else if ($fId == 'post_tag') {
            $term = [];
            if ($('#term_post_tag_holder').length && $('#post-taxonomy-post_tag')) {
                var selected_term = $('#term_post_tag_holder select').select2('data');
                selected_term.forEach(function (element) {
                    $term.push(element.id);
                });
            }
        }

        if ($fId) {
            var data = 'action=defaultFilterItem&filter=' + $fId + '&include=' + $term + "&rttpg_nonce=" + rttpg.nonce;
            $.ajax({
                type: "post",
                url: rttpg.ajaxurl,
                data: data,
                beforeSend: function () {
                    $("<span class='rt-loading'></span>").insertAfter(target);
                },
                success: function (data) {
                    if (!data.error) {
                        var selected = target.data('selected');
                        target.html(data.data);
                        if (selected) {
                            target.val(selected).trigger("change");
                        }

                    } else {
                        console.log(data.msg);
                    }
                    target.next(".rt-loading").remove();
                }
            });
        }
    }

    function rtTPGIsotopeFilter($this) {
        var arg = "post_type=" + $this.val();
        var bindElement = $this;
        var target = jQuery('.field-holder.sc-isotope-filter .field > select');
        tpgAjaxCall(bindElement, 'rtTPGIsotopeFilter', arg, function (data) {
            if (!data.error) {
                target.html(data.data);
                setDefaultItems();
                tgpLiveReloadScript();
            } else {
                console.log(data.msg);
            }
        });
    }

    function rtTPGTermListByTaxonomy($this) {
        var term = $this.val();
        var targetHolder = $('.rt-tpg-filter.taxonomy').children('.rt-tpg-filter-item').children('.field-holder').children('.term-filter-holder');
        var target = targetHolder.children('.term-filter-item-container.' + term);
        if ($this.is(':checked')) {
            var arg = "taxonomy=" + $this.val();
            var bindElement = $this;
            tpgAjaxCall(bindElement, 'rtTPGTermListByTaxonomy', arg, function (data) {
                if (!data.error) {
                    targetHolder.show();
                    $(data.data).prependTo(targetHolder).fadeIn('slow');
                    tgpLiveReloadScript();
                } else {
                    console.log(data.msg)
                }
            });
        } else {
            target.hide('slow').html('').remove();
        }

        var termLength = jQuery('input[name="tpg_taxonomy[]"]:checked').length;
        if (termLength > 1) {
            $('.field-holder.term-filter-item-relation ').show('slow');
        } else {
            $('.field-holder.term-filter-item-relation ').hide('slow');
        }

    }

    function detailLinkEffect() {
        var detailPageLink = $("#link_to_detail_page_holder input[name='link_to_detail_page']:checked").val();
        if (detailPageLink) {
            $(".field-holder.detail-page-link-type").show();
        } else {
            $(".field-holder.detail-page-link-type,.field-holder.popup-type,.field-holder.tpg-link-target").hide();
        }
        linkTypeEffect();
    }

    function linkTypeEffect() {
        var linkType = $("#detail_page_link_type_holder input[name='detail_page_link_type']:checked").val(),
            detailPageLink = $("#link_to_detail_page_holder input[name='link_to_detail_page']:checked").val();
        if (linkType == "popup" && detailPageLink) {
            $(".field-holder.popup-type").show();
            $(".field-holder.tpg-link-target").hide()
        } else {
            $(".field-holder.popup-type").hide();
            $(".field-holder.tpg-link-target").show();
        }
    }

    function loadMoreButtonVisibility(value) {
        if ('load_more' === value) {
            $('.field-holder.pagination-load-more-label').show();
        } else {
            $('.field-holder.pagination-load-more-label').hide();
        }

    }

    function tpgAjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(rttpg.nonceID);
        if (n < 0) {
            data = data + "&rttpg_nonce=" + rttpg.nonce;
        }

        data += "&uid="+rttpg.uid;

        $.ajax({
            type: "post",
            url: rttpg.ajaxurl,
            data: data,
            beforeSend: function () {
                $("<span class='rt-loading'></span>").insertAfter(element);
            },
            success: function (data) {
                element.next(".rt-loading").remove();
                handle(data);
            },
            error: function (e) {
                element.next(".rt-loading").remove();
            }
        });
    }

    $("#rt-tpg-settings-form").on('click', '.rt-licensing-btn', function (e) {
        e.preventDefault();
        var self = $(this),
            type = self.attr('name'),
            data = 'type=' + type;
        $("#license_key_holder").find(".rt-licence-msg").remove();
        tpgAjaxCall(self, 'rtTPGManageLicencing', data, function (data) {
            if (!data.error) {
                self.val(data.value);
                self.attr('name', data.name);
                self.addClass(data.class);
                if (data.name == 'license_deactivate') {
                    self.removeClass('button-primary');
                    self.addClass('danger');
                } else if (data.name == 'license_activate') {
                    self.removeClass('danger');
                    self.addClass('button-primary');
                }
            }
            if (data.msg) {
                $("<div class='rt-licence-msg'>" + data.msg + "</div>").insertAfter(self);
            }
            self.blur();
        });

        return false;
    });

    $("#rt-tpg-settings-form").on('click', '.rtSaveButton', function (e) {
        e.preventDefault();
        $('.rt-response').hide();
        var arg = $("#rt-tpg-settings-form").serialize();
        var bindElement = $('.rtSaveButton');
        tpgAjaxCall(bindElement, 'rtTPGSettings', arg, function (data) {
            if (data.error) {
                $('.rt-response').addClass('error');
                $('.rt-response').show('slow').text(data.msg);
            } else {
                $('.rt-response').addClass('updated');
                $('.rt-response').removeClass('error');
                $('.rt-response').show('slow').text(data.msg);
                var holder = $("#license_key_holder");
                if (!$(".license-status", holder).length && $("#license_key", holder).val()) {
                    var bindElement = $("#license_key", holder),
                        target = $(".description", holder);
                    target.find(".rt-licence-msg").remove();
                    tpgAjaxCall(bindElement, 'rtTPG_active_Licence', '', function (data) {
                        if (!data.error) {
                            target.append("<span class='license-status'>" + data.html + "</span>");
                        }
                        if (data.msg) {
                            if (target.find(".rt-licence-msg").length) {
                                target.find(".rt-licence-msg").html(data.msg);
                            } else {
                                target.append("<span class='rt-licence-msg'>" + data.msg + "</span>");
                            }
                            if (!data.error) {
                                target.find(".rt-licence-msg").addClass('success');
                            }
                        }
                    });
                }
                if (!$("#license_key", holder).val()) {
                    $('.license-status', holder).remove();
                }
            }
        });
        return false;
    });

    function rtTgpFilter() {
        $("#post_filter input[type=checkbox]:checked").each(function () {
            var id = $(this).val();
            if (id == 'tpg_taxonomy') {
                if (this.checked) {
                    rtTPGTaxonomyListByPostType(postType, $(this));
                } else {
                    $('.rt-tpg-filter.taxonomy > .taxonomy-field').hide('slow').html('');
                    $('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-holder').hide('slow').html('');
                    $('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-item-relation').hide('slow');
                }
            }
            $(".rt-tpg-filter." + id).show();
        });

        $("#post-taxonomy input[type=checkbox]:checked").each(function () {
            var id = $(this).val();
            $(".filter-item." + id).show();
        });
    }

    function tgpLiveReloadScript() {
        $("select.rt-select2").select2({
            theme: "classic",
            dropdownAutoWidth: true,
            width: '100%'
        });
    }

})(this, jQuery);
