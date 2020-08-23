<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href={{ asset('css/base.css')}}>
    <link rel="stylesheet" href={{  asset('css/header.css')}}>
    <script src="{{ asset('js/extra/jquery.js')}}"></script>
    <script src="{{ asset('js/base.js')}}"></script>
    @yield('extra_head')

</head>
<body style="background-color:#E5E5E5;">
<table style="position:absolute; text-align : center;" width="100%" id="toast_message_dialog"
       class="toast_message_dialog">
    <tr>
        <td>
            <div id="toast_message" class="toast_message">
                <div id="toast_message_content"></div>
            </div>
        </td>
    </tr>
</table>

<div id="header">
    @yield('header')
    <form method="get" action="/logout/" id="form_logout">
        <div id="menu_btn_logout">Đăng Xuất</div>
    </form>
    <div id="menu_btn_notification">
        <div class="icon">
            @if (Auth::user()->notification_unread_count > 0)
                <img class="active" alt="Thông báo"/>
            @else
                <img alt="Thông báo"/>
            @endif

        </div>
        @if  (Auth::user()->notification_unread_count > 0)
            <table class="total_message">
        @else
                    <table class="total_message no_message">
        @endif
                        <tr>
                            <td style="text-align:center;">{{Auth::user()->notification_unread_count}}</td>
                        </tr>
                    </table>
    </div>

</div>
<div id="menu">
    @yield('menu')

</div>
<div id="content">
    @yield('content')
</div>
<div id="dialog_notification"></div>
</body>
</html>

