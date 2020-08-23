@extends ('base_layout')
@section('extra_head')
<link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
<link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
<link rel="stylesheet" href={{ asset('css/admin/admin_list_campaign_names.css' ) }}>
<script src={{ asset('js/admin/admin_list_campaign_names.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
@endsection

@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_campaign_name", "confirm_dialog_btn_positive_id"=>"campaign_name_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"campaign_name_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
<div class="title">Danh Sách Chiến Dịch</div>


<div id="list_campaign_name_filter">
    <table>
        <tr>
             <td><input class="form-control" type="text" name="search_text" class="search_text" placeholder="Nhập tên chiến dịch" value="{{$search_campaign_name}}" id="search_campaign_name"></td>
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
            <button type="button" class="btn btn-secondary btn_add item" id="list_campaign_names_btn_add">Thêm</button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_update item" id="list_campaign_names_btn_update">Sửa
            </button>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-secondary btn_delete item" id="list_campaign_names_btn_delete">Xóa
            </button>
        </td>
    </tr>
</table>

<table class="tbl">
    <tr class="tbl_header_item">
        <td class="campaign_name">Tên Chiến Dịch</td>
        <td class="campaign_name_date">Ngày Tạo</td>
    </tr>
    @if(count($list_campaign_names) > 0)
        @foreach($list_campaign_names as $campaign_name)

            <tr class="tbl_item campaign_name_row">
                <input type="hidden" value="{{$campaign_name->id}}" class="campaign_id">
                <td class="campaign_name">{{$campaign_name->name}}</td>
                <td class="campaign_name_date">{{$campaign_name->getCreatedStr()}}</td>
            </tr>
        @endforeach
    @endif

</table>
@if(count($list_campaign_names) == 0)
    <div class="empty">Danh sách chiến dịch rỗng</div>
@endif
<table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
    <tr>
        <td>
            {{$list_campaign_names->withQueryString()->links()}}
        </td>

    </tr>
</table>
<div id="dialog_edit_campaign_name"></div>

 <button type="button" class="btn btn-secondary " id="fake_notification_button" style="display:none;">Fake Notification</button>

<script type="text/javascript">
 $(document).ready(function() {
        document.title = 'Chiến dịch';
        $('#admin_menu_item_campaigns').addClass('selected');

        $('#fake_notification_button').click(function() {
             /*$.get('/admin/fake-notification/', function(response) {
              if(response['status'] == 200){
                showMessage("fake notification successfully");
              }else{
                showMessage(response['message']);
              }
           })
           .fail(function() {
                showMessage("Lỗi mạng");
            })
            .always(function() {

            });*/
        });

 });
</script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection
