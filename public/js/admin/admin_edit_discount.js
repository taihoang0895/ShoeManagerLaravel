function validate(name, start_time, end_time, note){
    if(name === ""){
        showMessage("Tên chương trình không được để rỗng");
        return false;
    }
    if(start_time === ""){
         showMessage("Thời gian bắt đầu không được để rỗng");
        return false;
    }
    if(end_time === ""){
         showMessage("Thời gian kết thúc không được để rỗng");
        return false;
    }
    return true;

}

function saveDiscount(data){
     $.post('/admin/save-discount/', data, function(response) {
              if(response['status'] === 200){
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

function handleOkButton(){

 $('#edit_discount_btn_ok').click(function(){
        if(is_waiting_for_request){
            return;
        }
        var name = $('#discount_name').val().trim();
        var start_time = $('#discount_start_time').val().trim();
        var end_time = $('#discount_end_time').val().trim();
        var note = $('#discount_note').val().trim();

        if(validate(name, start_time, end_time,note )){
            is_waiting_for_request = true;
            setupCSRF();
             var discount_id = $('#edit_discount_id').val();
            var data = {
                'discount_id' : discount_id,
                'name' : name,
                'start_time' : start_time,
                'end_time' : end_time,
                'note' :note,
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
            saveDiscount(data);
        }
     });

}
function handleCancelButton(){
    $('#edit_discount_btn_cancel').click(function(){
        $('#edit_discount_dialog').css('display', 'none');
    });
}
$(document).ready(function () {

    handleOkButton();
    handleCancelButton();
});
