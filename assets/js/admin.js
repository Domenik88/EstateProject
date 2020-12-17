$('.js-estate-change').on('click', (e) => {
    const
        $currentTarget = $(e.currentTarget),
        dataUrl = $currentTarget.data('url'),
        requestParameters = {
            url: dataUrl,
            type: 'POST',
            dataType: 'json',
        };
    $currentTarget.addClass('_loading');
    $.ajax(requestParameters).done(() => {
        $currentTarget.removeClass('_loading').addClass('_success');
        $currentTarget.removeClass('btn-warning').addClass('btn-success');
    })
        .always(() => {
            $currentTarget.removeClass('_loading');
        })
});