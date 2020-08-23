var is_waiting_for_request = false;

function saveCampaignName(data){
     $.post('/admin/save-campaign-name/', data, function(response) {
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

function validate(campaign_name){
    if(campaign_name === ""){
      showMessage("Tên nguyên nhân không được để trống");
      return false;
    }

    return true;
}

function handleOkButton(){
     $('#add_campaign_name_btn_ok').click(function(){

        if(is_waiting_for_request){
            return;
        }

        var campaign_name = $('#campaign_name').val().trim();

        if(validate(campaign_name)){
            var campaign_name_id = $('#edit_campaign_name_id').val().trim();
            is_waiting_for_request = true;
            var data = {
                'campaign_name_id' : campaign_name_id,
                'name' : campaign_name,
                '_token': $('meta[name=csrf-token]').attr('content'),

            }

            saveCampaignName(data);
        }
     });
}
function handleCancelButton(){
     $('#add_campaign_name_btn_cancel').click(function(){
        $('#add_campaign_name_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    handleOkButton();
    handleCancelButton();
});
