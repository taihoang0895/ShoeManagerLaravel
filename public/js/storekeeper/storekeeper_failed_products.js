var is_waiting_for_request = false;

function collectFilterParam(){
    var filter_time = $('#failed_product_filter_time_text').val().trim();

    var product_code = $('#filter_product_code').val().trim();
    var product_size = $('#filter_product_size_text').text().trim();
    var product_color = $('#filter_product_color_text').text().trim();
    var param = "";
    if(filter_time != ''){
        param = 'filter_time='+filter_time;
    }

    if (product_code != ""){
        param += "&product_code="+product_code
    }
    if (!product_size.startsWith("_")){
        param += "&product_size="+product_size
    }
    if (!product_color.startsWith("_")){
        param += "&product_color="+product_color
    }
    if(param.startsWith("&")){
        param = param.substring(1);
    }
    return param;
}

function handleAddButton(){
    $('#failed_products_btn_add').click(function(){
             if (is_waiting_for_request){
                return;
              }
              is_waiting_for_request = true;
              $.get("/storekeeper/form-add-failed-product/", function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_failed_product').empty();
                               $('#dialog_edit_failed_product').html(response['content']);
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
     $('#failed_products_btn_update').click(function(){

           if (is_waiting_for_request){
                return;
           }
           if($('.failed_products_row_selected').length == 0){
                    showMessage("Vui lòng chọn một hàng để sửa");
           }else{
                    var failed_product_id = $('.failed_products_row_selected').first().find('.failed_product_id').val();
                    var data = {
                        "failed_product_id" : failed_product_id
                    }
                    var url = '/storekeeper/form-update-failed-product/';
                    is_waiting_for_request = true;
                    $.get(url,data, function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_failed_product').empty();
                               $('#dialog_edit_failed_product').html(response['content']);
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

function handleDeleteButton(){
     $('#failed_product_delete_dialog_btn_cancel').click(function(){
            $('#confirm_dialog_delete_failed_product').css('display', 'none');
         });
     $('#failed_product_delete_dialog_btn_ok').click(function(){
              $('#confirm_dialog_delete_failed_product').css('display', 'none');
               if (is_waiting_for_request){
                    return;
                }
                var failed_product_id = $('.failed_products_row_selected').first().find('.failed_product_id').val();
                var data = {
                        "failed_product_id" : failed_product_id,
                    '_token': $('meta[name=csrf-token]').attr('content'),
                }
                setupCSRF();
                is_waiting_for_request = true;
                $.post('/storekeeper/delete-failed-product/', data, function(response) {
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
     $('#failed_products_btn_delete').click(function(){

           if($('.failed_products_row_selected').length == 0){
                    showMessage("Vui lòng chọn một hàng để xóa");
              }else{
                   $('#confirm_dialog_delete_failed_product').css('display', 'block');
              }
      });

}

function handleFilterButton(){
     $('#failed_product_btn_filter').click(function(){

             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             curr_url = normalize(curr_url);
             location.href = curr_url;

    });
}
function init(){

      $('.failed_product_row').click(function(){
            $('.failed_products_row_selected').removeClass('failed_products_row_selected');
            $(this).addClass('failed_products_row_selected');
        });
      $('#filter_product_size a').click(function(){
             $('#filter_product_size_text').text($(this).text());
      });
      $('#filter_product_color a').click(function(){
             $('#filter_product_color_text').text($(this).text());
      });


}
$(document).ready(function () {
    init();
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handlePagination();
    handleFilterButton();
});
