<link rel="stylesheet" href={{ asset('css/sale/sale_detail_order.css'  ) }}>
<div id="show_detail_order_dialog">
    <input type="hidden" id="add_order_id" value="-1">
    <input type="hidden" id="add_order_state_id" value="-1">
    <input type="hidden" id="add_order_fail_reason_id" value="-1">
    <div id="show_detail_order_dialog_content">
        <div class="title" style="margin-top:15px;margin-bottom:30px;">Thông Tin Hóa Đơn</div>
        <table width="90%">
            <tr class="order_field_row">
                <td class="lbl_name_col1" style="padding-top:0px;">Mã hóa đơn</td>
                <td class="value_col1" style="padding-top:0px;">{{$order->code}}</td>
                <td class="lbl_name_col2" style="padding-top:0px;">MHD hoàn</td>
                <td class="value_col2" style="padding-top:0px;">{{$order->replace_order_code}}</td>
            </tr>
            <tr class="order_field_row">
                <td class="lbl_name_col1">Mã Khách</td>
                <td class="value_col1">{{$order->customer_code}}</td>
                <td class="lbl_name_col1">Tên Khách Hàng</td>
                <td class="value_col1">{{$order->customer_name}}</td>
            </tr>
            <tr class="order_field_row">
                <td class="lbl_name_col1">Trạng Thái</td>
                <td class="value_col1">
                    <label>{{$order->order_state_name}}</label>
                </td>
                <td class="lbl_name_col2">Ngày Lập</td>
                <td class="value_col2">
                    <label>{{$order->created_str}}</label>
                </td>

            </tr>
            <tr class="order_field_row">
                <td class="lbl_name_col1">Lý Do Lỗi</td>
                <td class="value_col1">
                    <label>{{$order->order_fail_cause}}</label>
                </td>
                <td class="lbl_name_col2">Ghi Chú</td>
                <td class="value_col2">
                    <label>{{$order->note}}</label>
                </td>
            </tr>
            <tr class="order_field_row">
                <td class="lbl_name_col1">Đơn Test</td>
                <td class="value_col1">
                    @if($order->is_test)
                        <input type="checkbox" value="" style="width:20px;height:20px;margin:0 auto;"
                               onclick="return false;"
                               class="tbl_detail_order_item_view" checked>

                    @else
                        <input type="checkbox" value="" style="width:20px;height:20px;margin:0 auto;"
                               onclick="return false;"
                               class="tbl_detail_order_item_view">
                    @endif
                </td>
                <td class="lbl_name_col2">Ngày Giao Hàng</td>
                <td class="value_col2">
                    <label>{{$order->delivery_time_str}}</label>
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
                        Kích cỡ
                    </th>
                    <th class="color">
                        Màu
                    </th>
                    <th class="quantity">
                        Số lượng
                    </th>
                    <th class="discount_code">
                        khuyến mại
                    </th>
                    <th class="price">
                        Giá
                    </th>
                    <th class="pick_money">
                        Tiền thu hộ
                    </th>
                    <th class="actually_collected">
                        Thực Thu
                    </th>
                </tr>


                @foreach ($order->list_detail_orders as $detail_order)
                <tr class="tbl_detail_order_item" id="tbl_detail_order_item_{{$loop->iteration}}">
                    <input type="hidden" id="detail_order_item_{{$loop->iteration}}_discount_id" value="-1">
                    <td style="text-align:center;">
                        <div>{{$detail_order->marketing_product_code}}</div>
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->product_size}}</div>
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->product_color}}</div>
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->quantity}}</div>
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->discount_name}}</div>
                    </td>
                    <td style="text-align:center;">
                        {{$detail_order->price_str}}&nbsp;&#8363;
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->pick_money_str}}&nbsp;&#8363;</div>
                    </td>
                    <td style="text-align:center;">
                        <div>{{$detail_order->actually_collected_str}}&nbsp;&#8363;</div>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_cancel" id="detail_order_btn_cancel">Ẩn</button>
                </td>
            </tr>
        </table>

    </div>

</div>

<script>
    $('#detail_order_btn_cancel').click(function () {
        $('#show_detail_order_dialog').css('display', 'none');
    });
</script>
