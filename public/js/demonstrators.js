$(document).ready(function () {
    $('.requests-tab').click(function (e) {
        e.preventDefault();
        var id = $(this).data('course');
        $(this).parent().parent().children('.is-active').removeClass('is-active');
        $(this).parent().addClass('is-active');
        $('.applicants-content-'+id).hide(0, function() {
            $('.requests-content-'+id).show();
        });
    });
    $('.applicants-tab').click(function (e) {
        e.preventDefault();
        var id = $(this).data('course');
        $(this).parent().parent().children('.is-active').removeClass('is-active');
        $(this).parent().addClass('is-active');
        $('.requests-content-'+id).hide(0, function() {
            $('.applicants-content-'+id).show();
        });
    });
    $('.applicants-checkbox').click(function (e) {
        var url = '/application/'+$(this).data('application')+'/toggle-accepted';
        axios.post(url, []).then(function( data ) {
            console.log('hello');
        });
    });
    $('.application-form').submit(function (e) {
        e.preventDefault();
        button = $(this).find('.submit-button');
        button.toggleClass('is-loading');
        var formDetails = $(this).serialize();
        axios.post('/request', formDetails).then(function( data ) {
            setTimeout(function() {
                button.toggleClass('is-loading');
            }, 300);
        });
    })
});