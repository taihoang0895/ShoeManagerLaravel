@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_order_report.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css'  ) }}>

    <script src={{ asset('js/sale/sale_order_report.js' ) }}></script>
@endsection
@section('content')

    <div class="title">Báo Cáo Đơn Hàng</div>
    <div id="list_order_report_filter">
        <input type="hidden" id="filter_member_id_selected" value="{{$filter_member_id}}">
        <table>
            <tr>
                <td class="filter_by_member">
                    <div class="dropdown" id="filter_by_member">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="filter_by_member_text"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$filter_member_str}}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height:200px;overflow-y: auto;">
                            @foreach ($list_members as $member)
                                <a class="dropdown-item"><input type="hidden"
                                                                value="{{$member->id}}">{{$member->alias_name}}</a>
                            @endforeach
                        </div>
                    </div>

                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_filter" id="btn_search">Lọc</button>
                </td>

            </tr>
        </table>
    </div>
    <table class="tbl_order_report">
        <tr class="tbl_header_item">
            <td class="date">Ngày</td>
            <td class="total_order">Tổng Đơn</td>
            <td class="total_customer">Tổng Khách</td>
            <td class="percent">Phần Trăm</td>
        </tr>
        @foreach ($list_order_reports as $report)

            <tr class="tbl_item">
                <td class="date">{{$report->date_str}}</td>
                <td class="total_order">{{$report->total_order}}</td>
                <td class="total_customer">{{$report->total_customer}}</td>
                <td class="percent">{{$report->percent}}&#37</td>
            </tr>
        @endforeach
    </table>
    @if (count($list_order_reports) == 0)
        <div class="empty">Danh sách rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_order_reports->withQueryString()->links()}}
            </td>

        </tr>
    </table>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Báo Cáo Đơn Hàng';
            $('#sale_menu_item_report').addClass('selected');
        });
    </script>
@endsection
@section('menu')
    @include( "sale.menu")
@endsection
