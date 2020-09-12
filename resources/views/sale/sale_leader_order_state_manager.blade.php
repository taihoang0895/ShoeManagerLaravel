@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_leader_order_state_manager.css' ) }}>

    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="{% static 'main/css/extra/tempusdominus-bootstrap-4.css' %}">
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src={{ asset('js/sale/sale_leader_order_state_manager.js') }}></script>
    <link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
    <script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>

@endsection
@section('content')
    <div class="title">Quản Lý Trạng Thái Đơn Hàng <span style="font-size : 0.9em;">({{$total_order}})</span></div>
    <table id="list_order_filter">
        <input type="hidden" id="filter_order_state_id_selected" value="{{$order_state_id_str}}">
        <tr>
            <td class="filter_ghtk_code">
                <input class="form-control" type="text" placeholder="Nhập mã ghtk"
                       value="{{$search_ghtk_code}}"
                       id="list_order_state_search_ghtk_code"></td>
            </td>

            <td class="filter_start_time">
                <div class="input-group date" id="order_filter_start_time" data-target-input="nearest">
                    <label style="margin-top:6px;">Từ ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#order_filter_start_time"
                           placeholder="dd/mm/yyyy" id="order_filter_start_time_text"
                           value="{{$start_time_str}}"/>
                    <div class="input-group-append" data-target="#order_filter_start_time" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

            </td>
            <td class="filter_end_time">
                <div class="input-group date" id="order_filter_end_time" data-target-input="nearest">
                    <label style="margin-top:6px;">&nbsp;&nbsp;&nbsp;Đến ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#order_filter_end_time"
                           placeholder="dd/mm/yyyy" id="order_filter_end_time_text" value="{{$end_time_str}}"/>
                    <div class="input-group-append" data-target="#order_filter_end_time" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </td>
            <td class="filter_by_order_state">
                <div class="dropdown" id="filter_order_dropdown_state">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="filter_order_dropdown_state_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{$order_state_str}}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"><input type="hidden" id="state_id" value="-1">_______</a>
                        @foreach ($list_states as $state)
                            <a class="dropdown-item"><input type="hidden" id="state_id"
                                                            value="{{$state->id}}">{{$state->name}}
                            </a>
                        @endforeach
                    </div>
                </div>

            </td>

            <td class="btn_filter">
                <button type="button" class="btn btn-warning btn_filter" id="order_btn_filter">Lọc</button>
            </td>
        </tr>
    </table>

    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary item" id="btn_sync_order">Đồng Bộ
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary item" id="btn_update_order_state">Sửa Trạng Thái
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary item" id="btn_cancel_order">Huỷ Đơn
                </button>
            </td>
        </tr>
    </table>
    <table class="tbl_order_state_manager">
        <tr class="tbl_header_item">
            <td class="col_mark"><input type="checkbox" style="width:20px;height:20px;" id="cb_selected_all"></td>
            <td class="order_code">MHD</td>
            <td class="sale">Người Tạo</td>
            <td class="ghtk_label">Mã GHTK</td>
            <td class="created">Ngày Lập</td>
            <td class="order_state">Trạng Thái</td>
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

        @foreach ($list_orders as $order)
            <tr class="tbl_item order_state_manager_row">
                <td class="col_mark"><input type="checkbox" style="width:20px;height:20px;" class="cb_mark"><input
                        type="hidden" class="order_id" value="{{$order->id}}"></td>
                <td class="order_code">{{$order->code}}</td>
                <td class="sale">{{$order->sale_name}}</td>
                <td class="ghtk_label">{{$order->ghtk_label}}</td>
                <td class="created">{{$order->created_str}}</td>
                <td class="order_state">{{$order->order_state_name}}</td>
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
    </table>
    @if (count($list_orders) == 0)
        <div class="empty">không có đơn hàng nào</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_orders->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="form_order_state_manager"></div>


    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Đơn Hàng';
            $('#sale_menu_item_order_state_manager').addClass('selected');


            $("#list_order_state_search_ghtk_code").autocomplete({
                source: function (request, response) {
                    // Fetch data
                    $.ajax({
                        url: "/sale/search-ghtk-code/",
                        type: 'get',
                        dataType: "json",
                        data: {
                            search: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    // Set selection
                    $('#list_order_state_search_ghtk_code').val(ui.item.value);
                    return false;
                }
            });

            $("#order_filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });

            $("#order_filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            @if (count($list_orders) != 0)
            $('.tbl_order_state_manager tbody').css('width', $('.tbl_order_state_manager thead').width() + 17);
            @else
            $('.tbl_order_state_manager tbody').css('display', 'none');
            @endif

        });


    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection
