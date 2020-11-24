console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

require('jquery');
require('jquery-lazy');
window.SimpleBar = require('simplebar');
require('./plugin/slick.js');
require('./plugin/jquery.validate.min.js');
require('./plugin/jquery.maskedinput.js');
require('./modules/select.js');
require('./modules/form.js');
require('./modules/popup.js');
require('./modules/common.js');

require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});