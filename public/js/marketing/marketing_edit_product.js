function genColumnProductColor(productColor, index) {
    var colStr = '<td style="text-align:center;">';

    colStr += '<input type="text" class="form-control tbl_detail_product_item_updating"';
    colStr += ' id="detail_product_updating_product_color_' + index.toString() + '"';
    colStr += ' autocomplete="off" style="width: 70%;margin: auto">'

    colStr += ' <label class="tbl_detail_product_item_view"';
    colStr += ' id="detail_product_view_product_color_' + index.toString() + '">' + productColor;
    colStr += '</label>';
    colStr += "</td>";
    return colStr;
}

function genColumnProductSize(productSize, index) {
    var colStr = '<td style="text-align:center;">';

    colStr += '<input type="text" class="form-control tbl_detail_product_item_updating"';
    colStr += ' id="detail_product_updating_product_size_' + index.toString() + '"';
    colStr += ' autocomplete="off" style="width: 70%;margin: auto">'

    colStr += ' <label class="tbl_detail_product_item_view"';
    colStr += ' id="detail_product_view_product_size_' + index.toString() + '">' + productSize;
    colStr += '</label>';
    colStr += "</td>";
    return colStr;
}

function genColumnUpdateDeleteButton(index) {
    var colStr = '<td style="text-align:center;">';
    colStr += '<table table width="80%" style="margin-left:auto;margin-right:auto;">';
    colStr += '<tr>';
    colStr += '<td>';
    colStr += '<button type="button" class="btn btn-success detail_product_btn_update" value="' + index.toString() + '">Sửa</button>';
    colStr += '</td>';
    colStr += '<td>';
    colStr += '<button type="button" class="btn btn-success detail_product_btn_delete" value="' + index.toString() + '">Xóa</button>';
    colStr += '</td>';
    colStr += '</tr>';
    colStr += '</table>';
    colStr += "</td>";
    return colStr;
}

function genRow(productColor, productSize, index) {
    var rowStr = '<tr class="tbl_detail_product_item" id="tbl_detail_product_item_' + index.toString() + '">'
    rowStr += genColumnProductSize(productSize, index);
    rowStr += genColumnProductColor(productColor, index);
    rowStr += genColumnUpdateDeleteButton(index);
    rowStr += "</tr>";
    return rowStr;
}

function collectDetailProduct() {
    var list_detail_product = [];
    var ele_index = 0;
    $('.tbl_detail_product_item').each(function () {

        if (ele_index > 0) {
            var id = $(this).attr('id').replace('tbl_detail_product_item_', '');
            if ($('#detail_product_view_product_size_' + id).css('display') != 'none') {
                var detail_product = {};
                detail_product['product_size'] = $('#detail_product_view_product_size_' + id).text().trim();
                detail_product['product_color'] = $('#detail_product_view_product_color_' + id).text().trim();
                list_detail_product.push(detail_product);
            }
        }
        ele_index += 1;
    });

    return list_detail_product;
}

function validateDetailProduct(productColor, productSize) {
    if (productColor == "") {
        showMessage("Bạn phải chọn một màu");
        return false;
    }
    if (productSize == "") {
        showMessage("Bạn phải chọn một kích cỡ");
        return false;
    }
    list_detail_products = collectDetailProduct();

    for (var item of list_detail_products) {

        if (item['product_size'] == productSize && item['product_color'] == productColor) {
            showMessage("đã tồn tại item này trong danh sách");
            return false;
        }

    }
    return true;
}

function updateRow(id) {
    var product_color_selected = "detail_product_updating_product_color_" + id;
    var product_size_selected = "detail_product_updating_product_size_" + id;

    var view_product_color_id_selected = "detail_product_view_product_color_" + id;
    var view_product_size_id_selected = "detail_product_view_product_size_" + id;
    $('.tbl_detail_product_item_updating').each(function () {
        if ([product_color_selected, product_size_selected].indexOf($(this).attr('id')) >= 0) {
            if ($(this).attr('id') === product_color_selected) {
                $('#' + view_product_color_id_selected).text($('#' + product_color_selected).val().trim());
            }
            if ($(this).attr('id') === product_size_selected) {
                $('#' + view_product_size_id_selected).text($('#' + product_size_selected).val().trim());
            }
        }
    });
}

