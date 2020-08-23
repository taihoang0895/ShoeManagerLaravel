var is_waiting_for_request = false;
function collectFilterParam(){
    var search_landing_page = $('#search_landing_page').val().trim();

    var param = "";
    if(search_landing_page !== ''){
        param = 'landing_page_name='+search_landing_page;
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
    $('#list_landing_pages_btn_add').click(function(){
              if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/admin/form-add-landing-page/", function(response) {
                        if(response['status'] === 200){
                               $('#dialog_edit_landing_page').empty();
                               $('#dialog_edit_landing_page').html(response['content']);
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
      $('#list_landing_pages_btn_update').click(function(){
             if($('.landing_page_row_selected').length ===0){
                    showMessage("Vui lòng chọn một chiến dịch để sửa");
              }else{
                  var landing_page_id = $('.landing_page_row_selected').find('.landing_page_id').val();
                   if (is_waiting_for_request){
                        return;
                      }
                      is_waiting_for_request = true;
                      var data = {
                            "landing_page_id" : landing_page_id,
                      }
                      $.get("/admin/form-update-landing-page/",data, function(response) {
                                if(response['status'] === 200){
                                       $('#dialog_edit_landing_page').empty();
                                       $('#dialog_edit_landing_page').html(response['content']);
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
       $('#list_landing_pages_btn_delete').click(function(){
              if($('.landing_page_row_selected').length == 0){
                    showMessage("Vui lòng chọn một chiến dịch xóa");
              }else{
                  $('#confirm_dialog_delete_landing_page').css('display', 'block');
              }
         });

         $('#landing_page_delete_dialog_btn_cancel').click(function(){
             $('#confirm_dialog_delete_landing_page').css('display', 'none');
         });

          $('#landing_page_delete_dialog_btn_ok').click(function(){
           $('#confirm_dialog_delete_landing_page').css('display', 'none');
             if(is_waiting_for_request){
                        return;
              }

               var landing_page_id = $('.landing_page_row_selected').find('.landing_page_id').val();

                var data = {
                        'landing_page_id' : landing_page_id,
                        '_token': $('meta[name=csrf-token]').attr('content'),
                }

                is_waiting_for_request = true;
                  $.post('/admin/delete-landing-page/', data, function(response) {
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
    $('.landing_page_row').click(function(){
            $('.landing_page_row_selected').removeClass('landing_page_row_selected');
            $(this).addClass('landing_page_row_selected');
        });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
    handleSearchButton();
});
