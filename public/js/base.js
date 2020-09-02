var is_waiting_for_request = false;
$(document).ready(function () {

    $('.btn_search').mousedown(function () {
        $(this).css('opacity', '0.5');
    });
    $('.btn_pagination').mousedown(function (e) {
        if ($(e.target).hasClass('enable')) {
            $(e.target).css('background-color', 'rgba(0, 0, 0, 0.1)');
        }
    });

    $(document).mouseup(function () {
        $('.btn_search').css('opacity', '1.0');
        $('.btn_pagination').each(function () {
            $(this).css('background-color', ' #ffffff');

        });
    });

    $('#menu_btn_notification').click(function () {
        if ($('#dialog_notification').is(':empty')) {

            if (is_waiting_for_request) {
                return;
            }
            data = {}
            $.get('/common/get-notifications/', data, function (response) {
                if (response['status'] == 200) {
                    var count_message = response['count_message_unread'];
                    update_menu_notification(count_message);
                    $('#dialog_notification').empty();
                    $('#dialog_notification').html(response['content']);
                } else {
                    showMessage(response['message']);
                }
            })
                .fail(function () {
                    showMessage("Lỗi mạng");
                })
                .always(function () {
                    is_waiting_for_request = false;

                });
        } else {
            $('#dialog_notification').empty();
        }
    });


});

function update_menu_notification(count_message) {
    var notification_img = $('#menu_btn_notification').find('.icon').find('img');
    var total_message = $('#menu_btn_notification').find('.total_message');

    if (count_message > 0) {
        if (!notification_img.hasClass('active')) {
            notification_img.addClass('active');
        }
        if (total_message.hasClass('no_message')) {
            total_message.removeClass('no_message');

        }
        var count_message_str = "" + count_message;
        if (count_message > 10) {
            count_message_str = "10+";
        }
        total_message.find('td').text(count_message_str);

    } else {
        if (notification_img.hasClass('active')) {
            notification_img.removeClass('active');
        }

        if (!total_message.hasClass('no_message')) {
            total_message.addClass('no_message');
        }
    }
}

function validateTimeRangeFilter(start_time, end_time) {
    if (start_time == '' && end_time == '') {
        return true;
    }
    if (start_time != '' && end_time == '') {
        showMessage("Bạn phải nhập thời gian kết thúc");
        return false;
    }
    if (start_time == '' && end_time != '') {
        showMessage("Bạn phải nhập thời gian bắt đầu");
        return false;
    }
    return true;
}

function removeAllParam(url) {
    var pos = url.indexOf("?")
    if (pos > 0) {
        return url.substring(0, pos);
    } else {
        return url;
    }

}

function normalize(url) {
    var char_index = url.indexOf("?")
    if (char_index == url.length - 1) {
        url = url.substring(0, char_index)
    }
    return url;
}

function addParam(url, param) {
    var pos = url.indexOf("?")
    if (pos > 0) {
        if (pos == (url.length - 1)) {
            return url + param;
        } else {
            return url + '&' + param;
        }
    } else {
        return url + "?" + param;
    }
}

function load_page(page) {
    var curr_url = location.href.toString().toLowerCase();
    var res = curr_url.match(/page-\d+/i);
    var replace_page_text = "";
    if (page > 0) {
        replace_page_text = "page-" + page.toString();
    }
    while (curr_url.match(/\/$/i)) {
        curr_url = curr_url.replace(/\/$/i, '');
    }

    if (res) {
        location.href = curr_url.replace(/page-\d+/i, replace_page_text);
    } else {
        location.href = curr_url + "/" + replace_page_text;
    }

}

function showMessage(message) {
    $('#toast_message_content').text(message);
    $('#toast_message_dialog').fadeIn(500, function () {
        $('#toast_message_dialog').delay(2000).fadeOut(500);
    });

}

function isInteger(value_str) {
    var value1 = parseInt(value_str);
    if (Number.isNaN(value1)) {
        return false;
    }
    var value2 = parseFloat(value_str);
    if (value1 != value2) {
        return false;
    }
    return true;

}

function isFloat(value_str) {
    var value1 = parseFloat(value_str);
    if (Number.isNaN(value1)) {
        return false;
    }
    return true;

}

function getCookie(name) {
    let cookieValue = null;
    if (document.cookie && document.cookie !== '') {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            // Does this cookie string begin with the name we want?
            if (cookie.substring(0, name.length + 1) === (name + '=')) {
                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                break;
            }
        }
    }
    return cookieValue;
}

function csrfSafeMethod(method) {
    // these HTTP methods do not require CSRF protection
    return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
}

function setupCSRF() {
    const csrftoken = getCookie('csrftoken');
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            if (!csrfSafeMethod(settings.type) && !this.crossDomain) {
                xhr.setRequestHeader("X-CSRFToken", csrftoken);
            }
        }
    });
}

function handleLogout() {
    $('#menu_btn_logout').click(function () {
        $('#form_logout').submit();
    });
}


function synchronizeNotification() {
    $.get('/common/check-notification/', function (response) {
        if (response['status'] == 200) {
            var has_notification = response['has_notification'];
            if (has_notification == 1) {
                var count_message = response['unread_message'];
                update_menu_notification(count_message);

            }
        }
    });

}

$(document).ready(function () {
    handleLogout();
    setInterval(synchronizeNotification, 1000 * 10);
});