function showRowInViewMode() {
    $('.tbl_detail_product_item_updating').each(function () {
        $(this).css('display', 'none');
    });

    $('.tbl_detail_product_item_view').each(function () {
        $(this).css('display', 'block');
    });
    $('.detail_product_btn_delete').each(function () {
        $(this).text("Xóa");
    });
    $('.detail_product_btn_update').each(function () {
        $(this).text("Sửa");
    });
}

function showRowInUpdateMode(id) {
    var product_color_selected = "detail_product_updating_product_color_" + id;
    var product_size_selected = "detail_product_updating_product_size_" + id;

    var view_product_color_id_selected = "detail_product_view_product_color_" + id;
    var view_product_size_id_selected = "detail_product_view_product_size_" + id;
    $('.tbl_detail_product_item_updating').each(function () {
        if ([product_color_selected, product_size_selected].indexOf($(this).attr('id')) >= 0) {

            $(this).css('display', 'block');

            if ($(this).attr('id') === product_color_selected) {
                $('#' + product_color_selected).val($('#' + view_product_color_id_selected).text());
            }
            if ($(this).attr('id') === product_size_selected) {
                $('#' + product_size_selected).val($('#' + view_product_size_id_selected).text());
            }

        } else {
            $(this).css('display', 'none');
        }
    });

    $('.tbl_detail_product_item_view').each(function () {
        if ([view_product_color_id_selected, view_product_size_id_selected].indexOf($(this).attr('id')) >= 0) {
            $(this).css('display', 'none');
        }
    });
}


function handleUpdateDetailProductBtnClicked() {
    var updateVal = $(this).val();
    var updateText = $(this).text();
    if (updateText === "Lưu") {
        var id = updateVal;
        var product_color_text_id_selected = "detail_product_updating_product_color_" + id;
        var product_size_text_id_selected = "detail_product_updating_product_size_" + id;

        var product_color = $('#' + product_color_text_id_selected).val().trim();
        var product_size = $('#' + product_size_text_id_selected).val().trim();
        if (validateDetailProduct(product_color, product_size)) {
            updateRow(updateVal);
            showRowInViewMode();
        }
    } else {
        showRowInViewMode();
        showRowInUpdateMode(updateVal);
        $(this).text("Lưu");
        $('.detail_product_btn_delete').each(function () {
            if ($(this).val() == updateVal) {
                $(this).text("Huỷ");
            }
        });
    }
}

function handleDeleteDetailProductBtnClicked() {
    var deleteText = $(this).text();
    if (deleteText.trim() === "Xóa") {
        var delete_col_id = "tbl_detail_product_item_" + $(this).val();
        $('.tbl_detail_product_item').each(function () {
            if ($(this).attr('id') === delete_col_id) {
                $(this).remove();
            }
        });
    } else {
        showRowInViewMode();
    }
}

function addNewDetailProduct() {
    var productSize = $('#detail_product_additional_size_text').val().trim();
    var productColor = $('#detail_product_additional_color_text').val().trim();
    if (validateDetailProduct(productColor, productSize)) {
        var row_index = $('.tbl_detail_product_item').length;
        $('#row_additional_detail_product').after(genRow(productColor, productSize, row_index));
        $('.detail_product_btn_update').first().click(handleUpdateDetailProductBtnClicked);
        $('.detail_product_btn_delete').first().click(handleDeleteDetailProductBtnClicked);
        $('#detail_product_additional_size_text').val("");
        $('#detail_product_additional_color_text').val("");

        var detail_product_size = "detail_product_updating_product_size_" + row_index.toString();
        $("#" + detail_product_size).autocomplete({
            source: list_suggest_product_sizes
        });
        var detail_product_color = "detail_product_updating_product_color_" + row_index.toString();
        $("#" + detail_product_color).autocomplete({
            source: list_suggest_product_colors
        });


    }

}

