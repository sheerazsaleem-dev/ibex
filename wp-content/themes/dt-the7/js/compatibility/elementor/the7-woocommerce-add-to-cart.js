(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-product-add-to-cart-v2.default", function ($widget, $) {
            $(function () {
                $widget.productVariations();
            })
        });
    });

    $.productVariations = function (el) {
        const $widget = $(el);
        const $variationList = $widget.find(".the7-vr-options");
        const $form = $widget.find("form");
        const $singleVariation = $form.find(".single_variation");

        // Store a reference to the object.
        $.data(el, "productVariations", $widget);

        // Private methods.
        const methods = {
            init: function () {
                if ($variationList.length) {
                    $("li a", $variationList).on("click", function (e) {
                        e.preventDefault();

                        const $this = $(this);
                        const $parent = $this.parent();
                        const $currentVariation = $this.closest("ul");
                        const atr = $.escapeSelector($currentVariation.attr("data-atr"));
                        const $select = $currentVariation.siblings("select#" + atr);

                        if ($parent.hasClass("active")) {
                            $parent.removeClass("active");
                            $select.val("").trigger("change");
                        } else {
                            const id = $this.attr("data-id");

                            // Set variation active.
                            $parent.siblings().removeClass("active");
                            $parent.addClass("active");

                            $select.val(id).trigger("change");
                        }
                    });

                    $form.find(".variations select").each(function () {
                        const $this = $(this);
                        const val = $this.val();

                        if (val.length) {
                            const atr = $.escapeSelector($this.attr("id"));
                            $variationList.filter("[data-atr='" + atr + "']").find("li a[data-id='" + val + "']").trigger("click");
                        }
                    });
                }

                const applyPriceBottomMargin = function () {
                    $singleVariation.children().not(":empty").last().addClass("last");
                }

                $widget.find(".single_variation_wrap").on("show_variation", function (event, variation) {
                    applyPriceBottomMargin();
                });

                applyPriceBottomMargin();
            }
        };

        methods.init();
    };

    $.fn.productVariations = function () {
        return this.each(function () {
            if ($(this).data("productVariations") !== undefined) {
                $(this).removeData("productVariations")
            }
            new $.productVariations(this);
        });
    };
})(jQuery);
