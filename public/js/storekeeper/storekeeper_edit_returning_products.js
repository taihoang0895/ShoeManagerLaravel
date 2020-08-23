var is_waiting_for_request = false;


function handleSaveButton(){
    $('#edit_returning_product_btn_ok').click(function(){
            if(is_waiting_for_request){
                return;
            }

            if(validateData()){
                is_waiting_for_request = true;
                var returning_product_id = $("#edit_returning_product_id").val().trim();
                var product_code = $("#product_code").val().trim();
                var product_size = $("#edit_product_size_text").text().trim();
                var product_color = $("#edit_product_color_text").text().trim();
                var product_quantity  = $('#product_quantity').val().trim();
                var note  = $('#note').val().trim();

                var data = {
                    'returning_product_id' : returning_product_id,
                    'product_code' : product_code,
                    'product_size' : product_size,
                    'product_color' : product_color,
                    'product_quantity' : product_quantity,
                    'note' : note,
                    '_token': $('meta[name=csrf-token]').attr('content'),
                }
                $.post('/storekeeper/save-returning-product/', data, function(response) {
                  if(response['status'] == 200){
                        location.reload();
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

function handleCancelButton(){
      $('#edit_returning_product_btn_cancel').click(function(){
             $('#edit_returning_product_dialog').css('display', 'none');
      });
}

function validateData(){
     var product_code = $("#product_code").val().trim();
     var product_size = $("#edit_product_size_text").text().trim();
     var product_color = $("#edit_product_color_text").text().trim();
     var product_quantity  = $('#product_quantity').val().trim();

     if(product_code == ''){
        showMessage("MSP không được để rỗng");
        return false;
     }
     if(!isInteger(product_quantity)){
        showMessage("Số Lượng phải là một số nguyên dương");
        return false;
     }
     if(product_quantity <= 0){
        showMessage("Số lương phải lớn hơn 0");
        return false;
     }
     return true;
}

function init(){

      $('.returning_product_row').click(function(){
            $('.returning_products_row_selected').removeClass('returning_products_row_selected');
            $(this).addClass('returning_products_row_selected');
        });
      $('#edit_product_color a').click(function(){
             $('#edit_product_color_text').text($(this).text());
      });

      $('#edit_product_size a').click(function(){
             $('#edit_product_size_text').text($(this).text());
      });

}


init();
handleSaveButton();
handleCancelButton();
