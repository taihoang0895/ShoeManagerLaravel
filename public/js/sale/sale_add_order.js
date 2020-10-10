var dropdownDefaultSizeText = "___";
var dropdownDefaultColorText = "___";
var dropdownDefaultDiscountText = "___";
var list_product_color = [];
var list_product_size = [];
var list_product_discount = [];
var list_product_id = [];
is_waiting_for_request = false;

function genColumnProductCode(productCode, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<input class="form-control tbl_detail_order_item_updating" type="text"';
    colStr += ' id="detail_order_updating_product_code_' + index.toString() + '"';
    colStr += ' placeholder="Nhập mã sản phẩm" style="width:80%;margin: 0 auto;">';
    colStr += '<div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_product_code_' + index.toString() + '">' + productCode + '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnProductSize(productSize, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<div class="dropdown tbl_detail_order_item_updating detail_order_updating_product_size"';
    colStr += ' id="detail_order_updating_product_size_' + index.toString() + '">';
    colStr += '<button class="btn btn-secondary dropdown-toggle" type="button"';
    colStr += ' id="dropdown_detail_order_product_size_' + index.toString() + '"';
    colStr += ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    colStr += productSize;
    colStr += '</button>';
    colStr += '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    for (size_index in list_product_size) {
        colStr += '<a class="dropdown-item">' + list_product_size[size_index] + '</a>';
    }
    colStr += '</div>';
    colStr += ' </div>';
    colStr += ' <div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_product_size_' + index.toString() + '">' + productSize;
    colStr += '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnActuallyCollected(actuallyCollected, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<input class="form-control tbl_detail_order_item_updating" type="number" min="0"';
    colStr += ' id="detail_order_updating_actually_collected_' + index.toString() + '"';
    colStr += ' value="0" style="margin: 0 auto;">';
    colStr += '<div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_actually_collected_' + index.toString() + '">' + actuallyCollected + '</div>';
    colStr += "</td>";
    return colStr;

}

function genColumnProductColor(productColor, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<div class="dropdown tbl_detail_order_item_updating detail_order_updating_product_color"';
    colStr += ' id="detail_order_updating_product_color_' + index.toString() + '">';
    colStr += '<button class="btn btn-secondary dropdown-toggle" type="button"';
    colStr += ' id="dropdown_detail_order_product_color_' + index.toString() + '"';
    colStr += ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    colStr += productColor;
    colStr += '</button>';
    colStr += '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    for (color_index in list_product_color) {
        colStr += '<a class="dropdown-item">' + list_product_color[color_index] + '</a>';
    }
    colStr += '</div>';
    colStr += ' </div>';
    colStr += ' <div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_product_color_' + index.toString() + '">' + productColor;
    colStr += '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnDiscount(discount_id, discount, index) {
    var view_text = discount;
    var edit_text = discount;
  /*  if (discount == dropdownDefaultDiscountText) {
        edit_text = dropdownDefaultDiscountText;
        view_text = "";
    }*/


    var colStr = '<td style="text-align:center;">';
    /*  colStr += '<input type="hidden" id="detail_order_view_discount_id_' + index.toString() + '" value=-1>';
      colStr += '<div class="dropdown tbl_detail_order_item_updating detail_order_updating_discount"';
      colStr += ' id="detail_order_updating_product_discount_' + index.toString() + '">';
      colStr += '<button class="btn btn-secondary dropdown-toggle" type="button"';
      colStr += ' id="dropdown_discount_code_' + index.toString() + '"';
      colStr += ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
      colStr += edit_text;
      colStr += '</button>';
      colStr += '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
      for (product_discount_index in list_product_discount) {
          var id = "detail_order_options_discount_id_" + index.toString();
          var discount_id_value_tag = '<input type="hidden" id="discount_id" value="' + list_product_id[product_discount_index].toString() + '">';
          colStr += '<a class="dropdown-item tbl_detail_order_item_view" id="' + id + '">' + discount_id_value_tag + list_product_discount[product_discount_index] + '</a>';
      }
      colStr += '</div>';
      colStr += ' </div>';*/
    colStr += '<input type="hidden" id="detail_order_view_discount_id" value=' + discount_id + '>';
    colStr += ' <div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_product_discount_' + index.toString() + '">' + view_text;
    colStr += '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnQuantity(quantity, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<input type="number" class="form-control tbl_detail_order_item_updating" min="1" value="1"';
    colStr += ' style="margin: 0 auto;text-align:center;"';
    colStr += '  id="detail_order_updating_product_quantity_' + index.toString() + '">';
    colStr += '<div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_product_quantity_' + index.toString() + '">' + quantity.toString();
    colStr += '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnPrice(price, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += price;
    colStr += "</td>";
    return colStr;
}

