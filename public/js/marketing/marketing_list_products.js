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
               $.get("/common/detail-product/?product_code=" + product_code.toString(), function(response) {
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

function handleAddButton() {
    $('#list_products_btn_add').click(function () {

        if (is_waiting_for_request) {
            return;
        }
        is_waiting_for_request = true;
        $.get("/marketing/form-add-product/", function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_product').empty();
                $('#dialog_edit_product').html(response['content']);
            } else {
                showMessage(response['message']);
            }
        })
            .fail(function () {

                showMessage("Lỗi mạng");
            })
            .always(function () {
                is_waiting_for_request = false;
            });
    });
}
function handleUpdateButton() {
    $('#list_products_btn_update').click(function () {
        if ($('.product_row_selected').length == 0) {
            showMessage("Vui lòng chọn một sản phẩm để sửa");
        } else {
            var product_code_id = $('.product_row_selected').first().attr('id');

            var product_code = product_code_id.replace("product_", "").trim();

            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;

            $.get("/marketing/form-update-product/?product_code=" + product_code.toString(), function (response) {
                if (response['status'] == 200) {
                    $('#dialog_edit_product').empty();
                    $('#dialog_edit_product').html(response['content']);
                } else {
                    showMessage(response['message']);
                }
            })
                .fail(function () {
                    showMessage("Lỗi mạng");
                })
                .always(function () {
                    is_waiting_for_request = false;
                });
        }
    });
}

function handleDeleteButton() {
    $('#list_products_btn_delete').click(function () {
        if ($('.product_row_selected').length == 0) {
            showMessage("Vui lòng chọn một sản phẩm để xoá");
        } else {
            $('#confirm_dialog_delete_product').css('display', 'block');
            $('#product_delete_dialog_btn_ok').click(function () {
                $('#confirm_dialog_delete_product').css('display', 'none');
                var product_code_id = $('.product_row_selected').first().attr('id');
                var product_code = product_code_id.replace("product_", "").trim();
                if (is_waiting_for_request) {
                    return;
                }
                is_waiting_for_request = true;
                var data = {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    "product_code": product_code.toString()
                };
                setupCSRF();
                $.post("/marketing/delete-product/", data, function (response) {
                    if (response['status'] == 200) {
                        var curr_url = location.href.toString().toLowerCase();
                        curr_url = removeAllParam(curr_url);
                        location.href = curr_url;
                    } else {
                        showMessage(response['message']);
                    }
                })
                    .fail(function () {
                        showMessage("Lỗi mạng");
                    })
                    .always(function () {
                        is_waiting_for_request = false;
                    });
            });
            $('#product_delete_dialog_btn_cancel').click(function () {
                $('#confirm_dialog_delete_product').css('display', 'none');
            });


        }
    });

}


$(document).ready(function () {
    $('.product_row').click(function () {
        $('.product_row_selected').removeClass('product_row_selected');
        $(this).addClass('product_row_selected');
    });
    handlePagination();
    handleSearchButton();
    handleShowDetailProduct();
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
});
