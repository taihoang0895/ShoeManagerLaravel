@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_main.css') }}>
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_list_bank_accounts.css') }}>
    <script src={{ asset('js/marketing/marketing_list_bank_accounts.js' ) }}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_bank_account", "confirm_dialog_btn_positive_id"=>"bank_account_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"bank_account_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Chiến Dịch</div>


    <div id="list_bank_account_filter">
        <table>
            <tr>
                <td><input class="form-control" type="text" name="search_text" class="search_text"
                           placeholder="Nhập tên chiến dịch" value="{{$search_bank_account}}" id="search_bank_account">
                </td>
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
                <button type="button" class="btn btn-secondary btn_add item" id="list_bank_accounts_btn_add">Thêm
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_bank_accounts_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_bank_accounts_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="bank_account">Tên Thẻ</td>
            <td class="bank_account_date">Ngày Tạo</td>
        </tr>
        @foreach ($list_bank_accounts as $bank_account)
            <tr class="tbl_item bank_account_row">
                <input type="hidden" value="{{$bank_account->id}}" class="bank_account_id">
                <td class="bank_account">{{$bank_account->name}}</td>
                <td class="bank_account_date">{{$bank_account->createdStr()}}</td>
            </tr>
        @endforeach

    </table>
    @if (count($list_bank_accounts) == 0)
        <div class="empty">Danh sách Thẻ rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_bank_accounts->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_bank_account"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#marketing_menu_item_bank_account').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "marketing.menu")
@endsection