function genColumnPickMoney(pickMoney, index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<input class="form-control tbl_detail_order_item_updating" type="number" min="0"';
    colStr += ' id="detail_order_updating_pick_money_' + index.toString() + '"';
    colStr += ' value="0" style="margin: 0 auto;">';
    colStr += '<div class="tbl_detail_order_item_view"';
    colStr += ' id="detail_order_view_pick_money_' + index.toString() + '">' + pickMoney + '</div>';
    colStr += "</td>";
    return colStr;
}

function genColumnUpdateDeleteButton(index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<table table width="80%" style="margin-left:auto;margin-right:auto;">';
    colStr += '<tr>';
    colStr += '<td>';
    colStr += '<button type="button" class="btn btn-success detail_order_btn_update" value="' + index.toString() + '">Sửa</button>';
    colStr += '</td>';
    colStr += '<td>';
    colStr += '<button type="button" class="btn btn-success detail_order_btn_delete" value="' + index.toString() + '">Xóa</button>';
    colStr += '</td>';
    colStr += '</tr>';
    colStr += '</table>';
    colStr += "</td>";
    return colStr;
}

function genRow(productCode, productSize, productColor, quantity, discountId, productDiscount, actuallyCollected, price, pickMoney, index) {
    var rowStr = '<tr class="tbl_detail_order_item" id="tbl_detail_order_item_' + index.toString() + '">'
    rowStr += genColumnProductCode(productCode, index);
    rowStr += genColumnProductSize(productSize, index);
    rowStr += genColumnProductColor(productColor, index);
    rowStr += genColumnQuantity(quantity, index);
    rowStr += genColumnDiscount(discountId, productDiscount, index);
    rowStr += genColumnPrice(price, index);
    rowStr += genColumnActuallyCollected(actuallyCollected, index);
    rowStr += genColumnPickMoney(pickMoney, index);
    rowStr += genColumnUpdateDeleteButton(index);
    rowStr += "</tr>";
    return rowStr;
}

function validate_detail_order(productCode, productSize, productColor, quantity, pick_money, actuallyCollected) {
    var message = "";
    if (!productCode || productCode.trim().length == 0) {
        message = "Mã sản phẩm không được để rỗng";
        showMessage(message);
        return false;
    }

    if (!productSize || productSize.trim() === dropdownDefaultSizeText) {
        message = "Phải chọn trường kích cỡ";
        showMessage(message);
        return false;
    }
    if (!productColor || productColor.trim() === dropdownDefaultColorText) {
        message = "Phải chọn trường màu";
        showMessage(message);
        return false;
    }
    try {
        var actually_value_value = parseFloat(actuallyCollected);
        if (isNaN(actually_value_value)) {
            message = "Tiền thực thu phải là một số";
            showMessage(message);
            return false;
        }
        if (actually_value_value < 0) {
            message = "Tiền thực thu phải lớn hơn 0";
            showMessage(message);
            return false;
        }

        var pick_money_value = parseFloat(pick_money);
        if (isNaN(pick_money_value)) {
            message = "Tiền thu hộ phải là một số";
            showMessage(message);
            return false;
        }
        if (pick_money_value < 0) {
            message = "Tiền thu hộ phải lớn hơn 0";
            showMessage(message);
            return false;
        }
    } catch (err) {
        message = "Dữ liệu không hợp lệ";
        showMessage(message);
        return false;
    }

    if (quantity < 1) {
        message = "Số lượng phải lớn hơn 0";
        showMessage(message);
        return false;
    }
    return message == "";

}

