@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_list_users.css' ) }}>
    <script src={{ asset('js/admin/admin_list_users.js') }}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_user", "confirm_dialog_btn_positive_id"=>"user_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"user_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf

    <div class="title">Danh Sách Users</div>


    <div id="list_users_filter">
        <table>
            <tr>
                <td><input class="form-control" type="text" name="search_text" class="search_text" placeholder="Nhập tài khoản" value="{{$search_username}}" id="search_username"></td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-warning btn_search_text" id="list_users_btn_search">Tìm Kiếm
                    </button>
                </td>

            </tr>
        </table>
    </div>

    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_users_btn_add">Thêm</button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_users_btn_update">Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_users_btn_delete">Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="username">Tài Khoản</td>
            <td class="alias_name">Danh Tính</td>
            <td class="department">Phòng Ban</td>
            <td class="role">Chức vụ</td>

        </tr>
        @if(count($list_users) > 0)
            @foreach($list_users as $user)
                <tr class="tbl_item user_row">
                    <input type="hidden" value="{{$user->id}}" class="user_id">
                    <td class="username">{{$user->username}}</td>
                    <td class="alias_name">{{$user->alias_name}}</td>
                    <td class="department">{{$user->department_name}}</td>
                    <td class="role">{{$user->role_name}}</td>
                </tr>
            @endforeach
        @endif

    </table>
    @if(count($list_users) == 0)
        <div class="empty">Danh sách rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_users->withQueryString()->links()}}
            </td>

        </tr>
    </table>
    <div id="dialog_edit_user"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            document.title = 'Khuyến Mại';
            $('#admin_menu_item_users').addClass('selected');
        });
    </script>
@endsection


@section('menu')
    @include( "admin.menu")
@endsection
