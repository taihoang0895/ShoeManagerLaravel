var is_waiting_for_request = false;

function collectFilterParam() {
    var search_product_code = $('#list_product_search_product_code').val().trim();

    var param = "";
    if (search_product_code !== '') {
        param = 'product_code=' + search_product_code;
    }

    return param;
}

function handleAddButton() {
    $('#list_products_btn_add').click(function () {

        if (is_waiting_for_request) {
            return;
        }
        is_waiting_for_request = true;
        $.get("/admin/form-add-product/", function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_product').empty();
                $('#dialog_edit_product').html(response['content']);
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
    });
}

function handleShowDetailProduct() {
    $('.show_detail').click(function () {
        var product_code = $(this).find('input').val();
        if (is_waiting_for_request) {
            return;
        }
        is_waiting_for_request = true;
        var data = {
            "product_code": product_code.toString()
        }
        $.get("/detail-product/", data, function (response) {
            if (response['status'] == 200) {
                $('#dialog_edit_product').empty();
                $('#dialog_edit_product').html(response['content']);
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
        return false;
    });
}

function handleUpdateButton() {
    $('#list_products_btn_update').click(function () {
        if ($('.product_row_selected').length == 0) {
            showMessage("Vui lòng chọn một sản phẩm để sửa");
        } else {
            var product_code_id = $('.product_row_selected').first().attr('id');

            var product_code = product_code_id.replace("product_", "").trim();

            if (is_waiting_for_request) {
                return;
            }
            is_waiting_for_request = true;

            $.get("/admin/form-update-product/?product_code=" + product_code.toString(), function (response) {
                if (response['status'] == 200) {
                    $('#dialog_edit_product').empty();
                    $('#dialog_edit_product').html(response['content']);
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
    });
}

function handleDeleteButton() {
    $('#list_products_btn_delete').click(function () {
        if ($('.product_row_selected').length == 0) {
            showMessage("Vui lòng chọn một sản phẩm để xoá");
        } else {
            $('#confirm_dialog_delete_product').css('display', 'block');
            $('#product_delete_dialog_btn_ok').click(function () {
                $('#confirm_dialog_delete_product').css('display', 'none');
                var product_code_id = $('.product_row_selected').first().attr('id');
                var product_code = product_code_id.replace("product_", "").trim();
                if (is_waiting_for_request) {
                    return;
                }
                is_waiting_for_request = true;
                var data = {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    "product_code": product_code.toString()
                };
                setupCSRF();
                $.post("/admin/delete-product/", data, function (response) {
                    if (response['status'] == 200) {
                        var curr_url = location.href.toString().toLowerCase();
                        curr_url = removeAllParam(curr_url);
                        location.href = curr_url;
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
            });
            $('#product_delete_dialog_btn_cancel').click(function () {
                $('#confirm_dialog_delete_product').css('display', 'none');
            });


        }
    });

}


function handleSearchButton() {
    $('#list_product_btn_search').click(function () {
        var curr_page = parseInt($('#curr_page').val());
        var curr_url = location.href.toString().toLowerCase();
        curr_url = removeAllParam(curr_url);
        filter_param = collectFilterParam();
        curr_url = addParam(curr_url, filter_param);
        location.href = curr_url;

    });
}

$(document).ready(function () {
    $('.product_row').click(function () {
        $('.product_row_selected').removeClass('product_row_selected');
        $(this).addClass('product_row_selected');
    });
    handleAddButton();
    handleUpdateButton();
    handleDeleteButton();
    handleShowDetailProduct();
    handleSearchButton();
});
