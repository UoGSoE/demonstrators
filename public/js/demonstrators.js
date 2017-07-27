$(document).ready(function () {
    //Requests/Applicants view for staff members - tabs
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

    //Staff accepting a student's application
    $('.applicants-checkbox').click(function (e) {
        var url = '/application/'+$(this).data('application')+'/toggle-accepted';
        axios.post(url, []).then(function( data ) {
        });
    });

    //Staff saving their request details
    $('.request-form').submit(function (e) {
        e.preventDefault();
        button = $(this).find('.submit-button');
        button.toggleClass('is-loading');
        var formDetails = $(this).serialize();
        axios.post('/request', formDetails).then(function( data ) {
            setTimeout(function() {
                button.toggleClass('is-loading');
            }, 300);
        }).catch(function (error) {
            button.toggleClass('is-loading');
            button.removeClass('is-info');
            button.addClass('is-danger');
            button.prop('disabled', true);
            button.html('Error');
        });;
    });

    //Student applying for a request
    $('.application-form').submit(function (e) {
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

    //Student click this button to show the notes form
    $('#info-button').click(function(e) {
        e.preventDefault();
        $(this).fadeOut(200, function() {
            $('.notes-form').fadeIn(400);
        });
    });

    //Student setting their notes
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

    //Admin toggle button to set student's contract status
    $('.contracts-checkbox').click(function (e) {
        var url = '/admin/contracts';
        var id = $(this).data('user');
        axios.post(url, {student_id:id}).then(function( data ) {
        });
    });

    //
    $('.delete-request').click(function (e) {
        e.preventDefault();
        button = $(this);
        button.toggleClass('is-loading');
        var id = $(this).data('request');
        var url = '/request/'+id+'/withdraw';
        axios.post(url).then(function( data ) {
            setTimeout(function() {
                button.toggleClass('is-loading');
                location.reload();
            }, 500);
        });
    });
});