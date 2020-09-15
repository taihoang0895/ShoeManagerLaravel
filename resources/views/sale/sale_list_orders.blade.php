@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css'  ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_leader_list_orders.css'  ) }}>
    <script src={{ asset('js/sale/sale_leader_list_orders.js'  ) }}></script>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css'  ) }}>


    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js'  ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js'  ) }}></script>
    <script src={{ asset('js/sale/sale_main.js'  ) }}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">

@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_order", "confirm_dialog_btn_positive_id"=>"order_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"order_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Hóa Đơn <span style="font-size : 0.9em;">({{$total_order}})</span></div>
    <table id="list_order_filter">
        <input type="hidden" id="filter_order_state_id_selected" value="{{$order_state_id_str}}">
        <input type="hidden" id="filter_member_id_selected" value="{{$filter_member_id}}">
        <input type="hidden" id="filter_order_type_selected" value="{{$filter_order_type}}">
        <tr>
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
                        <a class="dropdown-item"><input type="hidden" id="state_id" value="">_______</a>
                        @foreach ($list_states as $state)
                            <a class="dropdown-item"><input type="hidden" id="state_id"
                                                            value="{{$state->id}}">{{$state->name}}
                            </a>
                        @endforeach
                    </div>
                </div>

            </td>
            <td class="filter_by_member">
                <div class="dropdown" id="filter_by_member">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="filter_by_member_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($filter_member_id == -1 )
                            Chọn Người Tạo
                        @else
                            {{$filter_member_str}}
                        @endif
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                        @foreach ($list_members as $member)
                            <a class="dropdown-item"><input type="hidden"
                                                            value="{{$member->id}}">{{$member->alias_name}}</a>
                        @endforeach
                    </div>
                </div>

            </td>
            <td class="filter_order_type" style="text-align: center;vertical-align: middle;">
                <div class="dropdown" id="filter_order_type">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="filter_order_type_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($filter_order_type == -1 )
                            Lọc Test Đơn
                        @else
                            {{$filter_order_type_str}}
                        @endif
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"><input class="order_type_id" type="hidden" value="-1">_______</a>
                        <a class="dropdown-item"><input class="order_type_id" type="hidden" value="1">Đơn Test</a>
                        <a class="dropdown-item"><input class="order_type_id" type="hidden" value="0">Đơn Thực</a>
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
                <button type="button" class="btn btn-secondary btn_add item" id="list_orders_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_orders_customer_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_orders_customer_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>
    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="order_code">MHD</td>
            <td class="order_sale">Người Tạo</td>
            <td class="order_created">Ngày lập</td>
            <td class="order_customer_name">Tên Khách</td>
            <td class="order_customer_phone_number">SĐT</td>
            <td class="order_state">Trạng thái</td>
            <td class="order_product_name">Sản phẩm</td>
            <td class="detail"></td>
        </tr>
        @foreach ($list_orders as $order)

            <tr class="tbl_item order_row" id="order_row_{{$order->id}}">
                <td class="order_code">
                    <div>{{$order->code}}</div>
                </td>
                <td class="order_sale">
                    <div>{{$order->sale_name}}</div>
                </td>
                <td class="order_created">
                    <div>{{$order->created_str}}</div>
                </td>
                <td class="order_customer_name">  <div>{{$order->customer_name}}</div></td>
                <td class="order_customer_phone">
                    <div>{{$order->customer_phone}}</div>
                </td>
                <td class="order_state">
                    <div>{{$order->order_state_name}}</div>
                </td>

                <td class="order_product_name">
                    <div>{!! $order->list_order_codes !!}</div>
                </td>
                <td class="show_detail_order" id="order_id_{{$order->id}}">xem chi tiết</td>
            </tr>
        @endforeach
    </table>
    @if (count($list_orders) == 0)
        <div class="empty">Danh sách hóa đơn rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_orders->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="detail_order_show_detail_item"></div>
    <script type="text/javascript">

        $(document).ready(function () {
            $("#order_filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#order_filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            document.title = 'Hoá đơn';
            $('#sale_menu_item_orders').addClass('selected');
        });
    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection

