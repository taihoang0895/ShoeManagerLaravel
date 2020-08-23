var is_waiting_for_request = false;

function collectAllOrdersSelected(){
    var list_order_ids = [];
     $('.cb_mark').each(function() {
         if ($(this).is(':checked')){
            list_order_ids.push($(this).parent().find('.order_id').val());
         }
     });
     return list_order_ids;
}

function handlePushOrderButton(){
    $('#list_orders_btn_add').click(function(){

                var list_order_ids = collectAllOrdersSelected();
                if (list_order_ids.length == 0){
                    showMessage("Vui lòng chọn ít nhất một hóa đơn để đẩy lên GHTK");
                }else{
                    if (is_waiting_for_request){
                        return;
                    }
                    is_waiting_for_request = true;
                    var data = {};
                    data['list_order_ids'] = JSON.stringify(list_order_ids);
                    $.get("/sale/form-prepare-order-deliver/",data, function(response) {
                        if(response['status'] == 200){
                               $('#form_order_delivering').empty();
                               $('#form_order_delivering').html(response['content']);
                               $('#form_order_delivering').css('display', 'block');

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

function handelSelectAllButton(){
    $('#cb_selected_all').click(function() {
        if ($(this).is(':checked')){
             $('.cb_mark').each(function() {
                 $(this).prop('checked', true);
             });
        }else{
            $('.cb_mark').each(function() {
               $(this).prop('checked', false);
            });
        }

    });

    $('.cb_mark').click(function() {
        var selectAll = true;
        $('.cb_mark').each(function() {
             if (!$(this).is(':checked')){
                selectAll = false;
                return false;
             }
        });
        if (selectAll){
             $('#cb_selected_all').prop('checked', true);
        }else{
              $('#cb_selected_all').prop('checked', false);
        }
    });

}


$(document).ready(function () {
      handelSelectAllButton();
      handlePushOrderButton();
});