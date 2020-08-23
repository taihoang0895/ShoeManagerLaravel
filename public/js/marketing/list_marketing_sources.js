var is_waiting_for_request = false;

function handleAddButton(){
    $('#list_marketing_source_btn_add').click(function(){
              if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/marketing/form-add-marketing-source/", function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_marketing_source').empty();
                               $('#dialog_edit_marketing_source').html(response['content']);
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
      $('#list_marketing_source_btn_update').click(function(){
             if($('.marketing_source_row_selected').length ==0){
                    showMessage("Vui lòng chọn một nguồn marketing để sửa");
              }else{
                  var marketing_source_id = $('.marketing_source_row_selected').first().find('.marketing_source_id').val().trim();

                   if (is_waiting_for_request){
                        return;
                      }
                      is_waiting_for_request = true;
                      $.get("/marketing/form-update-marketing-source/"+marketing_source_id.toString(), function(response) {
                                if(response['status'] == 200){
                                       $('#dialog_edit_marketing_source').empty();
                                       $('#dialog_edit_marketing_source').html(response['content']);
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
       $('#list_marketing_source_btn_delete').click(function(){
              if($('.marketing_source_row_selected').length == 0){
                    showMessage("Vui lòng chọn một nguồn marketing để xóa");
              }else{
                  $('#confirm_dialog_delete_marketing_source').css('display', 'block');
              }
         });

         $('#marketing_source_delete_dialog_btn_cancel').click(function(){
             $('#confirm_dialog_delete_marketing_source').css('display', 'none');
         });

          $('#marketing_source_delete_dialog_btn_ok').click(function(){
           $('#confirm_dialog_delete_marketing_source').css('display', 'none');
             if(is_waiting_for_request){
                        return;
              }

             var marketing_source_id = $('.marketing_source_row_selected').first().find('.marketing_source_id').val().trim();

                var data = {
                        'marketing_source_id' : marketing_source_id
                }
                setupCSRF();
                is_waiting_for_request = true;
                  $.post('/marketing/delete-marketing_source/', data, function(response) {
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
    $('.marketing_source_row').click(function(){
            $('.marketing_source_row').removeClass('marketing_source_row_selected');
            $(this).addClass('marketing_source_row_selected');
        });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
});