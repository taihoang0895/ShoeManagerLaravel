@extends ('base_layout')
@section('extra_head')
<link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
<link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
<link rel="stylesheet" href={{ asset('css/admin/admin_list_landing_pages.css' ) }}>
<script src={{ asset('js/admin/admin_list_landing_pages.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
@endsection

@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_landing_page", "confirm_dialog_btn_positive_id"=>"landing_page_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"landing_page_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
<div class="title">Danh Sách Landing Pages</div>


<div id="list_landing_page_filter">
    <table>
        <tr>
             <td><input class="form-control" type="text" name="search_text" class="search_text" placeholder="Nhập tên" value="{{$search_landing_page_name}}" id="search_landing_page"></td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-warning btn_search_text" id="list_campaign_btn_search">Tìm Kiếm
                </button>
            </td>

        </tr>
    </table>
</div>

<table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
    <tr>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_add item" id="list_landing_pages_btn_add">Thêm</button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_update item" id="list_landing_pages_btn_update">Sửa
            </button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_delete item" id="list_landing_pages_btn_delete">Xóa
            </button>
        </td>
    </tr>
</table>

<table class="tbl">
    <tr class="tbl_header_item">
        <td class="landing_page">Tên</td>
        <td class="landing_page_date">Ngày Tạo</td>
        <td class="landing_page_note">Ghi Chú</td>
    </tr>
    @if(count($list_landing_pages) > 0)
        @foreach($list_landing_pages as $landing_page)

            <tr class="tbl_item landing_page_row">
                <input type="hidden" value="{{$landing_page->id}}" class="landing_page_id">
                <td class="landing_page_name">{{$landing_page->name}}</td>
                <td class="landing_page_date">{{$landing_page->getCreatedStr()}}</td>
                <td class="landing_page_note">{{$landing_page->note}}</td>
            </tr>
        @endforeach
    @endif

</table>
@if(count($list_landing_pages) == 0)
    <div class="empty">Danh sách landing pages rỗng</div>
@endif
<table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
    <tr>
        <td>
            {{$list_landing_pages->withQueryString()->links()}}
        </td>

    </tr>
</table>
<div id="dialog_edit_landing_page"></div>

 <button type="button" class="btn btn-secondary " id="fake_notification_button" style="display:none;">Fake Notification</button>

<script type="text/javascript">
 $(document).ready(function() {
        document.title = 'Chiến dịch';
        $('#admin_menu_item_landing_page').addClass('selected');

 });
</script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection
