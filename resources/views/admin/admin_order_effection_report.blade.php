@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('admin_overview_report_detail_order_state.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_report_product_revenue.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css') }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
    <script src={{ asset('js/admin/admin_product_revenue_report.js' ) }}></script>
    <script src={{ asset('js/admin/admin_product_revenue_report.js' ) }}></script>
    <style>
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin-top: 30px;
            margin-left: 30px;
            margin-right: 30px;
            -webkit-box-shadow: 0px 0px 0px 0px #000;
            box-shadow: 0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            font-size: 1.2em !important;
            font-weight: bold !important;
            text-align: left !important;
            width: auto;
            padding: 0 10px;
            border-bottom: none;
        }
    </style>
@endsection
@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{$actives[0]}}" href="/admin/reports/">Tổng quan chung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[1]}}" href="/admin/report-product-revenue/">Doanh thu mẫu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[2]}}" href="/admin/report-order-type/">Tổng đơn</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[3]}}" href="/admin/report-order-effection/">Hiệu suất đơn</a>
        </li>
    </ul>
    <div id="filter_panel">
        <form method="get" action="/admin/report-order-effection/">
            <input type="hidden" id="filter_member_id_selected" name="filter_member_id" value="{{$filter_member_id}}">
            <input type="hidden" id="filter_order_time_type" name="filter_order_time_type"
                   value="{{$filter_order_time_type}}">
            <table>
                <tr>
                    <td>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Lọc theo thời gian</legend>
                            <table>
                                <tr>
                                    <td class="filter_order_report_time" id="filter_order_report_time_cell">
                                        <div class="input-group date" id="order_report_time"
                                             data-target-input="nearest">

                                            <input type="text" class="form-control datetimepicker-input" name="time1"
                                                   data-target="#order_report_time"
                                                   placeholder="dd/mm/yyyy" id="order_report_time_text"
                                                   value="{{$time1}}"/>
                                            <div class="input-group-append" data-target="#order_report_time"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="filter_order_report_time_2" id="filter_order_report_time_2_cell">
                                        <div class="input-group date" id="order_report_time_2"
                                             data-target-input="nearest">

                                            <input type="text" class="form-control datetimepicker-input" name="time2"
                                                   data-target="#order_report_time_2"
                                                   placeholder="dd/mm/yyyy" id="order_report_time_2_text"
                                                   value="{{$time2}}"/>
                                            <div class="input-group-append" data-target="#order_report_time_2"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="filter_order_report_by_time_type">
                                        <div class="dropdown" id="filter_order_report_by_time_type">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="filter_order_report_by_time_type_text"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{$filter_order_time_type_text}}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"><input type="hidden" value="0">Ngày</a>
                                                <a class="dropdown-item"><input type="hidden" value="1">Tháng</a>
                                                <a class="dropdown-item"><input type="hidden" value="2">Năm</a>

                                            </div>
                                        </div>

                                    </td>
                                    <td class="btn_filter">
                                        <button type="submit" class="btn btn-warning btn_filter"
                                                id="order_report_btn_filter">Lọc
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Lọc theo nhân viên</legend>
                            <table>
                                <tr>
                                    <td class="filter_by_member">
                                        <div class="dropdown" id="filter_by_member">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="filter_by_member_text"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{$filter_member_str}}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                                 style="max-height:200px;overflow-y: auto;">
                                                <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                                                @foreach ($list_members as $member)
                                                    <a class="dropdown-item"><input type="hidden"
                                                                                    value="{{$member->id}}">{{$member->alias_name}}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>

                                    </td>

                                    <td class="btn_filter">
                                        <button type="submit" class="btn btn-warning btn_filter"
                                                id="order_report_btn_filter">Lọc
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>

        </form>
    </div>

    <div id="report_content_panel">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Báo cáo hiệu quả</legend>
            <table class="table table-bordered table-dark table-striped" style="text-align: center;margin-top: 15px">
                <thead>
                <tr>
                    <th>
                        Mẫu
                    </th>
                    <th>
                        Đơn TC
                    </th>
                    <th>
                        Đơn hoàn
                    </th>
                    <th>
                        Tổng đơn
                    </th>
                    <th>
                        % TC
                    </th>
                    <th>
                        % Hoàn
                    </th>
                    <th>
                        Doanh thu thực
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($list_rows as $row)
                    <tr>
                        <td>
                            {{$row->product_code}}
                        </td>
                        <td>
                            {{$row->total_success_order}}
                        </td>
                        <td>
                            {{$row->total_returning_order}}
                        </td>
                        <td>
                            {{$row->total_order}}
                        </td>
                        <td>
                            {{$row->success_order_percent}}%

                        </td>
                        <td>
                            {{$row->returning_order_percent}}%
                        </td>
                        <td>
                            {{$row->revenue}}&nbsp;&#8363;
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </fieldset>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Doanh thu mẫu';
            $('#admin_menu_item_report').addClass('selected');

            $("#order_report_time").datetimepicker({
                @if ($filter_order_time_type == 0)
                format: 'DD/MM/YYYY'
                @elseif($filter_order_time_type == 1)
                format: 'MM/YYYY'
                @else
                format: 'YYYY'
                @endif
            });
            $("#order_report_time_2").datetimepicker({
                @if ($filter_order_time_type == 0)
                format: 'DD/MM/YYYY'
                @elseif($filter_order_time_type == 1)
                format: 'MM/YYYY'
                @else
                format: 'YYYY'
                @endif
            });
        });
    </script>

@endsection

@section('menu')
    @include( "admin.menu")
@endsection
