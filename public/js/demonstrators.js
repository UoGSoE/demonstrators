$(document).ready(function () {
    $('.requests-tab').click(function (e) {
        e.preventDefault();
        var id = $(this).data('course');
        $('.applicants-content-'+id).hide(0, function() {
            $('.requests-content-'+id).show();
        });
    });
    $('.applicants-tab').click(function (e) {
        e.preventDefault();
        var id = $(this).data('course');
        $('.requests-content-'+id).hide(0, function() {
            $('.applicants-content-'+id).show();
        });
    });
    $('.checkbox').click(function (e) {
        var url = '/application/'+$(this).data('application')+'/toggle-accepted';
        axios.post(url, []).then(function( data ) {
            console.log('hello');
        });
    })
});