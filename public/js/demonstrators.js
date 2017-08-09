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

    $('.accept-position').click(function (e) {
        var url ='/application/'+$(this).data('application')+'/student-accepts';
        axios.post(url).then(function (data) {
            console.log('hi');
        });
    });

});