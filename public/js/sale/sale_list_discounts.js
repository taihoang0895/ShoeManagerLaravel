var is_waiting_for_request = false;
function collectFilterParam(){
    var search_discount_code = $('#search_discount_code').val().trim();

    var param = "";
    if(search_discount_code != ''){
        param = 'discount_code='+search_discount_code;
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
                    location.href = curr_url;
             }
        });
}

function handleSearchButton(){
     $('#list_discount_btn_search').click(function(){
            var curr_page = parseInt($('#curr_page').val());
             var curr_url = location.href.toString().toLowerCase();
             curr_url = removeAllParam(curr_url);
             filter_param = collectFilterParam();
             curr_url = addParam(curr_url, filter_param);
             location.href = curr_url;

    });
}

$(document).ready(function () {
        handlePagination();
        handleSearchButton();
});