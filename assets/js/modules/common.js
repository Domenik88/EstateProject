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
        this.initSmoothScroll();
        this.initSimpleScroll();
        this.initNavLinks();
        this.initLazyLoad();
        this.initTriggerSlider();
        this.initDefaultSlider();
        this.initToggleActive();
        this.initEstateGallerySlider();
        this.initStickyBlock();
        this.initConfCollapseForm();
        this.initShowMore();
        this.initSlideMenu();
        this.initFormatInput();
        this.initPrintListing();
        this.initContentTabs();
        this.initAddToFavorites();
        this.initFullSearch();
        this.initDropdownButton();
        this.initSvgMap();
        this.initToggleNext();
        this.initKeywordsInput();
        this.initKeywordsInput();
    },
    
    initCache() {
        this.$page = $('html, body');
        this.$window = $(window);
        this.$body = $('body');
        
        this.$overlay = $('.js-overlay');
        this.$menuBtn = $('.js-menu-btn');
        this.$header = $('.js-header');
        this.$scrollTop = $('.js-scroll-top');
        this.$navLink = $('.js-nav-link');
        this.$checkInWindow = $('.js-check-in-window');

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

        this.$contentTab = $('.js-content-tab');
        this.$contentTabNav = $('.js-content-tab-nav');
        this.$tabImg = $('.js-tab-img');

        this.$slideMenu = $('.js-slide-menu');
        this.$slideMenuItem = $('.js-slide-menu-item');
        this.$slideMenuWrap = $('.js-slide-menu-wrap');
        this.$slideMenuBtnLeft = $('.js-slide-menu-btn-left');
        this.$slideMenuBtnRight = $('.js-slide-menu-btn-right');
        this.$slideMenuLink = $('.js-slide-menu-link');

        this.$printListing = $('.js-print-listing');
        this.$listingPrintPopup = $('.js-listing-print-popup');
        this.$dataContent = $('.js-data-content');
        this.$addToFavorites = $('.js-favorite-listing');

        this.$fullSearch = $('.js-full-search');
        this.$fullSearchTypeLink = $('.js-full-search-type-link');
        this.$fullSearchTypeInput = $('.js-full-search-type');
        this.$fullSearchTab = $('.js-fs-tab');

        this.$dropdownButton = $('.js-dropdown-button');
        this.$dropdownSelected = $('.js-dropdown-selected');

        this.$svgMap = $('.js-svg-map');
        this.$svgMapLink = $('.js-svg-map-link');

        this.$toggleNext = $('.js-toggle-next');

        this.$keywords = $('.js-keywords');
        this.$keywordsArray = $('.js-keywords-array');
        this.$keywordsInsert = $('.js-keywords-insert');
        this.$addKeyword = $('.js-add-keyword');
        this.$keywordsList = $('.js-keywords-list');

        this.windowWidth = $_.$window.width();
        this.windowHeight = $_.$window.height();
        
        this.breakpoints = {
            tablet: 1000,
            preMobile: 900,
            mobile: 700
        };
        
        this.selectors = {
            smoothScroll: '.js-smooth-scroll',
            simpleScroll: '.js-simple-scroll',
            lazyLoad: '.js-lazy',
            jsWrap: '.js-wrap',
            sliderNav: '.js-slider-nav',
            arrowLeft: '.js-arrow-left',
            arrowRight: '.js-arrow-right',
            current: '.js-current',
            total: '.js-total',
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

    initKeywordsInput() {
        function pasteKeywords(obj) {
            const { keywords, $keywordsList } = obj;

            $keywordsList.html('');

            keywords.forEach(item => {
                $keywordsList.append($(`<div class="keyword">${item}<span class="remove"></span></div>`));
            });
        }

        $_.$keywords.each((key, item) => {
            const
                $currentModule = $(item),
                $keywordsArray = $currentModule.find($_.$keywordsArray),
                $keywordsInsert = $currentModule.find($_.$keywordsInsert),
                $addKeyword = $currentModule.find($_.$addKeyword),
                $keywordsList = $currentModule.find($_.$keywordsList);

            let keywords = [];

            $currentModule.on('click', '.remove', (e) => {
                const
                    $currentTarget = $(e.currentTarget),
                    $relatedKeyword = $currentTarget.closest('.keyword'),
                    text = $relatedKeyword.text(),
                    index = keywords.indexOf(text);

                if (index !== -1) {
                    keywords.splice(index, 1);
                    $relatedKeyword.remove();
                    $keywordsArray.attr('value', JSON.stringify(keywords));
                }
            });

            $addKeyword.on('click', () => {
                const val = $keywordsInsert.val().trim();

                if (val.length && (keywords.indexOf(val) === -1)) {
                    keywords.push(val);
                    $keywordsArray.attr('value', JSON.stringify(keywords));
                    pasteKeywords({$keywordsList, keywords});
                }
                $keywordsInsert.val('');
            });
        });
    },

    initSimpleScroll() {
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

        $($_.selectors.simpleScroll).each((key, el) => {
            initScroll(el);
        });
    },

    initToggleNext: function() {
        $_.$toggleNext.on('click', function (e) {
            const
                $currentTarget = $(e.currentTarget),
                $next = $currentTarget.next(),
                isActive = $currentTarget.hasClass('_active'),
                nextIsHidden = $next.is(':hidden');

            if (isActive && !nextIsHidden) {
                $currentTarget.removeClass('_active');
                $next.stop().slideUp(300);
            }

            if (!isActive && nextIsHidden) {
                $currentTarget.addClass('_active');
                $next.stop().slideDown(300);
            }
        });

        $_.$body.on('body:resize:width', function () {
            $_.$toggleNext.removeClass('_active').next().attr('style', '');
        });
    },

    initSvgMap() {
        function filterByEventItemId($links, event) {
            return $links.filter(`[data-id="${$(event.currentTarget).data('id')}"]`);
        }

        $_.$body.on('trigger:init-svg-map', () => {
            $_.$svgMap.each((key, item) => {
                const
                    $currentMap = $(item),
                    $citiesGroup = $currentMap.find('.js-cities-group'),
                    $cities = $citiesGroup.find('.js-svg-map-city'),
                    $links = $_.$svgMap.find($_.$svgMapLink);

                $links.on('mouseenter', (e) => {
                    $links.removeClass('_active');
                    filterByEventItemId($cities, e).addClass('_active');
                });

                $links.on('mouseleave', (e) => {
                    filterByEventItemId($cities, e).removeClass('_active');
                });

                $links.on('trigger:click', (e) => {
                    location.href = e.currentTarget.href;
                });

                $cities.on('mouseenter', (e) => {
                    $links.removeClass('_active');
                    filterByEventItemId($links, e).addClass('_active');
                });

                $cities.on('mouseleave', (e) => {
                    filterByEventItemId($links, e).removeClass('_active');
                });

                $cities.on('click', (e) => {
                    filterByEventItemId($links, e).trigger('trigger:click');
                });
            });
        });
    },

    initDropdownButton() {
        function toggleButton($btn, selected) {
            $btn[selected ? 'addClass' : 'removeClass']('_selected');
        }

        function findFieldText($btn, val) {
            return $btn.find(`[value="${val}"]`).text();
        }

        function showSelected(data) {
            const
                { $currentButton, $selectedContainer, $innerFields, dataProps } = data,
                { patternSimple, patternTwin, patternMulti, patternReplace } = dataProps,
                values = $innerFields.serializeArray();

            if (patternSimple) {
                const value = values[0] && values[0].value;
                if (value) $selectedContainer.html(patternSimple.replace(patternReplace, value));
                toggleButton($currentButton, !!value);
            }

            if (patternTwin) {
                const
                    { first, last, both } = patternTwin,
                    valueFirst = values[0] && values[0].value,
                    valueSecond = values[1] && values[1].value;

                if (valueFirst && valueSecond) {
                    $selectedContainer.html(both.replace(patternReplace, valueFirst).replace(patternReplace, valueSecond));
                } else if (valueFirst) {
                    $selectedContainer.html(first.replace(patternReplace, valueFirst));
                } else {
                    $selectedContainer.html(last.replace(patternReplace, valueSecond));
                }

                toggleButton($currentButton, !!(valueFirst || valueSecond));
            }

            if (patternMulti) {
                const { single, multi } = patternMulti;

                if (values.length > 1) {
                    $selectedContainer.html(multi.replace(patternReplace, values.length));
                } else if (values.length > 0) {
                    $selectedContainer.html(single.replace(patternReplace, values[0].value));
                }

                toggleButton($currentButton, !!values.length);
            }
        }

        function setMaxHeight($item) {
            const { top } = $item[0].getBoundingClientRect();
            $item.css('max-height', `${$_.windowHeight - top - 100}px`);
        }

        $_.$dropdownButton.each((key, item) => {
            const
                $currentButton = $(item),
                $maxScroll = $currentButton.find('.js-max-height'),
                $selectedContainer = $currentButton.find($_.$dropdownSelected),
                $innerFields = $currentButton.find('input, select'),
                dataProps = $currentButton.data('props');

            if ($maxScroll.length) {
                setMaxHeight($maxScroll);

                $_.$body.on('body:resize', () => {
                    setMaxHeight($maxScroll);
                });
            }

            if (dataProps) {
                $innerFields.on('change', () => {
                    showSelected({
                        $currentButton,
                        $selectedContainer,
                        $innerFields,
                        dataProps,
                    });
                });
            }
        });
    },

    initFullSearch() {
        $_.$fullSearch.each((key, item) => {
            const
                $fullSearch = $(item),
                $relatedTypeLinks = $fullSearch.find($_.$fullSearchTypeLink),
                $relatedTypeInput = $fullSearch.find($_.$fullSearchTypeInput),
                $relatedTabs = $fullSearch.find($_.$fullSearchTab);

            $relatedTypeLinks.on('click', (e) => {
                const
                    $currentTarget = $(e.currentTarget),
                    $dataVal = $currentTarget.data('val'),
                    $dataToggle = $currentTarget.data('toggle') || 'default',
                    $matchedTabs = $relatedTabs.filter(`[class*="-${$dataToggle}"]`);

                $relatedTabs.addClass('_hide');
                $matchedTabs.removeClass('_hide');
                $relatedTypeInput.attr('value', $dataVal);

                $_.$body.trigger('body:trigger:init:scrollbars');
            });
        })
    },

    initContentTabs() {
        function switchTabs($wrap, dataContentId) {
            const
                $tabImages = $wrap.find($_.$tabImg),
                $relatedTab = $wrap.find($_.$contentTab).filter(`[data-content-id="${dataContentId}"]`);

            $relatedTab.addClass('_active').siblings().removeClass('_active');

            if ($tabImages.length) {
                $tabImages.each((key, item) => {
                    const
                        $item = $(item),
                        dataSrc = $item.data('src');

                    $item.attr('src', dataSrc);
                });
            }
        }

        $_.$contentTabNav.on('click', (e) => {
            const
                $currentLink = $(e.currentTarget),
                dataContentId = $currentLink.data('content-id'),
                dataInitMap = $currentLink.data('init-map'),
                $wrap = $currentLink.closest($_.selectors.jsWrap);

            if (dataInitMap) $_.$body.trigger('trigger:init-map', dataInitMap);

            if ($.isArray(dataContentId)) {
                dataContentId.forEach(item => switchTabs($wrap, item));
            } else {
                switchTabs($wrap, dataContentId);
            }
        });
    },

    initPrintListing() {
        if (!$_.$printListing.length) return false;

        let firstLoad = true;

        $_.$printListing.on('click', () => {
            const $map = $('#listing-print-map');

            if (firstLoad) {
                firstLoad = false;

                $map.on('trigger:open-street-map-lite-loaded', () => {
                    setTimeout(() => {
                        window.print();
                    }, 1000);
                });
            } else {
                window.print();
            }

            pasteContent();
        });

        window.onbeforeprint = () => {
            $_.$body.addClass('_print');
        };

        window.onafterprint = () => {
            $_.$body.removeClass('_print');
        };

        function pasteContent() {
            const $dataContentItems = $_.$listingPrintPopup.find($_.$dataContent);

            $dataContentItems.each((key, item) => {
                const
                    $currentItem = $(item),
                    dataContent = $currentItem.data('content'),
                    dataSrc = $currentItem.data('src');

                if (typeof dataContent !== "undefined") $currentItem.html(dataContent);
                if (typeof dataSrc !== "undefined") $currentItem.attr('src', dataSrc);
            });

            $_.$body.trigger('trigger:init-map', '#listing-print-map');
        }
    },

    initAddToFavorites() {
        $_.$addToFavorites.on('click', (e) => {
            e.preventDefault();
            const
                $currentTarget = $(e.currentTarget),
                dataUrl = $currentTarget.data('url'),
                requestParameters = {
                    url: dataUrl,
                    type: 'POST',
                    dataType: 'json',
                };
            $.ajax(requestParameters).done(() => {
                $currentTarget.toggleClass('_active');
            })
        });
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

            $currentInput.on('trigger:set-val', (e, data) => {
                const { val, change=false } = data;

                $currentInput.val(_formatInputVal({
                    dataFormatParams,
                    val,
                }));

                if (change) $currentInput.change();
            });
        });
    },

    initSlideMenu() {
        function moveMenu(params) {
            const { fixOffset, $currentMenu, $relatedWrap } = params;

            $currentMenu.stop(true, true).animate({'left': -fixOffset}, 300, () => {
                checkDrag({
                    $currentMenu,
                    $relatedWrap,
                });
            });
        }

        function checkDrag(params) {
            const
                { $currentMenu, $relatedWrap, ui } = params,
                { right: wrapRight, left: wrapLeft, width: wrapWidth } = $relatedWrap[0].getBoundingClientRect(),
                { right: menuRight, left: menuLeft, width: menuWidth } = $currentMenu[0].getBoundingClientRect(),
                widthDiff = Math.min(wrapWidth - menuWidth, 0);

            $relatedWrap[menuRight > wrapRight ? 'removeClass' : 'addClass']('_end');
            $relatedWrap[wrapLeft === menuLeft ? 'addClass' : 'removeClass']('_start');

            if (ui && ui.position) {
                ui.position.left = Math.min(0, ui.position.left);
                ui.position.left = Math.max(widthDiff, ui.position.left);
            }
        }

        function bindLeftButton(params) {
            const { $currentMenu, $relatedWrap, $relatedMenuItems, $relatedButtonLeft } = params;

            $relatedButtonLeft.on('click', () => {
                const
                    { left: wrapLeft } = $relatedWrap[0].getBoundingClientRect(),
                    { left: menuLeft } = $currentMenu[0].getBoundingClientRect();

                $relatedMenuItems.each((key, item) => {
                    const
                        $item = $(item),
                        { left: itemLeft } = item.getBoundingClientRect(),
                        { left: nextItemLeft } = $item.next()[0].getBoundingClientRect();

                    if (nextItemLeft >= wrapLeft) {
                        const
                            diffItemLeft = wrapLeft - itemLeft,
                            diffWrapsLeft = wrapLeft - menuLeft,
                            paddingRight =  parseInt($item.css('padding-right')),
                            offset = diffWrapsLeft - diffItemLeft - $relatedButtonLeft.width() - paddingRight,
                            fixOffset = Math.max(offset, 0);

                        if (fixOffset === 0) $relatedWrap.addClass('_start');
                        moveMenu({ fixOffset, $currentMenu, $relatedWrap });
                        return false;
                    }
                });
            }).addClass('_init');
        }

        function bindRightButton(params) {
            const { $currentMenu, $relatedWrap, $relatedMenuItems, $relatedButtonRight } = params;

            $relatedButtonRight.on('click', () => {
                const
                    { right: wrapRight, left: wrapLeft, width: wrapWidth } = $relatedWrap[0].getBoundingClientRect(),
                    { left: menuLeft, width: menuWidth } = $currentMenu[0].getBoundingClientRect(),
                    widthDiff = menuWidth - wrapWidth;

                $relatedMenuItems.each((key, item) => {
                    const { right: itemRight } = item.getBoundingClientRect();

                    if (itemRight > wrapRight) {
                        const
                            diffItemRight = itemRight - wrapRight,
                            diffWrapsLeft = menuLeft - wrapLeft,
                            offset = diffWrapsLeft - diffItemRight - $relatedButtonRight.width(),
                            fixOffset = Math.min(Math.abs(offset), Math.abs(widthDiff));

                        if (fixOffset === widthDiff) $relatedWrap.addClass('_end');
                        moveMenu({ fixOffset, $currentMenu, $relatedWrap });
                        return false;
                    }
                });
            }).addClass('_init');
        }

        $_.$slideMenu.each((key, item) => {
            const
                $currentMenu = $(item),
                $relatedWrap = $currentMenu.closest($_.$slideMenuWrap),
                $relatedMenuItems = $relatedWrap.find($_.$slideMenuItem),
                $relatedButtonLeft = $relatedWrap.find($_.$slideMenuBtnLeft),
                $relatedButtonRight = $relatedWrap.find($_.$slideMenuBtnRight);

            $currentMenu.draggable({
                axis: 'x',
                create: () => {
                    checkDrag({
                        $currentMenu,
                        $relatedWrap,
                    })

                    bindLeftButton({
                        $currentMenu,
                        $relatedWrap,
                        $relatedMenuItems,
                        $relatedButtonLeft,
                    });

                    bindRightButton({
                        $currentMenu,
                        $relatedWrap,
                        $relatedMenuItems,
                        $relatedButtonRight,
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
        });

        $_.$slideMenuLink.on('click', (e) => {
            const
                $currentTarget = $(e.currentTarget),
                $relatedMenu = $currentTarget.closest($_.$slideMenu),
                $relatedLinks = $relatedMenu.find($_.$slideMenuLink);

            $relatedLinks.removeClass('_active');
            $currentTarget.addClass('_active');
        });
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
                $wrap = $btn.closest($_.selectors.jsWrap),
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
        if (!$_.$estateGallerySlider.length) return false;

        const
            dataLazyInner = $_.$estateGallerySlider.data('lazy-inner'),
            { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($_.$estateGallerySlider);

        $_.$estateGallerySlider.on('init', function (event, slick) {
            if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
            if (dataLazyInner) $_._initSliderLazyInner(slick);
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
                        dataSliderParams = $currentSlider.data('slider-parameters'),
                        isInit = $currentSlider.hasClass('slick-initialized'),
                        hasRelatedSlides = $slides && $slides.length && $slides[key];

                    if (isInit && hasRelatedSlides) {
                        $currentSlider.slick('unslick');
                        $currentSlider.html($slides[key]);
                    }

                    if ((!isInit && !hasRelatedSlides) || (isInit && hasRelatedSlides)) {
                        const
                            dataLazyInner = $currentSlider.data('lazy-inner'),
                            { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);

                        $currentSlider
                            .on('init', function (event, slick) {
                                $_.$body.trigger('update:lazy-load');
                                if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                                if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                                if (dataLazyInner) $_._initSliderLazyInner(slick);
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
                                ...sliderParams,
                                ...dataSliderParams
                            });
                    }
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

                // data-loader="inlineSvg" data-src="path/name.svg"
                inlineSvg: function(element) {
                    const
                        dataTrigger = element.data('trigger'),
                        dataSrc = element.data('src');

                    element.load(dataSrc, () => {
                        const reInitCIW = element.find('.js-check-in-window').length;

                        if (reInitCIW) $_.$checkInWindow = $('.js-check-in-window');
                        if (dataTrigger) setTimeout(() => {$_.$body.trigger(dataTrigger)}, 500);
                    });
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
                $preventChild = $currentSlider.find('[data-prevent-parent-swipe]'),
                dataLazyInner = $currentSlider.data('lazy-inner'),
                dataParameters = $currentSlider.data('slider-parameters') || {},
                { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);

            $currentSlider
                .on('init', function (event, slick) {
                    $_.$body.trigger('update:lazy-load');
                    if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                    if ($preventChild.length) $_._preventParentSliderSwipe($currentSlider, $preventChild);
                    if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                    if (dataLazyInner) $_._initSliderLazyInner(slick);
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


    _initSliderLazyInner(slick) {
        const
            { $slider, $slides } = slick,
            innerSliderSelector = $slider.data('inner-slider-selector'),
            dataImgSelector = $slider.data('img-selector');

        $_._initSliderInner({
            fromIndex: 0,
            slick,
            $slides,
            props: {
                dataImgSelector,
                innerSliderSelector,
            }
        });

        $slider.on('beforeChange', (event, slick, currentSlide, nextSlide) => {
            $_._initSliderInner({
                fromIndex: nextSlide,
                slick,
                $slides,
                props: {
                    dataImgSelector,
                    innerSliderSelector,
                }
            });
        });
    },

    _initSliderInner(data) {
        const { slick, fromIndex, $slides, props } = data;

        for (let i = fromIndex; i < (fromIndex + slick.options.slidesToShow * 2); i++) {
            const $slide = $slides.eq(i);

            if ($slide.length) $_._initSliderInnerContent({
                $slide,
                ...props
            });
        }
    },

    _initSliderInnerContent(data) {
        const { $slide, dataImgSelector, innerSliderSelector } = data;

        if (dataImgSelector) {
            const $images = $slide.find(`[data-${dataImgSelector}]`);

            $images.each((key, item) => {
                const $item = $(item);
                $item.attr('src', $item.data(dataImgSelector));
            });
        }

        if (innerSliderSelector) {
            const $innerSliders = $slide.find(innerSliderSelector);

            $_.$body.trigger('trigger:init-slider', {
                $sliders: $innerSliders
            })
        }
    },

    initSmoothScroll() {
        // setTimeout(() => {
        //     $('.controls-bar .js-call-popup').eq(0).click()
        // }, 500);

        function setScrollBars() {
            const $scrollbar = $($_.selectors.smoothScroll);

            $scrollbar.each(function (key, item) {
                const
                    $item = $(item),
                    $breakpointDetect = $item.find('.js-bp-detect'),
                    isInit = Scrollbar.has(item);

                if ($breakpointDetect.length) {
                    if ($breakpointDetect.eq(0).is(':hidden')) {
                        init(item, isInit);
                    } else {
                        if (isInit) {
                            $item.off('trigger:scroll-top');
                            Scrollbar.destroy(item);
                        }
                    }
                } else {
                    init(item, isInit);
                }
            });
        }

        function init(item, update) {
            const $item = $(item);

            if (update) {
                Scrollbar.get(item).update();

            } else {
                const
                    dataScrollOptions = $item.data('scroll-options') || {},
                    dataTriggerOnScroll = $item.data('trigger-on-scroll');

                const scrollbar = Scrollbar.init(item, {
                    damping: 0.1,
                    thumbMinSize: 50,
                    alwaysShowTracks: true,
                    continuousScrolling: false,
                    ...dataScrollOptions
                });

                if (dataTriggerOnScroll) {
                    scrollbar.addListener((status) => {
                        $item.trigger(dataTriggerOnScroll);
                    });
                }

                $item.on('trigger:scroll-top', () => {
                    scrollbar.scrollTop = 0;
                });
            }
        }

        $_.$body.on('body:resize', function () {
            setScrollBars();
        });

        $_.$body.on('body:trigger:init:scrollbars', function () {
            setScrollBars();
        });

        $_.$body.on('trigger:init-scrollbar', (e, data) => {
            const { el } = data;

            init(el);
        });

        setScrollBars();
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
                    $closestBccSibling = $target.closest('.js-bcc-sibling'),
                    $relatedBcc = $closestBccSibling.siblings('.js-bcc'),
                    $targetsToClose = $bccItems.not($targetToPrevent).not($relatedBcc);

                console.log('$target: ', $target);
                console.log('$closestBcc: ', $closestBcc);
                console.log('$closestBccSibling: ', $closestBccSibling);

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
            $wrap = $slider.closest($_.selectors.jsWrap),
            $sliderNav = $wrap.find($_.selectors.sliderNav);

        return {
            $sliderNav,
            $arrowLeft: $sliderNav.find($_.selectors.arrowLeft),
            $arrowRight: $sliderNav.find($_.selectors.arrowRight),
            $current: $sliderNav.find($_.selectors.current),
            $total: $sliderNav.find($_.selectors.total),
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

