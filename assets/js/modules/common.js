jQuery(function($){
    const $_ = {
        init() {
            this.initCache();
            this.initMagic();
            this.initResizeTrigger();
            this.initBodyClickClose();
            this.initMenu();
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
            this.initAutofill();
            this.initAutofillFilter();
            this.initClickPrevent();
            this.initTwinFields();
            this.initInputOnlyNumber();
            this.initMaxHeight();
            this.initMaxWidth();
        },

        initCache() {
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
                estateGallerySliderImg: '.js-estate-gallery-slider-img',
                hiddenInput: '.js-hidden-input',
            };

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
            this.$estateGallerySliderImg = $(this.selectors.estateGallerySliderImg);

            this.$stickyContainer = $('.js-sticky-container');
            this.$stickyBlock = $('.js-sticky-block');

            this.$collapse = $('.js-collapse');
            this.$confCollapseForm = $('.js-conf-collapse-form');

            this.$showMoreButton = $('.js-show-more-btn');

            this.$map = $('#y-map');
            this.mapIsInit = false;

            this.$defaultSlider = $('.js-default-slider');
            this.$estateCard = $('.js-estate-card');

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

            this.$autofill = $('.js-autofill');
            this.$autofillFilter = $('.js-autofill-filter');
            this.$autofillOption = $('.js-autofill-option');
            this.$autofillNoResults = $('.js-autofill-no-results');
            this.$autofillInput = $('.js-autofill-input');
            this.$autofillOptionsContainer = $('.js-autofill-options-container');

            this.$twinFields = $('.js-twin-fields');

            this.$onlyNumbers = $('.js-only-numbers');

            this.$maxHeight = $('.js-max-height');
            this.$maxWidth = $('.js-max-width');
            this.$maxWidthContainer = $('.js-max-width-container');

            this.windowWidth = window.outerWidth;
            this.windowHeight = $_.$window.height();

            this.breakpoints = {
                b1700: 1700,
                b1500: 1500,
                b1300: 1300,
                b1200: 1200,
                b1000: 1000,
                b900: 900,
                b700: 700,
                b500: 500,
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

        initInputOnlyNumber() {
            $_.$onlyNumbers.on('keyup change', (e) => {
                const
                    $currentTarget = $(e.currentTarget),
                    val = $currentTarget.val(),
                    fixVal = _getPureNumber(val);

                if (val !== fixVal) $currentTarget.val(fixVal).trigger('change');
            });
        },

        initTwinFields() {
            function getSelectOption(obj) {
                const { $wrap, name, val } = obj;
                return $wrap.find(`[data-name="${name}"]`).find(`[data-value="${val}"]`);
            }

            $_.$twinFields.each((key, item) => {
                const
                    $currentModule = $(item),
                    $relatedFields = $currentModule.find('input, select');

                $relatedFields.on('change', () => {
                    const
                        values = $relatedFields.serializeArray(),
                        { name: fistInputName, value: fistInputValue } = values[0],
                        { name: secondInputName, value: secondInputValue } = values[1];

                    if ((+fistInputValue && +secondInputValue) && (+fistInputValue > +secondInputValue)) {
                        if ($relatedFields.is('input')) {
                            const
                                firstInputVal = $relatedFields.eq(0).val(),
                                secondInputVal = $relatedFields.eq(1).val();

                            $relatedFields.eq(0).val(secondInputVal);
                            $relatedFields.eq(1).val(firstInputVal);
                        }

                        if ($relatedFields.is('select')) {
                            const
                                $setFirstSelectOption = getSelectOption({
                                    $wrap: $currentModule,
                                    name: fistInputName,
                                    val: secondInputValue,
                                }),
                                $setSecondSelectOption = getSelectOption({
                                    $wrap: $currentModule,
                                    name: secondInputName,
                                    val: fistInputValue,
                                });

                            if ($setFirstSelectOption.length && $setSecondSelectOption.length) {
                                $setFirstSelectOption.add($setSecondSelectOption).click();
                            }
                        }
                    }
                });
            });
        },

        initClickPrevent() {
            $_.$body.on('click', (e) => {
                const
                    $target = $(e.target),
                    targetIsPrevent = $target.hasClass('js-prevent'),
                    closestPrevent = $target.closest('.js-prevent');

                if (targetIsPrevent || closestPrevent.length) e.preventDefault();
            })
        },

        initAutofillFilter() {
            function getHighlightString(props) {
                const
                    { text, replace } = props,
                    from = text.toLowerCase().indexOf(replace.toLowerCase()),
                    to = from + replace.length;

                return from !== -1 ? (
                    text.substring(0, from) +
                    `<span class="highlight">${text.substring(from, to)}</span>` +
                    text.substring(to, text.length)
                ) : false
            }

            function filter(props) {
                const
                    { $relatedInput, $relatedOptions, $autofillNoResults } = props,
                    inputValue = $relatedInput.val();

                let matches = false;

                $autofillNoResults.removeClass('_show');

                $relatedOptions.each((key, item) => {
                    const
                        $currentOption = $(item),
                        dataVal = $currentOption.data('value'),
                        dataName = $currentOption.data('name'),
                        valHighlight = getHighlightString({ text: dataVal, replace: inputValue }),
                        nameHighlight = getHighlightString({ text: dataName, replace: inputValue }),
                        hasMatches = valHighlight || nameHighlight,
                        isHighlighted = $currentOption.data('highlight');

                    if (isHighlighted) $currentOption.html(dataVal).data('highlight', false);

                    if (hasMatches) {
                        matches = true;
                        $currentOption.data('highlight', true);

                        if (valHighlight) $currentOption.html(valHighlight);
                        if (nameHighlight) $currentOption.append(`<span class="option-label">${nameHighlight}</span>`);
                    }

                    $currentOption[hasMatches ? 'removeClass' : 'addClass']('_hide');
                });

                if (!matches) $autofillNoResults.addClass('_show');
            }

            function reset($options) {
                $options.removeClass('_hide');

                $options.each((key, item) => {
                    const
                        $currentOption = $(item),
                        dataVal = $currentOption.data('value');

                    if ($currentOption.data('highlight')) $currentOption.html(dataVal).data('highlight', false);
                });
            }

            $_.$autofillFilter.each((key, item) => {
                const
                    $currentAutofill = $(item),
                    $relatedInput = $currentAutofill.find($_.$autofillInput),
                    $autofillNoResults = $currentAutofill.find($_.$autofillNoResults),
                    $relatedOptions = $currentAutofill.find($_.$autofillOption);

                let timer = null;

                $relatedInput.on('focus click', () => {
                    $currentAutofill.addClass('_active');
                });

                $relatedOptions.on('click', (e) => {
                    const
                        $currentTarget = $(e.currentTarget),
                        dataValue = $currentTarget.data('value');

                    $relatedInput.val(dataValue);
                    $currentAutofill.removeClass('_active');
                    reset($relatedOptions);
                });

                $relatedInput.on('keyup change', () => {
                    clearTimeout(timer);

                    timer = setTimeout(() => {
                        if ($relatedInput.val().length) {
                            filter({ $relatedInput, $relatedOptions, $autofillNoResults })
                        } else {
                            reset($relatedOptions);
                        }
                    }, 500);
                });
            });
        },

        initAutofill() {
            function search(obj) {
                const
                    { $currentAutofill, $relatedOptionsContainer, inputVal, dataRequestOptions } = obj,
                    { action, type, spec } = dataRequestOptions,
                    defaultAjaxParameters = {
                        url: action,
                        type: type,
                        data: {
                            text: inputVal
                        },
                    },
                    specificAjaxParameters = {
                        here: {
                            url: `https://autocomplete.geocoder.ls.hereapi.com/6.2/suggest.json?query=${inputVal}&beginHighlight=<span class="highlight">&endHighlight=</span>&apiKey=r8YkmrDOzfX6spDJx1q3azz9rMoIn7zTSNdWIInUzbM`,
                            type: 'GET',
                        },
                    },
                    useParameters = spec ? specificAjaxParameters[spec] : defaultAjaxParameters;

                $.ajax(useParameters)
                    .done((data) => {
                        //TODO: remove testData
                        const
                            testData = ['option 1', 'option 2', 'option 3', 'option 4', 'option 5', 'option 6', 'option 7'],
                            useData = $.isArray(data) ? data : (data && data.suggestions ? data.suggestions.map(
                                item => item.label
                            ) : testData);

                        addOptions({
                            data: useData,
                            $currentAutofill,
                            $relatedOptionsContainer,
                        });
                    })
                    .fail((err) => {
                        console.log(err);
                    });
            }

            function addOptions(obj) {
                const { data, $currentAutofill, $relatedOptionsContainer } = obj;

                if (data.length) {
                    $relatedOptionsContainer.html(data.map(item => `
                        <span class="autofill-option">${item}</span>
                    `).join(''));

                    $currentAutofill.addClass('_active').data('has-options', true);
                } else {
                    clear({ $currentAutofill, $relatedOptionsContainer, noResults: true });
                }
            }

            function clear(obj) {
                const
                    { $currentAutofill, $relatedOptionsContainer, noResults } = obj,
                    dataNoResultText = $currentAutofill.data('no-result-text');

                $relatedOptionsContainer.html('');

                if (noResults && dataNoResultText) {
                    $relatedOptionsContainer.append(`<span class="autofill-no-results _show">${dataNoResultText}</span>`);
                    $currentAutofill.addClass('_active');
                } else {
                    $currentAutofill.removeClass('_active');
                    $currentAutofill.data('has-options', false);
                }
            }

            $_.$autofill.each((key, item) => {
                const
                    $currentAutofill = $(item),
                    $relatedInput = $currentAutofill.find($_.$autofillInput),
                    $relatedOptionsContainer = $currentAutofill.find($_.$autofillOptionsContainer),
                    dataRequestOptions = $currentAutofill.data('request-options');

                let timer = null,
                    lastVal = null;

                $relatedInput.on('focus click', () => {
                    if ($currentAutofill.data('has-options')) $currentAutofill.addClass('_active');
                });

                $relatedOptionsContainer.on('click', '.autofill-option', (e) => {
                    $relatedInput.val($(e.currentTarget).text());
                    $currentAutofill.removeClass('_active');
                });

                $relatedInput.on('keyup', () => {
                    clearTimeout(timer);

                    timer = setTimeout(() => {
                        const inputVal = $relatedInput.val();

                        if (inputVal.length) {
                            if (lastVal) {
                                if (lastVal !== inputVal) {
                                    search({ $currentAutofill, $relatedOptionsContainer, inputVal, dataRequestOptions });
                                }
                            } else {
                                search({ $currentAutofill, $relatedOptionsContainer, inputVal, dataRequestOptions });
                            }
                        } else {
                            clear({ $currentAutofill, $relatedOptionsContainer });
                        }

                        lastVal = inputVal;
                    }, 1000);
                });
            });
        },

        initKeywordsInput() {
            function pasteKeywords(obj) {
                const { keywords, $keywordsList } = obj;

                $keywordsList.html('');

                keywords.forEach(item => {
                    $keywordsList.append($(`<div class="keyword"><span class="text">${item}</span> <span class="remove"></span></div>`));
                });
            }

            function setValue(obj) {
                const
                    { $input, keywords } = obj,
                    val = keywords.length ? JSON.stringify(keywords) : '';

                $input.attr('value', val).trigger('change');
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
                        text = $relatedKeyword.find('.text').text(),
                        index = keywords.indexOf(text);

                    if (index !== -1) {
                        keywords.splice(index, 1);
                        setValue({$input: $keywordsArray, keywords,});
                        $relatedKeyword.fadeOut(300, () => {
                            $relatedKeyword.remove();
                        });
                    }
                });

                $addKeyword.on('click', () => {
                    const val = $keywordsInsert.val().trim();

                    if (val.length && (keywords.indexOf(val) === -1)) {
                        keywords.push(val);
                        setValue({$input: $keywordsArray, keywords,});
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

        initToggleNext() {
            $_.$toggleNext.on('click', (e) => {
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

            $_.$body.on('body:resize:width', () => {
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

            function getSelectOptionText(obj) {
                const { $btn, name, val } = obj;
                return $btn.find(`[data-name="${name}"]`).find(`[data-value="${val}"]`).data('text');
            }

            function showSelected(data) {
                const
                    { $currentButton, $selectedContainer, $innerFields, dataProps } = data,
                    { patternSimple, patternTwin, patternMulti, patternReplace, patternFixed } = dataProps,
                    values = $innerFields.serializeArray();

                if (patternFixed) {
                    const hasValues = values.map(item => !!item.value.length).indexOf(true) !== -1;
                    $selectedContainer.html(patternFixed);
                    toggleButton($currentButton, hasValues);
                }

                if (patternSimple) {
                    const value = values[0] && values[0].value;
                    if (value) $selectedContainer.html(patternSimple.replace(patternReplace, value));
                    toggleButton($currentButton, !!value);
                }

                if (patternTwin) {
                    const
                        { first, last, both } = patternTwin,
                        { name: fistInputName, value: fistInputValue } = values[0],
                        { name: secondInputName, value: secondInputValue } = values[1];

                    if (fistInputValue && secondInputValue) {
                        $selectedContainer.html(
                            both
                                .replace(patternReplace, getSelectOptionText({
                                    $btn: $currentButton,
                                    name: fistInputName,
                                    val: fistInputValue,
                                }))
                                .replace(patternReplace, getSelectOptionText({
                                    $btn: $currentButton,
                                    name: secondInputName,
                                    val: secondInputValue,
                                }))
                        );
                    } else if (fistInputValue) {
                        $selectedContainer.html(first.replace(patternReplace, getSelectOptionText({
                            $btn: $currentButton,
                            name: fistInputName,
                            val: fistInputValue,
                        })));
                    } else {
                        $selectedContainer.html(last.replace(patternReplace, getSelectOptionText({
                            $btn: $currentButton,
                            name: secondInputName,
                            val: secondInputValue,
                        })));
                    }

                    toggleButton($currentButton, !!(fistInputValue || secondInputValue));
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

            $_.$dropdownButton.each((key, item) => {
                const
                    $currentButton = $(item),
                    $selectedContainer = $currentButton.find($_.$dropdownSelected),
                    $innerFields = $currentButton.find('input, select'),
                    dataProps = $currentButton.data('props');

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

        initMaxWidth() {
            function setMaxWidth($block, $wrap) {
                $block.css('max-width', $wrap.width());
            }

            $_.$maxWidth.each((key, item) => {
                const
                    $currentItem = $(item),
                    $wrap = $currentItem.closest($_.$maxWidthContainer);

                if ($wrap.length) {
                    setMaxWidth($currentItem, $wrap);

                    $_.$body.on('body:resize', () => {
                        setMaxWidth($currentItem, $wrap);
                    });
                }
            });
        },

        initMaxHeight() {
            function setMaxHeight(props) {
                const
                    { $item, dataOffset, stickyOffset, dataMhFromPosition } = props,
                    { top } = $item[0].getBoundingClientRect(),
                    { height: headerHeight } = $_.$header[0].getBoundingClientRect(),
                    totalHeightOffset = dataMhFromPosition ? top : headerHeight + stickyOffset;

                $item.css('max-height', `${$_.windowHeight - totalHeightOffset - dataOffset}px`);
                $_.$body.trigger('body:trigger:init:scrollbars');
            }

            $_.$maxHeight.each((key, item) => {
                const
                    $currentItem = $(item),
                    $parentStickyBlock = $currentItem.closest($_.$stickyBlock),
                    props = {
                        $item: $currentItem,
                        dataOffset: $currentItem.data('offset') || 0,
                        dataMhFromPosition: $currentItem.data('mh-from-position'),
                        stickyOffset: $parentStickyBlock.length ? ($parentStickyBlock.data('offset') || 0) : 0,
                    };

                setMaxHeight(props);

                $_.$body.on('body:resize', () => {
                    setMaxHeight(props);
                });
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

                if (dataInitMap) {
                    $_.$body.trigger('trigger:init-map', dataInitMap);
                    $currentLink.trigger('trigger:click');
                }

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

                if (dataUrl) {
                    $.ajax(requestParameters).done(() => {
                        $currentTarget.toggleClass('_active');
                    })
                }
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
                    dataOffset = $currentStickyBlock.data('offset') || 0,
                    offset = headerHeight + dataOffset;

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
                    $innerInputs = $currentForm.find('input[type="text"]').not($_.selectors.hiddenInput),
                    $parentStickyBlock = $currentForm.closest($_.$stickyBlock);

                $currentForm.on('change', () => {
                    const
                        values = $innerInputs.map((key, item) => item.value.length > 0).toArray(),
                        method = values.indexOf(true) !== -1 ? 'slideDown' : 'slideUp',
                        props = {
                            duration: 300,
                            complete: () => $_.$body.trigger('body:trigger:init:scrollbars'),
                        };

                    if ($parentStickyBlock.length) props.step = () => {
                        $parentStickyBlock.trigger('trigger:update');
                        $_.$body.trigger('body:trigger:init:scrollbars', {
                            preventDestroy: true,
                        });
                    };

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

            $_.$body.on('body:resize:width', () => {
                $_.$showMoreButton.removeClass('_active').prevAll().attr('style', '').data('showed', false);
            });
        },

        initEstateGallerySlider() {
            function initSlider($slider) {
                const
                    dataLazyInner = $slider.data('lazy-inner'),
                    dataScrollNav = $slider.data('scroll-nav'),
                    { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($slider),
                    dots = $_.windowWidth <= $_.breakpoints.b900;

                $slider.on('init', (event, slick) => {
                    if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                    if (dataLazyInner) $_._initSliderLazyInner(slick);
                    if (dataScrollNav) $_._initSliderScrollNav($slider);
                    if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                })
                    .slick({
                        lazyLoad: 'ondemand',
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: !dots,
                        prevArrow: $arrowLeft.eq(0),
                        nextArrow: $arrowRight.eq(0),
                        fade: true,
                        speed: 150,
                        infinite: false,
                        dots: dots,
                    });
            }

            function constructSlider(obj) {
                const
                    { $currentSlider, $imagesCache } = obj,
                    isInit = $currentSlider.hasClass('slick-initialized'),
                    dataImgCount = $currentSlider.data('img-count'),
                    imgCount = getNumberOfSlideInnerImages();

                if (!isInit) {
                    $currentSlider.data('img-count', imgCount);
                    addImgWrappers({ $currentSlider, $imagesCache, imgCount });
                    initSlider($currentSlider);
                } else {
                    if (dataImgCount !== imgCount) {
                        $currentSlider.data('img-count', imgCount);
                        resetSlider($currentSlider);
                        addImgWrappers({ $currentSlider, $imagesCache, imgCount });
                        initSlider($currentSlider);
                    }
                }
            }

            function addImgWrappers(obj) {
                const
                    { $currentSlider, $imagesCache, imgCount } = obj,
                    baseClass = $currentSlider[0].classList[0],
                    slides = [];

                let imgCounter = 0;

                for (let i = 0; i < Math.ceil($imagesCache.length/imgCount); i++) {
                    slides.push(`
                    <div class="${baseClass}__item">
                        <div class="${baseClass}__container">
                            ${$imagesCache.slice(i*imgCount, i*imgCount + imgCount).toArray().map(
                        item => {
                            item.setAttribute('data-index', imgCounter++);
                            return item.outerHTML
                        }
                    ).join('')}
                        </div>
                    </div>
                `)
                }

                $currentSlider.html(slides);
            }

            function resetSlider($slider) {
                $slider.slick('unslick');
                $slider.off('init');
                $slider.off('beforeChange');
                $slider.html('');
            }

            function getNumberOfSlideInnerImages() {
                return $_.windowWidth <= $_.breakpoints.b900 ? 1 : ($_.windowWidth <= $_.breakpoints.b1300 ? 3 : 5);
            }

            $_.$estateGallerySlider.each((key, item) => {
                const
                    $currentSlider = $(item),
                    $imagesCache = $currentSlider.find($_.$estateGallerySliderImg).clone(),
                    fullSizesArray = $imagesCache.map((key, item) => $(item).data('full-size')).toArray();

                $currentSlider.on('click', $_.selectors.estateGallerySliderImg, (e) => {
                    const
                        $currentImg = $(e.currentTarget),
                        index = $currentImg.data('index');

                    $_.$body.trigger('trigger:init-popup-slider', {
                        images: fullSizesArray,
                        index,
                    });
                });

                constructSlider({ $currentSlider, $imagesCache });

                $_.$body.on('body:resize:width', () => {
                    constructSlider({ $currentSlider, $imagesCache });
                });
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
                            dataScrollNav = $currentSlider.data('scroll-nav'),
                            dataSliderParams = $currentSlider.data('slider-parameters'),
                            isInit = $currentSlider.hasClass('slick-initialized'),
                            hasRelatedSlides = $slides && $slides.length && $slides[key];

                        if (hasRelatedSlides) {
                            if (isInit) $currentSlider.slick('unslick');
                            $currentSlider.html($slides[key]);
                        }

                        if (!isInit || (isInit && hasRelatedSlides)) {
                            const
                                dataLazyInner = $currentSlider.data('lazy-inner'),
                                { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);

                            $currentSlider
                                .on('init', (event, slick) => {
                                    $_.$body.trigger('update:lazy-load');
                                    if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                                    if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                                    if (dataScrollNav) $_._initSliderScrollNav($currentSlider);
                                    if (dataLazyInner) $_._initSliderLazyInner(slick);
                                })
                                .slick({
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    arrows: true,
                                    prevArrow: $arrowLeft.eq(0),
                                    nextArrow: $arrowRight.eq(0),
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
                    inlineSvg: (element) => {
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

            $_.$body.on('update:lazy-load', () => {
                setLazyLoad();
            });

            setLazyLoad();
        },

        initDefaultSlider() {
            $_.$defaultSlider.each((key, item) => {
                const
                    $currentSlider = $(item),
                    $estateCard = $currentSlider.find($_.$estateCard),
                    $preventChild = $currentSlider.find('[data-prevent-parent-swipe]'),
                    dataScrollNav = $currentSlider.data('scroll-nav'),
                    dataLazyInner = $currentSlider.data('lazy-inner'),
                    dataParameters = $currentSlider.data('slider-parameters') || {},
                    { $arrowLeft, $arrowRight, $current, $total } = $_._getRelatedSliderNav($currentSlider);

                $currentSlider
                    .on('init', (event, slick) => {
                        $_.$body.trigger('update:lazy-load');
                        if ($current.length && $total.length) $_._initSliderCounter(slick, $current, $total);
                        if ($preventChild.length) $_._preventParentSliderSwipe($currentSlider, $preventChild);
                        if (slick.$dots) $_._initSliderDotsNav({slick, dotsCount: 5});
                        if (dataScrollNav) $_._initSliderScrollNav($currentSlider);
                        if (dataLazyInner) $_._initSliderLazyInner(slick);
                    })
                    .slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        prevArrow: $arrowLeft.eq(0),
                        nextArrow: $arrowRight.eq(0),
                        fade: false,
                        infinite: false,
                        dots: false,
                        ...dataParameters
                    });
            });
        },

        _initSliderScrollNav($slider) {
            $slider.on('mousewheel', (e) => {
                e.stopPropagation();
                e.preventDefault();

                const
                    { wheelDelta, deltaY, detail } = e.originalEvent,
                    scrollUp = (wheelDelta > 0) || (detail < 0) || (deltaY < 0),
                    sliderSwitchDirection = scrollUp ? 'slickPrev' : 'slickNext';

                $slider.slick(sliderSwitchDirection);
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
            function setScrollBars(data) {
                const
                    $scrollbar = $($_.selectors.smoothScroll),
                    { preventDestroy } = data || {};

                $scrollbar.each((key, item) => {
                    const
                        $item = $(item),
                        $breakpointDetect = $item.find('.js-bp-detect'),
                        dataInitIfOverflow = $item.data('init-if-overflow'),
                        isInit = Scrollbar.has(item);

                    if (dataInitIfOverflow) {
                        const { clientHeight, scrollHeight } = item;

                        if (scrollHeight > clientHeight) {
                            init(item, isInit);
                        } else {
                            if (isInit && !preventDestroy) destroy($item);
                        }
                    } else {
                        if ($breakpointDetect.length) {
                            if ($breakpointDetect.eq(0).is(':hidden')) {
                                init(item, isInit);
                            } else {
                                if (isInit) {
                                    destroy($item);
                                }
                            }
                        } else {
                            init(item, isInit);
                        }
                    }
                });
            }

            function destroy($item) {
                $item.off('trigger:scroll-top');
                $item.off('trigger:update-scroll');
                $item.removeClass('_scroll-initialized');
                Scrollbar.destroy($item[0])
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

                    $item.addClass('_scroll-initialized');

                    $item.on('trigger:scroll-top', () => {
                        scrollbar.scrollTop = 0;
                    });

                    $item.on('trigger:update-scroll', () => {
                        Scrollbar.get(item).update();
                    });
                }
            }

            $_.$body.on('body:resize', () => {
                setScrollBars();
            });

            $_.$body.on('body:trigger:init:scrollbars', (e, data) => {
                setScrollBars(data);
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
                const
                    windowHeight = $_.$window.height(),
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

            $_.$scrollTop.on('click', () => $_._scroll(0));
            $_.$window.scroll(() => check());

            check();
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

                    $targetsToClose.removeClass('_active');
                }
            });
        },

        initResizeTrigger () {
            const resizeDelay = 300;

            let resizeTimer = null,
                windowWidth = window.outerWidth,
                windowHeight = $_.$window.height();

            $_.$window.resize(() => {
                clearTimeout(resizeTimer);

                resizeTimer = setTimeout(() => {
                    const
                        currentWidth = window.outerWidth,
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
            const
                stopScrollingDelay = 100,
                whileScrollingDelay = 200;

            let checkIsReady = true,
                scrollTimer = null;

            $_.$window.scroll(() => {
                checkTimer();

                if (!$_.mapIsInit && $_.$map.length) $_.$map.trigger('trigger:check');
            });

            function callTimerFunctions() {
                $_._checkInWindow();
            }

            function checkTimer() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    callTimerFunctions();
                }, stopScrollingDelay);

                if (checkIsReady) {
                    checkIsReady = false;
                    callTimerFunctions();

                    setTimeout(() => {
                        checkIsReady = true;
                    }, whileScrollingDelay)
                }
            }

            setTimeout(() => {
                checkTimer();
            }, 1000);

            $_.$map.on('trigger:is-init', () => {
                $_.mapIsInit = true;
            });
        },

        initMenu() {
            $_.$menuBtn.on('click', () => {
                $_.$header.toggleClass('_active');
            })
        },

        initNavLinks () {
            $_.$navLink.on('click', () => {
                e.preventDefault();

                const
                    $el = $(e.currentTarget),
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
                slick.$dots.data('isInit', true).wrap('<div class="slider-dots-nav js-prevent"></div>');

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
            $_.$checkInWindow.each((key, item) => {
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
            $child.on('touchstart mousedown', () => {
                $parent.slick('slickSetOption', 'swipe', false, false);
            });

            $child.on('touchend mouseup mouseout', () => {
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
            $current.html(slick.currentSlide + 1);
            $total.html(slick.slideCount);

            slick.$slider.on('beforeChange', (event, slick, currentSlide, nextSlide) => {
                $current.html(nextSlide + 1)
            });
        },
    };

    $(document).ready(() => {
        $_.init();
    });
});
