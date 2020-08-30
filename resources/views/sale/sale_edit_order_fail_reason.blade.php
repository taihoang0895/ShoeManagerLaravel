<script src={{ asset('js/sale/sale_edit_order_fail_reason.js') }}></script>
<link rel="stylesheet" href={{ asset('css/sale/sale_edit_order_fail_reason.css') }}>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_order_fail_reason_id" value="{{$order_fail_reason->id}}">
    <div id="add_order_fail_reason_dialog">
        <div id="add_order_fail_reason_dialog_content">
            <div class="title">Nhập Lý Do Lỗi</div>
            <table width="90%">
                 <tr class="order_fail_reason_field_row">
                    <td class="lbl_name">Nguyên Nhân</td>
                    <td class="value"><textarea class="form-control" rows="4" id="order_fail_reason_cause">{{$order_fail_reason->cause}}</textarea></td>
                </tr>
                <tr class="order_fail_reason_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value"><textarea class="form-control" rows="4" id="order_fail_reason_note">{{$order_fail_reason->note}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_order_fail_reason_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_order_fail_reason_btn_cancel">Hủy</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
