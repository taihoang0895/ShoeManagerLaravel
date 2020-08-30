<link rel="stylesheet" href={{ asset('css/marketing/marketing_edit_bank_account.css' ) }}>
<script src={{ asset('js/marketing/marketing_edit_bank_account.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_bank_account_id" value="{{$bank_account->id}}">
    <div id="add_bank_account_dialog">
        <div id="add_bank_account_dialog_content">
            <div class="title">Nhập Tên Thẻ Tín Dụng</div>
            <table width="90%">
                <tr class="bank_account_field_row">
                    <td class="lbl_name">Tên Thẻ</td>
                    <td class="value"><textarea class="form-control" rows="4"
                                                id="bank_account">{{$bank_account->name}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_bank_account_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_bank_account_btn_cancel">
                            Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
