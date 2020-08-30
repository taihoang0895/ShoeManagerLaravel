var is_waiting_for_request = false;

function validate(time, note){
}

function saveOrderFailReason(data){
     $.post('/sale/leader/save-order-fail-reason/', data, function(response) {
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
}

function validate(cause, note){
    if(cause == ''){
      showMessage("Nguyên nhân không được để trống");
      return false;
    }
    return true;
}

function handleOkButton(){
     $('#add_order_fail_reason_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var cause = $('#order_fail_reason_cause').val().trim();
        var note = $('#order_fail_reason_note').val().trim();
        if(validate(cause, note)){
            var order_fail_reason_id = $('#edit_order_fail_reason_id').val().trim();
            is_waiting_for_request = true;
            setupCSRF();
            var data = {
                'order_fail_reason_id' : order_fail_reason_id,
                'cause' : cause,
                'note' : note,
                '_token': $('meta[name=csrf-token]').attr('content')
            }
            saveOrderFailReason(data);
        }
     });
}
function handleCancelButton(){
     $('#add_order_fail_reason_btn_cancel').click(function(){
        $('#add_order_fail_reason_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    handleOkButton();
    handleCancelButton();
});
