
<link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
<link rel="stylesheet" href={{ asset('css/common/detail_product.css') }}>

<div id="detail_product_dialog">
    <div id="detail_product_dialog_content">
        <div class="title">Thông Tin Sản phẩm</div>
        <table id="tbl_detail_product">
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
            <tr id="remaining_quantity_product">
                <td rowspan=2
                    style="background-color : red; color:yellow; min-width:140px;height:60px;text-align:center;">
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

        </table>
        <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_cancel" id="detail_product_btn_cancel">Ẩn
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    $('#detail_product_btn_cancel').click(function () {
        $('#detail_product_dialog').css('display', 'none');
    });

    $(document).ready(function () {
        $('#tbl_detail_product').css('display', '');
        if ($('#detail_product_dialog_content').width() < $('#tbl_detail_product').width()) {
            $('#tbl_detail_product').css('display', 'block');
        }
    });


</script>
