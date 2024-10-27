(function ($) {
    'use strict';

    $(window).resize(function () {
        overlayIconResizeTpg();
    });
    $(window).on('load', function () {
        overlayIconResizeTpg();
    });
    $("#tpg-preview-container").on('click', 'a.tpg-zoom', function (e) {
        e.preventDefault();
        return false;
    });

    $(document).on({
        mouseenter: function () {
            var $this = $(this);
            var $title = $this.attr('title');
            $tooltip = '<div class="rt-tooltip" id="rt-tooltip">' +
                '<div class="rt-tooltip-content">' + $title + '</div>' +
                '<div class="rt-tooltip-bottom"></div>' +
                '</div>';
            $('body').append($tooltip);
            var $tooltip = $('body > .rt-tooltip');
            var tHeight = $tooltip.outerHeight();
            var tBottomHeight = $tooltip.find('.rt-tooltip-bottom').outerHeight();
            var tWidth = $tooltip.outerWidth();
            var tHolderWidth = $this.outerWidth();
            var top = $this.offset().top - (tHeight + tBottomHeight) + 14;
            var left = $this.offset().left;
            $tooltip.css('top', top + 'px');
            $tooltip.css('left', left + 'px');
            $tooltip.css('opacity', 1);
            $tooltip.show();
            if (tWidth <= tHolderWidth) {
                var itemLeft = (tHolderWidth - tWidth) / 2;
                left = left + itemLeft;
                $tooltip.css('left', left + 'px');
            } else {
                var itemLeft = (tWidth - tHolderWidth) / 2;
                left = left - itemLeft;
                if (left < 0) {
                    left = 0;
                }
                $tooltip.css('left', left + 'px');
            }
        },
        mouseleave: function () {
            $('body > .rt-tooltip').remove();
        }
    }, '.rt-tpg-social-share a');

    $("span.rtAddImage").on("click", function (e) {
        var file_frame, image_data;
        var $this = $(this).parents('.rt-image-holder');
        if (undefined !== file_frame) {
            file_frame.open();
            return;
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload Media For your profile gallery',
            button: {
                text: 'Use this media'
            },
            multiple: false
        });
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            var imgId = attachment.id;
            var imgUrl = (typeof attachment.sizes.thumbnail === "undefined") ? attachment.url : attachment.sizes.thumbnail.url;
            $this.find('.hidden-image-id').val(imgId);
            $this.find('.rtRemoveImage').show();
            $this.find('img').remove();
            $this.find('.rt-image-preview').append("<img src='" + imgUrl + "' />");
            renderTpgPreview();
        });
        // Now display the actual file_frame
        file_frame.open();
    });

    $("span.rtRemoveImage").on("click", function (e) {
        e.preventDefault();
        if (confirm("Are you sure?")) {
            var $this = $(this).parents('.rt-image-holder');
            $this.find('.hidden-image-id').val('');
            $this.find('.rtRemoveImage').hide();
            $this.find('img').remove();
            renderTpgPreview();
        }
    });

    $("#rttpg_meta").on('change', 'select,input', function () {
        renderTpgPreview();
    });

    $("#rttpg_meta").on("input propertychange", function () {
        renderTpgPreview();
    });
    renderTpgPreview();

    function IsotopeNCarouselRender() {
        $('.rt-tpg-container').each(function () {
            var container = $(this),
                str = $(this).attr("data-layout"),
                id = $.trim(container.attr('id')),
                scID = $.trim(container.attr("data-sc-id")),
                $default_order_by = $('.rt-order-by-action .order-by-default', container),
                $default_order = $('.rt-sort-order-action .rt-sort-order-action-arrow', container),
                $taxonomy_filter = $('.rt-filter-item-wrap.rt-tax-filter', container),
                $pagination_wrap = $('.rt-pagination-wrap', container),
                $loadmore = $('.rt-loadmore-action', container),
                $infinite = $('.rt-infinite-action', container),
                $page_prev_next = $('.rt-cb-page-prev-next', container),
                $page_numbers = $('.rt-page-numbers', container),
                html_loading = '<div class="rt-loading-overlay"></div><div class="rt-loading rt-ball-clip-rotate"><div></div></div>',
                preLoader = container.find('.tpg-pre-loader'),
                loader = container.find(".rt-content-loader"),
                contentLoader = container.children(".rt-row.rt-content-loader"),
                search_wrap = container.find(".rt-search-filter-wrap"),
                tpg_order = '',
                tpg_order_by = '',
                tpg_taxonomy = '',
                tpg_term = '',
                tpg_search = '',
                tpg_paged = 1,
                temp_total_pages = parseInt($pagination_wrap.attr('data-total-pages'), 10),
                tpg_total_pages = typeof (temp_total_pages) != 'undefined' && temp_total_pages != '' ? temp_total_pages : 1,
                temp_posts_per_page = parseInt($pagination_wrap.attr('data-posts-per-page'), 10),
                tpg_posta_per_page = typeof (temp_posts_per_page) != 'undefined' && temp_posts_per_page != '' ? temp_posts_per_page : 3,
                infinite_status = 0,
                paramsRequest = {},
                mIsotopWrap = '',
                IsotopeWrap = '',
                isMasonary = $('.rt-row.rt-content-loader.tpg-masonry', container),
                isIsotop = $(".rt-tpg-isotope", container),
                IsoButton = $(".rt-tpg-isotope-buttons", container),
                IsoDropdownFilter = $("select.isotope-dropdown-filter", container),
                isCarousel = $('.rt-swiper-holder', container),
                placeholder_loading = function () {
                    if (loader.find('.rt-loading-overlay').length == 0) {
                        loader.addClass('tpg-pre-loader');
                        loader.append(html_loading);
                    }
                },
                remove_placeholder_loading = function () {
                    loader.find('.rt-loading-overlay, .rt-loading').remove();
                    loader.removeClass('tpg-pre-loader');
                    $loadmore.removeClass('rt-lm-loading');
                    $page_numbers.removeClass('rt-lm-loading');
                    $infinite.removeClass('rt-active-elm');
                    search_wrap.find('input').prop("disabled", false);
                },
                check_query = function () {
                    if ($taxonomy_filter.length > 0) {
                        tpg_taxonomy = $taxonomy_filter.attr('data-taxonomy');
                        var term;
                        if ($taxonomy_filter.hasClass('rt-filter-button-wrap')) {
                            term = $taxonomy_filter.find('.rt-filter-button-item.selected').attr('data-term');
                        } else {
                            term = $taxonomy_filter.find('.term-default').attr('data-term');
                        }
                        if (typeof (term) != 'undefined' && term != '') {
                            tpg_term = term;
                        }
                    }
                    if ($default_order_by.length > 0) {
                        var order_by_param = $default_order_by.attr('data-order-by');
                        if (typeof (order_by_param) != 'undefined' && order_by_param != '' && (order_by_param.toLowerCase())) {
                            tpg_order_by = order_by_param;
                        }
                    }
                    if ($default_order_by.length > 0) {
                        var order_param = $default_order.attr('data-sort-order');
                        if (typeof (order_param) != 'undefined' && order_param != '' && (order_param == 'DESC' || order_param == 'ASC')) {
                            tpg_order = order_param;
                        }
                    }
                    if (search_wrap.length > 0) {
                        tpg_search = $.trim(search_wrap.find('input').val());
                    }
                    paramsRequest = {
                        'scID': scID,
                        'order': tpg_order,
                        'order_by': tpg_order_by,
                        'taxonomy': tpg_taxonomy,
                        'term': tpg_term,
                        'paged': tpg_paged,
                        'action': 'tpgLayoutAjaxAction',
                        'search': tpg_search,
                        'rttpg_nonce': rttpg.nonce
                    };
                },
                infinite_scroll = function () {
                    if (infinite_status == 1 || $infinite.hasClass('rt-hidden-elm') || $pagination_wrap.length == 0) {
                        return;
                    }
                    var ajaxVisible = $pagination_wrap.offset().top,
                        ajaxScrollTop = $(window).scrollTop() + $(window).height();

                    if (ajaxVisible <= (ajaxScrollTop) && (ajaxVisible + $(window).height()) > ajaxScrollTop) {
                        infinite_status = 1; //stop inifite scroll
                        tpg_paged = tpg_paged + 1;
                        $infinite.addClass('rt-active-elm');
                        ajax_action(true, true);
                    }
                },
                generateData = function (number) {
                    var result = [];
                    for (var i = 1; i < number + 1; i++) {
                        result.push(i);
                    }
                    return result;
                },
                setPostCount = function () {
                    if ($taxonomy_filter.length > 0 && $taxonomy_filter.hasClass('has-post-count')) {
                        if ($taxonomy_filter.hasClass('rt-filter-button-wrap')) {
                            var total = 0;
                            $taxonomy_filter.find('span').each(function () {
                                var self = $(this),
                                    target = self.find('span.rt-post-count');
                                if (target.length > 0) {
                                    total = total + parseInt($.trim(target.html()), 10);
                                }
                            });
                            $taxonomy_filter.find('span[data-term="all"]').append(" (<span class='rt-post-count'>" + total + "</span>)");
                        } else if ($taxonomy_filter.hasClass('rt-filter-dropdown-wrap')) {
                            var total = 0;
                            $taxonomy_filter.find('.term-dropdown.rt-filter-dropdown .rt-filter-dropdown-item').each(function () {
                                var self = $(this),
                                    target = self.find('span.rt-post-count');
                                if (target.length > 0) {
                                    total = total + parseInt($.trim(target.html()), 10);
                                }
                            });
                            if ($taxonomy_filter.find('span.rt-filter-dropdown-default').attr("data-term") == "all") {
                                $taxonomy_filter.find('span.rt-filter-dropdown-default .rt-text').append(" (<span class='rt-post-count'>" + total + "</span>)");
                            } else if ($taxonomy_filter.find('.term-dropdown.rt-filter-dropdown').find('.term-dropdown-item[data-term="all"]').length) {
                                var dd = $taxonomy_filter.find('.term-default.rt-filter-dropdown-default').find('span.rt-post-count').html();
                                total = total + parseInt($.trim(dd), 10);
                                $taxonomy_filter.find('.term-dropdown.rt-filter-dropdown').find('.term-dropdown-item[data-term="all"]').append(" (<span class='rt-post-count'>" + total + "</span>)");
                            }

                        }
                    }
                },
                createPagination = function () {
                    if ($page_numbers.length > 0) {
                        $page_numbers.pagination({
                            dataSource: generateData(tpg_total_pages * parseFloat(tpg_posta_per_page)),
                            pageSize: parseFloat(tpg_posta_per_page),
                            autoHidePrevious: true,
                            autoHideNext: true,
                            prevText: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
                            nextText: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
                        });
                        $page_numbers.addHook('beforePaging', function (pagination) {
                            infinite_status = 1;
                            tpg_paged = pagination;
                            $page_numbers.addClass('rt-lm-loading');
                            $page_numbers.pagination('disable');
                            ajax_action(true, false);
                        });
                        if (tpg_total_pages <= 1) {
                            $page_numbers.addClass('rt-hidden-elm');
                        } else {
                            $page_numbers.removeClass('rt-hidden-elm');
                        }
                    }
                },
                load_gallery_image_popup = function () {
                    container.find('.rt-row.layout17').each(function () {
                        var self = $(this);
                        self.magnificPopup({
                            delegate: 'a.tpg-zoom',
                            type: 'image',
                            gallery: {
                                enabled: true
                            }
                        });
                    });

                },
                ajax_action = function (page_request, append) {
                    page_request = page_request || false;
                    append = append || false;
                    if (!page_request) {
                        tpg_paged = 1;
                    }
                    check_query();
                    if (page_request == true && tpg_total_pages > 1 && paramsRequest.paged > tpg_total_pages) {
                        remove_placeholder_loading();
                        return;
                    }
                    $.ajax({
                        url: rttpg.ajaxurl,
                        type: 'POST',
                        data: paramsRequest,
                        cache: false,
                        beforeSend: function () {
                            placeholder_loading();
                        },
                        success: function (data) {
                            if (!data.error) {
                                tpg_paged = data.paged;
                                tpg_total_pages = data.total_pages;
                                if (data.paged >= tpg_total_pages) {
                                    if ($loadmore.length) {
                                        $loadmore.addClass('rt-hidden-elm');
                                    }
                                    if ($infinite.length) {
                                        infinite_status = 1;
                                        $infinite.addClass('rt-hidden-elm');
                                    }
                                    if ($page_prev_next.length) {
                                        if (!page_request) {
                                            $page_prev_next.addClass('rt-hidden-elm');
                                        } else {
                                            $page_prev_next.find('.rt-cb-prev-btn').removeClass('rt-disabled');
                                            $page_prev_next.find('.rt-cb-next-btn').addClass('rt-disabled');
                                        }
                                    }
                                } else {
                                    if ($loadmore.length) {
                                        $loadmore.removeClass('rt-hidden-elm');
                                    }
                                    if ($infinite.length) {
                                        infinite_status = 0;
                                        $infinite.removeClass('rt-hidden-elm');
                                    }
                                    if ($page_prev_next.length) {
                                        if (!page_request) {
                                            $page_prev_next.removeClass('rt-hidden-elm');
                                        } else {
                                            if (data.paged == 1) {
                                                $page_prev_next.find('.rt-cb-prev-btn').addClass('rt-disabled');
                                                $page_prev_next.find('.rt-cb-next-btn').removeClass('rt-disabled');
                                            } else {
                                                $page_prev_next.find('.rt-cb-prev-btn').removeClass('rt-disabled');
                                                $page_prev_next.find('.rt-cb-next-btn').removeClass('rt-disabled');
                                            }
                                        }
                                    }
                                }
                                if (append) {
                                    if (isIsotop.length) {
                                        IsotopeWrap.append(data.data)
                                            .isotope('appended', data.data)
                                            .isotope('reloadItems')
                                            .isotope('updateSortData')
                                            .isotope();
                                        IsotopeWrap.imagesLoaded(function () {
                                            preFunction();
                                            IsotopeWrap.isotope();
                                        });
                                        if (IsoButton.attr('data-count')) {
                                            isoFilterCounter(container, IsotopeWrap);
                                        }
                                    } else if (isMasonary.length) {
                                        mIsotopWrap.append(data.data).isotope('appended', data.data).isotope('updateSortData').isotope('reloadItems');
                                        mIsotopWrap.imagesLoaded(function () {
                                            mIsotopWrap.isotope();
                                        });
                                    } else {
                                        contentLoader.append(data.data);
                                    }
                                } else {
                                    contentLoader.html(data.data);
                                }
                                contentLoader.imagesLoaded(function () {
                                    preFunction();
                                    remove_placeholder_loading();
                                    load_gallery_image_popup();
                                });
                                if (!page_request) {
                                    createPagination();
                                }
                            } else {
                                remove_placeholder_loading();
                            }
                        },
                        error: function (error) {
                            remove_placeholder_loading();
                        }
                    });
                    if ($('.paginationjs-pages .paginationjs-page', $page_numbers).length > 0) {
                        $page_numbers.pagination('enable');
                    }
                };

            switch ($pagination_wrap.attr('data-type')) {
                case 'load_more':
                    $loadmore.on('click', function () {
                        $(this).addClass('rt-lm-loading');
                        tpg_paged = tpg_paged + 1;
                        ajax_action(true, true);
                    });
                    break;
                case 'pagination_ajax':
                    createPagination();
                    break;
                case 'pagination':
                    break;
                case 'load_on_scroll':
                    $(window).on('scroll load', function () {
                        infinite_scroll();
                    });
                    break;
                case 'page_prev_next':
                    if (tpg_paged == 1) {
                        $page_prev_next.find('.rt-cb-prev-btn').addClass('rt-disabled');
                    }
                    if (tpg_paged == tpg_total_pages) {
                        $page_prev_next.find('.rt-cb-next-btn').addClass('rt-disabled');
                    }
                    if (tpg_total_pages == 1) {
                        $page_prev_next.addClass('rt-hidden-elm');
                    }
                    break;
            }

            if (str) {
                var qsRegex,
                    buttonFilter;
                if (preLoader.find('.rt-loading-overlay').length == 0) {
                    preLoader.append(html_loading);
                }

                if (isCarousel.length) {
                    isCarousel.imagesLoaded(function () {
                        $(".rt-swiper-holder").each(function () {

                            var rtSwiperSlider = $(this).get(0),
                                prevButton = $(this).parent().children().find(".swiper-button-prev").get(0),
                                nextButton = $(this).parent().children().find(".swiper-button-next").get(0),
                                dotPagination = $(this).parent().children().find(".swiper-pagination").get(0),
                                dItem = parseInt(container.attr('data-desktop-col'), 10),
                                tItem = parseInt(container.attr('data-tab-col'), 10),
                                mItem = parseInt(container.attr('data-mobile-col'), 10),
                                options = isCarousel.data('rtowl-options'),
                                rtSwiperData = {
                                    slidesPerView: mItem ? mItem : 1,
                                    spaceBetween: 24,
                                    loop: options.loop,
                                    slideToClickedSlide: true,
                                    speed: options.speed,
                                    autoHeight: options.autoHeight,
                                    breakpoints: {
                                        0: {
                                            slidesPerView: mItem ? mItem : 1,
                                        },
                                        768: {
                                            slidesPerView: tItem ? tItem : 2,
                                        },
                                        992: {
                                            slidesPerView: dItem ? dItem : 3,
                                        },
                                    }
                                };

                            if (options.autoPlay) {
                                Object.assign(rtSwiperData, {
                                    autoplay: {
                                        delay: options.autoPlayTimeOut,
                                    }
                                });
                            }
                            if (options.nav) {
                                Object.assign(rtSwiperData, {
                                    navigation: {
                                        nextEl: nextButton,
                                        prevEl: prevButton,
                                    }
                                });
                            }
                            if (options.dots) {
                                Object.assign(rtSwiperData, {
                                    pagination: {
                                        el: dotPagination,
                                        clickable: true,
                                        dynamicBullets: true,
                                    }
                                });
                            }

                            new Swiper(rtSwiperSlider, rtSwiperData);
                            remove_placeholder_loading();
                        });
                    });
                } else if (isIsotop.length) {
                    var IsoURL = IsoButton.attr('data-url'),
                        IsoCount = IsoButton.attr('data-count');
                    if (!buttonFilter) {
                        if (IsoButton.length) {
                            buttonFilter = IsoButton.find('button.selected').data('filter');
                        } else if (IsoDropdownFilter.length) {
                            buttonFilter = IsoDropdownFilter.val();
                        }
                    }
                    IsotopeWrap = isIsotop.imagesLoaded(function () {
                        preFunction();
                        IsotopeWrap.isotope({
                            itemSelector: '.isotope-item',
                            masonry: {columnWidth: '.isotope-item'},
                            filter: function () {
                                var $this = $(this);
                                var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
                                var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                                return searchResult && buttonResult;
                            }
                        });
                        setTimeout(function () {
                            IsotopeWrap.isotope();
                            remove_placeholder_loading();
                        }, 100);
                    });
                    // use value of search field to filter
                    var $quicksearch = container.find('.iso-search-input').keyup(debounce(function () {
                        qsRegex = new RegExp($quicksearch.val(), 'gi');
                        IsotopeWrap.isotope();
                    }));

                    IsoButton.on('click', 'button', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).attr('data-filter');
                        if (IsoURL) {
                            location.hash = "filter=" + encodeURIComponent(buttonFilter);
                        } else {
                            IsotopeWrap.isotope();
                            $(this).parent().find('.selected').removeClass('selected');
                            $(this).addClass('selected');
                        }
                    });
                    if (IsoURL) {
                        windowHashChange(IsotopeWrap, IsoButton);
                        $(window).on("hashchange", function () {
                            windowHashChange(IsotopeWrap, IsoButton);
                        });
                    }
                    if (IsoCount) {
                        isoFilterCounter(container, IsotopeWrap);
                    }
                    IsoDropdownFilter.on('change', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).val();
                        IsotopeWrap.isotope();
                    });

                } else if (container.find('.rt-row.rt-content-loader.tpg-masonry').length) {
                    var masonryTarget = $('.rt-row.rt-content-loader.tpg-masonry', container);
                    mIsotopWrap = masonryTarget.imagesLoaded(function () {
                        preFunction();
                        mIsotopWrap.isotope({
                            itemSelector: '.masonry-grid-item',
                            masonry: {columnWidth: '.masonry-grid-item'}
                        });
                        remove_placeholder_loading();
                    });
                }
            }

            $('#' + id).on('click', '.rt-search-filter-wrap .rt-action', function (e) {
                search_wrap.find('input').prop("disabled", true);
                ajax_action();
            });
            $('#' + id).on('keypress', '.rt-search-filter-wrap .rt-search-input', function (e) {
                if (e.which == 13) {
                    search_wrap.find('input').prop("disabled", true);
                    ajax_action();
                }
            });
            $('#' + id).on('click', '.rt-filter-dropdown-wrap', function (event) {
                var self = $(this);
                self.toggleClass('active-dropdown');
            });// Dropdown click
            $('#' + id).on('click', '.term-dropdown-item', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    default_target = $taxonomy_filter.find('.rt-filter-dropdown-default'),
                    old_param = default_target.attr('data-term'),
                    old_text = default_target.find('.rt-text').html();
                $this_item.parents('.rt-filter-dropdown-wrap').removeClass('active-dropdown');
                $this_item.parents('.rt-filter-dropdown-wrap').toggleClass('active-dropdown');
                default_target.attr('data-term', $this_item.attr('data-term'));
                default_target.find('.rt-text').html($this_item.html());
                $this_item.attr('data-term', old_param);
                $this_item.html(old_text);
                ajax_action();
            });//term
            $('#' + id).on('click', '.order-by-dropdown-item', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    old_param = $default_order_by.attr('data-order-by'),
                    old_text = $default_order_by.find('.rt-text-order-by').html();

                $this_item.parents('.rt-order-by-action').removeClass('active-dropdown');
                $this_item.parents('.rt-order-by-action').toggleClass('active-dropdown');
                $default_order_by.attr('data-order-by', $this_item.attr('data-order-by'));
                $default_order_by.find('.rt-text-order-by').html($this_item.html());
                $this_item.attr('data-order-by', old_param);
                $this_item.html(old_text);
                ajax_action();
            });//Order By

            //Sort Order
            $('#' + id).on('click', '.rt-sort-order-action', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    $sort_order_elm = $('.rt-sort-order-action-arrow', $this_item),
                    sort_order_param = $sort_order_elm.attr('data-sort-order');
                if (typeof (sort_order_param) != 'undefined' && sort_order_param.toLowerCase() == 'desc') {
                    $default_order.attr('data-sort-order', 'ASC');
                } else {
                    $default_order.attr('data-sort-order', 'DESC');
                }
                ajax_action();
            });//Sort Order

            $taxonomy_filter.on('click', '.rt-filter-button-item', function () {
                var self = $(this);
                self.parents('.rt-filter-button-wrap').find('.rt-filter-button-item').removeClass('selected');
                self.addClass('selected');
                ajax_action();
            });

            $page_prev_next.on('click', '.rt-cb-prev-btn', function (event) {
                if (tpg_paged <= 1) {
                    return;
                }
                tpg_paged = tpg_paged - 1;
                ajax_action(true, false);
            });
            $page_prev_next.on('click', '.rt-cb-next-btn', function (event) {
                if (tpg_paged >= tpg_total_pages) {
                    return;
                }
                tpg_paged = tpg_paged + 1;
                ajax_action(true, false);
            });
            setPostCount();
            load_gallery_image_popup();

        });

        $(".rt-tpg-container a.disabled").each(function () {
            $(this).prop("disabled", true);
            $(this).removeAttr("href");
        });
    }

    function preFunction() {
        overlayIconResizeTpg();
    }


