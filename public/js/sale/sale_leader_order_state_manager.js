var is_waiting_for_request = false;

function collectAllOrdersSelected() {
    var list_order_ids = [];
    $('.cb_mark').each(function () {
        if ($(this).is(':checked')) {
            list_order_ids.push($(this).parent().find('.order_id').val());
        }
    });
    return list_order_ids;
}

function handlePushOrderButton() {
    $('#btn_sync_order').click(function () {
        var list_order_ids = collectAllOrdersSelected();
        if (list_order_ids.length == 0) {
            showMessage("Vui lòng chọn ít nhất một hóa đơn để đồng bộ");
        } else {
            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;
            var data = {};
            data['list_order_ids'] = JSON.stringify(list_order_ids);
            $.get("/sale/form-prepare-order-state-synchronizer/", data, function (response) {
                if (response['status'] == 200) {
                    $('#form_order_state_manager').empty();
                    $('#form_order_state_manager').html(response['content']);
                    $('#form_order_state_manager').css('display', 'block');

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

function handleUpdateOrderStateButton() {
    $('#btn_update_order_state').click(function () {
        var list_order_ids = collectAllOrdersSelected();
        if (list_order_ids.length == 0) {
            showMessage("Vui lòng chọn ít nhất một hóa đơn để sửa");
        } else {
            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;
            var data = {};
            data['list_order_ids'] = JSON.stringify(list_order_ids);
            $.get("/sale/form-prepare-update-order-state/", data, function (response) {
                if (response['status'] == 200) {
                    $('#form_order_state_manager').empty();
                    $('#form_order_state_manager').html(response['content']);
                    $('#form_order_state_manager').css('display', 'block');

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

function handleCancelOrderButton() {
    $('#btn_cancel_order').click(function () {
        var list_order_ids = collectAllOrdersSelected();
        if (list_order_ids.length == 0) {
            showMessage("Vui lòng chọn ít nhất một hóa đơn để đồng bộ");
        } else {
            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;
            var data = {};
            data['list_order_ids'] = JSON.stringify(list_order_ids);
            $.get("/sale/form-prepare-cancel-order/", data, function (response) {
                if (response['status'] == 200) {
                    $('#form_order_state_manager').empty();
                    $('#form_order_state_manager').html(response['content']);
                    $('#form_order_state_manager').css('display', 'block');

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

function handelSelectAllButton() {
    $('#cb_selected_all').click(function () {
        if ($(this).is(':checked')) {
            $('.cb_mark').each(function () {
                $(this).prop('checked', true);
            });
        } else {
            $('.cb_mark').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('.cb_mark').click(function () {
        var selectAll = true;
        $('.cb_mark').each(function () {
            if (!$(this).is(':checked')) {
                selectAll = false;
                return false;
            }
        });
        if (selectAll) {
            $('#cb_selected_all').prop('checked', true);
        } else {
            $('#cb_selected_all').prop('checked', false);
        }
    });

}

function handleDetailOrderButton() {
    $('.show_detail_order').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        var detail_order_id = $(this).attr('id');
        detail_order_id = detail_order_id.replace('order_id_', '');
        var url = '/sale/orders/' + detail_order_id;
        is_waiting_for_request = true;
        $.get(url, function (response) {

            if (response['status'] == 200) {
                $('#form_order_state_manager').empty();
                $('#form_order_state_manager').html(response['content']);
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

function init() {
    $('#filter_order_dropdown_state a').click(function () {
        $('#filter_order_dropdown_state_text').text($(this).text());
        $('#filter_order_state_id_selected').val($(this).find('#state_id').val());
    });

}

function collectFilterParam() {
    var start_time = $('#order_filter_start_time_text').val().trim();
    var end_time = $('#order_filter_end_time_text').val().trim();
    var order_state_id = $('#filter_order_state_id_selected').val().trim();
    var search_ghtk_code = $('#list_order_state_search_ghtk_code').val().trim();
    var search_phone_number = $('#list_order_state_search_phone_number').val().trim();

    var param = "";
    if (start_time != '' && end_time != '') {
        param = 'start_time=' + start_time + "&" + "end_time=" + end_time;
    }
    if (order_state_id != "-1") {
        param += "&order_state_id=" + order_state_id
    }
    if (search_ghtk_code != "") {
        param += "&search_ghtk_code=" + search_ghtk_code
    }
    if (search_phone_number != "") {
        param += "&search_phone_number=" + search_phone_number
    }

    if (param.startsWith("&")) {
        param = param.substring(1);
    }

    return param;
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

$(document).ready(function () {
    init();
    handelSelectAllButton();
    handlePushOrderButton();
    handleCancelOrderButton();
    handleUpdateOrderStateButton();
    handleDetailOrderButton();
    handleFilterButton();
});
