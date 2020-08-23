var is_waiting_for_request = false;

function handleCancelButon(){
    $('#edit_order_btn_cancel').click(function(){
        $('#edit_order_dialog').css('display', 'none');
    });
}

function handleOKButon(){
    $('#edit_order_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }
        var order_id = $('#edit_order_id').val().trim();

        var note = $('#edit_order_note').val().trim();
        var state_id = $('#edit_order_state_id').val();
        var fail_reason_id = $('#edit_order_fail_reason_id').val();
        var delivery_time = $('#edit_order_delivery_time_text').val();

        is_waiting_for_request = true;
        var order = {};
        order['order_id'] = order_id;
        order['order_state_id'] = state_id;
        order['order_fail_id'] = fail_reason_id;
        order['note'] = note;
        order['delivery_time'] = delivery_time;
        setupCSRF();
        $.post('/sale/update-order', order, function(response) {
                  if(response['status'] == 200){
                        location.reload();
                  }else{
                    showMessage(response['message']);
                  }
               })
               .fail(function() {
                    showMessage("Lỗi mạng");
                })
                .always(function() {
                    is_waiting_for_request = false;
          });


    });
}
function init(){

    $('#edit_order_dropdown_state a').click(function(){
        $('#edit_order_dropdown_state_text').text($(this).text());
        var state_id = $(this).attr('id');
        var state_id = state_id.replace("edit_order_state_id_", "");
         $('#edit_order_state_id').val(state_id);
    });

     $('#edit_order_dropdown_reason a').click(function(){
        $('#edit_order_dropdown_reason_text').text($(this).text());
         var fail_reason_id = $(this).attr('id');
         var fail_reason_id = fail_reason_id.replace("edit_order_fail_reason_id_", "");
         $('#edit_order_fail_reason_id').val(fail_reason_id);
    });
}
$(document).ready(function () {
    init();
    handleCancelButon();
    handleOKButon();
});