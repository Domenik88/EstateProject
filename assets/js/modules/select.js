jQuery(function($) {
    var sel = {
        init() {
            this.init_cache();
            this.events();
            this.init_selects();
        },
        
        init_cache() {
            this.$body = $('body');
            this.$module = $(".js-select-module");
            this.$select = $(".js-select-module-select");
            this.$container = $(".js-select-module-container");
            this.$opt_container = $(".js-select-module-options");
            this.$btn = $(".js-select-module-text-block");
        },
        
        events() {
            this.$container.on('click', (e) => {
                const
                    $el = $(e.currentTarget),
                    $relatedModule = $el.closest(sel.$module);
                
                $relatedModule.toggleClass("_active");
            });
        },
        
        init_selects() {
            this.$module.each((key, item) => {
                const
                    $item = $(item),
                    $select = $item.find(sel.$select),
                    $opt_container = $item.find(sel.$opt_container),
                    $btn = $item.find(sel.$btn);
                
                sel.build_options($select, $opt_container, $btn);
                sel.add_events($item, $btn, $select);
            });
        },
        
        build_options($select, $opt_container, $btn) {
            let $options = $select.find("option");
            
            for (let i = 0; i < $options.length; i++) {
                const
                    $item = $($options[i]),
                    isDisabled = $item.is(':disabled'),
                    isSelected = $item.is(':selected'),
                    selectedClass = isSelected ? "_active" : "",
                    value = $item.val(),
                    title = $item.text();
                
                if (!isDisabled) {
                    $opt_container.append(`
                        <div class="select-module__option js-select-module-option ${selectedClass}" data-value="${value}">
                            ${title}
                        </div>
                    `);
                }
                
                if (isSelected) {
                    $select.closest(sel.$module).addClass('_selected');
                    $btn.text(title);
                }

                // if (isSelected) {
                //     if (value && value.length) {
                //         $select.closest(sel.$module).addClass('_selected');
                //         $btn.text(title);
                //     }
                // }
            }
    
            sel.$body.trigger('trigger:init-scrollbar', {
                el: $opt_container[0]
            })
        },
        
        add_events($item, $btn, $select) {
            const $options = $item.find(".js-select-module-option");
            
            $options.on("click", (e) => {
                const
                    $opt = $(e.currentTarget),
                    value = $opt.data("value"),
                    title = $opt.text();
                
                $options.add($item).removeClass("_active");
                $opt.addClass("_active");
                
                $item.addClass('_selected');
                $btn.text(title);
                $select.val(value);
    
                $select.trigger('change');
                if ($select.closest('form').length) $select.valid();
            });
        }
    };

    $(document).ready(function() {
        sel.init();
    });
});
