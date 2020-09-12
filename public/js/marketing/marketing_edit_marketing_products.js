is_waiting_for_request = false;

var list_campaign_names = [];
var list_campaign_name_ids = [];
var list_bank_accounts = [];
var list_bank_account_ids = [];


function genColumnBankAccountNumber(backAccountSelectedId, index){
    item_selected_index = -1;
    for(bank_account_index in list_bank_accounts){
         if(list_bank_account_ids[bank_account_index] == backAccountSelectedId){
                item_selected_index = bank_account_index;
                break;
         }
    }

    var colStr = '<td style="text-align:center;">';
    colStr += '<div class="dropdown tbl_detail_campaign_item_updating detail_campaign_updating_bank_account"';
    colStr += ' id="detail_campaign_updating_bank_account_' + index.toString() + '">';
    colStr += ' <input type="hidden" class="backAccountUpdatingSelectedId" value="'+backAccountSelectedId.toString()+'" >';
     colStr += '<input type="hidden" class="backAccountViewSelectedId" value="'+backAccountSelectedId.toString()+'" >';
    colStr += '<button class="btn btn-secondary dropdown-toggle" type="button"';
    colStr += ' id="detail_campaign_updating_bank_account_text_'+ index.toString() + '"';
    colStr += ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    colStr += list_bank_accounts[item_selected_index];
    colStr += '</button>';
    colStr+= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    for(bank_account_index in list_bank_accounts){
         colStr+= '<a class="dropdown-item">'+list_bank_accounts[bank_account_index]+ ' <input type="hidden" value="'+list_bank_account_ids[bank_account_index]+'" >' +'</a>';
    }
    colStr+= '</div>';
    colStr+= ' </div>';
    colStr+= ' <div class="tbl_detail_campaign_item_view"';
    colStr+= ' id="detail_campaign_view_bank_account_' + index.toString() + '">' + list_bank_accounts[item_selected_index];
    colStr+= '</div>';
    colStr +="</td>";
    return colStr;
}

function genColumnCampaignNameNumber(campaignNameSelectedId, index){
    item_selected_index = -1;
    for(campaign_index in list_campaign_name_ids){
         if(list_campaign_name_ids[campaign_index] == campaignNameSelectedId){
                item_selected_index = campaign_index;
                break;
         }
    }

    var colStr = '<td style="text-align:center;">';
    colStr += '<div class="dropdown tbl_detail_campaign_item_updating detail_campaign_updating_campaign_name"';
    colStr += ' id="detail_campaign_updating_campaign_name_' + index.toString() + '">';
    colStr += '<input type="hidden" class="campaignNameUpdatingSelectedId" value="'+campaignNameSelectedId.toString()+'" >';
    colStr += '<input type="hidden" class="campaignNameViewSelectedId" value="'+campaignNameSelectedId.toString()+'" >';
    colStr += '<button class="btn btn-secondary dropdown-toggle" type="button"';
    colStr += ' id="detail_campaign_updating_campaign_name_text_'+ index.toString() + '"';
    colStr += ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    colStr +=  list_campaign_names[campaign_index];
    colStr += '</button>';
    colStr+= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    for(campaign_name_index in list_campaign_names){
         colStr+= '<a class="dropdown-item">'+list_campaign_names[campaign_name_index]+ ' <input type="hidden" value="'+list_campaign_name_ids[campaign_name_index]+'" >' +'</a>';
    }
    colStr+= '</div>';
    colStr+= ' </div>';
    colStr+= ' <div class="tbl_detail_campaign_item_view"';
    colStr+= ' id="detail_campaign_view_campaign_name_' + index.toString() + '">' + list_campaign_names[campaign_index];
    colStr+= '</div>';
    colStr +="</td>";
    return colStr;
}


function genColumnBudget(budget, index){
     var colStr = '<td style="text-align:center;">';
    colStr += '<input type="number" class="form-control tbl_detail_campaign_item_updating" placeholder="Nhập ngân sách" min="1" value="1"';
    colStr += ' style="margin: 0 auto;width:70%"';
    colStr += '  id="detail_campaign_updating_budget_'+ index.toString() + '">';
    colStr += '<label class="tbl_detail_campaign_item_view"';
    colStr+= ' id="detail_campaign_view_budget_' + index.toString() + '">' + budget.toString();
    colStr+= '</label>';
    colStr +="</td>";
    return colStr;
}

