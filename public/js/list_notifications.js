var clickedInNotificationBox = false;

function handleCBMark(){
    $(".cb_mark").click(function() {
            var notification_id = $(this).val();
            var unread = 1;
            if($(this).is(":checked")){
                unread = 0;
            }

            data = {
                "notification_ids" : JSON.stringify([notification_id]),
                "list_unread" : JSON.stringify([unread]),
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
             $.post('/common/update-notification/', data, function(response) {
                  if(response['status'] == 200){
                        var count_message = response['count_message_unread'];
                        update_menu_notification(count_message);

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
function handleMarkAll(){
      $('#btn_mark_all').click(function() {
        var list_notification_ids = [];
        var list_unread = [];
        $(".cb_mark").each(function() {
            if(!$(this).is(":checked")){
                list_notification_ids.push($(this).val());
                list_unread.push(0);
                $(this).prop('checked',true);
            }
        });
        if (list_notification_ids.length > 0){
            data = {
                "notification_ids" : JSON.stringify(list_notification_ids),
                "list_unread" : JSON.stringify(list_unread),
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
             $.post('/update-notification/', data, function(response) {
                  if(response['status'] == 200){
                        var count_message = response['count_message_unread'];
                        update_menu_notification(count_message);
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


function handleFilterButton(){
    $('#btn_filter').click(function() {
           if($(this).text().trim() == "Xem Tất Cả"){
                $(this).text("Lọc Chưa Đọc")
                $(".remind_item").each(function() {
                    $(this).css("display", "block");

                });
            $('#dialog_list_notifications #list_notifications .remind_item').css("width", "100%");
            $('#dialog_list_notifications #list_notifications .remind_item .content').css("width", "382px");
           }else{
                $(this).text("Xem Tất Cả")
                 $(".remind_item").each(function() {
                    if($(this).find(".cb_mark").is(":checked")){
                        $(this).css("display", "none");
                    }

                });
           }
     });
}

function handleClickedOutOfNotificationDialog(){
    $('#dialog_list_notifications_content').click(function() {
            clickedInNotificationBox = true;
     });

    $('#dialog_list_notifications').click(function() {

        if(!clickedInNotificationBox){
            $('#dialog_notification').empty();
        }
        clickedInNotificationBox = false;
     });
}

$(document).ready(function () {
    handleCBMark();
    handleMarkAll();
    handleFilterButton();
    handleClickedOutOfNotificationDialog();
});
