var is_waiting_for_request = false;
function collectFilterParam(){
    var search_campaign_name = $('#search_campaign_name').val().trim();

    var param = "";
    if(search_campaign_name !== ''){
        param = 'campaign_name='+search_campaign_name;
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
    $('#list_campaign_names_btn_add').click(function(){
              if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/marketing/form-add-campaign_name/", function(response) {
                        if(response['status'] === 200){
                               $('#dialog_edit_campaign_name').empty();
                               $('#dialog_edit_campaign_name').html(response['content']);
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
      $('#list_campaign_names_btn_update').click(function(){
             if($('.campaign_name_row_selected').length ===0){
                    showMessage("Vui lòng chọn một chiến dịch để sửa");
              }else{
                  var campaign_name_id = $('.campaign_name_row_selected').find('.campaign_id').val();
                   if (is_waiting_for_request){
                        return;
                      }
                      is_waiting_for_request = true;
                      var data = {
                            "campaign_name_id" : campaign_name_id,
                      }
                      $.get("/marketing/form-update-campaign_name/",data, function(response) {
                                if(response['status'] === 200){
                                       $('#dialog_edit_campaign_name').empty();
                                       $('#dialog_edit_campaign_name').html(response['content']);
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
       $('#list_campaign_names_btn_delete').click(function(){
              if($('.campaign_name_row_selected').length == 0){
                    showMessage("Vui lòng chọn một chiến dịch xóa");
              }else{
                  $('#confirm_dialog_delete_campaign_name').css('display', 'block');
              }
         });

         $('#campaign_name_delete_dialog_btn_cancel').click(function(){
             $('#confirm_dialog_delete_campaign_name').css('display', 'none');
         });

          $('#campaign_name_delete_dialog_btn_ok').click(function(){
           $('#confirm_dialog_delete_campaign_name').css('display', 'none');
             if(is_waiting_for_request){
                        return;
              }

               var campaign_name_id = $('.campaign_name_row_selected').find('.campaign_id').val();

                var data = {
                        'campaign_name_id' : campaign_name_id,
                        '_token': $('meta[name=csrf-token]').attr('content'),
                }
                setupCSRF();
                is_waiting_for_request = true;
                  $.post('/marketing/delete-campaign-name/', data, function(response) {
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
    $('.campaign_name_row').click(function(){
            $('.campaign_name_row_selected').removeClass('campaign_name_row_selected');
            $(this).addClass('campaign_name_row_selected');
        });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
    handleSearchButton();
});
