<link rel="stylesheet" href={{ asset('css/admin/admin_edit_product.css') }}>
<script src={{ asset('js/admin/admin_edit_product.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_product_id" value="{{$product->code}}">
    <div id="edit_product_dialog">
        <div id="edit_product_dialog_content">
            <div class="title">Nhập Thông Tin Sản phẩm</div>
            <table width="90%">
                <tr class="product_field_row">
                    <td class="lbl_name_col1">Mã Sản Phẩm</td>
                    <td class="value_col1">
                        @if ($product->code == "")
                            <input class="form-control" type="text" placeholder="Nhập mã sản phẩm"
                                   id="edit_product_code" value="{{$product->code}}">
                        @else
                            <input class="form-control" type="text" placeholder="Nhập mã sản phẩm"
                                   id="edit_product_code" value="{{$product->code}}" disabled>
                        @endif
                    </td>
                    <td class="lbl_name_col2">Tên</td>
                    <td class="value_col2"><input class="form-control" type="text" placeholder="Nhập tên sản phẩm"
                                                  id="edit_product_name" value="{{$product->name}}"></td>
                </tr>
                <tr class="product_field_row">
                    <td class="lbl_name_col1">Giá Bán</td>
                    <td class="value_col1"><input class="form-control" type="number" placeholder="Nhập giá bán"
                                                  id="edit_product_price" value="{{$product->price}}"></td>
                    <td class="lbl_name_col2">Giá Gốc</td>
                    <td class="value_col2"><input class="form-control" type="number" placeholder="Nhập giá gốc"
                                                  id="edit_product_historical_cost"
                                                  value="{{$product->historical_cost}}">
                    </td>
                </tr>
            </table>
            <div id="detail_product_content">
                <table width="100%" class="tbl_detail_product" id="tbl_detail_product">

                    <tr class="tbl_detail_product_header">
                        <th class="size">
                            Kích Cỡ
                        </th>
                        <th class="color">
                            Màu
                        </th>
                        <th class="button">

                        </th>
                    </tr>
                    <tr class="tbl_detail_product_item" id="row_additional_detail_product">
                        <td style="text-align:center">
                            <input type="text" class="form-control" id="detail_product_additional_size_text"
                                   autocomplete="off" style="width: 70%;margin: auto">
                        </td>
                        <td style="text-align:center;">
                            <input type="text" class="form-control" id="detail_product_additional_color_text"
                                   autocomplete="off" style="width: 70%;margin: auto">
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-success detail_product_btn_add"
                                    id="detail_product_additional_btn_add">
                                Thêm
                            </button>
                        </td>
                    </tr>

                </table>
            </div>
            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_product_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_product_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

<script>
    var list_suggest_product_sizes = JSON.parse({!!json_encode($list_suggest_product_sizes)!!});
    var list_suggest_product_colors = JSON.parse({!!json_encode($list_suggest_product_colors)!!});
    $("#detail_product_additional_size_text").autocomplete({
        source: list_suggest_product_sizes
    });
    $("#detail_product_additional_color_text").autocomplete({
        source: list_suggest_product_colors
    });
    @foreach($list_detail_products as $detail_product)
        var row_index = $('.tbl_detail_product_item').length;
        $('#row_additional_detail_product').after(genRow(
            '{{$detail_product->color}}',
            '{{$detail_product->size}}',
            row_index));
        $('.detail_product_btn_update').first().click(handleUpdateDetailProductBtnClicked);
        $('.detail_product_btn_delete').first().click(handleDeleteDetailProductBtnClicked);
        var detail_product_size = "detail_product_updating_product_size_" + row_index.toString();
        $("#" + detail_product_size).autocomplete({
            source: list_suggest_product_sizes
        });
        var detail_product_color = "detail_product_updating_product_color_" + row_index.toString();
        $("#" + detail_product_color).autocomplete({
            source: list_suggest_product_colors
        });
    @endforeach
</script>
