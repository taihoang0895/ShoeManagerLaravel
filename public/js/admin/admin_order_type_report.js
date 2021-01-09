function reportTimeChanged() {
    $('#order_report_time_text').val("");
    $('#order_report_time_2_text').val("");
    if ($('#filter_order_time_type').val() == '0') {
        $('#filter_order_report_time_cell').empty();
        $('#filter_order_report_time_cell').html('<div class="input-group date" id="order_report_time" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time1"\n' +
            '                                       data-target="#order_report_time"\n' +
            '                                       placeholder="dd/mm/yyyy" id="order_report_time_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time").datetimepicker({
            format: 'DD/MM/YYYY'
        });

        $('#filter_order_report_time_2_cell').empty();
        $('#filter_order_report_time_2_cell').html('<div class="input-group date" id="order_report_time_2" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time2"\n' +
            '                                       data-target="#order_report_time_2"\n' +
            '                                       placeholder="dd/mm/yyyy" id="order_report_time_2_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time_2"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time_2").datetimepicker({
            format: 'DD/MM/YYYY'
        });
    }
    if ($('#filter_order_time_type').val() == '1') {
        $('#filter_order_report_time_cell').empty();
        $('#filter_order_report_time_cell').html('<div class="input-group date" id="order_report_time" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time1"\n' +
            '                                       data-target="#order_report_time"\n' +
            '                                       placeholder="mm/yyyy" id="order_report_time_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time").datetimepicker({
            format: 'MM/YYYY'
        });

        $('#filter_order_report_time_2_cell').empty();
        $('#filter_order_report_time_2_cell').html('<div class="input-group date" id="order_report_time_2" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time2"\n' +
            '                                       data-target="#order_report_time_2"\n' +
            '                                       placeholder="mm/yyyy" id="order_report_time_2_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time_2"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time_2").datetimepicker({
            format: 'MM/YYYY'
        });
    }
    if ($('#filter_order_time_type').val() == '2') {
        $('#filter_order_report_time_cell').empty();
        $('#filter_order_report_time_cell').html('<div class="input-group date" id="order_report_time" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time1"\n' +
            '                                       data-target="#order_report_time"\n' +
            '                                       placeholder="yyyy" id="order_report_time_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time").datetimepicker({
            format: 'YYYY'
        });

        $('#filter_order_report_time_2_cell').empty();
        $('#filter_order_report_time_2_cell').html('<div class="input-group date" id="order_report_time_2" data-target-input="nearest">\n' +
            '\n' +
            '                                <input type="text" class="form-control datetimepicker-input" name="time2"\n' +
            '                                       data-target="#order_report_time_2"\n' +
            '                                       placeholder="yyyy" id="order_report_time_2_text"/>\n' +
            '                                <div class="input-group-append" data-target="#order_report_time_2"\n' +
            '                                     data-toggle="datetimepicker">\n' +
            '                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
            '                                </div>\n' +
            '                            </div>')
        $("#order_report_time_2").datetimepicker({
            format: 'YYYY'
        });
    }
}

$(document).ready(function () {
    $('#filter_order_report_by_time_type a').click(function () {
        $('#filter_order_report_by_time_type_text').text($(this).text());
        $('#filter_order_time_type').val($(this).find('input').val());
        reportTimeChanged();
    });


    $('#filter_by_member a').click(function () {
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });

});
