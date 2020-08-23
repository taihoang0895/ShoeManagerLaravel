$(document).ready(function () {
     $('#confirm_dialog_btn_positive').mousedown(function(){
            $(this).css('opacity', '0.5');
     });
     $('#confirm_dialog_btn_negative').mousedown(function(e){
               $(this).css('opacity', '0.5');
     });




     $(document).mouseup(function(){
        $('#confirm_dialog_btn_positive').css('opacity', '1');
        $('#confirm_dialog_btn_negative').css('opacity', '1');
    });

     $('#confirm_dialog_btn_positive').click(function(){
             $("#confirm_dialog").css('display', 'none');
        });
     $('#confirm_dialog_btn_negative').click(function(){
             $("#confirm_dialog").css('display', 'none');
         });

});