function genColumnComment(totalComment, index){
     var colStr = '<td style="text-align:center;">';
    colStr += '<input type="number" class="form-control tbl_detail_campaign_item_updating" placeholder="Nhập số comment" min="1" value="1"';
    colStr += ' style="margin: 0 auto;width:70%"';
    colStr += '  id="detail_campaign_updating_comment_'+ index.toString() + '">';
    colStr += '<label class="tbl_detail_campaign_item_view"';
    colStr+= ' id="detail_campaign_view_comment_' + index.toString() + '">' + totalComment.toString();
    colStr+= '</label>';
    colStr +="</td>";
    return colStr;
}

function genColumnUpdateDeleteButton(index){
    var colStr = '<td style="text-align:center;">';
    colStr += '<table table width="80%" style="margin-left:auto;margin-right:auto;">';
    colStr += '<tr>';
    colStr += '<td>';
    colStr += '<button type="button" class="btn btn-success detail_campaign_btn_update" value="'+index.toString() + '">Sửa</button>';
    colStr += '</td>';
    colStr += '<td>';
       colStr += '<button type="button" class="btn btn-success detail_campaign_btn_delete" value="'+index.toString() + '">Xóa</button>';
    colStr += '</td>';
    colStr += '</tr>';
    colStr += '</table>';
    colStr +="</td>";
    return colStr;
}

function genRow(campaignNameSelectedId, backAccountSelectedId, budget, totalComment, index){
    var rowStr = '<tr class="tbl_detail_campaign_item" id="tbl_detail_campaign_item_'+index.toString()+'">'
    rowStr += genColumnCampaignNameNumber(campaignNameSelectedId, index);
    rowStr += genColumnBankAccountNumber(backAccountSelectedId, index);
    rowStr += genColumnBudget(budget, index);
    rowStr += genColumnComment(totalComment, index);
    rowStr+= genColumnUpdateDeleteButton(index);
    rowStr +="</tr>";
    return rowStr;
}

function validateCampaign(backAccountSelectedId, campaignNameSelectedId, budget, totalComment){
     var message = "";
       if(backAccountSelectedId == '-1'){
            message = "Số thẻ không được để rỗng";
            showMessage(message);
            return false;
       }

       if(campaignNameSelectedId == '-1'){
            message = "Tên chiến dịch không được để rỗng";
            showMessage(message);
            return false;
       }
       try{
            var budget_value = parseFloat(budget);
            if(isNaN(budget_value)){
                message = "Ngân sách phải là một số";
                showMessage(message);
                return false;
            }
            if(budget < 0){
                  message = "Ngân sách phải lớn hơn 0";
            showMessage(message);
            return false;
            }
       }catch(err){
             message = "Ngân sách phải là một số";
            showMessage(message);
            return false;
       }

       try{
            var total_comment_value = parseInt(totalComment);
            if(isNaN(total_comment_value) || total_comment_value != parseFloat(totalComment)){
                message = "Số comment phải là một số nguyên";
                showMessage(message);
                return false;
            }
            if(total_comment_value < 0){
                  message = "Số comment phải lớn hơn 0";
            showMessage(message);
            return false;
            }
       }catch(err){
            message = "Số comment phải là một số nguyên";
            showMessage(message);
            return false;
       }
       return message == "";
}