function handleAddDetailProductButton() {
    $('#detail_product_additional_btn_add').click(function () {
        addNewDetailProduct();
    });
}

function validatedProduct() {
    var product_code = $('#edit_product_code').val().trim();
    var product_name = $('#edit_product_name').val().trim();
    var price = $('#edit_product_price').val().trim();
    var historical_cost = $('#edit_product_historical_cost').val().trim();

    if (product_code === "") {
        showMessage("Mã sản phẩm không được để rỗng");
        return false;
    }
    if (product_name === "") {
        showMessage("Tên sản phẩm không được để rỗng");
        return false;
    }
    if (price === "") {
        showMessage("Giá sản phẩm không được để rỗng");
        return false;
    }

    if (historical_cost === "") {
        showMessage("Giá gốc sản phẩm không được để rỗng");
        return false;
    }

    if (price <= 0) {
        showMessage("Giá sản phẩm phải lớn hơn không");
        return false;
    }
    if (historical_cost <= 0) {
        showMessage("Giá gốc sản phẩm phải lớn hơn không");
        return false;
    }
    var isInUpdatingMode = false;
    $('.tbl_detail_product_item_updating').each(function () {
        if ($(this).css('display') != 'none') {
            isInUpdatingMode = true;
            return false;
        }
    });
    if (isInUpdatingMode) {
        showMessage("Chi tiết sản phẩm đang trong trạng thái chỉnh sửa.Vui lòng lưu lại để tiếp tục hoạt động khác");
        return false;
    }
    return true;
}

function saveProduct() {
    if (is_waiting_for_request) {
        return;
    }
    var listDetailProduct = collectDetailProduct();
    /*if(listDetailProduct.length === 0){
        showMessage("Chi tiết sản phẩm phải có it nhất một phần tử");
        return;
    }*/
    var product_code = $('#edit_product_code').val().trim();
    var product_name = $('#edit_product_name').val().trim();
    var price = $('#edit_product_price').val().trim();
    var historical_cost = $('#edit_product_historical_cost').val().trim();
    var storage_id = $("#edit_product_storage_id").val();
    var is_test = 0;
    if($('#edit_product_is_test').is(":checked")){
        is_test = 1;
    }
    is_waiting_for_request = true;

    var product = {
        '_token': $('meta[name=csrf-token]').attr('content'),
    };
    product['product_code'] = product_code;
    product['product_name'] = product_name;
    product['product_price'] = price;
    product['product_historical_cost'] = historical_cost;
    product['is_test'] = is_test;
    product['storage_id'] = storage_id;
    product['list_detail_products'] = JSON.stringify(listDetailProduct);

    var url = '/marketing/add-product/';
    if ($('#edit_product_id').val().trim() !=='') {
        url = '/marketing/update-product/';
    }
    $.post(url, product, function (response) {
        if (response['status'] === 200) {
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

function handleOkButtonClicked() {
    $('#edit_product_btn_ok').click(function () {
        if (validatedProduct()) {

            saveProduct();
        }

    });
}

function handleCancelButtonClicked() {
    $('#edit_product_btn_cancel').click(function () {
        $('#edit_product_dialog').css('display', 'none');
    });
}
function init(){
    $('#dropdown_storage_address a').click(function(){
        $('#dropdown_storage_address_text').text($(this).text());
        $("#edit_product_storage_id").val($(this).find(".id").val());
    });
}
$(document).ready(function () {
    init();
    handleAddDetailProductButton();
    handleCancelButtonClicked();
    handleOkButtonClicked();
});
