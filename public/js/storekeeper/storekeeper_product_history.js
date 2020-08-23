
function collectFilterParam(){
    var start_time = $('#filter_start_time_text').val().trim();
    var end_time = $('#filter_end_time_text').val().trim();

    var param = "";
    if(start_time != '' && end_time != ''){
        param = 'start_time='+start_time +"&"+"end_time=" +end_time;
    }

    if(param.startsWith("&")){
        param = param.substring(1);
    }

    return param;
}

function handlePagination(){
     $('#previous_page').click(function(){
           if($(this).hasClass('enable')){
                 var curr_page = $('#curr_page').val();
                 var prev_page = parseInt(curr_page) - 1;
                 var curr_url = location.href.toString().toLowerCase();
                 curr_url = removeAllParam(curr_url);
                 if (prev_page != 0){
                    curr_url = addParam(curr_url, "page="+prev_page);
                 }
                  filter_param = collectFilterParam();
                  if (filter_param != ''){
                        curr_url = addParam(curr_url, filter_param);
                  }

                 location.href = curr_url;
           }
        });
     $('#next_page').click(function(){

             if($(this).hasClass('enable')){
                    var curr_page = $('#curr_page').val();
                    var next_page = parseInt(curr_page) + 1;
                    var curr_url = location.href.toString().toLowerCase();
                    curr_url = removeAllParam(curr_url);
                    curr_url = addParam(curr_url, "page="+next_page);

                    filter_param = collectFilterParam();
                    if (filter_param != ''){
                        curr_url = addParam(curr_url, filter_param);
                    }
                    curr_url = normalize(curr_url);
                    location.href = curr_url;
             }
        });
}

function handleFilterButton(){
    $('#btn_filter').click(function(){
        var start_time = $('#filter_start_time_text').val().trim();
        var end_time = $('#filter_end_time_text').val().trim();

        if(validateTimeRangeFilter(start_time, end_time)){
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);

             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             curr_url = normalize(curr_url);
             location.href = curr_url;
        }

    });
}

$(document).ready(function () {
    handleFilterButton();
    handlePagination();
});