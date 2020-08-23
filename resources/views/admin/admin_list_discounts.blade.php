@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_list_discounts.css') }}>
    <script src={{ asset('js/admin/admin_list_discounts.js')}}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection

@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_discount", "confirm_dialog_btn_positive_id"=>"discount_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"discount_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
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

    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_discounts_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_discounts_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_discounts_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="discount_code">Mã Chương Trình</td>
            <td class="name">Tên Chương Trình</td>
            <td class="start_time">Từ Ngày</td>
            <td class="end_time">Đến Ngày</td>
            <td class="note">Ghi Chú</td>

        </tr>
        @if(count($list_discounts) > 0)
            @foreach($list_discounts as $discount_row)
                <tr class="tbl_item discount_row">
                    <input type="hidden" value="{{$discount_row->id}}" class="discount_id">
                    <td class="discount_code">{{$discount_row->code}}</td>
                    <td class="name">{{$discount_row->name}}</td>
                    <td class="start_time">{{$discount_row->getStartTimeStr()}}</td>
                    <td class="end_time">{{$discount_row->getEndTimeStr()}}</td>
                    <td class="note">{{$discount_row->note}}</td>
                </tr>
            @endforeach
        @endif

    </table>
    @if(count($list_discounts) == 0)
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Khuyến Mại';
            $('#admin_menu_item_discounts').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection
