jQuery(function($){
    const $_ = {
        init() {
            this.initCache();
            this.initValidation();
            this.initResetButton();
            this.sendValidation();
        },

        initCache() {
            this.$form = $('.js-ajax-form');
            this.$resetButton = $('.js-form-reset-button');

            this.options = {
                'type': 'POST',
                'handler': '',
                'contentType': false,
                'processData': false,
                'onload': false,
                'success': false,
                'error': false,
            }
        },

        initResetButton() {
            $_.$resetButton.on('click', (e) => {
                const
                    $currentTarget = $(e.currentTarget),
                    $relatedForm = $currentTarget.closest('form'),
                    $relatedSelectModules = $relatedForm.find('.js-select-module');

                $relatedSelectModules.each((key, item) => {
                    $(item).find('.js-select-module-option').eq(0).click();
                });

                $relatedForm.trigger('reset');
            });
        },

        initValidation() {
            $.validator.addMethod(
                'regexp',
                (value, element, regexp) => {
                    const re = new RegExp(regexp);
                    return this.optional(element) || re.test(value);
                },
                "Please check your input."
            );

            $.validator.addClassRules({
                usermail: {
                    email: true
                },
                required: {
                    required: true
                },
                password:{
                    minlength:6
                },
                passwordConfirm:{
                    minlength:6,
                    equalTo:'.js-input-new-password'
                }
            });
        },

        sendValidation() {
            this.$form.each((key, item) => {
                $(item).validate({
                    errorPlacement: (error) => {
                        error.remove();
                    },
                    submitHandler: (form) => {
                        $_.$form_cur = $(form);
                        $_._sendForm(form);
                    }
                })
            });
        },

        _sendForm(form) {
            const
                { type, handler, contentType, processData } = $_.options,
                $form = $(form),
                action = $form.attr('action'),
                method = $form.attr('method'),
                data = new FormData(form);

            $.ajax({
                type: method || type,
                url: action || handler,
                contentType: contentType,
                processData: processData,
                data: data
            })
            .done(() => {
                $_._successHandler();
            })
            .error((err) => {
                $_._errorHandler(err);
            })
            .always(() => {
                $_.$form_cur = false;
            });
        },

        _errorHandler(err) {
            console.log(err);
        },

        _successHandler() {
            const
                $form = $($_.$form_cur),
                $parentScroll = $form.closest('.js-smooth-scroll'),
                $input = $form.find('input');

            setTimeout(() => {
                $form.trigger('reset').find('._active, .valid, .error').removeClass('_active valid error');
                $input.removeClass('_active');
            }, 500);

            $form.addClass('_ty');
            if ($parentScroll.length) $parentScroll.trigger('trigger:update-scroll');
        }
    };

    $(document).ready(() => {
        $_.init();
    });
});
