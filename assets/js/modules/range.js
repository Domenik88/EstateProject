jQuery(function($){
    const $_ = {
        init(){
            this.initCache();
            this.initRange();
        },

        initCache() {
            this.$range = $('.js-range');
        },

        _normalizeNum(num) {
            return num.toFixed(2) * 1000 / 1000;
        },

        _setSliderVal($slider, $relativeInput, mlt) {
            const fixMlt = $_._normalizeNum((1 / mlt) * _getPureNumber($relativeInput.val()));
            $slider.slider("value", fixMlt);
        },

        initRange(){
            $_.$range.each((key, item) => {
                const 
                    $item = $(item),
                    { relativeInputName, trigger, options={} } = $item.data('params') || {},
                    $relativeInput = $(`input[name="${relativeInputName}"]`),
                    { mlt=1 } = $relativeInput.data('format-props') || {},
                    fixMlt = mlt || 1,
                    { range, start, min, max } = options;

                $item.slider({
                    range: range,
                    min: min,
                    max: max,
                    start: start,
                    create: () => {
                        $_._setSliderVal($item, $relativeInput, fixMlt);
                    },
                    slide: (event, ui) => {
                        $relativeInput.trigger('trigger:set-val', {
                            val: $_._normalizeNum(ui.value * fixMlt),
                            change: true
                        });
                    }
                });

                $relativeInput.on('change', () => {
                    $_._setSliderVal($item, $relativeInput, fixMlt);
                });

                $relativeInput.on('trigger:set-range-props', (e, data) => {
                    const {
                        sliderOptions
                    } = data;

                    $item.slider( "option", {
                        ...sliderOptions
                    });
                });
            });
        },
    };

    $(document).ready(function() {
        $_.init();
    });
});
