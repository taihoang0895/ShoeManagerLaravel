@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_inventory_report.css' ) }}>
@endsection
@section('content')
    <div class="title">Báo Cáo Tồn Kho</div>

    <table id="tbl_inventory_report">
        <tr id="code_color_product">
            <td rowspan=2 style="background-color : #1c4587; min-width:140px;">
            </td>
            <td rowspan=2
                style=" text-decoration: none;background-color : #00ffff; min-width:70px;color:black;text-align:center;">
                TỔNG<br>KHO<br>CÒN
            </td>
            @foreach($code_color_cells as $code_color_cell)
                <td colspan="{{$code_color_cell->colspan}}"
                    style="min-width:{{$code_color_cell->width_str}};height:{{$code_color_cell->height_str}};"
                    class="code_color_product_cell">
                    &nbsp;{{$code_color_cell->text}}&nbsp;
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
        <tr id="remaining_quantity_product">
            <td rowspan=2 style="background-color : red; color:yellow; min-width:140px;height:60px;text-align:center;">
                TỔNG TRONG<br>KHO CÒN
            </td>
            <td class="sum_quantity_cell">{{$sum_remaining_quantity}}</td>
            @foreach($remaining_quantity_cells as $remaining_quantity_cell)
                <td colspan="{{$remaining_quantity_cell->colspan}}"
                    style="min-width:{{$remaining_quantity_cell->width_str}};height:{{$remaining_quantity_cell->height_str}};"
                    class="remaining_quantity_product_cell">
                    {{$remaining_quantity_cell->text}}
                </td>
            @endforeach
        </tr>

        <tr id="total_remaining_quantity_product">
            <td class="sum_quantity_cell">{{$sum_remaining_quantity}}</td>
            @foreach($total_remaining_quantity_cells as $total_remaining_quantity_cell)
                <td colspan="{{$total_remaining_quantity_cell->colspan}}"
                    style="min-width:{{$total_remaining_quantity_cell->width_str}};height:{{$total_remaining_quantity_cell->height_str}};"
                    class="total_remaining_quantity_product_cell">
                    {{$total_remaining_quantity_cell->text}}
                </td>
            @endforeach
        </tr>

        <tr id="importing_quantity_product">
            <td class="lbl_importing_quantity_product">
                HÀNG NHẬP
            </td>
            <td class="sum_quantity_cell">{{$sum_importing_quantity}}</td>
            @foreach($importing_quantity_cells as $importing_quantity_cell)

                <td colspan="{{$importing_quantity_cell->colspan}}"
                    style="min-width:{{$importing_quantity_cell->width_str}};height:{{$importing_quantity_cell->height_str}};"
                    class="importing_quantity_product_cell">
                    {{$importing_quantity_cell->text}}
                </td>
            @endforeach
        </tr>
        <tr id="exporting_quantity_product">
            <td class="lbl_exporting_quantity_product">
                KHO XUẤT
            </td>
            <td class="sum_quantity_cell">{{$sum_exporting_quantity}}</td>
            @foreach($exporting_quantity_cells as $exporting_quantity_cell)
                <td colspan="{{$exporting_quantity_cell->colspan}}"
                    style="min-width:{{$exporting_quantity_cell->width_str}};height:{{$exporting_quantity_cell->height_str}};"
                    class="exporting_quantity_product_cell">
                    {{$exporting_quantity_cell->text}}
                </td>
            @endforeach
        </tr>

        <tr id="returning_quantity_product">
            <td class="lbl_returning_quantity_product">
                HOÀN
            </td>
            <td class="sum_quantity_cell">{{$sum_returning_quantity}}</td>
            @foreach($returning_quantity_cells as $returning_quantity_cell)
                <td colspan="{{$returning_quantity_cell->colspan}}"
                    style="min-width:{{$returning_quantity_cell->width_str}};height:{{$returning_quantity_cell->height_str}};"
                    class="returning_quantity_product_cell">
                    {{$returning_quantity_cell->text}}
                </td>
            @endforeach
        </tr>

        <tr id="failed_quantity_product">
            <td class="lbl_failed_quantity_product">
                HÀNG LỖI
            </td>
            <td class="sum_quantity_cell">{{$sum_failed_quantity}}</td>
            @foreach($failed_quantity_cells as $failed_quantity_cell)
                <td colspan="{{$failed_quantity_cell->colspan}}"
                    style="min-width:{{$failed_quantity_cell->width_str}};height:{{$failed_quantity_cell->height_str}};"
                    class="failed_quantity_productt_cell">
                    {{$failed_quantity_cell->text}}
                </td>
            @endforeach
        </tr>
    </table>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Tồn Kho';
            $('#storekeeper_menu_item_inventory_report').addClass('selected');
        });


    </script>
@endsection

@section('menu')
    @include( "storekeeper.menu")
@endsection

