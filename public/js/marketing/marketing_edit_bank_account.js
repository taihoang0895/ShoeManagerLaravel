var is_waiting_for_request = false;

function saveBankAccount(data){
     $.post('/marketing/save-bank-account/', data, function(response) {
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

function validate(bank_account){
    if(bank_account == ''){
      showMessage("Tên thẻ không được để trống");
      return false;
    }

    return true;
}

function handleOkButton(){
     $('#add_bank_account_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var bank_account = $('#bank_account').val().trim();

        if(validate(bank_account)){
            var bank_account_id = $('#edit_bank_account_id').val().trim();
            is_waiting_for_request = true;
            var data = {
                'bank_account_id' : bank_account_id,
                'name' : bank_account,
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
            saveBankAccount(data);
        }
     });
}
function handleCancelButton(){
     $('#add_bank_account_btn_cancel').click(function(){
        $('#add_bank_account_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    handleOkButton();
    handleCancelButton();
});
