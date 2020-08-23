var is_waiting_for_request = false;
function collectFilterParam(){
    var username = $('#search_username').val().trim();

    var param = "";
    if(username !== ''){
        param = 'username='+username;
    }

    return param;
}

function handleAddButton(){
    $('#list_users_btn_add').click(function(){
          if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/admin/form-add-user/", function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_user').empty();
                               $('#dialog_edit_user').html(response['content']);
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
     $('#list_users_btn_update').click(function(){
         if($('.user_row_selected').length ==0){
              showMessage("Vui lòng chọn một khuyến mại để sửa");
         }else{
               var user_id = $('.user_row_selected').first().find('.user_id').val().trim();

               if (is_waiting_for_request){
                        return;
               }
               is_waiting_for_request = true;
               data = {
                    "user_id" : user_id
               }
               $.get("/admin/form-update-user", data, function(response) {
                                if(response['status'] == 200){
                                       $('#dialog_edit_user').empty();
                                       $('#dialog_edit_user').html(response['content']);
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
  $('#list_users_btn_delete').click(function(){
         if($('.user_row_selected').length ==0){
              showMessage("Vui lòng chọn một khuyến mại để xoá");
         }else{
               $('#confirm_dialog_delete_user').css('display', 'block');
               $('#user_delete_dialog_btn_ok').click(function(){
                   $('#confirm_dialog_delete_user').css('display', 'none');
                    var user_id = $('.user_row_selected').first().find('.user_id').val().trim();

                   if (is_waiting_for_request){
                            return;
                   }
                   is_waiting_for_request = true;
                   var data = {
                       '_token': $('meta[name=csrf-token]').attr('content'),
                        "user_id" : user_id.toString()
                   };
                   setupCSRF();
                   $.post("/admin/delete-user/", data, function(response) {
                                    if(response['status'] == 200){
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
               $('#user_delete_dialog_btn_cancel').click(function(){
                     $('#confirm_dialog_delete_user').css('display', 'none');
               });


         }
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
                  filter_param = collectFilterParam();
                  if (filter_param != ''){
                        curr_url = addParam(curr_url, filter_param);
                  }

                 location.href = curr_url;
           }
        });
     $('#next_page').click(function(){
             if($(this).hasClass('enable')){

                    var curr_page = $('#curr_page').val();

                    var next_page = parseInt(curr_page) + 1;
                    var curr_url = location.href.toString().toLowerCase();
                    curr_url = removeAllParam(curr_url);
                    curr_url = addParam(curr_url, "page="+next_page);
                    filter_param = collectFilterParam();
                    if (filter_param != ''){
                        curr_url = addParam(curr_url, filter_param);
                    }
                    location.href = curr_url;
             }
        });
}

function handleSearchButton(){
     $('#list_users_btn_search').click(function(){
            var curr_page = parseInt($('#curr_page').val());
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             location.href = curr_url;

    });
}

$(document).ready(function () {
        $('.user_row').click(function(){
            $('.user_row_selected').removeClass('user_row_selected');
            $(this).addClass('user_row_selected');
        });
        handleAddButton();
        handleUpdateButton();
        handleDeleteButton();
        handlePagination();
        handleSearchButton();
});
