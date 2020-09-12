@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_leader_order_deliver.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <script src={{ asset('js/sale/sale_leader_order_deliver.js'  ) }}></script>
@endsection
@section('content')
    <div class="title">Đăng Đơn Hàng</div>
    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary  btn-success item" id="list_orders_btn_add">Đẩy đơn sang
                    GHTK
                </button>
            </td>
        </tr>
    </table>
    <table class="tbl_order_deliver">
        <thead>
        <tr class="tbl_header_item">
            <td class="col_mark"><input type="checkbox" style="width:20px;height:20px;" id="cb_selected_all"></td>
            <td class="order_code">MHD</td>
            <td class="phone_number">SĐT</td>
            <td class="customer_name">Tên khách hàng</td>

            <td class="customer_province">Thành Phố</td>
            <td class="customer_district">Quận/Huyện</td>
            <td class="customer_street">Đường/Phố</td>
            <td class="customer_address">Địa chỉ</td>
            <td class="product_name">Tên sản phẩm</td>
            <td class="quantity">Số Lượng</td>
            <td class="kg">Khối lượng</td>
            <td class="actually_collected">Giá trị hàng</td>
            <td class="pick_money">Tiền thu hộ</td>
            <td class="note">Ghi chú</td>
        </tr>
        </thead>
        <tbody>
        @foreach ($list_orders as $order)
            <tr class="tbl_item">
                <td class="col_mark"><input type="checkbox" style="width:20px;height:20px;" class="cb_mark"><input
                        type="hidden" class="order_id" value="{{$order->id}}"></td>
                <td class="order_code">{{$order->code}}</td>
                <td class="phone_number">{{$order->customer_phone}}</td>
                <td class="customer_name">{{$order->customer_name}}</td>
                <td class="customer_province">{{$order->customer_province_name}}</td>
                <td class="customer_district">{{$order->customer_district_name}}</td>
                <td class="customer_street">{{$order->customer_street_name}}</td>
                <td class="customer_address">{{$order->customer_address}}</td>
                <td class="product_name">{!! $order->product_name !!}</td>
                <td class="quantity">{!!$order->total_quantity!!}</td>
                <td class="kg">{!!$order->kg!!}</td>
                <td class="actually_collected">{!!$order->actually_collected!!}</td>
                <td class="pick_money">{!!$order->pick_money!!}</td>
                <td class="note">{{$order->note}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
    @if (count($list_orders) == 0)
        <div class="empty">không có đơn hàng nào</div>
    @endif
    <div id="form_order_delivering"></div>
    <script type="text/javascript">

        $(document).ready(function () {
            document.title = 'Đăng Đơn';
            $('#sale_menu_item_order_deliver').addClass('selected');
            @if (count($list_orders) != 0)
            $('.tbl_order_deliver tbody').css('width', $('.tbl_order_deliver thead').width() + 17);
            $('.tbl_order_deliver').css('height', $(window).height() - $('.tbl_order_deliver thead').offset().top);
            $('.tbl_order_deliver tbody').css('height', $(window).height() - $('.tbl_order_deliver thead').offset().top - $('.tbl_order_deliver thead').height() - 20);
            @else
            $('.tbl_order_deliver tbody').css('height', '0px');
            @endif

        });

    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection
