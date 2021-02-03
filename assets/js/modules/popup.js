jQuery(function($){
    const popup = {
        init() {
            this.initCache();
            this.events();
        },
        
        initCache() {
            this.$body = $('body');
            this.$popups = $('.js-popup');
            this.$overlay = $('.js-overlay');
            this.$btn_close = $('.js-close-popup');
            this.overlayMods = [];
    
            this.templates = {
                estateSliderItem: (img) => `
                    <div class="fs-slider-item">
                        <div class="round-img-wrap">
                            <img src=${img} alt="#" class="of"/>
                        </div>
                    </div>
                `,
            }
        },
        
        events() {
            popup.$btn_close.on('click', () => {
                popup.closePopup();
            });
            
            popup.$overlay.on('click', () => {
                popup.closePopup();
            });
            
            popup.$body.on('click', '.js-call-popup', (e) => {
                e.preventDefault();
                popup._clickHandler(e);
            });
            
            popup.$body.on('show:ty-popup', (e, data, delay) => {
                popup._showTyPopup(data, delay);
            });
            
            popup.$body.on('trigger:init-popup-slider', (e, data) => {
                popup._initPopupSlider(data);
            });
        },
        
        _clearOverlay() {
            popup.$overlay.removeClass(popup.overlayMods.join(' '));
        },
    
        _initPopupSlider(data) {
            const
                { images, index } = data,
                $popup = $('.js-slider-popup'),
                $popupSlider = $popup.find('.js-slider'),
                slides = images.map(item => popup.templates.estateSliderItem(item)).join('');

            popup.$body.trigger('trigger:init-slider', {
                $sliders: $popupSlider,
                $slides: [slides],
                sliderParams: {
                    initialSlide: index,
                    infinite: true,
                    speed: 150,
                    fade: true,
                }
            });

            popup.$popups.removeClass('_active');
            popup._clearOverlay();
            $popup.addClass('_active');
        },
    
        _showTyPopup(data, delay) {
            const
                { target, tyText } = data,
                { title, subtitle } = tyText || {},
                $popup = $('.js-popup-' + target),
                $title = $popup.find('.js-ty-title'),
                $subtitle = $popup.find('.js-ty-subtitle'),
                titleDefault = $title.data('default-text'),
                subtitleDefault = $subtitle.data('default-text'),
                showDelay = delay || 500,
                hideDelay = showDelay + 7000;
            
            popup.$popups.removeClass('_active');
            
            $title.html(title || titleDefault);
            $subtitle.html(subtitle || subtitleDefault);
            
            setTimeout(function () {
                popup._clearOverlay();
                $popup.add(popup.$overlay).addClass('_active');
            }, showDelay);
            
            setTimeout(function () {
                if ($popup.hasClass('_active')) $popup.add(popup.$overlay).removeClass('_active');
            }, hideDelay);
        },
        
        _clickHandler(e) {
            const
                $btn = $(e.currentTarget),
                { target, fire_click_selector, show_overlay } = $btn.data('popup'),
                $popup = $('.js-popup-' + target),
                $recaptcha = $popup.find('.js-recaptcha');

            if ($recaptcha.length) popup.$body.trigger('trigger:init-recaptcha');

            popup.$popups.removeClass('_active');
            popup._clearOverlay();

            if (fire_click_selector) {
                $popup.find(fire_click_selector).click();
            }
            
            $popup.addClass('_active');
            if (show_overlay) popup.$overlay.addClass('_active');
        },
        
        closePopup() {
            const
                $popup_active = $('.js-popup._active'),
                $inputForClearing = $popup_active.find('.js-clear-on-close');
            
            popup.$overlay.removeClass('_active');
            $popup_active.removeClass('_active');
            
            if ($inputForClearing.length) $inputForClearing.attr('value', '');
        }
    };
    
    $(document).ready(() => {
        popup.init();
    });
});