var dropdownDefaultSizeText = "Chọn cỡ";
var dropdownDefaultColorText = "Chọn màu";


function collectFilterParam(){
    var product_code = $('#search_product_code').val().trim();
    var param = "";
    if(product_code != ''){
        param+= "product_code=" + product_code;
    }
    return param;
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

      $('#list_product_btn_search').click(function(){
         var curr_url = location.href.toString().toLowerCase();
         curr_url = removeAllParam(curr_url);
         filter_param = collectFilterParam();
         curr_url = addParam(curr_url, filter_param);
         location.href = curr_url;
    });
}

function handleShowDetailProduct(){
    $('.show_detail').click(function(){
              var product_code = $(this).find('input').val();
              if (is_waiting_for_request){
                    return;
              }
              is_waiting_for_request = true;
               $.get("/marketing/detail-product?product_code=" + product_code.toString(), function(response) {
                        if(response['status'] == 200){
                               $('#dialog_edit_product').empty();
                               $('#dialog_edit_product').html(response['content']);
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

$(document).ready(function () {
    handlePagination();
    handleSearchButton();
    handleShowDetailProduct();
});