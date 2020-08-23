
function handleFilterButton(){
    $('#btn_filter').click(function(){
        var start_time = $('#filter_start_time_text').val().trim();
        var end_time = $('#filter_end_time_text').val().trim();

        if(validateTimeRangeFilter(start_time, end_time)){
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);

            if (start_time != "" && end_time != ""){
                curr_url += "?start_time="+start_time+"&end_time="+end_time;
            }
            curr_url = normalize(curr_url);
             location.href = curr_url;
        }

    });
}

$(document).ready(function () {
    handleFilterButton();
});