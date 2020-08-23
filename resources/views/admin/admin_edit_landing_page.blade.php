<link rel="stylesheet" href={{ asset('css/admin/admin_edit_landing_page.css') }}>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_landing_page_id" value="{{$landing_page->id}}">
    <div id="add_landing_page_dialog">
        <div id="add_landing_page_dialog_content">
            <div class="title">Nhập Tên</div>
            <table width="90%">
                <tr class="landing_page_field_row">
                    <td class="lbl_name">Tên</td>
                    <td class="value"><input class="form-control" id="landing_page_name" value="{{$landing_page->name}}"></td>
                </tr>
                <tr class="landing_page_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value"><textarea class="form-control" rows="4"
                                                id="landing_page_note">{{$landing_page->note}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_landing_page_btn_ok">Lưu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_landing_page_btn_cancel">
                            Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</form>
<script src={{ asset('js/admin/admin_edit_landing_page.js' ) }}></script>
