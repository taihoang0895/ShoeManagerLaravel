var cancel_push_order = true;
var pending_orders = [];

function collectListOrders() {
    pending_orders = [];
    $('.order_state_synchronizer_row').each(function () {
        var order_id = $(this).find('.order_id').val();
        pending_orders.push(order_id);
    });
}

function synchronizeOrderState() {
    var order_id = pending_orders.shift();
    $("#order_state_synchronizer_row_" + order_id.toString()).find('.loader').css('display', 'block');
    $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_failed').css('display', 'none');
    $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_success').css('display', 'none');
    var data = {
        'order_id': order_id,
        '_token': $('meta[name=csrf-token]').attr('content'),
    }
    $.post("/sale/synchronize-order-state/", data, function (response) {
        if (response['status'] == 200) {
            var newOrderState = response['new_order_state'];
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_failed').css('display', 'none');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_success').css('display', 'block');
            if(response['is_change']){
                $("#order_state_synchronizer_row_" + order_id.toString()).find('.new_order_state').css("background-color", '#00FF00')
            }
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.new_order_state').text(newOrderState);
        }else{
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_failed').css('display', 'block');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_success').css('display', 'none');
        }
    })
        .fail(function () {
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.loader').css('display', 'none');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_failed').css('display', 'block');
            $("#order_state_synchronizer_row_" + order_id.toString()).find('.img_success').css('display', 'none');
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
        $('#order_state_synchronizer_dialog').css('display', 'none');
        location.reload();
    });
}


$(document).ready(function () {
    init();
    handleOkButton();
    handleCancelButton();


});

