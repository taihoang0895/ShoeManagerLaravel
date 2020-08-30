<link rel="stylesheet" href={{ asset('css/marketing/marketing_edit_marketing_source.css' ) }}>
<script src={{ asset('js/marketing/marketing_edit_marketing_source.js' ) }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_marketing_source_id" value="{{$marketing_source->id}}">
    <div id="add_marketing_source_dialog">
        <div id="add_marketing_source_dialog_content">
            <div class="title">Nhập Nguồn Marketing</div>
            <table width="90%">
                <tr class="marketing_source_field_row">
                    <td class="lbl_name">Tên Nguồn</td>
                    <td class="value">
                        <input class="form-control" type="text" placeholder="Nhập tên nguồn"
                               id="marketing_source_name" value="{{$marketing_source->name}}">
                    </td>
                </tr>
                <tr class="marketing_source_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value"><textarea class="form-control" rows="4"
                                                id="marketing_source_note">{{$marketing_source->note}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_marketing_source_btn_ok">Lưu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_marketing_source_btn_cancel">
                            Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
