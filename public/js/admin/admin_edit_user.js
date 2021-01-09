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

function handleChangeDepartment(){
    $(".storage_address").css("display", "")
    if($('#dropdown_user_department_text').text() == "Kho Vũ Ngọc Phan"){
        $("#storage_address").text("LK01, 25 Vũ Ngọc Phan, Đống Đa, Hà Nội, Phố Vũ Ngọc Phan, Quận Đống Đa, Hà Nội");
    }else{
        if($('#dropdown_user_department_text').text() == "Kho Xã Đàn"){
            $("#storage_address").text("136A Xã Đàn,PHƯƠNG LIÊN ,ĐỐNG ĐA.HN,PHƯỜNG PHƯƠNG LIÊN, HÀ NỘI, QUẬN ĐỐNG ĐA.");
        }else{
            if($('#dropdown_user_department_text').text() == "Kho Xuân La"){
                $("#storage_address").text("340 Lạc Long Quân");
            }else{
                $(".storage_address").css("display", "none")
            }

        }
    }

}

$(document).ready(function () {
    handleChangeDepartment();

    $('#dropdown_user_role a').click(function(){
        $('#dropdown_user_role_text').text($(this).text());
    });
    $('#dropdown_user_department a').click(function(){
        $('#dropdown_user_department_text').text($(this).text());
        handleChangeDepartment();
    });

    $('#dropdown_storage_address a').click(function(){
        $('#dropdown_storage_address_text').text($(this).text());
    });

    handleOkButton();
    handleCancelButton();
});
