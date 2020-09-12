var is_waiting_for_request = false;

function collectFilterParam() {

    var start_time = $('#order_filter_start_time_text').val().trim();
    var end_time = $('#order_filter_end_time_text').val().trim();

    var order_state_id = $('#filter_order_state_id_selected').val().trim();
    var filter_member_id = $('#filter_member_id_selected').val().trim();
    var filter_order_type = $('#filter_order_type_selected').val();

    var param = "";
    if (start_time != '' && end_time != '') {
        param = 'start_time=' + start_time + "&" + "end_time=" + end_time;
    }
    if (order_state_id != "-1") {
        param += "&order_state_id=" + order_state_id
    }
    if (filter_member_id != "-1") {
        param += "&filter_member_id=" + filter_member_id
    }
    param += "&filter_order_type=" + filter_order_type;
    if (param.startsWith("&")) {
        param = param.substring(1);
    }

    return param;
}

function handleAddButton() {
    $('#list_orders_btn_add').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        is_waiting_for_request = true;
        $.get("/sale/form-add-order", function (response) {
            if (response['status'] == 200) {
                $('#detail_order_show_detail_item').empty();
                $('#detail_order_show_detail_item').html(response['content']);
                $('#edit_order_dialog').css('display', 'block');
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

function handleDetailOrderButton() {
    $('.show_detail_order').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        var detail_order_id = $(this).attr('id');
        detail_order_id = detail_order_id.replace('order_id_', '');
        var data = {
            "order_id": detail_order_id
        }
        var url = '/sale/detail-order/';
        is_waiting_for_request = true;
        $.get(url, data, function (response) {

            if (response['status'] == 200) {
                $('#detail_order_show_detail_item').empty();
                $('#detail_order_show_detail_item').html(response['content']);
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
        return false;
    });
}

function handleUpdateButton() {
    $('#list_orders_customer_btn_update').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        if ($('.row_selected').length == 0) {
            showMessage("Vui lòng chọn một hóa đơn để sửa");
        } else {
            var order_id = $('.row_selected').first().attr('id');
            order_id = order_id.replace("order_row_", "");
            var data = {
                "order_id" : order_id
            }
            var url = '/sale/form-update-order/';
            is_waiting_for_request = true;
            $.get(url, data,function (response) {
                if (response['status'] == 200) {
                    $('#detail_order_show_detail_item').empty();
                    $('#detail_order_show_detail_item').html(response['content']);
                    $('#edit_order_dialog').css('display', 'block');
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
}

function handleDeleteButton() {
    $('#order_delete_dialog_btn_cancel').click(function () {
        $('#confirm_dialog_delete_order').css('display', 'none');
    });
    $('#order_delete_dialog_btn_ok').click(function () {
        $('#confirm_dialog_delete_order').css('display', 'none');
        if (is_waiting_for_request) {
            return;
        }
        var order_id = $('.row_selected').first().attr('id');
        order_id = order_id.replace("order_row_", "");
        var data = {
            'order_id': order_id,
            '_token': $('meta[name=csrf-token]').attr('content'),
        }

        is_waiting_for_request = true;
        $.post('/sale/delete-order', data, function (response) {
            if (response['status'] == 200) {
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
    $('#list_orders_customer_btn_delete').click(function () {

        if ($('.row_selected').length == 0) {
            showMessage("Vui lòng chọn một hóa đơn để xóa");
        } else {
            $('#confirm_dialog_delete_order').css('display', 'block');
        }
    });

}

function handleFilterButton() {
    $('#order_btn_filter').click(function () {

        var start_time = $('#order_filter_start_time_text').val().trim();
        var end_time = $('#order_filter_end_time_text').val().trim();

        if (validateTimeRangeFilter(start_time, end_time)) {

            var curr_url = location.href.toString().toLowerCase();
            curr_url = removeAllParam(curr_url);
            filter_param = collectFilterParam();

            curr_url = addParam(curr_url, filter_param);
            curr_url = normalize(curr_url);
            location.href = curr_url;
        }
    });
}

function init() {
    $('.order_row').click(function () {
        $('.row_selected').removeClass('row_selected');
        $(this).addClass('row_selected');
    });

    $('#filter_order_type a').click(function () {
        $('#filter_order_type_text').text($(this).text());
        $('#filter_order_type_selected').val($(this).find('.order_type_id').val())
    });
    $('#filter_order_dropdown_state a').click(function () {
        $('#filter_order_dropdown_state_text').text($(this).text());
        $('#filter_order_state_id_selected').val($(this).find('#state_id').val());
    });

    $('#filter_by_member a').click(function () {
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });

}

$(document).ready(function () {
    init();
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handleDetailOrderButton();
    handleDeleteButton();
    handlePagination();
    handleFilterButton();

});
