@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>

    <script src={{ asset('js/sale/sale_main.js' ) }}></script>
    <script src={{ asset('js/sale/sale_list_order_fail_reasons.js') }}></script>

    <link rel="stylesheet" href={{ asset('css/sale/sale_list_order_fail_reasons.css') }}>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_order_fail_reason", "confirm_dialog_btn_positive_id"=>"fail_reason_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"fail_reason_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf

    <div class="title">Danh Sách Lý Do Lỗi</div>
    @if($edit_able)
        <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_add item" id="list_fail_reason_btn_add">Thêm
                    </button>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_update item" id="list_fail_reason_btn_update">Sửa
                    </button>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_delete item" id="list_fail_reason_btn_delete">Xóa
                    </button>
                </td>
            </tr>
        </table>
    @endif
    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="fail_reason_time">Nguyên Nhân</td>
            <td class="fail_reason_note">Ghi Chú</td>
        </tr>
        @foreach ($list_order_fail_reasons as $order_fail_reason)
            <tr class="tbl_item order_fail_reason_row" id="{{$order_fail_reason->id}}">
                <td class="fail_reason_cause">{{$order_fail_reason->cause}}</td>
                <td class="fail_reason_note">{{$order_fail_reason->note}}</td>
            </tr>
        @endforeach
    </table>
    @if (count($list_order_fail_reasons) == 0)
        <div class="empty">Danh sách rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_order_fail_reasons->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_order_fail_reason"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Lý Do Lỗi';
            $('#sale_menu_item_order_fail_reasons').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "sale.menu")
@endsection
