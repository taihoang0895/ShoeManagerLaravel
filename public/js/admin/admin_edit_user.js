function validate(username, password, alias_name, department_name, role_name){
    if(username === ""){
        showMessage("Tài khoản không được để rỗng");
        return false;
    }
    if(password === ""){
         showMessage("Mật khẩu không được để rỗng");
        return false;
    }
    if(alias_name === ""){
         showMessage("Danh tính không được để rỗng");
        return false;
    }
    if(department_name === ""){
         showMessage("Phòng ban không được để rỗng");
        return false;
    }
    if(role_name === ""){
         showMessage("Chức vụ không được để rỗng");
        return false;
    }
    return true;

}

function saveUser(data){
     $.post('/admin/save-user/', data, function(response) {
              if(response['status'] === 200){
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

function handleOkButton(){

 $('#edit_user_btn_ok').click(function(){
        if(is_waiting_for_request){
            return;
        }
        var username = $('#user_name').val().trim();
        var password = $('#password').val().trim();
        var alias_name = $('#alias_name').val().trim();
        var department_name = $('#dropdown_user_department_text').text().trim();
        var role_name = $('#dropdown_user_role_text').text().trim();


        if(validate(username, password, alias_name,department_name,role_name)){
            is_waiting_for_request = true;
            var user_id = $('#edit_user_id').val();
            var data = {
                'user_id' : user_id,
                'username' : username,
                'password' : password,
                'alias_name' : alias_name,
                'department_name' :department_name,
                'role_name' :role_name,
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
            saveUser(data);
        }
     });

}
function handleCancelButton(){
    $('#edit_user_btn_cancel').click(function(){
        $('#edit_user_dialog').css('display', 'none');
    });
}
$(document).ready(function () {
    $('#dropdown_user_role a').click(function(){
        $('#dropdown_user_role_text').text($(this).text());
    });
     $('#dropdown_user_department a').click(function(){
        $('#dropdown_user_department_text').text($(this).text());
    });
    handleOkButton();
    handleCancelButton();
});