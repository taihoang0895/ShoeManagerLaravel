var is_waiting_for_request = false;

function saveMarketingSource(data){
     $.post('/marketing/save-marketing-source/', data, function(response) {
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

function validate(name, note){
    if(name == ''){
      showMessage("Tên nguồn không được để trống");
      return false;
    }

    return true;
}

function handleOkButton(){
     $('#add_marketing_source_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var name = $('#marketing_source_name').val().trim();
        var note = $('#marketing_source_note').val().trim();
        if(validate(name, note)){
            var marketing_source_id = $('#edit_marketing_source_id').val().trim();
            is_waiting_for_request = true;
            var data = {
                'marketing_source_id' : marketing_source_id,
                'name' : name,
                'note' : note,
                '_token': $('meta[name=csrf-token]').attr('content')
            }
            saveMarketingSource(data);
        }
     });
}
function handleCancelButton(){
     $('#add_marketing_source_btn_cancel').click(function(){
        $('#add_marketing_source_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    handleOkButton();
    handleCancelButton();
});
