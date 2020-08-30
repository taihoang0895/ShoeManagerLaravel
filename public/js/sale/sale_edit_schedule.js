var is_waiting_for_request = false;

function saveRemind(data){
     $.post('/sale/save-schedule', data, function(response) {
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
function validate(time, note){
    if(time == ''){
      showMessage("Thời gian không được để trống");
      return false;
    }

    if(note == ''){
      showMessage("Ghi chú không được để trống");
      return false;
    }

    return true;
}
$(document).ready(function () {
 $('#add_schedule_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var time = $('#schedule_time_text').val().trim();
        var note = $('#schedule_note').val().trim();
        if(validate(time, note)){

            is_waiting_for_request = true;
            var schedule_id = $('#add_schedule_id').val();
            var data = {
                'schedule_id' : schedule_id,
                'time' : time,
                'note' : note,
                '_token': $('meta[name=csrf-token]').attr('content')
            }
            saveRemind(data);
        }
     });

    $('#add_schedule_btn_cancel').click(function(){
        $('#add_schedule_dialog').css('display', 'none');
    });
});
