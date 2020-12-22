jQuery(function($){
    const rs_ = {
        init(){
            this.initCache();
            this.initRange();
        },

        initCache() {
            this.$range = $('.js-range');
        },

        initRange(){
            rs_.$range.each((key, item) => {
                const 
                    $item = $(item),
                    { relativeInputName, trigger, options={} } = $item.data('params') || {},
                    $relativeInput = $(`input[name="${relativeInputName}"]`),
                    { range, start, min, max } = options;

                $item.slider({
                    range: range,
                    min: min,
                    max: max,
                    start: start,
                    create: () => {
                        $item.slider("value", _getPureNumber($relativeInput.val()));
                    },
                    slide: (event, ui) => {
                        $relativeInput.trigger('trigger:set-val', {val: ui.value, change: true});
                    }
                });

                $relativeInput.on('change', () => {
                    $item.slider("value", _getPureNumber($relativeInput.val()));
                });

                $item.on('trigger:set-value', (e, val) => {
                    $item.slider("value", val);
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
        rs_.init();
    });
});