// debounce so filtering doesn't happen every millisecond
    function debounce(fn, threshold) {
        var timeout;
        return function debounced() {
            if (timeout) {
                clearTimeout(timeout);
            }

            function delayed() {
                fn();
                timeout = null;
            }

            setTimeout(delayed, threshold || 100);
        };
    }

    if ($("#rttpg_meta .rt-color").length) {
        var cOptions = {
            defaultColor: false,
            change: function (event, ui) {
                setTimeout(function () {
                    renderTpgPreview();
                }, 1);
            },
            clear: function () {
                renderTpgPreview();
            },
            hide: true,
            palettes: true
        };
        $("#rttpg_meta .rt-color").wpColorPicker(cOptions);
    }

    function renderTpgPreview() {
        if ($("#rttpg_meta").length) {
            var data = $("#rttpg_meta").find('input[name],select[name],textarea[name]').serialize(),
                container = $("#rttpg_meta").find('.rt-tpg-container'),
                loader = container.find(".rt-content-loader");
            data = data + '&' + $.param({'sc_id': $('#post_ID').val() || 0});
            $(".rt-loading").remove();
            $(".rt-response").addClass('loading');
            $(".rt-response").html('<span>Loading...</span>');
            tpgAjaxCall(null, 'tpgPreviewAjaxCall', data, function (data) {
                if (!data.error) {
                    $("#tpg-preview-container").html(data.data);
                    preFunction();
                    IsotopeNCarouselRender();
                    loader.find('.rt-loading-overlay, .rt-loading').remove();
                    loader.removeClass('tpg-pre-loader');
                }
                $(".rt-response").removeClass('loading');
                $(".rt-response").html('');
            });
        }
    }


    function tpgPreviewAjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(rttpg.nonceID);
        if (n < 0) {
            data = data + "&" + rttpg.nonceID + "=" + rttpg.nonce;
        }
        $.ajax({
            type: "post",
            url: wls.ajaxurl,
            data: data,
            beforeSend: function () {
                $("<span class='rt-loading'></span>").insertAfter(element);
            },
            success: function (data) {
                $(".rt-loading").remove();
                handle(data);
            }
        });
    }


    function overlayIconResizeTpg() {
        jQuery('.overlay').each(function () {
            var holder_height = jQuery(this).height();
            var target = jQuery(this).children('.link-holder');
            var targetd = jQuery(this).children('.view-details');
            var a_height = target.height();
            var ad_height = targetd.height();
            var h = (holder_height - a_height) / 2;
            var hd = (holder_height - ad_height) / 2;
            target.css('top', h + 'px');
            targetd.css('margin-top', hd + 'px');
        });
    }

    if ($(".rt-row.rt-content-loader.layout4").length) {
        equalHeight4Layout4();
    }

    function equalHeight4Layout4() {
        var $maxH = $(".rt-row.layout4 .layout4item").height();
        $(".rt-row.layout4 .layout4item .layoutInner .rt-img-holder img,.rt-row.layout4 .layout4item .layoutInner.layoutInner-content").height($maxH + "px");
    }

    function windowHashChange(isotope, IsoButton) {
        var $hashFilter = decodeHash() || '';
        if (!$hashFilter) {
            $hashFilter = IsoButton.find('button.selected').attr('data-filter') || '';
            $hashFilter = $hashFilter ? $hashFilter : '*';
        }
        $hashFilter = $hashFilter || '*';
        isotope.isotope({
            filter: $hashFilter
        });
        IsoButton.find("button").removeClass("selected");
        IsoButton.find('button[data-filter="' + $hashFilter + '"]').addClass("selected");
    }

    function isoFilterCounter(container, isotope) {
        var total = 0;
        container.find('.rt-tpg-isotope-buttons button').each(function () {
            var self = $(this),
                filter = self.attr("data-filter"),
                itemTotal = isotope.find(filter).length;
            if (filter != "*") {
                self.find('span').remove();
                self.append("<span> (" + itemTotal + ") </span>");
                total = total + itemTotal;
            }
        });
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').find('span').remove();
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').append("<span> (" + total + ") </span>");
    }

    function decodeHash() {
        var $matches = location.hash.match(/filter=([^&]+)/i);
        var $hashFilter = $matches && $matches[1];
        return $hashFilter && decodeURIComponent($hashFilter);
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
        $.ajax({
            type: "post",
            url: rttpg.ajaxurl,
            data: data,
            beforeSend: function () {
                if (element) {
                    $("<span class='rt-loading'></span>").insertAfter(element);
                }
            },
            success: function (data) {
                if (element) {
                    element.next(".rt-loading").remove();
                }
                handle(data);
            }
        });
    }
})(jQuery);