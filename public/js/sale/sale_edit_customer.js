var is_waiting_for_request = false;

function handleDistrictSelected() {
    var district_id = $(this).attr('id').replace("customer_district_", "").trim();
    $('#customer_district_id').val(district_id);
}

function handleCitySelected() {
    var city_id = $(this).attr('id').replace("customer_city_", "").trim();
    $('#customer_city_id').val(city_id);
    var filter_class = "district_city_" + city_id;
    $('.customer_district').each(function () {
        if ($(this).hasClass(filter_class)) {
            $(this).css('display', 'block');
        } else {
            $(this).css('display', 'none');
        }
    });
}

function saveCustomer(data) {
    $.post('/sale/save-customer/', data, function (response) {
        if (response['status'] == 200) {
            location.reload();
        } else {
            if (response['status'] == 302) {
                $('#dialog_edit_customer').empty();
                $('#dialog_edit_customer').html(response['content']);
                $('#dialog_edit_customer').css('display', 'block');
                $('#edit_order_btn_cancel').click(function () {
                    location.reload();
                });
            } else {
                showMessage(response['message']);
            }

        }
    })
        .fail(function () {
            showMessage("Lỗi mạng");
        })
        .always(function () {
            is_waiting_for_request = false;
        });
}

function validate(name, street_name, address, list_marketing_product_code) {
    if (name == '') {
        showMessage('Tên khách hàng không được để trống');
        return false;
    }
    if (list_marketing_product_code.length == 0) {
        showMessage('danh sách sản phẩm quan tâm không được rỗng');
        return false;
    }
    var customer_state_id_selected = $('#customer_state_id_selected').val();
    if (customer_state_id_selected == '4') {
        if (street_name == '') {
            showMessage('Bạn phải chọn một đường/phố');
            return false;
        }
        if (address == '') {
            showMessage('Địa chỉ không được để trống');
            return false;
        }
    }
    return true;
}


