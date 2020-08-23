<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css')}}>
    <script src={{ asset('js/extra/jquery.js')}}></script>
    <script src={{ asset('js/base.js')}}></script>
</head>
<body>
<link rel="stylesheet" href={{ asset('css/login.css')}}>
<form method="post" action="/login/" id="login_form">
    @csrf
    <div class="login_dialog">
        <div id="login_dialog_content">
            <div class="title">Đăng Nhập</div>
            @if($login_failed)
                <div class="alert alert-danger">
                    Tài khoản hoặc Mật khẩu không chính xác
                </div>
            @endif
            <div class="lbl_username">Tài Khoản</div>
            <input class="form-control lbl_username_typing" type="text" placeholder="Nhập tài khoản"
                   name="username" autocomplete="off"><br>
            <div class="lbl_password">Mật Khẩu</div>
            <input class="form-control lbl_password_typing" type="password" placeholder="Nhập mật khẩu"
                   name="password">
            <button type="button" class="btn btn-success btn_login" id="btn_login">Đăng Nhập</button>

        </div>

    </div>
</form>
</body>
</html>
<script>
    $(document).ready(function () {
        $('#btn_login').click(function () {
            $('#login_form').submit();
        });
    });

</script>
