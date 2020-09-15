@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_revenue_report.css') }}>
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_main.css') }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
    <script src={{ asset('js/marketing/marketing_revenue_report.js' ) }}></script>
@endsection
@section('content')
    <div class="title">Chi Tiết Chi Tiêu</div>
    <div id="marketing_revenue_report_filter">
        <input type="hidden" id="report_time_type" value="{{$report_time_type}}">
        <input type="hidden" id="filter_member_id_selected" value="{{$filter_member_id}}">
        <table>
            <tr>
                <td class="filter_revenue_report_time" id="filter_revenue_report_time_cell">
                    <div class="input-group date" id="marketing_revenue_report_time" data-target-input="nearest">

                        <input type="text" class="form-control datetimepicker-input"
                               data-target="#marketing_revenue_report_time"
                               placeholder="dd/mm/yyyy" id="marketing_revenue_report_time_text"
                               value="{{$report_time_str}}"/>
                        <div class="input-group-append" data-target="#marketing_revenue_report_time"
                             data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>

                </td>

                <td class="filter_revenue_report_by_time_type">
                    <div class="dropdown" id="filter_revenue_report_by_time_type">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="filter_revenue_report_by_time_type_text"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{$revenue_report_time_type}}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"><input type="hidden" value="0">Ngày</a>
                            <a class="dropdown-item"><input type="hidden" value="1">Tháng</a>
                            <a class="dropdown-item"><input type="hidden" value="2">Năm</a>
                        </div>
                    </div>

                </td>
                <td class="filter_by_member">
                    <div class="dropdown" id="filter_by_member">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="filter_by_member_text"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if($filter_member_id == -1)
                                Chọn Người Tạo
                            @else
                                {{$filter_member_str}}
                            @endif
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                            @foreach($list_members as $member)
                                <a class="dropdown-item"><input type="hidden"
                                                                value="{{$member->id}}">{{$member->alias_name}}</a>
                            @endforeach
                        </div>
                    </div>

                </td>
                <td class="btn_filter">
                    <button type="button" class="btn btn-warning btn_filter" id="revenue_report_btn_filter">Lọc</button>
                </td>
            </tr>
        </table>
    </div>

    <div id="revenue_report_content">
        @if(count($table) != 0)
            <table id="tbl_revenue_report">
                <tr>
                    <td class="lbl_user_name revenue_report_cell">
                        <svg viewBox="0 0 10 10" preserveAspectRatio="none">
                            <line x1="0" y1="0" x2="10" y2="10" stroke="black" stroke-width="0.1"/>
                        </svg>
                        <div style="float : right;margin-right:10px;">Số TK</div>
                        <div style="margin-top:30px;float : left;margin-left:10px;">User</div>
                    </td>
                    @foreach($col_names as $col_name)
                        <td class="bank_account revenue_report_cell">{{$col_name}}</td>
                    @endforeach
                </tr>
                @foreach($row_names as $row_name)
                    <tr>
                        <td class="user_name revenue_report_cell">{{$row_name}}</td>
                        @foreach($col_names as $col_name)
                            <td class="report_value revenue_report_cell">{{$table[$row_name][$col_name]}}&nbsp;&#8363;
                            </td>
                        @endforeach

                    </tr>

                @endforeach

            </table>
        @else
            <div class="empty">Không tìm thấy báo cáo nào trong ngày</div>
        @endif
    </div>
    <script>
        $(document).ready(function () {

            $("#marketing_revenue_report_time").datetimepicker({
                @if ($report_time_type == 0)
                format: 'DD/MM/YYYY'
                @elseif($report_time_type == 1)
                format: 'MM/YYYY'
                @else
                format: 'YYYY'
                @endif
            });

        });

    </script>

@endsection

@section('menu')
    @include( "marketing.menu")
@endsection
