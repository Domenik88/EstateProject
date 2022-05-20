jQuery(function($){
    const mc_ = {
        init(){
            this.initCache();
            this.initChart();
            this.initEvents();

            mc_._collectFormValues();
            mc_._calcMortgage();
        },

        initCache() {
            this.chartSelector = '.js-ct-chart';
            this.chart = null;

            this.$form = $('.js-mortgage-calc-form');
            this.$calcTotal = $('.js-calc-total');

            this.$homePriceInput = $('.js-home-price');
            this.$downPaymentInput = $('.js-down-payment');
            this.$downPaymentPercentInput = $('.js-down-payment-percent');

            this.formValues = {};
        },

        initEvents() {
            mc_.$form.on('change', () => {
                mc_._collectFormValues();
                mc_._calcMortgage();
            });

            mc_.$homePriceInput.on('change', () => {
                mc_._collectFormValues();

                const
                    { home_price } = mc_.formValues,
                    downPaymentInputFormatProps = mc_.$downPaymentInput.data('format-props');

                downPaymentInputFormatProps.max = home_price;

                mc_.$downPaymentInput.attr('data-format-props', JSON.stringify(downPaymentInputFormatProps))

                mc_.$downPaymentInput.trigger('trigger:set-val', {
                    val: mc_._getDownPaymentPrice()
                });

            });

            mc_.$downPaymentInput.on('change', () => {
                mc_._collectFormValues();

                mc_.$downPaymentPercentInput.trigger('trigger:set-val', {
                    val: mc_._getDownPaymentPercent(),
                    change: true,
                });

            });

            mc_.$downPaymentPercentInput.on('change', () => {
                mc_._collectFormValues();

                mc_.$downPaymentInput.trigger('trigger:set-val', {
                    val: mc_._getDownPaymentPrice()
                });
            });
        },

        _getDownPaymentPrice() {
            const { home_price, down_payment_percent } = mc_.formValues;
            return Math.round(home_price * (down_payment_percent / 100));
        },

        _getDownPaymentPercent() {
            const { home_price, down_payment } = mc_.formValues;
            return  Math.round((down_payment / home_price) * 100);
        },

        _calcMortgage() {
            const
                { home_price, down_payment, interest_rate, loan_type } = mc_.formValues,
                p = home_price - down_payment,
                r = interest_rate / 100 / 12,
                n = loan_type * 12,
                principalAndInterest = Math.round(p * (r*(1+r)**n) / ((1 + r)**n - 1));

            mc_.$calcTotal.html(_formatCurrencyCa(principalAndInterest));
            mc_._updateDonut([principalAndInterest, 202, 75, 513]);
        },

        _updateDonut(series) {
            mc_.chart.update({series: series});
        },

        _collectFormValues() {
            mc_.$form.serializeArray().map(item => {
                mc_.formValues[item.name] = _getPureNumber(item.value);
            });

        },

        initChart() {
            mc_.chart = new Chartist.Pie(mc_.chartSelector, {
                series: [16877, 202, 75, 513],
            }, {
                donut: true,
                donutWidth: 40,
                donutSolid: true,
                startAngle: 0,
                showLabel: false,
            });
        }
    };

    $(document).ready(function() {
        if ($('.js-ct-chart').length) mc_.init();
    });
});
