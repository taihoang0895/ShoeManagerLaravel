@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css') }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_list_customers.css' ) }}>
    <script src={{ asset('js/sale/sale_list_customers.js' ) }}></script>

    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_customer", "confirm_dialog_btn_positive_id"=>"customer_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"customer_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Khách Hàng <span style="font-size : 0.9em;">({{$total_customer}})</span></div>
    <div id="list_customer_filter">
        <input type="hidden" id="filter_member_id_selected" value="{{$filter_member_id}}">
        <input type="hidden" id="filter_customer_state_selected" value="{{$filter_customer_state}}">
        <table>
            <tr>
                <td><input class="form-control" type="text" placeholder="Nhập số điện thoại"
                           value="{{$search_phone_number}}"
                           id="list_customer_search_phone_number">
                </td>
                <td class="filter_by_member" style="display: {{$filter_member_display}}">
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
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height:200px;overflow-y: auto;">
                            <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                            @foreach ($list_members as $member)
                                <a class="dropdown-item"><input type="hidden"
                                                                value="{{$member->id}}">{{$member->alias_name}}</a>
                            @endforeach
                        </div>
                    </div>

                </td>
                <td class="filter_customer_state" style="text-align: center;vertical-align: middle;">
                    <div class="dropdown" id="filter_customer_state">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="filter_customer_state_text"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if($filter_customer_state == -1 )
                                Chọn Trạng Thái
                            @else
                                {{$filter_customer_state_str}}
                            @endif
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                            @foreach ($listOrderStates as $orderState)
                                <a class="dropdown-item"><input type="hidden"
                                                                value="{{$orderState->id}}">{{$orderState->name}}</a>
                            @endforeach
                        </div>
                    </div>

                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_filter" id="list_customer_btn_search">Lọc</button>
                </td>

            </tr>
        </table>
    </div>
    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_customer_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_customer_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_customer_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl_customers">
        <tr class="tbl_header_item">
            <td class="code">MKH</td>
            <td class="created">Ngày Tạo</td>
            <td class="customer_name">Tên Khách</td>
            <td class="phone_number">Số Điện Thoại</td>
            <td class="state">Trạng Thái</td>
            <td class="province">Thành Phố</td>
           {{-- <td class="district">Quận Huyện</td>
            <td class="street">Đường/Phố</td>--}}

            <td class="show_detail_header"></td>


        </tr>
        @foreach ($list_customers as $customer)
            <tr class="tbl_item customer_row" id="customer_{{$customer->id}}">
                <td class="code">{{$customer->code}}</td>
                <td class="created">{{$customer->created_str}}</td>
                <td class="customer_name">{{$customer->name}}</td>
                <td class="phone_number">{{$customer->phone_number}}</td>
                <td class="state" style="background-color: {{$customer->customer_state_color}}">{{$customer->state_name}}</td>
                <td class="province">{{$customer->province_name}}</td>
            {{--    <td class="district">{{$customer->district_name}}</td>
                <td class="street">{{$customer->street_name}}</td>--}}

                <td class="show_detail"><input type="hidden" value="{{$customer->id}}">Xem chi tiết</td>
            </tr>
        @endforeach
    </table>
    @if (count($list_customers) == 0)
        <div class="empty">Danh sách khách hàng rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_customers->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_customer"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Khách hàng';
            $('#sale_menu_item_customers').addClass('selected');
        });

    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection
