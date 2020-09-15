<link rel="stylesheet" href={{ asset('css/admin/admin_edit_discount.css') }}>

<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
      integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
<script src={{ asset('js/extra/tempusdominus-moment.js' ) }}></script>
<script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
<script src={{ asset('js/admin/admin_edit_discount.js' ) }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <input type="hidden" id="edit_discount_id" value="{{$discount->id}}">
    <div id="edit_discount_dialog">
        <div id="edit_discount_dialog_content">
            <div class="title">Nhập Thông Tin Khuyến Mại</div>
            <table width="90%">
                <tr class="discount_field_row">
                    <td class="lbl_name" style="padding-top:0px;">Tên Chương Trình</td>
                    <td class="value" style="padding-top:0px;"><input class="form-control" type="text"
                                                                      placeholder="Nhập tên chương trình"
                                                                      id="discount_name" value="{{$discount->name}}">
                    </td>
                </tr>
                <tr class="discount_field_row">
                    <td class="lbl_name">Từ Ngày</td>
                    <td class="value">
                        <div class="input-group date" id="datetimepicker1" data-target-input="nearest"
                             style="width:200px;">
                            <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1"
                                   placeholder="dd/mm/yyyy" id="discount_start_time"
                                   value="{{$discount->start_time_str}}"/>
                            <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="discount_field_row">
                    <td class="lbl_name">Đến Ngày</td>
                    <td class="value">
                        <div class="input-group date" id="datetimepicker2" data-target-input="nearest"
                             style="width:200px;">
                            <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"
                                   placeholder="dd/mm/yyyy" id="discount_end_time" value="{{$discount->end_time_str}}"/>
                            <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="discount_field_row">
                    <td class="lbl_name">Chiết Khấu</td>
                    <td class="value">
                        @if($discount->id == -1)
                        <input class="form-control" type="number" placeholder="Nhập giá bán"
                                                  id="discount_value" value="{{$discount->discount_value}}">
                        @else
                            <input class="form-control" type="number" placeholder="Nhập giá bán"
                                   id="discount_value" value="{{$discount->discount_value}}" disabled>
                        @endif
                    </td>
                </tr>
                <tr class="discount_field_row">
                    <td class="lbl_name">Ghi Chú</td>
                    <td class="value"><textarea class="form-control" rows="3"
                                                id="discount_note">{{$discount->note}}</textarea></td>
                </tr>
            </table>

            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_discount_btn_ok">Lưu</button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="edit_discount_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
<script>
    $(function () {
        $("#datetimepicker1").datetimepicker({
            format: 'DD/MM/YYYY',
        });
        $("#datetimepicker2").datetimepicker({
            format: 'DD/MM/YYYY',
        });
    });


</script>