function init() {
    $("#list_provinces").focusin(function () {
        prevProvinceName = $('#list_provinces').val().trim();
    });

    $("#list_provinces").focusout(function () {
        var name = $('#list_provinces').val().trim();

        if (prevProvinceName == name) {
            return;
        }
        for (province_name of list_province_names) {
            if (province_name == name) {
                $("#list_districts").prop('disabled', false);
                $("#list_districts").val("");
                $("#list_streets").prop('disabled', true);
                $("#list_streets").val("");
                $("#list_districts_data").empty();
                $("#list_streets").empty();

                if (is_waiting_for_request) {
                    return;
                }

                var param = {
                    "province_name": name
                }
                $.get('/list-districts/', param, function (response) {
                    if (response['status'] == 200) {
                        list_district_names = JSON.parse(response['content']);
                        for (district_name of list_district_names) {
                            $('#list_districts_data').append(new Option(district_name))
                        }
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
                return;
            }
        }
        $("#list_districts").prop('disabled', true);
        $("#list_districts").val("");
        $("#list_streets").prop('disabled', true);
        $("#list_streets").val("");
        $('#list_provinces').val('');
        $("#list_districts_data").empty();
        $("#list_streets").empty();

    });

    $("#list_districts").focusin(function () {
        prevDistrictName = $('#list_districts').val().trim();

    });
    $("#list_districts").focusout(function () {
        var name = $('#list_districts').val().trim();
        if (prevDistrictName == name) {
            return;
        }
        for (district_name of list_district_names) {

            if (district_name == name) {
                $("#list_streets").prop('disabled', false);
                $("#list_streets_data").empty();
                $("#list_streets").val("");
                var param = {
                    "province_name": $('#list_provinces').val().trim(),
                    "district_name": $('#list_districts').val().trim()
                }
                $.get('/list-streets/', param, function (response) {
                    if (response['status'] == 200) {
                        list_street_names = JSON.parse(response['content']);
                        for (street_name of list_street_names) {
                            $('#list_streets_data').append(new Option(street_name))
                        }
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
                return;
            }
        }
        $("#list_streets").prop('disabled', true);
        $("#list_streets").val("");
        $('#list_districts').val('');
        $("#list_streets_data").empty();

    });


    if (prevDistrictName == '') {
        $("#list_districts").prop('disabled', true);
    }

    if (prevStreetName == '') {
        $("#list_streets").prop('disabled', true);
    }


    $('#edit_customer_dropdown_state a').click(function () {
        $('#edit_customer_dropdown_state_text').text($(this).text());
        $('#customer_state_id_selected').val($(this).find('input').val());
    });
    $('#edit_customer_dropdown_landing_page a').click(function () {
        $('#edit_customer_dropdown_landing_page_text').text($(this).text());
        $('#customer_landing_page_id_selected').val($(this).find('input').val());
    });

}

function handleOkButton() {
    $('#edit_customer_btn_ok').click(function () {
        if (is_waiting_for_request) {
            return;
        }
        var name = $('#customer_name').val().trim();
        var phone_number = $('#customer_phone_number').val().trim();
        var is_public_phone_number = $('#customer_is_public_phone_number').is(":checked");
        var birthday = $('#customer_birthday_text').val();

        var customer_state_id = $('#customer_state_id_selected').val();
        var customer_landing_page_id = $('#customer_landing_page_id_selected').val();
        var address = $('#customer_address').val();

        var province_name = $('#list_provinces').val().trim();
        var district_name = $('#list_districts').val().trim();
        var street_name = $('#list_streets').val().trim();
        var list_marketing_product_code = [];
        $("#list_marketing_product .item").each(function () {
            var code = $(this).text().trim();
            code = code.substr(0, code.length - 1);
            list_marketing_product_code.push(code);
        });

        if (validate(name, street_name, address, list_marketing_product_code)) {
            is_waiting_for_request = true;
            setupCSRF();
            var customer_id = $('#edit_customer_id').val();
            var data = {
                'name': name,
                'phone_number': phone_number,
                'is_public_phone_number': is_public_phone_number,
                'birthday': birthday,
                'province_name': province_name,
                'district_name': district_name,
                'street_name': street_name,
                'address': address,
                'customer_id': customer_id,
                'state_id': customer_state_id,
                'list_marketing_product': JSON.stringify(list_marketing_product_code),
                'landing_page_id': customer_landing_page_id,
                '_token': $('meta[name=csrf-token]').attr('content'),
            }
            saveCustomer(data);
        }

    });

}

function handleRemoveMarketingProductButton() {
    $('#list_marketing_product .item').click(function () {
        $(this).parent().remove();
    })
}

function handleAddMarketingProductButton() {
    $('#add_marketing_product_button').click(function () {

        if (is_waiting_for_request) {
            return
        }
        var code = $('#search_marketing_product').val().trim();
        if (code != "") {
            var param = {
                "product_code" : code
            }
            $.get('/sale/customer-check-product-code/', param, function (response) {
                if(response['status'] == 200){
                    addMarketingProductitem(code);
                    $('#search_marketing_product').val("")
                }else{
                    showMessage(response['message'])
                }

            }).fail(function () {
                showMessage("Lỗi mạng");
            })
                .always(function () {
                    is_waiting_for_request = false;
                });


        }
    })
}

function addMarketingProductitem(code) {
    var str = '';
    str += '<td>'
    str += '<div class="item">' + code + '<span class="remove">x</span></div>'
    str += '</td>'
    $('#list_marketing_product tr:first').append(str)
    $('#list_marketing_product .item:last').click(function () {
        $(this).parent().remove();
    })
}

$(document).ready(function () {
    init();
    handleOkButton();
    handleRemoveMarketingProductButton();
    handleAddMarketingProductButton();
    $('#edit_customer_btn_cancel').click(function () {
        $('#edit_customer_dialog').css('display', 'none');
    });
    $('#dropdown_customer_city a').click(function () {
        $('#dropdown_customer_city_text').text($(this).text());
    });

    $('#dropdown_customer_district a').click(handleDistrictSelected);
    $('#dropdown_customer_city a').click(handleCitySelected);
});
