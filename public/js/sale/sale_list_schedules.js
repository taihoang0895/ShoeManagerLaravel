var is_waiting_for_request = false;


function collectFilterParam() {
    var start_time = $('#schedule_filter_start_time_text').val().trim();
    var end_time = $('#schedule_filter_end_time_text').val().trim();

    var param = "";

    if (start_time != '' && end_time != '') {
        return 'start_time=' + start_time + "&" + "end_time=" + end_time;
    }

    return param;
}


$(document).ready(function () {
    $('.schedule_row').click(function () {
        $('.schedule_row_selected').removeClass('schedule_row_selected');
        $(this).addClass('schedule_row_selected');
    });

    $('#list_schedule_btn_add').click(function () {
        var schedule_id = $('.schedule_row_selected').first().attr('id');
        data = {
            "schedule_id" : schedule_id
        }
        is_waiting_for_request = true;
        $.get('/sale/form-add-schedule/', data, function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_schedule').empty();
                $('#dialog_edit_schedule').html(response['content']);
            } else {
                showMessage(response['message']);
            }
        })
            .fail(function () {
                showMessage("Lỗi mạng");
            })
            .always(function () {
                is_waiting_for_request = false;

            });
    });

    $('#list_schedule_btn_update').click(function () {
        var schedule_id = $('.schedule_row_selected').first().attr('id');
        data = {
            "schedule_id" : schedule_id
        }

        is_waiting_for_request = true;
        $.get('/sale/form-update-schedule/', data, function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_schedule').empty();
                $('#dialog_edit_schedule').html(response['content']);
            } else {
                showMessage(response['message']);
            }
        })
            .fail(function () {
                showMessage("Lỗi mạng");
            })
            .always(function () {
                is_waiting_for_request = false;

            });
    });

    $('#list_schedule_btn_delete').click(function () {
        if ($('.schedule_row_selected').length == 0) {
            showMessage("Vui lòng chọn một lịch để xóa");
        } else {
            $('#confirm_dialog_delete_schedule').css('display', 'block');
        }
    });

    $('#schedule_delete_dialog_btn_cancel').click(function () {
        $('#confirm_dialog_delete_schedule').css('display', 'none');

    });

    $('#schedule_delete_dialog_btn_ok').click(function () {
        $('#confirm_dialog_delete_schedule').css('display', 'none');
        if (is_waiting_for_request) {
            return;
        }

        var schedule_id = $('.schedule_row_selected').first().attr('id');

        var data = {
            'schedule_id': schedule_id,
            '_token': $('meta[name=csrf-token]').attr('content')
        }
        setupCSRF();
        is_waiting_for_request = true;
        $.post('/sale/delete-schedule', data, function (response) {
            if (response['status'] == 200) {
                showMessage("xóa thành công");
                var curr_url = location.href.toString().toLowerCase();
                curr_url = removeAllParam(curr_url);
                location.href = curr_url;
            } else {
                showMessage(response['message']);
            }
        })
            .fail(function () {
                showMessage("Lỗi mạng");
            })
            .always(function () {
                is_waiting_for_request = false;
            });
    });

    $('#previous_page').click(function () {
        if ($(this).hasClass('enable')) {
            var curr_page = $('#curr_page').val();
            var prev_page = parseInt(curr_page) - 1;
            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            if (prev_page != 0) {
                curr_url = addParam(curr_url, "page=" + prev_page);
            }
            filter_param = collectFilterParam();
            if (filter_param != '') {
                curr_url = addParam(curr_url, filter_param);
            }

            location.href = curr_url;
        }
    });
    $('#next_page').click(function () {
        if ($(this).hasClass('enable')) {

            var curr_page = $('#curr_page').val();
            var next_page = parseInt(curr_page) + 1;
            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            curr_url = addParam(curr_url, "page=" + next_page);
            filter_param = collectFilterParam();
            if (filter_param != '') {
                curr_url = addParam(curr_url, filter_param);
            }
            location.href = curr_url;
        }
    });

    $('#schedule_btn_filter').click(function () {
        var start_time = $('#schedule_filter_start_time_text').val().trim();
        var end_time = $('#schedule_filter_end_time_text').val().trim();

        if (validateTimeRangeFilter(start_time, end_time)) {
            var curr_page = parseInt($('#curr_page').val());
            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            filter_param = collectFilterParam();
            if (curr_page == 0) {
                curr_url = addParam(curr_url, filter_param);
            } else {
                curr_url = addParam(curr_url, "page=" + curr_page);
                curr_url = addParam(curr_url, filter_param);
            }
            location.href = curr_url;
        }
    });
});
