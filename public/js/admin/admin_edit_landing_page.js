var is_waiting_for_request = false;

function saveLandingPage(data){
     $.post('/admin/save-landing-page/', data, function(response) {
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
    if(name === ""){
      showMessage("Tên không được để trống");
      return false;
    }

    return true;
}

function handleOkButton(){
     $('#add_landing_page_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var name = $('#landing_page_name').val().trim();
        var note = $('#landing_page_note').val().trim();

        if(validate(name, note)){
            var landing_page_id = $('#edit_landing_page_id').val().trim();
            is_waiting_for_request = true;
            var data = {
                'landing_page_id' : landing_page_id,
                'name' : name,
                'note' : note,
                '_token': $('meta[name=csrf-token]').attr('content'),

            }

            saveLandingPage(data);
        }
     });
}
function handleCancelButton(){
     $('#add_landing_page_btn_cancel').click(function(){
        $('#add_landing_page_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    handleOkButton();
    handleCancelButton();
});