function updateRow(id){
    var bank_account_number_id_selected = "detail_campaign_updating_bank_account_" + id;
    var campaign_name_id_selected = "detail_campaign_updating_campaign_name_" + id;
    var budget_id_selected = "detail_campaign_updating_budget_" + id;
    var comment_id_selected = "detail_campaign_updating_comment_" + id;

    var view_bank_account_number_id_selected = "detail_campaign_view_bank_account_" + id;
    var view_budget_id_selected = "detail_campaign_view_budget_" + id;
    var view_comment_id_selected= "detail_campaign_view_comment_" + id;
    var view_campaign_name_id_selected = "detail_campaign_view_campaign_name_" + id;

    var detail_campaign_updating_campaign_name = "detail_campaign_updating_campaign_name_" + id;
    $('#'+detail_campaign_updating_campaign_name).find('.campaignNameViewSelectedId').val($('#'+detail_campaign_updating_campaign_name).find('.campaignNameUpdatingSelectedId').val());

    var detail_campaign_updating_bank_account = "detail_campaign_updating_bank_account_" + id;
    $('#'+detail_campaign_updating_bank_account).find('.backAccountViewSelectedId').val($('#'+detail_campaign_updating_bank_account).find('.backAccountUpdatingSelectedId').val());


    $('.tbl_detail_campaign_item_updating').each(function(){
         if([bank_account_number_id_selected,budget_id_selected, comment_id_selected, campaign_name_id_selected].indexOf($(this).attr('id')) >=0){
            if($(this).attr('id') === campaign_name_id_selected){
                $('#'+view_campaign_name_id_selected).text($('#detail_campaign_updating_campaign_name_text_'+ id.toString()).text());
            }
             if($(this).attr('id') === bank_account_number_id_selected){
                $('#'+view_bank_account_number_id_selected).text($('#detail_campaign_updating_bank_account_text_'+ id.toString()).text());
            }

            if($(this).attr('id') === budget_id_selected){
                $('#'+view_budget_id_selected).text($(this).val());
            }
            if($(this).attr('id') === comment_id_selected){
                $('#'+view_comment_id_selected).text($(this).val());
            }
      }
    });
}

function showRowInViewMode(){
        $('.tbl_detail_campaign_item_updating').each(function(){
            $(this).css('display', 'none');
         });

        $('.tbl_detail_campaign_item_view').each(function(){
              $(this).css('display', 'block');
        });
        $('.detail_campaign_btn_delete').each(function(){
            $(this).text("Xóa");
        });
        $('.detail_campaign_btn_update').each(function(){
            $(this).text("Sửa");
        });
}

function showRowInUpdateMode(id){
    var bank_account_number_id_selected = "detail_campaign_updating_bank_account_" + id;
    var campaign_name_id_selected = "detail_campaign_updating_campaign_name_" + id;
    var budget_id_selected = "detail_campaign_updating_budget_" + id;
    var comment_id_selected = "detail_campaign_updating_comment_" + id;

    var view_bank_account_number_id_selected = "detail_campaign_view_bank_account_" + id;
    var view_budget_id_selected = "detail_campaign_view_budget_" + id;
    var view_comment_id_selected= "detail_campaign_view_comment_" + id;
    var view_campaign_name_id_selected = "detail_campaign_view_campaign_name_" + id;

    $('.tbl_detail_campaign_item_updating').each(function(){
     if([bank_account_number_id_selected,budget_id_selected, comment_id_selected, campaign_name_id_selected].indexOf($(this).attr('id')) >=0){

        $(this).css('display', 'block');
        if($(this).attr('id') === campaign_name_id_selected){
            $('#detail_campaign_updating_campaign_name_text_'+ id.toString()).text(($('#'+view_campaign_name_id_selected).text()));
        }

        if($(this).attr('id') === bank_account_number_id_selected){
            $('#detail_campaign_view_bank_account_text_'+ id.toString()).text(($('#'+view_bank_account_number_id_selected).text()));
        }

        if($(this).attr('id') === comment_id_selected){
            $(this).val($('#'+view_comment_id_selected).text());
        }
        if($(this).attr('id') === budget_id_selected){
            $(this).val($('#'+view_budget_id_selected).text());
        }

     }else{
        $(this).css('display', 'none');
     }
  });


   $('.tbl_detail_campaign_item_view').each(function(){
        if([view_bank_account_number_id_selected,view_budget_id_selected, view_comment_id_selected, view_campaign_name_id_selected].indexOf($(this).attr('id')) >=0){
            $(this).css('display', 'none');
       }
  });
}


function handleUpdateBtnClicked(){
   var updateVal = $(this).val();
    var updateText = $(this).text();

    if(updateText === "Lưu"){
        var id = updateVal;
        var bank_account_selected_id = $( "#detail_campaign_updating_bank_account_" + id).find('.backAccountSelectedId').val();
        var campaignNameSelectedId = $( "#detail_campaign_updating_campaign_name_" + id).find('.campaignNameUpdatingSelectedId').val();
        var budget_id_selected = "detail_campaign_updating_budget_" + id;
        var comment_id_selected = "detail_campaign_updating_comment_" + id;

        var budget = $('#'+budget_id_selected).val().trim();
        var totalComment = $('#'+comment_id_selected).val().trim();

        if(validateCampaign(bank_account_selected_id, campaignNameSelectedId, budget, totalComment)){
            updateRow(updateVal);
            showRowInViewMode();
        }
    }else{
        showRowInViewMode();
        showRowInUpdateMode(updateVal);
        $(this).text("Lưu");
        $('.detail_campaign_btn_delete').each(function(){
                if($(this).val() == updateVal){
                    $(this).text("Huỷ");
                }
        });
    }


}


