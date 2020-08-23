var is_waiting_for_request = false;
function collectFilterParam(){
    var search_discount_code = $('#search_discount_code').val().trim();

    var param = "";
    if(search_discount_code !== ''){
        param = 'discount_code='+search_discount_code;
    }

    return param;
}

function handleAddButton(){
    $('#list_discounts_btn_add').click(function(){
          if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/admin/form-add-discount/", function(response) {
                        if(response['status'] === 200){
                               $('#dialog_edit_discount').empty();
                               $('#dialog_edit_discount').html(response['content']);
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
     $('#list_discounts_btn_update').click(function(){
         if($('.discount_row_selected').length ===0){
              showMessage("Vui lòng chọn một khuyến mại để sửa");
         }else{
               var discount_id = $('.discount_row_selected').first().find('.discount_id').val().trim();

               if (is_waiting_for_request){
                        return;
               }
               is_waiting_for_request = true;
               data = {
                    "discount_id" : discount_id
               }
               $.get("/admin/form-update-discount", data, function(response) {
                                if(response['status'] == 200){
                                       $('#dialog_edit_discount').empty();
                                       $('#dialog_edit_discount').html(response['content']);
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
  $('#list_discounts_btn_delete').click(function(){
         if($('.discount_row_selected').length ==0){
              showMessage("Vui lòng chọn một khuyến mại để xoá");
         }else{
               $('#confirm_dialog_delete_discount').css('display', 'block');
               $('#discount_delete_dialog_btn_ok').click(function(){
                   $('#confirm_dialog_delete_discount').css('display', 'none');
                    var discount_id = $('.discount_row_selected').first().find('.discount_id').val().trim();

                   if (is_waiting_for_request){
                            return;
                   }
                   is_waiting_for_request = true;
                   var data = {
                        "discount_id" : discount_id.toString(),
                       '_token': $('meta[name=csrf-token]').attr('content'),
                   };
                   setupCSRF();
                   $.post("/admin/delete-discount/", data, function(response) {
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
               $('#discount_delete_dialog_btn_cancel').click(function(){
                     $('#confirm_dialog_delete_discount').css('display', 'none');
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
     $('#list_discount_btn_search').click(function(){
            var curr_page = parseInt($('#curr_page').val());
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             location.href = curr_url;

    });
}

$(document).ready(function () {
        $('.discount_row').click(function(){
            $('.discount_row_selected').removeClass('discount_row_selected');
            $(this).addClass('discount_row_selected');
        });
        handleAddButton();
        handleUpdateButton();
        handleDeleteButton();
        handlePagination();
        handleSearchButton();
});
