@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/sale/sale_list_discounts.css' ) }}>
    <script src={{ asset('js/sale/sale_list_discounts.js'  ) }}></script>
@endsection
@section('content')
    <div class="title">Danh Sách Khuyến Mại</div>


    <div id="list_discounts_filter">
        <table>
            <tr>
                <td><input class="form-control" type="text" name="search_text" class="search_text"
                           placeholder="Nhập mã khuyến mại" value="{{$search_discount_code}}" id="search_discount_code">
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_search_text" id="list_discount_btn_search">Tìm Kiếm
                    </button>
                </td>

            </tr>
        </table>
    </div>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="discount_code">Mã Chương Trình</td>
            <td class="name">Tên Chương Trình</td>
            <td class="discount_value">Chiết khấu</td>
            <td class="start_time">Từ Ngày</td>
            <td class="end_time">Đến Ngày</td>
            <td class="note">Ghi Chú</td>

        </tr>
        @foreach ($list_discounts as $discount_row)
            <tr class="tbl_item discount_row">
                <input type="hidden" value="{{$discount_row->id}}" class="discount_id">
                <td class="discount_code">{{$discount_row->code}}</td>
                <td class="name">{{$discount_row->name}}</td>
                <td class="discount_value">{{$discount_row->discount_value}}</td>
                <td class="start_time">{{$discount_row->getStartTimeStr()}}</td>
                <td class="end_time">{{$discount_row->getEndTimeStr()}}</td>
                <td class="note">{{$discount_row->note}}</td>
            </tr>
        @endforeach

    </table>
    @if (count($list_discounts) == 0)
        <div class="empty">Danh sách khuyến mại rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_discounts->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_discount"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Khuyến Mại';
            $('#sale_menu_item_discounts').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "sale.menu")
@endsection
