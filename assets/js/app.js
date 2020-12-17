console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

import SimpleBar from 'simplebar'
window.SimpleBar = SimpleBar;

require('jquery');
require('./plugin/jquery-ui.min.js');
require('./plugin/jquery.ui.touch-punch.min.js');
require('jquery-lazy');
require('objectFitPolyfill');
require('./plugin/slick.js');
require('./plugin/jquery.validate.min.js');
require('./modules/select.js');
require('./modules/form.js');
require('./modules/popup.js');
require('./modules/common.js');

require('bootstrap');

$(document).ready(() => {
    objectFitPolyfill($('.js-object-fit'));
    $('[data-toggle="popover"]').popover();
});