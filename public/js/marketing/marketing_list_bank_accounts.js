var is_waiting_for_request = false;
function collectFilterParam(){
    var search_bank_account = $('#search_bank_account').val().trim();

    var param = "";
    if(search_bank_account != ''){
        param = 'bank_account='+search_bank_account;
    }

    return param;
}

function handleSearchButton(){
     $('#list_campaign_btn_search').click(function(){
            var curr_page = parseInt($('#curr_page').val());
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             curr_url = normalize(curr_url);
             location.href = curr_url;

    });
}

function handleAddButton(){
    $('#list_bank_accounts_btn_add').click(function(){
              if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/marketing/form-add-bank_account/", function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_bank_account').empty();
                               $('#dialog_edit_bank_account').html(response['content']);
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

function handleUpdateButton(){
      $('#list_bank_accounts_btn_update').click(function(){
             if($('.bank_account_row_selected').length ==0){
                    showMessage("Vui lòng chọn một thẻ để sửa");
              }else{
                  var bank_account_id = $('.bank_account_row_selected').find('.bank_account_id').val();
                   if (is_waiting_for_request){
                        return;
                      }
                      is_waiting_for_request = true;
                      var data = {
                            "bank_account_id" : bank_account_id
                      }
                      $.get("/marketing/form-update-bank_account/",data, function(response) {
                                if(response['status'] == 200){
                                       $('#dialog_edit_bank_account').empty();
                                       $('#dialog_edit_bank_account').html(response['content']);
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

function handleDeleteButton(){
       $('#list_bank_accounts_btn_delete').click(function(){
              if($('.bank_account_row_selected').length == 0){
                    showMessage("Vui lòng chọn một thẻ để xóa");
              }else{
                  $('#confirm_dialog_delete_bank_account').css('display', 'block');
              }
         });

         $('#bank_account_delete_dialog_btn_cancel').click(function(){
             $('#confirm_dialog_delete_bank_account').css('display', 'none');
         });

          $('#bank_account_delete_dialog_btn_ok').click(function(){
           $('#confirm_dialog_delete_bank_account').css('display', 'none');
             if(is_waiting_for_request){
                        return;
              }

               var bank_account_id = $('.bank_account_row_selected').find('.bank_account_id').val();

                var data = {
                        'bank_account_id' : bank_account_id,
                        '_token': $('meta[name=csrf-token]').attr('content'),
                }
                setupCSRF();
                is_waiting_for_request = true;
                  $.post('/marketing/delete-bank-account/', data, function(response) {
                      if(response['status'] == 200){
                            showMessage("xóa thành công");
                            var curr_url = location.href.toString().toLowerCase();
                            curr_url = removeAllParam(curr_url);
                            location.href = curr_url;
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
function handlePagination(){
    $('#previous_page').click(function(){
           if($(this).hasClass('enable')){
                 var curr_page = $('#curr_page').val();
                 var prev_page = parseInt(curr_page) - 1;
                 var curr_url = location.href.toString().toLowerCase();
                 curr_url = removeAllParam(curr_url);
                 if (prev_page != 0){
                    curr_url = addParam(curr_url, "page="+prev_page);
                 }

                 location.href = normalize(curr_url);
           }
        });
    $('#next_page').click(function(){
             if($(this).hasClass('enable')){

                    var curr_page = $('#curr_page').val();
                    var next_page = parseInt(curr_page) + 1;
                    var curr_url = location.href.toString().toLowerCase();
                    curr_url = removeAllParam(curr_url);
                    curr_url = addParam(curr_url, "page="+next_page);

                    location.href = normalize(curr_url);
             }
        });
}
$(document).ready(function () {
    $('.bank_account_row').click(function(){
            $('.bank_account_row_selected').removeClass('bank_account_row_selected');
            $(this).addClass('bank_account_row_selected');
        });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
    handleSearchButton();
});
