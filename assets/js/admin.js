global.moment = require('moment');
require('tempusdominus-bootstrap-4');

$('.js-estate-change').on('click', (e) => {
    const
        $currentTarget = $(e.currentTarget),
        dataUrl = $currentTarget.data('url'),
        requestParameters = {
            url: dataUrl,
            type: 'POST',
            dataType: 'json',
        };
    $.ajax(requestParameters).done(() => {
        $currentTarget.toggleClass('active');
    })
});