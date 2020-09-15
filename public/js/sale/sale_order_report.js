function collectFilterParam(){
    var filter_member_id = $('#filter_member_id_selected').val().trim();
    var param = "";
    if (filter_member_id != "-1") {
        param += "&filter_member_id=" + filter_member_id
    }
    if (param.startsWith("&")) {
        param = param.substring(1);
    }
    return param;
}
function handleSearchButton() {
    $('#btn_search').click(function () {
        var curr_url = location.href.toString().toLowerCase();
        curr_url = removeAllParam(curr_url);
        filter_param = collectFilterParam();
        if (filter_param != ''){
            curr_url = addParam(curr_url, filter_param);
        }

        location.href = curr_url;
    });
}
function init(){
    $('#filter_by_member a').click(function () {
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });

}
$(document).ready(function () {
    init();
    handleSearchButton();
});
