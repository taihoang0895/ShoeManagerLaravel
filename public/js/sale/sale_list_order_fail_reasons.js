var is_waiting_for_request = false;

function handleAddButton() {
    $('#list_fail_reason_btn_add').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        is_waiting_for_request = true;
        $.get("/sale/leader/form-add-order-fail-reason", function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_order_fail_reason').empty();
                $('#dialog_edit_order_fail_reason').html(response['content']);
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
}

function handleUpdateButton() {
    $('#list_fail_reason_btn_update').click(function () {
        if ($('.order_fail_reason_row_selected').length == 0) {
            showMessage("Vui lòng chọn một lịch để sửa");
        } else {
            var order_fail_reason_id = $('.order_fail_reason_row_selected').first().attr('id');

            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;
            var data = {
                'order_fail_reason_id' : order_fail_reason_id
            }
            $.get("/sale/leader/form-update-order-fail-reason/",data, function (response) {
                if (response['status'] == 200) {
                    $('#dialog_edit_order_fail_reason').empty();
                    $('#dialog_edit_order_fail_reason').html(response['content']);
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
        }
    });

}

function handleDeleteButton() {
    $('#list_fail_reason_btn_delete').click(function () {
        if ($('.order_fail_reason_row_selected').length == 0) {
            showMessage("Vui lòng chọn một lịch để xóa");
        } else {
            $('#confirm_dialog_delete_order_fail_reason').css('display', 'block');
        }
    });

    $('#fail_reason_delete_dialog_btn_cancel').click(function () {
        $('#confirm_dialog_delete_order_fail_reason').css('display', 'none');
    });

    $('#fail_reason_delete_dialog_btn_ok').click(function () {
        $('#confirm_dialog_delete_order_fail_reason').css('display', 'none');
        if (is_waiting_for_request) {
            return;
        }

        var order_fail_reason_id = $('.order_fail_reason_row_selected').first().attr('id');

        var data = {
            'order_fail_reason_id': order_fail_reason_id,
            '_token': $('meta[name=csrf-token]').attr('content')
        }
        setupCSRF();
        is_waiting_for_request = true;
        $.post('/sale/leader/delete-order-fail-reason/', data, function (response) {
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

}

function handlePagination() {
    $('#previous_page').click(function () {
        if ($(this).hasClass('enable')) {
            var curr_page = $('#curr_page').val();
            var prev_page = parseInt(curr_page) - 1;
            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            if (prev_page != 0) {
                curr_url = addParam(curr_url, "page=" + prev_page);
            }

            location.href = normalize(curr_url);
        }
    });
    $('#next_page').click(function () {
        if ($(this).hasClass('enable')) {

            var curr_page = $('#curr_page').val();
            var next_page = parseInt(curr_page) + 1;
            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            curr_url = addParam(curr_url, "page=" + next_page);

            location.href = normalize(curr_url);
        }
    });
}

$(document).ready(function () {
    $('.order_fail_reason_row').click(function () {
        $('.order_fail_reason_row').removeClass('order_fail_reason_row_selected');
        $(this).addClass('order_fail_reason_row_selected');
    });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
});
