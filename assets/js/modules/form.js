'use strict'
jQuery(function($) {
    
    window.form_adjuster = {
        init: function(options) {
            this.options = options;
            this.init_cache();
            this.init_validation();
            
            if (this.options['file']) {
                this.check_file();
                this.input_file_reset();
            }
            
            this.send_validation();
        },
        
        init_options: function() {
            const
                default_settings = {
                    'type': 'POST',
                    'handler': window.send_mail_url || './form-handler.php',
                    'dataType': 'json',
                    'contentType': false,
                    'processData': false,
                    'file': false,
                    'onload': false,
                    'success': false,
                    'error': false
                };
            return this.options ? $.extend(default_settings, this.options) : default_settings
        },
        
        init_cache: function(options) {
            this.options = this.init_options();
            this.$input_phone = $('.userphone');
            this.$form = $('.js-ajax-form');
            this.$file_input = $('.js-file-input');
        },
        
        init_validation: function() {
            $.validator.addMethod(
                'regexp',
                function(value, element, regexp) {
                    var re = new RegExp(regexp);
                    return this.optional(element) || re.test(value);
                },
                "Please check your input."
            );
            
            $.validator.addClassRules({
                userphone: {
                    required: true
                },
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
        
        form_send: function(formObject, action) {
            var settings = form_adjuster.options;
            
            $.ajax({
                type: settings['type'],
                url: settings['handler'],
                dataType: settings['dataType'],
                contentType: settings['contentType'],
                processData: settings['processData'],
                data: formObject,
                success: function() {
                    if (settings['success']) {
                        settings['success']();
                    } else {
                        form_adjuster.ajax_success();
                    }
                    
                    form_adjuster.$form_cur = false;
                },
                error: function() {
                    if (settings['error']) {
                        settings['error']();
                    } else {
                        form_adjuster.ajax_error();
                    }
                    
                    form_adjuster.$form_cur = false;
                }
            });
        },
        
        ajax_success: function() {
            if (form_adjuster.$form_cur) {
                form_adjuster.$form_cur.trigger('reset');
            }
        },
        
        ajax_error: function() {
            console.log('error');
        },
        
        formData_assembly: function(form) {
            var formSendAll = new FormData(),
                formdata = {},
                form_arr,
                $allFields = $(form).find('input,select,textarea'),
                $typeHiddenFields = $allFields.filter('[type="hidden"]'),
                $fieldsToAssembly = $allFields.filter(':visible').add($typeHiddenFields),
                $captcha = $allFields.filter(".g-recaptcha-response").closest(".js-recaptcha");
            
            form_arr = $fieldsToAssembly.serializeArray();
            
            if ($captcha.length) {
                const
                    dataKey = $captcha.data("key"),
                    response = grecaptcha ? grecaptcha.getResponse(dataKey) : false;
                
                if (response) {
                    form_arr["g-recaptcha-response"] = response;
                } else {
                    $captcha.addClass("_error");
                    setTimeout(function() {
                        $captcha.removeClass("_error");
                    }, 2000);
                    return false;
                }
            }
            
            for (var i = 0; i < form_arr.length; i++) {
                if (form_arr[i].value.length > 0) {
                    
                    var $current_input = $fieldsToAssembly.filter('[name=' + form_arr[i].name + ']'),
                        value_arr = {};
                    
                    if ($current_input.attr('type') !== 'hidden') {
                        var title = $current_input.attr('data-title');
                        
                        if (!formdata[form_arr[i].name]) {
                            value_arr['value'] = form_arr[i].value;
                            value_arr['title'] = title;
                            formdata[form_arr[i].name] = value_arr;
                        } else {
                            if (typeof formdata[form_arr[i].name].value === 'string') {
                                const currentVal = formdata[form_arr[i].name].value;
                                formdata[form_arr[i].name].value = [];
                                formdata[form_arr[i].name].value.push(currentVal);
                            } else {
                                formdata[form_arr[i].name].value.push(form_arr[i].value);
                            }
                        }
                    } else {
                        formSendAll.append(form_arr[i].name, form_arr[i].value);
                    }
                }
            }
            
            formSendAll.append('formData', JSON.stringify(formdata));
            
            if (form_adjuster.options['file']) {
                var $input_file = $(form).find('.js-file-input');
                
                if ($input_file.length > 0) {
                    $input_file.each(function() {
                        var $input_cur = $(this),
                            val_length = $input_cur.val().length,
                            multy = $input_cur.prop('multiple');
                        
                        if (val_length > 0) {
                            if (!multy) {
                                formSendAll.append($input_cur.attr('name'), $input_cur[0].files[0]);
                            } else {
                                form_adjuster.collect_multiple_file(formSendAll, $input_cur);
                            }
                        }
                    })
                }
            }
            
            this.form_send(formSendAll, false);
        },
        
        collect_multiple_file: function(data, $input) {
            $('.js-file-list li').each(function() {
                var file_name = $(this).attr('data-name');
                
                for (var i = 0; i < $input[0].files.length; i++) {
                    if (file_name === $input[0].files[i].name) {
                        data.append($input.attr('name'), $input[0].files[i]);
                    }
                }
            })
        },
        
        check_file: function() {
            function errorHandler(evt) {
                switch (evt.target.error.code) {
                    case evt.target.error.NOT_FOUND_ERR:
                        alert('File Not Found!');
                        break;
                    case evt.target.error.NOT_READABLE_ERR:
                        alert('File is not readable');
                        break;
                    case evt.target.error.ABORT_ERR:
                        break; // noop
                    default:
                        alert('An error occurred reading this file.');
                }
            }
            
            function handleFileSelect() {
                var $input = $(this);
                
                for (var i = 0; i < $input[0].files.length; i++) {
                    reader_file($input[0].files[i], $input);
                }
            }
            
            function reader_file(file, $input) {
                var reader = new FileReader(),
                    file_name = file.name,
                    $wrapper = $input.closest('.js-upload-wrapper'),
                    $name = $wrapper.find('.js-filename');
                
                $name.addClass('_load').text(file_name);
                
                reader.onerror = errorHandler;
                
                reader.onabort = function(e) {
                    alert('File read cancelled');
                };
                
                reader.onload = function(e) {
                    if (form_adjuster.options['onload']) {
                        var obj = {
                            file: file,
                            $input: $input
                        };
                        
                        form_adjuster.options['onload'](obj);
                    }
                };
                
                reader.readAsDataURL(file);
            }
            
            form_adjuster.$file_input.on('change', handleFileSelect);
        },
        
        input_file_reset: function() {
            form_adjuster.$reset = $('.js-file-reset');
            
            $(document).on('click', form_adjuster.$reset, function() {
                var $btn = $(this),
                    $wrapper = $btn.closest('.js-upload-wrapper'),
                    $input = $wrapper.find('.js-file-input'),
                    $title = $wrapper.find('.js-filename');
                
                $title.removeClass('_loaded').text('');
                $input.replaceWith($input.val('').clone(true));
            })
        },
        
        send_validation: function() {
            this.$form.each(function() {
                $(this).validate({
                    errorPlacement: function(error, element) {
                        error.remove();
                    },
                    submitHandler: function(form) {
                        form_adjuster.$form_cur = $(form);
                        
                        form_adjuster.formData_assembly(form);
                    }
                })
            });
        }
    };
});