function addNewDetailOrder() {
    if (is_waiting_for_request) {
        return;
    }
    var productCode = $('#detail_order_additional_product_code').val().trim();
    var productSize = $('#detail_order_additional_product_size_text').text().trim();
    var productColor = $('#detail_order_additional_product_color_text').text().trim();
    var quantity = $('#detail_order_additional_product_quantity').val().trim();
    var productDiscount = $('#detail_order_additional_discount_text').text().trim();
    var discountId = $('#detail_order_additional_discount_id').val().trim();
    var storage_id = $("#edit_order_storage_id").val();
    if (!validate_detail_order(productCode, productSize, productColor, quantity, 0, 0)) {
        return;
    }
    var param = {
        'marketing_product_code': productCode,
        'product_size': productSize,
        'product_color': productColor,
        'quantity': quantity,
        'discount_id' : discountId,
        'storage_id' : storage_id
    }
    $.get('/product/price', param, function (response) {
        if (response['status'] == 200) {

            var price = response['content']['price'];
            var discountValue = response['content']['discount_value'];
            var row_index = $('.tbl_detail_order_item').length;
            $('#row_additional_detail_order').after(genRow(productCode, productSize, productColor, quantity, discountId, productDiscount, quantity * price - quantity * discountValue, price, quantity * price - quantity * discountValue, row_index));
            $('.detail_order_btn_update').first().click(handleUpdateBtnClicked);
            $('.detail_order_btn_delete').first().click(handleDeleteBtnClicked);
            $('.detail_order_updating_product_size').first().find('a').click(updatingRowProductSizeSelected);
            $('.detail_order_updating_product_color').first().find('a').click(updatingRowProductColorSelected);
            $('.detail_order_updating_discount').first().find('a').click(updatingRowProductDiscountSelected);

            // reset value
            $('#detail_order_additional_product_code').val("");
            $('#detail_order_additional_product_size_text').text(dropdownDefaultSizeText);
            $('#detail_order_additional_product_color_text').text(dropdownDefaultColorText);
            $('#detail_order_additional_product_quantity').val(1);
            $('#detail_order_additional_discount_text').text(dropdownDefaultDiscountText);
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

function showRowInViewMode() {
    $('.tbl_detail_order_item_updating').each(function () {
        $(this).css('display', 'none');
    });

    $('.tbl_detail_order_item_view').each(function () {
        $(this).css('display', 'block');
    });
    $('.detail_order_btn_delete').each(function () {
        $(this).text("Xóa");
    });
    $('.detail_order_btn_update').each(function () {
        $(this).text("Sửa");
    });
}

function showRowInUpdateMode(id) {
    /*  var product_code_id_selected = "detail_order_updating_product_code_" + id;
      var product_size_id_selected = "detail_order_updating_product_size_" + id;
      var product_color_id_selected = "detail_order_updating_product_color_" + id;*/
    var product_quantity_id_selected = "detail_order_updating_product_quantity_" + id;
    //var product_discount_id_selected = "detail_order_updating_product_discount_" + id;
    var product_out_of_quantity_id_selected = "detail_order_updating_product_out_of_quantity_" + id;
    var product_pick_money_id_selected = "detail_order_updating_pick_money_" + id;
    var actually_collected_id_selected = "detail_order_updating_actually_collected_" + id;
    /*
        var view_product_code_id_selected = "detail_order_view_product_code_" + id;
        var view_product_size_id_selected = "detail_order_view_product_size_" + id;
        var view_product_color_id_selected = "detail_order_view_product_color_" + id;*/
    var view_product_quantity_id_selected = "detail_order_view_product_quantity_" + id;
    //var view_product_discount_id_selected = "detail_order_view_product_discount_" + id;
    var view_product_out_of_quantity_id_selected = "detail_order_view_product_out_of_quantity_" + id;
    var view_product_pick_money_id_selected = "detail_order_view_pick_money_" + id;
    var view_actually_collected_id_selected = "detail_order_view_actually_collected_" + id;

    $('.tbl_detail_order_item_updating').each(function () {
        if ([product_quantity_id_selected, product_out_of_quantity_id_selected, product_pick_money_id_selected, actually_collected_id_selected].indexOf($(this).attr('id')) >= 0) {
            $(this).css('display', 'block');
            /* if($(this).attr('id') === product_code_id_selected){
                 $(this).val($('#'+view_product_code_id_selected).text());
             }
             if($(this).attr('id') === product_size_id_selected){
                 $('#dropdown_detail_order_product_size_'+id.toString()).text($('#'+view_product_size_id_selected).text());
             }
             if($(this).attr('id') === product_color_id_selected){
                 $('#dropdown_detail_order_product_color_'+id.toString()).text($('#'+view_product_color_id_selected).text());
             }*/
            if ($(this).attr('id') === product_quantity_id_selected) {
                $(this).val($('#' + view_product_quantity_id_selected).text());
            }
            if ($(this).attr('id') === actually_collected_id_selected) {
                $(this).val($('#' + view_actually_collected_id_selected).text());
            }
            /*   if ($(this).attr('id') === product_discount_id_selected) {
                   if ($('#' + view_product_discount_id_selected).text() != '') {
                       $('#dropdown_discount_code_' + id.toString()).text($('#' + view_product_discount_id_selected).text());
                   }
               }*/

            if ($(this).attr('id') === product_pick_money_id_selected) {
                $(this).val($('#' + view_product_pick_money_id_selected).text());
            }

            if ($(this).attr('id') === product_out_of_quantity_id_selected) {
                if ($('#' + view_product_out_of_quantity_id_selected).is(":checked")) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            }

        } else {
            $(this).css('display', 'none');
        }
    });

    $('.tbl_detail_order_item_view').each(function () {
        if ([view_product_quantity_id_selected, view_product_out_of_quantity_id_selected,
            view_product_pick_money_id_selected, view_actually_collected_id_selected].indexOf($(this).attr('id')) >= 0) {
            $(this).css('display', 'none');
        }
    });
}

function updateRow(id) {
    /*  var product_code_id_selected = "detail_order_updating_product_code_" + id;
      var product_size_id_selected = "detail_order_updating_product_size_" + id;
      var product_color_id_selected = "detail_order_updating_product_color_" + id;*/
    var product_quantity_id_selected = "detail_order_updating_product_quantity_" + id;
    //var product_discount_id_selected = "detail_order_updating_product_discount_" + id;
    var product_out_of_quantity_id_selected = "detail_order_updating_product_out_of_quantity_" + id;
    var product_pick_money_id_selected = "detail_order_updating_pick_money_" + id;
    var actually_collected_id_selected = "detail_order_updating_actually_collected_" + id;

    /*  var view_product_code_id_selected = "detail_order_view_product_code_" + id;
      var view_product_size_id_selected = "detail_order_view_product_size_" + id;
      var view_product_color_id_selected = "detail_order_view_product_color_" + id;*/
    var view_product_quantity_id_selected = "detail_order_view_product_quantity_" + id;
    //var view_product_discount_id_selected = "detail_order_view_product_discount_" + id;
    var view_product_out_of_quantity_id_selected = "detail_order_view_product_out_of_quantity_" + id;
    var view_product_pick_money_id_selected = "detail_order_view_pick_money_" + id;
    var view_actually_collected_id_selected = "detail_order_view_actually_collected_" + id;

    $('.tbl_detail_order_item_updating').each(function () {
        if ([product_quantity_id_selected, product_out_of_quantity_id_selected, product_pick_money_id_selected, actually_collected_id_selected].indexOf($(this).attr('id')) >= 0) {

            /*if($(this).attr('id') === product_code_id_selected){
                $('#'+view_product_code_id_selected).text( $(this).val());
            }
            if($(this).attr('id') === product_size_id_selected){
                $('#'+view_product_size_id_selected).text($('#dropdown_detail_order_product_size_'+id.toString()).text());
            }
            if($(this).attr('id') === product_color_id_selected){
                $('#'+view_product_color_id_selected).text($('#dropdown_detail_order_product_color_'+id.toString()).text());
            }*/
            if ($(this).attr('id') === product_quantity_id_selected) {
                $('#' + view_product_quantity_id_selected).text($(this).val());
            }
            if ($(this).attr('id') === actually_collected_id_selected) {
                $('#' + view_actually_collected_id_selected).text($(this).val());
            }
            /* if ($(this).attr('id') === product_discount_id_selected) {

                 if ($('#dropdown_discount_code_' + id.toString()).text().trim() != dropdownDefaultDiscountText) {
                     $('#' + view_product_discount_id_selected).text($('#dropdown_discount_code_' + id.toString()).text());
                 } else {
                     $('#' + view_product_discount_id_selected).text("");
                 }

             }*/

            if ($(this).attr('id') === product_out_of_quantity_id_selected) {
                if ($(this).is(":checked")) {
                    $('#' + view_product_out_of_quantity_id_selected).prop('checked', true);
                } else {
                    $('#' + view_product_out_of_quantity_id_selected).prop('checked', false);
                }
            }
            if ($(this).attr('id') === product_pick_money_id_selected) {
                $('#' + view_product_pick_money_id_selected).text($(this).val());
            }
        }
    });
}

function handleUpdateBtnClicked() {
    var updateVal = $(this).val();
    var updateText = $(this).text();
    if (updateText === "Lưu") {
        var id = updateVal;
        /*   var product_code_id_selected = "detail_order_updating_product_code_" + id;
           var product_size_id_selected = "detail_order_updating_product_size_" + id;
           var product_color_id_selected = "detail_order_updating_product_color_" + id;*/
        var product_code_id_selected = "detail_order_view_product_code_" + id;
        var product_size_id_selected = "detail_order_view_product_size_" + id;
        var product_color_id_selected = "detail_order_view_product_color_" + id;
        var product_quantity_id_selected = "detail_order_updating_product_quantity_" + id;
        var pick_money_id_selected = "detail_order_updating_pick_money_" + id;
        var actually_collected_id_selected = "detail_order_updating_actually_collected_" + id;

        /* var product_code = $('#'+product_code_id_selected).val().trim();*/
        var product_code = $('#' + product_code_id_selected).text().trim();
        var product_size = $('#' + product_size_id_selected).text().trim();
        var product_color = $('#' + product_color_id_selected).text().trim();
        var product_quantity = $('#' + product_quantity_id_selected).val().trim();
        var pick_money = $('#' + pick_money_id_selected).val().trim();
        var actually_collected = $('#' + actually_collected_id_selected).val().trim();

        if (validate_detail_order(product_code, product_size, product_color, product_quantity, pick_money, actually_collected)) {
            updateRow(updateVal);
            showRowInViewMode();
        }
    } else {
        showRowInViewMode();
        showRowInUpdateMode(updateVal);
        $(this).text("Lưu");
        $('.detail_order_btn_delete').each(function () {
            if ($(this).val() == updateVal) {
                $(this).text("Huỷ");
            }
        });
    }


}

function handleDeleteBtnClicked() {
    var deleteText = $(this).text();
    if (deleteText.trim() === "Xóa") {
        var delete_col_id = "tbl_detail_order_item_" + $(this).val();
        $('.tbl_detail_order_item').each(function () {
            if ($(this).attr('id') === delete_col_id) {
                $(this).remove();
            }
        });
    } else {
        showRowInViewMode();
    }
}

function updatingRowProductSizeSelected() {
    $(this).parent().parent().find('button').text($(this).text());
}

function updatingRowProductColorSelected() {
    $(this).parent().parent().find('button').text($(this).text());
}

function updatingRowProductDiscountSelected() {
    var text = dropdownDefaultDiscountText;
    if ($(this).text() != "") {
        text = $(this).text();
    }
    id = $(this).attr('id').replace('detail_order_options_discount_id_', '');
    var discount_id_hidden_value = 'detail_order_view_discount_id_' + id.toString();
    $('#' + discount_id_hidden_value).val($(this).find("#discount_id").val());
    $(this).parent().parent().find('button').text(text);
}

function validate_order() {
    var customer_code = $('#edit_order_customer_code').val().trim();
    var note = $('#edit_order_note').val().trim();
    var state_id = $('#edit_order_state_id').val();
    var fail_reason_id = $('#edit_order_fail_reason_id').val();
    if (valida_order_data(customer_code, state_id)) {
        var isInUpdatingMode = false;
        $('.tbl_detail_order_item_updating').each(function () {
            if ($(this).css('display') != 'none') {
                isInUpdatingMode = true;
                return false;
            }
        });
        if (isInUpdatingMode) {
            showMessage("Chi tiết đơn hàng đang trong trạng thái chỉnh sửa.Vui lòng lưu lại để tiếp tục hoạt động khác");
            return false;
        } else {
            list_detail_orders = collect_detail_order();
            if (list_detail_orders.length == 0) {
                showMessage("Hóa đơn phải có it nhất một mặt hàng");
                return false;
            }
            return true;
        }

    }
    return false;
}

function valida_order_data(customer_code, state_id) {
    if (customer_code == '') {
        showMessage('Mã khách không được rỗng');
        return false;
    }

    if (state_id == '-1') {
        showMessage('Bạn phải chọn một trạng thái');
        return false;
    }
    return true;
}

function collect_detail_order() {
    var list_detail_order = [];
    var ele_index = 0;

    $('.tbl_detail_order_item').each(function () {
        if (ele_index > 0) {
            var id = $(this).attr('id').replace('tbl_detail_order_item_', '');
            var detail_order = {};
            detail_order['marketing_product_code'] = $('#detail_order_view_product_code_' + id).text().trim();
            detail_order['product_size'] = $('#detail_order_view_product_size_' + id).text().trim();
            detail_order['product_color'] = $('#detail_order_view_product_color_' + id).text().trim();
            detail_order['quantity'] = $('#detail_order_view_product_quantity_' + id).text().trim();
            detail_order['discount_id'] = $(this).find("#detail_order_view_discount_id").val().trim();

            detail_order['pick_money'] = $('#detail_order_view_pick_money_' + id).text().trim();
            detail_order['actually_collected'] = $('#detail_order_view_actually_collected_' + id).text().trim();
            list_detail_order.push(detail_order);
        }

        ele_index += 1;
    });
    return list_detail_order;

}

function save_order(order_id, customer_code, order_state_id, order_fail_id, note, list_detail_orders, replace_order, delivery_time, is_order_test) {
    if (is_waiting_for_request) {
        return;
    }
    is_waiting_for_request = true;
    var storage_id = $("#edit_order_storage_id").val();
    var order = {
        '_token': $('meta[name=csrf-token]').attr('content'),
        'storage_id' : storage_id
    };
    order['order_id'] = order_id;
    order['customer_code'] = customer_code;
    order['order_state_id'] = order_state_id;
    order['order_fail_id'] = order_fail_id;
    order['note'] = note;
    order['replace_order'] = replace_order;
    order['delivery_time'] = delivery_time;

    if(is_order_test){
        order['is_order_test'] = 1;
    }else{
        order['is_order_test'] = 0;
    }
    order['list_detail_orders'] = JSON.stringify(list_detail_orders);

    setupCSRF();
    $.post('/sale/add-order', order, function (response) {
        if (response['status'] == 200) {
            location.reload();
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

function init(){
    $('#dropdown_storage_address a').click(function(){
        $('#dropdown_storage_address_text').text($(this).text());
        $("#edit_order_storage_id").val($(this).find(".id").val());
    });
}

$(document).ready(function () {
    init();
    $('#edit_order_dropdown_state a').click(function () {
        $('#edit_order_dropdown_state_text').text($(this).text());
        var state_id = $(this).attr('id');
        var state_id = state_id.replace("edit_order_state_id_", "");
        $('#edit_order_state_id').val(state_id);
    });

    $('#edit_order_dropdown_reason a').click(function () {
        $('#edit_order_dropdown_reason_text').text($(this).text());
        var fail_reason_id = $(this).attr('id');
        var fail_reason_id = fail_reason_id.replace("edit_order_fail_reason_id_", "");
        $('#edit_order_fail_reason_id').val(fail_reason_id);
    });

    $('#detail_order_additional_product_size a').click(function () {
        $('#detail_order_additional_product_size_text').text($(this).text());
    });

    $('#detail_order_additional_product_color a').click(function () {
        $('#detail_order_additional_product_color_text').text($(this).text());
    });

    $('#detail_order_additional_discount a').click(function () {
        $('#detail_order_additional_discount_text').text($(this).text());
        $('#detail_order_additional_discount_id').val($(this).find(".option_detail_order_discount_id").val())
    });

    $('#detail_order_btn_add').click(function () {
        addNewDetailOrder();
    });

    $('.detail_order_updating_product_size a').click(updatingRowProductSizeSelected);
    $('.detail_order_updating_product_color a').click(updatingRowProductColorSelected);
    $('.detail_order_updating_discount a').click(updatingRowProductDiscountSelected);

    $('.detail_order_btn_update').click(handleUpdateBtnClicked);
    $('.detail_order_btn_delete').click(handleDeleteBtnClicked);

    list_product_size = []
    $('#detail_order_additional_product_size a').each(function () {
        list_product_size.push($(this).text());
    });
    list_product_color = []
    $('#detail_order_additional_product_color a').each(function () {
        list_product_color.push($(this).text());
    });
    list_product_discount = []
    list_product_id = []
    $('#detail_order_additional_discount a').each(function () {
        list_product_discount.push($(this).text());
        list_product_id.push($(this).attr("id"));
    });
    $('#edit_order_btn_ok').click(function () {
        if (validate_order()) {
            list_detail_orders = collect_detail_order();
            var customer_code = $('#edit_order_customer_code').val().trim();
            var order_id = $('#edit_order_id').val().trim();
            var note = $('#edit_order_note').val().trim();
            var state_id = $('#edit_order_state_id').val();
            var fail_reason_id = $('#edit_order_fail_reason_id').val();
            var replace_order = $('#edit_order_replace_order').val();
            var delivery_time = $('#edit_order_delivery_time_text').val();
            var is_order_test = $('#edit_order_is_test').is(':checked');
            save_order(order_id, customer_code, state_id, fail_reason_id, note, list_detail_orders, replace_order, delivery_time, is_order_test);

        }
    });

    $('#edit_order_btn_cancel').click(function () {
        $('#edit_order_dialog').css('display', 'none');
        /*var count = $('.tbl_detail_order_item').length;
        alert(count);*/
    });
});
