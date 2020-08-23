@extends ('base_layout')
@section('extra_head')

    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/storekeeper/storekeeper_failed_products.css' ) }}>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>

    <script src={{ asset('js/storekeeper/storekeeper_failed_products.js' ) }}></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
    <script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_failed_product", "confirm_dialog_btn_positive_id"=>"failed_product_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"failed_product_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
<div class="title">Danh Sách Hàng Lỗi</div>

<table id="list_failed_product_filter">
    <tr>
        <td class="filter_time">
            <div class="input-group date" id="failed_product_filter_time" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" data-target="#failed_product_filter_time"
                       placeholder="dd/mm/yyyy" id="failed_product_filter_time_text"
                       value="{{$filter_time}}"/>
                <div class="input-group-append" data-target="#failed_product_filter_time" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>

        </td>
         <td class="value">
             @include("autocomplete", ["autocomplete_id"=>"filter_product_code", "autocomplete_placeholder"=>"Nhập mã sản phẩm",
             "autocomplete_value"=>$filter_product_code, "autocomplete_data"=>$list_product_codes])
         </td>
        <td class="filter_by_failed_product_state">
              <div class="dropdown" id="filter_product_size">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="filter_product_size_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$filter_product_size}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item">____</a>
                                @foreach($list_product_sizes as $product_size)
                                    <a class="dropdown-item">{{$product_size}}</a>
                                @endforeach
                            </div>
                        </div>

        </td>

         <td class="filter_by_failed_product_state">
              <div class="dropdown" id="filter_product_color">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="filter_product_color_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$filter_product_color}}

                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                 <a class="dropdown-item">____</a>
                                @foreach($list_product_colors as $color)
                                    <a class="dropdown-item">{{$color}}</a>
                                @endforeach
                            </div>
                        </div>

        </td>

        <td class="btn_filter">
            <button type="button" class="btn btn-warning btn_filter" id="failed_product_btn_filter">Lọc</button>
        </td>
    </tr>
</table>

<table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
    <tr>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_add item" id="failed_products_btn_add">Thêm</button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_update item" id="failed_products_btn_update">Sửa
            </button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_delete item" id="failed_products_btn_delete">Xóa
            </button>
        </td>
    </tr>
</table>

<table class="tbl_failed_products">
    <tr class="tbl_header_item">
        <td class="date">Ngày</td>
        <td class="code">MSP</td>
        <td class="size">Size</td>
        <td class="color">Màu</td>
        <td class="quantity">Số Lượng</td>
         <td class="note">Ghi Chú</td>
    </tr>
    @foreach($list_failed_products as $failed_product)
        <tr class="tbl_item failed_product_row">
            <input type="hidden" class="failed_product_id" value="{{$failed_product->id}}">
             <td class="date">{{$failed_product->created_str}}</td>
            <td class="code">{{$failed_product->product_code}}</td>
            <td class="size">{{$failed_product->product_size}}</td>
            <td class="color">{{$failed_product->product_color}}</td>
            <td class="quantity">{{$failed_product->quantity}}</td>
              <td class="note">{{$failed_product->note}}</td>
        </tr>
    @endforeach
</table>

@if(count($list_failed_products) == 0)
    <div class="empty">Danh sách hàng lỗi rỗng</div>
@endif
<table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
    <tr>
        <td>
            {{$list_failed_products->withQueryString()->links()}}
        </td>

    </tr>
</table>
<div id="dialog_edit_failed_product"></div>
<script type="text/javascript">
 $(document).ready(function() {
        document.title = 'Hàng Lỗi';
        $('#storekeeper_menu_item_failed_products').addClass('selected');

        $("#failed_product_filter_time").datetimepicker({
             format: 'DD/MM/YYYY',
        });
 });

</script>
@endsection

@section('menu')
    @include( "storekeeper.menu")
@endsection

