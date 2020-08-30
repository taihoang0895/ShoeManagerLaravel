
<link rel="stylesheet" href={{ asset('css/sale/sale_edit_schedule.css' ) }}>
<script src={{ asset('js/sale/sale_edit_schedule.js' ) }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="add_schedule_id" value="{{$schedule->id}}">
    <div id="add_schedule_dialog">
        <div id="add_schedule_dialog_content">
            <div class="title">Thêm Nhắc Nhở</div>
            <table width="90%">
                <tr class="schedule_field_row">
                    <td class="lbl_name" style="padding-top:0px;">Thời gian</td>
                    <td class="value" style="padding-top:0px;">
                        <div class="input-group date" id="schedule_time" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#schedule_time"
                                   placeholder="dd/mm/yyyy hh:mm:ss" id="schedule_time_text" value="{{$schedule->time_str}}"/>
                            <div class="input-group-append" data-target="#schedule_time" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="schedule_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value"><textarea class="form-control" rows="6" id="schedule_note">{{$schedule->note}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="add_schedule_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="add_schedule_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

<script>
      $(function () {
        $("#schedule_time").datetimepicker({
             format: 'DD/MM/YYYY HH:mm:ss',
        });
      });



</script>
