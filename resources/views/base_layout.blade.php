<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href={{ asset('css/base.css')}}>
    <link rel="stylesheet" href={{  asset('css/header.css')}}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <script src="{{ asset('js/extra/jquery.js')}}"></script>
    <script src="{{ asset('js/base.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
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
    <table id="menu_item_right">
        <tr>
            <td>
                <div class="avatar">
                    <img alt="avatar"/>
                </div>
            </td>
            <td>
                <div id="menu_alias_user">{{Auth::user()->alias_name}}</div>
            </td>
            <td>
                <div class="dropdown" id="dropdown_menu_department">
                    <button class="btn dropdown-toggle" type="button"
                            id="dropdown_menu_department_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if(Session::has('current_department_name'))
                            {{Session::get("current_department_name")}}
                        @else
                            {{Auth::user()->getDepartmentName()}}
                        @endif
                    </button>
                    @if(Auth::user()->isAdmin()|| Auth::user()->isSaleAdmin())
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item" href="/admin">Admin</a>
                                <a class="dropdown-item" href="/sale">Sale</a>
                                <a class="dropdown-item" href="/marketing">Marketing</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=2">{{\App\models\Storage::STORAGE_VU_NGOC_PHAN_NAME}}</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=3">{{\App\models\Storage::STORAGE_XA_DAN_NAME}}</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=4">{{\App\models\Storage::STORAGE_XUAN_LA_NAME}}</a>
                            @elseif(Auth::user()->isSaleAdmin())
                                <a class="dropdown-item" href="/sale">Sale</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=2">{{\App\models\Storage::STORAGE_VU_NGOC_PHAN_NAME}}</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=3">{{\App\models\Storage::STORAGE_XA_DAN_NAME}}</a>
                                <a class="dropdown-item" href="/storekeeper?department_code=4">{{\App\models\Storage::STORAGE_XUAN_LA_NAME}}</a>
                            @endif
                        </div>
                    @endif
                </div>
            </td>
            <td>
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
            </td>
            <td>
                <form method="get" action="/logout/" id="form_logout">
                    <div id="menu_btn_logout">Đăng Xuất</div>
                </form>
            </td>
        </tr>
    </table>
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

