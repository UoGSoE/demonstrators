$(document).ready(function () {
    window.setTimeout(function () {
        location.reload();
    }, 7200000);
    $('.card-header').css('cursor', 'pointer');

    /*
        ------------Academics front page-----------
    */

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
        var course_id = $(this).data('course');
        var user_id = $(this).data('user');
        $(this).parent().parent().children('.is-active').removeClass('is-active');
        $(this).parent().addClass('is-active');
        $('.requests-content-' + course_id).hide(0, function() {
            $('.applicants-content-' + course_id).show();
        });
        var url = '/application/mark-seen';
        axios.post(url, { course_id: course_id, user_id: user_id}).catch((error) => {
            console.log(error);
        });
    });

    /*
        ------------Admin pages-----------
    */

    //Admin toggle button to set student's RTW status
    $('.rtw-checkbox').click(function (e) {
        var url = '/admin/rtw';
        var id = $(this).data('user');
        axios.post(url, {student_id:id}).then(function( response ) {
            if (response.data.returned_rtw) {
                $(".modal-card-title-rtw").text(response.data.student_name + ' - RTW Start and End Dates');
                $("#rtw-dates-form input[name=student_id]").val(id);
                $("#rtw-dates-form input[name=rtw_start]").val(response.data.rtw_start);
                $("#rtw-dates-form input[name=rtw_end]").val(response.data.rtw_end);
                $(".rtw-modal").toggleClass('is-active');
            }
        });
    });

    //When admin toggles RTW button on, this is the form that submits for the dates
    $(".rtw-dates-submit").click(function (e) {
        button = $(this);
        button.toggleClass('is-loading');
        var formDetails = $('#rtw-dates-form').serialize();
        var url = '/admin/rtw/dates';
        axios.post(url, formDetails).then(function (response) {
            setTimeout(function () {
                $(".rtw-start-" + response.data.id).show();
                $(".rtw-start-" + response.data.id).children(".rtw-start").text(response.data.rtw_start);
                $(".rtw-end-" + response.data.id).show();
                $(".rtw-end-" + response.data.id).children(".rtw-end").text(response.data.rtw_end);
                button.toggleClass("is-loading");
                $(".rtw-modal").toggleClass("is-active");
            }, 300);
        }); 
    });

    //Edit RTW Dates
    $(".rtw-dates-edit").click(function (e) {
        var id = $(this).data('user');
        var url = '/admin/rtw/dates/' + id;
        axios.get(url).then(function (response) {
            $(".modal-card-title-rtw").text(response.data.student_name + ' - RTW Start and End Dates');
            $("#rtw-dates-form input[name=student_id]").val(id);
            $("#rtw-dates-form input[name=rtw_start]").val(response.data.rtw_start);
            $("#rtw-dates-form input[name=rtw_end]").val(response.data.rtw_end);
            $(".rtw-modal").toggleClass('is-active');
        });
    });

    //Dismiss the form that appears for inputting RTW start and end dates
    $(".rtw-dates-dismiss").click(function (e) {
        $(".rtw-modal").toggleClass("is-active");
    });



    //Admin toggle button to set student's contract status
    $('.contracts-checkbox').click(function (e) {
        var url = '/admin/contracts';
        var id = $(this).data('user');
        axios.post(url, {student_id:id}).then(function( response ) {
            if (response.data.has_contract) {
                $(".modal-card-title-contract").text(response.data.student_name + ' - Contract Start and End Dates');
                $("#contract-dates-form input[name=student_id]").val(id);
                $("#contract-dates-form input[name=contract_start]").val(response.data.contract_start);
                $("#contract-dates-form input[name=contract_end]").val(response.data.contract_end);
                $(".contract-modal").toggleClass('is-active');
            }
        });
    });

    //When admin toggles contract button on, this is the form that submits for the dates
    $(".contract-dates-submit").click(function (e) {
        button = $(this);
        button.toggleClass('is-loading');
        var formDetails = $('#contract-dates-form').serialize();
        var url = '/admin/contracts/dates';
        axios.post(url, formDetails).then(function (response) {
            setTimeout(function () {
                $(".contract-start-" + response.data.id).show();
                $(".contract-start-" + response.data.id).children(".contract-start").text(response.data.contract_start);
                $(".contract-end-" + response.data.id).show();
                $(".contract-end-" + response.data.id).children(".contract-end").text(response.data.contract_end);
                button.toggleClass("is-loading");
                $(".contract-modal").toggleClass("is-active");
            }, 300);
        });
    });

    //Edit contract Dates
    $(".contract-dates-edit").click(function (e) {
        var id = $(this).data('user');
        var url = '/admin/contracts/dates/' + id;
        axios.get(url).then(function (response) {
            $(".modal-card-title-contract").text(response.data.student_name + ' - Contract Start and End Dates');
            $("#contract-dates-form input[name=student_id]").val(id);
            $("#contract-dates-form input[name=contract_start]").val(response.data.contract_start);
            $("#contract-dates-form input[name=contract_end]").val(response.data.contract_end);
            $(".contract-modal").toggleClass('is-active');
        });
    });

    //Dismiss the form that appears for inputting contract start and end dates
    $(".contract-dates-dismiss").click(function (e) {
        $(".contract-modal").toggleClass("is-active");
    });

    //Admin add new student
    $('.add-student').hover(function () {
        $(this).append($("<span> Add new student</span>"));
    }, function () {
        $(this).find("span:last").remove();
    });

    //Admin add new staff
    $('.add-staff').hover(function () {
        $(this).append($("<span> Add new staff</span>"));
    }, function () {
        $(this).find("span:last").remove();
    });

    //Admin add new course
    $('.add-course').hover(function () {
        $(this).append($("<span> Add new course</span>"));
    }, function () {
        $(this).find("span:last").remove();
    });

    //Admin delete a student
    $('.delete-student').hover( function() {
        $( this ).append( $( "<span> This will remove this student and all their applications (accepted or pending)</span>" ) );
        }, function() {
        $( this ).find( "span:last" ).remove();
    });

    $('.delete-student').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $(this).parent().children('.confirm-destroy').show();
    });

    //Admin confirm delete a student
    $('.confirm-destroy').hover(function () {
        $(this).append($("<span> Are you sure you want to remove this student and all their applications?</span>"));
    }, function () {
        $(this).find("span:last").remove();
    });

    //Adds loading class to import form button
    $('.import-form').submit(function (e) {
        $('.import-button').addClass('is-loading');
        $('.import-button').prop('disabled', true);
    });

    //Datatable used in reports
    $('#data-table').DataTable({ "pageLength": 100, "aaSorting": [], "lengthChange": false });

    /*
        ------------Universal pages-----------
    */

    //Toggle login blurb
    $('.toggle-blurb').click(function (e) {
        $('.modal').toggleClass('is-active');
    });

    //Disable login blurb
    $('.disable-blurb').click(function (e) {
        var url = '/user/'+$(this).data('user')+'/disable-blurb';
        axios.post(url);
    });

    //Makes all cards foldable
    $('.card-header-title').click(function (e) {
        $(this).parent().parent().children('.card-content').slideToggle();
    });
});