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
                    { range, start, value } = options;

                $item.slider({
                    range: range,
                    min: value[0],
                    max: value[1],
                    create: () => {
                        $item.slider("value", _getPureNumber($relativeInput.val()));
                    },
                    slide: (event, ui) => {
                        $relativeInput.trigger('trigger:set-val', ui.value);
                    }
                });

                $relativeInput.on('change', () => {
                    $item.slider("value", _getPureNumber($relativeInput.val()));
                });

                $item.on('trigger:set-value', (e, val) => {
                    $item.slider("value", val);
                })
            });
        },
    };

    $(document).ready(function() {
        rs_.init();
    });
});
