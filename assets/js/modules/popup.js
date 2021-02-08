jQuery(function($){
    const $_ = {
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
            $_.$btn_close.on('click', () => {
                $_.closePopup();
            });
            
            $_.$overlay.on('click', () => {
                $_.closePopup();
            });
            
            $_.$body.on('click', '.js-call-popup', (e) => {
                e.preventDefault();
                $_._clickHandler(e);
            });
            
            $_.$body.on('show:ty-popup', (e, data, delay) => {
                $_._showTyPopup(data, delay);
            });
            
            $_.$body.on('trigger:init-popup-slider', (e, data) => {
                $_._initPopupSlider(data);
            });
        },
        
        _clearOverlay() {
            $_.$overlay.removeClass($_.overlayMods.join(' '));
        },
    
        _initPopupSlider(data) {
            const
                { images, index } = data,
                $popup = $('.js-slider-popup'),
                $popupSlider = $popup.find('.js-slider'),
                slides = images.map(item => $_.templates.estateSliderItem(item)).join('');

            $_.$body.trigger('trigger:init-slider', {
                $sliders: $popupSlider,
                $slides: [slides],
                sliderParams: {
                    initialSlide: index,
                    infinite: true,
                    speed: 150,
                    fade: true,
                }
            });

            $_.$popups.removeClass('_active');
            $_._clearOverlay();
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
            
            $_.$popups.removeClass('_active');
            
            $title.html(title || titleDefault);
            $subtitle.html(subtitle || subtitleDefault);
            
            setTimeout(() => {
                $_._clearOverlay();
                $popup.add($_.$overlay).addClass('_active');
            }, showDelay);
            
            setTimeout(() => {
                if ($popup.hasClass('_active')) $popup.add($_.$overlay).removeClass('_active');
            }, hideDelay);
        },
        
        _clickHandler(e) {
            const
                $btn = $(e.currentTarget),
                { target, fire_click_selector, show_overlay } = $btn.data('popup'),
                $popup = $('.js-popup-' + target),
                $recaptcha = $popup.find('.js-recaptcha');

            if ($recaptcha.length) $_.$body.trigger('trigger:init-recaptcha');

            $_.$popups.removeClass('_active');
            $_._clearOverlay();

            if (fire_click_selector) {
                $popup.find(fire_click_selector).click();
            }
            
            $popup.addClass('_active');
            if (show_overlay) $_.$overlay.addClass('_active');
        },
        
        closePopup() {
            const
                $popup_active = $('.js-popup._active'),
                $inputForClearing = $popup_active.find('.js-clear-on-close');
            
            $_.$overlay.removeClass('_active');
            $popup_active.removeClass('_active');
            
            if ($inputForClearing.length) $inputForClearing.attr('value', '');
        }
    };
    
    $(document).ready(() => {
        $_.init();
    });
});