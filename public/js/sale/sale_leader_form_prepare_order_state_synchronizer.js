var cancel_push_order = true;
var pending_orders = [];

function collectListOrders(){
    pending_orders = [];
    $('.order_state_synchronizer_row').each(function() {
        var order_id = $(this).find('.order_id').val();
        pending_orders.push(order_id);
    });
}

function synchronizeOrderState(){
    var order_id = pending_orders.shift();
    $("#order_state_synchronizer_row_"+order_id.toString()).find('.loader').css('display', 'block');
    $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_failed').css('display', 'none');
    $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_success').css('display', 'none');
    $.get("/sale/synchronize-order-state/"+order_id.toString() + "/", function(response) {
                    if(response['status'] == 200){
                        $("#order_state_synchronizer_row_"+order_id.toString()).find('.loader').css('display', 'none');
                        $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_failed').css('display', 'none');
                        $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_success').css('display', 'block');
                     }
                 })
                 .fail(function() {
                    $("#order_state_synchronizer_row_"+order_id.toString()).find('.loader').css('display', 'none');
                    $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_failed').css('display', 'block');
                    $("#order_state_synchronizer_row_"+order_id.toString()).find('.img_success').css('display', 'none');
                 })
                 .always(function() {
                      if (pending_orders.length == 0){
                        $('#edit_order_state_btn_ok').text("Xong");
                      }else{
                            if (!cancel_push_order){
                                synchronizeOrderState();
                            }
                      }
                 });

}

function handleOkButton(){


    $('#edit_order_state_btn_ok').click(function() {

             if ($('#edit_order_state_btn_ok').text().trim() != "Bắt Đầu"){
                if ($('#edit_order_state_btn_ok').text().trim() == "Xong"){
                      location.reload();
                }
                return;
            }

            collectListOrders();
            if (pending_orders.length == 0){
                showMessage("Không có order nào");
            }else{
                $('#edit_order_state_btn_ok').text("Đang Đồng Bộ");
                cancel_push_order = false;
                synchronizeOrderState();
            }


    });
}
function handleCancelButton(){

    $('#edit_order_state_btn_cancel').click(function() {
          cancel_push_order= true;
          $('#order_state_synchronizer_dialog').css('display', 'none')
    });
}


$(document).ready(function () {
        init();
        handleOkButton();
        handleCancelButton();


});

