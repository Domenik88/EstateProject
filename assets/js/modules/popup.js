'use strict'
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
                    <div class="estate-slider-item">
                        <img src=${img} alt="#" class="of"/>
                    </div>
                `,
                estateSliderDescription: ({title, text}) => `
                    <div class="estate-slider-description">
                        <h1>${title}</h1>
                        <p>${text}</p>
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
            
            $('.js-call-popup').on('click', (e) => {
                e.preventDefault();
                popup._clickHandler(e);
            });
            
            popup.$body.on('show:ty-popup', (e, data, delay) => {
                popup._showTyPopup(data, delay);
            });
            
            popup.$body.on('trigger:show-estate-popup', (e, data) => {
                popup._showEstatePopup(data);
            });
        },
        
        _clearOverlay() {
            popup.$overlay.removeClass(popup.overlayMods.join(' '));
        },
    
        _showEstatePopup(data) {
            const
                { images } = data,
                $popup = $('.js-popup-estate'),
                $popupSlider = $popup.find('.js-estate-popup-slider'),
                $popupDescription = $popup.find('.js-estate-popup-description');
    
            $popupDescription.html(popup.templates.estateSliderDescription(data));
    
            popup.$body.trigger('trigger:init-slider', {
                $sliders: $popupSlider,
                $slides: [images.map((item) => popup.templates.estateSliderItem(item)).join('')],
            });
            
            popup.$popups.removeClass('_active');
            popup._clearOverlay();
            $popup.add(popup.$overlay).addClass('_active');
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
                { target, overlay_mod } = $btn.data('popup'),
                $popup = $('.js-popup-' + target),
                $recaptcha = $popup.find('.js-recaptcha');

            $('body').trigger('trigger:init-map', '#test-popup-map')

            if ($recaptcha.length) popup.$body.trigger('trigger:init-recaptcha');

            popup._clearOverlay();
            
            if (overlay_mod) {
                if (popup.overlayMods.indexOf(overlay_mod) === -1) popup.overlayMods.push(overlay_mod);
                popup.$overlay.addClass(overlay_mod);
            }
            
            $popup.add(popup.$overlay).addClass('_active');
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