@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_product_history.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js') }}></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src={{ asset('js/storekeeper/storekeeper_product_history.js' ) }}></script>
@endsection
@section('content')

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="/sale/exporting-product-history/">Xuất Hàng</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/sale/order-history/">Đặt Hàng</a>
        </li>

    </ul>

    <div class="title">Lịch Sử Xuất Hàng</div>
    <table id="list_histories_filter">
        <tr>
            <td class="filter_start_time">
                <div class="input-group date" id="filter_start_time" data-target-input="nearest">
                    <label style="margin-top:6px;">Từ ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#filter_start_time"
                           placeholder="dd/mm/yyyy" id="filter_start_time_text"
                           value="{{$start_time_str}}"/>
                    <div class="input-group-append" data-target="#filter_start_time" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

            </td>
            <td class="filter_end_time">
                <div class="input-group date" id="filter_end_time" data-target-input="nearest">
                    <label style="margin-top:6px;">&nbsp;&nbsp;&nbsp;Đến ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#filter_end_time"
                           placeholder="dd/mm/yyyy" id="filter_end_time_text" value="{{$end_time_str}}"/>
                    <div class="input-group-append" data-target="#filter_end_time" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </td>

            <td class="btn_filter">
                <button type="button" class="btn btn-warning btn_filter" id="btn_filter">Lọc</button>
            </td>
        </tr>
    </table>

    <table class="tbl_histories">
        <tr class="tbl_header_item">
            <td class="date">Ngày</td>
            <td class="username">Username</td>
            <td class="action">Action</td>
            <td class="product_code">MSP</td>
            <td class="product_size">Size</td>
            <td class="product_color">Màu</td>
            <td class="quantity">Số Lượng</td>
        </tr>
        @foreach ($list_histories as $history)
            <tr class="tbl_item returning_product_row">
                <td class="date">{{$history->date_str}}</td>
                <td class="username">{{$history->username}}</td>
                <td class="action">{{$history->action}}</td>
                <td class="product_code">{{$history->product_code}}</td>
                <td class="product_size">{{$history->product_size}}</td>
                <td class="product_color">{{$history->product_color}}</td>
                <td class="quantity">{{$history->quantity}}</td>
            </tr>
        @endforeach
    </table>
    @if (count($list_histories) == 0)
        <div class="empty">Danh sách lịch sử rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_histories->withQueryString()->links()}}
            </td>

        </tr>
    </table>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Lịch Sử Xuất Hàng';
            $("#filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#tbl_product_summary .size_product_cell").each(function (index) {
                $('#tbl_product_detail .col_' + (index + 3)).width($(this).width());
            });
            $('#sale_menu_item_order_history').addClass('selected');
        });
    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection
