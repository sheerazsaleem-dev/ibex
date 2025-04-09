jQuery(function ($) {
    // Class to handle products variations.
    class ProductVariations {

        /**
         * Construct object with some data.
         *
         * @param data
         */
        constructor(data) {
            this.data = data || {};
            this.data.outOfStockAttributes = this.findOutOfStockAttributes();
            this.data.state = this.data.state || {};
            this.data.$originPrice = this.data.$price.clone();
        }

        /**
         * Return current state or part of it.
         *
         * @param key
         * @returns {*}
         */
        getState(key) {
            return key ? this.data.state[key] : Object.assign({}, this.data.state);
        }

        /**
         * Apply visual effects.
         */
        render() {
            const attributes = this.getState();

            if (!attributes) {
                return;
            }

            const $attributeWrap = this.data.$variations.find("li");
            const attributesSelector = Object.keys(attributes).filter(function (val) {
                return !!val;
            }).map(function (key) {
                return 'a.' + $.escapeSelector(key + '_' + attributes[key]);
            }).join(',');


            // Set active attributes.
            $attributeWrap.removeClass("active");
            $attributeWrap.find(attributesSelector).each(function () {
                $(this).parent().addClass("active");
            });

            // Dynamic out-of-stock system.
            this.handleOutOfStockVisibility(this.findMatchingOutOfStockVariations());


            const foundVariation = this.findMatchingVariation();

            if (foundVariation) {
                // Enable button.
                this.data.$addToCartButton.attr("data-product_id", foundVariation.variation_id);
                this.data.$addToCartButton.removeClass("variation-btn-disabled");

                // Update price.
                if (foundVariation.price_html) {
                    this.updateProductPriceData($(foundVariation.price_html));
                }
            } else {
                // Disable button.
                this.data.$addToCartButton.attr("data-product_id", "");
                this.data.$addToCartButton.addClass("variation-btn-disabled");
                // Update price.
                this.updateProductPriceData(this.data.$originPrice.clone());
            }

        }

        /**
         * Update state and render.
         *
         * @param state
         */
        updateState(state) {
            // Create a new object to avoid mutating the original state.
            this.data.state = Object.assign({}, this.data.state, state);
            this.render();
        }

        /**
         * Find matching variation for attributes.
         */
        findMatchingVariation() {
            const variations = this.data.variations;
            const attributes = this.getState();
            var matching = [];
            for (var i = 0; i < variations.length; i++) {
                var variation = variations[i];

                if (this.isExactMatch(variation.attributes, attributes)) {
                    matching.push(variation);
                }
            }

            return matching.shift();
        }

        /**
         * Update product price data.
         *
         * @param productData
         * @param $newPrice
         */
        updateProductPriceData($newPrice) {
            this.data.$price.replaceWith($newPrice);
            this.data.$price = $newPrice;
        }

        /**
         * Mark attributes as out-of-stock.
         *
         * @param outOfStockAttributes
         */
        handleOutOfStockVisibility(outOfStockAttributes) {
            const attributes = outOfStockAttributes || this.data.outOfStockAttributes;
            const $variationNodes = this.data.$variations.find("li a");
            $variationNodes.removeClass("out-of-stock");
            for (const [attr, values] of Object.entries(attributes)) {
                if (values.length) {
                    const selector = values.slice().map((val) => {
                        return "." + $.escapeSelector(attr + "_" + val);
                    }).join(",");
                    $variationNodes.filter(selector).addClass("out-of-stock");

                }
            }
        }

        /**
         * Find matching variations for attributes.
         */
        findMatchingOutOfStockVariations() {
            const self = this;
            const variations = this.data.variations;
            const attributes = this.getState();

            let matching = [];
            for (var i = 0; i < variations.length; i++) {
                const variation = variations[i];

                if (!variation.is_in_stock && this.isMatch(variation.attributes, attributes)) {
                    matching.push(variation);
                }
            }
            let outOfStockAttributes = Object.assign({}, this.data.outOfStockAttributes);

            matching.forEach(function (variation) {
                for (const [key, value] of Object.entries(variation.attributes)) {
                    // Ignore active variations.

                    if (self.getState(key) !== value) {

                        outOfStockAttributes[key] = outOfStockAttributes[key].slice() || [];
                        // Keep attributes unique.
                        if (outOfStockAttributes[key].indexOf(value) === -1) {
                            outOfStockAttributes[key].push(value);

                        }
                    }
                }
            });
            return outOfStockAttributes;
        }

        /**
         * Find out-of-stock attributes.
         *
         * @returns {{}}
         */
        findOutOfStockAttributes() {
            const variations = this.data.variations;
            let inStockAttributes = [];
            let outOfStockAttributes = [];
            let matching = {};

            for (let i = 0; i < variations.length; i++) {
                let variation = variations[i];

                if (variation.is_in_stock) {
                    inStockAttributes.push(Object.entries(variation.attributes));
                } else {
                    outOfStockAttributes.push(Object.entries(variation.attributes));
                }
            }

            inStockAttributes = [].concat(...inStockAttributes);
            outOfStockAttributes = [].concat(...outOfStockAttributes);

            const attrReducer = function (acc, entry) {
                const attr = entry[0];
                const val = entry[1];

                acc[attr] = acc[attr] || [];
                acc[attr].push(val);

                return acc;
            };

            const inStockAttributesObj = inStockAttributes.reduce(attrReducer, {});
            const outOfStockAttributesObj = outOfStockAttributes.reduce(attrReducer, {});

            for (const [key, value] of Object.entries(outOfStockAttributesObj)) {
                if (inStockAttributesObj[key]) {
                    matching[key] = value.filter(function (v) {
                        return !inStockAttributesObj[key].includes(v);
                    });
                }
            }

            return matching;
        }

        /**
         * See if attributes match.
         *
         * @return {Boolean}
         */
        isMatch(variation_attributes, attributes) {
            var match = false;
            for (var attr_name in variation_attributes) {
                if (variation_attributes.hasOwnProperty(attr_name)) {
                    var val1 = variation_attributes[attr_name];
                    var val2 = attributes[attr_name];
                    if (val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 === val2) {
                        match = true;
                    }
                }
            }
            return match;
        }

        /**
         * See if attributes match.
         *
         * @return {Boolean}
         */
        isExactMatch(variation_attributes, attributes) {
            var match = true;
            for (var attr_name in variation_attributes) {
                if (variation_attributes.hasOwnProperty(attr_name)) {
                    var val1 = variation_attributes[attr_name];
                    var val2 = attributes[attr_name];
                    if (val1 === undefined || val2 === undefined || val2.length === 0 || (val1.length !== 0 && val1 !== val2)) {
                        match = false;
                    }
                }
            }
            return match;
        }
    }

    $.productsVariations = function (el) {
        const $widget = $(el);
        const widgetSettings = new The7ElementorSettings($widget);
        let methods = {};
        let products = {};

        // Store a reference to the object
        $.data(el, "productsVariations", $widget);

        // Private methods
        methods = {
            init: function () {
                // Apply product quantity. Only for loop add-to-cart widget.
                const $quantity = $widget.find('.quantity');
                if ($quantity.length) {
                    const $addToCartButton = $widget.find(".box-button");
                    const $qty = $('.qty', $quantity);
                    $qty.on("change", function () {
                        $addToCartButton.attr("data-quantity", parseInt($qty.val()));
                    });
                }

                const variationsData = $widget.find("[data-product_variations]");
                if (variationsData.length) {
                    variationsData.each(function () {
                        const $this = $(this);
                        const $variationList = $this.find(".products-variations");
                        const $addToCartButton = $this.find(".box-button");

                        let $priceHtml;
                        if ($widget.data("widget_type") === 'the7-woocommerce-loop-add-to-cart.default') {
                            // Find price in the same loop item.
                            $priceHtml = $this.closest('div[data-elementor-type="loop-item"]').find(".price").first();
                        } else {
                            $priceHtml = $this.find("span.price").first();
                        }

                        const productVariations = new ProductVariations({
                            variations: $this.data("product_variations"),
                            $addToCartButton: $addToCartButton,
                            $variations: $variationList,
                            $price: $priceHtml,
                            state: $this.data("default_attributes") || {},
                        });

                        // Link product data with each attribute element.
                        $variationList.find("li a").data("the7_product_variations_obj", productVariations);

                        // Disable ajax handlers.
                        $addToCartButton.attr("data-product_id", "");
                        $addToCartButton.on("click", function (e) {
                            const $this = $(this);

                            /**
                             * Open product page if variations are hidden for current device.
                             * Ensure that add-to-cart button behave the same way it does when variations are disabled altogether.
                             */
                            const show_variations = widgetSettings.getCurrentDeviceSetting("show_variations");
                            if (show_variations == "n") {
                                e.preventDefault();
                                window.location.assign($this.attr("href"));
                                return;
                            }

                            if ($this.is(".variation-btn-disabled")) {
                                e.preventDefault();
                            }
                        });

                        // Apply variations visuals.
                        productVariations.render();
                    });

                    const $attr = $widget.find(".products-variations li a");
                    if ($(".touchevents").length) {
                        let origY, origX;

                        $attr.on("touchstart", function (e) {
                            origY = e.originalEvent.touches[0].pageY;
                            origX = e.originalEvent.touches[0].pageX;
                        });
                        $attr.on("touchend", function (e) {
                            e.preventDefault();

                            let touchEX = e.originalEvent.changedTouches[0].pageX;
                            let touchEY = e.originalEvent.changedTouches[0].pageY;

                            if (origY === touchEY || origX === touchEX) {
                                const $this = $(this);
                                methods.selectAttribute($this);
                            }
                        })
                    } else {
                        $attr.on("click", function (e) {
                            e.preventDefault();
                            const $this = $(this);
                            methods.selectAttribute($this);
                        });
                    }
                }

                const isMobile = !!$("html.mobile-true").length;

                if (isMobile) {
                    // Usually it is article or .woo-buttons-on-img.
                    const $imgHoverElements = $widget.find(".trigger-img-hover");

                    $imgHoverElements.each(function () {
                        const $imgHoverElement = $(this);
                        let origY, origX;

                        if ($imgHoverElement.hasClass("woo-ready")) {
                            return;
                        }

                        const isVariableProduct = !!$imgHoverElement.closest(".product-type-variable", $widget).length;

                        $imgHoverElement.on("touchstart", function (e) {
                            origY = e.originalEvent.touches[0].pageY;
                            origX = e.originalEvent.touches[0].pageX;
                        });
                        $imgHoverElement.on("touchend", function (e) {
                            const $this = $(this);
                            const touchEX = e.originalEvent.changedTouches[0].pageX;
                            const touchEY = e.originalEvent.changedTouches[0].pageY;
                            const isNotClicked = !$this.hasClass("is-clicked");
                            let preventDefault = isNotClicked;

                            if (isNotClicked) {
                                let isImageTouch;

                                if ($this.hasClass("woo-buttons-on-img")) {
                                    isImageTouch = true
                                } else {
                                    const imgWrap = $this.find(".woo-buttons-on-img").get(0);
                                    isImageTouch = e.originalEvent && imgWrap && e.originalEvent.composedPath().includes(imgWrap);
                                }

                                // If target is/on image.
                                if (isImageTouch && $(e.target).closest(".products-variations-wrap, .woo-list-buttons", $this).length) {
                                    const show_variations_on_hover = widgetSettings.getCurrentDeviceSetting("show_variations_on_hover");
                                    const show_btn_on_hover = widgetSettings.getCurrentDeviceSetting("show_btn_on_hover");
                                    const isVariationsVisible = (!isVariableProduct || (show_variations_on_hover && show_variations_on_hover !== "on-hover"));
                                    const isAddToCartVisible = (show_btn_on_hover && show_btn_on_hover !== "on-hover");

                                    // Variations and Add-to-cart button are visible on image, don't preventDefault.
                                    if (isVariationsVisible && isAddToCartVisible) {
                                        preventDefault = false;
                                    }
                                }

                                if (origY == touchEY || origX == touchEX) {
                                    // Remove all "is-clicked" classes in widget.
                                    $imgHoverElements.removeClass("is-clicked");
                                    $this.addClass("is-clicked");

                                    const eventData = {
                                        boundaryElement: $this.get(0),
                                        $affectedElements: $imgHoverElements
                                    };

                                    elementorFrontend.elements.$body
                                        .off("touchstart", methods.onOuterTouchHandler)
                                        .on("touchstart", eventData, methods.onOuterTouchHandler);
                                }
                            }

                            if (preventDefault) {
                                e.preventDefault();
                            }
                        });

                        $imgHoverElement.addClass("woo-ready");
                    });
                }
            },

            /**
             * Select variation.
             */
            selectAttribute: function ($el) {
                if ($el.is(".out-of-stock") || widgetSettings.getCurrentDeviceSetting("show_add_to_cart") === "n") {
                    return;
                }

                const atr = $el.closest("ul").attr("data-atr");
                const variations = $el.data("the7_product_variations_obj");
                variations.updateState({
                    [atr]: $el.parent().hasClass("active") ? "" : String($el.data("id"))
                });
            },

            onOuterTouchHandler: function (event) {
                /**
                 * Close dropdown if event path not contains menu wrap object.
                 *
                 * @see https://developer.mozilla.org/en-US/docs/Web/API/Event/composedPath
                 */
                if (event.originalEvent && event.data && event.data.boundaryElement && !event.originalEvent.composedPath().includes(event.data.boundaryElement)) {
                    event.data.$affectedElements && event.data.$affectedElements.removeClass("is-clicked") && elementorFrontend.elements.$body.off("touchstart", methods.onOuterTouchHandler);
                }
            }
        };

        methods.init();
    };

    $.fn.productsVariations = function () {
        return this.each(function () {
            if ($(this).data("productsVariations") !== undefined) {
                $(this).removeData("productsVariations")
            }
            new $.productsVariations(this);
        });
    };
});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {

        const initVariations = function ($widget, $) {
            $(function () {
                $widget.productsVariations();
            })
        };
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-wc-products.default", initVariations);
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-wc-products-carousel.default", initVariations);
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-loop-add-to-cart.default", initVariations);
    });
})(jQuery);
