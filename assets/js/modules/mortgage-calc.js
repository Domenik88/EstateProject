jQuery(function($){
    const $_ = {
        init(){
            this.initCache();
            this.initChart();
            this.initEvents();

            $_._collectFormValues();
            $_._calcMortgage();
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
            $_.$form.on('change', () => {
                $_._collectFormValues();
                $_._calcMortgage();
            });

            $_.$homePriceInput.on('change', () => {
                $_._collectFormValues();

                const
                    { home_price } = $_.formValues,
                    downPaymentInputFormatProps = $_.$downPaymentInput.data('format-props');

                downPaymentInputFormatProps.max = home_price;

                $_.$downPaymentInput.attr('data-format-props', JSON.stringify(downPaymentInputFormatProps))

                $_.$downPaymentInput.trigger('trigger:set-val', {
                    val: $_._getDownPaymentPrice()
                });

            });

            $_.$downPaymentInput.on('change', () => {
                $_._collectFormValues();

                $_.$downPaymentPercentInput.trigger('trigger:set-val', {
                    val: $_._getDownPaymentPercent(),
                    change: true,
                });

            });

            $_.$downPaymentPercentInput.on('change', () => {
                $_._collectFormValues();

                $_.$downPaymentInput.trigger('trigger:set-val', {
                    val: $_._getDownPaymentPrice()
                });
            });
        },

        _getDownPaymentPrice() {
            const { home_price, down_payment_percent } = $_.formValues;
            return Math.round(home_price * (down_payment_percent / 100));
        },

        _getDownPaymentPercent() {
            const { home_price, down_payment } = $_.formValues;
            return  Math.round((down_payment / home_price) * 100);
        },

        _calcMortgage() {
            const
                { home_price, down_payment, interest_rate, loan_type } = $_.formValues,
                p = home_price - down_payment,
                r = interest_rate / 100 / 12,
                n = loan_type * 12,
                principalAndInterest = Math.round(p * (r*(1+r)**n) / ((1 + r)**n - 1));

            $_.$calcTotal.html(_formatCurrencyCa(principalAndInterest));
            $_._updateDonut([principalAndInterest, 202, 75, 513]);
        },

        _updateDonut(series) {
            $_.chart.update({series: series});
        },

        _collectFormValues() {
            $_.$form.serializeArray().map(item => {
                $_.formValues[item.name] = _getPureNumber(item.value);
            });

        },

        initChart() {
            $_.chart = new Chartist.Pie($_.chartSelector, {
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

    $(document).ready(() => {
        if ($('.js-ct-chart').length) $_.init();
    });
});
