var cancel_push_order = true;
var pending_orders = [];

function collectListOrders(){
    pending_orders = [];
    $('.order_delivering_row').each(function() {
        var order_id = $(this).find('.order_id').val();
        pending_orders.push(order_id);
    });
}

function pushOrder(){
    var order_id = pending_orders.shift();
    $("#order_delivering_row_"+order_id.toString()).find('.loader').css('display', 'block');
    $("#order_delivering_row_"+order_id.toString()).find('.img_failed').css('display', 'none');
    $("#order_delivering_row_"+order_id.toString()).find('.img_success').css('display', 'none');
    $.get("/sale/push-order-to-deliver/"+order_id.toString() + "/", function(response) {
                    if(response['status'] == 200){
                        $("#order_delivering_row_"+order_id.toString()).find('.loader').css('display', 'none');
                        $("#order_delivering_row_"+order_id.toString()).find('.img_failed').css('display', 'none');
                        $("#order_delivering_row_"+order_id.toString()).find('.img_success').css('display', 'block');
                     }
                 })
                 .fail(function() {
                    $("#order_delivering_row_"+order_id.toString()).find('.loader').css('display', 'none');
                    $("#order_delivering_row_"+order_id.toString()).find('.img_failed').css('display', 'block');
                    $("#order_delivering_row_"+order_id.toString()).find('.img_success').css('display', 'none');
                 })
                 .always(function() {
                      if (pending_orders.length == 0){
                        $('#edit_customer_btn_ok').text("Xong");
                      }else{
                            if (!cancel_push_order){
                                pushOrder();
                            }
                      }
                 });

}

function handleOkButton(){
    $('#edit_order_btn_ok').click(function() {
             if ($('#edit_customer_btn_ok').text().trim() != "Bắt Đầu"){
                if ($('#edit_customer_btn_ok').text().trim() == "Xong"){
                      location.reload();
                }
                return;
            }
            $('#edit_customer_btn_ok').text("Đang Đẩy Đơn");
            collectListOrders();
            if (pending_orders.length == 0){
                showMessage("Không có order nào");
            }else{
                cancel_push_order = false;
                pushOrder();
            }


    });
}
function handleCancelButton(){

    $('#edit_order_btn_cancel').click(function() {
          cancel_push_order= true;
          $('#order_delivering_dialog').css('display', 'none')
    });
}


$(document).ready(function () {
        handleOkButton();
        handleCancelButton();

});

