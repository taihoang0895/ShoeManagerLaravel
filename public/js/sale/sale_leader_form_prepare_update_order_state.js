var cancel_push_order = true;
var pending_orders = [];

function collectListOrders() {
    pending_orders = [];
    $('.update_state_order_row').each(function () {
        var order_id = $(this).find('.order_id').val();
        pending_orders.push(order_id);
    });
}

function synchronizeOrderState() {
    var order_id = pending_orders.shift();
    var newStateId =  $("#update_state_order_row_" + order_id.toString()).find('.new_order_state_selected').val();
    $("#update_state_order_row_" + order_id.toString()).find('.loader').css('display', 'block');
    $("#update_state_order_row_" + order_id.toString()).find('.img_failed').css('display', 'none');
    $("#update_state_order_row_" + order_id.toString()).find('.img_success').css('display', 'none');


    var data = {
        'order_id': order_id,
        'new_state_id' : newStateId,
        '_token': $('meta[name=csrf-token]').attr('content'),
    }
    $.post("/sale/update-order-state/", data, function (response) {
        if (response['status'] == 200) {
            var newOrderState = response['new_order_state'];
            $("#update_state_order_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#update_state_order_row_" + order_id.toString()).find('.img_failed').css('display', 'none');
            $("#update_state_order_row_" + order_id.toString()).find('.img_success').css('display', 'block');
        }else{
            $("#update_state_order_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#update_state_order_row_" + order_id.toString()).find('.img_failed').css('display', 'block');
            $("#update_state_order_row_" + order_id.toString()).find('.img_success').css('display', 'none');
        }
    })
        .fail(function () {
            $("#update_state_order_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#update_state_order_row_" + order_id.toString()).find('.img_failed').css('display', 'block');
            $("#update_state_order_row_" + order_id.toString()).find('.img_success').css('display', 'none');
        })
        .always(function () {
            if (pending_orders.length == 0) {
                $('#order_synchronizer_btn_ok').text("Xong");
            } else {
                if (!cancel_push_order) {
                    synchronizeOrderState();
                }
            }
        });

}

function handleOkButton() {


    $('#order_synchronizer_btn_ok').click(function () {

        if ($('#order_synchronizer_btn_ok').text().trim() != "Bắt Đầu") {
            if ($('#order_synchronizer_btn_ok').text().trim() == "Xong") {
                location.reload();
            }
            return;
        }

        collectListOrders();
        if (pending_orders.length == 0) {
            showMessage("Không có order nào");
        } else {
            $('#order_synchronizer_btn_ok').text("Đang Đồng Bộ");
            cancel_push_order = false;
            synchronizeOrderState();
        }


    });
}

function handleCancelButton() {
    $('#order_synchronizer_btn_cancel').click(function () {
        cancel_push_order = true;
        $('#update_state_order_dialog').css('display', 'none');
        location.reload();
    });

    $('.update_state_order_row').each(function () {
        $(this).find('#dropdown_new_order_state a').click({row_id : $(this).find(".order_id").val()}, function (event) {
            var row_id = event.data.row_id
            $('#update_state_order_row_' + row_id).find('#dropdown_new_order_state_text').text($(this).text());
            $('#update_state_order_row_' + row_id).find('.new_order_state_selected').val($(this).find('#state_id').val());
        });
    });
}


$(document).ready(function () {
    init();
    handleOkButton();
    handleCancelButton();


});

