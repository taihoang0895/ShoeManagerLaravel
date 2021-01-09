<html>
<head>
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/base.css')}}>
    <link rel="stylesheet" href={{  asset('css/header.css')}}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <script src="{{ asset('js/extra/jquery.js')}}"></script>
    <script src="{{ asset('js/base.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href={{ asset('css/admin/admin_overview_report_weekly.css' ) }}>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css') }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
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
</head>
<body style="background-color: #E5E5E5">
<div id="filter_panel">
    <form method="get" action="/admin/overview-report-weekly/">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Lọc theo thời gian</legend>
            <table>
                <tr>
                    <td class="filter_overview_report_weekly_time" id="filter_overview_report_weekly_time_cell">
                        <div class="input-group date" id="overview_report_weekly_time" data-target-input="nearest">

                            <input type="text" class="form-control datetimepicker-input" name="time"
                                   data-target="#overview_report_weekly_time"
                                   placeholder="yyyy" id="overview_report_weekly_time_text" value="{{$time}}"/>
                            <div class="input-group-append" data-target="#overview_report_weekly_time"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>

                    <td class="btn_filter">
                        <button type="submit" class="btn btn-warning btn_filter" id="order_report_btn_filter">Lọc
                        </button>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
<div id="report_content_panel">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Báo cáo số lượng đơn hàng</legend>
        <table width="100%" class="table table-bordered table-dark table-striped"
               style="text-align: center;">
            <thead>
            <tr>
                @foreach($weekly_reports as $report)
                    <th>
                        {{$report->key}}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach($weekly_reports as $report)
                    <td>
                        {{$report->value}}
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
        @if (count($weekly_reports) == 0)
            <div class="empty" style="margin-bottom:0px;">Danh sách rỗng</div>
        @else
            <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
                <tr>
                    <td>
                        {{$weekly_reports->withQueryString()->links()}}
                    </td>

                </tr>
            </table>
        @endif
    </fieldset>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#overview_report_weekly_time").datetimepicker({
            format: 'YYYY'
        });

    });
</script>

</body>
</html>