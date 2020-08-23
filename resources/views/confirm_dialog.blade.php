<link rel="stylesheet" href={{asset('css/confirm_dialog.css') }}>
<script src={{asset('js/confirm_dialog.js') }}></script>
<div id="{{$confirm_dialog_id}}" class="confirm_dialog" style="display:none;">
    <div id="confirm_dialog_content">
        <div id="confirm_dialog_message">{{$confirm_dialog_message}}</div>
        <table width="100%">
            <tr>
                <td width="50%">
                    <div id="{{$confirm_dialog_btn_positive_id}}" class="confirm_dialog_btn_positive">Có</div>
                </td>
                <td width="50%">
                    <div id="{{$confirm_dialog_btn_negative_id}}" class="confirm_dialog_btn_negative">Không</div>
                </td>
            </tr>
        </table>


    </div>

</div>
