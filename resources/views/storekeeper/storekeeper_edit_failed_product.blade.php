<link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_edit_failed_products.css') }}>
<script src={{ asset('js/storekeeper/storekeeper_edit_failed_products.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">


<form method="post">
    @csrf
    <input type="hidden" id="edit_failed_product_id" value="{{$failed_product->id}}">
    <div id="edit_failed_product_dialog">
        <div id="edit_failed_product_dialog_content">
            <div class="title">Nhập Số Lượng Hàng Lỗi</div>
            <table width="90%">
                <tr class="failed_product_field_row">
                    <td class="lbl_name" style="padding-top:0px;">MSP</td>
                    <td class="value" style="padding-top:0px;">
                        @if($failed_product->id > 0)
                            <input id="product_code" class="form-control" value={{$product_code_selected}} disabled>
                        @else
                            @include("autocomplete", ["autocomplete_id"=>"product_code", "autocomplete_placeholder"=>"Nhập mã sản phẩm",
                           "autocomplete_value"=>$product_code_selected, "autocomplete_data"=>$list_product_codes])
                        @endif

                    </td>
                </tr>
                <tr class="failed_product_field_row">
                    <td class="lbl_name">Size</td>
                    <td class="value">
                        <div class="dropdown" id="edit_product_size">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_product_size_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$product_size_selected}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @foreach($list_product_sizes as $product_size)
                                    <a class="dropdown-item">{{$product_size}}</a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="failed_product_field_row">
                    <td class="lbl_name">Màu</td>
                    <td class="value">
                        <div class="dropdown" id="edit_product_color">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_product_color_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$product_color_selected}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @foreach($list_product_colors as $color)
                                    <a class="dropdown-item">{{$color}}</a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr >
                 <tr class="failed_product_field_row">
                    <td class="lbl_name">Số Lượng</td>
                    <td class="value">
                        <input type="number" class="form-control" min="1" value="{{$failed_product->quantity}}" id="product_quantity">
                    </td>
                </tr>
                 <tr class="failed_product_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value">
                        <textarea class="form-control" rows="4" id="note">{{$failed_product->note}}</textarea>
                    </td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_failed_product_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_failed_product_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
