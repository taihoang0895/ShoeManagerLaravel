function validate(){
    var bill_cost_threshold = $('#bill_cost_threshold').val().trim();
    var comment_cost_threshold = $('#comment_cost_threshold').val().trim();

    if (!isFloat(bill_cost_threshold)){
        showMessage("Ngưỡng Bill Cost phải là một số");
         return false;
    }
    if (!isFloat(comment_cost_threshold)){
        showMessage("Ngưỡng Bill Cost phải là một số");
         return false;
    }

    return true;
}

function handleSaveButton(){
     $('#config_btn_save').click(function(){
           if(validate()){
                var bill_cost_threshold = $('#bill_cost_threshold').val().trim();
                var comment_cost_threshold = $('#comment_cost_threshold').val().trim();
                var data = {
                    'bill_cost_threshold' : bill_cost_threshold,
                    'comment_cost_threshold' : comment_cost_threshold,
                    '_token': $('meta[name=csrf-token]').attr('content'),

                }
                 $.post('/admin/save-config/', data, function(response) {
                      if(response['status'] == 200){
                            showMessage("Lưu thành công");
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
    });
}
$(document).ready(function () {
    handleSaveButton();
})
