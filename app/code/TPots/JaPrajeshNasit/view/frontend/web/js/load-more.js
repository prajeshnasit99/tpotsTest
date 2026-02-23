/**
 * TPots_JaPrajeshNasit – Load More PLP module
 * RequireJS widget: handles AJAX loading and DOM appending of next product page.
 */
define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        var $wrapper     = $(element);
        var $btn         = $wrapper.find('#japrajesh-load-more-btn');
        var ajaxUrl      = $wrapper.data('ajax-url');
        var catId        = $wrapper.data('cat-id');
        var currentPage  = parseInt($wrapper.data('current-page'), 10);
        var totalPages   = parseInt($wrapper.data('total-pages'), 10);
        var pageLimit    = parseInt($wrapper.data('page-limit'), 10);

        // Resolve extra URL params from current page URL (filters, sort, etc.)
        function getCurrentUrlParams() {
            var urlParams = {};
            window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, val) {
                urlParams[decodeURIComponent(key)] = decodeURIComponent(val.replace(/\+/g, ' '));
            });
            // Remove the default page param so we control it
            delete urlParams['p'];
            return urlParams;
        }

        // Locate the product list container
        function getProductList() {
            // Luma uses: .products-grid ol.products.list or .products-list ol.products.list
            return $('ol.products.list.items.product-items').first();
        }

        function setLoading(state) {
            if (state) {
                $btn.addClass('is-loading').prop('disabled', true);
            } else {
                $btn.removeClass('is-loading').prop('disabled', false);
            }
        }

        function loadNextPage() {
            var nextPage   = currentPage + 1;
            var extraParams = getCurrentUrlParams();
            var params      = $.extend({}, extraParams, {
                p:      nextPage,
                limit:  pageLimit
            });

            // Use catId from wrapper if available, fallback to DOM lookup
            var resolvedCatId = catId || $('[data-role="category-id"]').val() || $('input[name="cat"]').val() || '';

            if (resolvedCatId) {
                params.cat_id = resolvedCatId;
            }

            setLoading(true);

            $.ajax({
                url:      ajaxUrl,
                type:     'GET',
                data:     params,
                dataType: 'json'
            }).done(function (response) {
                if (response && response.html) {
                    var $list = getProductList();
                    if ($list.length) {
                        $list.append(response.html);

                        // Trigger Magento's product-image lazyload / swatches re-init if present
                        $list.trigger('contentUpdated');
                    }

                    currentPage = nextPage;
                    $wrapper.data('current-page', currentPage);

                    if (!response.hasMore) {
                        $btn.closest('.japrajesh-load-more-btn-wrapper').hide();
                    }
                } else if (response && response.error) {
                    console.error('[JaPrajeshNasit LoadMore] ' + response.error);
                }
            }).fail(function (xhr) {
                console.error('[JaPrajeshNasit LoadMore] AJAX error', xhr.status, xhr.statusText);
            }).always(function () {
                setLoading(false);
            });
        }

        // Bind click handler
        if ($btn.length) {
            $btn.on('click', function () {
                loadNextPage();
            });
        }
    };
});
