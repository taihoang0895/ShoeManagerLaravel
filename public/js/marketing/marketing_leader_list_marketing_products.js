var is_waiting_for_request = false;


function handelShowDetailMarketingProduct(){
     $('.tbl .show_detail_markting_product').click(function(){
           var marketing_product_id = $(this).find('input').val();
         data = {
             "marketing_product_id": marketing_product_id
         }
          $.get('/marketing/detail-marketing-product/', data, function(response) {
                          if(response['status'] == 200){
                                $('#dialog_edit_marketing_product').empty();
                                $('#dialog_edit_marketing_product').html(response['content']);
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
           return false;
      });
}
function handelBtnAddMarketingProduct(){
    $('#list_marketing_products_btn_add').click(function(){
         if(is_waiting_for_request){
                return;
         }

          data = {}
          is_waiting_for_request = true;
          $.get('/marketing/form-add-marketing-product', data, function(response) {
                          if(response['status'] == 200){
                                $('#dialog_edit_marketing_product').empty();
                                $('#dialog_edit_marketing_product').html(response['content']);
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

function handleBtnDeleteMarketingProduct(){
     $('#list_marketing_products_btn_delete').click(function(){
      if($('.marketing_product_row_selected').length == 0){
           showMessage("Vui lòng chọn một sản phẩm để xoá");
      }else{
          $('#confirm_dialog_delete_marketing_product').css('display', 'block');
      }
    });

    $('#marketing_product_delete_dialog_btn_ok').click(function(){
         $('#confirm_dialog_delete_marketing_product').css('display', 'none');
                if (is_waiting_for_request){
                    return;
                }
                var marketing_product_id = $('.marketing_product_row_selected').first().attr('id');
                marketing_product_id = marketing_product_id.replace("marketing_product_row_", "");

                var data = {
                        'marketing_product_id' : marketing_product_id
                    }
                setupCSRF();
                is_waiting_for_request = true;
                $.post('/marketing/delete-marketing-product', data, function(response) {
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
     $('#marketing_product_delete_dialog_btn_cancel').click(function(){
        $('#confirm_dialog_delete_marketing_product').css('display', 'none');
    });
}

function handleBtnUpdateMarketingProduct(){
    $('#list_marketing_products_btn_update').click(function(){
         if(is_waiting_for_request){
                return;
         }

         if($('.marketing_product_row_selected').length == 0){
                    showMessage("Vui lòng chọn một sản phẩm để sửa");
              }else{
                    var marketing_product_id = $('.marketing_product_row_selected').first().attr('id');
                    marketing_product_id = marketing_product_id.replace("marketing_product_row_", "");
                    var url = '/marketing/form-update-marketing-product/' + marketing_product_id;
                    is_waiting_for_request = true;
                    $.get(url, function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_marketing_product').empty();
                                $('#dialog_edit_marketing_product').html(response['content']);
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

function collectFilterParam(){
    var start_time = $('#marketing_product_filter_start_time_text').val().trim();
    var end_time = $('#marketing_product_filter_end_time_text').val().trim();
    var filter_marketing_source_id = $('#filter_marketing_source_id_selected').val().trim();
     var filter_member_id = $('#filter_member_id_selected').val().trim();
    var search_product_code = $('#search_product_code').val().trim();
    var param = "";
    if(start_time != '' && end_time != ''){
        param = 'start_time='+start_time +"&"+"end_time=" +end_time;
    }
    if (filter_marketing_source_id != "-1"){
        param += "&marketing_source_id="+filter_marketing_source_id
    }
    if (filter_member_id != "-1"){
        param += "&filter_member_id="+filter_member_id
    }
    if(search_product_code != ""){
        param += "&search_product_code=" + search_product_code
    }
    if(param.startsWith("&")){
        param = param.substring(1);
    }

    return param;
}

function handleMarketingProductFilter(){
     $('#marketing_product_btn_filter').click(function(){
        var start_time = $('#marketing_product_filter_start_time_text').val().trim();
        var end_time = $('#marketing_product_filter_end_time_text').val().trim();

        if(validateTimeRangeFilter(start_time, end_time)){
            var curr_page = parseInt($('#curr_page').val());
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             location.href = normalize(curr_url);
        }

    });
}

function handlePagination(){
    $('#previous_page').click(function(){
    alert();
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
                    filter_param = collectFilterParam();
                    if (filter_param != ''){
                        curr_url = addParam(curr_url, filter_param);
                    }
                    location.href = normalize(curr_url);
             }

    });

}

function init(){
    $('.marketing_product_row').click(function(){
            $('.marketing_product_row_selected').removeClass('marketing_product_row_selected');
            $(this).addClass('marketing_product_row_selected');
     });

    $('#filter_marketing_source_dropdown_state a').click(function(){
        $('#filter_marketing_source_dropdown_state_text').text($(this).text());
        $('#filter_marketing_source_id_selected').val($(this).find('input').val());
    });

    $('#filter_by_member a').click(function(){
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });
}
$(document).ready(function () {
    init();
    handelShowDetailMarketingProduct();
    handelBtnAddMarketingProduct();
    handleBtnDeleteMarketingProduct();
    handleBtnUpdateMarketingProduct();
    handleMarketingProductFilter();
    handlePagination();
});