function handleDeleteBtnClicked(){
    var deleteText = $(this).text();
    if(deleteText.trim() ==="Xóa"){
        var delete_col_id = "tbl_detail_campaign_item_" + $(this).val();
        $('.tbl_detail_campaign_item').each(function() {
            if($(this).attr('id') === delete_col_id){
                $(this).remove();
            }
        });
    }else{
        showRowInViewMode();
    }
}

function updatingRowBankAccountSelected(){
    $(this).parent().parent().find('button').text($(this).text());
    $(this).parent().parent().find('.backAccountUpdatingSelectedId').val($(this).find('input').val());
}

function updatingRowCampaignNameSelected(){
     $(this).parent().parent().find('button').text($(this).text());
     $(this).parent().parent().find('.campaignNameUpdatingSelectedId').val($(this).find('input').val());

}
function addNewCampaign(){
       var backAccountSelectedId = $('#detail_campaign_additional_bank_account_selected_id').val().trim();
       var campaignNameSelectedId = $('#detail_campaign_additional_campaign_name_selected_id').val().trim();
       var budget = $('#detail_campaign_additional_budget').val().trim();
       var totalComment = $('#detail_campaign_additional_comment').val().trim();

       if(validateCampaign(backAccountSelectedId, campaignNameSelectedId, budget, totalComment)){

             var row_index = $('.tbl_detail_campaign_item').length;
             $('#row_additional_detail_campaign').after(genRow(campaignNameSelectedId, backAccountSelectedId,budget, totalComment, row_index));
             $('.detail_campaign_btn_update').first().click(handleUpdateBtnClicked);
             $('.detail_campaign_btn_delete').first().click(handleDeleteBtnClicked);
             $('.detail_campaign_updating_bank_account').first().find('a').click(updatingRowBankAccountSelected);
             $('.detail_campaign_updating_campaign_name').first().find('a').click(updatingRowCampaignNameSelected);


             $('#detail_campaign_additional_bank_account_text').text("___");
             $('#detail_campaign_additional_campaign_name_text').text("___");
              $('#detail_campaign_additional_campaign_name_selected_id').text("-1");
             $('#detail_campaign_additional_bank_account_selected_id').text("-1");
             $('#detail_campaign_additional_budget').val("0");
             $('#detail_campaign_additional_comment').val("1");
       }
}

function validateMarketingProduct(){
    var marketing_code = $('#edit_marketing_product_marketing_code').val().trim();
    var marketing_product_code = $('#edit_marketing_product_product_code').val().trim();
    var product_source_id = $('#edit_marketing_product_source_id').val().trim();
    var marketing_product_created = $('#edit_marketing_product_created_text').val().trim();

    if(marketing_code == ""){
        message = "Mã marketing không được để trống";
        showMessage(message);
        return false;
    }
    if(marketing_product_code ==""){
        message = "Mã sản phẩm không được để trống";
        showMessage(message);
        return false;
    }
    if(product_source_id == -1){
        message = "Nguồn không được để trống";
        showMessage(message);
        return false;
    }
    if(marketing_product_created == ""){
        message = "Ngày lập không được để trống";
        showMessage(message);
        return false;
    }




    var isInUpdatingMode = false;
        $('.tbl_detail_campaign_item_updating').each(function(){
            if($(this).css('display') != 'none'){
                isInUpdatingMode = true;
                return false;
            }
    });
    if (isInUpdatingMode){
            showMessage("Danh sách chiến dịch đang trong trạng thái chỉnh sửa.Vui lòng lưu lại để tiếp tục hoạt động khác");
            return false;
    }else{
         list_campaigns = collectListCampaigns();
            if(list_campaigns.length == 0){
                 showMessage("Sản phẩm marketing phải có ít nhất một chiến dịch");
                 return false;
            }
            return true;
        }
    try{
                var value = parseInt(budget);
                if(isNaN(value)){
                    message = "Bạn phải chọn trường nguồn";
                    showMessage(message);
                    return false;
                }
                if(value == -1){
                      message = "Bạn phải chọn trường nguồn";
                    showMessage(message);
                    return false;
                }
    }catch(err){
                 message = "Bạn phải chọn trường nguồn";
                showMessage(message);
                return false;
    }
    return true;
}

