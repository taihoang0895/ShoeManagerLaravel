function reportTimeChanged() {
    $('#marketing_revenue_report_time_text').val("");
    if ($('#report_time_type').val() == '0') {
        $('#filter_revenue_report_time_cell').html('<div class="input-group date" id="marketing_revenue_report_time" data-target-input="nearest"><input type="text" class="form-control datetimepicker-input" data-target="#marketing_revenue_report_time" placeholder="dd/mm/yyyy" id="marketing_revenue_report_time_text"/> <div class="input-group-append" data-target="#marketing_revenue_report_time" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-calendar"></i></div></div></div>');
        $("#marketing_revenue_report_time").datetimepicker({
            format: 'DD/MM/YYYY'
        });
    }
    if ($('#report_time_type').val() == '1') {
        $('#filter_revenue_report_time_cell').empty();
        $('#filter_revenue_report_time_cell').html('<div class="input-group date" id="marketing_revenue_report_time" data-target-input="nearest"><input type="text" class="form-control datetimepicker-input" data-target="#marketing_revenue_report_time" placeholder="mm/yyyy" id="marketing_revenue_report_time_text"/> <div class="input-group-append" data-target="#marketing_revenue_report_time" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-calendar"></i></div></div></div>');
        $("#marketing_revenue_report_time").datetimepicker({
            format: 'MM/YYYY'
        });
    }
    if ($('#report_time_type').val() == '2') {
        $('#filter_revenue_report_time_cell').empty();
        $('#filter_revenue_report_time_cell').html('<div class="input-group date" id="marketing_revenue_report_time" data-target-input="nearest"><input type="text" class="form-control datetimepicker-input" data-target="#marketing_revenue_report_time" placeholder="yyyy" id="marketing_revenue_report_time_text"/> <div class="input-group-append" data-target="#marketing_revenue_report_time" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-calendar"></i></div></div></div>');
        $("#marketing_revenue_report_time").datetimepicker({
            format: 'YYYY'
        });
    }
}

function updateReportTimeText() {
    if ($('#report_time_type').val() == '0') {
        $('#marketing_revenue_report_time_text').attr("placeholder", "dd/mm/yyyy");
    }
    if ($('#report_time_type').val() == '1') {
        $('#marketing_revenue_report_time_text').attr("placeholder", "mm/yyyy");
    }
    if ($('#report_time_type').val() == '2') {
        $('#marketing_revenue_report_time_text').attr("placeholder", "yyyy");
    }
}
function collectFilterParam(){
    var report_time = $('#marketing_revenue_report_time_text').val().trim();
    var report_time_type = $('#report_time_type').val().trim();
    var filter_member_id = $('#filter_member_id_selected').val().trim();

    var param = "";
    if (report_time != '') {
        param += "&report_time="+ report_time + "&report_time_type=" + report_time_type;
    }

    if (filter_member_id != "-1"){
        param += "&filter_member_id="+filter_member_id
    }

    if(param.startsWith("&")){
        param = param.substring(1);
    }

    return param;
}
function handleFilterButton() {
    $('#revenue_report_btn_filter').click(function () {

        filter_param = collectFilterParam();
        var curr_url = location.href.toString().toLowerCase();
        curr_url = removeAllParam(curr_url);
        curr_url = addParam(curr_url, filter_param);
        location.href = normalize(curr_url);
    });
}

function init() {
    $('#filter_by_member a').click(function () {
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });
}

$(document).ready(function () {
    document.title = 'Báo Cáo';
    $('#marketing_menu_item_report').addClass('selected');
    $('#filter_revenue_report_by_time_type a').click(function () {
        $('#filter_revenue_report_by_time_type_text').text($(this).text());
        $('#report_time_type').val($(this).find('input').val());
        reportTimeChanged();
    });
    init();
    updateReportTimeText();
    handleFilterButton();
});
