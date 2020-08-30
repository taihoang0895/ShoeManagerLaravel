@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_list_products.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <script src={{ asset('js/sale/sale_list_products.js') }}></script>
    <link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
    <script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>

@endsection
@section('content')
    <div class="title">Danh Sách Sản Phẩm</div>


    <div id="list_products_filter">
        <table>
            <tr>
                <td><input class="form-control" type="text" name="search_text" class="search_text"
                           placeholder="Nhập mã sản phẩm" value="{{$search_product_code}}"
                           id="search_product_code"></td>
                <td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_search_text" id="list_product_btn_search">Tìm Kiếm
                    </button>
                </td>

            </tr>
        </table>
    </div>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="product_code">Mã SP</td>
            <td class="name">Tên</td>
            <td class="price">Giá Bán</td>
            <td class="show_detail_header"></td>

        </tr>

        @foreach ($products as $product_row)
            <tr class="tbl_item product_row" id="product_{{$product_row->code}}">
                <td class="product_code">{{$product_row->code}}</td>
                <td class="name">{{$product_row->name}}</td>
                <td class="price">{{$product_row->getPriceStr()}}&nbsp;&#8363;</td>
                <td class="show_detail"><input type="hidden" value="{{$product_row->code}}">xem chi tiết</td>
            </tr>
        @endforeach
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


            $( "#search_product_code" ).autocomplete({
                source: function( request, response ) {
                    // Fetch data
                    $.ajax({
                        url:"/sale/search-product-code/",
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
                    $('#search_product_code').val(ui.item.value);
                    return false;
                }
            });

            $('#sale_menu_item_products').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "sale.menu")
@endsection

