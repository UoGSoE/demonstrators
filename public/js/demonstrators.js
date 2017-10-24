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
                button.removeClass('is-gla');
                button.addClass('is-gla-success');
                $('.notes-form').fadeOut(400, function() {
                    $('#info-button').fadeIn(400);
                });
            }, 300);
        });
    });

    //Admin toggle button to set student's RTW status
    $('.rtw-checkbox').click(function (e) {
        var url = '/admin/rtw';
        var id = $(this).data('user');
        axios.post(url, {student_id:id}).then(function( data ) {
        });
    });

    //Admin toggle button to set student's contract status
    $('.contracts-checkbox').click(function (e) {
        var url = '/admin/contracts';
        var id = $(this).data('user');
        axios.post(url, {student_id:id}).then(function( data ) {
        });
    });

    //Admin mega delete
    $('.mega-delete').hover( function() {
        $( this ).append( $( "<span> This will remove all their applications (accepted or pending)</span>" ) );
        }, function() {
        $( this ).find( "span:last" ).remove();
    });

    //Student confirms their position
    $('.accept-position').click(function (e) {
        var url ='/application/'+$(this).data('application')+'/student-confirms';
        var row = '.row-'+$(this).data('application');
        axios.post(url).then(function (data) {
            $(row).fadeOut(400, function(){});
        });
    });

    //Student declines their position
    $('.decline-position').click(function (e) {
        var url ='/application/'+$(this).data('application')+'/student-declines';
        var row = '.row-'+$(this).data('application');
        axios.post(url).then(function (data) {
            $(row).fadeOut(400, function(){});
        });
    });

    $('.toggle-blurb').click(function (e) {
        $('.modal').toggleClass('is-active');
    });

    $('.disable-blurb').click(function (e) {
        var url = '/user/'+$(this).data('user')+'/disable-blurb';
        axios.post(url);
    });

    $('#staff-table').DataTable({"pageLength": 100, "aaSorting": [], "lengthChange": false});
});