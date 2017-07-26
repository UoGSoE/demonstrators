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
    });
    $('.request-form').submit(function (e) {
        e.preventDefault();
        button = $(this).find('.submit-button');
        button.toggleClass('is-loading');
        var formDetails = $(this).serialize();
        var url = '/request/'+$(this).data('request')+'/apply';
        axios.post(url, formDetails).then(function( data ) {
            setTimeout(function() {
                button.toggleClass('is-loading');
                button.removeClass('is-info');
                button.addClass('is-success');
                button.prop('disabled', true);
                button.html('<span class="icon"><i class="fa fa-check"></i></span>');
            }, 300);
        });
    });
    $('.notes-form').submit(function (e) {
        e.preventDefault();
        button = $(this).find('.submit-button');
        button.toggleClass('is-loading');
        var formDetails = $(this).serialize();
        var url = '/student/'+$(this).data('user')+'/notes';
        axios.post(url, formDetails).then(function( data ) {
            setTimeout(function() {
                button.toggleClass('is-loading');
                button.removeClass('is-info');
                button.addClass('is-success');
                $('.notes-form').fadeOut(400, function() {
                    $('#info-button').fadeIn(400);
                });
            }, 300);
        });
    });
    $('#info-button').click(function(e) {
        e.preventDefault();
        $(this).fadeOut(200, function() {
            $('.notes-form').fadeIn(400);
        });
    })

});