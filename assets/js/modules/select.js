jQuery(function($) {
    const $_ = {
        init() {
            this.initCache();
            this.initEvents();
            this.initSelects();
        },
        
        initCache() {
            this.$body = $('body');
            this.$module = $('.js-select-module');
            this.$select = $('.js-select-module-select');
            this.$container = $('.js-select-module-container');
            this.$optContainer = $('.js-select-module-options');
            this.$btn = $('.js-select-module-text-block');
        },

        initEvents() {
            $_.$container.on('click', (e) => {
                const
                    $el = $(e.currentTarget),
                    $relatedModule = $el.closest($_.$module);
                
                $relatedModule.toggleClass("_active");
            });
        },
        
        initSelects() {
            $_.$module.each((key, item) => {
                const
                    $item = $(item),
                    $select = $item.find($_.$select),
                    $optContainer = $item.find($_.$optContainer),
                    $btn = $item.find($_.$btn);
                
                $_._buildOptions($select, $optContainer, $btn);
                $_._addEvents($item, $btn, $select);
            });
        },
        
        _buildOptions($select, $optContainer, $btn) {
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
                    $optContainer.append(`
                        <div class="select-module__option js-select-module-option ${selectedClass}" 
                            data-value="${value}"
                            data-text="${title}"
                        >
                            ${title}
                        </div>
                    `);
                }
                
                if (isSelected) {
                    $select.closest($_.$module).addClass('_selected');
                    $btn.text(title);
                }
            }
    
            $_.$body.trigger('trigger:init-scrollbar', {
                el: $optContainer[0]
            })
        },
        
        _addEvents($item, $btn, $select) {
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

    $(document).ready(() => {
        $_.init();
    });
});
