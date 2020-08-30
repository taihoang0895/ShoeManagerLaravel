@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/sale/sale_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>

    <script src={{ asset('js/marketing/marketing_main.js') }}></script>
    <script src={{ asset('js/marketing/list_marketing_sources.js' ) }}></script>
    <link rel="stylesheet" href={{ asset('css/marketing/list_marketing_sources.css') }}>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_marketing_source", "confirm_dialog_btn_positive_id"=>"marketing_source_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"marketing_source_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Nguồn Marketing</div>
    @if($editable)
        <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_add item" id="list_marketing_source_btn_add">
                        Thêm
                    </button>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_update item"
                            id="list_marketing_source_btn_update">Sửa
                    </button>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_delete item"
                            id="list_marketing_source_btn_delete">Xóa
                    </button>
                </td>
            </tr>
        </table>
    @endif
    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="marketing_source_time">Tên Nguồn</td>
            <td class="marketing_source_note">Ghi Chú</td>
        </tr>
        @foreach ($list_marketing_sources as $marketing_source)
        <tr class="tbl_item marketing_source_row">
            <input type="hidden" class="marketing_source_id" value="{{$marketing_source->id}}">
            <td class="marketing_source_name">{{$marketing_source->name}}</td>
            <td class="marketing_source_note">{{$marketing_source->note}}</td>
        </tr>
        @endforeach
    </table>
    @if (count($list_marketing_sources) == 0)
    <div class="empty">Danh sách rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_marketing_sources->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_marketing_source"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Nguồn Marketing';
            $('#marketing_menu_item_marketing_source').addClass('selected');
        });
    </script>
@endsection
@section('menu')
    @include( "marketing.menu")
@endsection
