

$(document).ready(function () {
    $('#filter_by_member a').click(function () {
        $('#filter_by_member_text').text($(this).text());
        $('#filter_member_id_selected').val($(this).find('input').val());
    });
});
