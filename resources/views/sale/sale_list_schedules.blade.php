@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <script src={{ asset('js/sale/sale_main.js') }}></script>
    <script src={{ asset('js/sale/sale_list_schedules.js' ) }}></script>
    <link rel="stylesheet" href={{ asset('css/sale/sale_list_schedules.css'  ) }}>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css'  ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js'  ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>

    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')

    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_schedule", "confirm_dialog_btn_positive_id"=>"schedule_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"schedule_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Nhắc Nhở</div>
    <table id="list_schedule_filter">
        <tr>
            <td class="label">Từ ngày</td>
            <td class="value">
                <div class="input-group date" id="schedule_filter_start_time" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input"
                           data-target="#schedule_filter_start_time"
                           placeholder="dd/mm/yyyy hh:mm:ss" id="schedule_filter_start_time_text"
                           value="{{$start_time_str}}"/>
                    <div class="input-group-append" data-target="#schedule_filter_start_time"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

            </td>
            <td class="label">Đến ngày</td>
            <td class="value">
                <div class="input-group date" id="schedule_filter_end_time" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#schedule_filter_end_time"
                           placeholder="dd/mm/yyyy hh:mm:ss" id="schedule_filter_end_time_text"
                           value="{{$end_time_str}}"/>
                    <div class="input-group-append" data-target="#schedule_filter_end_time"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </td>
            <td class="value">
                <button type="button" class="btn btn-warning btn_filter" id="schedule_btn_filter">Lọc</button>
            </td>
        </tr>
    </table>
    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_schedule_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_schedule_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_schedule_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>
    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="schedule_time">Thời Gian</td>
            <td class="schedule_note">Ghi Chú</td>
        </tr>
        @foreach($list_schedules as $schedule)
            @if($schedule->active)
                <tr class="tbl_item schedule_row" id="{{$schedule->id}}">
            @else
                <tr class="tbl_item" id="{{$schedule->id}}" style="background-color:#ededed;">
                    @endif
                    <td class="schedule_time">{{$schedule->timeStr()}}</td>
                    <td class="schedule_note">{{$schedule->note}}</td>
                </tr>
                @endforeach
    </table>
    @if(count($list_schedules) == 0)
        <div class="empty">Danh sách lịch rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_schedules->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_schedule"></div>
    <script type="text/javascript">

        $(document).ready(function () {
            $("#schedule_filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',
            });
            $("#schedule_filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',
            });
            $('#sale_menu_item_schedules').addClass('selected');
            document.title = 'Nhắc nhở';
        });


    </script>
@endsection

@section('menu')
    @include( "sale.menu")
@endsection

