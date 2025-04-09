jQuery(document).ready(function ($) {
    $.the7Slider = function (el) {
        const data = {
            selectors: {
                slider: '.elementor-slides-wrapper',
                slide: 'the7-swiper-slide',
                slideInnerContents: '.the7-slide-content',
                activeSlide: '.swiper-slide-active',
                activeDuplicate: '.swiper-slide-duplicate-active'
            },
            classes: {
                inPlaceTemplateEditable: "elementor-in-place-template-editable"
            },
            attributes: {
                dataAnimation: 'animation'
            },
            changeableProperties: {
                pause_on_hover: 'pauseOnHover',
                autoplay_speed: 'delay',
                transition_speed: 'speed',
                autoplay: 'autoplay'
            }
        };

        let $widget = $(el),
            elementorSettings,
            elementorAnimation,
            settings,
            methods,
            swiper,
            intersectionObserver,
            widgetType,
            elements = {
                $swiperContainer: $widget.find(data.selectors.slider).first(),
                animatedSlides: {},
                activeElements: []
            };
        elements.$slides = elements.$swiperContainer.find('> .the7-elementor-slides >' + '.' + data.selectors.slide);
        
        $widget.vars = {
            sliderInitialized: false,
            isInlineEditing: false
        };
        // Store a reference to the object
        $.data(el, "the7Slider", $widget);
        // Private methods
        methods = {
            init: function () {
                elementorSettings = new The7ElementorSettings($widget);
                widgetType = elementorSettings.getWidgetType();
                settings = elementorSettings.getSettings();

                elementorAnimation = new The7ElementorAnimation();

                if (elementorFrontend.isEditMode()) {
                    methods.handleCTA();
                }

                this.initSlider();
                $widget.refresh();
                methods.handleResize = elementorFrontend.debounce(methods.handleResize, 1000);
            },

            runElementHandlers: function (elements) {
                [...elements].flatMap(el => [...el.querySelectorAll('.elementor-element')]).forEach(el => elementorFrontend.elementsHandler.runReadyTrigger(el));
            },

            handleElementHandlers: function () {
                if (!swiper) {
                    return;
                }
                const duplicatedSlides = Array.from(swiper.slides).filter(slide => slide.classList.contains(swiper.params.slideDuplicateClass));
                methods.runElementHandlers(duplicatedSlides);
            },

        handleCTA: function () {
                if (typeof elementorPro === 'undefined') {
                    return;
                }
                const emptyViewContainer = document.querySelector(`[data-id="${elementorSettings.getID()}"] .e-loop-empty-view__wrapper`);
                const emptyViewContainerOld = document.querySelector(`[data-id="${elementorSettings.getID()}"] .e-loop-empty-view__wrapper_old`);

                if (emptyViewContainerOld) {
                    $widget.css('opacity', 1);
                    return;
                }

                if (!emptyViewContainer) {
                    return;
                }

                const shadowRoot = emptyViewContainer.attachShadow({
                    mode: 'open'
                });
                shadowRoot.appendChild(elementorPro.modules.loopBuilder.getCtaStyles());
                shadowRoot.appendChild(elementorPro.modules.loopBuilder.getCtaContent(widgetType));
                if (methods.isLoop()) {
                    const ctaButton = shadowRoot.querySelector('.e-loop-empty-view__box-cta');
                    ctaButton.addEventListener('click', () => {
                        elementorPro.modules.loopBuilder.createTemplate();
                        methods.handleSlider();
                    });
                }
                $widget.css('opacity', 1);
            },
            bindEvents: function () {
                methods.initIntersectionObserver();
                elementorFrontend.elements.$window.on('the7-resize-width', methods.handleResize);
            },
            unBindEvents: function () {
                elementorFrontend.elements.$window.off('the7-resize-width', methods.handleResize);
                if (intersectionObserver !== undefined) {
                    intersectionObserver.unobserve($widget[0]);
                    intersectionObserver = undefined;
                }
            },
            handleSlider: function () {
                if (!$widget.vars.sliderInitialized) return;
                $widget.vars.isInlineEditing = true;
                $widget.addClass(data.classes.inPlaceTemplateEditable);
                swiper.slideTo(0);
                swiper.autoplay.stop();
                swiper.pagination.destroy();
                swiper.navigation.destroy();
                swiper.allowTouchMove = false;
                swiper.params.autoplay.disableOnInteraction = true;
                swiper.params.autoplay.pauseOnMouseEnter = false;
                swiper.params.autoplay.delay = 1000000; // Add a long delay so that the Swiper does not move while editing the Template. Even though it was paused, it will start again on mouse leave.
                swiper.update();
            },

            handleResize: function () {
                if (!$widget.vars.sliderInitialized) return;
                methods.removeElementsAnimation(true);
                methods.findAnimationInElements();
                methods.updateActiveElements();
                methods.removeElementsAnimation();
                methods.addElementsAnimation();
            },
            getLoopedSlides: function (slidesPerGroup) {
                let slides = methods.getSlidesCount();
                if (settings['slides_to_scroll'] !== 'all'){
                    return slides;
                }
                return Math.trunc(slides / slidesPerGroup) * slidesPerGroup;
            },
            getSlidesCount: function () {
                return elements.$slides.length;
            },
            initIntersectionObserver: function () {
                if ('yes' !== settings.autoplay) return;
                intersectionObserver = elementorModules.utils.Scroll.scrollObserver({
                    offset: '-15% 0% -15%',
                    callback: event => {
                        if (event.isInViewport) {
                            methods.swiperAutoplayStart();
                        } else {
                            methods.swiperAutoplayStop();
                        }
                    }
                });
                intersectionObserver.observe($widget[0]);
            },
            swiperAutoplayStop() {
                if ($widget.vars.sliderInitialized && !$widget.vars.isInlineEditing) {
                    swiper.autoplay.stop();
                }
            },
            swiperAutoplayStart() {
                if ($widget.vars.sliderInitialized && !$widget.vars.isInlineEditing) {
                    swiper.autoplay.start();
                }
            },
            getEffect() {
                return settings['transition'];
            },
            getSlidesPerView: function (device) {
                if ('slide' === methods.getEffect()) {
                    let slides_per_view = The7ElementorSettings.getResponsiveControlValue(settings, 'slides_per_view', 'size', device)
                    return +slides_per_view || 1;
                    //return Math.min(methods.getSlidesCount(), +slides_per_view || 1);
                }
                return 1;
            },
            isEnoughtElements: function (slidesPerView) {
                return slidesPerView < methods.getSlidesCount();
            },
            getSlidesToScroll: function (slidesPerView) {
                let slidesToScroll = 1;
                if ('slide' === methods.getEffect()) {
                    if (settings['slides_to_scroll'] === 'all') {
                        slidesToScroll = slidesPerView;
                    }
                }
                return slidesToScroll;
            },
            getAutoHeight: function () {
                let autoHeight = true;
                if (methods.isLoop()) {
                    autoHeight = false;
                }
                return autoHeight;
            },
            getSwiperOptions: function () {
                let slidesPerView = methods.getSlidesPerView('desktop');

                swiperOptions = {
                    autoplay: this.getAutoplayConfig(),
                    grabCursor: true,
                    initialSlide: this.getInitialSlide(),
                    slidesPerView: slidesPerView,
                    slidesPerGroup: methods.getSlidesToScroll(slidesPerView),
                    loop: methods.isEnableLoop(slidesPerView),
                    loopPreventsSlide:true,
                    pauseOnMouseEnter: true,
                    speed: settings.transition_speed,
                    effect: methods.getEffect(),
                    observeParents: true,
                    observer: true,
                    handleElementorBreakpoints: false,
                    slideClass: data.selectors.slide
                };
                swiperOptions.autoHeight = methods.getAutoHeight();
                const navigation = true,
                    pagination = true;
                if (navigation) {
                    swiperOptions.navigation = {
                        prevEl: elements.$swiperContainer.children('.the7-swiper-button-prev')[0],
                        nextEl: elements.$swiperContainer.children('.the7-swiper-button-next')[0]
                    };
                }
                if (pagination) {
                    swiperOptions.pagination = {
                        el: elements.$swiperContainer.children('.swiper-pagination')[0],
                        type: 'bullets',
                        bulletActiveClass: 'active',
                        bulletClass: 'owl-dot',
                        clickable: true,
                        renderBullet: function (index, className) {
                            return '<button role="button" class="' + className + '" aria-label="Go to slide ' + index + 1 + '"><span></span></button>';
                        },
                    };
                }
                //if (true === swiperOptions.loop) {
                swiperOptions.loopedSlides = methods.getLoopedSlides(swiperOptions.slidesPerGroup);
                //}
                if ('fade' === swiperOptions.effect) {
                    swiperOptions.fadeEffect = {
                        crossFade: true
                    };
                }

                if (settings.slides_gap) {
                    swiperOptions.spaceBetween = this.getSpaceBetween();
                }

                breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
                swiperOptions.breakpoints = {};
                Object.keys(breakpoints).forEach(breakpointName => {
                    let breakPointVal = breakpoints[breakpointName].value;
                    swiperOptions.breakpoints[breakPointVal] = {};
                    let slidesPerView = methods.getSlidesPerView(breakpointName);
                    if (slidesPerView) {
                        swiperOptions.breakpoints[breakPointVal].slidesPerView = slidesPerView;
                        swiperOptions.breakpoints[breakPointVal].slidesPerGroup = methods.getSlidesToScroll(slidesPerView);
                        swiperOptions.breakpoints[breakPointVal].pagination = {};
                    }
                    if (settings.slides_gap) {
                        swiperOptions.breakpoints[breakPointVal]['spaceBetween'] = methods.getSpaceBetween(breakpointName);
                    }
                });

                let switchPointsWide = dtLocal.elementor.settings.container_width;
                let wideBreakpoint = settings['widget_columns_wide_desktop_breakpoint'];
                if (wideBreakpoint) {
                    switchPointsWide = wideBreakpoint;
                }
                let wideColumns = settings['wide_desk_columns'];
                if (wideColumns) {
                    swiperOptions.breakpoints[switchPointsWide] = {
                        slidesPerView: parseInt(wideColumns),
                        slidesPerGroup: methods.getSlidesToScroll(parseInt(wideColumns))
                    }
                }
                swiperOptions = methods.adjustBreakpointsConfig(swiperOptions);
                return swiperOptions;
            },

            // Backwards compatibility for Elementor Pro <2.9.0 (old Swiper version - <5.0.0)
            // In Swiper 5.0.0 and up, breakpoints changed from acting as max-width to acting as min-width
            adjustBreakpointsConfig(config) {
                const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
                    elementorBreakpointValues = elementorFrontend.breakpoints.getBreakpointValues();
                Object.keys(config.breakpoints).forEach(configBPKey => {
                    const configBPKeyInt = parseInt(configBPKey);
                    let breakpointToUpdate;

                    // The `configBPKeyInt + 1` is a BC Fix for Elementor Pro Carousels from 2.8.0-2.8.3 used with Elementor >= 2.9.0
                    if (configBPKeyInt === elementorBreakpoints.mobile.value || configBPKeyInt + 1 === elementorBreakpoints.mobile.value) {
                        // This handles the mobile breakpoint. Elementor's default sm breakpoint is never actually used,
                        // so the mobile breakpoint (md) needs to be handled separately and set to the 0 breakpoint (xs)
                        breakpointToUpdate = 0;
                    } else if (elementorBreakpoints.widescreen && (configBPKeyInt === elementorBreakpoints.widescreen.value || configBPKeyInt + 1 === elementorBreakpoints.widescreen.value)) {
                        // Widescreen is a min-width breakpoint. Since in Swiper >5.0 the breakpoint system is min-width based,
                        // the value we pass to the Swiper instance in this case is the breakpoint from the user, unchanged.
                        breakpointToUpdate = configBPKeyInt;
                    } else {
                        // Find the index of the current config breakpoint in the Elementor Breakpoints array
                        const currentBPIndexInElementorBPs = elementorBreakpointValues.findIndex(elementorBP => {
                            // BC Fix for Elementor Pro Carousels from 2.8.0-2.8.3 used with Elementor >= 2.9.0
                            return configBPKeyInt === elementorBP || configBPKeyInt + 1 === elementorBP;
                        });

                        if (currentBPIndexInElementorBPs === -1) {
                            return;
                        }

                        // For all other Swiper config breakpoints, move them one breakpoint down on the breakpoint list,
                        // according to the array of Elementor's global breakpoints
                        breakpointToUpdate = elementorBreakpointValues[currentBPIndexInElementorBPs - 1];
                    }
                    config.breakpoints[breakpointToUpdate] = config.breakpoints[configBPKey];

                    // Then reset the settings in the original breakpoint key to the default values
                    config.breakpoints[configBPKey] = {
                        slidesPerView: config.slidesPerView,
                        slidesPerGroup: config.slidesPerGroup ? config.slidesPerGroup : 1
                    };

                    if ("spaceBetween" in config) {
                        config.breakpoints[configBPKey].spaceBetween = config.spaceBetween;
                    }
                });
                return config;
            },
            getSpaceBetween() {
                let device = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
                return The7ElementorSettings.getResponsiveControlValue(settings, 'slides_gap', 'size', device) || 0;
            },
            getAutoplayConfig: function () {
                if ('yes' !== settings.autoplay) {
                    return false;
                }

                return {
                    stopOnLastSlide: true,
                    // Has no effect in infinite mode by default.
                    delay: settings.autoplay_speed,
                    disableOnInteraction: true
                };
            },

            handlePauseOnHover: function () {
                if (!$widget.vars.sliderInitialized) return;

                let toggleOn = false;
                if ('yes' === settings.pause_on_hover) {
                    toggleOn = true;
                }

                if ('yes' !== settings.autoplay) {
                    toggleOn = false;
                }
                if (toggleOn) {
                    elements.$swiperContainer.on({
                        mouseenter: () => {
                            methods.swiperAutoplayStop();
                        },
                        mouseleave: () => {
                            methods.swiperAutoplayStart();
                        }
                    });
                } else {
                    elements.$swiperContainer.off('mouseenter mouseleave');
                }
            },

            getInitialSlide() {
                return 0;
            },

            initSlider: async function () {
                const $slider = elements.$swiperContainer;
                if (!$slider.length) return;
                const Swiper = elementorFrontend.utils.swiper;
                swiper = await new Swiper($slider, this.getSwiperOptions()); // Expose the swiper instance in the frontend
                $widget.vars.sliderInitialized = true;
                swiper.navigation.enabled = true;
                if (methods.isEnoughtElements(swiper.params.slidesPerView)) {
                    swiper.navigation.enabled = false;
                }
                methods.updateBreakpoint();
                methods.loopLazyFix();
                methods.updateNav();
                methods.findAnimationInElements();
                $widget.css('opacity', 1);
                methods.updateActiveElements();
                methods.removeElementsAnimation();
                setTimeout(() => {
                    methods.updateActiveElements();
                    methods.removeElementsAnimation(true);
                    methods.addElementsAnimation();
                }, 300);
                methods.handlePauseOnHover();
                swiper.on('slideChangeTransitionStart', function () {
                });
                swiper.on('slideChangeTransitionEnd', function () {
                    methods.updateActiveElements();
                    methods.removeElementsAnimation();
                    methods.addElementsAnimation();
                });
                swiper.on('snapGridLengthChange', methods.updateNav);
                swiper.on('breakpoint', methods.updateBreakpoint);
                methods.handleElementHandlers();
                $widget.find('.dt-owl-carousel-call, .elementor-owl-carousel-call, .related-projects, .slider-simple:not(.slider-masonry)').trigger('refresh.owl.carousel');
            },
            loopLazyFix: function () {
                if (swiper.params.loop){
                let $swiperDuplicates = $(swiper.wrapperEl).children("." + (swiper.params.slideDuplicateClass));
                    $swiperDuplicates.find(".is-loading").removeClass("is-loading");
                    $swiperDuplicates.layzrInitialisation();
                }
            },
            updateNav: function () {
                if (methods.isEnoughtElements(swiper.params.slidesPerView)) {
                    if (!swiper.navigation.enabled) {
                        swiper.navigation.destroy();
                        swiper.navigation.init();
                        swiper.navigation.update();

                        swiper.pagination.destroy();
                        swiper.pagination.init();
                        swiper.pagination.render();
                        swiper.pagination.update();
                        swiper.navigation.enabled = true;
                    }
                } else if (swiper.navigation.enabled) {
                    swiper.navigation.destroy();
                    if ( swiper.navigation.$nextEl){
                        swiper.navigation.$nextEl.addClass(swiper.params.navigation.disabledClass);
                    }
                    if ( swiper.navigation.$prevEl) {
                        swiper.navigation.$prevEl.addClass(swiper.params.navigation.disabledClass);
                    }

                    swiper.pagination.destroy();
                    if(swiper.pagination.$el) {
                        swiper.pagination.$el.addClass(swiper.params.pagination.hiddenClass);
                    }

                    swiper.navigation.enabled = false;
                }
            },
            updateBreakpoint: function () {
                methods.updateScrollSpeed();
                let updateLoop = false;
                let oldLoop = swiper.params.loop;
                swiper.params.loop = methods.isEnableLoop(swiper.params.slidesPerView)

                if (true === swiper.params.loop) {
                    let oldLoopedSlides = swiper.params.loopedSlides;
                    swiper.params.loopedSlides = methods.getLoopedSlides(swiper.params.slidesPerGroup);
                    if (swiper.params.loopedSlides != oldLoopedSlides) {
                        updateLoop = true;
                    }
                }
                if (swiper.params.loop !== oldLoop && swiper.params.loop){
                    updateLoop = true;
                }
                if (updateLoop) {
                    swiper.loopDestroy();
                    swiper.loopCreate();
                    swiper.updateSlides();
                    methods.loopLazyFix();
                    methods.handleElementHandlers();
                } else if (swiper.params.loop !== oldLoop && !swiper.params.loop) {
                    swiper.loopDestroy();
                    swiper.updateSlides();
                }
            },
            isEnableLoop: function (slidesPerView) {
                let result = 'yes' === settings.infinite;
                return result && methods.isEnoughtElements(slidesPerView);
            },
            updateScrollSpeed: function () {
                swiper.params.speed = settings.transition_speed;
                if (swiper.params.slidesPerGroup == swiper.params.slidesPerView) {
                    swiper.params.speed = swiper.params.slidesPerView * settings.transition_speed;
                }
            },
            updateSwiperOption: function (propertyName) {
                if (!$widget.vars.sliderInitialized) return;

                let respControlNames = ['slides_to_scroll', 'slides_gap', 'slides_per_view'];
                let handled = false;
                respControlNames.forEach(controlName => {
                    if (propertyName.startsWith(controlName)) {
                        swiper.params.breakpoints = this.getSwiperOptions().breakpoints;
                        swiper.currentBreakpoint = false;
                        swiper.update();
                        handled = true;
                        methods.findAnimationInElements();
                        if (propertyName.startsWith('slides_per_view')) {
                            methods.updateActiveElements();
                            methods.addElementsAnimation();
                        }
                    }
                });

                if (handled) {
                    return;
                }

                const newSettingValue = settings[propertyName];
                let propertyToUpdate = data.changeableProperties[propertyName],
                    valueToUpdate = newSettingValue;

                switch (propertyName) {
                    case 'autoplay_speed':
                        swiper.autoplay.stop();
                        propertyToUpdate = 'autoplay';
                        valueToUpdate = {
                            delay: newSettingValue,
                            disableOnInteraction: true
                        };
                        break;
                    case 'pause_on_hover':
                        methods.handlePauseOnHover()
                        break;
                    case 'autoplay':
                        swiper.autoplay.stop();
                        valueToUpdate = methods.getAutoplayConfig()
                        methods.handlePauseOnHover()
                        break;
                }

                if ('pause_on_hover' !== propertyName) {
                    swiper.params[propertyToUpdate] = valueToUpdate;
                }
                swiper.update();
                if ('autoplay' === propertyToUpdate) {
                    if ('yes' === settings.autoplay) {
                        swiper.autoplay.start();
                    }
                }
            },
            isLoop: function () {
                return widgetType === 'the7-slider-loop';
            },

            updateActiveElements: function () {
                if (!swiper.params) return;
                let activeElements = [];

                let slidesPerView = swiper.params.slidesPerView ? swiper.params.slidesPerView : 1;
                let slidesStart = swiper.activeIndex;
                let slidesEnd = slidesStart + slidesPerView

                for (let activeSlideIndex = slidesStart; activeSlideIndex < slidesEnd; activeSlideIndex++) {
                    let activeSlide = elements.animatedSlides[activeSlideIndex];

                    if (activeSlide === undefined) {
                        continue;
                    }
                    //do not alter activeSlide
                    activeElements = $.merge($.merge([], activeSlide), activeElements);
                }

                let $activeDuplicates = $(swiper.slides).filter(data.selectors.activeDuplicate);
                $activeDuplicates.each(function (index) {
                    const duplicateIndex = $(swiper.slides).index($(this));
                    slidesStart = duplicateIndex;
                    slidesEnd = slidesStart + slidesPerView

                    for (let activeSlideIndex = duplicateIndex; activeSlideIndex < slidesEnd; activeSlideIndex++) {

                        const activeDuplicateSlide = elements.animatedSlides[activeSlideIndex];
                        if (activeDuplicateSlide !== undefined) {
                            //do not alter activeSlide
                            activeElements = $.merge($.merge([], activeDuplicateSlide), activeElements);
                        }
                    }
                });
                elements.activeElements = activeElements;
            },
            removeElementsAnimation(isForce = false) {
                if (!$widget.vars.sliderInitialized) return;
                let notActiveElements = [];
                Object.keys(elements.animatedSlides).forEach(function (slideKey) {
                    let e = elements.animatedSlides[slideKey];
                    notActiveElements = $.merge($.merge([], e), notActiveElements);
                });
                if (!isForce) {
                    notActiveElements = notActiveElements.filter(function (e) {
                        let val = $.inArray(e, elements.activeElements)
                        return val < 0
                    })
                }
                elementorAnimation.resetElements(notActiveElements)
            },

            addElementsAnimation() {
                if (!$widget.vars.sliderInitialized) return;
                elementorAnimation.animateElements(elements.activeElements);
            },

            findAnimationInElements() {
                if (!$widget.vars.sliderInitialized) return;
                let animatedSlides = {};

                $(swiper.slides).each(function (index) {
                        const $slide = $(this);
                        let elementsWithAnimation = elementorAnimation.findAnimationsInNode($slide);
                        if (elementsWithAnimation.length) {
                            animatedSlides[index] = elementsWithAnimation;
                        }
                    }
                );
                elements.animatedSlides = animatedSlides;
            }
        };

        //global functions
        $widget.refresh = function () {
            settings = elementorSettings.getSettings();
            methods.unBindEvents();
            methods.bindEvents();
        };
        $widget.delete = function () {
            methods.unBindEvents();
            $widget.removeData("the7Slider");
            if (swiper) swiper.destroy();
        };

        $widget.updateSwiperOption = function (propertyName) {
            settings = elementorSettings.getSettings();
            methods.updateSwiperOption(propertyName);
        }

        $widget.onDocumentLoaded = function (document) {
            if (document.config.type === 'loop-item' && methods.isLoop()) {
                if (!$widget.vars.sliderInitialized) return;
                methods.handleSlider();
                let elementsToRemove = ['.swiper-pagination', '.the7-swiper-button'];
                const templateID = document.id;
                elementsToRemove = [...elementsToRemove, 'style#loop-' + templateID, 'link#font-loop-' + templateID, 'style#loop-dynamic-' + templateID];
                elementsToRemove.forEach(elementToRemove => {
                    $widget.find(elementToRemove).remove();
                });
            }
        }
        $widget.getSwiper = function(){
            return swiper;
        }
        methods.init();
    };

    $.fn.the7Slider = function () {
        return this.each(function () {
            var widgetData = $(this).data('the7Slider');
            if (widgetData !== undefined) {
                widgetData.delete();
            }
            new $.the7Slider(this);
        });
    };
});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-slider.default", widgetHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-slider-loop.post", widgetHandler);

        function widgetHandler($widget, $) {
            $(document).ready(function () {
                if (elementorFrontend.isEditMode()) {
                    The7ElementorAnimation.patchElementsAnimation($widget);
                }
                $widget.the7Slider();
            })
        }

        if (elementorFrontend.isEditMode()) {
            elementorEditorAddOnChangeHandler("the7-slider", refresh);
            elementorEditorAddOnChangeHandler("the7-slider-loop", refresh);
            elementor.on("document:loaded", onDocumentLoaded);
        } else {
            The7ElementorAnimation.patchElementsAnimation($('.elementor-widget-the7-slider-common .the7-swiper-slide'));
        }

        function onDocumentLoaded(document) {
            var $elements = $('.elementor-widget-the7-slider-loop');
            $elements.each(function () {
                const $widget = $(this);
                const widgetData = $widget.data('the7Slider');
                if (typeof widgetData !== 'undefined') {
                    widgetData.onDocumentLoaded(document);
                }
            });
        }

        function refresh(controlView, widgetView) {
            let refresh_controls = [
                "autoplay_speed",
                "pause_on_hover",
                "autoplay",
                "transition_speed",
                ...The7ElementorSettings.getResponsiveSettingList('slides_gap'),
                "slides_to_scroll",
                ...The7ElementorSettings.getResponsiveSettingList('slides_per_view'),
            ];
            const controlName = controlView.model.get('name');
            if (-1 !== refresh_controls.indexOf(controlName)) {
                const $widget = $(widgetView.$el);
                const widgetData = $widget.data('the7Slider');
                if (typeof widgetData !== 'undefined') {
                    widgetData.updateSwiperOption(controlName);
                }
            }
        }

    });
})(jQuery);