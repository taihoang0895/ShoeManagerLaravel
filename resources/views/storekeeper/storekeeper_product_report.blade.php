@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_product_report.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css') }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>

    <script src={{ asset('js/storekeeper/storekeeper_product_report.js') }}></script>
@endsection
@section('content')

    <ul class="nav nav-tabs">

        <li class="nav-item">
            <a class="nav-link {{$actives[0]}}" href="/storekeeper/importing-product-report/">Nhập Hàng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[1]}}" href="/storekeeper/exporting-product-report/">Xuất Hàng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[2]}}" href="/storekeeper/returning-product-report/">Hàng Hoàn</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[3]}}" href="/storekeeper/failed-product-report/">Hàng Lỗi</a>
        </li>
    </ul>
    @if($tab_index == 0)
        <div class="title">Báo Cáo Nhập Hàng</div>
    @endif
    @if($tab_index == 1)
        <div class="title">Báo Cáo Xuất Hàng</div>
    @endif
    @if($tab_index == 2)
        <div class="title">Báo Cáo Hoàn Hàng</div>
    @endif
    @if($tab_index == 3)
        <div class="title">Báo Cáo Hàng Lỗi</div>
    @endif
    <table id="product_report_filter">
        <tr>
            <td class="filter_start_time">
                <div class="input-group date" id="filter_start_time" data-target-input="nearest">
                    <label style="margin-top:6px;">Từ ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#filter_start_time"
                           placeholder="dd/mm/yyyy" id="filter_start_time_text"
                           value="{{$start_time}}"/>
                    <div class="input-group-append" data-target="#filter_start_time" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

            </td>
            <td class="filter_end_time">
                <div class="input-group date" id="filter_end_time" data-target-input="nearest">
                    <label style="margin-top:6px;">&nbsp;&nbsp;&nbsp;Đến ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input" data-target="#filter_end_time"
                           placeholder="dd/mm/yyyy" id="filter_end_time_text" value="{{$end_time}}"/>
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

    <table id="tbl_product_summary">
        <thead>
        <tr id="code_color_product">
            <td rowspan="4"
                style="min-width:125px; width:125px;max-width:125px;background-color:yellow;color:black;font-family : 'roboto_bold';"
                class="col_1">
                TỔNG NHẬP
            </td>
            <td rowspan="2"
                style=" text-decoration: none;background-color : #00ffff; min-width:70px;width:70px;color:black;text-align:center;font-size:17px;"
                class="col_2">
                TỔNG<br>KHO<br>CÒN
            </td>
            @foreach($code_color_cells as $code_color_cell)
                <td colspan="{{$code_color_cell->colspan}}"
                    style="min-width:{{$code_color_cell->width_str}};max-width:{{$code_color_cell->width_str}};height:{{$code_color_cell->height_str}};"
                    class="code_color_product_cell">
                    {{$code_color_cell->text}}
                </td>
            @endforeach
        </tr>
        <tr id="size_product">
            @foreach($size_cells as $size_cell)
                <td colspan="{{$size_cell->colspan}}"
                    style="min-width:{{$size_cell->width_str}};height:{{$size_cell->height_str}};"
                    class="size_product_cell">
                    {{$size_cell->text}}
                </td>
            @endforeach
        </tr>
        <tr id="quantity_by_size_product">
            <td style="background-color : #00ffff; min-width:70px;color:black;">{{$total_quantity}}</td>
            @foreach($quantity_by_size_cells as $quantity_cell)
                <td colspan="{{$quantity_cell->colspan}}"
                    style="min-width:{{$quantity_cell->width_str}};height:{{$quantity_cell->height_str}};"
                    class="quantity_by_size_product_cell">
                    {{$quantity_cell->text}}
                </td>
            @endforeach
        </tr>
        <tr id="quantity_by_code_color_product">
            <td style="background-color : #b7e1cd; min-width:70px;color:black;">{{$total_quantity}}</td>
            @foreach($quantity_by_code_color_cells as $quantity_cell)
                <td colspan="{{$quantity_cell->colspan}}"
                    style="min-width:{{$quantity_cell->width_str}};height:{{$quantity_cell->height_str}};"
                    class="quantity_by_code_color_product_cell">
                    {{$quantity_cell->text}}
                </td>
        @endforeach
        </thead>
        <tbody>
        @foreach($list_reports_by_date as $row)
            <tr class="product_by_date_row">
                @foreach($row as $col)
                    <td colspan="{{$col->colspan}}" style="min-width:{{$col->width_str}};height:{{$col->height_str}};"
                        class="col_{{ $loop->iteration }}">
                        {{$col->text}}
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>


    <script type="text/javascript">
        $(document).ready(function () {
            @if($tab_index == 0)
                document.title = 'Báo Cáo Nhập';
            @endif
                @if($tab_index == 1)
                document.title = 'Báo Cáo Xuất';
            @endif
                @if($tab_index == 2)
                document.title = 'Báo Cáo Hoàn';
            @endif
                @if($tab_index == 3)
                document.title = 'Báo Cáo Hàng Lỗi';
            @endif
            $('#storekeeper_menu_item_product_report').addClass('selected');
            $("#filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#tbl_product_summary .size_product_cell").each(function (index) {
                $('#tbl_product_summary .col_' + (index + 3)).width($(this).width());
            });
            $('#tbl_product_summary tbody').css('width', $('#tbl_product_summary thead').width() + 17);
            $('#tbl_product_summary').css('height', $(window).height() - $('#tbl_product_summary thead').offset().top);
            $('#tbl_product_summary tbody').css('height', $(window).height() - $('#tbl_product_summary thead').offset().top - $('#tbl_product_summary thead').height() - 20);

            $(window).on('resize', function () {
                $("#tbl_product_summary .size_product_cell").each(function (index) {
                    $('#tbl_product_summary .col_' + (index + 3)).width($(this).width());
                });
                $('#tbl_product_summary tbody').css('width', $('#tbl_product_summary thead').width() + 17);
                $('#tbl_product_summary').css('height', $(window).height() - $('#tbl_product_summary thead').offset().top);
                $('#tbl_product_summary tbody').css('height', $(window).height() - $('#tbl_product_summary thead').offset().top - $('#tbl_product_summary thead').height() - 20);

            });

        });


    </script>
@endsection

@section('menu')
    @include( "storekeeper.menu")
@endsection
