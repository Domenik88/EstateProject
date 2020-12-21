var $_ = {
    init() {
        this.initCache();
        this.initMagic();
        this.initResizeTrigger();
        this.initBodyClickClose();
        this.initMenu();
        this.initForms();
        this.initScrollTopButton();
        this.initScrollEvents();
        this.initCustomScrollbar();
        this.initNavLinks();
        this.initLazyLoad();
        this.initDefaultSlider();
        this.initTriggerSlider();
        this.initToggleActive();
        this.initEstateGallerySlider();
        this.initStickyBlock();
        this.initConfCollapseForm();
        this.initShowMore();
        this.initSlideMenu();
        this.initFormatInput();
    },
    
    initCache() {
        this.$page = $('html, body');
        this.$window = $(window);
        this.$body = $('body');
        this.$jsWrap = $('.js-wrap');
        
        this.$overlay = $('.js-overlay');
        this.$menuBtn = $('.js-menu-btn');
        this.$header = $('.js-header');
        this.$scrollTop = $('.js-scroll-top');
        this.$navLink = $('.js-nav-link');
        this.$checkInWindow = $('.js-check-in-window');

        this.$sliderNav = $('.js-slider-nav');
        this.$arrowLeft = $('.js-arrow-left');
        this.$arrowRight = $('.js-arrow-right');
        this.$current = $('.js-current');
        this.$total = $('.js-total');

        this.$toggleActive = $('.js-toggle-active');

        this.$estateGallerySlider = $('.js-estate-gallery-slider');

        this.$stickyContainer = $('.js-sticky-container');
        this.$stickyBlock = $('.js-sticky-block');

        this.$collapse = $('.js-collapse');
        this.$confCollapseForm = $('.js-conf-collapse-form');

        this.$showMoreButton = $('.js-show-more-btn');

        this.$map = $('#y-map');
        this.mapIsInit = false;
        
        this.$defaultSlider = $('.js-default-slider');

        this.$formatInput = $('.js-format-input');


        this.$slideMenu = $('.js-slide-menu');
        this.$slideMenuItem = $('.js-slide-menu-item');
        this.$slideMenuWrap = $('.js-slide-menu-wrap');
        this.$slideMenuButton = $('.js-slide-menu-button');

        this.windowWidth = $_.$window.width();
        this.windowHeight = $_.$window.height();
        
        this.breakpoints = {
            tablet: 1000,
            preMobile: 900,
            mobile: 700
        };
        
        this.selectors = {
            scrollbar: '.js-custom-scrollbar',
            lazyLoad: '.js-lazy',
        };
        
        this.animationEvents = 'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd';
    },
    
    initMagic () {
        document.addEventListener("touchstart", function(){}, true);
        
        if (!("ontouchstart" in document.documentElement)) {
            document.documentElement.className += " no-touch";
        }
        
        function is_touch_device() {
            return !!('ontouchstart' in window);
        }
        is_touch_device();
    },

    initFormatInput() {
        $_.$formatInput.each((key, item) => {
            const
                $currentInput = $(item),
                dataFormatParams = $currentInput.data('format-props');

            $currentInput.val(_formatInputVal({
                dataFormatParams,
                val: $currentInput.val(),
            }));

            $currentInput.on('change', () => {
                $currentInput.val(_formatInputVal({
                    dataFormatParams,
                    val: $currentInput.val(),
                }));
            });

            $currentInput.on('trigger:set-val', (e, val) => {
                $currentInput.val(_formatInputVal({
                    dataFormatParams,
                    val,
                }));
            });
        });
    },

    initSlideMenu() {
        function checkDrag(params) {
            const
                { $currentMenu, $relatedWrap, ui } = params,
                { right: wrapRight, width: wrapWidth } = $relatedWrap[0].getBoundingClientRect(),
                { right: menuRight, width: menuWidth } = $currentMenu[0].getBoundingClientRect(),
                widthDiff = Math.min(wrapWidth - menuWidth, 0);

            if (menuRight > wrapRight) {
                $relatedWrap.removeClass('_end');
            } else {
                $relatedWrap.addClass('_end');
            }

            if (ui && ui.position) {
                ui.position.left = Math.min(0, ui.position.left);
                ui.position.left = Math.max(widthDiff, ui.position.left);
            }
        }

        function bindButton(params) {
            const { $currentMenu, $relatedWrap, $relatedMenuItems, $relatedButton } = params;

            $relatedButton.on('click', () => {
                const
                    { right: wrapRight, left: wrapLeft, width: wrapWidth } = $relatedWrap[0].getBoundingClientRect(),
                    { left: menuLeft, width: menuWidth } = $currentMenu[0].getBoundingClientRect(),
                    widthDiff = menuWidth - wrapWidth;

                $relatedMenuItems.each((key, item) => {
                    const { right: itemRight } = item.getBoundingClientRect();

                    if (itemRight > wrapRight) {
                        const
                            diffRight = itemRight - wrapRight,
                            diffLeft = menuLeft - wrapLeft,
                            offset = diffLeft - diffRight - $relatedButton.width(),
                            fixOffset = Math.min(Math.abs(offset), Math.abs(widthDiff));

                        if (fixOffset === widthDiff) $relatedWrap.addClass('_end');
                        $currentMenu.stop(true, true).animate({'left': -fixOffset}, 300);
                        return false;
                    }
                })
            });
        }

        $_.$slideMenu.each((key, item) => {
            const
                $currentMenu = $(item),
                $relatedWrap = $currentMenu.closest($_.$slideMenuWrap),
                $relatedMenuItems = $relatedWrap.find($_.$slideMenuItem),
                $relatedButton = $relatedWrap.find($_.$slideMenuButton);

            $currentMenu.draggable({
                axis: 'x',
                create: () => {
                    checkDrag({
                        $currentMenu,
                        $relatedWrap,
                    })

                    bindButton({
                        $currentMenu,
                        $relatedWrap,
                        $relatedMenuItems,
                        $relatedButton,
                    });
                }
            })
            .on('drag', ( event, ui ) => {
                checkDrag({
                    $currentMenu,
                    $relatedWrap,
                    ui,
                });
            });
        })
    },

    initStickyBlock() {
        function setSticky($currentStickyBlock, $relatedStickyContainer) {
            const
                { top: wrapTop, bottom: wrapBottom } = $relatedStickyContainer[0].getBoundingClientRect(),
                { height: blockHeight } = $currentStickyBlock[0].getBoundingClientRect(),
                { height: headerHeight } = $_.$header[0].getBoundingClientRect(),
                offset = headerHeight + 20;

            if (wrapBottom <= (blockHeight + offset)) {
                $currentStickyBlock.attr('style', '');
                $currentStickyBlock.removeClass('_stick-to-top').addClass('_stick-to-bottom');
            } else if (wrapTop <= offset) {
                $currentStickyBlock.css('top', offset);
                $currentStickyBlock.removeClass('_stick-to-bottom').addClass('_stick-to-top');
            } else {
                $currentStickyBlock.attr('style', '');
                $currentStickyBlock.removeClass('_stick-to-bottom _stick-to-top');
            }
        }

        function setStickyTop($currentStickyBlock) {
            const
                { height: headerHeight } = $_.$header[0].getBoundingClientRect(),
                offset = headerHeight + 20;

            $currentStickyBlock.css('top', offset);
            $currentStickyBlock.removeClass('_stick-to-bottom').addClass('_stick-to-top');
        }

        $_.$stickyBlock.each((key, item) => {
            const
                $currentStickyBlock = $(item),
                $relatedStickyContainer = $currentStickyBlock.closest($_.$stickyContainer);

            $_.$window.on('scroll', () => {
                setSticky($currentStickyBlock, $relatedStickyContainer);
            });

            $currentStickyBlock.on('trigger:update', () => {
                setSticky($currentStickyBlock, $relatedStickyContainer);
            });

            $currentStickyBlock.on('trigger:set-sticky-top', () => {
                setStickyTop($currentStickyBlock);
            });

            setSticky($currentStickyBlock, $relatedStickyContainer);
        });
    },

    initConfCollapseForm() {
        $_.$confCollapseForm.each((key, item) => {
            const
                $currentForm = $(item),
                $innerCollapseBlock = $currentForm.find($_.$collapse),
                $innerInputs = $currentForm.find('input[type="text"]'),
                $parentStickyBlock = $currentForm.closest('.js-sticky-block');

            $currentForm.on('change', () => {
                const
                    values = $innerInputs.map((key, item) => item.value.length > 0).toArray(),
                    method = values.indexOf(true) !== -1 ? 'slideDown' : 'slideUp',
                    props = {
                        duration: 300,
                    };

                if ($parentStickyBlock.length) props.step = () => $parentStickyBlock.trigger('trigger:update');

                $innerCollapseBlock[method](props);
            });
        });
    },

    initShowMore() {
        $_.$showMoreButton.on('click', (e) => {
            const
                $btn = $(e.currentTarget),
                $wrap = $btn.closest($_.$jsWrap),
                $stickyBlocks = $wrap.find($_.$stickyBlock),
                $hiddenElements = $btn.prevAll(':hidden'),
                show = $hiddenElements.length,
                $elementsToToggle = show ? $hiddenElements : $btn.prevAll().filter(
                    (key, item) => $(item).data('showed')
                ),
                animationsProps = {
                    duration: 300,
                };

            if ($stickyBlocks.length && !show) animationsProps.step = (step, data) => {
                if ($elementsToToggle.eq(0).is(data.elem)) $stickyBlocks.trigger('trigger:update');
            }

            if ($stickyBlocks.length && show) {
                $stickyBlocks.trigger('trigger:set-sticky-top');
                animationsProps.complete = () => $stickyBlocks.trigger('trigger:update');
            }

            if (show) {
                $elementsToToggle.each((key, item) => {
                    const
                        $item = $(item),
                        isAnimated = $item.hasClass('_animate');

                    if (!isAnimated) {
                        const delay = `${($elementsToToggle.length - key)*100 + 300}ms`;

                        $item.css('animation-delay', delay).addClass('_animate');
                    }
                })
            }

            $elementsToToggle.slideToggle(animationsProps).data('showed', !!show);
            $btn.toggleClass('_active');
        });

        $_.$body.on('body:resize:width', function () {
            $_.$showMoreButton.removeClass('_active').prevAll().attr('style', '').data('showed', false);
        });
    },

    initEstateGallerySlider() {
        const
            $currentSlider = $_.$estateGallerySlider,
            { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);


        $_.$estateGallerySlider.on('init', function (event, slick) {
            if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
        })
            .slick({
                lazyLoad: 'ondemand',
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                prevArrow: $arrowLeft,
                nextArrow: $arrowRight,
                fade: false,
                infinite: false,
                dots: false
            });
    },

    initToggleActive() {
        $_.$toggleActive.on('click', (e) => {
            $(e.currentTarget).toggleClass('_active');
        })
    },

    initTriggerSlider() {
        $_.$body.on('trigger:init-slider', (e, obj) => {
            const { $sliders, $slides, sliderParams={} } = obj;
            
            if ($sliders.length) {
                $sliders.each((key, item) => {
                    const
                        $currentSlider = $(item),
                        { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);
    
                    if ($slides && $slides.length && $slides[key]) {
                        if ($currentSlider.hasClass('slick-initialized')) $currentSlider.slick('unslick');
                        $currentSlider.html($slides[key]);
                    }
    
                    $currentSlider
                        .on('init', function (event, slick) {
                            $_.$body.trigger('update:lazy-load');
                            if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                        })
                        .slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: true,
                            prevArrow: $arrowLeft,
                            nextArrow: $arrowRight,
                            fade: false,
                            infinite: false,
                            dots: false,
                            ...sliderParams
                        });
                });
            }
        });
    },
    
    initLazyLoad() {
        function setLazyLoad() {
            $($_.selectors.lazyLoad).Lazy({
                scrollDirection: 'vertical',
                threshold: window.innerHeight / 2,
                visibleOnly: true,
                afterLoad: (element) => {
                    const
                        $currentEl = $(element),
                        hasObjectFit = $currentEl.hasClass('js-object-fit');
                    
                    if (hasObjectFit) objectFitPolyfill($currentEl);
                },
            });
        }
        
        $_.$body.on('update:lazy-load', function () {
            setLazyLoad();
        });
        
        setLazyLoad();
    },
    
    initDefaultSlider() {
        $_.$defaultSlider.each(function (key, item) {
            const
                $currentSlider = $(item),
                $innerSlider = $currentSlider.find($_.$defaultSlider),
                dataParameters = $currentSlider.data('slider-parameters') || {},
                { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);

            $currentSlider
                .on('init', function (event, slick) {
                    $_.$body.trigger('update:lazy-load');
                    if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                    if ($innerSlider.length) $_._preventParentSliderSwipe($currentSlider, $innerSlider);
                    if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                })
                .slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    prevArrow: $arrowLeft,
                    nextArrow: $arrowRight,
                    fade: false,
                    infinite: false,
                    dots: false,
                    ...dataParameters
                });
        });
    },
    
    initCustomScrollbar() {
        function initScroll(el) {
            const
                $currentTarget = $(el),
                dataMinSize = $currentTarget.data('min-size') || 50,
                dataTriggerOnScroll = $currentTarget.data('trigger-on-scroll');

            const scroll = new SimpleBar(el, {
                autoHide: false,
                scrollbarMinSize: dataMinSize,
            });
    
            if (scroll && scroll.getScrollElement) {
                const scrollElement = scroll.getScrollElement();
        
                $currentTarget.on('trigger:scroll-top', () => {
                    scrollElement.scrollTop = 0;
                });
        
                if (dataTriggerOnScroll) {
                    scrollElement.addEventListener('scroll', () => {
                        $currentTarget.trigger(dataTriggerOnScroll);
                    });
                }
            }
        }

        $($_.selectors.scrollbar).each((key, el) => {
            initScroll(el);
        });

        $_.$body.on('trigger:init-scrollbar', (e, data) => {
            const { el } = data;

            initScroll(el);
        });
    },
    
    initScrollTopButton () {
        if (!$_.$scrollTop.length) return true;
        
        function check() {
            var windowHeight = $_.$window.height(),
                pageOffsetTop = $(document).scrollTop(),
                pageOffsetBottom = pageOffsetTop + windowHeight,
                footerOffsetTop = $('.footer').offset().top,
                firstScreenScrolled = pageOffsetTop >= windowHeight,
                scrolledBelowFooter = pageOffsetBottom >= footerOffsetTop;
            
            if (firstScreenScrolled && !scrolledBelowFooter) {
                $_.$scrollTop.addClass('_show');
            } else {
                $_.$scrollTop.removeClass('_show');
            }
        }
        
        $_.$scrollTop.on('click', function () {
            $_._scroll(0);
        });
        
        $_.$window.scroll(function () {
            check();
        });
        
        check();
    },

    initForms() {
        form_adjuster.init({
            'file': true,
            'success': () => {
                var $form = $(form_adjuster.$form_cur),
                    $input = $form.find('input'),
                    tyTarget = $form.data('ty-target') || 'ty',
                    tyText = $form.data('ty-text');
                
                setTimeout(function() {
                    $form.trigger('reset').find('._active, .valid, .error').removeClass('_active valid error');
                    $input.removeClass('_active');
                }, 500);
                
                $_.$body.trigger('show:ty-popup', {
                    target: tyTarget,
                    tyText: tyText,
                });
            }
        });
    },
    
    initBodyClickClose () {
        $_.$body.on('click', (e) => {
            const
                $target = $(e.target),
                $bccItems = $('.js-bcc').filter('._active');

            if ($bccItems.length) {
                const
                    isBcc = $target.hasClass('js-bcc'),
                    $closestBcc = $target.closest('.js-bcc'),
                    $targetToPrevent = isBcc ? $target : $closestBcc,
                    dataBccPrevent = $targetToPrevent.data('bcc-prevent'),
                    dataSelector = '[data-bcc-prevent="'+dataBccPrevent+'"]',
                    $targetsToClose = $bccItems.not($targetToPrevent).not(dataSelector);
                
                $targetsToClose.removeClass('_active');
            }
        });
    },
    
    initResizeTrigger () {
        var resizeTimer = null,
            resizeDelay = 300,
            windowWidth = $_.$window.width(),
            windowHeight = $_.$window.height();
        
        $_.$window.resize(function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                var currentWidth = $_.$window.width(),
                    currentHeight = $_.$window.height(),
                    resizeWidth = windowWidth !== currentWidth,
                    resizeHeight = windowHeight !== currentHeight;
                
                if (resizeWidth) {
                    windowWidth = currentWidth;
                    $_.windowWidth = currentWidth;
                    
                    $_.$body.trigger('body:resize:width');
                    if (!resizeHeight) $_.$body.trigger('body:resize:only-width');
                }
                
                if (resizeHeight) {
                    windowHeight = currentHeight;
                    $_.windowHeight = currentHeight;
                    
                    $_.$body.trigger('body:resize:height');
                    if (!resizeWidth) $_.$body.trigger('body:resize:only-height');
                }
                
                $_.$body.trigger('body:resize');
            }, resizeDelay);
        });
    },
    
    initScrollEvents () {
        var stopScrollingDelay = 100,
            whileScrollingDelay = 200,
            checkIsReady = true,
            scrollTimer = null;
        
        $_.$window.scroll(function () {
            checkTimer();
            
            if (!$_.mapIsInit && $_.$map.length) $_.$map.trigger('trigger:check');
        });
        
        function callTimerFunctions() {
            $_._checkInWindow();
        }
        
        function checkTimer() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function () {
                callTimerFunctions();
            }, stopScrollingDelay);
            
            if (checkIsReady) {
                checkIsReady = false;
                callTimerFunctions();
                
                setTimeout(function () {
                    checkIsReady = true;
                }, whileScrollingDelay)
            }
        }
        
        setTimeout(function () {
            checkTimer();
        }, 1000);
        
        $_.$map.on('trigger:is-init', function () {
            $_.mapIsInit = true;
        });
    },
    
    initMenu() {
        $_.$menuBtn.on('click', function () {
            $_.$header.toggleClass('_active');
        })
    },
    
    initNavLinks () {
        $_.$navLink.on('click', function (e) {
            e.preventDefault();
            
            var $el = $(e.currentTarget),
                anchor = $el.attr('href'),
                id = anchor.indexOf('#') !== -1 ? anchor : "#" + anchor,
                headerHeight = $_.$header.height(),
                offset = $(id).offset().top - headerHeight;
            
            $_.$header.removeClass('_active');
            $_._scroll(offset);
        })
    },

    _getTranslateStyles(x = 0, y = 0) {
        return {
            "-webkit-transform": `translate3d(${x}px, ${y}px, 0)`,
            "-moz-transform": `translate3d(${x}px, ${y}px, 0)`,
            "-ms-transform": `translate3d(${x}px, ${y}px, 0)`,
            "-o-transform": `translate3d(${x}px, ${y}px, 0)`,
            transform: `translate3d(${x}px, ${y}px, 0)`,
        }
    },

    _initSliderDotsNav(obj) {
        const
            { slick, dotsCount: dotsCountParam } = obj,
            dotsCount = dotsCountParam ? (dotsCountParam % 2 !== 0 ? dotsCountParam : dotsCountParam - 1) : 5,
            dotsMid = (dotsCount - 1) / 2;

        if (!slick.$dots.data('isInit')) {
            slick.$dots.data('isInit', true).wrap('<div class="slider-dots-nav"></div>');

            const
                $wrap = slick.$dots.parent(),
                $dots = slick.$dots.children(),
                dotWidth = $dots.width(),
                dotHeight = $dots.height(),
                wrapWidth = dotWidth * dotsCount,
                dotsAreFit = slick.slideCount < dotsCount;

            $wrap.css({'width': wrapWidth, 'height': dotHeight});

            if (dotsAreFit) {
                $wrap.addClass('_center');
            } else {
                slick.$slider.on('beforeChange', (event, slick, currentSlide, nextSlide) => {
                    const
                        offset = dotWidth * (nextSlide - dotsMid),
                        minOffset = 0,
                        maxOffset = slick.$dots.width() - Math.abs(wrapWidth),
                        fixOffset = offset < 0 ? minOffset : (offset > maxOffset ? maxOffset : offset);

                    slick.$dots.css($_._getTranslateStyles(-fixOffset));
                });
            }
        }
    },
    
    _toggleActiveClasses($el) {
        $el.addClass('_active').siblings().removeClass('_active');
    },
    
    _scroll(val) {
        $_.$page.on('scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove', () => {
            $_.$page.stop();
        });
        
        $('html, body').stop().animate({scrollTop: val}, 1000, () => {
            $_.$page.off('scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove');
        })
    },
    
    _checkInWindow() {
        $_.$checkInWindow.each(function (key, item) {
            const
                $el = $(item),
                dataTriggerIn = $el.data('trigger-in'),
                dataTriggerOut = $el.data('trigger-out'),
                dataBodyTrigger = $el.data('trigger-body'),
                dataActiveOutOfTop = $el.data('active-out-of-top'),
                disableOut = $el.data('disable-out'),
                dataFirstDelay = $el.data('first-inw-delay'),
                preventDelay = $el.data('prevent-delay'),
                delay = preventDelay ? 0 : (dataFirstDelay || 0);
            
            setTimeout(() => {
                $el.data('prevent-delay', true);
                
                const
                    position = item.getBoundingClientRect(),
                    inWindowD = $el.data('in-window') || false,
                    outOfTop = position.bottom < 0,
                    outOfBottom = position.top > $_.windowHeight;
                
                if (dataActiveOutOfTop && outOfTop && !inWindowD) $el.addClass('_in-window');
                
                if (outOfTop || outOfBottom) {
                    if (inWindowD) {
                        $el.data('in-window', false);
                        if (!disableOut) $el.removeClass('_in-window');
                        if (dataTriggerOut) $el.trigger(dataTriggerOut);
                    }
                } else {
                    if (!inWindowD) {
                        $el.data('in-window', true);
                        $el.addClass('_in-window');
                        if (dataTriggerIn) $el.trigger(dataTriggerIn);

                        if (dataBodyTrigger) {
                            const { trigger, data='' } = dataBodyTrigger;

                            $_.$body.trigger(trigger, data)
                        }
                    }
                }
            }, delay)
        });
    },

    _preventParentSliderSwipe($parent, $child) {
        $child.on('touchstart mousedown', function(e) {
            $parent.slick('slickSetOption', 'swipe', false, false);
        });

        $child.on('touchend mouseup mouseout', function(e) {
            $parent.slick('slickSetOption', 'swipe', true, false);
        });
    },
    
    _getRelatedSliderNav($slider) {
        const
            $wrap = $slider.closest($_.$jsWrap),
            $sliderNav = $wrap.find($_.$sliderNav);
        
        return {
            $sliderNav,
            $arrowLeft: $sliderNav.find($_.$arrowLeft),
            $arrowRight: $sliderNav.find($_.$arrowRight),
            $current: $sliderNav.find($_.$current),
            $total: $sliderNav.find($_.$total),
        }
    },
    
    _initSliderCounter(slick, $current, $total) {
        $current.html(_addZero(slick.currentSlide + 1));
        $total.html(_addZero(slick.slideCount));
        
        slick.$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
            $current.html(_addZero(nextSlide + 1))
        });
    },
};

$(document).ready(() => {
    $_.init();
});

