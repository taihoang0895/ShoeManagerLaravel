<link rel="stylesheet" href={{ asset('css/admin/admin_edit_user.css' ) }}>
<script src={{ asset('js/admin/admin_edit_user.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_user_id" value="{{$user->id}}">
    <div id="edit_user_dialog">
        <div id="edit_user_dialog_content">
            <div class="title">Nhập Thông Tin Nhân Viên</div>
            <table width="90%">
                <tr class="user_field_row">
                    <td class="lbl_name" style="padding-top:0px;">Tài Khoản</td>
                    <td class="value" style="padding-top:0px;">
                        @if($user->id == -1 )
                            <input class="form-control" type="text"
                                   placeholder="Nhập tài khoản" id="user_name" value="{{$user->username}}">
                        @else
                            <input class="form-control" type="text"
                                   placeholder="Nhập tài khoản" id="user_name" value="{{$user->username}}" disabled>
                        @endif
                    </td>
                </tr>
                <tr class="user_field_row">
                    <td class="lbl_name">Mật khẩu</td>
                    <td class="value"><input class="form-control" type="password"
                                             placeholder="Nhập mật khẩu" id="password" value="{{$user->password}}">
                    </td>
                </tr>
                <tr class="user_field_row">
                    <td class="lbl_name">Danh Tính</td>
                    <td class="value"><input class="form-control" type="text"
                                             placeholder="Nhập tài khoản" id="alias_name" value="{{$user->alias_name}}">
                    </td>
                </tr>
                <tr class="user_field_row">
                    <td class="lbl_name">Phòng Ban</td>
                    <td class="value">
                        <div class="dropdown" id="dropdown_user_department">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdown_user_department_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$user->department_name}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item">Sale</a>
                                <a class="dropdown-item">Marketing</a>
                                <a class="dropdown-item">Kho</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="user_field_row">
                    <td class="lbl_name">Chức Vụ</td>
                    <td class="value">
                        <div class="dropdown" id="dropdown_user_role">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdown_user_role_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$user->role_name}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item">Member</a>
                                <a class="dropdown-item">Leader</a>
                                <a class="dropdown-item">Sale Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>

            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_user_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_user_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
