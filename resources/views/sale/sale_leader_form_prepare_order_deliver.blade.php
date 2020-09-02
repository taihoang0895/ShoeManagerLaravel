<link rel="stylesheet" href={{ asset('css/sale/sale_leader_form_prepare_order_deliver.css') }}>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <div id="order_delivering_dialog">
        <div id="order_delivering_dialog_content">
            <div class="title">Danh Sách Hóa Đơn Đẩy Lên GHTK</div>
            <table class="tbl_order_delivering" id="tbl_order_delivering">
                <thead>
                <tr class="tbl_header_item">
                    <td class="col_mark" id="col_mark_header" width="70px">
                    </td>
                    <td class="order_code" id="order_code_header">MHD</td>
                    <td class="customer_phone" id="customer_phone_header">SĐT</td>
                    <td class="product_name" id="product_name_header">Tên sản phẩm</td>
                    <td class="quantity" id="quantity_header">Số Lượng</td>
                    <td class="actually_collected" id="actually_collected_header">Giá trị hàng</td>
                    <td class="pick_money" id="pick_money_header">Tiền thu hộ</td>
                </tr>
                </thead>
                <tbody style="display: none">
                @foreach ($list_orders as $order)
                    <tr class="tbl_item order_delivering_row" id="order_delivering_row_{{$order->id}}">
                        <input type="hidden" value="{{$order->id}}" class="order_id">
                        <td class="col_mark">
                            <div class="loader"></div>
                            <img class="img_failed" src={{ asset('images/ic_failed.png'  ) }}>
                            <img class="img_success" src={{ asset('images/ic_success.png') }}>
                            <input type="hidden" class="order_id" value="{{$order->id}}">
                        </td>
                        <td class="order_code">{{$order->code}}</td>
                        <td class="customer_phone">{{$order->customer_phone}}</td>
                        <td class="product_name">{!! $order->product_name !!}</td>
                        <td class="quantity">{!!$order->total_quantity!!}</td>
                        <td class="actually_collected">{!!$order->actually_collected!!}</td>
                        <td class="pick_money">{!!$order->pick_money!!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="push_order_btn_ok">Bắt Đầu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="push_order_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
<script src={{ asset('js/sale/sale_leader_form_prepare_order_deliver.js') }}></script>
<script type="text/javascript">

    $(document).ready(function () {
        $('#tbl_order_delivering').css('max-height', $(window).height() - $('#tbl_order_delivering thead').offset().top - 120);
        $('#tbl_order_delivering tbody').css('min-width', $('#tbl_order_delivering thead').width());
        $('#tbl_order_delivering tbody').css('max-height', $(window).height() - $('#tbl_order_delivering').offset().top - $('#tbl_order_delivering thead').height() - 120);
        setTimeout(function () {
            var col_mark_width = $('#col_mark_header').width();
            $('.order_delivering_row .col_mark').each(function () {
                $(this).width(col_mark_width);
                $(this).css('min-width', col_mark_width);

            });

            var order_code_width = $('#order_code_header').width();
            $('.order_delivering_row .order_code').each(function () {
                $(this).width(order_code_width);
                $(this).css('min-width', order_code_width);
            });

            var customer_phone_width = $('#customer_phone_header').width();
            $('.order_delivering_row .customer_phone').each(function () {
                $(this).width(customer_phone_width);
                $(this).css('min-width', customer_phone_width);
            });

            var product_name_width = $('#product_name_header').width();
            $('.order_delivering_row .product_name').each(function () {
                $(this).width(product_name_width);
                $(this).css('min-width', product_name_width);
            });

            var quantity_width = $('#quantity_header').width();
            $('.order_delivering_row .quantity').each(function () {
                $(this).width(quantity_width);
                $(this).css('min-width', quantity_width);
            });

            var actually_collected_width = $('#actually_collected_header').width();
            $('.order_delivering_row .actually_collected').each(function () {
                $(this).width(actually_collected_width);
                $(this).css('min-width', actually_collected_width);
            });

            var pick_money_width = $('#pick_money_header').width();
            $('.order_delivering_row .pick_money').each(function () {
                $(this).width(pick_money_width);
                $(this).css('min-width', pick_money_width);

            });
            $('#order_delivering_dialog_content #tbl_order_delivering tbody').css('display', 'block');
        }, 100)
    });

</script>

