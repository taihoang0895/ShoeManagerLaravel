<link rel="stylesheet" href={{ asset('css/admin/admin_edit_campaign_name.css') }}>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_campaign_name_id" value="{{$campaign_name->id}}">
    <div id="add_campaign_name_dialog">
        <div id="add_campaign_name_dialog_content">
            <div class="title">Nhập Tên Chiến Dịch</div>
            <table width="90%">
                <tr class="campaign_name_field_row">
                    <td class="lbl_name">Tên Chiến Dịch</td>
                    <td class="value"><textarea class="form-control" rows="4"
                                                id="campaign_name">{{$campaign_name->name}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_campaign_name_btn_ok">Lưu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_campaign_name_btn_cancel">
                            Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</form>
<script src={{ asset('js/admin/admin_edit_campaign_name.js' ) }}></script>
