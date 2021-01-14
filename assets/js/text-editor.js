const te_ = {
    init() {
        this.initCache();
        if (document.getElementById(te_.editorId)) this.initTextEditor();
    },

    initCache() {
        this.editorId = 'editor';
    },

    initTextEditor() {
        const
            Quill = require('./plugin/quill'),
            Parchment = Quill.import('parchment'),
            Block = Quill.import('blots/block'),
            icons = Quill.import('ui/icons'),
            ImgWrapAttributor = new Parchment.Attributor.Class('img-wrap-class', 'img', {
                scope: Parchment.Scope.BLOCK
            }),
            toolbarOptions = [
                [
                    { "header": [false, 1, 2, 3, 4, 5, 6]},
                    'bold',
                    'italic',
                    'underline',
                    'link',
                    { 'list': 'ordered' },
                    { 'list': 'bullet' },
                    'image',
                ],
                [
                    "img-round-wrap",
                    "img-gallery",
                    "img-big",
                    "img-float-left",
                    "img-float-right",
                    "img-clear-format",
                ]
            ];

        icons['img-gallery'] = 'Image gallery';
        icons['img-round-wrap'] = 'Image round wrap';
        icons['img-big'] = 'Big image';
        icons['img-float-left'] = 'Image float left';
        icons['img-float-right'] = 'Image float right';
        icons['img-clear-format'] = 'Clear image format';

        Quill.register(ImgWrapAttributor, true);

        class ImageBlot extends Parchment.Embed {
            static create(value) {
                const
                    wrap = document.createElement('span'),
                    img = document.createElement('img');

                img.setAttribute('src', value);
                wrap.classList.add('img-wrap');
                wrap.appendChild(img);

                return wrap;
            }

            static value(node) {
                return node.getAttribute('src');
            }

            static formats(domNode) {
                if (domNode.hasAttribute('class')) {
                    return { 'class': domNode.getAttribute('class') }
                }
            }

            format(name, value) {
                if (value) {
                    this.domNode.classList.add(value.class || value);
                }
            }
        }
        ImageBlot.blotName = 'image';
        ImageBlot.tagName = 'span';
        Parchment.register(ImageBlot);

        class CustomParagraph extends Block {
            static create(value) {
                return super.create(value);
            }

            static formats(domNode) {
                if (domNode.hasAttribute('class')) {
                    return {
                        'class': domNode.getAttribute('class')
                    }
                }
            }

            format(name, value) {
                if (name === 'img-wrap-class' || name === 'block') {
                    if (value === 'round') {
                        const img = this.domNode.getElementsByTagName('img')[0];

                        if (img && (!img.classList.contains('img-wrap'))) {
                            const
                                imgWrap = document.createElement('span'),
                                imgClone = img.cloneNode(),
                                range = document.createRange();

                            imgWrap.classList.add('img-wrap');
                            imgWrap.appendChild(imgClone);

                            this.domNode.insertBefore(imgWrap, img);
                            img.setAttribute('style', 'display:none');

                            setTimeout(() => {
                                range.setStart(this.domNode, 0);
                                range.setEnd(this.domNode, 1);
                                document.getSelection().removeAllRanges();
                                document.getSelection().addRange(range);
                                img.remove();
                            }, 50);
                        }
                    } else if (value === 'img-clear-format') {
                        this.domNode.setAttribute('class','');
                    } else {
                        if (value) {
                            const setClass = value.class || value;

                            if (setClass && setClass.length) {
                                const prefix = name ? (name === 'img-wrap-class' ? 'img-' : '') : '';
                                this.domNode.classList.add(prefix + (value.class || value));
                            }
                        }
                    }
                } else {
                    super.format(name, value);
                }
            }
        }
        CustomParagraph.tagName = 'p';
        Quill.register(CustomParagraph, true);

        class Label extends Parchment.Embed {
            static create(value) {
                const node = super.create(value);
                node.appendChild(value);
                return node;
            }

            static value(node) {
                return node.childNodes[0];
            }
        }
        Label.blotName = 'label';
        Label.tagName = 'SPAN';
        Label.className = 'img-wrap';
        Quill.register(Label);


        const quill = new Quill('#' + te_.editorId, {
            theme: "snow",
            modules: {
                clipboard: {
                    matchVisual: false,
                },
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        'img-round-wrap': () => { te_._formatImg(quill, 'img-wrap-class', 'round') },
                        'img-gallery': () => { te_._formatImg(quill, 'img-wrap-class', 'gallery') },
                        'img-big': () => { te_._formatImg(quill, 'img-wrap-class', 'big') },
                        'img-float-right': () => { te_._formatImg(quill, 'img-wrap-class', 'float-right') },
                        'img-float-left': () => { te_._formatImg(quill, 'img-wrap-class', 'float-left') },
                        'img-clear-format': () => { te_._formatImg(quill, 'img-wrap-class', 'img-clear-format') },
                    }
                }
            },
        });
    },

    _formatImg(quill, action, val) {
        const
            range = quill.getSelection(),
            format = quill.getFormat(range);

        if (!format[action]) {
            quill.format(action, val);
        } else {
            quill.removeFormat(range.index, range.index + range.length);
        }
    }
};

document.addEventListener("DOMContentLoaded", () => {
    te_.init();
});
