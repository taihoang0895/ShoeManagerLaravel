<link rel="stylesheet" href={{ asset('css/sale/sale_edit_order.css'  ) }}>
<script src={{ asset('js/sale/sale_add_order.js' ) }}></script>
<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
<script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <div id="edit_order_dialog">
        <input type="hidden" id="edit_order_id" value="{{$order->id}}">
        <input type="hidden" id="edit_order_state_id" value="{{$order->order_state_id}}">
        <input type="hidden" id="edit_order_fail_reason_id" value="{{$order->order_fail_reason_id}}">
        <div id="edit_order_dialog_content">

            <div class="title" style="margin-top:15px;margin-bottom:30px;">Nhập Thông Tin Hóa Đơn</div>
            <table width="90%">

                <tr class="order_field_row">
                    <td class="lbl_name_col1">Mã Khách</td>
                    <td class="value_col1"><input class="form-control" type="text" placeholder="Nhập mã khách hàng"
                                                  id="edit_order_customer_code" value="{{$order->customer_code}}"
                                                  style="background-color:white;"></td>
                    <td class="lbl_name_col2">Trạng Thái</td>
                    <td class="value_col2">
                        <div class="dropdown" id="edit_order_dropdown_state">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_order_dropdown_state_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$order_state_name}}
                            </button>
                           {{-- @if($user->isLeader())
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                     style="max-height:200px;overflow-y: auto;">
                                    @foreach ($list_states as $state)
                                        <a class="dropdown-item"
                                           id="edit_order_state_id_{{$state->id}}">{{$state->name}}</a>
                                    @endforeach
                                </div>
                            @endif--}}
                        </div>
                    </td>
                </tr>
                <tr class="order_field_row">
                    <td class="lbl_name_col1" style="padding-top:0px;">Mã Hóa Đơn Hoàn</td>
                    <td class="value_col1" style="padding-top:0px;"><input class="form-control" type="text"
                                                                           id="edit_order_replace_order"
                                                                           placeholder="Nhập mã hóa đơn hoàn"
                                                                           value="{{$order->replace_order_code}}"
                                                                           style="background-color:white;"></td>
                    <td class="lbl_name_col2">Lý Do Lỗi</td>
                    <td class="value_col2">
                        <div class="dropdown" id="edit_order_dropdown_reason">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_order_dropdown_reason_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$order->order_fail_cause}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                 style="max-height:200px;overflow-y: auto;">
                                <a class="dropdown-item"
                                   id="edit_order_fail_reason_id_-1">___</a>
                                @foreach ($list_fail_reasons as $fail_reason)
                                    <a class="dropdown-item"
                                       id="edit_order_fail_reason_id_{{$fail_reason->id}}">{{$fail_reason->cause}}</a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="order_field_row">
                    <td class="lbl_name_col1" rowspan="2">Ghi Chú</td>
                    <td rowspan="2">
                        <textarea class="form-control" rows="3" id="edit_order_note">{{$order->note}}</textarea>
                    </td>

                    <td class="lbl_name_col2">Đơn Test</td>
                    <td class="value_col2">
                        @if($order->is_test)
                            <input type="checkbox" value="" style="width:20px;height:20px;"
                                   id="edit_order_is_test" checked>
                        @else
                            <input type="checkbox" value="" style="width:20px;height:20px;"
                                   id="edit_order_is_test">
                        @endif
                    </td>
                </tr>
                <tr class="order_field_row">
                    <td class="lbl_name_col2">Ngày Giao Hàng</td>
                    <td class="value_col2">
                        <div class="input-group date" id="edit_order_delivery_time" data-target-input="nearest"
                             style="width:200px;">
                            <input type="text" class="form-control datetimepicker-input"
                                   data-target="#edit_order_delivery_time"
                                   placeholder="dd/mm/yyyy" id="edit_order_delivery_time_text"
                                   value="" style="background-color:white;"/>
                            <div class="input-group-append" data-target="#edit_order_delivery_time"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="detail_order_content">
                <table width="100%" class="tbl_detail_order" id="tbl_detail_order">

                    <tr class="tbl_detail_order_header">
                        <th class="product_code">
                            Mã SP
                        </th>
                        <th class="size">
                            Size
                        </th>
                        <th class="color">
                            Màu
                        </th>
                        <th class="quantity">
                            Số lượng
                        </th>
                        <th class="discount_code">
                            Mã khuyến mại
                        </th>

                        <th class="price">
                            Giá
                        </th>
                        <th class="actually_collected">
                            Thực Thu
                        </th>
                        <th class="pick_money">
                            Tiền thu hộ
                        </th>
                        <th class="button">

                        </th>
                    </tr>
                    @if($detailEditable)
                        <tr class="tbl_detail_order_item" id="row_additional_detail_order">
                            <td>
                                @include("autocomplete", ["autocomplete_id"=>"detail_order_additional_product_code", "autocomplete_placeholder"=>"Nhập MSP",
"autocomplete_data"=>$list_suggestion_product_codes,"autocomplete_value" => ""])

                            </td>
                            <td style="text-align:center;">
                                <div class="dropdown" id="detail_order_additional_product_size">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="detail_order_additional_product_size_text"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        ___
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                         style="max-height:200px;overflow-y: auto;">
                                        @foreach ($list_product_size as $size)
                                            <a class="dropdown-item">{{$size}}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;" id="detail_order_additional_product_color">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="detail_order_additional_product_color_text"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        ___
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                         style="max-height:200px;overflow-y: auto;">
                                        @foreach ($list_product_color as $color)
                                            <a class="dropdown-item">{{$color}}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            <td style="text-align:center;">

                                <input type="number" class="form-control" min="1" value="1"
                                       style="margin: 0 auto;text-align:center;"
                                       id="detail_order_additional_product_quantity">

                            </td>
                            <td style="text-align:center;">
                                <input type="hidden" id="detail_order_additional_discount_id" value="-1">
                                <div class="dropdown" id="detail_order_additional_discount">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="detail_order_additional_discount_text"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        ___
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                         style="max-height:200px;overflow-y: auto;">
                                        <a class="dropdown-item" id="-1"><input type="hidden" value="-1"
                                                                                class="option_detail_order_discount_id">___</a>
                                        @foreach ($list_product_discount as $discount)
                                            <a class="dropdown-item" id="{{$discount->id}}"><input type="hidden"
                                                                                                   value="{{$discount->id}}"
                                                                                                   class="option_detail_order_discount_id">{{$discount->name}}
                                            </a>
                                        @endforeach

                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;" id="detail_order_additional_product_price">
                                0
                            </td>
                            <td style="text-align:center;" id="detail_order_additional_actually_collected">
                                0
                            </td>
                            <td style="text-align:center;" id="detail_order_additional_product_pick_money">
                                0
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="btn btn-success detail_order_btn_add"
                                        id="detail_order_btn_add">
                                    Thêm
                                </button>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_order_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_order_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>

        </div>

    </div>
</form>


<script>
    $(function () {
        $("#edit_order_delivery_time").datetimepicker({
            format: 'DD/MM/YYYY',
        });
        @if ($detailEditable)
        @foreach ($list_detail_orders as $detail_order)
        var row_index = $('.tbl_detail_order_item').length;

        $('#row_additional_detail_order').after(genRow(
            '{{$detail_order->marketing_product_code}}',
            '{{$detail_order->product_size}}',
            '{{$detail_order->product_color}}',
            '{{$detail_order->quantity}}',
            '{{$detail_order->discount_id}}',
            '{{$detail_order->discount_name}}',
            '{{$detail_order->actually_collected}}',
            '{{$detail_order->price}}',
            '{{$detail_order->pick_money}}',
            row_index));
        $('.detail_order_btn_update').first().click(handleUpdateBtnClicked);
        $('.detail_order_btn_delete').first().click(handleDeleteBtnClicked);
        $('.detail_order_updating_product_size').first().find('a').click(updatingRowProductSizeSelected);
        $('.detail_order_updating_product_color').first().find('a').click(updatingRowProductColorSelected);
        $('.detail_order_updating_discount').first().find('a').click(updatingRowProductDiscountSelected);
        @endforeach
        @endif


    });
</script>
