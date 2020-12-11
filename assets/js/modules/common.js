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
        
        this.$arrowLeft = $('.js-arrow-left');
        this.$arrowRight = $('.js-arrow-right');
        this.$current = $('.js-current');
        this.$total = $('.js-total');

        this.$toggleActive = $('.js-toggle-active');

        this.$estateGallerySlider = $('.js-estate-gallery-slider');

        this.$map = $('#y-map');
        this.mapIsInit = false;
        
        this.$defaultSlider = $('.js-default-slider');
        
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
            const { $sliders, $slides } = obj;
            
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
                            // prevArrow: $arrowLeft,
                            // nextArrow: $arrowRight,
                            fade: false,
                            infinite: false,
                            dots: false
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
                { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);
            
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
                dots: false
            });
        })
    },
    
    initCustomScrollbar() {
        function initScroll(el) {
            const
                $currentTarget = $(el),
                dataTriggerOnScroll = $currentTarget.data('trigger-on-scroll');

            const scroll = new SimpleBar(el, {
                autoHide: false,
                scrollbarMinSize: 100,
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
                dataTrigger = $el.data('trigger'),
                dataTriggerIn = $el.data('trigger-in'),
                dataTriggerOut = $el.data('trigger-out'),
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
                        if (dataTrigger) $_.$body.trigger(dataTrigger);
                    }
                }
            }, delay)
        });
    },
    
    _getRelatedSliderNav($slider) {
        const $wrap = $slider.closest($_.$jsWrap);
        
        return {
            $arrowLeft: $wrap.find($_.$arrowLeft),
            $arrowRight: $wrap.find($_.$arrowRight),
            $current: $wrap.find($_.$current),
            $total: $wrap.find($_.$total),
        }
    },
    
    _initSliderCounter(slick, $current, $total) {
        $current.html($_._addZero(slick.currentSlide + 1));
        $total.html($_._addZero(slick.slideCount));
        
        slick.$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
            $current.html($_._addZero(nextSlide + 1))
        });
    },
    
    _addZero(num) {
        return `${num < 10 ? '0' : ''}${num}`
    }
};

$(document).ready(() => {
    $_.init();
});