function collectListCampaigns(){

     var list_campaigns = [];
     var  ele_index = 0;

     $('.tbl_detail_campaign_item').each(function(){
        if(ele_index > 0){
        var id  = $(this).attr('id').replace('tbl_detail_campaign_item_', '');
        var campaign = {};

        var detail_campaign_updating_campaign_name = "detail_campaign_updating_campaign_name_" + id;
        var detail_campaign_updating_bank_account = "detail_campaign_updating_bank_account_" + id;

        campaign['campaign_name_id'] = $('#'+detail_campaign_updating_campaign_name).find('.campaignNameViewSelectedId').val().trim();
        campaign['bank_account_id'] = $('#'+detail_campaign_updating_bank_account).find('.backAccountViewSelectedId').val().trim();
        campaign['budget'] = $('#detail_campaign_view_budget_' +id).text().trim();
        campaign['total_comment'] = $('#detail_campaign_view_comment_' +id).text().trim();
        list_campaigns.push(campaign);
        }

        ele_index +=1;
     });
      return list_campaigns;

}


function saveMarketingProduct(){
        if(is_waiting_for_request){
           return;
        }
        if(validateMarketingProduct()){
                is_waiting_for_request = true;
                var marketing_product_id = $('#edit_marketing_product_id').val().trim();
                var product_code = $('#edit_marketing_product_product_code').val().trim();
                var marketing_source_id = $('#edit_marketing_product_source_id').val().trim();
                var marketing_product_created = $('#edit_marketing_product_created_text').val().trim();
                var marketing_code = $('#edit_marketing_product_marketing_code').val().trim();

                list_campaigns = collectListCampaigns();
                marketing_product = {
                    '_token': $('meta[name=csrf-token]').attr('content')
                }
                marketing_product['marketing_product_id'] = marketing_product_id;
                marketing_product['marketing_product_created'] = marketing_product_created;
                marketing_product['product_code'] = product_code;
                marketing_product['marketing_code'] = marketing_code;
                marketing_product['marketing_source_id'] = marketing_source_id;
                marketing_product['list_campaigns'] =JSON.stringify(list_campaigns);

                $.post('/marketing/save-marketing-product', marketing_product, function(response) {
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

}
function init(){

  $('#edit_marketing_product_product_source a').click(function(){
    $("#edit_marketing_product_source_id").val($(this).find('.marketing_source_id').val());
        $('#edit_marketing_product_product_source_text').text($(this).text());
    });

    $('#detail_campaign_additional_bank_account a').click(function(){
        $("#detail_campaign_additional_bank_account_selected_id").val($(this).find('input').val());
        $('#detail_campaign_additional_bank_account_text').text($(this).text());
    });
    $('#detail_campaign_additional_campaign_name a').click(function(){
        $("#detail_campaign_additional_campaign_name_selected_id").val($(this).find('input').val());
        $('#detail_campaign_additional_campaign_name_text').text($(this).text());
    });

    list_bank_accounts = []
    list_bank_account_ids = []

    $('#detail_campaign_additional_bank_account a').each(function(){
            list_bank_accounts.push($(this).text());
            list_bank_account_ids.push($(this).find('input').val());
    });

    list_campaign_names = []
    list_campaign_name_ids = []

    $('#detail_campaign_additional_campaign_name a').each(function(){
            list_campaign_names.push($(this).text());
            list_campaign_name_ids.push($(this).find('input').val());
    });
}
$(document).ready(function () {

    init();

    $('#edit_marketing_product_btn_cancel').click(function(){
        $("#edit_marketing_product_dialog").css('display', "none");
    });

     $('#detail_campaign_btn_add').click(function(){
            addNewCampaign();
    });
    $('#edit_marketing_product_btn_ok').click(function(){
           saveMarketingProduct();
    });
});
