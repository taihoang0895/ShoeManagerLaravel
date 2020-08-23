var is_waiting_for_request = false;

function handleAddButton(){
    $('#list_customer_btn_add').click(function(){
             if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/sale/form-add-customer", function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_customer').empty();
                               $('#dialog_edit_customer').html(response['content']);
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

function handleShowDetailCustomer(){
    $('.show_detail').click(function(){
             if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              var customer_id = $(this).find('input').val();
              var data = {
                'customer_id' : customer_id
              }
              $.get("/sale/detail-customer/",data, function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_customer').empty();
                               $('#dialog_edit_customer').html(response['content']);
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
     $('#list_customer_btn_update').click(function(){
             if($('.customer_row_selected').length ==0){
                    showMessage("Vui lòng chọn một khách hàng để sửa");
              }else{
                     var customer = $('.customer_row_selected').first().attr('id');

                     var customer_id = customer.replace("customer_", "").trim();

                     if (is_waiting_for_request){
                        return;
                      }
                      is_waiting_for_request = true;
                      $.get("/sale/form-update-customer/"+customer_id.toString(), function(response) {
                                if(response['status'] == 200){
                                       $('#dialog_edit_customer').empty();
                                       $('#dialog_edit_customer').html(response['content']);
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
      $('#list_customer_btn_delete').click(function(){
              if($('.customer_row_selected').length == 0){
                    showMessage("Vui lòng chọn một khách hàng để xóa");
              }else{
                  $('#confirm_dialog_delete_customer').css('display', 'block');
              }
         });
          $('#customer_delete_dialog_btn_cancel').click(function(){
            $('#confirm_dialog_delete_customer').css('display', 'none');

         });
          $('#customer_delete_dialog_btn_ok').click(function(){
            $('#confirm_dialog_delete_customer').css('display', 'none');
             if(is_waiting_for_request){
                        return;
                  }
                  var customer_id = $('.customer_row_selected').first().attr('id');
                  customer_id = customer_id.replace("customer_", "").trim();
                  var data = {
                        'customer_id' : customer_id
                  }
                  setupCSRF();
                   is_waiting_for_request = true;
                  $.post('/sale/delete-customer', data, function(response) {
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

}

function handlePagination(){
        $('#previous_page').click(function(){
          if($(this).hasClass('enable')){
                var curr_page = $('#curr_page').val();
                var prev_page = parseInt(curr_page) - 1;
                load_page(prev_page);
           }
        });
        $('#next_page').click(function(){

        if($(this).hasClass('enable')){
                var curr_page = $('#curr_page').val();
                var next_page = parseInt(curr_page) + 1;
                $('#curr_page').val(next_page);
                load_page(next_page);
           }

        });

}

function handleSearchButton(){
      $('#list_customer_btn_search').click(function(){
               var search_phone_number = $('#list_customer_search_phone_number').val().trim();
               if(search_phone_number != ''){
                location.href = "/sale/search-customer/" + search_phone_number;
               }else{
                    location.href = "/sale/customers";
               }

         });
}

$(document).ready(function () {
        $('.customer_row').click(function(){
            $('.customer_row_selected').removeClass('customer_row_selected');
            $(this).addClass('customer_row_selected');
        });
        handleAddButton();
        handleUpdateButton();
        handleDeleteButton();
        handlePagination();
        handleSearchButton();
        handleShowDetailCustomer();
});
