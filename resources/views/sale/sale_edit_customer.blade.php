<link rel="stylesheet" href={{ asset('css/sale/sale_edit_customer.css'  ) }}>
<script src={{ asset('js/sale/sale_edit_customer.js' ) }}></script>
<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
<script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">

    @csrf
    <input type="hidden" id="edit_customer_id" value="{{$customer->id}}">
    <div id="edit_customer_dialog" onresize="alert()">
        <div id="edit_customer_dialog_content">
            <div class="title">Nhập Thông Tin Khách Hàng</div>
            <table width="90%">
                <tr class="customer_field_row">
                    <td class="lbl_name" style="padding-top:0px;">Tên</td>
                    <td class="value" style="padding-top:0px;"><input class="form-control" type="text"
                                                                      placeholder="Nhập tên khách" id="customer_name"
                                                                      value="{{$customer->name}}">
                    </td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Số điện thoại</td>
                    <td class="value"><input class="form-control" type="text" placeholder="Nhập số điện thoại"
                                             id="customer_phone_number" value="{{$customer->phone_number}}" autocomplete="off"></td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Số điện thoại công khai</td>
                    <td class="value">
                        @if($customer->is_public_phone_number)
                            <input type="checkbox" value="" style="width:20px;height:20px;"
                                   id="customer_is_public_phone_number" checked>
                        @else
                            <input type="checkbox" value="" style="width:20px;height:20px;"
                                   id="customer_is_public_phone_number">
                        @endif
                    </td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Ngày sinh</td>
                    <td class="value">
                        <div class="input-group date" id="customer_birthday" data-target-input="nearest"
                             style="width:200px;">
                            <input type="text" class="form-control datetimepicker-input"
                                   data-target="#customer_birthday"
                                   placeholder="dd/mm/yyyy" id="customer_birthday_text"
                                   value="{{$customer->birthday_str}}"/>
                            <div class="input-group-append" data-target="#customer_birthday"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Tỉnh/Thành Phố</td>
                    <td class="value">
                        <input list="list_provinces_data" id="list_provinces" class="form-control" name="browser"
                               style="width:200px;" placeholder="Tỉnh/Thành phố" value="{{$prevProvinceName}}"
                               autocomplete="off">
                        <datalist id="list_provinces_data" style="max-height: 100px;overflow-y: auto;">
                            @foreach ($list_province_names as $province_name)
                                <option value="{{$province_name}}">
                            @endforeach
                        </datalist>

                    </td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Quận/Huyện</td>
                    <td class="value">
                        <input list="list_districts_data" id="list_districts" class="form-control" name="browser"
                               style="width:200px;" placeholder="Quận/Huyện" value="{{$prevDistrictName}}"
                               autocomplete="off">
                        <datalist id="list_districts_data" style="max-height: 100px;overflow-y: auto;">
                            @foreach ($list_district_names as $district_name)
                                <option value="{{$district_name}}">
                            @endforeach
                        </datalist>
                    </td>
                </tr>


                <tr class="customer_field_row">
                    <td class="lbl_name">Đường/Phố</td>
                    <td class="value">
                        <input list="list_streets_data" id="list_streets" class="form-control" name="browser"
                               style="width:200px;" placeholder="Đường/Phố" value="{{$prevStreetName}}"
                               autocomplete="off">
                        <datalist id="list_streets_data">
                            @foreach ($list_street_names as $street_name)
                                <option value="{{$street_name}}">
                            @endforeach
                        </datalist>
                    </td>
                </tr>
                <tr class="customer_field_row">
                    <td class="lbl_name">Địa chỉ</td>
                    <td class="value">
                        <input class="form-control" type="text" placeholder="Địa chỉ"
                               id="customer_address" value="{{$customer->address}}">
                    </td>
                </tr>
                <tr class="customer_field_row">
                    <input type="hidden" id="customer_state_id_selected" value="{{$customer->customer_state}}">
                    <td class="lbl_name">Trạng Thái</td>
                    <td class="value">
                        <div class="dropdown" id="edit_customer_dropdown_state">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_customer_dropdown_state_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$customer->customer_state_name}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @foreach ($list_customer_states as $state)
                                    <a class="dropdown-item"><input type="hidden"
                                                                    value="{{$state->id}}">{{$state->name}}</a>
                                @endforeach
                            </div>
                        </div>
                    </td>

                <tr class="customer_field_row">
                    <input type="hidden" id="customer_landing_page_id_selected" value="{{$customer->landing_page_id}}">
                    <td class="lbl_name">Landing Pages</td>
                    <td class="value">
                        <div class="dropdown" id="edit_customer_dropdown_landing_page">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_customer_dropdown_landing_page_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$customer->landing_page_name}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item"><input type="hidden"
                                                                value="-1">____</a>
                                @foreach ($list_landing_pages as $landing_page)
                                    <a class="dropdown-item"><input type="hidden"
                                                                    value="{{$landing_page->id}}">{{$landing_page->name}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="customer_field_row">
                    <td class="lbl_name">Sản phẩm quan tâm</td>
                    <td class="value">
                        <div class="form-inline">
                            <input class="form-control" id="search_marketing_product" type="text"
                                   placeholder="Thêm mã sản phẩm">
                            <div id="add_marketing_product_button"><img class="add_marketing_product_button"
                                                                        src={{ asset('images/ic_plus.png')}}></div>
                        </div>

                    </td>
                </tr>
            </table>


            <table id="list_marketing_product">
                <tr>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_customer_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_customer_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>


<script>
    var prevProvinceName = "{{$prevProvinceName}}";
    var prevDistrictName = "{{$prevDistrictName}}";
    var prevStreetName = "{{$prevStreetName}}";
    var list_district_names = JSON.parse({!!json_encode($list_district_names_encode)!!});
    var list_street_names = JSON.parse({!!json_encode($list_street_names_encode)!!});
    var list_province_names = JSON.parse({!!json_encode($list_province_names_encode)!!});

    $(function () {
        $("#customer_birthday").datetimepicker({
            format: 'DD/MM/YYYY',
        });
        @foreach ($customer->list_marketing_code as $marketing_code)
        addMarketingProductitem("{{$marketing_code}}")
        @endforeach


        $("#customer_phone_number").autocomplete({
            source: function (request, response) {
                // Fetch data
                $.ajax({
                    url: "/sale/search-phone-number/",
                    type: 'get',
                    dataType: "json",
                    data: {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        search: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                // Set selection
                $('#customer_phone_number').val(ui.item.value);
                var param = {
                    "phone_number": $("#customer_phone_number").val().trim()
                };
                $.get('/sale/find-customer-by-phone-number/', param, function (response) {

                    if (response['status'] == 200) {
                        $('#customer_name').val(response['customer']['name']);
                        if (response['customer']['is_public_phone_number']) {
                            $("#customer_is_public_phone_number").prop('checked', true);
                        } else {
                            $("#customer_is_public_phone_number").prop('checked', false);
                        }
                        list_district_names = JSON.parse(response['customer']['list_district_names_encode']);
                        list_street_names = JSON.parse(response['customer']['list_street_names_encode']);

                        for (district_name of list_district_names) {
                            $('#list_districts_data').append(new Option(district_name))
                        }
                        for (street_name of list_street_names) {
                            $('#list_streets_data').append(new Option(street_name))
                        }

                        $('#customer_birthday_text').val(response['customer']['birthday_text']);
                        $('#list_provinces').val(response['customer']['province']);
                        $('#list_districts').val(response['customer']['district']);
                        $('#list_streets').val(response['customer']['street']);
                        $('#customer_address').val(response['customer']['address']);

                        if (response['customer']['district'] == '') {
                            $("#list_districts").prop('disabled', true);
                        }else{
                            $("#list_districts").prop('disabled', false);
                        }

                        if (response['customer']['street'] == '') {
                            $("#list_streets").prop('disabled', true);
                        }else{
                            $("#list_streets").prop('disabled', false);
                        }

                    }
                });


                return false;
            }
        });

        $("#search_marketing_product").autocomplete({
            source: function (request, response) {
                // Fetch data
                $.ajax({
                    url: "/search-marketing-code-and-product-code/",
                    type: 'get',
                    dataType: "json",
                    data: {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        search: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                // Set selection
                $('#search_marketing_product').val(ui.item.value);
                return false;
            }
        });
    });


</script>
