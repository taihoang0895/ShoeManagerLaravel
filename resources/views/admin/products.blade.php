@extends ('base_layout')

@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_list_products.css') }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css') }}>
    <script src={{ asset('js/admin/admin_list_products.js') }}></script>


    <link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
    <script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_product", "confirm_dialog_btn_positive_id"=>"product_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"product_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Sản Phẩm</div>

    <div id="list_products_filter">
        <table>
            <tr>
                <td><input class="form-control" type="text" name="search_text" class="search_text"
                           placeholder="Nhập mã sản phẩm" value="{{$search_product_code}}"
                           id="list_product_search_product_code" aria-label="Search"></td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_search_text" id="list_product_btn_search">Tìm Kiếm
                    </button>
                </td>

            </tr>
        </table>
    </div>

    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_products_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_products_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_products_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="product_code">Mã SP</td>
            <td class="name">Tên</td>
            <td class="price">Giá Bán</td>
            <td class="historical_cost">Giá Gốc</td>
            <td class="show_detail_header"></td>

        </tr>
        @if (count($products) > 0)
            @foreach ($products as $product_row)
                <tr class="tbl_item product_row" id="product_{{$product_row->code}}">
                    <td class="product_code">{{$product_row->code}}</td>
                    <td class="name">{{$product_row->name}}</td>
                    <td class="price">{{$product_row->getPriceStr()}}&nbsp;&#8363;</td>
                    <td class="historical_cost">{{$product_row->getHistoricalCostStr()}}&nbsp;&#8363;</td>
                    <td class="show_detail"><input type="hidden" value="{{$product_row->code}}">xem chi tiết</td>
                </tr>
            @endforeach
        @endif

    </table>
    @if (count($products) == 0)
        <div class="empty">Danh sách sản phẩm rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$products->withQueryString()->links()}}
            </td>
        </tr>
    </table>
    <div id="dialog_edit_product"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Sản phẩm';
            $('#admin_menu_item_products').addClass('selected');

            $( "#list_product_search_product_code" ).autocomplete({
                source: function( request, response ) {
                    // Fetch data
                    $.ajax({
                        url:"/search-product-code/",
                        type: 'get',
                        dataType: "json",
                        data: {
                            '_token': $('meta[name=csrf-token]').attr('content'),
                            search: request.term
                        },
                        success: function( data ) {
                            response( data );
                        }
                    });
                },
                select: function (event, ui) {
                    // Set selection
                    $('#list_product_search_product_code').val(ui.item.value);
                    return false;
                }
            });
        });
    </script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection

